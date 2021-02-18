<?php
    //Generate a random string
    class randString{
        public function string($length = 10){
            $i = 0;
            $string = "";
    
            while($i <= $length){
                $rand = ((rand(0, 9) & 1)? ((rand(0, 9) & 1)? range('A', 'Z') : range(0, 9)) : ((rand(0, 9) & 1)? range('a', 'z') : range(0, 9)));
    
                $string .= ((is_string($rand[0])? $rand[rand(0, 25)] : $rand[rand(0, 9)]));
                $i++;
            }
            return $string;
        }
    }
?>
