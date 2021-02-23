<div class="row">
    <div class="col-md-6">
        <h1>Account activation</h1><hr />
        <?php
            if(isset($_GET['code'])){
                $code = htmlentities($_GET['code'], ENT_QUOTES, 'UTF-8');
                $querycode = $handler->query("SELECT * FROM users WHERE email_code = '$code'");
                $fetch = $querycode->fetch(PDO::FETCH_ASSOC);

                if($querycode->rowCount() && !$fetch['active']){
                    $handler->query("UPDATE users SET active = 1 WHERE email_code = '$code'");

                    echo $accountactivated;
                }
                else{
                    echo $doesnotexist;
                }
            }
            else{
                header('Location: ' . $website_url);
            }
        ?>
    </div>
</div>
