<script>
/* Highlights the query terms within the search results */

$(function(){
    <?php
    $tok = strtok(strtolower($_SESSION['queryDisplay']), "() NOT OR AND || && -");

    while ($tok !== false) {
        $termArray[] = $tok;
        $tok = strtok("() NOT OR AND || && -");
    }
    foreach ($termArray as $term)
    {
        echo '$( ".resultContainer" ).highlight(\''.$term.'\');';
    }
    ?>
});
</script>
