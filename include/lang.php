<?php

    /**
    * This file is meant for all customizable variables
    */

    /**
     * Core
     */

    $error              = "Something went wrong, please try again.";            //Database error
    $pagedoesnotexist   = "<h2>404 Error</h2><hr />This page does not exist";         //404 Error
    $invalidchartitle   = "You can only use letters and numbers in your title"; //Invalid characters
    $waitTime           = 60;                                                    //Wait time in seconds
    $char               = 25;                                                   //Character minimum
    $pleaseWait         = "Please wait $waitTime seconds before posting again.";    //Please wait
    $messageTooShort    = "Your message needs to be at least $char characters long"; //Message too short
    $userDoesNotExist   = "That user does not exist.";                          //User does not exist
    $noWarningsFound    = "This user has not received any warnings.";           //No warnings found
    $warningDoesNotExist = "This warning does not exist.";                      //Warning does not exist
    $videoReport        = "The video has been reported.";                       //Video was reported
    $videoReportFail    = "You can not report this video.";                     //Video was not reported
    
    /***
     * Login & Register
     */

    $redirect          = $website_url;                                         //Redirect location
    $incorrectpw       = "That is not the correct password";                   //Incorrect password
    $emptyerror        = "You left a field empty :/";                          //Field was empty
    $regsuccess        = "You have successfully registered an account. Please activate your account"; //Registration was a success
    $catcherror        = "Something went wrong, please try again.";            //Database error
    $passworderror     = "The passwords did not match.";                       //The passwords did not match
    $nomail            = "That is not a valid email address";                  //Not a valid email address
    $nocaptcha         = "You did not fill in the captcha";                    //No captcha
    $existingusername  = "That username has already been taken.";              //Username has been taken
    $existingemail     = "That email address has already been used";           //Email has been taken
    $invalidchar       = "You can only use letters and numbers in your name";  //Invalid characters
    $tooshort          = "Your password need to be at least 5 characters long";    //Password too short
    $accountactivated  = "Your account has been activated.";                   //Account has been activated
    $notactive         = "Your account has not yet been activated.";           //Account not activated
    $doesnotexist      = "That code does not exist.";                          //Invalid code
    $banned            = "You have been banned from this forum";               //Banned
    $emailDoesNotExist = "That emailaddress does not exist in our database.";   //Email does not exist
    $userdoesnotexist  = "That username does not exist";                        //User does not exist
    $redirectInTime    = "You will be redirected to the home page";             //Redirection message
    $usernameNotAllowed = "That channel name is not allowed.";                  //Forbidden username message
    $bannedUsername    = ["channel"];                                           //Forbidden usernames

    /**
     * Videos
     */

    $tooBig             = "The file exceeded the maximum size.";                //File too big
    $noFile             = "No file was uploaded.";                              //No file was uploaded
    $diskError          = "There was a disk error, please contact an administator."; //Disk error
    $novideo            = "The file you uploaded was not a video.";             //Uploaded file was not a video
    $videoSuccess       = "Your video was successfully uploaded.";              //Video was successfully uploaded
    $externalError      = "Something went wrong with the upload, please try again."; //Something went wrong trying to upload to an external server
    $tagLength          = "You can only have 100 characters maximum in tags.";  //Maximum tag characters
    $editSuccess        = "Your video sucessfully edited your video.";          //Video was sucessfully edited
    $videoDeleted       = "Your video has been successfully deleted.";          //Video is archived

    /**
     * Comments
     */
    
    $noComment          = "You can't leave an empty comment.";                   //Empty comment field

    /**
     * Channel
     */

    $profileUpdated    = "Your channel was successfully updated.";              //Profile updated
    $notAWebsite       = "That is not a valid website.";                        //Invalid website

    /**
     * Search
     */

    $searchResults          = " Result(s) was/were found.";                      //No results

    /**
     * Arrays for easy class access DO NOT EDIT
     */
    $coreLang = [
        "error"                 => $error,
        "pagedoesnotexist"      => $pagedoesnotexist,
        "waitTime"              => $waitTime,
        "char"                  => $char,
        "pleaseWait"            => $pleaseWait,
        "messageTooShort"       => $messageTooShort,
        "userDoesNotExist"      => $userDoesNotExist,
        "noWarningsFound"       => $noWarningsFound,
        "warningDoesNotExist"   => $warningDoesNotExist,
        "videoReport"           => $videoReport,
        "videoReportFail"       => $videoReportFail
    ];

    $registerLang = [
        "redirect"              => $website_url,
        "incorrectpw"           => $incorrectpw,
        "emptyerror"            => $emptyerror,
        "regsuccess"            => $regsuccess,
        "catcherror"            => $catcherror,
        "passworderror"         => $passworderror,
        "nomail"                => $nomail,
        "nocaptcha"             => $nocaptcha,
        "existingusername"      => $existingusername,
        "existingemail"         => $existingemail,
        "invalidchar"           => $invalidchar,
        "tooshort"              => $tooshort,
        "accountactivated"      => $accountactivated,
        "notactive"             => $notactive,
        "doesnotexist"          => $doesnotexist,
        "banned"                => $banned,
        "emailDoesNotExist"     => $emailDoesNotExist,
        "userdoesnotexist"      => $userdoesnotexist,
        "redirectInTime"        => $redirectInTime,
        "bannedUsername"        => $bannedUsername,
        "usernameNotAllowed"    => $usernameNotAllowed
    ];

    $videoMessage = [
        "tooBig"                => $tooBig,
        "noFile"                => $noFile,
        "diskError"             => $diskError,
        "noVideo"               => $novideo,
        "videoSuccess"          => $videoSuccess,
        "externalError"         => $externalError,
        "tagLength"             => $tagLength,
        "editSuccess"           => $editSuccess,
        "videoDeleted"          => $videoDeleted
    ];

    $commentMessage = [
        "noComment"             => $noComment
    ];

    $channelLang = [
        "profileUpdated"        => $profileUpdated,
        "notAWebsite"           => $notAWebsite
    ];

    $searchLang = [
        "searchResults"             => $searchResults
    ];
?>