<?php
    class search{
        private $handler,
                $video,
                $profile;

        public function __construct($handler, $video, $profile){
            $this->handler      = $handler;
            $this->video        = $video;
            $this->profile      = $profile;
            $this->pagination   = new pagination($this->handler);
        }

        public function sQuery($input, $optional = [25]){
            global $coreLang,
                   $searchLang,
                   $website_url;

            $query = $this->handler->prepare('SELECT COUNT(*) as rowCount FROM videos WHERE  v_hidden = 0');
            $query->execute();
            $total = $query->fetch(PDO::FETCH_ASSOC)['rowCount'];
            //echo $total . $searchLang['searchResults'];

            if($total > 0){
                $pagenumber = ((isset($_GET['pn']) && is_numeric($_GET['pn']) && $_GET['pn'] > 0)? (int)$_GET['pn'] : 1);
                $start      = (($pagenumber > 1)? ($pagenumber * $optional[0]) - $optional[0] : 0);
                $startCount = $pagenumber*$optional[0];
                
                $pages = ceil($total / $optional[0]);

                if($pagenumber == 1){
                    $pages = (($pages > 0)? $pages : 1);
                }
                elseif($pagenumber > $pages){
                    header('Location: search?q=' . $input . '&pn=1');
                }
    
                $querya = $this->handler->prepare('SELECT *, MATCH (v_title, v_desc, v_tags) AGAINST (:input) AS relevance FROM videos WHERE MATCH (v_title, v_desc, v_tags) AGAINST (:inputWhere) AND v_hidden = 0 ORDER BY relevance DESC LIMIT :startcount, :perpage');
                try{
                    $querya->execute([
                        ':input'        => '%' . $input . '%',
                        ':inputWhere'   => '%' . $input . '%',
                        ':startcount'   => $start,
                        ':perpage'      => $startCount,
                    ]);
                }
                catch(PDOException $e){
                    echo $e->getMessage();
                }
                echo $this->pagination->getPagination($pagenumber, $pages);
                echo'<table class="table">';
                $x = 0;

                while($fetch = $querya->fetch(PDO::FETCH_ASSOC)){
                    $avatar         = $this->video->getDetails('', $fetch['v_fileName'], ['avatar', 32]);
                    $username       = $this->video->getDetails('', $fetch['v_fileName'], 'channel');
                    $description    = str_replace(["\r", "\n", "<br>", "<br />"], '', str_limit_html($this->video->getDetails('', $fetch['v_fileName'], 'desc'), 1000));

                    echo <<<EOD
                        <tr class="row" style="margin-top: 5px;">
                            <td class="col-md-2 d-flex justify-content-center justify-content-md-start">
                                <a href="$website_url/watch?v=$fetch[v_fileName]"><img src="$website_url/videos/users/thumbnails/$fetch[v_thumbnail]" style="background-size: cover; width: 320px; height: 180px;"/></a>
                            </td>
                            <td class="col-md-10">
                                <div style="margin-left: 10px;">
                                    <a href="$website_url/watch?v=$fetch[v_fileName]"><h2>$fetch[v_title]</h2></a><br />
                                    <small><a href="$website_url/channel/$username">$avatar $username</a></small><br />
                                    <small><a href="$website_url/watch?v=$fetch[v_fileName]" style="color: black;">$description</a></small><br />
                                    <small class="flex-end">Tags: $fetch[v_tags]</small>
                                </div>
                            </td>
                        </tr>
                    EOD;
                    
                    $x++;
                }
                echo'</table></div>';
            }
            else{
                echo'</div>';
            }
        }

        //User search
        public function userSearch($input){
            global $coreLang,
                   $searchLang,
                   $website_url;


            $query = $this->handler->prepare('SELECT * FROM users WHERE username LIKE :input');
            try{
                $query->execute([
                    ':input'        => '%' . $input . '%',
                ]);
            }
            catch(PDOException $e){
                echo $e->getMessage();
            }
            
            echo $query->rowCount() . $searchLang['searchResults'] . '<br />';

            if($query->rowCount()){
                $x = 0;
                
                echo'<table class="table">';

                while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                    $avatar         = $this->profile->getChannelDetails($fetch['username'], ['avatar', 100]);
                    $username       = $fetch['username'];
                    $description    = str_replace(["\r", "\n", "<br>", "<br />"], '', str_limit_html($this->profile->getChannelDetails($fetch['username'], ['description']), 1000));

                    echo <<<EOD
                        <tr>
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

                echo'</table>';
            }
            else{
                //No results
            }
        }
    }
?>