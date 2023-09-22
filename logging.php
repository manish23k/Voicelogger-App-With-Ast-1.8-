<?php
function MLog($file, $log)
{
        $file = "/var/www/log/$file";
        $result = false;
        $log = date("d/m/Y H:i:s:")." $log\n\n";

        if(file_exists($file) && filesize($file) > 104857600)
        {
                rename($file, $file.".bak");
        }

        $filehandle = fopen($file, "at");
        if($filehandle)
        {
                $result = fwrite($filehandle, $log);
                fclose($filehandle);
        }

        if($result == false)
        {
                $file = "/var/www/log/loggingerror.log";
                if(file_exists($file) && filesize($file) > 104857600)
                {
                        rename($file, $file.".bak");
                }

                $errorhandle = fopen($file, "at");
                if($errorhandle)
                {
                        fwrite($errorhandle, $log);
                        fclose($errorhandle);
                }
        }
}


?>

