<?php
require_once('../includes/head.php');
getHeadFiles('feedback');

    echo '<div class="column">';
        echo '<div class="heading">';
            echo '<div class="title">Similar Terms</div>';
            echo '<div id="similarHelpContainer" class="icon"><img src="images/help_16.png" id="similar-Help" /></div>';
        echo '</div>';
        
        echo '<div>';
        for ( $i = 0; $i < $queryArraySize; $i++ )
        {
            echo '<div class="listContainer">';
                echo '<div class="synonymTitle">'.$queryArray[$i].'</div>';
                echo '<div>';
                echo '<ul id="synonym-'.$i.'" class="synonym droptrue">';
                for ( $j = 0; $j < $synonymsAmount; $j++ )
                {
                    if ( $querySynonyms[$i][0] === NULL )
                    // If the first synonym is NULL, disable the whole ul
                    {
                        echo '<script>$("#synonym-'.$i.'").sortable({ disabled: true });</script>';
                        echo "<li id='noMatchingWord' class='ui-state-error ui-state-disabled'>No matching words</li>";
                        break;
                    }
                    elseif ( $querySynonyms[$i][$j] === NULL && $j >= 1 )
                    // If the synonym is NULL but it's not the first entry just break
                    {
                        break;
                    }
                    else
                    {
                        echo "<li id=".urlencode($querySynonyms[$i][$j])." class='ui-state-highlight'>".$querySynonyms[$i][$j]."</li>";
                    }
                }
                echo '</ul>';
                echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    echo '</div>';

    echo '<div class="column">';
        echo '<div class="heading">';
            echo '<div class="title">Relevant Terms</div>';
            echo '<div id="relevantHelpContainer" class="icon"><img src="images/help_16.png" id="relevant-Help" /></div>';
        echo '</div>';
        
        echo '<div class="listContainer">';
            echo '<ul class="relevant droptrue">';
            if ( $mergedTerms[$i] === NULL )
            {
                echo '<script>$(".relevant").sortable({ disabled: true });</script>';
                echo "<li id='noMatchingWord' class='ui-state-error ui-state-disabled'>No matching words</li>";
            }
            else
            {
                for ( $i = 0; $i < 10; $i++ )
                {
                    echo "<li id=".urlencode($mergedTerms[$i])." class='ui-state-highlight'>".$mergedTerms[$i]."</li>";
                }
            }
            echo '</ul>';
        echo '</div>';
    echo '</div>';

    echo '<div class="column">';
        echo '<div class="heading">';
            echo '<div class="title">Finished Query</div>';
        echo '</div>';
        
        // For the finished query I want to include every term that the user entered without removing anything
        echo '<div class="listContainer">';
            echo '<ul class="finalised droptrue">';
            for ( $i = 0; $i < $queryAPIArraySize; $i++ )
            {
                echo "<li id=".urlencode($queryAPIArray[$i])." class='ui-state-default ui-priority-primary'>".$queryAPIArray[$i]."</li>";
            }
            echo '</ul>';
        echo '</div>';
        echo '<div><button id="showMeTheList">Search</button></div>';
    echo '</div>';
?>
