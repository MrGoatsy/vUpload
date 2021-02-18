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

                    $fetch = $queryw->fetch(PDO::FETCH_ASSOC);
                    $points = null;
                    
                    if($fetch['warningPoints'] >= 100){
                        $query = $this->handler->prepare('UPDATE users SET rank = 0 WHERE username = :username');
                        $query->execute([
                            ':username' => $username
                        ]);
                    }
                    header('Location: ' . $website_url . '/admin?a=users&warn=' . $username);
                }

            }
            else{
                header('Location: ' . $website_url . '/admin?a=users&warn=' . $username);
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
            //echo $query->rowCount() . $queryr->rowCount() . $this->user->getSessionRank() . $fetch['rank']; die();
            if($query->rowCount() && !$queryr->rowCount() && $this->user->getSessionRank() >= $fetch['rank']){
                $points = $this->handler->prepare('SELECT * FROM warningnames WHERE wn_id = :wn_id');
                $points->execute([
                    ':wn_id' => $reportReason
                ]);

                $query = $this->handler->prepare('INSERT INTO reports (wn_id, u_reporter_id, u_reported_id) VALUES (:wn_id, :u_reporter_id, :u_reported_id)');
                try{
                    $query->execute([
                        ':wn_id'            => $points->fetch(PDO::FETCH_ASSOC)['wn_id'],
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

        public function getReports($username){
            global $website_url;

            $query = $this->handler->prepare('SELECT reports.*, users.username AS rBy, warningnames.warningInfo FROM reports LEFT JOIN users ON users.u_id = reports.u_reporter_id INNER JOIN warningnames ON warningnames.wn_id = reports.wn_id WHERE u_reported_id = (SELECT u_id FROM users WHERE username = :username)');
            $query->execute([
                ':username' => $username
            ]);
            
                $x = 0;
                $str = null;
            while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                $str .= '
                    <tr style="border-right: 1px solid; ' . (($x%2 == 0)? 'background: #e3e3e3' : '') . '">
                        <td>
                            ' . $fetch['warningInfo'] . '
                        </td>
                        <td>
                            <a href="' . $website_url . '/admin?a=users&warn=' . $fetch['rBy'] . '">' . $fetch['rBy'] . '</a>
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
                            <a href="' . $website_url . '/admin?a=users&warn=' . $fetch['rBy'] . '">' . $fetch['rBy'] . '</a>
                        </td>
                        <td>
                            ' . $fetch['warningDate'] . '
                        </td>
                    </tr>';

                $x++;
            }

            return $str;
        }

        public function getRecentReports($amount = NULL){
            global $website_url;
            
            $query = $this->handler->prepare('SELECT reports.*, u.username AS rBy, uo.username AS rr, warningnames.warningInfo FROM reports LEFT JOIN users AS u ON u.u_id = reports.u_reporter_id LEFT JOIN users AS uo ON uo.u_id = u_reported_id INNER JOIN warningnames ON warningnames.wn_id = reports.wn_id ORDER BY reports.reportDate DESC');
            $query->execute();
                
            $x = 0;
            $str = null;
            
            while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                $str .= '
                    <tr style="' . (($x%2 == 0)? 'background: #e3e3e3' : '') . '">
                        <td>
                            ' . $fetch['warningInfo'] . '
                        </td>
                        <td>
                            <a href="' . $website_url . '/admin?a=users&warn=' . $fetch['rBy'] . '">' . $fetch['rBy'] . '</a>
                        </td>
                        <td>
                            <a href="' . $website_url . '/admin?a=users&warn=' . $fetch['rr'] . '">' . $fetch['rr'] . '</a>
                        </td>
                        <td>
                            ' . $fetch['reportDate'] . '
                        </td>
                    </tr>';

                $x++;
            }

            return $str;
        }
    }
?>