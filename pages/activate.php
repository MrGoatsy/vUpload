<div class="row">
    <div class="col-md-6">
        <h2>Account activation</h2><hr />
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
