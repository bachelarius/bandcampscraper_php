<?php
// HACK: initialize the WordPress environment, enough to use the Plugin
require_once( dirname(__FILE__) . '/../../../../wp-load.php' );

    $url = htmlspecialchars($_GET["url"]);
    
    if (empty($url)){
        echo "<h1>Welcome to the Bandcamp scraper. </h1> 
        <p>This service returns all available track/album data as a JSON result. Please provide a Bandcamp track or album URL by adding ?url=[link], </p>
        <p>e.g. <a href='/wp-content/plugins/fullwidth-audio-player/inc/bandcampScraper.php?url=https://abdominal.bandcamp.com/track/broken'>?url=https://abdominal.bandcamp.com/track/broken</a></p>";
        http_response_code(400);
        return;
    } 
    
    $bc = wp_remote_get($url);
    if (!$bc || is_wp_error($bc)){
        echo "The url '" . $url . "' could not be found.";
        http_response_code(404);
        return;
    }
    $pageData = wp_remote_retrieve_body($bc);
    
    //find the variable TralbumData, as it basically already is a JSON var
    // with everything we need already there.
    $indexOfStartOfData =  strpos($pageData, "var TralbumData");
    $indexOfStartOfBandcamp =  strpos( strtolower($pageData), "bandcamp");

    if ( ! $indexOfStartOfBandcamp || ! $indexOfStartOfData) {
        echo "<h1>The URL '" . $url . "' is not a valid bootcamp url</h1><h2>Data Returned:</h2>";
        echo print_r($bc);
        echo $pageData;
        http_response_code(400);
        return;
    } 
        
    // the +1 is to include the last '}' but exclude the ';'.
    $indexOfEndOfData = strpos($pageData, "};", $indexOfStartOfData) + 1;
    $lengthOfData = $indexOfEndOfData - $indexOfStartOfData;
    $relevantBitOfPage = substr($pageData, $indexOfStartOfData, $lengthOfData);
    
    $output = "{";
    // foreach line of the output...
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $relevantBitOfPage) as $line){
        $line = trim($line);
        //we are ignoring comments and variable declarations, as they
        // are not valid json.
        if (startsWith($line,"//") || startsWith(strtolower($line),"var")){
            continue;
        }
        //if the line contains a colon, e.g. artist : "artistName", 
        // we need to wrap the phrase before the colon with quotes.
        // TODO: Make this less hacky.
        if (strpos($line, ":")){
            //get rid of any spaces before colon
            $line = preg_replace('/ :/', ':', $line, 1);
            //add closing quote
            $line = preg_replace('/:/', '":', $line, 1);
            //add opening quote
            $line = "\"" . $line;
        }
        //if the line contains an end of line quote, then remove it. 
        // Again, this is very hacky...
        if (strpos($line, ", //")){
            $line = preg_replace ('/, \/\/(.*)/' , ',' , $line);
        }
        //if the line contains a string concatination, then we need 
        // to actually concatonate the strings.
        // TODO: Make this less hacky.
        if (strpos($line, "\" + \"")){
            $line = str_replace("\" + \"", "", $line);
        }
        
        // add the line to the final output.
        $output .= $line;
    }
    echo $output;
    
    //source: http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
    function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

?>
