<?php
    error_reporting(E_ALL);//Remove
    ini_set('display_errors', 'On');//Remove
    /**
     * @author Tom Heek
     * @copyright 2021
     * This project uses PHP 8
     */

    session_start();
    ob_start();

    header('Content-Type: text/html; charset=utf-8');

    //Initialize database
    $mysqldb    = 'localhost';  //Mysql database
    $dbname     = 'vupload';           //Mysql database name
    $mysqluser  = 'root';           //Mysql username
    $mysqlpass  = '';           //Mysql password

    //Project location
    $map            = "";   //What map did you put the forum? Leave empty for root
    $website_url    = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $map; //Do not edit this
    $contactemail   = ""; //Admin contact email

    //Unique code for your website
    $uniqueCode = '';

    //0 for PHP mail function OR 1 for PHPMailer class
    $mailer = 1;

    //Mail configuration only change if $mailer = 1.
    $smtp = [
        'server'        => '',  //Specify main and backup SMTP servers
        'auth'          => true,                //Enable SMTP authentication
        'username'      => '',  //SMTP username
        'password'      => '',        //SMTP password
        'encryption'    => 'tls',               //Enable TLS encryption, `ssl` also accepted
        'port'          => 2525                 //TCP port to connect to
    ];
    
    //0 for video and thumbnail upload on this server OR 1 for an external server
    $uploadServer = 0;

    //FTP details only change if $uploadServer = 1
    $ftpServer = [
        'server'    => '',                          //FTP server
        'location'  => '',                          //Map URL of external host where the videos are stored
        'port'      => '21',                        //Port default 21 does not work with SFTP
        'username'  => '',                          //Username
        'password'  => '',                          //Password
        'folder'    => ''                           //Folder location on server
    ];

    /**
     * Remove if you are using Linux
     *  'ffmpeg.binaries'  => '..\ffmpeg.exe',
     *  'ffprobe.binaries' => '..\ffprobe.exe',
     */
    $ffmpeg = [
        'ffmpeg.binaries'  => '..\ffmpeg.exe',
        'ffprobe.binaries' => '..\ffprobe.exe',
        'timeout' => 9800, // The timeout for the underlying process
        'ffmpeg.threads' => 1 // The number of threads that FFMpeg should use
    ];

    require_once'configNoEdit.php';
?>