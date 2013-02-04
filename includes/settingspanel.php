<form name="settings" method="post" action="" id="settings">
    <div id="settingsWidgetsContainer">
        <div id="aggregationSwitchContainer">
            <div class="labelText">
                Aggregation
            </div>
            <div id="aggregationSwitchButtonset">
                <input type="radio" id="aggregationSwitch_On" name="aggregationSwitch" value="true" />
                    <label for="aggregationSwitch_On">On</label>
                <input type="radio" id="aggregationSwitch_Off" name="aggregationSwitch" value="false" />
                    <label for="aggregationSwitch_Off">Off</label>
            </div>
        </div>
        
        <div id="aggregationEngineWeightsContainer">
            <div class="weightingLabel">
                Adjust the weighting of each search engine
            </div>
            <div class="sliderContainer">
                <div class="sliderLabel">bing</div>
                <div class="sliderDiv"><div id="slider-bing"></div></div>
                <div class="sliderValue"><input type="text" id="bingWeight" /></div>
            </div>
            <div class="sliderContainer">
                <div class="sliderLabel">entireweb</div>
                <div class="sliderDiv"><div id="slider-entireweb"></div></div>
                <div class="sliderValue"><input type="text" id="entirewebWeight" /></div>
            </div>
            <div class="sliderContainer">
                <div class="sliderLabel">blekko</div>
                <div class="sliderDiv"><div id="slider-blekko"></div></div>
                <div class="sliderValue"><input type="text" id="blekkoWeight" /></div>
            </div>
            <div id="sliderResetContainer"><button id="sliderResetButton">Default Weighting</button></div>
        </div>
        
        <div id="miscSettingsContainer">
            <?php
            $options = array("caching", "evaluation", "feedbackoptions", "queryTerm", "promotedResults"/*, "clustering"*/);
            $labels = array("Caching", "Evaluation", "Feedback", "Query Term Boost", "Promoted Results"/*, "Clustering"*/);
            for ( $i = 0; $i < count($options); $i++ )
            {
                if ( $i == 0 )
                {
                    echo '<div id="defaultOn">';
                }
                elseif ( $i == 3 )
                {
                    echo '</div>';
                    echo '<div id="defaultOff">';
                }
                echo '
                <div id="'.$options[$i].'Container" class="miscSettingsIndContainer">
                    <div class="labelText">
                        '.$labels[$i].'
                    </div>
                    <div id="'.$options[$i].'HelpContainer" class="miscSettingsIndHelpContainer"><img src="images/help_16.png" id="'.$options[$i].'Help" /></div>
                    <div id="'.$options[$i].'Buttonset" class="miscSettingsIndButtonset">
                        <input type="radio" id="'.$options[$i].'_On" name="'.$options[$i].'" value="true" /><label for="'.$options[$i].'_On">On</label>
                        <input type="radio" id="'.$options[$i].'_Off" name="'.$options[$i].'" value="false" /><label for="'.$options[$i].'_Off">Off</label>';
                    echo '</div>
                </div>';
            }
            ?>
            </div>
        </div>
        
        <div id="engineContainer">
            <input type="checkbox" id="bingCheckbox" /><label for="bingCheckbox">Bing</label>
            <input type="checkbox" id="blekkoCheckbox" /><label for="blekkoCheckbox">Blekko</label>
            <input type="checkbox" id="entirewebCheckbox" /><label for="entirewebCheckbox">Entireweb</label>
            <?php echo $_SESSION['bingStatus']; ?>
            <?php echo $_SESSION['blekkoStatus']; ?>
            <?php echo $_SESSION['entirewebStatus']; ?>
        </div>
        
    </div>
    
    <div id="saveSettingsContainer">
        <div><input type="submit" value="Save Settings" id="saveSettings" /></div>
    </div>
</form>
