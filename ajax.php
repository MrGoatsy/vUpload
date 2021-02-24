<?php
    include'config.php';

    if(isset($_GET['q'])){
        //User has to be logged in for these
        if($user->loggedIn()){
            if($_GET['q'] == 'uploadSubmit'){
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    if(isset($_FILES) && !empty($_FILES)){
                        if($_FILES['video']['error'] === 0 && !empty($_POST['title'])){
                            $title = $_POST['title'];
                            $desc = $_POST['description'];
                            $tags = $_POST['tags'];
                            
                            if(strlen($tags) <= 100){
                                $video->upload($_FILES, $title, $desc, $tags);
                            }
                            else{
                                echo $tagLength;
                            }
                        }
                        else{
                            echo $error;
                        }
                    }
                }
            }
            elseif($_GET['q'] == 'editVideo'){
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    if(!empty($_POST['title'])){
                            $fileName   = $_GET['fileName'];
                        if($video->getDetails('', $fileName, 'count')){
                            $title      = $_POST['title'];
                            $desc       = $_POST['description'];
                            $tags       = $_POST['tags'];
                            
                            if(strlen($tags) <= 100){
                                echo $video->editVideo($_FILES, $fileName, $title, $desc, $tags);
                            }
                            else{
                                echo $tagLength;
                            }
                        }
                        else echo $error;
                    }
                    else{
                        echo $error;
                    }
                }
            }
            elseif($_GET['q'] == 'vrated'){
                if(isset($_GET['fileName']) && isset($_GET['rating'])){
                    if($_GET['rating'] == 1){
                        echo $video->getLord($_GET['fileName'], 1);
                    }
                    elseif($_GET['rating'] == -1){
                        echo $video->getLord($_GET['fileName'], -1);
                    }
                }
                else{
                    if($_SERVER['REQUEST_METHOD'] == 'POST'){
                        $video->setLord($_POST['videoURL'], $_POST['action']);
                    }
                    else{
                        die();
                    }
                }
            }
            elseif($_GET['q'] == 'subscribe'){
                if(isset($_POST['channelName']) && $_POST['channelName'] != $user->getSessionUsername()){
                    echo $user->subscribe($_POST['channelName']);
                }
            }
            elseif(isset($_SESSION['progress']) && $_GET['q'] == 'progress'){
                if($_SESSION['progress'] == 99 || $_SESSION['progress'] == 100){
                    unset($_SESSION['progress']);
                    
                    echo'The upload has been completed';
                }
                else{
                    echo $_SESSION['progress'];
                }
            }
            elseif($_GET['q'] == 'commentPost'){
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    if(!empty($_POST['comment'])){
                        $commentClass->postComment($_GET['videoURL'], $_POST['comment']);
                    }
                    else{
                        echo $noComment;
                    }
                }
            }
            elseif($_GET['q'] == 'getNewComment' && $_GET['fileName']){
                $username = $user->getSessionUsername();
                
                echo <<<EOD
                    <a href="$website_url/channel/$username">{$user->getSessionUserAvatar([32])}</a>
                    <a href="$website_url/channel/$username">$username</a>
                    <small>{$commentClass->getCurrentUserCommentDetails($_GET['fileName'], ['date'])}</small><br />
                    <small>{$commentClass->getCurrentUserCommentDetails($_GET['fileName'], ['comment'])}</small><hr />
                EOD;
            }
            elseif($_GET['q'] == 'getComment' && $_GET['fileName']){
                echo $commentClass->getAllCurrentUserComments($_GET['fileName']);
            }
        }
        //User does not have to be logged in for these
        if($_GET['q'] == 'loadComments' && $_GET['fileName'] && $_GET['order']){
            $filename       = $_GET['fileName'];
            $order          = $_GET['order'];
            
            if($_SESSION['currentAmount'] <= $commentClass->getCommentDetails($filename, ['rowCount', '', '', $_SESSION['currentAmount']])-1){
                $username   = $commentClass->getCommentDetails($filename, ['user', 'username', $order, $_SESSION['currentAmount']]);
                $avatar     = $commentClass->getCommentDetails($filename, ['user', 'avatar', $order, $_SESSION['currentAmount']]);
                $date       = $commentClass->getCommentDetails($filename, ['date', '', $order, $_SESSION['currentAmount']]);
                $comment    = $commentClass->getCommentDetails($filename, ['comment', '', $order, $_SESSION['currentAmount']]);

                echo <<<EOD
                    <a href="$website_url/channel/$username">$avatar</a>
                    <a href="$website_url/channel/$username">$username</a>
                    <small>$date</small><br />
                    <small>$comment</small><hr />
                EOD;

                $_SESSION['currentAmount']++;
            }
            else{
                exit;
            }
        }
        elseif($_GET['q'] == 'checkSession'){
            echo $_SESSION['currentAmount'];
        }
    }
?>