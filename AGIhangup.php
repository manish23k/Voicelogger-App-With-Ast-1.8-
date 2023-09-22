#!/usr/bin/php -q
<?php
$d = date('c').'-- Enter'."\n";
@file_put_contents('hangup-log.log', $d, FILE_APPEND);

$phonenumber = $argv[2];
$extension = $argv[1];
$datetime= date("Y-m-d H:i:s");
$filename= $argv[3];
$endtime = $argv[4];

$d = date('c')."-- Enter 1 $phonenumber $extension $datetime $endtime $filename\n";
@file_put_contents('hangup-log.log', $d, FILE_APPEND);

$con = mysqli_connect("localhost","cron","1234");
if (!$con)
  {
$d = date('c')."-- Enter error 1 $phonenumber $extension $datetime $endtime $filename\n";
@file_put_contents('hangup-log.log', $d, FILE_APPEND);

  die('Could not connect: ' . mysqli_error());
  }

mysqli_select_db($con, "voicecatch");
$d = date('c')."-- Enter 2 $phonenumber $extension $datetime $endtime $filename\n";
@file_put_contents('hangup-log.log', $d, FILE_APPEND);

$sql = "UPDATE calldetails SET endtime = '$endtime'  WHERE PhoneNumber = '$phonenumber' AND Extension = '$extension' AND Filename = '$filename'"; 
if (!mysqli_query($con,$sql))
  {
$d = date('c')."-- Enter error 2 $phonenumber $extension $datetime $endtime $filename\n";
@file_put_contents('hangup-log.log', $d, FILE_APPEND);

  die('Error: ' . mysqli_error());
  }
$d = date('c')."-- Enter 3 $phonenumber $extension $datetime $endtime $filename\n";
@file_put_contents('hangup-log.log', $d, FILE_APPEND);

mysqli_close($con);

$d = date('c')."-- Enter 4 $phonenumber $extension $datetime $endtime $filename\n";
@file_put_contents('hangup-log.log', $d, FILE_APPEND);


