<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<?php
    if($user->loggedIn()){
        if($profile->getChannelDetails($user->getSessionUsername(), ['rank', 'int']) >= 950){
?>
	<div class="row">
		<div class="col-md-1">
        <ul class="nav flex-column">
        <h1>Menu</h1><hr />
            <li class="nav-item">
                <a href="<?php echo $website_url . '/admin?a=main'; ?>" class="nav-link <?php echo ((isset($_GET['a']) || empty($_GET['a']) || $_GET['q'] == 'main')? 'active' : ''); ?>">Main</a>
            </li>
            <li class="nav-item">
                <a href="<?php echo $website_url . '/admin?a=users'; ?>" class="nav-link <?php echo ((isset($_GET['a']) && $_GET['a'] == 'users')? 'active' : ''); ?>">Users</a>
            </li>
            <li class="nav-item">
                <a href="<?php echo $website_url . '/admin?a=videos'; ?>" class="nav-link <?php echo ((isset($_GET['a']) && $_GET['a'] == 'videos')? 'active' : ''); ?>">Videos</a>
            </li>
        </ul>
		</div>
		<div class="col-md-11">
            <?php
                if(isset($_GET['a'])){
                    if(file_exists('pages/admin/' . $_GET['a'] . '.php')){
                        include'pages/admin/' . $_GET['a'] . '.php';
                    }
                    else{
                        echo $pagedoesnotexist;
                    }
                }
                else{
                    include'pages/admin/main.php';
                }
            ?>
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