<?php
    /**
     * Do not edit anything below.
     */

    try{
        $handler = new PDO('mysql:host=' . $mysqldb . ';dbname=' . $dbname, $mysqluser, $mysqlpass);
        $handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $handler->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }catch(PDOException $e){
        die('Something went wrong, please try again.');
    }

    $max_upload = min((int)ini_get('post_max_size'), (int)ini_get('upload_max_filesize'));
    $max_upload = $max_upload * 1024;

    //Hack for dynamic search results
    ((!isset($_SESSION['searchResults']))? $_SESSION['searchResults'] = 0 : '');

    //Included core files
    require_once'include/lang.php';
    require_once __DIR__ . '/vendor/autoload.php';
    require_once'vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';

    use phpseclib3\phpseclib3\Net\SFTP;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    //Purify HTML
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);

    //PHPMailer
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = $smtp['server'];
    $mail->SMTPAuth = $smtp['auth'];
    $mail->Username = $smtp['username'];
    $mail->Password = $smtp['password'];
    $mail->SMTPSecure = $smtp['encryption'];
    $mail->Port = $smtp['port'];

    $channelName = explode('/', $_SERVER['REQUEST_URI']);
    
    $ffmpeg     = FFMpeg\FFMpeg::create($ffmpeg);
    $ffprobe    = FFMpeg\FFProbe::create();

    require_once'include/init.php';

    if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
        $_SERVER['REMOTE_ADDR'] = getUserIpAddr();
    }
    
    //Setting up classes
    $user           = new user($handler, $uniqueCode, $purifier, new profile($handler, $purifier)); //I do not like this solution but it's what it's
    $rand           = new randString();
    $profile        = new profile($handler, $purifier, $user);
    $errorHandler   = new errorHandler();
    $other          = new other();
    $video          = new video($handler, $ftpServer, $user, $purifier, $profile);
    $commentClass   = new comment($handler, $user, $video);
    $search         = new search($handler, $video, $profile);
    $sub            = new subscriptions($handler, $video, $user, $profile);
    $process        = new process($handler);
    $warning        = new warning($handler, $user);
?>