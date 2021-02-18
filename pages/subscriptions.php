<?php
    if($user->loggedin()){
?>
<div class="row">
    <div class="col-md-2">
        <h1>Subscriptions</h1>
        <hr />
        <?php
            echo $sub->getCreators();
        ?>
    </div>
    <div class="col-md-10">
        <h1>Most recent subscription videos</h1>
        <hr />
        <?php
                echo $sub->getSubs();
            }
            else{
                echo $notLoggedIn;
            }
        ?>
    </div>
</div>