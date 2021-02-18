<h1>Videos</h1><hr />
<div class="row">
<?php
    $orderByWhat    = ((isset($_GET['orderByWhat']) && $_GET['orderByWhat'] == 'date')? 'v_uploadtime' : ((isset($_GET['orderByWhat']) && $_GET['orderByWhat'] == 'views')? 'views' : 'v_uploadtime'));
    $descasc        = ((isset($_GET['order']) && $_GET['order'] == 'DESC')? 'DESC' : ((!isset($_GET['order']))? 'DESC' : 'ASC'));

    for($i = 0; $i <= $video->getRecentVideos('count', ['all' => 1])-1; $i++){
        $x              = $i+1;
        $fileName       = $video->getRecentVideos('details', ['videoURL', 'amount' => $i, 'orderByWhat' => $orderByWhat, 'DESCASC' => $descasc, 'all' => 1]);
        $thumbnail      = $video->getRecentVideos('details', ['thumbnail', 'amount' => $i, 'orderByWhat' => $orderByWhat, 'DESCASC' => $descasc, 'all' => 1]);
        $title          = $video->getRecentVideos('details', ['title', 'amount' => $i, 'orderByWhat' => $orderByWhat, 'DESCASC' => $descasc, 'all' => 1]);
        $views          = $video->getDetails('', $fileName, 'views');
        $deleteVideo    = $website_url . '/admin?videos&delete=' . $fileName;
        
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
                <?php
                    if($video->getDetails('', $fileName, 'hidden') == 0){
                ?>
                <button class="btn btn-sm btn-warning float-end" id="delete<?php echo $i; ?>" style="color: black;">Delete</button>
                <?php
                    }
                ?>
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