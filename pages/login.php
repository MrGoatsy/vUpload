<div class="row">
        <?php
            if($user->loggedIn()){
                header('Location: ' . $website_url);
            }
            else{
                if(isset($_COOKIE['newpassword'])){
        ?>
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            Your password has been reset, please check your email.
                        </div>
                <?php
                    }
                ?>
                <div class="col-md-1"></div>
                <div class="col-md-5">
                    <h1>Login</h1><hr />
                    <form method="post">
                        <div class="input-group margin-bottom-sm" style="margin-bottom: 5px;">
                          <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                          <input class="form-control" type="text" name="username" placeholder="Username" />
                        </div>
                        <div class="input-group" style="margin-bottom: 5px;">
                          <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                          <input class="form-control" type="password" name="password" placeholder="Password" />
                        </div>
                        <div class="input-group" style="margin-bottom: 5px;">
                            <input class="btn btn-success" type="submit" name="login" value="Login" />
                        </div>
                    </form>
                    <div style="margin-right: 5px; float: right;">
                        <?php
                            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                                if(isset($_POST['login'])){
                                    if(!empty($_POST['username']) && !empty($_POST['password'])){
                                        $username       = $_POST['username'];
                                        $password       = $_POST['password'];
                                        
                                        echo $user->login($username, $password);
                                    }
                                    else{
                                        echo $emptyerror;
                                    }
                                }
                            }
                        ?>
                    </div>
                    <a href="<?php echo $website_url; ?>p/forgotpassword">Forgot password?</a>
                </div>
                <div class="col-md-5">
                    <h1>Register</h1><hr />
                    <form method="post">
                        <div class="input-group margin-bottom-sm" style="margin-bottom: 5px;">
                          <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                          <input class="form-control" type="text" name="username" placeholder="Username" required />
                        </div>
                        <div class="input-group margin-bottom-sm" style="margin-bottom: 5px;">
                          <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                          <input class="form-control" type="email" name="email" placeholder="Email" required />
                        </div>
                        <div class="input-group" style="margin-bottom: 5px;">
                          <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                          <input class="form-control" type="password" name="password" placeholder="Password" required />
                        </div>
                        <div class="input-group" style="margin-bottom: 5px;">
                          <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                          <input class="form-control" type="password" name="passwordconf" placeholder="Repeat password" required />
                        </div>
                        <div class="input-group" style="margin-bottom: 5px;">
                        <div style="display: none;">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input class="form-control" type="text" name="name" placeholder="Name" />
                        </div>
                        </div>
                        <div class="input-group" style="margin-bottom: 5px;">
                            <input class="btn btn-info" type="submit" name="register" value="Register" />
                        </div>
                    </form>
                    <div style="margin-right: 10px; float: right;">
                        <?php
                            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                                if(isset($_POST['register'])){
                                    if(!empty($_POST['username'])){
                                        if(!empty($_POST['email'])){
                                            if(!empty($_POST['password'])){
                                                if(!empty($_POST['passwordconf'])){
                                                    if(empty($_POST['name'])){ //Bot test
                                                        $username       = $_POST['username'];
                                                        $email          = $_POST['email'];
                                                        $password       = $_POST['password'];
                                                        $passwordconf   = $_POST['passwordconf'];

                                                        echo $user->register($username, $email, $password, $passwordconf);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        ?>
                    </div>
                </div>
                <div class="col-md-1"></div>
        <?php
        }
        ?>
</div>
