<?php
    if($user->logout()){
        echo'<h1>You have been logged out</h1>';

        header('Location: ' . $website_url);
    }
?>