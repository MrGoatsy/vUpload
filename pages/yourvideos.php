<?php
    if($user->loggedIn()){
        $query = $handler->prepare('SELECT * FROM users WHERE username = :username AND rank > 0');
        try{
            $query->execute([
                ':username' => $user->getSessionUsername()
            ]);
        }catch(PDOException $e){
            echo $error;
        }

        if($query->rowCount()){
            if(isset($_GET['delete'])){
                echo $video->deleteVideo($video->getdetails('', $_GET['delete'], 'videoFileName'), $video->getdetails('', $_GET['delete'], 'extension'));

                header('Location: ' . $website_url . '/yourvideos');
            }
            else{
?>
<div class="row">
    <div class="col-md-12">
    <h1>Your videos</h1>
    <hr />
        <div class="row">
        <?php
            for($i = 0; $i <= $video->getVideo($user->getSessionUsername(), 'count', [$i])-1; $i++){
                $x              = $i+1;
                $fileName       = $video->getVideo($user->getSessionUsername(), 'details', ['videoFileName', $i]);
                $thumbnail      = $fileName . '.jpg';
                $title          = $video->getVideo($user->getSessionUsername(), 'details', ['title', $i]);
                $views          = $video->getDetails('', $fileName, 'views');
                $deleteVideo    = $website_url . '/yourvideos?delete=' . $fileName;
                
                if($x%4 == 1){
                    echo'<div class="row">';
                }
        ?>
                <script type="text/javascript">
                    $(function() {
                        var clicked = 0;
                        
                        $("#delete<?php echo $i; ?>").click(function() {
                            if(clicked == 0){
                                $('#delete<?php echo $i; ?>').removeClass('btn-warning').addClass('btn-danger'); 
                                $('#delete<?php echo $i; ?>').text('Confirm');

                                clicked = 1;
                            }
                            else{
                                $(location).attr("href", "<?php echo $deleteVideo; ?>");
                            }
                        });
                    });
                </script>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div style="background: url(<?php echo $website_url . '/videos/users/thumbnails/' . $thumbnail; ?>); padding-top: 56.25%; background-size:100% 100%;"></div>
                        <div class="card-body">
                        <p class="card-text">
                            <b><?php echo $title; ?></b>
                            <span class="float-end"><?php echo $views; ?> views</span>
                        </p>
                        <a href="<?php echo $website_url . '/watch?v=' . $fileName; ?>" class="btn btn-sm btn-outline-secondary" style="color: black;">View</a>
                        <a href="<?php echo $website_url . '/editvideo?v=' . $fileName; ?>" class="btn btn-sm btn-outline-secondary" style="color: black;">Edit</a>
                        <button class="btn btn-sm btn-warning float-end" id="delete<?php echo $i; ?>" style="color: black;">Delete</button>
                        </div>
                    </div>
                    <div style="margin-bottom: 15px;"></div>
                </div>
        <?php
            if($x%4 == 0){
                echo'</div><br />';
            }
        }
        ?>
    </div>
</div>
<?php
            }
        }
    }
?>