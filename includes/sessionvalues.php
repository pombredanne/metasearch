<?php
    /* This file checks the value of any SESSION variable. If any of the variables are uninitialised
     * it will generate default values. This won't overwrite any saved values. */
    
    session_start();

    if ($_SESSION['aggregation'] == NULL)
    {
        $_SESSION['aggregation'] = "false";
    }
    if ($_SESSION['caching'] == NULL)
    {
        $_SESSION['caching'] = "true";
    }
    if ($_SESSION['queryTerm'] == NULL)
    {
        $_SESSION['queryTerm'] = "false";
    }
    if ($_SESSION['feedback'] == NULL)
    {
        $_SESSION['feedback'] = "false";
    }
    if ($_SESSION['clustering'] == NULL)
    {
        $_SESSION['clustering'] = "false";
    }
    if ($_SESSION['evaluation'] == NULL)
    {
        $_SESSION['evaluation'] = "false";
    }
    if ($_SESSION['promotedResults'] == NULL)
    {
        $_SESSION['promotedResults'] = "false";
    }
    
    if ($_SESSION['bing'] == NULL)
    {
        $_SESSION['bing'] = "1.7";
    }
    if ($_SESSION['blekko'] == NULL)
    {
        $_SESSION['blekko'] = "1.3";
    }
    if ($_SESSION['entireweb'] == NULL)
    {
        $_SESSION['entireweb'] = "1.1";
    }
    
    if ($_SESSION['bingStatus'] == NULL)
    {
        $_SESSION['bingStatus'] = "true";
    }
    if ($_SESSION['blekkoStatus'] == NULL)
    {
        $_SESSION['blekkoStatus'] = "true";
    }
    if ($_SESSION['entirewebStatus'] == NULL)
    {
        $_SESSION['entirewebStatus'] = "true";
    }

?>
