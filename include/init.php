<?php
    //Automatically includes all php files the folder "classes"
    spl_autoload_register(function($class){
        require_once __DIR__ . "/classes/{$class}.class.php";
    });

    //Automatically includes all php files the folder "classes"
    /*$map = ((is_dir('classes/')? 'classes/' : '../classes/'));

    $Directory = new RecursiveDirectoryIterator($map);
    $Iterator = new RecursiveIteratorIterator($Directory);
    $objects = new RegexIterator($Iterator, '/^.+\.php$/i');

    foreach($objects as $name){
        require_once $name;
    }*/


    //Automatically includes all php files the folder "plugins"
    $map = ((is_dir('plugins/')? 'plugins/' : '../plugins/'));

    $Directory = new RecursiveDirectoryIterator($map);
    $Iterator = new RecursiveIteratorIterator($Directory);
    $objects = new RegexIterator($Iterator, '/^.+\.php$/i');

    foreach($objects as $name){
        require_once $name;
    }
?>
