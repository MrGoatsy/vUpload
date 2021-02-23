<?php
    require'config.php';

    if(isset($_GET['p']) && $_GET['p'] == 'watch'){
        $fileName = $purifier->purify($_GET['v']);
    }
    
    if($user->loggedIn()){
        $queryUser = $handler->prepare('SELECT * FROM users WHERE username = :username');
        $queryUser->execute([
            ':username' => $_SESSION[$uniqueCode]
        ]);

        $fetchUser = $queryUser->fetch(PDO::FETCH_ASSOC);
        $queryPermissions = $handler->query('SELECT * FROM ranks WHERE rankValue =' . $fetchUser['rank']);
        $fetchPermissions = $queryPermissions->fetch(PDO::FETCH_ASSOC);
    }
    else{
        $fetchUser = NULL;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo ((isset($_GET['p']) && $_GET['p'] == 'watch')? $video->getDetails('', $fileName, 'title') . ' - ' : '') ?>vUpload</title>

    <meta name="author" content="Tom Heek">

    <link rel="icon" href="<?php echo $website_url ?>/images/favicon.svg" sizes="any" type="image/svg+xml">

    <!-- include libraries(jQuery, bootstrap) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="http://malsup.github.com/jquery.form.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" async></script>
    <script src="<?php echo $website_url; ?>/js/custom.js"></script>
    
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <!-- jQuery throttle/debounce -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js"></script>

    <!-- PLYR media player -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.6.3/plyr.css" />
    <script src="https://cdn.plyr.io/3.6.3/plyr.js" defer></script>

    <link href="<?php echo $website_url; ?>/css/main.css" rel="stylesheet" />
    
</head>
<body class="site d-flex flex-column min-vh-100">
    <header>
            <?php
                require_once'include/menu.php';
            ?>
    </header>
    <main class="site-content flex-fill">
        <div class="container-fluid">
            <?php
                if($user->isBanned()){
                    include'pages/accountstatus.php';
                }
                elseif(isset($_GET['p'])){
                    if(file_exists('pages/' . $_GET['p'] . '.php')){
                        include'pages/' . $_GET['p'] . '.php';
                    }
                    elseif(isset($_GET['watch'])){
                        require_once'pages/watch.php';
                    }
                    elseif(isset($_GET['search'])){
                        require_once'pages/search.php';
                    }
                    elseif($channelName[2] == "channel"){
                        if($channelName[3] == "yourvideos"){
                            include'pages/yourvideos.php';
                        }
                        else{
                            include'pages/channel.php';
                        }
                    }
                    elseif($channelName[2] == "include"){
                        include'include/uploadvideo.php';
                    }
                    else{
                        echo $pagedoesnotexist;
                    }
                }
                else{
                    include'pages/home.php';
                }
            ?>
        </div>
    </main>
    <footer>
            <?php
                require'include/footer.php';
            ?>

    </footer>
</body>
</html>