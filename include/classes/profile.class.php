<?php
    class profile{
        private $handler,
                $purifier,
                $user;

        public function __construct($handler, $purifier, $user = NULL){
            $this->handler          = $handler;
            $this->rand             = new randString();
            $this->purifier         = $purifier;
            $this->user             = $user;
            $this->imageConverter   = new imageConverter();
        }
        
        //Check if the URL is a URL
        public function editChannelSafety($website){
            if(empty($website)){
                return true;
            }
            else{
                if(filter_var($website, FILTER_VALIDATE_URL)){
                    return true;
                }
                else{
                    return false;
                }
            }
        }

        //Upload avatar
        public function editChannel($file, $description, $website){
            global $coreLang,
                   $channelLang,
                   $website_url;

            $file           = $file['avatar'];
            $description    = $this->purifier->purify($_POST['description']);
            $website        = $this->purifier->purify($_POST['website']);
            
            if($this->editChannelSafety($website)){
                if($file['error'] != 4){
                    if($file['error'] === 0){
                        $mimeType = mime_content_type($file['tmp_name']);
                        $fileType = explode('/', $mimeType)[0];
                        
                        if(is_uploaded_file($file['tmp_name'])){
                            if ($fileType === 'image'){
                                $sourcePath = $file['tmp_name'];
                                $extension  = pathinfo($file['name'], PATHINFO_EXTENSION);
                                $fileName   = $this->user->getSessionUsername();
                                $targetPath = "images/avatars/" . $fileName;
    
                                if(move_uploaded_file($sourcePath, $targetPath . '1.' . $extension)){
                                    $this->imageConverter->convert($targetPath . '1.' . $extension, $targetPath . '.jpg', 100);
                                    unlink($targetPath . '1.' . $extension);
                                    
                                    $query = $this->handler->prepare('UPDATE users SET u_desc = :u_desc, website = :website, avatar = :avatar WHERE u_id = :u_id');
                                    $query->execute([
                                        ':u_desc'   => $description,
                                        ':website'  => $website,
                                        ':avatar'   => $fileName . '.jpg',
                                        ':u_id'     => $this->user->getUserId()
                                    ]);

                                    return'<div class="alert alert-success">' . $channelLang['profileUpdated'] . '</div>';
                                }
                            }
                        }
                    }
                }
                else{
                    $query = $this->handler->prepare('UPDATE users SET u_desc = :u_desc, website = :website WHERE u_id = :u_id');
                    $query->execute([
                        ':u_desc'   => $description,
                        ':website'  => $website,
                        ':u_id'     => $this->user->getUserId()
                    ]);
                    
                    //return'<div class="alert alert-success">' . $channelLang['profileUpdated'] . '</div>';
                    header('Location: ' . $website_url . '/channel/' . $this->user->getSessionUsername());
                }
            }
        }

        //Get channel details
        public function getChannelDetails($channel, $optional = [NULL, NULL]){
            global $website_url;

            $optional[1] = ((empty($optional[1])? '64' : $optional[1]));
            $query = $this->handler->prepare('SELECT * FROM users WHERE username = :username');
            $query->execute([
                ':username' => $channel
            ]);

            if($query->rowCount()){
                $fetch = $query->fetch(PDO::FETCH_ASSOC);

                if($optional[0] == 'avatar'){
                    if($fetch['avatar'] == 'default.png'){
                        return'<img src="https://eu.ui-avatars.com/api/?name=' . $fetch['username'] . '&size=' . $optional[1] . '"/>';
                    }
                    else{
                        return'<img src="' . $website_url . '/images/avatars/' . $fetch['avatar'] . '" style="width: ' . $optional[1] . 'px; height: ' . $optional[1] . 'px;"/>';
                    }
                }
                elseif($optional[0] == 'id'){
                    return $fetch['u_id'];
                }
                elseif($optional[0] == 'description'){
                    return $fetch['u_desc'];
                }
                elseif($optional[0] == 'website'){
                    return $fetch['website'];
                }
                elseif($optional[0] == 'joindate'){
                    return $fetch['joindate'];
                }
                elseif($optional[0] == 'rank'){
                    $queryRank = $this->handler->prepare('SELECT * FROM ranks WHERE rankValue = :rankValue');
                    $queryRank->execute([
                        ':rankValue' => $fetch['rank']
                    ]);

                    if($optional[1] == 'int'){
                        return $queryRank->fetch(PDO::FETCH_ASSOC)['rankValue'];
                    }
                    elseif($optional[1] == 'name'){
                        return $queryRank->fetch(PDO::FETCH_ASSOC)['rankName'];
                    }
                }
                elseif($optional[0] == 'videoCount'){
                    $queryVideo = $this->handler->prepare('SELECT COUNT(*) FROM videos WHERE u_id = :u_id AND v_hidden != 1');
                    $queryVideo->execute([
                        ':u_id' => $fetch['u_id']
                    ]);
                    
                    return $queryVideo->fetchColumn();
                }
            }
        }
    }
?>