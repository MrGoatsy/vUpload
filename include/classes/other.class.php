<?php
    class other{
        public function execInBackground($cmd, $targetPath, $extension, $video) {
            if (substr(php_uname(), 0, 7) == "Windows"){
                //pclose(popen("start /B ". $cmd . ' -- ' . escapeshellarg($targetPath) . ' ' . escapeshellarg($extension) . ' ' . escapeshellarg($video), "r")); 
                exec('php -f ' . $cmd . ' -- ' . escapeshellarg($targetPath) . ' ' . escapeshellarg($extension) . ' ' . escapeshellarg($video) . " > /dev/null &");
            }
            else {
                exec($cmd ." > /dev/null &");
            } 
        } 
    }
?>