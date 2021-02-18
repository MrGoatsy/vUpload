<div class="row d-flex justify-content-center">
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="user d-flex flex-row align-items-center">
                <?php echo $comment->getCommentDetails($filename, ['user', 'avatar']) ; ?>
                <span><small class="font-weight-bold text-primary">james_olesenn</small>
                <small class="font-weight-bold">Hmm, This poster looks cool</small></span>
            </div>
            <small>2 days ago</small>
        </div>
        <div class="action d-flex justify-content-between mt-2 align-items-center">
            <div class="reply px-4"> <small>Remove</small> <span class="dots"></span> <small>Reply</small> <span class="dots"></span> <small>Translate</small> </div>
            <div class="icons align-items-center"> <i class="fa fa-star text-warning"></i> <i class="fa fa-check-circle-o check-icon"></i> </div>
        </div>
    </div>
</div>

<table style="width: 100%;" class="table table-bordered border border-dark">
    <tr>
        <td>
            <a href="<?php echo $website_url . '/channel/' . $video->getDetails($filename, 'channel'); ?>"><?php echo $video->getDetails($filename, 'avatar'); ?></a>
        </td>
        <td>
            <a href="<?php echo $website_url . '/channel/' . $video->getDetails($filename, 'channel'); ?>"><?php echo $video->getDetails($filename, 'channel'); ?></a><br />
            <small><?php echo $views . (($views == 1)? ' view' : ' views') . ' • ' .  $video->getDetails($filename, 'time'); ?></small>
        </td>
    </tr>
</table>