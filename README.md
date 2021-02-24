### vUpload - Open Source video sharing
#### About
vUpload is an open source video sharing script, think of it as a lightweight Youtube.

#### Demo
https://heekdevelopment.com/test/

Login with test account:

    Name: test
    Password: test123

Uploading and Thumbnail editing has been disabled to prevent abuse.

#### Future ideas
    - Report comments
    - Upload to other servers via SFTP - Need to install PECL ssh2
    - Process videos in the background
    - Add messaging from user to user
    - Redo classes with smaller functions

#### Installation

This project users [Composer](http://https://github.com/composer/composer "Composer"), you need to install the following packages:
1. `composer require ezyang/htmlpurifier`
2. `composer require phpmailer/phpmailer`
3. `composer require php-ffmpeg/php-ffmpeg`

You also need to install FFmpeg on your server, you can download it from here:<br />
https://ffmpeg.org/

1. Upload the files to your server.
2. Edit `config.php` and change the 
   - While this is still in development rename `configSettings.php` to `config.php` and move it to the root folder.
3. Go to your MySQL or MariaDB manager and execute `vupload.sql`.
4. Optionally `.htaccess` located in the root folder and change these values(the default is 5gb):
   - `php_value post_max_size 5000M`
   - `php_value upload_max_filesize 5000M`
   - `LimitRequestBody 5368709120` 
5. In `php.ini` you need to add the following at the bottom of your extensions:
   - `extension=php_ffmpeg.dll`

6. You also need to remove the ';' from the following:
   - `;extension=gd`
7. Optionally you can edit `lang.php` located in the `include` folder if you want to change the language of most of the action/error messages.
8. It should be working now.

#### Current problems
1. None that are known

#### Notes

There are some extra MySQL tables, these are for potential future features.