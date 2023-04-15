<?php // content="text/plain; charset=utf-8"

require 'vendor/autoload.php';

use Aws\Rds\RdsClient;

$rds = new Aws\Rds\RdsClient([
'version' => 'latest', 
'region' => 'us-east-2'
]);

$hostrr = $rds->describeDBInstances([
'DBInstanceIdentifier' => 'itmo544rds-rr'
]);
$endpointrr = $hostrr['DBInstances'][0]['Endpoint']['Address'];

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

$client = new Aws\CloudWatch\CloudWatchClient([
    'version' => 'latest',
    'region'  => 'us-east-2'
]);

$result = $client->getMetricStatistics([
    'Dimensions' => [
        [
            'Name' => 'ImageId', // REQUIRED
            'Value' => 'ami-15725b70', // REQUIRED
        ],
    ],
    'EndTime' => strtotime('now'), // REQUIRED
    'MetricName' => 'CPUUtilization', // REQUIRED
    'Namespace' => 'AWS/EC2', // REQUIRED
    'Period' => 60, // REQUIRED
    'StartTime' => strtotime('-5 minutes'), // REQUIRED
    'Statistics' => ['Maximum']
]);

$max1 =  $result['Datapoints'][0]['Maximum'];
$max2 =  $result['Datapoints'][1]['Maximum'];
$max3 =  $result['Datapoints'][2]['Maximum'];

echo '% ' .$max1. "\n";
echo '% ' .$max2. "\n";
echo '% ' .$max3. "\n";

if (!($stmt5 = $linkrr->prepare("SELECT keyname FROM records where status = ?"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    
    $stat = '1';
    $stmt5->bind_param("s", $stat);
    $stmt5->execute(); 
    $result5 = $stmt5->get_result();
    echo "\n"."Images Processed: $result5->num_rows \n";

if (!($stmt6 = $linkrr->prepare("SELECT keyname FROM records where status = ?"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
       
    $stat1 = '0';
    $stmt6->bind_param("s", $stat1);
    $stmt6->execute(); 
    $result6 = $stmt6->get_result();
    echo "\n"."Images not yet processed: $result6->num_rows \n";

?>
