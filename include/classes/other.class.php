<?php
    class other{
        public function execInBackground($cmd) {
            if (PHP_OS === "WINNT"){
                exec('start /B C:\xampp\php\php.exe -f index.php r');
            }
            else{
                exec($cmd . " > /dev/null &");  
            }
        }
    }
    /*$params = array("function_name" => 'image_detect',"image_guid" => $image_guid);

    $query_string = http_build_query($params, "", " ");
   if (PHP_OS === "WINNT") {
      pclose(popen("start /B php " . $script_location . " " . $query_string, "r"));
   } else {
      exec("/opt/plesk/php/5.6/bin/php " . $script_location . " " . $query_string . " &> /dev/null &");
   }*/
?>