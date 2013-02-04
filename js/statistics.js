<script>
$(function() {
    
    // Hide the relevancyContainer by default
    $( ".statisticsContainer" ).hide();
    
    $( ".showStatistics" ).click(function()
    {
        $(this).siblings(".statisticsContainer").toggle( "blind", 500 );
        return false;
    });
    
});
</script>
