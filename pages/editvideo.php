<?php
	$fileName = $purifier->purify($_GET['v']);
	$views = $video->getDetails('', $fileName, 'views');

    if($user->loggedIn() && $user->getUserId() == $video->getDetails('', $_GET['v'], 'u_id') || $profile->getChannelDetails($user->getSessionUsername(), ['rank', 'int']) == 999){
        if(!$video->getDetails('', $fileName, 'videoFileName') || $video->getDetails('', $fileName, 'hidden') == 1){
            echo'<h2>This video does not exist</h2>';
        }
        else{
?>
<script type="text/javascript">
    $(function() {
        $("#uploadForm").submit(function() {
            var formData = new FormData($('#uploadForm')[0]);
            
            $.ajax({
                url: "ajax.php?q=editVideo&fileName=<?php echo $fileName; ?>",
                type: 'POST',
                data: formData,
                success: function(data) {
                    if (data.includes('!=[]_')) {
                            $("#result").html(data.substr(5)); 
                            $("#result").addClass("alert alert-success");
                        }
                        else {
                            $("#result").html(console.log(data)); 
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
        });

        var maxLength = 100;

        $('#tags').keyup(function() {
        var length = $(this).val().length;
        var length = maxLength-length;
        $('#chars').text(length);
        });
    });
</script>
<h1>Edit video</h1>
<hr />
<div id="result"></div>
<div class="row">
		<div class="col-md-8">
            <form id="uploadForm" enctype="multipart/form-data" method="POST">
                <div class="input-group margin-bottom-sm" style="margin-bottom: 5px;">
                    <span class="input-group-text"><i class="bi bi-camera-video-fill"></i>&nbsp;Select thumbnail</span>
                    <input class="form-control" id="thumbnail" type="file" name="thumbnail" accept="image/*" placeholder="thumbnail" />
                </div>
                <div class="input-group margin-bottom-sm" style="margin-bottom: 5px;">
                    <span class="input-group-text"><i class="bi bi-chat-right-text-fill"></i>&nbsp; Title</span>
                    <input class="form-control" type="text" name="title" value="<?php echo $video->getDetails('', $fileName, 'title'); ?>" placeholder="Title" required/>
                </div>
                <label for="description">Description:</label>
                <textarea name="description" id="description"></textarea>
                <script>
                    var noXSS = (function() {/*<?php echo str_replace('*/', '', $video->getDetails('', $fileName, 'desc')); ?>*/}).toString().match(/[^]*\/\*([^]*)\*\/\}$/)[1];
                
                    $('#description').summernote({
                        height: 250,
                        codeviewFilter: false,
                        codeviewIframeFilter: true,
                        // toolbar
                        toolbar: [
                            ['font', ['bold', 'italic', 'underline', 'clear']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['view', ['fullscreen']]
                        ],
                    }).on("summernote.enter", function(we, e) {
                        $(this).summernote('pasteHTML', '<br />&VeryThinSpace;');
                        e.preventDefault();
                    });
                    $("#description").summernote("code", noXSS);
                </script>
                <div class="input-group margin-bottom-sm" style="margin-top: 5px; margin-bottom: 5px;">
                    <span class="input-group-text"><i class="bi bi-tags-fill"></i></i>&nbsp; Tags</span>
                    <input class="form-control" id="tags" type="text" name="tags" value="<?php echo $video->getDetails('', $fileName, 'tags'); ?>" placeholder="Separate by comma" maxlength="200" />
                </div>
                <div class="input-group margin-bottom-sm" style="margin-top: 5px; margin-bottom: 5px;">
                    <input type="submit" id="btnSubmit" value="Submit" class="btn btn-success" style="width: 100%;" />
                </div>
            </form>
		</div>
		<div class="col-md-4">
        <h3>Current thumbnail:</h3>
        <div style="background: url(<?php echo $website_url . '/videos/users/thumbnails/' . $video->getDetails('', $fileName, 'videoFileName') . '.jpg'; ?>); padding-top: 56.25%; background-size:100% 100%;"></div>
        <hr />
        <h3>Current video:</h3>
        <video controls crossorigin playsinline poster="" id="player">
			<source src="<?php echo $website_url; ?>/videos/users/<?php echo $video->getDetails('', $fileName, 'videoURL'); ?>" type="video/<?php echo $video->getDetails('', $fileName, 'extension'); ?>" size="576">
		</video>
        <div style="margin-top: 5px;"></div>
		</div>
	</div>
<?php
        }
    }
    else{
        echo $pagedoesnotexist;
    }
?>