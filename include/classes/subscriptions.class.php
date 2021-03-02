<?php
    class subscriptions{
        private $handler,
                $video,
                $user,
                $profile;

        public function __construct($handler, $video, $user, $profile){
            $this->handler  = $handler;
            $this->video    = $video;
            $this->user     = $user;
            $this->profile  = $profile;
        }

        public function getSubs(){
            global $website_url;

            $query  = $this->handler->prepare('SELECT * FROM videos WHERE u_id = (SELECT u_makerId FROM subscriptions WHERE u_followerId = :userId) AND v_hidden = 0');
            $query->execute([
                ':userId'   => $this->user->getUserId()
            ]);

            if($query->rowCount()){
                $x = 0;

                while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                    $queryV         = $this->handler->prepare('SELECT COUNT(*) FROM history WHERE v_id = :v_id');
                    $queryV->execute([
                        ':v_id' => $fetch['v_id']
                    ]);

                    $viewCount      = $queryV->fetchColumn();
                    $avatar         = $this->video->getDetails('', $fetch['v_fileName'], ['avatar', 32]);
                    $username       = $this->video->getDetails('', $fetch['v_fileName'], 'channel');
                    $description    = str_replace(["\r", "\n", "<br>", "<br />"], '', str_limit_html($this->video->getDetails('', $fetch['v_fileName'], 'desc'), 1000));

                    echo <<<EOD
                        <div class="row" style="margin-top: 5px;">
                            <div class="col-md-2 ">
                                <a href="$website_url/watch?v=$fetch[v_fileName]">
                                    <div class="ratio ratio-16x9">
                                        <img src="$website_url/videos/users/thumbnails/$fetch[v_fileName].jpg"/>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-10">
                                <div style="margin-left: 10px;">
                                    <a href="$website_url/watch?v=$fetch[v_fileName]"><h2>$fetch[v_title]</h2></a><br />
                                    <small><a href="$website_url/channel/$username">$avatar $username</a> &bull; <a href="$website_url/watch?v=$fetch[v_fileName]">$viewCount views &bull; $fetch[v_uploadtime]</a></small><br />
                                    <small><a href="$website_url/watch?v=$fetch[v_fileName]" style="color: black;">$description</a></small>
                                    <small class="flex-end">Tags: $fetch[v_tags]</small>
                                </div>
                            </div>
                        </div>
                        <hr />
                    EOD;
                    
                    $x++;
                }
            }
            else{
                //No results
            }
        }

        public function getCreators(){
            global $website_url;

            $query = $this->handler->prepare('SELECT * FROM users WHERE u_id = (SELECT u_makerId FROM subscriptions WHERE u_followerId = :userId)');
            $query->execute([
                ':userId' => $this->user->getUserId()
            ]);

            while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                echo <<<EOD
                    <a href="$website_url/channel/$fetch[username]">
                        <div class="w-100" onmouseover="this.style.background='#ffadad';" onmouseout="this.style.background='';">
                            {$this->profile->getChannelDetails($fetch['username'], ['avatar', 32])} $fetch[username]
                        </div>
                    </a>
                        <hr style="margin-top: 5px;" />
                EOD;
            }
        }
    }
?>