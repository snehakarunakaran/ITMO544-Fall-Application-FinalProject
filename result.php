<?php
echo "Hello World1";
session_start();
var_dump($_POST);
if(!empty($_POST)){
echo $_POST['useremail'];
echo $_POST['phone'];
echo $_POST['firstname'];
$_SESSION['firstname']=$_POST['firstname'];
$_SESSION['phone']=$_POST['phone'];
$_SESSION['useremail']=$_POST['useremail'];
}

else
{
echo "post empty";
}

$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
print '<pre>';


if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
  echo "File is valid, and was successfully uploaded.\n";
}

else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);
print "</pre>";

require 'vendor/autoload.php';


$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
#print_r($s3);

$bucket = uniqid("Final",false);

## AWS PHP SDK version 3 create bucket

$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);

#print_r($result);
// check for change
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => "RawURL".$uploadfile,
'ContentType' => $_FILES['userfile']['type'],
'Body' => fopen($uploadfile,'r+')
]);

$result = $s3->putBucketLifecycleConfiguration([
		'Bucket' => $bucket, // REQUIRED
		'LifecycleConfiguration' => [
			'Rules' => [ // REQUIRED
				[
				'Expiration' => [

				'Days' => 2,
				],	

			'NoncurrentVersionExpiration' => [
			'NoncurrentDays' => 2,
				],

			'Prefix' => '', // REQUIRED
			'Status' => 'Enabled', // REQUIRED

			],
			],
		],
	]);

$url = $result['ObjectURL'];
echo $url;

##s3 and url for the thumbnailimage

$thumbimageobj = new Imagick($uploadfile);
$thumbimageobj->thumbnailImage(150,150);
$thumbimageobj->writeImage();

echo "thumbnail";

//print_r($resultfinished);
$resultfinished = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => "FinishedURL".$uploadfile,
'ContentType' => $_FILES['userfile']['type'],
'Body' => fopen($uploadfile,'r+')
]);

$resultfinished = $s3->putBucketLifecycleConfiguration([
		'Bucket' => $bucket, // REQUIRED
		'LifecycleConfiguration' => [
			'Rules' => [ // REQUIRED
				[
				'Expiration' => [

				'Days' => 2,
				],	

			'NoncurrentVersionExpiration' => [
			'NoncurrentDays' => 2,
				],

			'Prefix' => '', // REQUIRED
			'Status' => 'Enabled', // REQUIRED

			],
			],
		],
	]);



$finishedurl = $resultfinished['ObjectURL'];
echo $finishedurl;
$emailtemp = $_POST['useremail'];

$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

$resultdb = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'db1'
   
));
$endpoint = $resultdb['DBInstances'][0]['Endpoint']['Address'];
    echo "============\n". $endpoint . "================";

$link = mysqli_connect($endpoint,"testconnection1","testconnection1","Project1");

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

else {
echo "Success";
}

#create sns client

$sns = new Aws\Sns\SnsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

#print_r($result);
//echo "sns Topic";
//to list topics

$resultsns = $sns->listTopics(array(

));


foreach ($resultsns['Topics'] as $key => $value){

if(preg_match("/ImageTopicSK/", $resultsns['Topics'][$key]['TopicArn'])){
$topicARN =$resultsns['Topics'][$key]['TopicArn'];
}
}
//extra code
$resultsub = $sns->listSubscriptionsByTopic(array(
     //TopicArn is required
    'TopicArn' => $topicARN,
   
));
foreach ($resultsub['Subscriptions'] as $key => $value){

if((preg_match($emailtemp, $resultsub['Subscriptions'][$key]['endpoint']))&&(preg_match("PendingConfirmation", $resultsub['Subscriptions'][$key]['SubscriptionArn']))){
$alertmsg='true';
$_SESSION['alertmsg']=$alertmsg;
}
else{
$alertmsg='false';
$_SESSION['alertmsg']=$alertmsg;
}
}




$uname=$_POST['firstname'];
$email = $_POST['useremail'];
$phoneforsms = $_POST['phone'];
$raws3url = $url; 
$finisheds3url =$finishedurl;
$jpegfilename = basename($_FILES['userfile']['name']);
$state=0;

$res = $link->query("SELECT * FROM MiniProject1 where email='$email'");

//if($res->num_rows>0){

if (!($stmt = $link->prepare("INSERT INTO MiniProject1 (uname,email,phoneforsms,raws3url,finisheds3url,jpegfilename,state) VALUES (?,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}

$stmt->bind_param("ssssssi",$uname,$email,$phoneforsms,$raws3url,$finisheds3url,$jpegfilename,$state);
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno0 . ") " . $stmt->error;
}

printf("%d Row inserted.\n", $stmt->affected_rows);

$stmt->close();

if($res->num_rows>0){
$pub = $sns->publish(array(
    'TopicArn' => $topicARN,
    // Message is required
    'Subject' => 'Image Upload Notification',
    'Message' => 'Image is successfully uploaded and saved!',
    
    
));
}

else 
{

$url	= "temp.php";
   header('Location: ' . $url, true);
   die();

}

#RDB Connection:

$resultrdb = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'mp1SKread-replica'
   
));
$endpointrdb = $resultrdb['DBInstances'][0]['Endpoint']['Address'];
    echo "============\n". $endpointrdb . "================";

$linkrdb = mysqli_connect($endpointrdb,"testconnection1","testconnection1","Project1");

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

else {
echo "Connection to RDB Success";
}


$linkrdb->real_query("SELECT * FROM MiniProject1");
$resrdb = $linkrdb->use_result();
echo "Result set order...\n";
while ($row = $resrdb->fetch_assoc()) {
    echo $row['id'] . " " . $row['email']. " " . $row['phoneforsms'];
}

$link->close();
$linkrdb->close();

$url	= "gallery.php";
   header('Location: ' . $url, true);
   die();
//}
//else 
//{

//$url	= "temp.php";
//   header('Location: ' . $url, true);
//   die();

//}
?> 

     

 
