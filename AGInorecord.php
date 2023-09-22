#!/usr/bin/php
<?php
$callerid = $argv[1];

$con = mysqli_connect("localhost","cron","1234");
if (!$con)
  {
  die('Could not connect: ' . mysqli_error());
  }
  
mysqli_select_db($con, "voicecatch");
	$sql="SELECT id FROM logrules WHERE phonenumber='$callerid'";

$rs = mysqli_query($con,$sql);
$rcnt = mysqli_num_rows($rs);
if($rcnt > 0) {

$rc = mysqli_fetch_assoc($rs);
$id = $rc['id'];

define('STDOUT', fopen("php://stdout", "w"));
fwrite(STDOUT, "SET VARIABLE CALLEXTOUT $id\n");
}

mysqli_close($con);
