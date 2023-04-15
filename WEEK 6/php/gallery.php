<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="CSS/style.css" type="text/css" media="screen" />
    <meta charset="utf-8" />
    <title>Gallery</title>
    <script type= 'text/javascript' src='js/jquery-1.12.4.js'></script>
    <script type= 'text/javascript' src='js/RotatingPictureDisplay.js'></script>
  </head>
  <body>
    <div id = "bodyspecs">
      <div id = "image-wrap">       
      <img class="images"
      src="http://www.nskelectronics.in/image/cache/data/Category%20Images/Mini%20Project-200x200.png" 
      alt="MP">
 <img class="images"
            src="https://botw-pd.s3.amazonaws.com/styles/logo-thumbnail/s3/062015/php_0.png?itok=ILNlhPBL" 
      alt="PHP">
      <img class="images"
            src="http://www.princetonwebsystems.com/wp-content/uploads/2014/05/aws-logo-01.png" 
      alt="AWS">
      </div>
      <header>
        <nav class="center">
          <ul id = "list-nav">
            <li><a href="index.php">Index</a></li>
            <li><a href="gallery.php">Gallery</a></li>
            <li><a href="submit.php">Form</a></li>
          </ul>
        </nav>
      </header>
      <div id = "bodyspecs2">
        <section id = "resumecontactinfo">
          <h1> Welcome to the Gallery page!!! </h1>	  	
          <p> Cell Number: 630-518-1224  </p>
          <p> Personal Email: reachabdu@gmail.com </p>
          <p> Student Email: asuhail@hawk.iit.edu </p>

        <?php

        //Include the AWS SDK using the Composer autoloader.
        require 'vendor/autoload.php';

        use Aws\Rds\RdsClient;

        $rds = new Aws\Rds\RdsClient([
        'version' => 'latest', 
        'region' => 'us-east-2'
        ]);

        $host = $rds->describeDBInstances([
        'DBInstanceIdentifier' => 'itmo544rds-rr'
        ]);

        $endpoint = $host['DBInstances'][0]['Endpoint']['Address'];

        
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

            
          // Prepared statement, pre processing
          if (!($stmt = $link->prepare("SELECT s3kachaurl FROM records"))) {
          echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
          }
          $stmt->execute();
          $result = $stmt->get_result();
          echo("<br><br>Before Pictures:<br>");
          while ($myarray = $result->fetch_assoc()) {
            $s3kachaurl = $myarray['s3kachaurl'];
            echo "<br><br><img src='$s3kachaurl'/>";
          }
           $stmt->close();


          // Prepared statement, post processing
          if (!($stmt2 = $link->prepare("SELECT s3cookedurl FROM records"))) {
          echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
          }

          //$stmt->bind_param("s" $s3cookedurl);
          $stmt2->execute();
           /*get_result function*/
          $result2 = $stmt2->get_result();
          
          echo("<br><br>After Pictures:<br>");
          while ($myarray2 = $result2->fetch_assoc()) {
            $s3cookedurl = $myarray2['s3cookedurl'];
            echo "<br><br><img src='$s3cookedurl'/>";
          }
          $stmt2->close();
          
          ?>


        </section>
        <footer id="ContactInfo">
          <address>
	      <p><br><br>Get in touch with me:</p>
	      <p>Personal Email: <a href="mailto:reachabdu@gmail.com">reachabdu@gmail.com</a></p>
	      <p>Academic E-mail: <a href="mailto:asuhail@hawk.iit.edu">asuhail@hawk.iit.edu</a></p>
	      <p>Visit me at my local library: 4 Friendship Plaza, Addison, IL 60101</p>
	      <p>Coded by Abdullah Suhail (2017)</p>
          </address>
        </footer>
      </div>
    </div>
  </body>
</html>
