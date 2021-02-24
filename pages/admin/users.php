<h1>Users</h1><hr />
<?php
    if(!isset($_GET['manage'])){
?>
        <form method="POST">
            <input class="form-control alert-info" name="uSearch" type="text" value="<?php echo ((isset($_GET['uSearch']))? $_GET['uSearch'] : ''); ?>" placeholder="Press enter to search" aria-label="Search">
        </form>
        <br />
<?php
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['uSearch'])){
        header("Refresh:0");
        header('Location: ' . $website_url . '/admin?a=users&uSearch=' . $_POST['uSearch']);
    }
    elseif(isset($_GET['uSearch'])){
        $input = $purifier->purify($_GET['uSearch']);
        
        echo $search->userSearch($input);
    }
    elseif(isset($_GET['manage'])){
        $input          = $purifier->purify($_GET['manage']);
        $userCheck      = $handler->prepare('SELECT * FROM users WHERE username = :username');
        $userCheck->execute([
            ':username' => $input
        ]);
        
        if($userCheck->rowCount()){
            include'manageUsers/manage.php';
        }
        else{
            header('Location: ' . $website_url . '/admin?a=users');
        }
    }
    else{
        $pagenumber = ((isset($_GET['pn']) && is_numeric($_GET['pn']) && $_GET['pn'] > 0)? (int)$_GET['pn'] : 1);
        
        echo $user->getAllUsers($pagenumber, [25]);
    }
?>