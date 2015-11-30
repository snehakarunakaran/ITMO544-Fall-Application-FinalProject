<?php
session_start();
require 'vendor/autoload.php';
$introspec=true;
$_SESSION['introspec']=$introspec;
echo "=========== $introspec =======";

$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

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


$backupFile = '/tmp/FinalProjectDB'.date("Y-m-d-H-i-s").'.gz';
$command = "mysqldump --opt -h $endpointrdb -u testconnection1 -ptestconnection1 Project1 | gzip > $backupFile";
exec($command);
echo "success";


			$s3 = new Aws\S3\S3Client([
				'version' => 'latest',
				'region'  => 'us-east-1'
			]);

$bucket='snehatestproject-'.rand().'-dbdump';
			if(!$s3->doesBucketExist($bucket)) {
				
				$result = $s3->createBucket([
					'ACL' => 'public-read',
					'Bucket' => $bucket,
				]);
	
				$s3->waitUntil('BucketExists', array('Bucket' => $bucket));
				echo "$bucket Created Successfully";
			}



$result = $s3->putObject([
'ACL' => 'public-read',
'Bucket' => $bucket,
'Key' => $backupFile,
'SourceFile'   => $backupFile,

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

echo "backup success";
$url = $result['ObjectURL'];
echo $url;


$urlintro	= "index.php";
   header('Location: ' . $urlintro, true);
   die();

$linkrdb->close();
?>