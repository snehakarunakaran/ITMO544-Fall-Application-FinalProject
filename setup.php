<?php

require 'vendor/autoload.php';
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

$result = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'db1'
   
));
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    echo "============\n". $endpoint . "================";

$link = new mysqli($endpoint,"testconnection1","testconnection1","Project1",3306) or die("Error " . mysqli_error($link));

$sql = "DROP TABLE IF EXISTS MiniProject1";
if(!mysqli_query($link, $sql)) {
   echo "Error : " . mysqli_error($link);
} 

$link->query("CREATE TABLE MiniProject1 
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
uname VARCHAR(20),
email VARCHAR(50),
phoneforsms VARCHAR(20),
raws3url VARCHAR(256),
finisheds3url VARCHAR(256),
jpegfilename VARCHAR(256),
state tinyint(3) CHECK(state IN(0,1,2)),
datetime timestamp
)");

shell_exec("chmod 600 setup.php");

?>
