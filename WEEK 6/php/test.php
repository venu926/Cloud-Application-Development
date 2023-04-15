<?php

  $email = ($_REQUEST['email']);
  $phone = ($_REQUEST['phone']);

  echo("email: " .$email);
  echo("<br/>phone: " .$phone);


define("endpoint", "sg-cli-test.cgw9ugisub9a.us-east-2.rds.amazonaws.com");
define("db_user", "myawsuser");
define("db_pass", "myawsuser");
define("db_name", "itmo544db");
define("port", 3306);

$link=mysqli_connect(endpoint,db_user,db_pass,db_name,port); 

// Check connection
if (mysqli_connect_errno($link)){
    echo "Failure to connect: " . mysqli_connect_error();
}

/* Prepared statement, stage 1: prepare */
if (!($stmt = $link->prepare("INSERT INTO records (email, phone) VALUES
(?,?)"))) {
 echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

$stmt->bind_param("ss",$email,$phone);

$stmt->execute();

$stmt->close();

echo "<br><br>6";
?>
