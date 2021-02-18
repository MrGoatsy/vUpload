<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_FILES['video']) && $_FILES['video']['error'] != 4){
            $video = $_FILES['video'];

            $file_name = $video['name'];
            $file_tmp = $video['tmp_name'];
            $file_size = $video['size'];
            $file_error = $video['error'];

            $mime = mime_content_type($video);

            if(strstr($video, "video/")){      
                if($file_error === 0){
                    if($file_size <= 1000000){
                        $file_name_new = $fetchUser['u_id'] . '.' . $file_ext;
                        $file_destination = 'videos/users/' . $file_name_new;
                   
                            if(move_uploaded_file($file_tmp, $file_destination)){
                                if($profile->uploadVideo($website, $u_desc, $fetchUser['u_id'], $file_name_new)){
                                    header('Location: ' . $website_url . '/channel/' . $fetchUser['username']);
                                }
                                else{
                                    echo $error;
                                }
                            }
                            else{
                                echo $couldNotMoveFile;
                            }
                        }
                        else{
                            echo $imageTooBig;
                        }
                    }
                }
                else{
                    echo $error;
                }
            }
    }
?>