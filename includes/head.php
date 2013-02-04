<?php

/* Function for returning the scripts and stylesheets needed for each page */

$server_root = "/seng";

function getHeadFiles($page)
{
    if($page == 'index')
    {
        echo '
        <title>daveja Vu</title>
        
        <link type="text/css" href="'.$GLOBALS['server_root'].'/css/smoothness/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
        <link type="text/css" href="'.$GLOBALS['server_root'].'/css/jquery-qtip.min.css" rel="stylesheet" />
        <link type="text/css" href="'.$GLOBALS['server_root'].'/css/master.css" rel="stylesheet" />
        <link href="http://fonts.googleapis.com/css?family=Gloria+Hallelujah" rel="stylesheet" type="text/css">
        
		<script type="text/javascript" src="'.$GLOBALS['server_root'].'/js/libs/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="'.$GLOBALS['server_root'].'/js/libs/jquery-ui-1.8.21.custom.min.js"></script>
        <script type="text/javascript" src="'.$GLOBALS['server_root'].'/js/libs/jquery-qtip.min.js"></script>
        <script type="text/javascript" src="'.$GLOBALS['server_root'].'/js/libs/jquery-highlight-3.js"></script>
        ';
    }
    if($page == 'search')
    {
        echo '        
        <link type="text/css" href="'.$GLOBALS['server_root'].'/css/master.css" rel="stylesheet" />
        ';
    }
    if($page == 'feedback')
    {
        echo '        
        <link type="text/css" href="'.$GLOBALS['server_root'].'/css/master.css" rel="stylesheet" />
        ';
    }
}

?>
