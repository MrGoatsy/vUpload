<?php
    if($user->isBanned()){
        $user->logout();
?>
        <h1>You are banned.</h1><hr />
        You will now be logged out.
<?php
    }
    else{
        header('Location: ' . $website_url);
    }
?>