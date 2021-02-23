<div class="row">
    <div class="col-md-12">
        <?php
            if($user->loggedIn()){
                $channelName = $user->getSessionUsername();
        ?>
                <h1>Edit profile</h1>
                You can edit your profile here.
                <form method="POST" enctype="multipart/form-data">
                    <div class="input-group margin-bottom-sm" style="margin-bottom: 5px;">
                        <span class="input-group-text"><i class="bi bi-image"></i>&nbsp;Avatar</span>
                        <input class="form-control" type="file" name="avatar" placeholder="Avatar" />
                    </div>
                    <div class="input-group margin-bottom-sm" style="margin-bottom: 5px;">
                        <span class="input-group-text"><i class="bi bi-cloud-plus-fill"></i>&nbsp; Website</span>
                        <input class="form-control" type="text" name="website" placeholder="Website" value="<?php echo $profile->getChannelDetails($channelName, ['website']); ?>" />
                    </div>
                    <label for="description">About your channel:</label>
                    <textarea name="description" id="description"></textarea>
                    <script>
                        var noXSS = (function() {/*<?php echo str_replace('*/', '', $profile->getChannelDetails($channelName, ['description'])); ?>*/}).toString().match(/[^]*\/\*([^]*)\*\/\}$/)[1];

                        $('#description').summernote({
                            height: 250,
                            // toolbar
                            toolbar: [
                                ['font', ['bold', 'italic', 'underline', 'clear']],
                                ['color', ['color']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['view', ['fullscreen']],
                            ]
                        }).on("summernote.enter", function(we, e) {
                            $(this).summernote('pasteHTML', '<br />&VeryThinSpace;');
                            e.preventDefault();
                        });
                        $("#description").summernote("code", noXSS);
                    </script>
                    <div class="input-group pull-right" style="margin-top: 5px; margin-bottom: 5px;">
                        <input class="btn btn-success" style="width: 100%;" type="submit" name="editProfile" value="Submit" />
                    </div>
                </form>
                <?php
                    if($_SERVER['REQUEST_METHOD'] == 'POST'){
                        echo $profile->editChannel($_FILES, $_POST['description'], $_POST['website']);
                    }
            }
         ?>
    </div>
</div>
