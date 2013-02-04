<script>
/* Javascript needed for the settingspanel.
 * This is kept as a PHP file so that JavaScript can work with the SESSION variables */

$(function(){
    
    // Get the values of the SESSION variables
    var aggValue = "<?php echo $_SESSION['aggregation']; ?>";
    var cachingValue = "<?php echo $_SESSION['caching']; ?>";
    var queryTermValue = "<?php echo $_SESSION['queryTerm']; ?>";
    var feedbackValue = "<?php echo $_SESSION['feedback']; ?>";
    var promotedResultsValue = "<?php echo $_SESSION['promotedResults']; ?>";
    var clusteringValue = "<?php echo $_SESSION['clustering']; ?>";
    var evaluationValue = "<?php echo $_SESSION['evaluation']; ?>";
    var bingValue = "<?php echo ($_SESSION['bing'] - 1) * 100; ?>";
    var blekkoValue = "<?php echo ($_SESSION['blekko'] - 1) * 100; ?>";
    var entirewebValue = "<?php echo ($_SESSION['entireweb'] - 1) * 100; ?>";
    
    var bingState = "<?php echo $_SESSION['bingStatus']; ?>";
    var blekkoState = "<?php echo $_SESSION['blekkoStatus']; ?>";
    var entirewebState = "<?php echo $_SESSION['entirewebStatus']; ?>";
    
    /*** GENERAL SETTINGS PANEL ***/
    // Convert search submit button into jQuery button
    $( "#searchButton" ).button();

    // Convert settings button into jQuery button
    $( "#settingsButton" ).button(
    {
        icons: { primary: "ui-icon-wrench" }
    });
    
    // Convert engine buttons into buttonset
    $( "#engineContainer" ).buttonset();
    if (bingState === "true")
    {
        $('#bingCheckbox').attr('checked', true);
    }
    else
    {
        $('#bingCheckbox').attr('checked', false);
    }
    if (blekkoState === "true")
    {
        $('#blekkoCheckbox').attr('checked', true);
    }
    else
    {
        $('#blekkoCheckbox').attr('checked', false);
    }
    if (entirewebState === "true")
    {
        $('#entirewebCheckbox').attr('checked', true);
    }
    else
    {
        $('#entirewebCheckbox').attr('checked', false);
    }
    $( '#engineContainer' ).buttonset("refresh");
    
    
    
    $( "#bingCheckbox" ).click(function()
    {
        if (bingState === "true")
        {
            bingState = false;
        }
        else
        {
            bingState = true;
        }
    });
    $( "#blekkoCheckbox" ).click(function()
    {
        if (blekkoState === "true")
        {
            blekkoState = false;
        }
        else
        {
            blekkoState = true;
        }
    });
    $( "#entirewebCheckbox" ).click(function()
    {
        if (entirewebState === "true")
        {
            entirewebState = false;
        }
        else
        {
            entirewebState = true;
        }
    });
    
    // Hide the settings panel, feedback, notification, and results container by default
    $( "#settingsPanelContainer" ).hide(); 
    $( "#notificationContainer" ).hide();   
    $( "#feedbackContainer" ).hide();  
    $( "#resultsContainer" ).hide();  
    // When settings button is clicked open up settings panel
    $( "#settingsButton" ).click(function()
    {
        $( "#settingsPanelContainer" ).toggle( "blind", 500 );
        return false;
    });
    /********************************************/
    
    
    
    /*** AGGREGATION SETTINGS ***/
    // Declare the aggregation button switches as a buttonset
    $( "#aggregationSwitchButtonset" ).buttonset();
    // Check to see if aggregation is on before deciding to show or hide the weighting container
    if (aggValue === "true")
    {
        // Aggregation is On so show the weights container
        $( "#aggregationEngineWeightsContainer" ).show();
        $( '[name="aggregationSwitch"][value="true"]' ).prop("checked", true);
        $( '#aggregationSwitchButtonset' ).buttonset("refresh");
        
        // Show the aggregation options
        $( "#defaultOff" ).show();
        $("#miscSettingsContainer #defaultOn #cachingContainer .labelText").css("width", "110");
        $("#miscSettingsContainer #defaultOn #evaluationContainer .labelText").css("width", "110");
        $("#miscSettingsContainer #defaultOn #feedbackoptionsContainer .labelText").css("width", "110");
    }
    else if (aggValue === "false")
    {
        // Aggregation is Off so hide the weights container
        $( "#aggregationEngineWeightsContainer" ).hide();
        $( '[name="aggregationSwitch"][value="false"]' ).prop("checked", true);
        $( '#aggregationSwitchButtonset' ).buttonset("refresh");
        
        // Hide the aggregation options
        $( "#defaultOff" ).hide();
        $("#miscSettingsContainer #defaultOn #cachingContainer .labelText").css("width", "60");
        $("#miscSettingsContainer #defaultOn #evaluationContainer .labelText").css("width", "60");
        $("#miscSettingsContainer #defaultOn #feedbackoptionsContainer .labelText").css("width", "60");
    };
    // When aggregation is switched on by the user, show the weighting container
    $( "#aggregationSwitch_On" ).click(function()
    {
        $( "#aggregationEngineWeightsContainer" ).show( "fold", 500 );
        $( "#defaultOff" ).delay(550).show("drop", 500);
        $("#miscSettingsContainer #defaultOn #cachingContainer .labelText").css("width", "110");
        $("#miscSettingsContainer #defaultOn #evaluationContainer .labelText").css("width", "110");
        $("#miscSettingsContainer #defaultOn #feedbackoptionsContainer .labelText").css("width", "110");
        aggValue = true;
        return false;
    });
    // When aggregation is switched off by the user, hide the weighting container
    $( "#aggregationSwitch_Off" ).click(function()
    {
        $( "#aggregationEngineWeightsContainer" ).hide( "fold", 500 );
        $( "#defaultOff" ).hide();
        $("#miscSettingsContainer #defaultOn #cachingContainer .labelText").css("width", "60");
        $("#miscSettingsContainer #defaultOn #evaluationContainer .labelText").css("width", "60");
        $("#miscSettingsContainer #defaultOn #feedbackoptionsContainer .labelText").css("width", "60");
        aggValue = false;
        return false;
    });
    
    // Convert slider reset button into jQuery button
    $( "#sliderResetButton" ).button(
        {
            // Give it an icon
            icons: { primary: "ui-icon-arrowreturnthick-1-w" }
        }
    );
    // Action for when slider reset button is clicked
    $( "#sliderResetButton" ).click(function()
    {
        // Define the default weight
        //var defaultWeightValue = 10;
        
        // Set each of the sliders to the default weight
        $( "#slider-blekko" ).slider( "option", "value", 30 );
        $( "#slider-bing" ).slider( "option", "value", 70 );
        $( "#slider-entireweb" ).slider( "option", "value", 10 );
        
        // Update the text field showing the sliders' values
        $( "#blekkoWeight" ).val( $( "#slider-blekko" ).slider( "value" ) );
        $( "#bingWeight" ).val( $( "#slider-bing" ).slider( "value" ) );
        $( "#entirewebWeight" ).val( $( "#slider-entireweb" ).slider( "value" ) );
        
        // Prevents the page from reloading when the button is clicked
        return false;
    });
    // Slider settings
    $( "#slider-bing" ).slider({
        orientation: "horizontal",
        range: "min",
        min: 0,
        max: 100,
        value: bingValue,
        slide: function( event, ui ) {
            $( "#bingWeight" ).val( ui.value );
        }
    });
    $( "#bingWeight" ).val( $( "#slider-bing" ).slider( "value" ) );
        
    $( "#slider-blekko" ).slider({
        orientation: "horizontal",
        range: "min",
        min: 0,
        max: 100,
        value: blekkoValue,
        slide: function( event, ui ) {
            $( "#blekkoWeight" ).val( ui.value );
        }
    });
    $( "#blekkoWeight" ).val( $( "#slider-blekko" ).slider( "value" ) );
        
    $( "#slider-entireweb" ).slider({
        orientation: "horizontal",
        range: "min",
        min: 0,
        max: 100,
        value: entirewebValue,
        slide: function( event, ui ) {
            $( "#entirewebWeight" ).val( ui.value );
        }
    });
    $( "#entirewebWeight" ).val( $( "#slider-entireweb" ).slider( "value" ) );
    /********************************************/
    
    
    
    /*** CACHING SETTINGS ***/
    // Declare the caching switches as a buttonset
    $( "#cachingButtonset" ).buttonset();
    
    // If caching is on, highlight the On button
    if (cachingValue === "true")
    {
        $( '[name="caching"][value="true"]' ).prop("checked", true);
        $( '#cachingButtonset' ).buttonset("refresh");
    }
    // If caching is off, highlight the Off button
    else if (cachingValue === "false")
    {
        $( '[name="caching"][value="false"]' ).prop("checked", true);
        $( '#cachingButtonset' ).buttonset("refresh");
    };
    
    // Controls for when the user clicks one of the buttons
    $( "#caching_On" ).click(function() {
        cachingValue = true;
        $( '[name="caching"][value="true"]' ).prop("checked", true);
        $( '#cachingButtonset' ).buttonset("refresh");
        return false;
    });
    $( "#caching_Off" ).click(function() {
        cachingValue = false;
        $( '[name="caching"][value="false"]' ).prop("checked", true);
        $( '#cachingButtonset' ).buttonset("refresh");
        return false;
    });
    /*** CACHING QTIP SETTINGS ***/
    // Initialise the qTip
    $("#cachingHelpContainer").qtip(
    {
        content:
        {
            title:
            {
                text: 'Caching'
            },
            text: 'Saves search engine responses of queries for 24 hours enabling faster retrieval times for frequent queries.<br /><br /><strong>Enabled by default</strong>.'
        },
        position:
        {
            my: 'left center',
            at: 'right center',
            target: $("#cachingHelp")
        },
        style:
        {
            classes: 'ui-tooltip-blue ui-tooltip-shadow'
        },
        hide:
        {
            delay: 300
        }
    });
    /********************************************/
    
    
    
    /*** QUERY TERM BOOSTING SETTINGS ***/
    // Declare the query term switches as a buttonset
    $( "#queryTermButtonset" ).buttonset();
    
    // If query term is on, highlight the On button
    if (queryTermValue === "true")
    {
        $( '[name="queryTerm"][value="true"]' ).prop("checked", true);
        $( '#queryTermButtonset' ).buttonset("refresh");
    }
    // If query term is off, highlight the Off button
    else if (queryTermValue === "false")
    {
        $( '[name="queryTerm"][value="false"]' ).prop("checked", true);
        $( '#queryTermButtonset' ).buttonset("refresh");
    };
    
    // Controls for when the user clicks one of the buttons
    $( "#queryTerm_On" ).click(function() {
        queryTermValue = true;
        $( '[name="queryTerm"][value="true"]' ).prop("checked", true);
        $( '#queryTermButtonset' ).buttonset("refresh");
        return false;
    });
    $( "#queryTerm_Off" ).click(function() {
        queryTermValue = false;
        $( '[name="queryTerm"][value="false"]' ).prop("checked", true);
        $( '#queryTermButtonset' ).buttonset("refresh");
        return false;
    });
    /*** QUERY TERM QTIP SETTINGS ***/
    // Initialise the qTip
    $("#queryTermHelpContainer").qtip(
    {
        content:
        {
            title:
            {
                text: 'Query Term Boost'
            },
            text: 'Improves the ranking of pages that have a higher frequency of query terms.<br /><br /><strong>Disabled by default</strong>.'
        },
        position:
        {
            my: 'left center',
            at: 'right center',
            target: $("#queryTermHelp")
        },
        style:
        {
            classes: 'ui-tooltip-blue ui-tooltip-shadow'
        },
        hide:
        {
            delay: 300
        }
    });
    /********************************************/
    
    
    
    /*** FEEDBACK SETTINGS ***/
    // Declare the feedback switches as a buttonset
    $( "#feedbackoptionsButtonset" ).buttonset();
    
    // If feedback is on, highlight the On button
    if (feedbackValue === "true")
    {
        $( '[name="feedbackoptions"][value="true"]' ).prop("checked", true);
        $( '#feedbackoptionsButtonset' ).buttonset("refresh");
        $("body").on("click", "#searchButton").find("#searchButton").attr('value','Get Feedback');
        $("#notificationContainer").html("Feedback is on");
        $( "#notificationContainer" ).show( "blind", 500 );
    }
    // If feedback is off, highlight the Off button
    else if (feedbackValue === "false")
    {
        $( '[name="feedbackoptions"][value="false"]' ).prop("checked", true);
        $( '#feedbackoptionsButtonset' ).buttonset("refresh");
        $("body").on("click", "#searchButton").find("#searchButton").attr('value','Search');
    }
    
    // Controls for when the user clicks one of the buttons
    $("#feedbackoptions_On").click(function() {
        feedbackValue = "true";
        $( '[name="feedbackoptions"][value="true"]' ).prop("checked", true);
        $( '#feedbackoptionsButtonset' ).buttonset("refresh");
        return false;
    });
    
    $("#feedbackoptions_Off").click(function() {
        feedbackValue = "false";
        $( '[name="feedbackoptions"][value="false"]' ).prop("checked", true);
        $( '#feedbackoptionsButtonset' ).buttonset("refresh");
        return false;
    });
    /*** FEEDBACK QTIP SETTINGS ***/
    // Initialise the qTip
    $("#feedbackoptionsHelpContainer").qtip(
    {
        content:
        {
            title:
            {
                text: 'Feedback'
            },
            text: 'Provides suggestions to increase relevant results by scanning the top three documents for expanded terms & expanding the original search query.<br /><br /><strong>Disabled by default</strong>.'
        },
        position:
        {
            my: 'left center',
            at: 'right center',
            target: $("#feedbackoptionsHelp")
        },
        style:
        {
            classes: 'ui-tooltip-blue ui-tooltip-shadow'
        },
        hide:
        {
            delay: 300
        }
    });
    /********************************************/
    
    
    
    /*** PROMOTED RESULTS SETTINGS ***/
    // Declare the promoted results switches as a buttonset
    $( "#promotedResultsButtonset" ).buttonset();
    
    // If promoted results is on, highlight the On button
    if (promotedResultsValue === "true")
    {
        $( '[name="promotedResults"][value="true"]' ).prop("checked", true);
        $( '#promotedResultsButtonset' ).buttonset("refresh");
    }
    // If promoted results is off, highlight the Off button
    else if (promotedResultsValue === "false")
    {
        $( '[name="promotedResults"][value="false"]' ).prop("checked", true);
        $( '#promotedResultsButtonset' ).buttonset("refresh");
    };
    
    // Controls for when the user clicks one of the buttons
    $( "#promotedResults_On" ).click(function() {
        promotedResultsValue = true;
        $( '[name="promotedResults"][value="true"]' ).prop("checked", true);
        $( '#promotedResultsButtonset' ).buttonset("refresh");
        return false;
    });
    $( "#promotedResults_Off" ).click(function() {
        promotedResultsValue = false;
        $( '[name="promotedResults"][value="false"]' ).prop("checked", true);
        $( '#promotedResultsButtonset' ).buttonset("refresh");
        return false;
    });
    /*** PROMOTED RESULTS QTIP SETTINGS ***/
    // Initialise the qTip
    $("#promotedResultsHelpContainer").qtip(
    {
        content:
        {
            title:
            {
                text: 'Promoted Results'
            },
            text: 'Will promote results that have been marked as relevant by other users.<br /><br /><strong>Disabled by default</strong>.'
        },
        position:
        {
            my: 'left center',
            at: 'right center',
            target: $("#promotedResultsHelp")
        },
        style:
        {
            classes: 'ui-tooltip-blue ui-tooltip-shadow'
        },
        hide:
        {
            delay: 300
        }
    });
    /********************************************/
    
    
    
    /*** CLUSTERING SETTINGS ***/
    // Declare the clustering switches as a buttonset
    $( "#clusteringButtonset" ).buttonset();
    
    // If clustering is on, highlight the On button
    if (clusteringValue === "true")
    {
        $( '[name="clustering"][value="true"]' ).prop("checked", true);
        $( '#clusteringButtonset' ).buttonset("refresh");
    }
    // If clustering is off, highlight the Off button
    else if (clusteringValue === "false")
    {
        $( '[name="clustering"][value="false"]' ).prop("checked", true);
        $( '#clusteringButtonset' ).buttonset("refresh");
    };
    
    // Controls for when the user clicks one of the buttons
    $( "#clustering_On" ).click(function() {
        clusteringValue = true;
        $( '[name="clustering"][value="true"]' ).prop("checked", true);
        $( '#clusteringButtonset' ).buttonset("refresh");
        return false;
    });
    $( "#clustering_Off" ).click(function() {
        clusteringValue = false;
        $( '[name="clustering"][value="false"]' ).prop("checked", true);
        $( '#clusteringButtonset' ).buttonset("refresh");
        return false;
    });
    /*** CLUSTERING QTIP SETTINGS ***/
    // Initialise the qTip
    $("#clusteringHelpContainer").qtip(
    {
        content:
        {
            title:
            {
                text: 'Clustering'
            },
            text: 'Will check the search term for any disambiguation and provide a further search refinement.<br /><br /><strong>Disabled by default</strong>.'
        },
        position:
        {
            my: 'left center',
            at: 'right center',
            target: $("#clusteringHelp")
        },
        style:
        {
            classes: 'ui-tooltip-blue ui-tooltip-shadow'
        },
        hide:
        {
            delay: 300
        }
    });
    /********************************************/
    
    
    
    /*** EVALUATION SETTINGS ***/
    // Declare the evaluation switches as a buttonset
    $( "#evaluationButtonset" ).buttonset();
    
    // If evaluation is on, highlight the On button
    if (evaluationValue === "true")
    {
        $( '[name="evaluation"][value="true"]' ).prop("checked", true);
        $( '#evaluationButtonset' ).buttonset("refresh");
    }
    // If evaluation is off, highlight the Off button
    else if (evaluationValue === "false")
    {
        $( '[name="evaluation"][value="false"]' ).prop("checked", true);
        $( '#evaluationButtonset' ).buttonset("refresh");
    };
    
    // Controls for when the user clicks one of the buttons
    $( "#evaluation_On" ).click(function() {
        evaluationValue = true;
        $( '[name="evaluation"][value="true"]' ).prop("checked", true);
        $( '#evaluationButtonset' ).buttonset("refresh");
        return false;
    });
    $( "#evaluation_Off" ).click(function() {
        evaluationValue = false;
        $( '[name="evaluation"][value="false"]' ).prop("checked", true);
        $( '#evaluationButtonset' ).buttonset("refresh");
        return false;
    });
    /*** EVALUATION QTIP SETTINGS ***/
    // Initialise the qTip
    $("#evaluationHelpContainer").qtip(
    {
        content:
        {
            title:
            {
                text: 'Evaluation'
            },
            text: 'Will include evaluation results for the query if turned on.<br /><br /><strong>Disabled by default</strong>.'
        },
        position:
        {
            my: 'left center',
            at: 'right center',
            target: $("#evaluationHelp")
        },
        style:
        {
            classes: 'ui-tooltip-blue ui-tooltip-shadow'
        },
        hide:
        {
            delay: 300
        }
    });
    /********************************************/
    


    /*** GET FEEDBACK OPTIONS ***/
    $("#searchButton").click(function()
    {
        // check if feedback is on, send query in relevant direction, otherwise don't do anything
        if ( feedbackValue === "true" )
        {
            searchQuery = $("#searchInput").val();
            
            // apply loading animation
            $("#feedbackContainer").html('<div id="loadingDiv"></div>');
            $('#loadingDiv')
                .html("<div class='image'><img src='images/loading.gif' /></div>")
                .append("<div class='text'>Retrieving Feedback</div>");
            $("#feedbackContainer").load("/seng/bin/process.php", {service: "getFeedback", query: searchQuery}, function() {
                // remove loading animation
                $("#loadingDiv").html('');
            });
            
            $( "#feedbackContainer" ).show( "blind", 500 );
            return false;
        }
        else
        {
            searchQuery = $("#searchInput").val();
            
            // apply loading animation
            $("#resultsContainer").html('<div id="loadingDiv"></div>');
            $('#loadingDiv')
                .html("<div class='image'><img src='images/loading.gif' /></div>")
                .append("<div class='text'>Retrieving Results</div>");
            $("#resultsContainer").load("/seng/bin/process.php", {service: "Search", query: searchQuery}, function() {
                // remove loading animation
                $("#loadingDiv").html('');
            });
            
            $( "#resultsContainer" ).show( "blind", 500 );
            return false;
        }
    });
    /********************************************/
    


    /*** SAVE SETTINGS OPTIONS ***/
    // Convert save settings submit into jQuery button
    $( "input:submit", "#saveSettingsContainer" ).button();
        
    $("#saveSettings").click(function()
    {
        // Get the weight values for the search engines
        var blekkoWeighting = $("#blekkoWeight").val();
        var bingWeighting = $("#bingWeight").val();
        var entirewebWeighting = $("#entirewebWeight").val();
        // The process script needs to be told what action to do, declare it here
        var service = "SaveSettings";
        
        // Concatenate the required variables
        var dataString =    'service=' + service +
                            '&aggregation=' + aggValue +
                            '&caching=' + cachingValue +
                            '&queryTerm=' + queryTermValue +
                            '&clustering=' + clusteringValue +
                            '&evaluation=' + evaluationValue +
                            '&feedback=' + feedbackValue +
                            '&promotedResults=' + promotedResultsValue +
                            '&blekko=' + blekkoWeighting +
                            '&bing=' + bingWeighting +
                            '&entireweb=' + entirewebWeighting +
                            '&blekkoState=' + blekkoState +
                            '&bingState=' + bingState +
                            '&entirewebState=' + entirewebState;
        // Setup an AJAX call
        $.ajax({  
            type: "POST",  
            url: "/seng/bin/process.php",  
            data: dataString,  
            success: function()
            {
                // Display a confirmation message
                $('#saveSettingsContainer').append("<div id='submitSettingsMessageContainer'></div>");
                $('#submitSettingsMessageContainer')
                    .html("<div class='image'><img src='images/green_tick_24.png' /></div>")
                    .append("<div class='text'>Settings Saved</div>")
                    .hide()
                    .show( "fade", 2000)
                    .hide( "fade", 1000 );
                if ( feedbackValue === "true" )
                {
                    $("body").on("click", "#searchButton").find("#searchButton").attr('value','Get Feedback');
                    $("#notificationContainer").html("Feedback is on");
                    $( "#notificationContainer" ).show( "blind", 500 );
                }
                else
                {
                    $("body").on("click", "#searchButton").find("#searchButton").attr('value','Search');
                    $( "#notificationContainer" ).hide("blind", 500);
                    $( "#feedbackContainer" ).hide("blind", 500); 
                }
            }  
        });  
        return false;
    });  

});
</script>
