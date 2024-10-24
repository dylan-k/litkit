<?php
/*
 * @author @skinofstars Kevin Carmody
 * GPLv3 - https://www.gnu.org/copyleft/gpl.html
 *
 * this is really a command line app with no flags
 * for turning a bunch ofurls into an OPML file
 *
 * 1.takes input file of newline seperated urls, normally blogs
 * 2.finds (autodiscovery) associated rss of each url
 * 3.outputs an OPML file for you to use in a feed reader
 */

// file config
$inputFile = "URLlist.txt";
$outputFile = "blogroll.opml";

// OPML config
$opmlTitle = "Some Select Blogs";
$opmlOwnerName = "Dylan Kinnett";
$opmlOwnerEmail = "dylan@nocategories.net";

/** no need to edit after this :) **/
$inHandle = @fopen($inputFile, "r");//read-only
$outHandle = @fopen($outputFile, "a");//append

if ($inHandle && $outHandle) {
    $headerOut = opmlHeader($opmlTitle,$opmlOwnerName,$opmlOwnerEmail);
    fwrite($outHandle,$headerOut);

    while (!feof($inHandle)) {
        $buffer = fgets($inHandle, 4096);
        $source = getFile($buffer);
        $rssURL = getRSSLocation($source, $buffer);
        $rssTitle = htmlentities(getTitleAlt($source));
        if($rssURL){
            if($rssTitle){
                $entryOut = opmlEntry($rssURL,$rssTitle);
                fwrite($outHandle,$entryOut);
            } else {
                $entryOut = opmlEntry($rssURL,$rssURL);
                fwrite($outHandle,$entryOut);
            }
            //echo ".";//uncomment to print a dot to screen on each success, nice for seeing progress
        } else {
            echo "Fail on: ".$buffer;
        }
    }
    $footerOut = opmlFooter();
    fwrite($outHandle,$footerOut);

    fclose($inHandle);
    fclose($outHandle);
} else {
    if(!$inHandle){
        echo 'not got a handle on input file: '.$inputFile."\n";
        die;
    }
    if(!$outHandle){
        echo 'not got a got handle on output file: '.$outputFile."\n";
        die;
    }
}

echo "\nAll done :)\n";

/**
 * basic opml header
 * @param string $opmlTitle
 * @param string $opmlOwnerName
 * @param string $opmlOwnerEmail
 * @return string
 */
function opmlHeader($opmlTitle,$opmlOwnerName,$opmlOwnerEmail){
    $oheader = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n"
    ."<opml version=\"1.1\">\n"
    ." <head>\n"
    ."     <title>".$opmlTitle."</title>\n"
    ."     <dateCreated>".date("r")."</dateCreated>\n"
    ."     <ownerName>".$opmlOwnerName."</ownerName>\n"
    ."     <ownerEmail>".$opmlOwnerEmail."</ownerEmail>\n"
    ."     </head>\n"
    ." <body>\n";
    return $oheader;
}

/**
 * just returns a test footer
 * @return string
 */
function opmlFooter(){
    $ofooter = "  </body>\n"
    ."</opml>";
    return $ofooter;
}

/**
 * creates an XML entry for the OPML file
 * @param string $feedURL
 * @param string $feedTitle
 * @return string
 */
function opmlEntry($feedURL,$feedTitle){
    $outline = "    <outline text=\"".$feedTitle."\" type=\"rss\" xmlUrl=\"".$feedURL."\"/>\n";
    return $outline;
}

/**
 * returns the page title extracted from source
 * @param string $html
 * @return string
 */
function getTitleAlt($html) {
    if (preg_match('/<title>(.*?)<\/title>/is',$html,$found)) {
        $title = $found[1];
        return $title;
    } else {
        return;
    }
}

/**
 * https://keithdevens.com/weblog/archive/2002/Jun/03/RSSAuto-DiscoveryPHP
 * public domain
 */
function getFile($location){
    $ch = curl_init($location);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

/**
 * https://keithdevens.com/weblog/archive/2002/Jun/03/RSSAuto-DiscoveryPHP
 * public domain
 */
function getRSSLocation($html, $location){
    if(!$html or !$location){
        return false;
    }else{
        #search through the HTML, save all <link> tags
        # and store each link's attributes in an associative array
        preg_match_all('/<link\s+(.*?)\s*\/?>/si', $html, $matches);
        $links = $matches[1];
        $final_links = array();
        $link_count = count($links);
        for($n=0; $n<$link_count; $n++){
            $attributes = preg_split('/\s+/s', $links[$n]);
            foreach($attributes as $attribute){
                $att = preg_split('/\s*=\s*/s', $attribute, 2);
                if(isset($att[1])){
                    $att[1] = preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]);
                    $final_link[strtolower($att[0])] = $att[1];
                }
            }
            $final_links[$n] = $final_link;
        }
        #now figure out which one points to the RSS file
        for($n=0; $n<$link_count; $n++){
            if(strtolower($final_links[$n]['rel']) == 'alternate'){
                if(strtolower($final_links[$n]['type']) == 'application/rss+xml'){
                    $href = $final_links[$n]['href'];
                }
                if(!$href and strtolower($final_links[$n]['type']) == 'text/xml'){
                    #kludge to make the first version of this still work
                    $href = $final_links[$n]['href'];
                }
                if($href){
                    if(strstr($href, "https://") !== false){ #if it's absolute
                        $full_url = $href;
                    }else{ #otherwise, 'absolutize' it
                        $url_parts = parse_url($location);
                        #only made it work for https:// links. Any problem with this?
                        $full_url = "https://$url_parts[host]";
                        if(isset($url_parts['port'])){
                            $full_url .= ":$url_parts[port]";
                        }
                        if($href{0} != '/'){ #it's a relative link on the domain
                            $full_url .= dirname($url_parts['path']);
                            if(substr($full_url, -1) != '/'){
                                #if the last character isn't a '/', add it
                                $full_url .= '/';
                            }
                        }
                        $full_url .= $href;
                    }
                    return $full_url;
                }
            }
        }
        return false;
    }
}
