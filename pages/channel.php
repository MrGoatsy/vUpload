<div class="row">
    <div class="col-md-12">
        <?php
            if(isset($_GET['p'])){
                $query = $handler->prepare('SELECT * FROM users WHERE username = :username');
                try{
                    $query->execute([
                        ':username' => $channelName[3]
                    ]);
                }catch(PDOException $e){
                    echo $error;
                }

                if($query->rowCount()){

        ?>
            <div class="row" style="margin-top: 5px;">
                <div class="col-md-12">
                    <table style="width: 100%;" class="table table-bordered border border-dark">
                        <tr>
                            <td class="threadtd" style="width: 64px;">
                                <?php echo $profile->getChannelDetails($channelName[3], ['avatar']); ?>
                            </td>
                            <td class="threadtd">
                                <span style="font-size: 20px;"><?php echo $channelName[3]; ?><?php echo (($user->loggedIn() && $user->getSessionUsername() == $channelName[3])? ' <a href="' . $website_url . '/editchannel">[Edit]</a>' : ''); ?></span><br />
                                <?php echo $profile->getChannelDetails($channelName[3], ['rank', 'name']); ?>
                            </td>
                        </tr>
                    </table><br />
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">About channel</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Channel information</a>
                                </li>
                            </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                        <table style="width: 100%;" class="table table-bordered border border-dark">
                                                <tr>
                                                    <td style="height: 150px; vertical-align: top;"><div style="margin-left: 5px;"><?php echo $profile->getChannelDetails($channelName[3], ['description']); ?></div></td>
                                                </tr>
                                            </table>
                                    </div>
                                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                            <table style="width: 25%;" class="table table-bordered border border-dark">
                                                <tr>
                                                    <td style="width: 10%;"><div class="profileSpan">Joined:</div></td>
                                                    <td style="width: 20%;"><div class="profileSpan"><?php echo substr($profile->getChannelDetails($channelName[3], ['joindate']), 0, 7); ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 10%;"><div class="profileSpan">Videos uploaded:</div></td>
                                                    <td style="width: 20%;"><div class="profileSpan"><?php echo $profile->getChannelDetails($channelName[3], ['videoCount']); ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 10%;"><div class="profileSpan">Website:</div></td>
                                                    <td style="width: 20%;"><div class="profileSpan"><?php echo $profile->getChannelDetails($channelName[3], ['website']); ?></div></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                        </div>
                        <div class="col-md-12">
                        <h3>Uploads</h3><hr />
                        <div class="row">
                            <?php
                                for($i = 0; $i <= $video->getVideo($channelName[3], 'count', [$i])-1; $i++){
                                    $x = $i+1;
                                    
                                    if($x%4 == 1){
                                        echo'<div class="row">';
                                    }
                            ?>
                                    <div class="col-md-3">
                                        <a href="<?php echo $website_url . '/watch?v=' . $video->getVideo($channelName[3], 'details', ['videoFileName', $i]); ?>" style="color: black;">
                                        <div class="card shadow-sm">
                                            <div style="background: url(<?php echo $website_url . '/videos/users/thumbnails/' . $video->getVideo($channelName[3], 'details', ['thumbnail', $i]); ?>); padding-top: 56.25%; background-size:100% 100%;"></div>
                                            <div class="card-body">
                                            <p class="card-text">
                                                <b><?php echo $video->getVideo($channelName[3], 'details', ['title', $i]); ?></b>
                                            </p>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                            <?php
                                if($x%4 == 0){
                                    echo'</div><br />';
                                }
                            }
                            ?>
                            <div style="margin-bottom: 15px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
                }
                else{
                    echo $pagedoesnotexist;
                }
            }
            else{
                echo $pagedoesnotexist;
            }
        ?>
    </div>
</div>
