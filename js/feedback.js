<script>
	$(function() {
		$( "ul.droptrue" ).sortable({
			connectWith: "ul",
            cursor: "crosshair"
		});

		$( ".synonym, .relevant, .finalised" ).disableSelection();
        
        $( "#showMeTheList" ).button();
        
        $( "#showMeTheList" ).click(function() { 
            var result = $('.finalised').sortable('toArray');
            var searchQuery = result.join("%20");
            
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
            $( "#feedbackContainer" ).hide( "blind", 500 );
            return false;
        });
	});
    /*** SIMILAR TERMS QTIP SETTINGS ***/
    // Initialise the qTip
    $("#similarHelpContainer").qtip(
    {
        content:
        {
            title:
            {
                text: 'Similar Terms'
            },
            text: 'Display synonyms for each of the terms in your search query.'
        },
        position:
        {
            my: 'left center',
            at: 'right center',
            target: $("#similar-Help")
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
    
    /*** RELEVANT TERMS QTIP SETTINGS ***/
    // Initialise the qTip
    $("#relevantHelpContainer").qtip(
    {
        content:
        {
            title:
            {
                text: 'Relevant Terms'
            },
            text: 'Display relevant terms based on your search query.'
        },
        position:
        {
            my: 'left center',
            at: 'right center',
            target: $("#relevant-Help")
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
</script>
