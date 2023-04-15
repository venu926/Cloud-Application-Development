<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="CSS/style.css" type="text/css" media="screen" />
    <meta charset="utf-8" />
    <title>Submit</title>
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
	  
        <section id = "forminfo">
          <h1>Upload Your Photos Here! </h1>	  	
          <p> After you are done, please click the submit button.</p>
        </section>
		
		<div id = "bodyspecs4">
		<div id = "bodyspecs5">
        <form action="upload.php" method="post" enctype="multipart/form-data">
		  <label>Phone Number: </label> <br> <input type="number_format" name="phone"> <br>
		  <label>E-mail: </label> <br> <input type="text" name="email"> <br>
		  Select images to upload:
		  <input type="file" name="images[]" multiple="multiple">
		  <input type="submit" value="Upload Images" name="submit">
		</form>
		</div>
		</div>
		
        <footer id="ContactInfo">
          <address>
	    <p>Get in touch with me:</p>
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
