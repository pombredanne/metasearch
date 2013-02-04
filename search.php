<?php
    require_once('includes/head.php');
    getHeadFiles('search');
?>
    <div id="searchQueryDisplay">
    <?php
    if ($_SESSION['aggregation'] === "false")
    {
        echo "Displaying non-aggregated results for: ";
    }
    elseif ($_SESSION['aggregation'] === "true")
    {
        echo "Displaying aggregated results for: ";
    }
        echo "<strong>".$_SESSION['queryDisplay']."</strong>";
    ?>
    </div>
    
    <?php
    if ($_SESSION['aggregation'] === "false")
    {
        require_once('includes/searchNonAggregatedResults.php');
    }
    elseif ($_SESSION['aggregation'] === "true")
    {
        require_once('includes/searchAggregatedResults.php');
    }
    ?>
