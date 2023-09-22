#!/usr/bin/php -q
<?php
include "logging.php";

$phonenumber = $argv[2];
$extension = $argv[1];
$datetime= date("%Y-%M-%D %H:%m:%s");
$starttime = $argv[3];
$endtime = "NULL";
$type = $argv[4];
$filename = $argv[5];
$pri = $argv[6];

$con = mysqli_connect("localhost","cron","1234");
if (!$con)
  {
  die('Could not connect: ' . mysqli_error($con));
  }

mysqli_select_db($con, "voicecatch");
if("$type"== "outgoing")
{
$sql="INSERT INTO calldetails (PhoneNumber, Extension, StartTime, EndTime, Type, Filename, pri) VALUES ('$extension' , '$phonenumber' , '$starttime', '$endtime' , '$type' ,'$filename' ,'$pri')";
}
else
{
$sql="INSERT INTO calldetails (PhoneNumber, Extension, StartTime, EndTime, Type, Filename, pri) VALUES ('$phonenumber' , '$extension' , '$starttime', '$endtime' , '$type' ,'$filename' , '$pri')";

}
Mlog("insertcalldetail.log", $sql);

if (!mysqli_query($con,$sql))
  {
  die('Error: ' . mysqli_error($con));
  }

mysqli_close($con);

?>