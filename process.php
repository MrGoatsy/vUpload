<?php
    include'config.php';

    //echo $process->pVideo($_GET['t'], $_GET['e'], $_GET['v']);
    
    $handler->query('INSERT INTO processingqueue (v_id) VALUES (' . $argv[0] . ')');
?>