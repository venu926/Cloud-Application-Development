<?php
require 'vendor/autoload.php';
echo "hello world!\n";

use Aws\S3\S3Client;
//use Aws\S3\Exception\S3Exception;
//use Aws\Credentials\CredentialProvider;
use Aws\Rds\RdsClient;

$rds = new Aws\Rds\RdsClient([
'version' => 'latest', 
'region' => 'us-east-2'
]);

$hostrr = $rds->describeDBInstances([
'DBInstanceIdentifier' => 'itmo544rds-rr'
]);
$endpointrr = $hostrr['DBInstances'][0]['Endpoint']['Address'];

$host = $rds->describeDBInstances([
  'DBInstanceIdentifier' => 'itmo544rds'
  ]);
  $endpoint = $host['DBInstances'][0]['Endpoint']['Address'];


$s3 = new Aws\S3\S3Client([
  'version' => 'latest',
  'region'  => 'us-east-2'
]);

$sqs = new Aws\Sqs\SqsClient([
   'version' => 'latest',
   'region'  => 'us-east-2'
]);

$sns = new Aws\Sns\SnsClient([
  'version' => 'latest',
  'region' => 'us-east-2'
  ]);



 #list the SQS Queue URL
 $listQueueresult = $sqs->listQueues([
    
 ]);
 # print out every thing
 # print_r ($listQueueresult);  
 echo "Your SQS URL is: " . $listQueueresult['QueueUrls'][0] . "\n";
 $queueurl = $listQueueresult['QueueUrls'][0];
 ### 
 # you need some code to insert records into the database -- make sure you retrieve the UUID into a variable so you can pass it to the SQS message
 

$receivemessageresult = $sqs->receiveMessage([
    'MaxNumberOfMessages' => 1,
    'QueueUrl' => $queueurl, // REQUIRED
    'VisibilityTimeout' => 10,
    'WaitTimeSeconds' => 0,
]);

# print out content of SQS message - we need to retreive Body and Receipt Handle
#print_r ($receivemessageresult['Messages'])
$receiptHandle = $receivemessageresult['Messages'][0]['ReceiptHandle'];

$uuid = $receivemessageresult['Messages'][0]['Body'];

//echo "The content of the message is: " . $uuid;

$bucket = 's3kacha';
$bucket2 = 's3cooked';
$status = 1;

define("endpointrr", $endpointrr);
define("db_userrr", "myawsuser");
define("db_passrr", "myawsuser");
define("db_namerr", "itmo544db");
define("portrr", 3306);

$linkrr=mysqli_connect(endpointrr,db_userrr,db_passrr,db_namerr,portrr);

// Check connection
if (mysqli_connect_errno($linkrr)){
echo "Failure to connect to database: " . mysqli_connect_error();
}

define("endpoint", $endpoint);
define("db_user", "myawsuser");
define("db_pass", "myawsuser");
define("db_name", "itmo544db");
define("port", 3306);

$link=mysqli_connect(endpoint,db_user,db_pass,db_name,port);

// Check connection
if (mysqli_connect_errno($link)){
echo "Failure to connect to database: " . mysqli_connect_error();
}

if ($uuid == NULL){
  echo "There aren't any jobs to process\n";
  exit;
}
else{
// Prepared statement, pre processing
if (!($stmt3 = $linkrr->prepare("SELECT s3kachaurl, keyname FROM records where uuid = ?"))) {
echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}


$stmt3->bind_param("s", $uuid);

$stmt3->execute(); 

$result3 = $stmt3->get_result();

/*

$stmt3 = "select keyname from records where uuid = '$uuid'";
$result3 = $link->query($stmt3);
while($row = $result3->fetch_assoc()){
  echo "Keyname: " . $row["keyname"] . "<br>";
}

*/

while ($myarray3 = $result3->fetch_assoc()) {
  $s3kachaurl = $myarray3['s3kachaurl'];
  $key = $myarray3['keyname'];
  $keyname = str_ireplace(' ', '_', $key);
  echo "\n";
  echo $s3kachaurl;
  echo "\n";
  echo $keyname;
  echo "\n";
  $tempdir = '/tmp/images/original.jpg';
  $editedimagefile = '/tmp/edited/edited.jpg';
  file_put_contents($tempdir, file_get_contents($s3kachaurl));

  $im = imagecreatefromjpeg($tempdir);
  imagefilter($im, IMG_FILTER_GRAYSCALE);
  //imagefilter($im, IMG_FILTER_CONTRAST, 1000);
  // Save the image as 'edited.jpg'
  imagejpeg($im, $editedimagefile);

  try {
    // Upload data.
    $s3->putObject(array(
        'Bucket' => $bucket2,
        'Key' => $keyname,
        'SourceFile' => $editedimagefile,
        'ACL' => 'public-read'
    ));

    $s3cookedurl= 'http://s3.us-east-2.amazonaws.com/'.$bucket2.'/'.$keyname;
    //print you url
    echo "S3 File URL:".$s3cookedurl;
    //echo "<br><br><img src='$s3cookedurl'/>";
    $result4 = $sns->listTopics([]);
    
    $topicArn = $result4['Topics'][0]['TopicArn'];
    echo "Your Topic ARN: " . $topicArn . "\n";
    
    $message = "Ok, so image " . $keyname . " has been successfully uploaded. Good for you!";
    
    $result5 = $sns->publish([
      'TopicArn' => $topicArn,
      'Subject' => 'Image uploaded',
      'Message' => $message,
      ]);
} catch (S3Exception $e) {
    echo $e->getMessage() . "<br>";
}
}


echo "\nhello world!\n";

 $stmt3->close();

 // Prepared statement, stage 1: prepare
if (!($stmt4 = $link->prepare("update records set s3cookedurl = ?, status = ? where uuid = ?"))) {
 echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}


$stmt4->bind_param("sss",$s3cookedurl,$status,$uuid);

$stmt4->execute();

$stmt4->close();
}


$deletemessageresult = $sqs->deleteMessage([
    'QueueUrl' => $queueurl, // REQUIRED
    'ReceiptHandle' => $receiptHandle, // REQUIRED
]);

?>
