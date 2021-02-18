<?php
    if($user->isBanned()){
?>
        <h3>Banned temp page</h3>
<?php
    }
    else{
        header('Refresh:0; url=' . $website_url);
    }
?>