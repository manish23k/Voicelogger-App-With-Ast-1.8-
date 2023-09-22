#!/usr/bin/php
<?php
$callerid = $argv[1];

$con = mysqli_connect("localhost","cron","1234");
if (!$con)
  {
  die('Could not connect: ' . mysqli_error());
  }

mysqli_select_db($con, "voicecatch");
	$sql="SELECT extension FROM landings WHERE callerid='$callerid'";

$rs = mysqli_query($con,$sql);
$rcnt = mysqli_num_rows($rs);
if($rcnt > 0) {

$rc = mysqli_fetch_assoc($rs);
$extension = $rc['extension'];

define('STDOUT', fopen("php://stdout", "w"));
fwrite(STDOUT, "SET VARIABLE CALLEXT $extension\n");
}

mysqli_close($con);
