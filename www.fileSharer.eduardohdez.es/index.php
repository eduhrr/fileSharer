<html>

<head>
<title>FileSharer</title>
<style>
body
{
background-image:url('Images/avion_papel.jpg');
background-repeat:no-repeat;
background-position:right top;

}
</style>
</head>
<body>
<link href="css/bootstrap.css" rel="stylesheet" media="screen">
<link href="css/default.css" rel="stylesheet" media="screen">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-responsive.css" rel="stylesheet" media="screen" >
<link href="css/bootstrap-responsive.min.css" rel="stylesheet" media="screen" >
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">

<?php


// Define a mebibyte
define('MB', 1048576);

			//check whether a form was submitted

			if(isset($_POST['Submit'])){
			
				require_once '/usr/share/php/sdk-1.5.15/sdk.class.php';

				//retreive post variables
				$fileName = $_FILES['theFile']['name'];
				$fileTempName = $_FILES['theFile']['tmp_name'];
				//echo "$fileName - $fileTempName" . "\n";
				
				$s3 = new AmazonS3();
				$bucket = 'test-eduhdez';

				//Normal (single part) upload --> first to the server and then to de S3
				/*if( $s3->create_object($bucket, $fileName, array(
					'fileUpload' => $fileTempName,
					'acl' => $s3::ACL_PUBLIC,
					'storage' => $s3::STORAGE_REDUCED))){
					echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}*/
				
				
				// Using the High-Level PHP API for Multipart Upload --> first to the server and then to de S3
				if( $s3->create_mpu_object($bucket, $fileName, array(
					'fileUpload' => $fileTempName,
					// Optional configuration
					'partSize' => 10*MB, // Defaults to 50MB
					'acl' => AmazonS3::ACL_PUBLIC,
					'storage' => AmazonS3::STORAGE_REDUCED,))){
					echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}
	
				//obtaining the url
				$url = $s3->get_object_url($bucket, $fileName);
				
				//creating a unique key (checks if the unique potential key is already in the db)
				include 'randomString.php';
				include 'db.php';
				do { $key = rand_string(100);
					$queryRecoverKey = sprintf("SELECT randomString, link FROM iitondacloud.links WHERE randomString = '%s'",mysql_real_escape_string($key));
					$RecoverKey = mysql_query($queryRecoverKey ) or die('Invalid query: '. mysql_error());
				}While (mysql_num_rows($RecoverKey)> 0);
				//insert the pair (key,url) into the db
				mysql_query("INSERT INTO iitondacloud.links (randomString, link) VALUES ('$key', '$url');");
				
				//go to dowload page
				header("Location: index.php?go=$key");
				
				
			}elseif(isset($_GET["go"])){	//load the download page
				//checks if any key in the db
				include 'db.php';
				$queryRecoverKey = sprintf("SELECT randomString, link FROM iitondacloud.links WHERE randomString = '%s'",mysql_real_escape_string($_GET["go"]));
				$RecoverKey = mysql_query($queryRecoverKey ) or die('Invalid query: '. mysql_error());
				if(mysql_num_rows($RecoverKey)> 0) { 
					$row = mysql_fetch_array($RecoverKey);
					$_SESSION["url"]=$row["link"];
					include 'download.php';
				}else{	//error case
						include 'upload.php';
				}
				
				
			}else{ //loads the upload page
				include 'upload.php';
			}

?>

</body>
</html>