<?php

    require_once('filesFunctions.php');

    $filesManager = new FilesManager();
    
    echo $filesManager->accountByName();
    
?>
