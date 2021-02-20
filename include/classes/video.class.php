<?php
    class video{
        private $handler,
                $ftpServer,
                $rand,
                $user,
                $purifier,
                $profile;

        public function __construct($handler, $ftpServer, $user, $purifier, $profile){
            $this->handler          = $handler;
            $this->ftpServer        = $ftpServer;
            $this->rand             = new randString();
            $this->user             = $user;
            $this->purifier         = $purifier;
            $this->other            = new other();
            $this->imageConverter   = new imageConverter();
            $this->profile          = $profile;
        }

        //Video upload handler
        public function upload($file, $title, $desc, $tags){
            global $website_url,
                   $coreLang,
                   $videoMessage,
                   $uploadServer,
                   $ffmpeg,
                   $max_upload;

            $file = $_FILES['video'];
            $fileName   = $this->rand->String() . uniqid();

            if($file['error'] === 0){
                $mimeType = mime_content_type($file['tmp_name']);
                $fileType = explode('/', $mimeType)[0];
                
                if(is_uploaded_file($file['tmp_name'])){
                    if ($fileType === 'video'){
                        $sourcePath = $file['tmp_name'];
                        $extension  = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $targetPath = "videos/users/" . $fileName;
                        $message    = '!=[]_' . $videoMessage['videoSuccess'] . ' Click here to watch it: <a href="' . $website_url . '/watch?v=' . $fileName . '">' . $website_url . '/watch?v=' . $fileName . '</a>';

                        if(move_uploaded_file($sourcePath, $targetPath . '.' . $extension)){
                            $query = $this->handler->prepare('INSERT INTO videos (u_id, v_filename, v_extension, v_title, v_desc, v_tags) VALUES (:u_id, :v_filename, :v_extension, :v_title, :v_desc, :v_tags)');
                            
                            try{
                                $query->execute([
                                    ':u_id'         => $this->user->getUserId(),
                                    ':v_filename'   => $fileName,
                                    ':v_extension'  => $extension,
                                    ':v_title'      => $this->purifier->purify(htmlentities($title, ENT_QUOTES)),
                                    ':v_desc'       => $this->purifier->purify($desc),
                                    ':v_tags'       => str_replace(', ', ',', $this->purifier->purify(htmlentities($tags, ENT_QUOTES))),
                                ]);

                                rename($targetPath . '.' . $extension, $targetPath . '1.' . $extension);

                                $video = $ffmpeg->open($targetPath . '1.' . $extension);
                                $format = new \FFMpeg\Format\Video\X264();
                                $format->setVideoCodec('libx264');
                                $format->setAudioCodec('aac');
                                $format->setAdditionalParameters(['-preset', 'fast', '-crf', '24']);
                                $video->save($format, $targetPath . '.mp4');
                                //$this->other->execInBackground('process.php', $targetPath, $extension, $this->getDetails('', $fileName, 'id'));
                                unlink($targetPath . '1.' . $extension);

                                if($uploadServer == 1){
                                    // set up basic connection
                                    $conn_id = ftp_connect($this->ftpServer['server'], $this->ftpServer['port']);

                                    // login with username and password
                                    $login_result = ftp_login($conn_id, $this->ftpServer['username'], $this->ftpServer['password']);

                                    ftp_pasv($conn_id, true);

                                    // upload a file
                                    if (ftp_put($conn_id, $this->ftpServer['folder'] . $fileName . '.' . $extension, $targetPath . '.' . $extension, FTP_ASCII)){
                                        unlink($targetPath . '.' . $extension);

                                        $query = $this->handler->prepare('INSERT INTO videoservers (`v_id`, `v_server`) VALUES (:v_id, :v_server)');
                                        $query->execute([
                                            ':v_id'     => $fileName . '.' . $extension,
                                            ':v_server' => $ftpServer['location']
                                        ]);
                                        echo $message;
                                    }
                                    else{
                                        echo $videoMessage['externalError'];
                                        exit;
                                    }
                                }
                                else{
                                    echo $message;
                                }

                                if($query){
                                    if(isset($_FILES['thumbnail'])){
                                        $thumbnail  = $_FILES['thumbnail'];
        
                                        if(!empty($thumbnail['name'])){
                                            $this->uploadThumbnail([$thumbnail, $fileName, 1]);
                                        }
                                        else{
                                            echo $this->uploadThumbnail(['', $fileName, [
                                                "targetPath"    => $targetPath,
                                                "extension"     => $extension,
                                                0
                                                ]]);
                                        }
                                    }

                                    $query      = $this->handler->prepare('UPDATE videos SET v_hidden = :v_hidden WHERE v_id = :v_id AND u_id = :u_id');
                                    $query->execute([
                                        ':v_id'     => $this->getdetails('', $fileName, 'id'),
                                        ':u_id'     => $this->user->getUserId(),
                                        ':v_hidden' => 0
                                    ]);
                                }
                            }
                            catch(PDOException $e){
                                return $this->errorHandler->dbError();
                                exit;
                            }
                        }
                        else{
                            return $coreLang['error'];
                            exit;
                        }
                    }
                    else{
                        return $videoMessage['noVideo'];
                        exit;
                    }
                }
                else{
                    return $coreLang['error'];
                    exit;
                }
            }
            elseif($file['error'] === 1 || filesize('/videos/users/' . $fileName)){
                return $videoMessage['tooBig'];
                exit;
            }
            elseif($file['error'] === 4){
                return $videoMessage['noFile'];
                exit;
            }
            elseif($file['error'] === 7){
                return $videoMessage['diskError'];
                exit;
            }
            else{
                return $coreLang['error'];
                exit;
            }
        }

        //Video upload handler
        public function editVideo($file, $fileName, $title, $desc, $tags){
            global $website_url,
                   $coreLang,
                   $videoMessage;

            $message    = '!=[]_' . $videoMessage['videoSuccess'] . ' Click here to watch it: <a href="' . $website_url . '/watch?v=' . $fileName . '">' . $website_url . '/watch?v=' . $fileName . '</a>';
            $query      = $this->handler->prepare('UPDATE videos SET v_title = :v_title, v_desc = :v_desc, v_tags = :v_tags WHERE v_filename = :v_filename');
            
            try{
                $query->execute([
                    ':v_title'      => $this->purifier->purify(htmlentities($title, ENT_QUOTES)),
                    ':v_desc'       => $this->purifier->purify($desc),
                    ':v_tags'       => str_replace(', ', ',', $this->purifier->purify(htmlentities($tags, ENT_QUOTES))),
                    ':v_filename'   => $this->purifier->purify($fileName)
                ]);
                
                echo $this->uploadThumbnail([$file, $fileName, 0]);

                echo $message;
            }
            catch(PDOException $e){
                echo $this->errorHandler->dbError();
                exit;
            }
        }

        //Thumbnail upload handler
        public function uploadThumbnail($optional = []){
            global $ffmpeg,
                   $ffprobe;

            $file       = (($optional[2] == 0)? $optional[0]['thumbnail'] : $optional[0]);
            $fileName   = $optional[1];
            $query      = $this->handler->prepare('UPDATE videos SET v_thumbnail = :v_thumbnail WHERE v_id = :v_id AND u_id = :u_id');
            $targetPath = "videos/users/thumbnails/" . $fileName;

            if(!empty($file['name'])){
                if($file['error'] === 0){
                    $mimeType = mime_content_type($file['tmp_name']);
                    $fileType = explode('/', $mimeType)[0];
                    
                    if(is_uploaded_file($file['tmp_name'])){
                        if ($fileType === 'image'){
                            $sourcePath = $file['tmp_name'];
                            $extension  = pathinfo($file['name'], PATHINFO_EXTENSION);

                            if(move_uploaded_file($sourcePath, $targetPath . '1.' . $extension)){
                                $this->imageConverter->convert($targetPath . '1.' . $extension, $targetPath . '.jpg', 100);
                                unlink($targetPath . '1.' . $extension);
                                
                                $query->execute([
                                    ':v_thumbnail'  => $fileName . '.jpg',
                                    ':v_id'         => $this->getDetails('', $fileName, 'id'),
                                    ':u_id'         => $this->user->getUserId()
                                ]);
                            }
                        }
                    }
                }
            }
            elseif(empty($file['name'])){
                $queryT = $this->handler->prepare('SELECT * FROM videos WHERE v_id = :v_id AND u_id = :u_id');
                $queryT->execute([
                    ':v_id' => $this->getDetails('', $fileName, 'id'),
                    ':u_id' => $this->user->getUserId()
                ]);
                
                $fetch = $queryT->fetch(PDO::FETCH_ASSOC);
                
                if($queryT->rowCount() && empty($fetch['v_thumbnail'])){
                    $movie = 'videos/users/' . $fileName . '.mp4';
                    $sec = $ffprobe->format($movie)->get('duration') / rand(10,100);
                    $thumbnail = "videos/users/thumbnails/" . $fileName . '1.jpg';
    
                    $video = $ffmpeg->open($movie);
                    $frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($sec));

                    $frame->save($thumbnail);
                    
                    $this->imageConverter->convert($thumbnail, $targetPath . '.jpg', 100);
                    unlink($thumbnail);
    
                    $query->execute([
                        ':v_thumbnail'  => $fileName,
                        ':v_id'         => $this->getDetails('', $fileName, 'id'),
                        ':u_id'         => $this->user->getUserId()
                    ]);
                }
            }
        }

        //Delete video
        public function deleteVideo($fileName, $extension){
            global $website_url,
                   $coreLang,
                   $videoMessage;


            $query = $this->handler->prepare('SELECT * FROM videos WHERE v_fileName = :v_fileName');
            $query->execute([
                ':v_fileName'   => $fileName
            ]);

            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            $x = (($this->user->getUserId() == $fetch['u_id'] || $this->profile->getChannelDetails($this->user->getSessionUsername(), ['rank', 'int']) >= 950)? 1 : 0);
            
            if($x = 1){
                unlink("videos/users/" . $fileName . '.' . $extension);
                //unlink("videos/users/thumbnails/" . $fileName . '.jpg');

                $message    = '!=[]_' . $videoMessage['videoDeleted'];
                $query      = $this->handler->prepare('UPDATE videos SET v_title = :v_title, v_hidden = :v_hidden WHERE v_filename = :v_filename');
                $query->execute([
                    ':v_title'      => $fetch['v_title'] . ' [DELETED]',
                    ':v_hidden'     => 1,
                    ':v_filename'   => $fileName,
                ]);
            }
            else{
                header('Location: ' . $website_url);
            }
        }

        //Add to watch history
        public function addViewer($fileName){
            $query = $this->handler->prepare('SELECT * FROM history WHERE v_id = (SELECT v_id FROM videos WHERE v_fileName = :v_fileName AND v_hidden = 0) AND u_id = :u_id');
            $query->execute([
                ':v_fileName' => $this->purifier->purify($fileName),
                ':u_id'       => $this->user->getUserId()
            ]);

            if(!$query->rowCount() && $this->user->loggedIn()){
                $newViewerQuery = $this->handler->prepare('INSERT INTO history (u_id, v_id) VALUES (:u_id, :v_id)');
                    $newViewerQuery->execute([
                        ':u_id'     => $this->user->getUserId(),
                        ':v_id'     => $this->getDetails('', $fileName, 'id')
                    ]);
            }
            elseif(!$query->rowCount() && !$this->user->loggedIn()){
                $queryn = $this->handler->prepare('SELECT * FROM history WHERE v_id = (SELECT v_id FROM videos WHERE v_fileName = :v_fileName AND v_hidden = 0) AND v_loggedOut = :v_loggedOut');
                $queryn->execute([
                    ':v_fileName'       => $this->purifier->purify($fileName),
                    ':v_loggedOut'      => $_SERVER['REMOTE_ADDR']
                ]);

                if(!$queryn->rowCount()){
                    $newViewerQuery = $this->handler->prepare('INSERT INTO history (u_id, v_id, v_loggedOut) VALUES (:u_id, :v_id, :v_loggedOut)');
                    $newViewerQuery->execute([
                        ':u_id'         => NULL,
                        ':v_id'         => $this->getDetails('', $fileName, 'id'),
                        ':v_loggedOut'  => $_SERVER['REMOTE_ADDR']
                    ]);
                }
            }
            else{
                return false;
            }
        }
        
        //Add like or dislike and updates if the users changes rating
        public function setLord($fileName, $optional = NULL){
            $query = $this->handler->prepare('SELECT * FROM history WHERE v_id = :v_id AND u_id = :u_id');
            try{
            $query->execute([
                ':v_id'         => $this->getDetails('', $fileName, 'id'),
                ':u_id'         => $this->user->getUserId()
            ]);
            }catch(PDOException $e){
                return $this->errorHandler->dbError();
            }
            
            $fetch = $query->fetch(PDO::FETCH_ASSOC);

            if(!$query->rowCount()){
                $fetch = $query->fetch(PDO::FETCH_ASSOC);
                $liked = $this->handler->prepare('INSERT INTO history (`u_id`, `v_id`, `lord`) VALUES (:u_id, :v_id, :lord)');
                
                if($optional == 'like'){
                    $liked->execute([
                        ':u_id'     => $this->user->getUserId(),
                        ':v_id'     => $this->getDetails('', $fileName, 'id'),
                        ':lord'     => 1
                    ]);
                }
                elseif($optional == 'dislike'){
                    $liked->execute([
                        ':u_id'     => $this->user->getUserId(),
                        ':v_id'     => $this->getDetails('', $fileName, 'id'),
                        ':lord'     => -1
                    ]);
                }
                else{
                    return $coreLang['error'];
                }
            }
            else{
                    if($optional == 'like'){
                        if($this->getLord($fileName, 2) == 1){
                            $like = NULL;
                        }
                        elseif($this->getLord($fileName, 2) == 0){
                            $like = 1;
                        }
                        elseif($this->getLord($fileName, 2) == -1){
                            $like = 1;
                        }
                    }
                    elseif($optional == 'dislike'){
                        if($this->getLord($fileName, 2) == -1){
                            $like = NULL;
                        }
                        elseif($this->getLord($fileName, 2) == 0){
                            $like = -1;
                        }
                        elseif($this->getLord($fileName, 2) == 1){
                            $like = -1;
                        }
                    }

                    $lordQuery = $this->handler->prepare('UPDATE history SET lord = :lord WHERE h_id = :h_id');
                    $lordQuery->execute([
                        ':lord'         => $like,
                        ':h_id'         => $fetch['h_id']
                    ]);
            }
        }

        //Get likes and dislikes
        public function getLord($fileName, $lord){;
            if($lord == 2){
                $query = $this->handler->prepare('SELECT * FROM history WHERE v_id = :v_id AND u_id = :u_id');
                    $query->execute([
                        ':v_id'         => $this->getDetails('', $fileName, 'id'),
                        ':u_id'         => $this->user->getUserId()
                    ]);

                    if($query->rowCount()){
                        $fetch = $query->fetch(PDO::FETCH_ASSOC);

                        return $fetch['lord'];
                    }
                    else{
                        return false;
                    }
            }
            else{
                $query = $this->handler->prepare('SELECT COUNT(*) FROM history WHERE v_id = :v_id AND lord = :lord');
                    $query->execute([
                        ':v_id'         => $this->getDetails('', $fileName, 'id'),
                        ':lord'         => $lord
                    ]);
                
                if($query->rowCount()){
                    $fetch = $query->fetchColumn();
                    
                    return $fetch;
                }
                else{
                    return 0;
                }
            }
        }

        //Get the title, description and viewcount by id or filename
        public function getDetails($v_id, $fileName, $optional = NULL){
            global $website_url;
            
            $query = $this->handler->prepare('SELECT * FROM videos WHERE (v_id = :v_id OR v_fileName = :v_fileName)');
            $query->execute([
                ':v_id'         => $this->purifier->purify($v_id),
                ':v_fileName'   => $this->purifier->purify($fileName)
            ]);
            
            if($query->rowCount()){
                $fetch = $query->fetch(PDO::FETCH_ASSOC);

                //Return id
                if($optional == 'id'){
                    return $fetch['v_id'];
                }
                elseif($optional == 'count'){
                    return $query->rowCount();
                }
                //Return filename and extension
                elseif($optional == 'videoURL'){
                    return $fetch['v_fileName'] . '.' . $fetch['v_extension'];
                }
                //Return filename
                elseif($optional == 'videoFileName'){
                    return $fetch['v_fileName'];
                }
                elseif($optional == 'extension'){
                    return $fetch['v_extension'];
                }
                //Return title
                elseif($optional == 'title'){
                    return $fetch['v_title'];
                }
                //Return description
                elseif($optional == 'desc'){
                    return $fetch['v_desc'];
                }
                //Return upload date and time
                elseif($optional == 'time'){
                    return $fetch['v_uploadtime'];
                }
                elseif($optional == 'thumbnail'){
                    return $fetch['v_thumbnail'];
                }
                elseif($optional == 'hidden'){
                    return $fetch['v_hidden'];
                }
                //Return view count
                elseif($optional == 'views'){
                    $viewQuery = $this->handler->prepare('SELECT COUNT(*) FROM history WHERE v_id = :v_id');
                    $viewQuery->execute([
                        'v_id'  => $fetch['v_id']
                    ]);
                    
                    return $viewQuery->fetchColumn();
                }
                //Return tags
                elseif($optional == 'tags' || $optional == 'tagsLink'){
                    if($optional == 'tags'){
                        echo str_replace(',', ', ', $this->purifier->purify($fetch['v_tags']));
                    }
                    elseif($optional == 'tagsLink'){
                        $tags = explode(',', $fetch['v_tags']);

                        foreach($tags as $tag){
                            echo'<a href="' . $website_url . '/search?q=' . $tag . '">' . $tag . '</a> ';
                        }
                    }
                }
                //Return channel name, id or avatar
                else{
                    $viewQuery = $this->handler->prepare('SELECT * FROM users WHERE u_id = (SELECT u_id FROM videos WHERE v_id = :v_id)');
                    $viewQuery->execute([
                        'v_id'  => $this->getdetails('', $fileName, 'id') 
                    ]);
                    $fetch = $viewQuery->fetch(PDO::FETCH_ASSOC);

                    if($optional == 'channel'){
                        return $fetch['username'];
                    }
                    elseif($optional == 'u_id'){
                        return $fetch['u_id'];
                    }
                    elseif($optional[0] == 'avatar'){
                        if($fetch['avatar'] == 'default.png'){
                            return'<img src="https://eu.ui-avatars.com/api/?name=' . $fetch['username'] . '&size=' . $optional[1] . '"/>';
                        }
                        else{
                            return'<img src="' . $website_url . '/images/avatars/' . $fetch['avatar'] . '" style="width: ' . $optional[1] . 'px; height: ' . $optional[1] . 'px;"/>';
                        }
                    }
                }
            }
            else{
                //header('Location: ' . $website_url);
            }
        }

        //Get video details by user id
        public function getVideo($userid, $what, $optional = []){
            if($what == 'count'){
                $query = $this->handler->prepare('SELECT COUNT(*) FROM videos WHERE u_id = :u_id AND v_hidden = 0');
                $query->execute([
                    ':u_id'     => $this->user->getChannelId($userid)
                ]);
    
                if($query->rowCount()){
                    $fetch = $query->fetchColumn();
    
                    return $fetch;
                }
            }
            elseif($what == 'details'){
                $query = $this->handler->prepare('SELECT * FROM videos WHERE (u_id = :u_id AND v_hidden = 0) ORDER BY v_uploadtime DESC LIMIT :climit,1');
                $query->execute([
                    ':u_id'     => $this->user->getChannelId($userid),
                    ':climit'   => $optional[1]
                ]);

                $fetch = $query->fetch(PDO::FETCH_ASSOC);
                
                if($optional[0] == 'id'){
                    return $fetch['v_id'];
                }
                elseif($optional[0] == 'videoURL'){
                    return $fetch['v_fileName'] . '.' . $fetch['v_extension'];
                }
                elseif($optional[0] == 'videoFileName'){
                    return $fetch['v_fileName'];
                }
                elseif($optional[0] == 'title'){
                    return $fetch['v_title'];
                }
                elseif($optional[0] == 'thumbnail'){
                    return $fetch['v_thumbnail'];
                }
            }
        }
        
        //Get most recent videos from all users
        public function getRecentVideos($what, $optional = []){
            global $website_url;
            
            if($what == 'count'){
                if(isset($optional['all'])){
                    $query = $this->handler->prepare('SELECT COUNT(*) FROM videos');
                    $query->execute();
                }
                else{
                    $query = $this->handler->prepare('SELECT COUNT(*) FROM videos WHERE v_hidden = 0');
                    $query->execute();
                }
    
                if($query->rowCount()){
                    $fetch = $query->fetchColumn();
    
                    return $fetch;
                }
            }
            
            if($what == 'details'){
                if(isset($optional['all'])){
                    $query = $this->handler->prepare('SELECT *, (SELECT username FROM users WHERE users.u_id = videos.u_id) AS username FROM videos ORDER BY ' . $optional['orderByWhat'] . ' ' . $optional['DESCASC'] . ' LIMIT :climit, 1');
                }
                else{
                    $query = $this->handler->prepare('SELECT *, (SELECT username FROM users WHERE users.u_id = videos.u_id) AS username FROM videos WHERE v_hidden = 0 ORDER BY ' . $optional['orderByWhat'] . ' ' . $optional['DESCASC'] . ' LIMIT :climit, 1');
                }
                $query->execute([
                    ':climit'       => $optional['amount'],
                ]);

                if($query->rowCount()){
                    $fetch = $query->fetch(PDO::FETCH_ASSOC);

                    if($optional[0] == 'videoURL'){
                        return $fetch['v_fileName'];
                    }
                    elseif($optional[0] == 'uploadTime'){
                        return $fetch['v_uploadTime'];
                    }
                    elseif($optional[0] == 'thumbnail'){
                        return $fetch['v_fileName'] . '.jpg';
                    }
                    elseif($optional[0] == 'title'){
                        return $fetch['v_title'];
                    }
                    elseif($optional[0] == 'description'){
                        return $fetch['v_desc'];
                    }
                    elseif($optional[0] == 'tags'){
                        $tags = explode(',', $fetch['v_tags']);

                        foreach($tags as $tag){
                            echo'<a href="' . $website_url . '/search?q=' . $tag . '">' . $tag . '</a>&nbsp;';
                        }
                    }
                    elseif($optional[0] == 'username'){
                        return $fetch['username'];
                    }
                }
            }
        }
        
        //Current video count
        public function getCurrentVideoCount($optional = NULL){
            if($optional[0] == 'all'){
                $query = $this->handler->prepare('SELECT COUNT(*) FROM videos');
                $query->execute();
    
                return $query->fetchColumn();
            }
            elseif($optional[0] == 'noDeleted'){
                $query = $this->handler->prepare('SELECT COUNT(*) FROM videos WHERE v_hidden = 0');
                $query->execute();
    
                return $query->fetchColumn();
            }
        }

        //New videos per month
        public function newVideosPerMonth(){
            $videoArray = [];

            for($x = 1; $x <= 12; $x++){
                $vuploadtime = date("Y") . '-' . (($x < 10)? '0' . $x : $x);
        
                $videosPerMonth = $this->handler->prepare('SELECT COUNT(*) as videoCount FROM videos WHERE v_uploadtime LIKE :v_uploadtime');
                $videosPerMonth->execute([':v_uploadtime' => "%{$vuploadtime}%"]);
        
                $videoArray[] .= $videosPerMonth->fetch(PDO::FETCH_ASSOC)['videoCount'];
            }
        
            return json_encode($videoArray);
        }
    }
?>