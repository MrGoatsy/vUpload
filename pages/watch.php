<?php
	$_SESSION['currentAmount'] = 0;

	//$fileName = $purifier->purify($_GET['v']);
	$views = $video->getDetails('', $fileName, 'views');

	if(!$video->getDetails('', $fileName, 'videoFileName') || $video->getDetails('', $fileName, 'hidden') == 1){
		echo'<h2>This video does not exist</h2>';
	}
	else{
		if(isset($_GET['report']) && $user->loggedIn()){
			$query = $handler->prepare('SELECT * FROM warningnames');
			$query->execute();
			$str = null;

			$str = '<h2>Report "' . $video->getDetails('', $fileName, 'title') . '"</h2><br />
				<form method="post">
                    <div class="input-group">
                        <label class="input-group-text" for="inputGroupSelect">Reason</label>
                        <select name="report" class="form-select" id="inputGroupSelect" required>
                            <option selected disabled>Choose...</option>';

        while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
            $str .= '<option value="' . $fetch['wn_id'] . '">' . $fetch['warningInfo'] . '</option>';
        }

        	$str .= '    </select>
                    </div>
                    <div class="input-group" style="margin-bottom: 5px;">
                        <input class="btn btn-danger" style="width: 100%;" type="submit" name="reportSubmit" value="Submit report" ' . ((isset($_POST['report']))? 'disabled' : '') . ' />
                    </div>
                </form>';

		echo $str;

				if(isset($_POST['report'])){
					echo $warning->reportVideo($fileName, $_POST['report'], $video->getDetails('', $fileName, 'channel'));
				}
		}
		else{
			if($user->loggedIn()){
?>
<script type="text/javascript">
	var current = <?php echo $user->subCheck($video->getDetails('', $fileName, 'u_id')); ?>;
	console.log(current);
	
	$(function () {
		$('.like').click(function (){ likeFunction(this);});
		$('.dislike').click(function (){ dislikeFunction(this);});
		$('#btnSubmit').click(function() { commentPost(this);});
		$('#subscribe').click(function() { subscribe(this);});

		$.ajax({
			type: 'POST',
			url: 'ajax.php?q=getComment&fileName=<?php echo $fileName; ?>&order=DESC',
			dataType: 'html',
			success: function(data) {
				$('#allUserComments').append(data);
			},
		});
	
		if(current == 1){
			$('#subscribe').removeClass('btn-danger').addClass('btn-light'); 
			$('#subscribe').text('Subscribed');
		}
		else{
			$('#subscribe').removeClass('btn-light').addClass('btn-danger'); 
			$('#subscribe').text('Subscribe');
		}
	});

	function likeFunction(caller){
		$.ajax({
			type: "POST",
			url: "ajax.php?q=vrated",
			data: 'videoURL=<?php echo $fileName; ?>&action=like',
			success: function (){//Remove this for performance mode
				$.ajax({
					type: "POST",
					url: "ajax.php?q=vrated&fileName=<?php echo $fileName; ?>&rating=1",
					dataType: "html",               
					success: function(response){                    
						$(".likes").html(response);
						$(".like").addClass("text-success");
					},
					error:function (){

					},
					async: true,
                }),
				$.ajax({
					type: "POST",
					url: "ajax.php?q=vrated&fileName=<?php echo $fileName; ?>&rating=-1",
					dataType: "html",               
					success: function(response){                    
						$(".dislikes").html(response);
						$(".dislike").removeClass("text-danger");
					},
					error:function (){

					},
					async: true,
				});
			},
			error: function(){
				
			}
		});
	}
	function dislikeFunction(caller){
		$.ajax({
			type: "POST",
			url: "ajax.php?q=vrated",
			data: 'videoURL=<?php echo $fileName; ?>&action=dislike',
			success: function (){//Remove this for performance mode
				$.ajax({
					type: "POST",
					url: "ajax.php?q=vrated&fileName=<?php echo $fileName; ?>&rating=-1",             
					dataType: "html",               
					success: function(response){                    
						$(".dislikes").html(response);
						$(".dislike").addClass("text-danger");
					},
					error:function (){

					},
				}),
				$.ajax({
					type: "POST",
					url: "ajax.php?q=vrated&fileName=<?php echo $fileName; ?>&rating=1",             
					dataType: "html",               
					success: function(response){                    
						$(".likes").html(response);
						$(".like").removeClass("text-success");;
					},
					error:function (){

					},
                });
			},
			error: function(){

			}
		});
	}
	function commentPost(caller){
		var formData = new FormData($('#commentForm')[0]);

		$.ajax({
			type: "POST",
			url: "ajax.php?q=commentPost&videoURL=<?php echo $fileName; ?>",
			data: formData,
			success: function(data) {
				if (data.includes('!=[]_')) {
					$("#result").removeClass("alert alert-danger");
					$("#result").html(data.substr(5));
					$("#result").addClass("alert alert-success");

					$.ajax({
						type: 'POST',
						url: 'ajax.php?q=getNewComment&fileName=<?php echo $fileName; ?>',
						dataType: 'html',
						success: function(data) {
							$('#comments').prepend(data);
						},
						async: true
					});
				}
				else {
					$("#result").removeClass("alert alert-success");
					$("#result").html(data); 
					$("#result").addClass("alert alert-danger");
				}
			},
			error: function(data){

			},
			cache: false,
			contentType: false,
			processData: false,
			resetForm: true 
		});
		return false;
	}
	function subscribe(caller){
		$.ajax({
			type: "POST",
			url: "ajax.php?q=subscribe",
			data: 'channelName=<?php echo $video->getDetails('', $fileName, 'channel'); ?>',
			success: function (data){
				if(current == 0){
					$('#subscribe').removeClass('btn-danger').addClass('btn-light'); 
					$('#subscribe').text('Subscribed');

					current = 1;
				}
				else{
					$('#subscribe').removeClass('btn-light').addClass('btn-danger'); 
					$('#subscribe').text('Subscribe');

					current = 0;
				}
			},
			error: function(){
				
			}
		});
	}
</script>
<?php
		}
?>
<script>
	$(function () {
		var win = $(window);
		var i = 0;

		win.scroll($.throttle( 100, function() {
			$('#loadingAllComments').show();
			if($('#loadingAllComments').is(':visible')){
				while(i < 2){
					$.ajax({
						type: 'POST',
						url: 'ajax.php?q=loadComments&fileName=<?php echo $fileName; ?>&order=DESC',
						dataType: 'html',
						success: function(data) {
							$('#comments').append(data);
							$('#loadingAllComments').hide();
						},
					});
					i++;
				}
				i=0;

				$('#loadingAllComments').hide();
			}
		}));
	});
</script>
<div class="row">
	<div class="col-md-2">
	</div>
	<div class="col-md-8">
	<div style="margin-top: 5px;"></div>
		<video poster="" id="player" data-poster="<?php echo $website_url . '/videos/users/thumbnails/' . $video->getDetails('', $fileName, 'thumbnail'); ?>" controls crossorigin playsinline >
			<source src="<?php echo $website_url; ?>/videos/users/<?php echo $video->getDetails('', $fileName, 'videoURL'); ?>" type="video/<?php echo $video->getDetails('', $fileName, 'extension'); ?>" size="576">
		</video>
		<div style="margin-top: 5px;"></div>
		<table style="width: 100%;" class="table table-bordered border border-dark">
			<tr>
				<td style='width: 90%; max-width: 1280px;'>
					<h1><?php echo $video->getDetails('', $fileName, 'title'); ?></h1>
				</td>
				<td align=center>
					<table>
						<tr>
							<td>
								<h2><i class="like bi bi-hand-thumbs-up-fill <?php echo (($video->getLord($fileName, 1))? 'text-success' : ''); ?>"></i></h2>
							</td>
							<td>
								<h2><i class="dislike bi bi-hand-thumbs-down-fill <?php echo (($video->getLord($fileName, -1))? 'text-danger"' : ''); ?>"></i></h2>
							</td>
						</tr>
						<tr>
							<td align=center class="likes">
								<?php echo $video->getLord($fileName, 1); ?>
							</td>
							<td align=center class="dislikes">
								<?php echo $video->getLord($fileName, -1); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<table style="width: 100%">
					<tr>
						<td style="width: 64px;">
							<a href="<?php echo $website_url . '/channel/' . $video->getDetails('', $fileName, 'channel'); ?>"><?php echo $video->getDetails('', $fileName, ['avatar', 64]); ?></a>
						</td>
						<td>
							<a href="<?php echo $website_url . '/channel/' . $video->getDetails('', $fileName, 'channel'); ?>"><?php echo $video->getDetails('', $fileName, 'channel'); ?></a><br />
							<small><?php echo $views . (($views == 1)? ' view' : ' views') . ' &bull; ' .  $video->getDetails('', $fileName, 'time'); ?></small>
						</td>
						<td>
							<?php echo (($user->getUserId() == $video->getDetails('', $fileName, 'u_id'))? '' : '<button class="btn btn-danger float-end" id="subscribe">Subscribe</button>'); ?>
						</td>
					</tr>
				</table><hr />
					<?php echo $video->getDetails('', $fileName, 'desc'); ?><hr />
					<small>Tags: <?php echo $video->getDetails('', $fileName, 'tagsLink'); ?></small>
					<small class="float-end"><a href="<?php echo $website_url . '/watch?v=' . $fileName . '&report' ?>">Report</a></small>
				</td>
			</tr>
		</table>
		<?php
			if($user->loggedIn()){
		?>
		<div id="result"></div>
		<form id="commentForm" enctype="multipart/form-data" method="POST">
			<textarea name="comment" id="comment" required></textarea>
			<script>
				$('#comment').summernote({
					placeholder: "Leave a comment...",
					height: 50,
					// toolbar
					toolbar: [
						['font', ['bold', 'italic', 'underline', 'clear']],
						['para', ['ul', 'ol', 'paragraph']],
					],
				});
			</script>
			<div class="input-group margin-bottom-sm" style="margin-top: 5px; margin-bottom: 5px;">
				<input type="button" id="btnSubmit" value="Submit" class="btn btn-success" style="width: 100%;" />
			</div>
			<hr />
			</form>
			<?php
				}
			?>
			<table style="width: 100%">
			<tr>
				<td></td>
			</tr>
			<tr>
				<td>
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="nav-link active" id="pills-all-comments-tab" data-bs-toggle="pill" href="#pills-all-comments" role="tab" aria-controls="pills-all-comments" aria-selected="true">All Comments</a>
						</li>
						<?php
							if($user->loggedIn()){
						?>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="pills-your-comments-tab" data-bs-toggle="pill" href="#pills-your-comments" role="tab" aria-controls="pills-your-comments" aria-selected="false">Your Comments</a>
						</li>
						<?php
							}
						?>
					</ul>
					<div class="tab-content" id="pills-tabContent">
						<div class="tab-pane fade show active" id="pills-all-comments" role="tabpanel" aria-labelledby="pills-all-comments-tab">
							<div id="newUserComment"></div>
							<div id="comments"></div>
								<div id="loadingAllComments" style="text-align: center;">
									<i class="bi bi-cloud-arrow-down-fill"></i>
								</div>
						</div>
						<div class="tab-pane fade" id="pills-your-comments" role="tabpanel" aria-labelledby="pills-your-comments-tab">
							<div id="allUserComments"></div>
							<div id="loadingAllUserComments" style="text-align: center;">
								<i class="bi bi-cloud-arrow-down-fill"></i>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="col-md-2">
	</div>
</div>
<?php
		echo $video->addViewer($fileName);
		}
	}
?>