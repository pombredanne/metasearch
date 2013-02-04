<?php
    // Query the search engine APIs
    function getEngineResults($service, $query)
    {
        switch($service)
        {            
            case 'results':
            {
                // Initialise the engines array with the enabled engines
                if ($_SESSION['bingStatus'] === "true")
                {
                    $engines = array('bing_1', 'bing_2');
                }
                if ($_SESSION['blekkoStatus'] === "true")
                {
                    $engines[] = 'blekko';
                }
                if ($_SESSION['entirewebStatus'] === "true")
                {
                    $engines[] = 'entireweb';
                }
                
                $engineCount = count($engines);
                $currentDate = date('dmY');
                
                // Check to see if this query has been cached within the last day
                if ( $_SESSION['caching'] === "true" )
                {
                    $cached = johnnyCache("results", $query);
                }
                else
                {
                    $cached = false;
                }
                
                // If it has been cached then read the local files
                if ( $cached === true )
                {
                    for($i = 0; $i < $engineCount; $i++)
                    {
                        // File path is relative to search.php
                        $file = '../cache/results/'.$currentDate.'-'.$query.'-'.$engines[$i].'.txt';
                        $GLOBALS[''.$engines[$i].''] = unserialize(file_get_contents($file));
                    }
                }
                else if ( $cached === false )
                {
                    // API keys & IP Address
                    $apikey_bing = "KwXxd2OjK75s8A8ZN0ih6TBXlIFbfwMgiVYwzrxDcTs=";
                    $apikey_blekko = "f4c8acf3";
                    $apikey_entireweb = "8f2c14ee14d54e0b64d34415fdcfaaf3";
                    $clientIP = $_SERVER['REMOTE_ADDR'];
                
                    // Inititalise the apis array with the enabled engine's apis
                    if ($_SESSION['bingStatus'] === "true")
                    {   
                        $apis = array('https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=%27'.$query.'%27&$format=json&$top=50&Market=%27en-us%27', 'https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=%27'.$query.'%27&$format=json&$top=50&$skip=50&Market=%27en-us%27');
                    }
                    if ($_SESSION['blekkoStatus'] === "true")
                    {
                        $apis[] = 'http://blekko.com/ws/?q='.$query.'+/json+/ps=100&auth='.$apikey_blekko.'';
                    }
                    if ($_SESSION['entirewebStatus'] === "true")
                    {
                        $apis[] = 'http://www.entireweb.com/xmlquery?pz='.$apikey_entireweb.'&ip='.$clientIP.'&q='.$query.'&format=json&n=100&sc=0&lang=en';
                    }
                
                    $api_count = count($apis);

                    // Initialise parallel downloading from search engines
                    $curl_arr = array();
                    $master = curl_multi_init();

                    for($i = 0; $i < $api_count; $i++)
                    {
                        $url = $apis[$i];
                        $curl_arr[$i] = curl_init($url);
                        curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
                        // If it's downloading from bing provide the apikey
                        if ($engines[$i] == "bing_1" || $engines[$i] == "bing_2")
                        {
                            curl_setopt($curl_arr[$i], CURLOPT_USERPWD, ":".$apikey_bing."");
                            curl_setopt($curl_arr[$i], CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                        }
                        curl_multi_add_handle($master, $curl_arr[$i]);
                    }

                    do {
                        curl_multi_exec($master,$running);
                    } while($running > 0);
        
                    for($i = 0; $i < $api_count; $i++)
                    {
                        // Store the results in the results array
                        $results[$i] = curl_multi_getcontent($curl_arr[$i]);
                        // Decoe these results into the global variables
                        $GLOBALS[''.$engines[$i].''] = json_decode($results[$i], true);
                    }
                
                    // Cache these results for future queries
                    for($i = 0; $i < $api_count; $i++)
                    {
                        // File path is relative to search.php
                        $file = '../cache/results/'.$currentDate.'-'.$query.'-'.$engines[$i].'.txt';
                        $content = serialize($GLOBALS[''.$engines[$i].'']);
                        file_put_contents($file, $content);
                    }
                }
            }
            break;
        }
    }
    
    function getFeedbackResults($service, $amount, &$bing, &$blekko, &$entireweb, $queryAPISafe)
    {
        switch($service)
        {
            case 'feedback':
            {
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
                $engineCount = count($engines);
                $currentDate = date('dmY');
                
                // Check to see if this query has been cached within the last day
                if ( $_SESSION['caching'] === "true" )
                {
                    $cached = johnnyCache("feedback", $queryAPISafe);
                }
                else
                {
                    $cached = false;
                }
                
                // If it has been cached then read the local files
                if ( $cached === true )
                {
                    for($i = 0; $i < $engineCount; $i++)
                    {
                        // File path is relative to process.php
                        $file = '../cache/feedback/'.$currentDate.'-'.$queryAPISafe.'-'.$engines[$i].'.txt';
                        $$engines[$i] = unserialize(file_get_contents($file));
                    }
                }
                // If it has not been cached then read the remote files
                else if ( $cached === false )
                {
                    // API keys & IP Address
                    $apikey_bing = "KwXxd2OjK75s8A8ZN0ih6TBXlIFbfwMgiVYwzrxDcTs=";
                    $apikey_blekko = "f4c8acf3";
                    $apikey_entireweb = "8f2c14ee14d54e0b64d34415fdcfaaf3";
                    $clientIP = $_SERVER['REMOTE_ADDR'];
                
                    // Start downloading results
                    $apis = array('https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query=%27'.$queryAPISafe.'%27&$format=json&$top='.$amount.'&Market=%27en-us%27', 'http://blekko.com/ws/?q='.$queryAPISafe.'+/json+/ps='.$amount.'&auth='.$apikey_blekko.'', 'http://www.entireweb.com/xmlquery?pz='.$apikey_entireweb.'&ip='.$clientIP.'&q='.$queryAPISafe.'&format=json&n='.$amount.'&sc=0');
                    $api_count = count($apis);

                    // Initialise parallel downloading from search engines
                    $curl_arr = array();
                    $master = curl_multi_init();

                    for($i = 0; $i < $api_count; $i++)
                    {
                        $url = $apis[$i];
                        $curl_arr[$i] = curl_init($url);
                        curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
                        if ($i == 0)
                        {
                            curl_setopt($curl_arr[$i], CURLOPT_USERPWD, ":".$apikey_bing."");
                            curl_setopt($curl_arr[$i], CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                        }
                        curl_multi_add_handle($master, $curl_arr[$i]);
                    }

                    do {
                        curl_multi_exec($master,$running);
                    } while($running > 0);
        
                    for($i = 0; $i < $api_count; $i++)
                    {
                        $results[] = curl_multi_getcontent($curl_arr[$i]);
                    }
        
                    $bing = json_decode($results[0], true);
                    $blekko = json_decode($results[1], true);
                    $entireweb = json_decode($results[2], true);
                
                    // Cache these results for future queries
                    for($i = 0; $i < $api_count; $i++)
                    {
                        // File path is relative to process.php
                        $file = '../cache/feedback/'.$currentDate.'-'.$queryAPISafe.'-'.$engines[$i].'.txt';
                        $content = serialize($$engines[$i]);
                        file_put_contents($file, $content);
                    }
                }
            }
            break;
        }
    }
    
    // Checks if results for $queryAPISafe are already cached. Returns true or false
    function johnnyCache($service, $query)
    {
        $currentDate = date('dmY');
        $cached = array();
        
        switch($service)
        {
            case 'results':
            {
                // Initialise the engines array with the enabled engines
                if ($_SESSION['bingStatus'] === "true")
                {
                    $engines = array('bing_1', 'bing_2');
                }
                if ($_SESSION['blekkoStatus'] === "true")
                {
                    $engines[] = 'blekko';
                }
                if ($_SESSION['entirewebStatus'] === "true")
                {
                    $engines[] = 'entireweb';
                }
                $engineCount = count($engines);
                for ( $i = 0; $i < $engineCount; $i++ )
                {
                    $filename = '../cache/results/'.$currentDate.'-'.$query.'-'.$engines[$i].'.txt';
                    if (file_exists($filename))
                    {
                        $cached[$i] = true;
                    }
                    else
                    {
                        $cached[$i] = false;
                    }
                }
                
                // Returns false if at least one cached result wasn't found
                if (in_array(false, $cached))
                {
                    return false;
                }
                else
                {
                    return true;
                }
            }
            break;
            
            case 'feedback':
            {
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
                $engineCount = count($engines);
                for ( $i = 0; $i < $engineCount; $i++ )
                {
                    $filename = '../cache/feedback/'.$currentDate.'-'.$query.'-'.$engines[$i].'.txt';
                    if (file_exists($filename))
                    {
                        $cached[$i] = true;
                    }
                    else
                    {
                        $cached[$i] = false;
                    }
                }
                
                // Returns false if at least one cached result wasn't found
                if (in_array(false, $cached))
                {
                    return false;
                }
                else
                {
                    return true;
                }
            }
            break;
            
            case 'synonyms':
            {
                $queryArraySize = sizeof($query);
                
                for ( $i = 0; $i < $queryArraySize; $i++ )
                {
                    $filename = '../cache/synonyms/'.$currentDate.'-'.$query[$i].'.txt';
                    if (file_exists($filename))
                    {
                        $cached[$i] = true;
                    }
                    else
                    {
                        $cached[$i] = false;
                    }
                }
                
                // Returns false if at least one cached result wasn't found
                if (in_array(false, $cached))
                {
                    return false;
                }
                else
                {
                    return true;
                }
            }
            break;
        }
    }
    
    // Returns synonym results from http://www.bighugelabs.com.
    function getSynonymResults($queryArray, $queryArraySize, &$synonymDecoded)
    {
        $currentDate = date('dmY');
        
        // Check to see if the list of synonyms have been cached within the last day
        if ( $_SESSION['caching'] === "true" )
        {
            $cached = johnnyCache("synonyms", $queryArray);
        }
        else
        {
            $cached = false;
        }
            
        // If it has been cached then read the local files
        if ( $cached === true )
        {
            for($i = 0; $i < $queryArraySize; $i++)
            {
                // File path is relative to process.php
                $file = '../cache/synonyms/'.$currentDate.'-'.$queryArray[$i].'.txt';
                $synonymDecoded[$i] = unserialize(file_get_contents($file));
            }
        }
        // If it hasn't been cached then download new files
        else if ( $cached === false )
        {
            $apikey = "c0ab7fa2377f5add0e537dfc0345033a";
        
            for ( $i = 0; $i < $queryArraySize; $i++ )
            {
                $nodes[$i] = 'http://words.bighugelabs.com/api/2/'.$apikey.'/'.$queryArray[$i].'/json';
            }

            $curl_arr = array();
            $master = curl_multi_init();

            for($i = 0; $i < $queryArraySize; $i++)
            {
                $url = $nodes[$i];
                $curl_arr[$i] = curl_init($url);
                curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($master, $curl_arr[$i]);
            }

            do {
                curl_multi_exec($master,$running);
            } while($running > 0);
        
            for($i = 0; $i < $queryArraySize; $i++)
            {
                $results = curl_multi_getcontent($curl_arr[$i]);
                $synonymDecoded[$i] = json_decode($results, true);
                // Cache these words for future queries
                $file = '../cache/synonyms/'.$currentDate.'-'.$queryArray[$i].'.txt';
                $content = serialize($synonymDecoded[$i]);
                file_put_contents($file, $content);
            }
        }
    }
    
    // Returns the total results given back by the search engine
    function getTotalResults($engine)
    {
        switch($engine)
        {
            case 'bing':
            {
                // Bing doesn't provide a figure in its JSON results
                $total = "100";
                return $total;
            }
            break;
            
            case 'entireweb':
            {
                $total = number_format($GLOBALS['entireweb']["last"]);
                return $total;
            }
            break;
            
            case 'blekko':
            {
                $total = number_format($GLOBALS['blekko']["total_num"]);
                return $total;
            }
            break;
        }
    }
    // Returns the URL of the requested page from the specified engine
    function getPageURL($engine, $i, $bingValue)
    {
        switch($engine)
        {
            case 'bing':
            {
                // Depending on the bingValue the URL returned will be from either the first or second bing API request
                if ( $bingValue == 1 )
                {
                    $url = $GLOBALS['bing_1'][d][results][$i]["Url"];
                    $url = rawurldecode($url); // bing encodes literal characters with % strings, revert this behaviour
                    $url = removeTrailingSlashes($url);
                    $url = standardiseURL($url);
                    return $url;
                }
                elseif ( $bingValue == 2 )
                {
                    $url = $GLOBALS['bing_2'][d][results][$i]["Url"];
                    $url = rawurldecode($url); // bing encodes literal characters with % strings, revert this behaviour
                    $url = removeTrailingSlashes($url);
                    $url = standardiseURL($url);
                    return $url;
                }
            }
            break;
            
            case 'entireweb':
            {
                $url = $GLOBALS['entireweb'][hits][$i]["url"];
                $url = removeTrailingSlashes($url);
                $url = standardiseURL($url);
                return $url;
            }
            break;
            
            case 'blekko':
            {
                $url = $GLOBALS['blekko'][RESULT][$i]["url"];
                $url = removeTrailingSlashes($url);
                $url = standardiseURL($url);
                return $url;
            }
            break;
            
            case 'results':
            {
                $keys = array_keys($GLOBALS['results']);
                $id = $keys[$i];
                $url = $GLOBALS['mergedArray'][$id]["url"];
                return $url;
            }
            break;
        }
    }
    // Returns the title of the requested page from the specified engine
    function getPageTitle($engine, $i, $bingValue, &$titles)
    {
        switch($engine)
        {
            case 'bing':
            {
                // Depending on the bingValue the title returned will be from either the first or second bing API request
                if ( $bingValue == 1 )
                {
                    $title = $GLOBALS['bing_1'][d][results][$i]["Title"];
                    return $title;
                }
                elseif ( $bingValue == 2 )
                {
                    $title = $GLOBALS['bing_2'][d][results][$i]["Title"];
                    return $title;
                }
            }
            break;
            
            case 'entireweb':
            {
                $title = $GLOBALS['entireweb'][hits][$i]["title"];
                return $title;
            }
            break;
            
            case 'blekko':
            {
                $title = $GLOBALS['blekko'][RESULT][$i]["url_title"];
                return $title;
            }
            break;
            
            case 'results':
            {
                $keys = array_keys($GLOBALS['results']);
                $id = $keys[$i];
                $title = $GLOBALS['mergedArray'][$id]["title"];
                return $title;
            }
            break;
            
            case 'feedback':
            {
                for ( $j = 0, $k = 0; $j < $i; $j++ )
                {
                    $titles[$k] = $GLOBALS['bing'][d][results][$j]["Title"];
                    $k++;
                    $titles[$k] = $GLOBALS['blekko'][RESULT][$j]["url_title"];
                    $k++;
                    $titles[$k] = $GLOBALS['entireweb'][hits][$j]["title"];
                    $k++;
                }
            }
            break;
        }
    }
    // Returns the display URL of the requested page from the specified engine
    function getPageDisplayURL($engine, $i, $bingValue)
    {
        switch($engine)
        {
            case 'bing':
            {
                // Depending on the bingValue the display URL returned will be from either the first or second bing API request
                if ( $bingValue == 1 )
                {
                    $display_url = $GLOBALS['bing_1'][d][results][$i]["DisplayUrl"];
                    $display_url = rawurldecode($display_url); // bing encodes literal characters with % strings, revert this behaviour
                    return $display_url;
                }
                elseif ( $bingValue == 2 )
                {
                    $display_url = $GLOBALS['bing_2'][d][results][$i]["DisplayUrl"];
                    $display_url = rawurldecode($display_url); // bing encodes literal characters with % strings, revert this behaviour
                    return $display_url;
                }
            }
            break;
            
            case 'entireweb':
            {
                $display_url = $GLOBALS['entireweb'][hits][$i]["displayurl"];
                return $display_url;
            }
            break;
            
            case 'blekko':
            {
                $display_url = $GLOBALS['blekko'][RESULT][$i]["display_url"];
                return $display_url;
            }
            break;
            
            case 'results':
            {
                $keys = array_keys($GLOBALS['results']);
                $id = $keys[$i];
                $display_url = $GLOBALS['mergedArray'][$id]["display_url"];
                return $display_url;
            }
            break;
        }
    }
    // Returns the snippet of the requested page from the specified engine
    function getPageSnippet($engine, $i, $bingValue, &$snippets)
    {
        switch($engine)
        {
            case 'bing':
            {
                // Depending on the bingValue the snippet returned will be from either the first or second bing API request
                if ( $bingValue == 1 )
                {
                    $snippet = $GLOBALS['bing_1'][d][results][$i]["Description"];
                    return $snippet;
                }
                elseif ( $bingValue == 2 )
                {
                    $snippet = $GLOBALS['bing_2'][d][results][$i]["Description"];
                    return $snippet;
                }
            }
            break;
            
            case 'entireweb':
            {
                $snippet = $GLOBALS['entireweb'][hits][$i]["snippet"];
                return $snippet;
            }
            break;
            
            case 'blekko':
            {
                $snippet = $GLOBALS['blekko'][RESULT][$i]["snippet"];
                return $snippet;
            }
            break;
            
            case 'results':
            {
                $keys = array_keys($GLOBALS['results']);
                $id = $keys[$i];
                $snippet = $GLOBALS['mergedArray'][$id]["snippet"];
                return $snippet;
            }
            break;
            
            case 'feedback':
            {
                for ( $j = 0, $k = 0; $j < $i; $j++ )
                {
                    $snippets[$k] = $GLOBALS['bing'][d][results][$j]["Description"];
                    $k++;
                    $snippets[$k] = $GLOBALS['blekko'][RESULT][$j]["snippet"];
                    $k++;
                    $snippets[$k] = $GLOBALS['entireweb'][hits][$j]["snippet"];
                    $k++;
                }
            }
            break;
        }
    }
    
    // Returns the number of documents available to the search engine, not just the ones displayed
    function getTotalDocuments($engine)
    {
        switch($engine)
        {
            case 'entireweb':
            {
                $totalDocuments = $GLOBALS['entireweb'][estimate];
                return $totalDocuments;
            }
            break;
            
            case 'blekko':
            {
                $totalDocuments = $GLOBALS['blekko']["total_num"];
                return $totalDocuments;
            }
            break;
        }
    }
    
    // Returns the engine(s) of the requested page for the specified page
    function getPageEngines($engine, $i)
    {
        switch($engine)
        {
            case 'results':
            {
                $keys = array_keys($GLOBALS['results']);
                $id = $keys[$i];
                $pageEngine = $GLOBALS['mergedArray'][$id]["engine"];
                
                return implode(",", $pageEngine);
            }
            break;
        }
    }
    // Returns the point(s) of the requested page for the specified page
    function getPagePoints($engine, $i)
    {
        switch($engine)
        {
            case 'results':
            {
                $keys = array_keys($GLOBALS['results']);
                $id = $keys[$i];
                $pagePoints = $GLOBALS['mergedArray'][$id]["points"];
                
                return implode(",", $pagePoints);
            }
            break;
        }
    }
    // Returns the position(s) of the requested page for the specified page
    function getPagePositions($engine, $i)
    {
        switch($engine)
        {
            case 'results':
            {
                $keys = array_keys($GLOBALS['results']);
                $id = $keys[$i];
                $pagePositions = $GLOBALS['mergedArray'][$id]["position"];
                
                return implode(",", $pagePositions);
            }
            break;
        }
    }
    // Returns the domain modifier value of the requested page for the specified page
    function getPageDomainModifier($engine, $i)
    {
        switch($engine)
        {
            case 'results':
            {
                $keys = array_keys($GLOBALS['results']);
                $id = $keys[$i];
                $pageDomainModifier = $GLOBALS['mergedArray'][$id]["modifiers"]["domain"];
                
                return $pageDomainModifier;
            }
            break;
        }
    }
    // Returns the term modifier value of the requested page for the specified page
    function getPageTermModifier($engine, $i)
    {
        switch($engine)
        {
            case 'results':
            {
                $keys = array_keys($GLOBALS['results']);
                $id = $keys[$i];
                $pageTermModifier = $GLOBALS['mergedArray'][$id]["modifiers"]["termboost"];
                
                return $pageTermModifier;
            }
            break;
        }
    }
    
    // Gets the intersection between the different arrays and updates the results array with combined points
    function getIntersection($sets, $array1, $array2, $array3)
    {
        switch($sets)
        {
            // For finding intersection of 3 sets
            case '3':
            {
                // Get the intersecting arrays
                $intersection = array_intersect($GLOBALS[''.$array1.'URLList'], $GLOBALS[''.$array2.'URLList'], $GLOBALS[''.$array3.'URLList']);
                // Iterate through the intersecting array updating points in the results array
                foreach ($intersection as $key_thisArray => $url)
                {
                    // Do this twice as there are 3 sets
                    for ( $i = 0; $i < 2; $i++ )
                    {
                        if ( $i == 0 )
                        {
                            $this_array = $array1;
                            $other_array = $array2;
                        }
                        else
                        {
                            $this_array = $array1;
                            $other_array = $array3;
                        }
                        
                        // For each url, find the key in the other array
                        $key_otherArray = array_search(''.$url.'', $GLOBALS[''.$other_array.'URLList']);
                        // Now that I have the key, get the points in the other array
                        $points_other_array = $GLOBALS['results'][''.$key_otherArray.''];
                        // Get the points in this array
                        $points_this_array = $GLOBALS['results'][''.$key_thisArray.''];
                        $combinedPoints = $points_this_array + $points_other_array;
                        // Update this array's entry in results
                        $GLOBALS['results'][''.$key_thisArray.''] = $combinedPoints;

                        // Grab the engine, position, and points from the other array
                        $other_array_engine = $GLOBALS[''.$other_array.'List']["".$key_otherArray.""]["engine"][0];
                        $other_array_position = $GLOBALS[''.$other_array.'List']["".$key_otherArray.""]["position"]["".$other_array.""];
                        $other_array_points = $GLOBALS[''.$other_array.'List']["".$key_otherArray.""]["points"]["".$other_array.""];
                        // Add the other array's engine, position, and points to this array's entry
                        $GLOBALS[''.$this_array.'List']["".$key_thisArray.""]["engine"][] = $other_array_engine;
                        $GLOBALS[''.$this_array.'List']["".$key_thisArray.""]["position"]["".$other_array.""] = $other_array_position;
                        $GLOBALS[''.$this_array.'List']["".$key_thisArray.""]["points"]["".$other_array.""] = $other_array_points;
                        
                        // Remove the other array's entry from results
                        unset($GLOBALS['results']["".$key_otherArray.""]);
                        // Also have to remove it from the URLList to stop it being included in intersection between 2 sets
                        unset($GLOBALS[''.$other_array.'URLList']["".$key_otherArray.""]);
                    }
                }
            }
            break;
            
            // For finding intersection of 2 sets
            case '2':
            {
                // Get the intersecting arrays
                $intersection = array_intersect($GLOBALS[''.$array1.'URLList'], $GLOBALS[''.$array2.'URLList']);
                $this_array = $array1;
                $other_array = $array2;
                // Iterate through the intersecting array updating points in the results array
                foreach ($intersection as $key_thisArray => $url)
                {
                    // For each url, find the key in the other array
                    $key_otherArray = array_search(''.$url.'', $GLOBALS[''.$other_array.'URLList']);
                    // Now that I have the key, get the points in the other array
                    $points_other_array = $GLOBALS['results'][''.$key_otherArray.''];
                    // Get the points in this array
                    $points_this_array = $GLOBALS['results'][''.$key_thisArray.''];
                    $combinedPoints = $points_this_array + $points_other_array;
                    // Update this array's entry in results
                    $GLOBALS['results'][''.$key_thisArray.''] = $combinedPoints;

                    // Grab the engine, position, and points from the other array
                    $other_array_engine = $GLOBALS[''.$other_array.'List']["".$key_otherArray.""]["engine"][0];
                    $other_array_position = $GLOBALS[''.$other_array.'List']["".$key_otherArray.""]["position"]["".$other_array.""];
                    $other_array_points = $GLOBALS[''.$other_array.'List']["".$key_otherArray.""]["points"]["".$other_array.""];
                    // Add the other array's engine, position, and points to this array's entry
                    $GLOBALS[''.$this_array.'List']["".$key_thisArray.""]["engine"][] = $other_array_engine;
                    $GLOBALS[''.$this_array.'List']["".$key_thisArray.""]["position"]["".$other_array.""] = $other_array_position;
                    $GLOBALS[''.$this_array.'List']["".$key_thisArray.""]["points"]["".$other_array.""] = $other_array_points;
                    
                    // Remove the other array's entry from results
                    unset($GLOBALS['results']["".$key_otherArray.""]);
                }
            }
            break;
        }
        
    }

    // Initialise the needed arrays for storing and manipulating the aggregated results
    function initialiseAggregatedLists($query)
    {
        $engineCount = count($GLOBALS['engines']);
        for ( $d = 0; $d < $engineCount; $d++ )
        {
            $engine = $GLOBALS['engines'][$d];
            $total = getTotalResults($engine);
        
            for ( $i = 0, $j = $GLOBALS['longest_list_length'], $k = 0, $bingValue = 1; $k < $total; $i++, $j--, $k++ )
            {
                // Only run this if the engine is bing
                if ( $engine == 'bing' )
                {
                    if ( $k == 50 )
                    {
                        $bingValue = 2;
                        $i = 0; // Reset $i as the new bing JSON file starts all over again
                    }
                }
        
                // Initialise the '$engine'List containing details of each result
                $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["title"] = getPageTitle($engine, $i, $bingValue);
                $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["url"] = getPageURL($engine, $i, $bingValue);
                $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["display_url"] = getPageDisplayURL($engine, $i, $bingValue);
                $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["snippet"] = getPageSnippet($engine, $i, $bingValue);
                $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["engine"][] = $engine;
                $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["position"][$engine] = $k;
                
                // If promoted results are on, calculate the modifiers
                if ( $_SESSION['promotedResults'] === "true" )
                {
                    $domainRelevancy = promoteDomain($GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["url"], $query);
                }
                else
                {
                    $domainRelevancy = 1;
                }
                
                // If query term boost is on, calculate the modifiers
                if ( $_SESSION['queryTerm'] === "true" )
                {
                    $termBoost = queryTermBoost($query, $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["title"], $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["snippet"]);
                }
                else
                {
                    $termBoost = 1;
                }
 
                // Calculate final points
                $final_points = ($_SESSION[''.$engine.''] * $j * $domainRelevancy * $termBoost); // $_SESSION[''.$engine.''] = engine weighting
                
                $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["points"][$engine] = $final_points;
                $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["modifiers"]["domain"] = $domainRelevancy;
                $GLOBALS[''.$engine.'List'][''.$engine.'-'.$k.'']["modifiers"]["termboost"] = $termBoost;
        
                // Initialise the '$engine'URLList containing the URL of each result and its corresponding ID in the '$engine'List
                $GLOBALS[''.$engine.'URLList'][''.$engine.'-'.$k.''] = getPageURL($engine, $i, $bingValue);
            
                // Initialise the results list containing each result's non-combined points and its corresponding ID in the '$engine'List
                $GLOBALS['results'][''.$engine.'-'.$k.''] = $final_points;
            }
        }
    }
    
    // Returns the full formatted list of the retrieved results from the specified engine
    function displayNonAggregatedResults($i)
    {
        $engine = $GLOBALS['engines'][$i];
        $total_results = getTotalResults($engine);
        
        if ( $_SESSION['evaluation'] === "true" )
        {
            $totalRelevantDocuments = getTotalRelevantDocuments();
            $queryPrecision = getQueryPrecision($totalRelevantDocuments);
            $queryRecall = getQueryRecall($totalRelevantDocuments);
            $queryAveragePrecision = getAveragePrecision($totalRelevantDocuments);
            $queryPrecisionat10 = getPrecisionAt10();
            $queryFMeasure = getFMeasure($totalRelevantDocuments);
        }
        
        echo '<div class="totalResultsContainer">';
            echo '<div class="totalResults">Displaying 1 - '.$total_results.' of '.$total_results.' results</div>';
            if ( $_SESSION['evaluation'] === "true" )
            {
                echo '<div class="evaluationContainer" style="font-size: 0.9em;">Total relevant documents: '.$totalRelevantDocuments.', precision: '.$queryPrecision.'%, recall: '.$queryRecall.'%, average precision: '.$queryAveragePrecision.'%, precision@10: '.$queryPrecisionat10.'%, F-measure: '.$queryFMeasure.'%</div>';
            }
        echo '</div>';
        
        for ($i = 0, $j = 1, $k = 0, $bingValue = 1; $k < $total_results; $i++, $j++, $k++)
        {
            // Only run this if the engine is bing
            if ( $engine == 'bing' )
            {
                if ( $k == 50 )
                {
                    $bingValue = 2;
                    $i = 0; // Reset $i as the new bing JSON file starts all over again
                }
            }
            echo '<div class="resultContainer">';
                echo '<div class="title">'.$j.'. <a href="'.getPageURL($engine, $i, $bingValue).'">'.getPageTitle($engine, $i, $bingValue).'</a></div>';
                echo '<div class="url">'.getPageDisplayURL($engine, $i, $bingValue).'</div>';
                echo '<div class="description">'.getPageSnippet($engine, $i, $bingValue).'</div>';
            echo '</div>';
        }
    }
    
    // Returns the full formatted list of the retrieved results from the specified engine
    function displayAggregatedResults()
    {
        $resultsCount = count($GLOBALS['results']);
        
        if ( $_SESSION['evaluation'] === "true" )
        {
            $totalRelevantDocuments = getTotalRelevantDocuments();
            $queryPrecision = getQueryPrecision($totalRelevantDocuments);
            $queryRecall = getQueryRecall($totalRelevantDocuments);
            $queryAveragePrecision = getAveragePrecision($totalRelevantDocuments);
            $queryPrecisionat10 = getPrecisionAt10();
            $queryFMeasure = getFMeasure($totalRelevantDocuments);
        }
        
        echo '<div class="totalResultsContainer">';
            echo '<div class="totalResults">Displaying 1 - '.$resultsCount.' of '.$resultsCount.' results</div>';
            if ( $_SESSION['evaluation'] === "true" )
            {
                echo '<div class="evaluationContainer">Total relevant documents: '.$totalRelevantDocuments.', precision: '.$queryPrecision.'%, recall: '.$queryRecall.'%, average precision: '.$queryAveragePrecision.'%, precision@10: '.$queryPrecisionat10.'%, F-measure: '.$queryFMeasure.'%</div>';
            }
        echo '</div>';
    
        $engine = "results";
        for ( $i = 0, $documentPosition = 1; $i < $resultsCount; $i++, $documentPosition++ )
        {
            $pageEngines = getPageEngines($engine, $i);
            $pagePoints = getPagePoints($engine, $i);
            $pagePositions = getPagePositions($engine, $i);
            $pageDomainModifier = getPageDomainModifier($engine, $i);
            $pageTermModifier = getPageTermModifier($engine, $i);
            $pageURL = getPageURL($engine, $i);
        
            $pageEnginesArray = explode(",", $pageEngines);
            $pagePointsArray = explode(",", $pagePoints);
            
            echo '<div class="resultContainer" id="result-'.$i.'">';
                echo '<div style="width: 900px; float: left">';
                    echo '<div class="title">'.$documentPosition.'. <a href="'.$pageURL.'">'.getPageTitle($engine, $i).'</a></div>';
                    echo '<div class="url">'.getPageDisplayURL($engine, $i).'</div>';
                    echo '<div class="description">'.getPageSnippet($engine, $i).'</div>';
                echo '</div>';
                echo '<div class="showStatistics" style="width: 50px; float: left; text-align: right; padding: 5px;">';
                    echo '<img src="images/office-chart-bar-stacked_48.png" />';
                echo '</div>';
            
                echo '<div class="statisticsContainer">';
                    echo '<div class="">Engines: '.$pageEngines.' '.count($pageEnginesArray).'</div>';
                    echo '<div class="">Points: '.$pagePoints.' '.array_sum($pagePointsArray).'</div>';
                    echo '<div class="">Positions: '.$pagePositions.'</div>';
                    echo '<div class="">Domain Modifier: '.$pageDomainModifier.'</div>';
                    echo '<div class="">Term boost: '.$pageTermModifier.'</div>';
                echo '</div>';
            echo '</div>';
        }
    }
    
    // Gets google's results for the query from the evaluation folder and stores them in an array
    function initialiseGoogleEvaluation($query)
    {
        for ( $i = 0, $k = 0; $i < 10; $i++ )
        {
            $filename = '../evaluation/'.$query.'_'.$i.'.json';
            $googleResults = file_get_contents($filename);
            $googleDecoded = json_decode($googleResults, true);
            
            for ( $j = 0; $j < 10; $j++ )
            {
                $url = $googleDecoded[items][$j]["link"];
                $url = removeTrailingSlashes($url);
                $url = standardiseURL($url);
                $GLOBALS['google'][$k] = $url;
                
                $k++;
            }
            fclose($filename);
        }
    }
    
    // Store the evaluation results
    function initialiseEvaluation($engine)
    {
        if ( $engine === "results" )
        {
            $resultsCount = count($GLOBALS['results']);
        }
        else
        {
            $resultsCount = getTotalResults($engine);
        }
        $relevantDocumentCount = 1;
        
        for ( $i = 0, $rank = 1, $k = 0; $i < $resultsCount; $i++, $rank++ )
        {
            if ( $engine == "bing" )
            {
                if ( $i < 50 )
                {
                    $bingValue = 1;
                }
                else
                {
                    $bingValue = 2;
                }
            }
            $metasearchURL = getPageURL($engine, $i, $bingValue);
            
            for ( $j = 0; $j < 100; $j++ )
            {
                if ( $GLOBALS['google'][$j] == $metasearchURL )
                {
                    $GLOBALS['evaluation'][$k]["url"] = $metasearchURL;
                    $GLOBALS['evaluation'][$k]["precision"] = number_format((($relevantDocumentCount / $rank) * 100), 0);
                    $GLOBALS['evaluation'][$k]["recall"] = number_format((($relevantDocumentCount / 100) * 100), 0);
                    $GLOBALS['evaluation'][$k]["metasearchPosition"] = $rank;
                    
                    $relevantDocumentCount++;
                    $k++;
                }
            }
            
            if ( $i === 9 )
            {
                $GLOBALS['evaluationPrecisionat10'] = ($relevantDocumentCount - 1) * 10;
            }
        }
    }
    
    function getTotalRelevantDocuments()
    {
        return count($GLOBALS['evaluation']);
    }
    function getQueryPrecision($total)
    {
        $total--;        
        return $GLOBALS['evaluation'][$total]["precision"];
    }
    function getQueryRecall($total)
    {
        $total--;        
        return $GLOBALS['evaluation'][$total]["recall"];
    }
    function getAveragePrecision($total)
    {
        $precision = 0;
        
        for ( $i = 0; $i < $total; $i++ )
        {
            $precision += $GLOBALS['evaluation'][$i]["precision"];
        }
        
        return ($precision / 100);
    }
    function getPrecisionAt10()
    {
        return $GLOBALS['evaluationPrecisionat10'];
    }
    function getFMeasure($total)
    {
        $precision = getQueryPrecision($total);
        $recall = getQueryRecall($total);
        
        return number_format(((2 * $precision * $recall) / ($precision + $recall)), 0);
    }
    
    // Generates a query in the correct format for the APIs
    function createQueryAPI($query)
    {
        $queryArray = explode(" ", $query);
        $queryAPISafe = implode("+", $queryArray);
        
        return $queryAPISafe;
    }
    
    // Removes any trailing slashes from the end of a URL
    function removeTrailingSlashes($url)
    {
        $pattern = '/\/$/';
        $replacement = '';
        return preg_replace($pattern, $replacement, $url);
    }
    
    // Will provide a level of standardisation for URLs.
    /* Sometimes the engine results will generate the same URL in a different
     * format causing identical results to not be aggregated together. This reduces the effect. */
    function standardiseURL($url)
    {
        // If the url begins with http://www. or https://www. then return it
        if ( preg_match('/^http(s)?:\/\/www\./', $url) == true)
        {
            return $url;
        }
        else if ( preg_match('/^http(s)?:\/\/www\./', $url) == false)
        {
            // If it begins with www. then return it
            if ( preg_match('/^www\./i', $url) == true)
            {
                return $url;
            }
            // If it doesn't begin with www.
            else if ( preg_match('/^www\./i', $url) == false)
            {
                // Check if it matches http://domain - without www, return it with http://www.domain
                if ( preg_match('/^http:\/\//', $url) == true)
                {
                    $pattern = '/^http:\/\//';
                    $replacement = 'http://www.';
                    return preg_replace($pattern, $replacement, $url);
                }
                // Check if it matches https://domain - without www, return it with https://www.domain
                else if ( preg_match('/^https:\/\//', $url) == true)
                {
                    $pattern = '/^https:\/\//';
                    $replacement = 'https://www.';
                    return preg_replace($pattern, $replacement, $url);
                }
            
                // Now check to see if there are two slashes in a row after a colon, signifies something like ftp:// leave this alone
                if ( preg_match('/:\/{2}/', $url) == true)
                {
                    return $url;
                }
                // If no condition satisfied add www. to the beginning of the URL
                else
                {
                    $replacement = 'www.';
                    return $replacement.$url;
                }
            }
            else
            {
                return $url;
            }
        }
        else
        {
            return $url;
        }
    }
    
    // Removes stopwords from $query
    function removeStopwords($query)
    {
        $stopwords = file('stopwords.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        // As this function is used to remove stopwords on results as well, I need to strip HTML away
        $query = strip_tags($query);
        $query = preg_replace('/[^a-z0-9]+/i', ' ', $query);
        $queryArray = explode(" ", strtolower($query));
        $queryArraySize = sizeof($queryArray);
        $stopwordsArraySize = sizeof($stopwords);

        // Before removing list of stopwords, delete any element with 2 characters or less
        for ( $i = 0; $i < $queryArraySize; $i++ )
        {
            if ( strlen($queryArray[$i]) <= 2 )
            {
                unset($queryArray[$i]);
            }
        }
        // Remove any empty elements
        $queryArray = array_filter($queryArray);
        // Re-index array
        $queryArray = array_values($queryArray);

        // Compare each query term to the list of stopwords
        for ( $i = 0; $i < $queryArraySize; $i++ )
        {
            for ( $j = 0; $j < $stopwordsArraySize; $j++ )
            {
                if ( $queryArray[$i] === $stopwords[$j] )
                {
                    // If the term is a stopword remove term from the query array
                    unset($queryArray[$i]);
                    break;
                }
            }
        }
    
        // Remove any empty elements
        $queryArray = array_filter($queryArray);
        // Re-index array
        $queryArray = array_values($queryArray);
    
        return $queryArray;
    }

    // Removes query terms from $result
    function removeQueryTerms($query, $result)
    {
        // As this function is used to remove query terms on results as well, I need to strip HTML away
        $query = strip_tags($query);
        $queryArray = explode(" ", strtolower($query));
        $queryArraySize = sizeof($queryArray);
        $resultArraySize = sizeof($result);
    
        // Compare each result term to each query term
        for ( $i = 0; $i < $resultArraySize; $i++ )
        {
            for ( $j = 0; $j < $queryArraySize; $j++ )
            {
                if ( $result[$i] === $queryArray[$j] )
                {
                    // If the term is in the query remove term from the result array
                    unset($result[$i]);
                    break;
                }
            }
        }
    
        return $result;
    }

    // Finds $amount synonyms for $queryArray, stores them in $querySynonyms
    function getSynonyms($queryArray, $amount, &$querySynonyms)
    {
        $synonymDecoded;
        // Apply some basic lemmatisation to the query
        $queryArray = lemmatiseQuery($queryArray);
        //var_dump($queryArray);
        $queryArraySize = sizeof($queryArray);
        // Download a list of synonyms
        getSynonymResults($queryArray, $queryArraySize, $synonymDecoded);
        for ( $j = 0; $j < $queryArraySize; $j++ )
        {
            for ( $i = 0; $i < $amount; $i++ )
            {
                // If the word isn't a noun try searching for a verb
                if ( $synonymDecoded[$j][noun][syn][$i] == NULL )
                {
                    $querySynonyms[$j][$i] = $synonymDecoded[$j][verb][syn][$i];
                }
                else
                {
                    $querySynonyms[$j][$i] = $synonymDecoded[$j][noun][syn][$i];
                }
            }
        }
    }
    
    // Provides a very crude level of lemmatisation to increase chances of finding synonyms
    function lemmatiseQuery($queryArray)
    {
        $queryArraySize = sizeof($queryArray);

        for ( $i = 0; $i < $queryArraySize; $i++ )
        {
            if ( preg_match('/ed$/', $queryArray[$i]) == true)
            {
                $pattern = '/ed$/';
                $replacement = 'e';
                $queryArray[$i] = preg_replace($pattern, $replacement, $queryArray[$i]);
            }
        }
        
        return $queryArray;
    }
    
    // Provides a modifier for results with a relevant domain
    function promoteDomain($url, $query)
    {
        /* Logic here is to give a boost to results that contain the entire query within the domain part of their URL.
         * If the result matches $query.com then this is given a larger boost as it's the most likely candidate.
         * Higher boost values are given to domains that have the query string nearer the beginning of the domain string,
         * and have a larger portion of the domain string occupied by the query string.
         * 
         * The formula is: ($occupancy * $relativePosition) + 1
         *    where $occupancy represents how much of the domain string is occupied by the query string (higher is better)
         *    $relativePosition represents how early the query string is found in the domain string (earlier is better) */
        
        // Strip away anything that isn't alphanumeric from query and concatentae
        $pattern = '/[^a-zA-Z0-9]/';
        $replacement = '';
        $query = preg_replace($pattern, $replacement, $query);
        
        // If http(s):// is present leave it as is
        if ( preg_match('/^http(s)?:\/\//', $url) == true)
        {
            $url = $url;
        }
        else
        {
            // If it's some other protocol of syntax x:// e.g. ftp:// leave it as is
            if ( preg_match('/:\/{2}/', $url) == true)
            {
                $url = $url;
            }
            // Else append http:// to the domain so parse_url works properly
            else
            {
                $pattern = 'http://';
                $url = $pattern.$url;
            }
        }
        
        // Return the domain part of the URL
        $url = parse_url($url, PHP_URL_HOST);
        
        // Remove www. from domain if present
        if ( preg_match('/^www\./i', $url) == true )
        {
            $pattern = '/^www\./';
            $replacement = '';
            $url = preg_replace($pattern, $replacement, $url);
        }

        $domainLength = strlen($url);
        $queryLength = strlen($query);
        // Find position of query within the domain
        $position_inURL = strpos($url, $query);
        
        // Find position of query.'$TLD'
        $TLDs = array(".com", ".org", ".net");
        $TLDcount = count($TLDs);
        
        for ( $i = 0; $i < $TLDcount; $i++ )
        {
            $position_totalURL = strpos($url, $query.''.$TLDs[$i].'');
            if ( $position_totalURL === 0 )
            {
                break;
            }
        }
        
        // If the query isn't present, return modifier of 1
        if ($position_inURL === false)
        {
            return 1;
        }
        else
        {
            // If the query is present in the URL but it's not query.com
            if ($position_totalURL === false || $position_totalURL >= 1)
            {
                $weighting = 8;
                $position_inURL++; // so don't potentially divide by 0
                $relativePosition = 1 / $position_inURL;
                $occupancy = ($queryLength / $domainLength) / $weighting;
                $final = ($occupancy * $relativePosition) + 1;
                return $final;
            }
            // If the URL matches query.com
            else if ($position_totalURL === 0)
            {
                $weighting = 1;
                $position_inURL++; // so don't potentially divide by 0
                $relativePosition = 1 / $position_inURL;
                $occupancy = ($queryLength / $domainLength) / $weighting;
                $final = ($occupancy * $relativePosition) + 1;
                return $final;
            }
        }
    }
    
    // Provides a modifier for the number of times query terms are mentioned in the result, based on the Okapi BM25 formula
    function queryTermBoost($query, $title, $snippet)
    {
        $queryArray = explode("+", strtolower($query));
        $queryArraySize = sizeof($queryArray);
        $termFrequency;
        $score;
        $k1 = 2;
        $b = 0.75;
        
        // Store the snippet in an array
        $snippet = strip_tags($snippet);
        $snippet = preg_replace('/[^a-z0-9]+/i', ' ', $snippet);
        $snippetArray = explode(" ", strtolower($snippet));
        
        // Store the title in an array
        $title = strip_tags($title);
        $title = preg_replace('/[^a-z0-9]+/i', ' ', $title);
        $titleArray = explode(" ", strtolower($title));
        
        // Merge the snippet and title
        $mergedTitleSnippet = array_merge($titleArray, $snippetArray);
        $documentLength = count($mergedTitleSnippet);
        $averageDocumentLength = 40;
        // Assuming a million here, as long as it's bigger than the $numberOfDocumentsContainingTerm
        $totalNumberofDocuments = 1000000;
        // Assume that returned results all contain the query term(s), setting it to 300 as this will be the maximum possibility with all 3 engines working
        $numberOfDocumentsContainingTerm = 300;
        $mergedTitleSnippet = array_count_values($mergedTitleSnippet);
        
        for ( $i = 0; $i < $queryArraySize; $i++ )
        {
            $termFrequency[$i] = $mergedTitleSnippet[$queryArray[$i]];
            
            $score += log(($totalNumberofDocuments - $numberOfDocumentsContainingTerm + 0.5) / ($numberOfDocumentsContainingTerm + 0.5)) * ($termFrequency[$i] * ($k1 + 1)) / ($termFrequency[$i] + $k1 * (1 - $b + $b * ($documentLength / $averageDocumentLength)));
        }
        
        if ( $score <= 0 )
        {
            return 1;
        }
        else
        {
            return $score;
        }
    }
?>
