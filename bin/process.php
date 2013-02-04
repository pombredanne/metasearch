<?php

session_start();

// Get the functions needed for messing about with the search results
require_once('../includes/searchFunctions.php');

switch($_REQUEST['service'])
{
    case 'SaveSettings':
    {
        // Save the SESSION values from the user into variables
        $aggVar = "$_POST[aggregation]";
        $cachingVar = "$_POST[caching]";
        $queryTermVar = "$_POST[queryTerm]";
        $feedbackVar = "$_POST[feedback]";
        $evaluationVar = "$_POST[evaluation]";
        $promotedResultsVar = "$_POST[promotedResults]";
        $clusteringVar = "$_POST[clustering]";
        $blekkoVar = "$_POST[blekko]";
        $bingVar = "$_POST[bing]";
        $entirewebVar = "$_POST[entireweb]";
        $blekkoStateVar = "$_POST[blekkoState]";
        $bingStateVar = "$_POST[bingState]";
        $entirewebStateVar = "$_POST[entirewebState]";
        
        // Convert the weighting values into desired format
        $blekkoVar = ($blekkoVar / 100) + 1;
        $bingVar = ($bingVar / 100) + 1;
        $entirewebVar = ($entirewebVar / 100) + 1;
        
        // Update the SESSION values to reflect the user's changes
        $_SESSION['aggregation'] = $aggVar;
        $_SESSION['caching'] = $cachingVar;
        $_SESSION['queryTerm'] = $queryTermVar;
        $_SESSION['feedback'] = $feedbackVar;
        $_SESSION['evaluation'] = $evaluationVar;
        $_SESSION['clustering'] = $clusteringVar;
        $_SESSION['promotedResults'] = $promotedResultsVar;
        $_SESSION['blekko'] = $blekkoVar;
        $_SESSION['bing'] = $bingVar;
        $_SESSION['entireweb'] = $entirewebVar;
        $_SESSION['blekkoStatus'] = $blekkoStateVar;
        $_SESSION['bingStatus'] = $bingStateVar;
        $_SESSION['entirewebStatus'] = $entirewebStateVar;
    }
    break;
    
    
    
    case 'Search':
    {
        // Store query locally
        $query = "$_POST[query]";
        $query = urldecode($query); // The feedback list encodes literal characters with % strings, revert this behaviour
        
        // Check query for any boolean operators and convert them into compatible characters
        $userInput = array(" not ", " and ", "||", "! ", "!", "&&");
        $booleanOperators = array(" -", " + ", "or", "-", "-", "+");
        $parsedQuery = str_replace($userInput, $booleanOperators, strtolower($query));
        
        // queryParsed is what the program sends to the search engines
        $_SESSION['queryParsed'] = $parsedQuery;
        // queryDisplay is used to display the original query
        $_SESSION['queryDisplay'] = $query;
        
        // Start the Display Code
        require_once('../js/searchinput.js.php');
        require_once('../js/searchhighlight.js.php');
        require_once('../js/nonAggregatedResults.js');
        require_once('../js/statistics.js');
        require_once('../search.php');
    }
    break;
    
    
    
    case 'getFeedback':
    {
        // Store the original query
        $query = "$_POST[query]";
        // Check for boolean operators and replace them
        $userInput = array(" not ", " and ", "||", "! ", "!", "&&");
        $booleanOperators = array(" -", " + ", "or", "-", "-", "+");
        $parsedQuery = str_replace($userInput, $booleanOperators, strtolower($query));
        // Now replace spaces with +
        $queryAPISafe = createQueryAPI($parsedQuery);
        
        // Put the original query into an array for feedback display
        $queryAPIArray = explode(" ", strtolower($query));
        $queryAPIArraySize = sizeof($queryAPIArray);
        
        // Get synonyms for each word in the query
        $queryArray = removeStopwords($query);
        $queryArraySize = sizeof($queryArray);
        $synonymsAmount = 5;
        $querySynonyms = array();
        getSynonyms($queryArray, $synonymsAmount, $querySynonyms);
        
        // Get $resultsAmount search results for the original query and find relevant terms
        $resultsAmount = 5;
        $engineAmount = 3;
        $totalResults = $resultsAmount * $engineAmount;
        // These will store the search results
        $bing = NULL;
        $blekko = NULL;
        $entireweb = NULL;
        // These will store the titles and snippets from the search results
        $titles = NULL;
        $snippets = NULL;
        // These will store the merged results
        $mergedTitles = array();
        $mergedSnippets = array();
        $mergedTerms = array();
        
        // Get all the data and populate the above arrays
        getFeedbackResults("feedback", $resultsAmount, $bing, $blekko, $entireweb, $queryAPISafe);
        getPageTitle("feedback", $resultsAmount, 0, $titles); // 0 is for the bingValue
        getPageSnippet("feedback", $resultsAmount, 0, $snippets); // 0 is for the bingValue
        
        // Remove stopwords and query terms
        for ( $i = 0; $i < $totalResults; $i++ )
        {
            $titles[$i] = removeStopwords($titles[$i]);
            $snippets[$i] = removeStopwords($snippets[$i]);
            $titles[$i] = removeQueryTerms($query, $titles[$i]);
            $snippets[$i] = removeQueryTerms($query, $snippets[$i]);
        }
        
        // Reindex the arrays as gaps will exist from removed terms
        $titles = array_values($titles);
        $snippets = array_values($snippets);
        
        // Merge the titles/snippets multidimensional arrays into one dimensional arrays
        foreach($titles as $item)
        {
            $mergedTitles = array_merge($mergedTitles, $item);
        }
        foreach($snippets as $item)
        {
            $mergedSnippets = array_merge($mergedSnippets, $item);
        }
        
        // Merge the two arrays together
        $mergedTerms = array_merge($mergedSnippets, $mergedTitles);
                
        // Combine and count equivalent terms
        $mergedTerms = array_count_values($mergedTerms);
        // Sort from high to low
        arsort($mergedTerms);
        
        // As the keys contain the information I want to display
        $mergedTerms = (array_keys($mergedTerms));
        $mergedValid = isset($mergedTerms);
        
        if ( $mergedValid === false )
        {
            $mergedTerms = NULL;
        }

        // Start the Display Code
        require_once('../js/feedback.js');
        require_once('../includes/feedbackResults.php');
    }
    break;
}

?>
