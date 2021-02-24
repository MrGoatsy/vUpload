<div class="row">
    <div class="col-md-12">
        <h1>Account activation</h1><hr />
        <?php
            if(isset($_GET['code'])){
                $code = htmlentities($_GET['code'], ENT_QUOTES, 'UTF-8');

                echo $user->activate($code);
            }
            else{
                header('Location: ' . $website_url);
            }
        ?>
    </div>
</div>
