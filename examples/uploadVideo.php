<?php
  echo "<html><body>"."\n";
  $s = "<form action=\"../../ObjectCreation.php\" method=\"post\" enctype=\"multipart/form-data\">"."\n";
  $s = $s."<input type=\"hidden\" name=\"type\" value=\"upload_videos\">"."\n";
  $s = $s."Video name  : "."\n";
  $s = $s."<input type=\"text\" name=\"name\"><br>"."\n";
  $s = $s."<input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\"><br>"."\n";
  $s = $s."<br><input type=\"submit\" value=\"Submit\">"."\n";
  echo $s;
  echo "</body></html>";

?>

