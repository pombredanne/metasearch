<?php
    //////////////////////////////////////////////
    // Displays the search results for non-aggregated requests
    //////////////////////////////////////////////

    // Array of search engines
    // Initialise the engines array with the enabled engines
    if ($_SESSION['bingStatus'] === "true")
    {
        $engines[] = 'bing';
    }
    if ($_SESSION['blekkoStatus'] === "true")
    {
        $engines[] = 'blekko';
    }
    if ($_SESSION['entirewebStatus'] === "true")
    {
        $engines[] = 'entireweb';
    }

    // These will contain the decoded JSON data
    $blekko = NULL;
    // Two variables for bing as the maximum number of results returned is 50
    // so will be making two requests
    $bing_1 = NULL;
    $bing_2 = NULL;
    $entireweb = NULL;
    $google = array();
    $evaluation = array();
    $evaluationPrecisionat10;
    
    // Get the functions needed for messing about with the search results
    require_once('../includes/searchFunctions.php');
    
    $query = $_SESSION['queryParsed'];
    $queryAPISafe = createQueryAPI($query);
    
    getEngineResults("results", $queryAPISafe);
    
    // If evaluation is on run the evaluation functions
    if ( $_SESSION['evaluation'] === "true" )
    {
        initialiseGoogleEvaluation($queryAPISafe);
    }

    echo '<div id="tabs">';
    echo '<ul>';
    $i = 1;
    // Create the tab headings
    foreach ($engines as $engine)
    {
        echo '<li><a href="#tabs-'.$i.'">'.$engine.'</a></li>';
        $i++;
    }
    echo '</ul>';
    
    // Populate the tab contents
    for ( $i = 0, $j = 1; $i < 3; $i++, $j++ )
    {
        // If evaluation is on run the evaluation functions
        if ( $_SESSION['evaluation'] === "true" )
        {
            unset($GLOBALS['evaluation']);
            initialiseEvaluation($GLOBALS['engines'][$i]);
        }
        
        echo '<div id="tabs-'.$j.'" class="individualTabs">';
                displayNonAggregatedResults($i);
        echo '</div>';
    }
    echo '</div>';
?>
