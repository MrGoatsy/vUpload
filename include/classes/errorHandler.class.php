<?php
    class errorHandler{
        public function __construct(){

        }

        public function dbError($optional = []){
            global $coreLang;

            return $coreLang['error'];
        }
    }
?>