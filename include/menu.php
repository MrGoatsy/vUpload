<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<div class="container-fluid">
		<a class="navbar-brand" href="#">vUpload</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

			<div class="collapse navbar-collapse" id="navbarsExample05">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="<?php echo $website_url; ?>">Home</a>
			</li>
			<?php
				if($user->loggedIn()){
			?>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="<?php echo $website_url . '/subscriptions'; ?>">Subscriptions</a>
			</li>
			<?php
					if($profile->getChannelDetails($user->getSessionUsername(), ['rank', 'int']) >= 950){
			?>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="<?php echo $website_url . '/admin'; ?>">Admin panel</a>
			</li>
			<?php
					}
				}
			?>
			</ul>
			<form style="width: 500px;" method="POST">
			<input class="form-control" name="q" type="text" value="<?php echo ((isset($_GET['p']) && $_GET['p'] == 'search' && isset($_GET['q']))? $_GET['q'] : ''); ?>" placeholder="Press enter to search" aria-label="Search">
			</form>
			<?php
				if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['q']) && !empty($_POST['q'])){
					header("Refresh:0");
					header('Location: ' . $website_url . '/search?q=' . $_POST['q']);
				}

				if($user->loggedIn()){
			?>
					<a href="<?php echo $website_url . '/upload'; ?>" style="margin-left: 5px;"><i class="bi bi-upload" style="color: white; font-size: 32px;"></i></a>
					<div class="btn-group dropstart float-end dropdown-menu-left" style="margin-left: 5px;">
						<a href="#" type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $user->getSessionUserAvatar([32]); ?></a>
						<ul class="dropdown-menu dropdown-menu-dark">
							<li><a class="dropdown-item" href="<?php echo $website_url . '/channel/' . $user->getSessionUsername(); ?>">Channel</a></li>
							<li><a class="dropdown-item" href="yourvideos">Your videos</a></li>
							<li><a class="dropdown-item" aria-current="page" href="<?php echo $website_url . '/logout'; ?>">Log out</a></li>
						</ul>
					</div>
			<?php }
				else{
			?>
			<div style="width: 5px;"></div>
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" aria-current="page" href="<?php echo $website_url . '/login'; ?>">Log in</a>
				</li>
			</ul>
			<?php
				}
			?>
		</div>
	</div>
</nav>