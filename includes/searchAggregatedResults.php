<?php
    //////////////////////////////////////////////
    // Displays the search results for aggregated requests
    //////////////////////////////////////////////

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
    // Array of lengths for each list
    $result_lengths;
    // Holds the length of the longest list
    $longest_list_length;
    
    // Array of results details i.e. title, url, snippet ...
    $bingList;
    $blekkoList;
    $entirewebList;
    // Array of results URL tied to a unique ID
    $bingURLList;
    $blekkoURLList;
    $entirewebURLList;
    // Array of accumulated points and unique IDs
    $results;
    // Array of merged '$engine'List arrays
    $mergedArrays;

    // These will contain the decoded JSON data
    $blekko = array();
    // Two variables for bing as the maximum number of results returned is 50
    // so will be making two requests
    $bing_1 = array();
    $bing_2 = array();
    $entireweb = array();
    $google = array();
    $evaluation = array();
    $evaluationPrecisionat10;

    // Get the functions needed for messing about with the search results
    require_once('../includes/searchFunctions.php');

    $query = $_SESSION['queryParsed'];
    $queryAPISafe = createQueryAPI($query);
    
    getEngineResults("results", $queryAPISafe);
    
    // Download engine results
    for ( $i = 0; $i < 3; $i++ )
    {
        // Find the length of the results
        $engine = $engines[$i];
        $GLOBALS['result_lengths'][$i] = getTotalResults($engine);
    }
    
    // Sort with the longest list as the first index
    arsort($GLOBALS['result_lengths']);
    // Get the first item in the sorted array
    $GLOBALS['longest_list_length'] = array_shift(array_values($GLOBALS['result_lengths']));
    
    // Initialise the needed arrays
    initialiseAggregatedLists($queryAPISafe);
    
    // Find matching URLs of each set and update the results array
    getIntersection(3, "bing", "blekko", "entireweb");
    getIntersection(2, "bing", "blekko");
    getIntersection(2, "bing", "entireweb");
    getIntersection(2, "blekko", "entireweb");
    
    // Sort the combined results array from high points to low
    arsort($GLOBALS['results']);
    
    // Merge the '$engine'List arrays so that it's easier to access the contained information
    // Check to see if they are not NULL
    $bingValid = isset($GLOBALS['bingList']);
    $blekkoValid = isset($GLOBALS['blekkoList']);
    $entirewebValid = isset($GLOBALS['entirewebList']);
    $valid = 3;
    
    // Reduce the valid count for each engine that didn't return results
    if ( $bingValid === false )
    {
        $valid--;
    }
    if ( $blekkoValid === false )
    {
        $valid--;
    }
    if ( $entirewebValid === false )
    {
        $valid--;
    }
    
    // Merge the arrays depending on how many there are
    switch($valid)
    {
        // Merge all 3
        case '3':
        {
            $GLOBALS['mergedArray'] = array_merge($GLOBALS['bingList'], $GLOBALS['blekkoList'], $GLOBALS['entirewebList']);
        }
        break;
        
        // Merge only the 2 that exist
        case '2':
        {
            if ( $bingValid === true )
            {
                $a = "bing";
                if ( $blekkoValid === true )
                {
                    $b = "blekko";
                }
                else
                {
                    $b = "entireweb";
                }
            }
            else
            {
                if ( $blekkoValid === true )
                {
                    $a = "blekko";
                    $b = "entireweb";
                }
            }
                
            $GLOBALS['mergedArray'] = array_merge($GLOBALS[''.$a.'List'], $GLOBALS[''.$b.'List']);
        }
        break;
        
        // Merge the only one that exists
        case '1':
        {
            if ( $bingValid === true )
            {
                $a = "bing";
            }
            else if ( $blekkoValid === true )
            {
                $a = "blekko";
            }
            else
            {
                $a = "entireweb";
            }
            $GLOBALS['mergedArray'] = array_merge($GLOBALS[''.$a.'List']);
        }
        break;
        
        // If no results error
        default:
        {
            echo 'error';
        }
        break;
    }
    
    // If evaluation is on run the evaluation functions
    if ( $_SESSION['evaluation'] === "true" )
    {
        initialiseGoogleEvaluation($queryAPISafe);
        initialiseEvaluation("results");
    }

    // Display the result array
    displayAggregatedResults();
?>
