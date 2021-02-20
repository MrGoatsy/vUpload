<?php
    if($user->loggedIn()){
?>
<h1>Upload</h1>
<hr />
<script type="text/javascript">
    $(function() {
        $("#uploadForm").submit(function() {
            var formData = new FormData($('#uploadForm')[0]);
            
            $.ajax({
                url: "ajax.php?q=uploadSubmit",
                type: 'POST',
                data: formData,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;

                            $("#progress-bar").width(Math.round(percentComplete * 100) + '%');
                            $("#progress-bar").attr('aria-valuenow', Math.round(percentComplete * 100)).css('width', Math.round(percentComplete * 100) + '%');
                            $("#progress-bar").text(Math.round(percentComplete * 100) + '%');

                            if(percentComplete == 1){
                                $("#result").html('Your video is now processing, this may take some time. To continue to watch videos, at the moment you have to open a new window.'); 
                                $("#result").addClass("alert alert-success");
                                $('#btnSubmit').attr('disabled', true);
                            }
                        }
                    }, false);
                    return xhr;
                },
                success: function(data) {
                    if (data.includes('!=[]_')) {
                            $("#result").html(data.substr(5)); 
                            $("#result").addClass("alert alert-success");
                            $('#btnSubmit').attr('disabled', true);
                        }
                        else {
                            $("#result").html(data); 
                            $("#result").addClass("alert alert-danger");
                            $('#btnSubmit').attr('disabled', false);
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
<div id="result"></div>
<form id="uploadForm" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" method="POST">
    <div class="input-group margin-bottom-sm" style="margin-bottom: 5px;">
        <span class="input-group-text"><i class="bi bi-camera-video-fill"></i></i>&nbsp;Select video</span>
        <input class="form-control" id="video" type="file" name="video" accept="video/*" placeholder="Video" required/>
    </div>
    <div class="progress" id="progress-div" style="margin-bottom: 5px;">
			<div class="progress-bar" id="progress-bar" role="progressbar" width="0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="input-group margin-bottom-sm" style="margin-bottom: 5px;">
        <span class="input-group-text"><i class="bi bi-camera-video-fill"></i></i>&nbsp;Select thumbnail</span>
        <input class="form-control" id="thumbnail" type="file" name="thumbnail" accept="image/*" placeholder="thumbnail" />
    </div>
    <div class="input-group margin-bottom-sm" style="margin-bottom: 5px;">
        <span class="input-group-text"><i class="bi bi-chat-right-text-fill"></i></i>&nbsp; Title</span>
        <input class="form-control" type="text" name="title" placeholder="Title" autocomplete="off" required/>
    </div>
    <label for="description">Description:</label>
        <textarea name="description" id="description"></textarea>
        <script>
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
        </script>
        <div class="input-group margin-bottom-sm" style="margin-top: 5px; margin-bottom: 5px;">
            <span class="input-group-text"><i class="bi bi-tags-fill"></i></i>&nbsp; Tags</span>
            <input class="form-control" id="tags" type="text" name="tags" placeholder="Separate by comma" maxlength="200" />
        </div>
        <div class="input-group margin-bottom-sm" style="margin-top: 5px; margin-bottom: 5px;">
            <input type="submit" id="btnSubmit" value="Submit" class="btn btn-success" style="width: 100%;" />
        </div>
</form>
<?php
    }
?>