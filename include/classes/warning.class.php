<?php
    class warning{
        private $handler,
                $user;

        public function __construct($handler, $user){
            $this->handler  = $handler;
            $this->user     = $user;
        }

        public function warn($username, $warning){
            global $website_url;

            $query = $this->handler->prepare('SELECT * FROM users WHERE username = :username');
            $query->execute([
                ':username' => $username
            ]);
            
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            
            if($query->rowCount() && $this->user->getSessionRank() > $fetch['rank']){
                $points = $this->handler->prepare('SELECT * FROM warningnames WHERE wn_id = :wn_id');
                $points->execute([
                    ':wn_id' => $warning
                ]);

                $query = $this->handler->prepare('INSERT INTO warnings (wn_id, u_reporter_id, u_reported_id) VALUES (:wn_id, :u_reporter_id, :u_reported_id)');
                try{
                    $query->execute([
                        ':wn_id'            => $points->fetch(PDO::FETCH_ASSOC)['wn_id'],
                        ':u_reporter_id'    => $this->user->getUserId(),
                        ':u_reported_id'    => $fetch['u_id'],
                    ]);
                }
                catch(PDOException $e){
                    echo $e->getMessage();
                }

                if($query){
                    $queryw = $this->handler->prepare('SELECT SUM(warningPoints) AS warningPoints FROM warnings LEFT JOIN warningnames ON warnings.wn_id = warningnames.wn_id WHERE u_reported_id = :u_reported_id');
                    $queryw->execute([
                        ':u_reported_id' => $fetch['u_id']
                    ]);

                    $fetchw = $queryw->fetch(PDO::FETCH_ASSOC);
                    $points = null;
                    
                    if($fetchw['warningPoints'] >= 100){
                        $query  = $this->handler->prepare('UPDATE users SET rank = 0 WHERE username = :username');
                        $queryv = $this->handler->prepare('UPDATE videos SET v_hidden = 1 WHERE u_id = :u_id');
                        $query->execute([
                            ':username' => $username
                        ]);
                        $queryv->execute([
                            ':u_id' => $fetch['u_id']
                        ]);
                    }
                    header('Location: ' . $website_url . '/admin?a=users&manage=' . $username);
                }

            }
            else{
                header('Location: ' . $website_url . '/admin?a=users&manage=' . $username);
            }
        }

        public function reportVideo($fileName, $reportReason, $reportedName){
            global $website_url,
                   $coreLang;

            $query = $this->handler->prepare('SELECT * FROM users WHERE username = :username');
            $query->execute([
                ':username' => $reportedName
            ]);
            $queryr = $this->handler->prepare('SELECT * FROM reports WHERE u_reporter_id = :u_reporter_id');
            $queryr->execute([
                ':u_reporter_id' => $this->user->getUserId()
            ]);
            
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            
            if($query->rowCount() && !$queryr->rowCount() && $this->user->getSessionRank() >= $fetch['rank']){
                $points = $this->handler->prepare('SELECT * FROM warningnames WHERE wn_id = :wn_id');
                $points->execute([
                    ':wn_id' => $reportReason
                ]);

                $queryV = $this->handler->prepare('SELECT * FROM videos WHERE v_fileName = :v_fileName');
                $queryV->execute([
                    ':v_fileName'   => $fileName
                ]);

                $query = $this->handler->prepare('INSERT INTO reports (wn_id, v_id, u_reporter_id, u_reported_id) VALUES (:wn_id, :v_id, :u_reporter_id, :u_reported_id)');
                try{
                    $query->execute([
                        ':wn_id'            => $points->fetch(PDO::FETCH_ASSOC)['wn_id'],
                        ':v_id'             => $queryV->fetch(PDO::FETCH_ASSOC)['v_id'],
                        ':u_reporter_id'    => $this->user->getUserId(),
                        ':u_reported_id'    => $fetch['u_id'],
                    ]);
                }
                catch(PDOException $e){
                    return $e->getMessage();
                }

                if($query){
                    return <<<EOD
                        <div class="alert alert-success" role="alert">
                            $coreLang[videoReport]
                        </div>
                    EOD;
                }

            }
            else{
                return <<<EOD
                <div class="alert alert-danger" role="alert">
                    $coreLang[videoReportFail]
                </div>
                EOD;
            }
        }

        public function getReports($optional = null){
            global $website_url;

            if($optional[0] == 'recent'){
                $query = $this->handler->prepare('SELECT reports.*, u.username AS rBy, uo.username AS rr, warningnames.warningInfo, videos.v_filename AS v_filename, videos.v_title AS title FROM reports LEFT JOIN users AS u ON u.u_id = reports.u_reporter_id LEFT JOIN users AS uo ON uo.u_id = u_reported_id LEFT JOIN warningnames ON warningnames.wn_id = reports.wn_id LEFT JOIN videos ON videos.v_id = reports.v_id ORDER BY reports.reportDate DESC');
                $query->execute();
            }
            else{
                $query = $this->handler->prepare('SELECT reports.*, users.username AS rBy, warningnames.warningInfo, videos.v_title AS title FROM reports LEFT JOIN users ON users.u_id = reports.u_reporter_id LEFT JOIN warningnames ON warningnames.wn_id = reports.wn_id LEFT JOIN videos ON videos.v_id = reports.v_id WHERE u_reported_id = (SELECT u_id FROM users WHERE username = :username)');
                $query->execute([
                    ':username' => $optional[0]
                ]);
            }
            
                $x = 0;
                $str = null;
            while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                $str .= '
                    <tr style="' . (($x%2 == 0)? 'background: #e3e3e3' : '') . '">
                        <td>
                            ' . $fetch['warningInfo'] . '
                        </td>
                        <td>
                            <a href="' . $website_url . '/watch?v=' . $fetch['v_filename'] . '">' . wordwrap($fetch['title'], 25, '<br />', true) . '</a>
                        </td>
                        <td>
                            <a href="' . $website_url . '/admin?a=users&manage=' . $fetch['rr'] . '">' . $fetch['rr'] . '</a>
                        </td>
                        <td>
                            <a href="' . $website_url . '/admin?a=users&manage=' . $fetch['rBy'] . '">' . $fetch['rBy'] . '</a>
                        </td>
                        <td>
                            ' . $fetch['reportDate'] . '
                        </td>
                    </tr>';

                $x++;
            }

            return $str;
        }
        
        public function getWarnings($username){
            global $website_url;

            $query = $this->handler->prepare('SELECT warnings.*, users.username AS rBy, warningnames.* FROM warnings LEFT JOIN users ON users.u_id = warnings.u_reporter_id INNER JOIN warningnames ON warningnames.wn_id = warnings.wn_id WHERE u_reported_id = (SELECT u_id FROM users WHERE username = :username)');
            $query->execute([
                ':username' => $username
            ]);
                
            $x = 0; 
            $str = null;
            while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                $str .= '
                    <tr style="border-left: 1px solid; ' . (($x%2 == 0)? 'background: #e3e3e3' : '') . '">
                        <td>
                            ' . $fetch['warningInfo'] . '
                        </td>
                        <td>
                            <a href="' . $website_url . '/admin?a=users&manage=' . $fetch['rBy'] . '">' . $fetch['rBy'] . '</a>
                        </td>
                        <td>
                            ' . $fetch['warningDate'] . '
                        </td>
                    </tr>';

                $x++;
            }

            return $str;
        }
    }
?>