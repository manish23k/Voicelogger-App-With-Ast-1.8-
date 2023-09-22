<?php
	$root = "/var/www/recordings/files";
	for($y=2023;$y<2099;$y++) {
		mkdir("$root/$y");
		for($m=1;$m<=12;$m++) {
			if ($m<10)
			{$m = "0"."$m"; }
			mkdir("$root/$y/$m");
			for($d=1;$d<=31;$d++) {
				if ($d<10) {
				
				mkdir("$root/$y/$m/"."0"."$d");
				}
				else{mkdir("$root/$y/$m/$d");}
			}
		
		}

	}
