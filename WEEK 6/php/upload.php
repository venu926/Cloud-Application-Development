<?php
header("Location: gallery.php"); /* Redirect browser */

  function resize_image($file, $width, $height) {
    list($w, $h) = getimagesize($file);
    /* calculate new image size with ratio */
    $ratio = max($width/$w, $height/$h);
    $h = ceil($height / $ratio);
    $x = ($w - $width / $ratio) / 2;
    $w = ceil($width / $ratio);
    /* read binary data from image file */
    $imgString = file_get_contents($file);
    /* create image from string */
    $image = imagecreatefromstring($imgString);
    $tmp = imagecreatetruecolor($width, $height);
    imagecopyresampled($tmp, $image,
    0, 0,
    $x, 0,
    $width, $height,
    $w, $h);
    imagejpeg($tmp, $file, 100);
    /* cleanup memory */
    imagedestroy($image);
    imagedestroy($tmp);
    
    return $file;   
}

  /*
  $im = imagecreatefromjpeg($image);
  imagefilter($im, IMG_FILTER_GRAYSCALE);
  //imagefilter($im, IMG_FILTER_CONTRAST, 1000);
  // Save the image as 'edited.jpg'
  imagejpeg($im, $editedimagefile);
  */

// Include the AWS SDK using the Composer autoloader.
require 'vendor/autoload.php';

use Aws\S3\S3Client;
//use Aws\S3\Exception\S3Exception;
//use Aws\Credentials\CredentialProvider;
use Aws\Rds\RdsClient;

        $rds = new Aws\Rds\RdsClient([
        'version' => 'latest', 
        'region' => 'us-east-2'
        ]);

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


$bucket = 's3kacha';
$bucket2 = 's3cooked';

if(!$s3->doesBucketExist($bucket))
{
    $s3->createBucket(array('Bucket' => $bucket));
}

if(!$s3->doesBucketExist($bucket2))
{
    $s3->createBucket(array('Bucket' => $bucket2));
}

/* Perform an operation to see the debug output.
$result = $s3->listBuckets();
echo("<br><br><br> Here are the Buckets: " .$result); 
*/

if(count($_FILES['images']['name']) > 0)
{
    for($i=0; $i<count($_FILES['images']['name']); $i++)
    {
      $email = ($_REQUEST['email']);
      $phone = ($_REQUEST['phone']);
      $resize_image = ($_FILES['images']['tmp_name'][$i]);
      $key = ($_FILES['images']['name'][$i]);
      $keyname = str_ireplace(' ', '_', $key);
      $editedimagefile = '/tmp/edited.jpg';

      $image = resize_image($resize_image, 960, 540);
      $status = 0;
      $uuid = uniqid();
      echo $uuid;
      echo "\n";
      echo $keyname;
      echo "\n";

try {
    // Upload data.
    $s3->putObject(array(
        'Bucket' => $bucket,
        'Key' => $keyname,
        'SourceFile' => $image,
        'ACL' => 'public-read'
    ));

    $s3kachaurl= 'http://s3.us-east-2.amazonaws.com/'.$bucket.'/'.$keyname;
    //print you url
    echo '<br>S3 File URL:'.$s3kachaurl;
    echo "\n";
    echo "<br><br><img src='$s3kachaurl'/>";
    echo "\n";
} catch (S3Exception $e) {
    echo $e->getMessage() . "<br>";
}

/*
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
    echo '<br>S3 File URL:'.$s3cookedurl;
    echo "<br><br><img src='$s3cookedurl'/>";
} catch (S3Exception $e) {
    echo $e->getMessage() . "<br>";
}
*/

 $result = $sqs->createQueue([
     'QueueName' => 'MiniProject', // REQUIRED
 ]);
 
 #list the SQS Queue URL
 $listQueueresult = $sqs->listQueues([
 ]);

 # print out every thing
 # print_r ($listQueueresult);  
 echo "\n";
 echo "Your SQS URL is: " . $listQueueresult['QueueUrls'][0] . "\n";
 echo "\n";
 $queueurl = $listQueueresult['QueueUrls'][0];
 ### 
 # you need some code to insert records into the database -- make sure you retrieve the UUID into a variable so you can pass it to the SQS message
 
 ### send message to the SQS Queue
 $sendmessageresult = $sqs->sendMessage([
    'DelaySeconds' => 0,
    'MessageBody' => $uuid, // REQUIRED
    'QueueUrl' => $queueurl // REQUIRED
 ]);
 
 echo "\n";
 echo "The messageID is: ". $sendmessageresult['MessageId'] . "\n";
 echo "\n";


// Prepared statement, stage 1: prepare
if (!($stmt = $link->prepare("INSERT INTO records (email, phone, s3kachaurl, keyname, status, uuid) VALUES
(?,?,?,?,?,?)"))) {
 echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}


$stmt->bind_param("ssssss",$email,$phone,$s3kachaurl,$keyname,$status,$uuid);

$stmt->execute();

$stmt->close();

    }
        $result2 = $sns->createTopic([
        'Name'=>'MiniProject',
        ]);
        $topicArn = $result2['TopicArn'];
        echo "Topic ARN is: $topicArn";
        $result3 = $sns->setTopicAttributes([
        'AttributeName'=>'DisplayName',
        'AttributeValue'=>'MP2',
        'TopicArn'=>$topicArn,
        ]);
        $result4 = $sns->subscribe([
        'Endpoint'=>$email,
        'Protocol'=>'email',
        'TopicArn'=>$topicArn,
        ]);
      
}
?>
