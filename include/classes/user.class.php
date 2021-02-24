<?php
    class user{
        private $handler,
                $uniqueCode,
                $purifier,
                $profile;

        public function __construct($handler, $uniqueCode, $purifier, $profile){
            $this->handler          = $handler;
            $this->uniqueCode       = $uniqueCode;
            $this->rand             = new randString();
            $this->purifier         = $purifier;
            $this->profile          = $profile;
            $this->pagination       = new pagination($this->handler);
        }

        //Check if user is logged in
        public function loggedIn(){
            if(isset($_SESSION[$this->uniqueCode])){
                return true;
            }
            else{
                return false;
            }
        }

        //Log out
        public function logout(){
            if(isset($_SESSION[$this->uniqueCode])){
                unset($_SESSION[$this->uniqueCode]);
                
                return true;
            }
            else{
                return false;
            }
        }

        //Get session username
        public function getSessionUsername(){
            return $_SESSION[$this->uniqueCode];
        }

        //Get session user id
        public function getUserId(){
            if($this->loggedIn()){
                $checkuser = $this->handler->prepare("SELECT * FROM users WHERE username = :username");
                $checkuser->execute([
                    ':username' => $this->getSessionUsername()
                    ]);

                $fetch = $checkuser->fetch(PDO::FETCH_ASSOC);

                return $fetch['u_id'];
            }
        }

        //Get avatar via id
        public function getSessionUserAvatar($optional = ['64']){
            global $website_url;
            
            if($this->loggedIn()){
                $checkuser = $this->handler->prepare("SELECT * FROM users WHERE username = :username");
                $checkuser->execute([
                    ':username' => $this->getSessionUsername()
                    ]);

                $fetch = $checkuser->fetch(PDO::FETCH_ASSOC);
                
                if($fetch['avatar'] == 'default.png'){
                    return'<img src="https://eu.ui-avatars.com/api/?name=' . $fetch['username'] . '&size=' . $optional[0] . '"/>';
                }
                else{
                    return'<img src="' . $website_url . '/images/avatars/' . $fetch['avatar'] . '" style="width: ' . $optional[0] . 'px; height: ' . $optional[0] . 'px;"/>';
                }
            }
        }
        
        //Register user
        public function register($username, $email, $password, $passwordconf){
            global $coreLang,
                   $registerLang,
                   $contactemail,
                   $mailer,
                   $mail;
            $users          = $this->handler->query('SELECT * FROM users');
            $usernamecheck  = $this->handler->prepare("SELECT * FROM users WHERE username = :username");
            $emailcheck     = $this->handler->prepare("SELECT * FROM users WHERE email = :email");
            
            $usernamecheck->execute([
                ':username' => $username
            ]);

            $emailcheck->execute([
                ':email'    => $email
            ]);

            $email_code     = md5($username . microtime());
            $rank           = ((!$users->rowCount())? 999 : -1);
            
            if(!in_array($username, $registerLang["bannedUsername"])){
                if(preg_match('/^[a-z\d]{2,255}$/i', $username)){
                    if(strlen($password) >= 5){
                        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                            if(!$usernamecheck->rowCount()){
                                if(!$emailcheck->rowCount()){
                                    if($password == $passwordconf){
                                        $options = [
                                            'cost' => 11
                                        ];
                                        $password   = password_hash($password, PASSWORD_BCRYPT, $options);

                                        $query = $this->handler->prepare('INSERT INTO users (username, password, email, email_code, rank, u_ip) VALUES (:username, :password, :email, :email_code, :rank, :u_ip)');

                                        try{
                                        $query->execute([
                                            ':username'     => $username,
                                            ':password'     => $password,
                                            ':email'        => $email,
                                            ':email_code'   => $email_code,
                                            ':rank'         => $rank,
                                            ':u_ip'         => $_SERVER['REMOTE_ADDR']
                                        ]);

                                        if($mailer === 0){
                                            mail($email, 'Account activation', "Please click this link to activate your account:\r\n" . $registerLang["redirect"] . "?p=activate&code=" . $email_code, "From: $contactemail");
                                        }
                                        elseif($mailer === 1){
                                            $mail->setFrom($contactemail);
                                            $mail->addAddress($email);     // Add a recipient
                                            $mail->isHTML(true);           // Set email format to HTML

                                            $mail->Subject = 'Account activation';
                                            $mail->Body    = "Please click this link to activate your account:\r\n" . $registerLang["redirect"] . "?p=activate&code=" . $email_code;

                                            if(!$mail->send()){
                                                return $registerLang['regsuccess'];
                                            }
                                        }

                                        return $registerLang["regsuccess"];
                                        }catch(PDOException $e){
                                            return $this->errorHandler->dbError();
                                        }
                                    }
                                    else{
                                        return $registerlang["passworderror"];
                                    }
                                }
                                else{
                                    return $registerLang["existingemail"];
                                }
                            }
                            else{
                                return $registerLang["existingusername"];
                            }
                        }
                        else{
                            return $registerLang["nomail"];
                        }
                    }
                    else{
                        return $registerLang["tooshort"];
                    }
                }
                else{
                    return $registerLang["invalidchar"];
                }
            }
            else{
                return $registerLang["usernameNotAllowed"];
            }
        }

        //Login user
        public function login($username, $password){
            global $coreLang,
                   $registerLang,
                   $website_url;
            
            $checkuser = $this->handler->prepare("SELECT * FROM users WHERE username = :username");
            $checkuser->execute([
                ':username' => $username
                ]);
            
            if($checkuser->rowcount() > 0){
                $fetch  = $checkuser->fetch(PDO::FETCH_ASSOC);
                $pw     = $fetch['password'];
                
                if(password_verify($password, $pw)){
                    if($fetch['rank'] >= 0){
                        $_SESSION[$this->uniqueCode] = $username;

                        return header("Location: $website_url");
                    }
                    else{
                        return $registerLang["notactive"];
                    }
                }
                else{
                    return $registerLang["incorrectpw"];
                }
            }
            else{
                return $registerLang['userdoesnotexist'];
            }
        }
        
        //Activate user
        public function activate($code){
            global $registerLang;
            
            $query = $this->handler->prepare('SELECT * FROM users WHERE email_code = :code');
            $query->execute([
                ':code' => $code
            ]);
            
            $fetch = $query->fetch(PDO::FETCH_ASSOC);

            if($query->rowCount() && $fetch['rank'] == -1){
                $query = $this->handler->prepare('UPDATE users SET rank = 1 WHERE email_code = :code');
                $query->execute([
                    ':code' => $code
                ]);

                return $registerLang['accountactivated'];
            }
            else{
                return $registerLang['doesnotexist'];
            }
        }

        //Check if user is banned
        public function isBanned(){
            if($this->loggedIn()){
                $user = $this->handler->prepare('SELECT * FROM users WHERE username = :username');
                $user->execute([
                    ':username' => $this->getSessionUsername()
                ]);
                
                $fetch = $user->fetch(PDO::FETCH_ASSOC);
    
                if($fetch['rank'] == 0){
                    return true;
                }
                else{
                    $query = $this->handler->prepare('SELECT * FROM warnings WHERE u_reported_id = :u_id');
                    $query->execute([
                        ':u_id' => $this->getUserId()
                    ]);
                    $x = 0;

                    while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                        $x += $fetch['amount'];
                    }

                    if($x >= 100){
                        $this->logout();
                        return true;
                    }
                    else{
                        return false;
                    }
                }
            }
        }

        //Return rank int
        public function getSessionRank(){
            if($this->loggedIn()){
                $user = $this->handler->prepare('SELECT * FROM users WHERE username = :username');
                $user->execute([
                    ':username' => $this->getSessionUsername()
                ]);
                
                $fetch = $user->fetch(PDO::FETCH_ASSOC);

                return $fetch['rank'];
            }
        }

        //Get channel id by username
        public function getChannelId($username, $optional = NULL){
            $query = $this->handler->prepare('SELECT * FROM users WHERE username = :username');
            $query->execute([
                ':username' => $username
            ]);

            if($optional == ['count']){
                return $query->rowCount();
            }
            else{
                $fetch = $query->fetch(PDO::FETCH_ASSOC);
                
                return $fetch['u_id'];
            }
        }
        
        //Check if user is subscribed
        public function subCheck($channelId){
            $query = $this->handler->prepare('SELECT * FROM subscriptions WHERE u_makerId = :channelId AND u_followerId = :currentId');
            $query->execute([
                ':channelId'    => $channelId,
                ':currentId'    => $this->getUserId()
            ]);
            
            if($channelId != $this->getUserId()){
                if($query->rowCount() == 0){
                    return 0;
                }
                else{
                    return 1;
                }
            }
            else{
                return 0;
            }
        }
        
        //Subscribe handler
        public function subscribe($channelName, $optional = NULL){
            if($this->getChannelId($channelName, ['count']) > 0 && $channelName != $this->getSessionUsername()){     
                if($this->subCheck($this->getChannelId($channelName)) == 0){
                    $query = $this->handler->prepare('INSERT INTO subscriptions (u_makerId, u_followerId) VALUES (:u_makerId, :u_followerId)');
                    $query->execute([
                        ':u_makerId'    => $this->getChannelId($channelName),
                        ':u_followerId'  => $this->getUserId()
                    ]);
                    
                    return 1;
                }
                else{
                    $query = $this->handler->prepare('DELETE FROM subscriptions WHERE u_makerId = :u_makerId AND u_followerId = :u_followerId');
                    $query->execute([
                        ':u_makerId'    => $this->getChannelId($channelName),
                        ':u_followerId'  => $this->getUserId()
                    ]);
                    $this->handler->query('ALTER TABLE subscriptions AUTO_INCREMENT = 1');

                    return 0;
                }
            }
            else{
                return 0;
            }
        }

        public function getCurrentUserCount(){
            $query = $this->handler->prepare('SELECT COUNT(*) FROM users');
            $query->execute();

            return $query->fetchColumn();
        }

        //New user count per month
        public function newUsersPerMonth(){
            $userArray = [];

            for($x = 1; $x <= 12; $x++){
                $joindate = date("Y") . '-' . (($x < 10)? '0' . $x : $x);
        
                $getUsersMonth = $this->handler->prepare('SELECT COUNT(*) as userCount FROM users WHERE joindate LIKE :joindate');
                $getUsersMonth->execute([':joindate' => "%{$joindate}%"]);
        
                $userArray[] .= $getUsersMonth->fetch(PDO::FETCH_ASSOC)['userCount'];
            }
        
            return json_encode($userArray);
        }

        public function getAllUsers($pagenumber, $optional = [25]){
            global $coreLang,
                   $searchLang,
                   $website_url;

            $str = null;

            $str = '<h2>All users</h2><hr />';
            
            $querya = $this->handler->prepare('SELECT COUNT(*) AS rowCount FROM users');
            $querya->execute();

            $total = $querya->fetch(PDO::FETCH_ASSOC)['rowCount'];

            if($total > 0){
                $pagenumber = ((isset($_GET['pn']) && is_numeric($_GET['pn']) && $_GET['pn'] > 0)? (int)$_GET['pn'] : 1);
                $startCount = $pagenumber*$optional[0];
                
                $pages = ceil($total / $optional[0]);

                if($pagenumber == 1){
                    $pages = (($pages > 0)? $pages : 1);
                }
                elseif($pagenumber > $pages){
                    header('Location: ' . $website_url . 'admin?a=users&pn=1');
                }
    
                $query = $this->handler->prepare('SELECT * FROM users WHERE u_id < :startcount ORDER BY u_id DESC LIMIT :perpage');
                try{
                    $query->execute([
                        ':startcount'   => $startCount,
                        ':perpage'      => $optional[0]
                    ]);
                }
                catch(PDOException $e){
                    return $e->getMessage();
                }

                $x = 0;
                $str .= $this->pagination->getPagination($pagenumber, $pages);
                $str .= '<table class="table">';

                while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                    $avatar         = $this->profile->getChannelDetails($fetch['username'], ['avatar', 100]);
                    $username       = $fetch['username'];
                    $description    = str_replace(["\r", "\n", "<br>", "<br />"], '', str_limit_html($this->profile->getChannelDetails($fetch['username'], ['description']), 1000));
                    $bannedStyle    = (($fetch['rank'] == 0)? 'style="background: #ffadad;"' : '');

                    $str .= <<<EOD
                        <tr $bannedStyle>
                            <td class="d-flex justify-content-center justify-content-md-start">
                                <a href="$website_url/channel/$username">
                                    $avatar
                                </a>
                                <a href="$website_url/admin?a=users&manage=$username" class="btn btn-sm btn-warning" style="color: black;">Manage</a>
                            </td>
                            <td class="w-100">
                                <div style="margin-left: 10px;">
                                    <a href="$website_url/channel/$username"><h2>$username</h2></a><br />
                                    <a href="$website_url/channel/$username"><small>$description</small></a><br />
                                </div>
                            </td>
                        </tr>
                    EOD;
                    
                    $x++;
                }

                $str .='</table>';
                $str .= $this->pagination->getPagination($pagenumber, $pages);

                return $str;
            }
            else{
                //No results
            }
        }

        public function setRank($username, $rank){
            global $website_url;
            
            $queryr = $this->handler->prepare('SELECT * FROM ranks WHERE r_id = :rank');
            $queryr->execute([
                ':rank' => $rank
            ]);
            
            if($queryr->rowCount()){
                $fetch = $queryr->fetch(PDO::FETCH_ASSOC);
                $query = $this->handler->prepare('UPDATE users LEFT JOIN ranks AS r ON r.r_id = :rank SET rank = r.rankValue WHERE username = :username');
                $query->execute([
                    ':rank'     => $rank,
                    ':username' => $username
                ]);

                $queryu = $this->handler->prepare('SELECT * FROM users WHERE username = :username');
                $queryu->execute([
                    ':username' => $username
                ]);
                $fetchu = $queryu->fetch(PDO::FETCH_ASSOC);
                
                if($fetch['rankName'] == 'Banned'){
                    $queryv = $this->handler->prepare('UPDATE videos SET v_hidden = 1 WHERE u_id = :u_id');
                    $queryv->execute([
                        ':u_id' => $fetchu['u_id']
                    ]);
                }
                else{
                    $queryv = $this->handler->prepare('UPDATE videos SET v_hidden = 0 WHERE u_id = :u_id');
                    $queryv->execute([
                        ':u_id' => $fetchu['u_id']
                    ]);
                }
            }

            header('Location: ' . $website_url . '/admin?a=users&manage=' . $username);
        }
    }
?>