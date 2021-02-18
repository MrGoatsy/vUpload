<h1>Users</h1><hr />
<?php
    if(!isset($_GET['warn'])){
?>
        <form method="POST">
            <input class="form-control alert-info" name="uSearch" type="text" value="<?php echo ((isset($_GET['uSearch']))? $_GET['uSearch'] : ''); ?>" placeholder="Press enter to search" aria-label="Search">
        </form>
<?php
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['uSearch'])){
        header("Refresh:0");
        header('Location: ' . $website_url . '/admin?a=users&uSearch=' . $_POST['uSearch']);
    }
    elseif(isset($_GET['uSearch'])){
        $input = $purifier->purify($_GET['uSearch']);
        
        echo $search->userSearch($input);
    }
    elseif($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['warn']) && isset($_POST['warningSubmit']) && !empty($_POST['warning'])){
        $input          = $purifier->purify($_GET['warn']);
        $warningPost    = $purifier->purify($_POST['warning']);
        
        echo $warning->warn($input, $warningPost);
    }
    elseif(isset($_GET['warn'])){
        $input          = $purifier->purify($_GET['warn']);
        $query          = $handler->prepare('SELECT * FROM warningnames');
        $query->execute();
        $userCheck      = $handler->prepare('SELECT * FROM users WHERE username = :username');
        $userCheck->execute([
            ':username' => $input
        ]);
        
        if($userCheck->rowCount()){
            echo'<h2>Warn user - <a href="' . $website_url . '/channel/' . $input . '">' . $input . '</a> - ' . (($profile->getChannelDetails($input, ['rank', 'name']))) . '</h2>
                    <br />
                    <form method="post">
                        <div class="input-group">
                            <label class="input-group-text" for="inputGroupSelect">Options</label>
                            <select name="warning" class="form-select" id="inputGroupSelect" required>
                                <option selected disabled>Choose...</option>';

                        while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                            echo'<option value="' . $fetch['wn_id'] . '">' . $fetch['warningInfo'] . '</option>';
                        }

                        echo'</select>
                        </div>
                        <div class="input-group" style="margin-bottom: 5px;">
                            <input class="btn btn-danger" style="width: 100%;" type="submit" name="warningSubmit" value="Submit warning" />
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table">
                        <tr>
                            <td class="align-text-top w-50">
                                <table class="w-100">
                                    <tr style="border-right: 1px solid;">
                                        <td style="border-bottom: 1px solid;">Reports</td>
                                        <td style="border-bottom: 1px solid;">Reported by</td>
                                        <td style="border-bottom: 1px solid;">Date</td>
                                    </tr>
                                    ' . $warning->getReports($input) . '
                                </table>
                            </td>
                            <td class="align-text-top w-50">
                                <table class="w-100">
                                    <tr style="border-left: 1px solid;">
                                        <td style="border-bottom: 1px solid;">Current Warnings</td>
                                        <td style="border-bottom: 1px solid;">Warned by</td>
                                        <td style="border-bottom: 1px solid;">Date</td>
                                    </tr>
                                    ' . $warning->getWarnings($input) . '
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>';
        }
        else{
            header('Location: ' . $website_url . '/admin?a=users');
        }
    }
    else{
        $pagenumber = ((isset($_GET['pn']) && is_numeric($_GET['pn']) && $_GET['pn'] > 0)? (int)$_GET['pn'] : 1);
        
        echo $user->getAllUsers($pagenumber, [25]);
    }
?>