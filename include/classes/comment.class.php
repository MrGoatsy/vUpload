<?php
    class comment{
        private $handler,
                $user,
                $video;

        public function __construct($handler, $user, $video){
            $this->handler  = $handler;
            $this->user     = $user;
            $this->video    = $video;
            $this->purifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());
        }

        //Get all comment details of v_id
        public function getCommentDetails($fileName, $optional = NULL){
            global $website_url;

            //Return row count
            if($optional[0] == 'rowCount'){
                $query = $this->handler->prepare('SELECT * FROM comments WHERE v_id = (SELECT v_id FROM videos WHERE v_fileName = :v_fileName)');
                $query->execute([
                    ':v_fileName'   => $fileName,
                ]);
                
                return $query->rowCount();
            }
            else{
                $query = $this->handler->prepare('SELECT * FROM comments WHERE v_id = (SELECT v_id FROM videos WHERE v_fileName = :v_fileName) ORDER BY c_date ' . $optional[2] . ' LIMIT ' . $optional[3] . ',1');
                $query->execute([
                    ':v_fileName'   => $fileName,
                ]);

                $fetch = $query->fetch(PDO::FETCH_ASSOC);

                //Return comment u_id
                if($optional[0] == 'id'){
                    return $fetch['u_id'];
                }
                //Return comment
                elseif($optional[0] == 'comment'){
                    return $fetch['comment'];
                }
                //Return comment username
                elseif($optional[0] == 'user'){
                    $queryU = $this->handler->prepare('SELECT * FROM users WHERE u_id = :u_id');
                    $queryU->execute([
                        ':u_id' => $fetch['u_id']
                    ]);

                    $fetchU = $queryU->fetch(PDO::FETCH_ASSOC);
                    
                    if($optional[1] == 'avatar'){
                        if($fetchU['avatar'] == 'default.png'){
                            return'<img src="https://eu.ui-avatars.com/api/?name=' . $fetchU['username'] . '&size=32"/>';
                        }
                        else{
                            return'<img src="' . $website_url . '/images/avatars/' . $fetchU['avatar'] . '" style="width: 32px; height: 32px;"/>';
                        }
                    }
                    elseif($optional[1] == 'username'){
                        return $fetchU['username'];
                    }
                }
                //Return comment post date
                elseif($optional[0] == 'date'){
                    return $fetch['c_date'];
                }
                //Return if comment is hidden
                elseif($optional[0] == 'hidden'){
                    return $fetch['c_hidden'];
                }
            }
        }
        //Get comment details of currently logged in user
        public function getCurrentUserCommentDetails($fileName, $optional = NULL){
            $query = $this->handler->prepare('SELECT * FROM comments WHERE v_id = (SELECT v_id FROM videos WHERE v_fileName = :v_fileName) AND u_id = :u_id  ORDER BY c_date DESC LIMIT 1');
            $query->execute([
                ':v_fileName'   => $fileName,
                'u_id'          => $this->user->getUserId()
            ]);
            
            if($query->rowCount()){
                $fetch = $query->fetch(PDO::FETCH_ASSOC);
                
                if($optional[0] == 'date'){
                    echo $fetch['c_date'];
                }
                elseif($optional[0] == 'comment'){
                    echo $fetch['comment'];
                }
            }
        }
        //Get all current user comments
        public function getAllCurrentUserComments($fileName){
            global $website_url;

            $username   = $this->user->getSessionUserName();
            $avatar     = $this->user->getSessionUserAvatar(['32']);

            $query = $this->handler->prepare('SELECT * FROM comments WHERE v_id = (SELECT v_id FROM videos WHERE v_fileName = :v_fileName) AND u_id = :u_id  ORDER BY c_date DESC');
            $query->execute([
                ':v_fileName'   => $fileName,
                'u_id'          => $this->user->getUserId()
            ]);
            
            if($query->rowCount()){
                while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                    echo <<<EOD
                            <a href="$website_url/channel/$username">$avatar</a>
                            <a href="$website_url/channel/$username">$username</a>
                            <small>$fetch[c_date]</small><br />
                            <small>$fetch[comment]</small><hr />
                    EOD;
                }
            }
        }
        //Post comment
        public function postComment($fileName, $comment){
            global $coreLang;

            $query = $this->handler->prepare('INSERT INTO comments (u_id, v_id, comment) VALUES (:u_id, :v_id, :comment)');
            try{
                $query->execute([
                    ':u_id'     => $this->user->getUserId(),
                    ':v_id'     => $this->video->getdetails('', $fileName, 'id'),
                    ':comment'  => $this->purifier->purify($comment)
                ]);

                echo'!=[]_Success';
                exit;
            }
            catch(PDOException $e){
                echo $coreLang['error'];
                exit;
            }
        }
    }
?>