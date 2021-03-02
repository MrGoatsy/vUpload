<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['manage']) && isset($_POST['warningSubmit']) && !empty($_POST['warning'])){
        $input          = $purifier->purify($_GET['manage']);
        $warningPost    = $purifier->purify($_POST['warning']);
        
        echo $warning->warn($input, $warningPost);
    }
    elseif($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['manage']) && isset($_POST['rank']) && !empty($_POST['rank'])){
        $input          = $purifier->purify($_GET['manage']);
        $rank           = $purifier->purify($_POST['rank']);
        
        echo $user->setRank($input, $rank);
    }
    else{
        echo'<h2>Warn user - <a href="' . $website_url . '/channel/' . $input . '">' . $input . '</a> - ' . (($profile->getChannelDetails($input, ['rank', 'name']))) . '</h2>';

        $query = $handler->prepare('SELECT * FROM ranks ORDER BY r_id DESC');
        $query->execute();

        echo'<br />
        <form method="post">
            <div class="input-group">
                <label class="input-group-text" for="inputGroupSelect">Options</label>
                <select name="rank" class="form-select" id="inputGroupSelect" required>
                    <option selected disabled>Choose...</option>';

            while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
                echo'<option value="' . $fetch['r_id'] . '">' . $fetch['rankName'] . '</option>';
            }

            echo'</select>
            </div>
            <div class="input-group" style="margin-bottom: 5px;">
                <input class="btn btn-primary" style="width: 100%;" type="submit" name="rankSubmit" value="Update rank" />
            </div>
        </form>';

        $query = $handler->prepare('SELECT * FROM warningnames');
        $query->execute();

        echo'<br />
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
                                <td style="border-bottom: 1px solid;">Reported videos</td>
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
                                <td style="border-bottom: 1px solid;">Remove?</td>
                            </tr>
                            ' . $warning->getWarnings($input) . '
                        </table>
                    </td>
                </tr>
            </table>
        </div>';
        }
?>