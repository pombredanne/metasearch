<script>
/* Fill the search box with the current query */

$(function(){
    var text = "<?php echo $_SESSION['queryDisplay']; ?>";
    $("input[name=searchQuery]", "#searchbarContainer").val(text);
});
</script>
