<?php
    
?>
<h1>Home</h1>
<hr />
<?php
    $orderByWhat    = ((isset($_GET['orderByWhat']) && $_GET['orderByWhat'] == 'date')? 'v_uploadtime' : ((isset($_GET['orderByWhat']) && $_GET['orderByWhat'] == 'views')? 'views' : 'v_uploadtime'));
    $descasc        = ((isset($_GET['order']) && $_GET['order'] == 'DESC')? 'DESC' : ((!isset($_GET['order']))? 'DESC' : 'ASC'));
    
    for($i = 0; $i <= $video->getRecentVideos('count')-1 && $i < 25; $i++){
        $description = $video->getRecentVideos('details', ['description', 'amount' => $i, 'orderByWhat' => $orderByWhat, 'DESCASC' => $descasc]);
        if($i+1%4 == 1){
            echo'<div class="row">';
        }
?>
        <div class="col-md-3">
            <a href="<?php echo $website_url . '/watch?v=' . $video->getRecentVideos('details', ['videoURL', 'amount' => $i, 'orderByWhat' => $orderByWhat, 'DESCASC' => $descasc]); ?>" style="color: black;">
            <div class="card shadow-sm">
                <div style="background: url(<?php echo $website_url . '/videos/users/thumbnails/' . $video->getRecentVideos('details', ['thumbnail', 'amount' => $i, 'orderByWhat' => $orderByWhat, 'DESCASC' => $descasc]); ?>); padding-top: 56.25%; background-size:100% 100%;"></div>
                <div class="card-body" style="position: relative;">
                    <h3 class="card-title"><?php echo $video->getRecentVideos('details', ['title', 'amount' => $i, 'orderByWhat' => $orderByWhat, 'DESCASC' => $descasc]); ?></h3><br />
                    <div class="card-text" style="margin-top: -25px; margin-bottom: 10px; font-size: 15px;">
                        <?php echo str_limit_html($description, 100); ?>
                    </div>
                    <div class="d-flex" style="position: absolute; bottom: 0px; font-size: 12px;">
                        Tags:&nbsp;<?php echo $video->getRecentVideos('details', ['tags', 'amount' => $i, 'orderByWhat' => $orderByWhat, 'DESCASC' => $descasc]); ?>
                    </div>
                </div>
            </div>
            </a>
            <div style="margin-bottom: 15px;"></div>
        </div>
<?php
    if($i+1%4 == 0){
        echo'</div><br />';
    }
}
?>
