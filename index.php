<?php
    session_start();
    
    require_once('includes/head.php');
    require_once('includes/sessionvalues.php');
    
    // Delete any stale cache files. Stale is anything that wasn't downloaded today
    $currentDate = date('dmY');
    $directories = array('cache/results/', 'cache/feedback/', 'cache/synonyms/');
    $directoriesCount = count($directories);
    
    for ( $i = 0; $i < $directoriesCount; $i++ )
    {
        $dir = $directories[$i];
        $files = scandir($dir);
        $filesArraySize = sizeof($files);
        for ( $j = 0; $j < $filesArraySize; $j++ )
        {
            if (preg_match("/".$currentDate."/", $files[$j]) == false)
            {
                unlink($dir.$files[$j]);
            }
        }
    }
?>
<!DOCTYPE HTML>
<html>
<head>
    <!-- Get title, styles, and JS libraries -->
    <?php echo getHeadFiles('index'); ?>
    
    <!-- Pull in javascript for this page -->
    <?php
        require_once('js/settingspanel.js.php');
    ?>
</head>
<body id="index">
<div id="contentWrapper">
    <h1>daveja Vu</h1>
    
    <div id="searchbarContainer">
        <?php require_once('includes/searchbar.php'); ?>
    </div>
    
    <div id="notificationContainer">
    </div>
    
    <div id="settingsPanelContainer">
        <?php require_once('includes/settingspanel.php'); ?>
    </div>
    
    <div id="feedbackContainer">
    </div>
    
    <div id="resultsContainer">
    </div>

</div>
    
</div>
</body>
</html>
