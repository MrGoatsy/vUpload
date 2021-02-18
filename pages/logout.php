<?php
    if($user->logout()){
        echo'<h3>You have been logged out</h3>' .  $redirectInTime;

        header('Refresh:0; url=' . $website_url);
    }
    elseif(!$user->loggedIn()){
        echo $redirectInTime;
    }
?>