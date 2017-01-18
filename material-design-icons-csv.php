<?php


$mdiUrl = 'https://cdn.materialdesignicons.com/1.6.50/';
// $officialMdiUrl = 'https://material.io/icons/data/grid.json';
// $thirdParty = 'https://material.io/icons/webcomponents/third-party.html';

/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
 * array containing the HTTP server response header fields and content.
 */
function get_web_page($url)
{
    $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $options = array(
            CURLOPT_CUSTOMREQUEST => 'GET',        //set request type post or get
            CURLOPT_POST => false,        //set to GET
            CURLOPT_USERAGENT => $user_agent, //set user agent
            CURLOPT_COOKIEFILE => 'cookie.txt', //set cookie file
            CURLOPT_COOKIEJAR => 'cookie.txt', //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING => '',       // handle all encodings
            CURLOPT_AUTOREFERER => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT => 120,      // timeout on response
            CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects
        );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $header = curl_getinfo($ch);
    curl_close($ch);

    $header['errno'] = $err;
    $header['errmsg'] = $errmsg;
    $header['content'] = $content;

    return $header;
}

// Material Icons from Google - https://material.io/icons/

if ($officialMdiUrl) {
    $result = get_web_page($officialMdiUrl);
    $arr = json_decode($result['content']);

    foreach ($arr->icons as $groups) {
        if ($count) {
            echo ', ';
        }
        echo $groups->ligature;
        $count = $count + 1;
    }
}

// Material Icons from https://cdn.materialdesignicons.com/1.6.50/

if ($mdiUrl) {
    $result = get_web_page($mdiUrl);

    $doc = new DOMDocument();
    $doc->loadHTML($result['content']);

    $scriptData = $doc->getElementsByTagName('script')->item(0)->textContent;

    $jsonStart = explode('icons = ', $scriptData);
    $mdiJson = explode(';', trim($jsonStart[1]));
    $jsonData = trim($mdiJson[0]);

    $arr = json_decode($jsonData);

    foreach ($arr as $icons) {
        if ($count) {
            echo ', ';
        }
        echo $icons->name;
        $count = $count + 1;
    }
}

// Third Party Icons - view-source:https://material.io/icons/webcomponents/third-party.html

if ($thirdParty) {
    $result = get_web_page($thirdParty);

        //if ( $result['errno'] != 0 )
        // 	{ echo 'error'; } //  ... error: bad url, timeout, redirect loop ...

        //if ( $result['http_code'] != 200 )
        //	{ echo 'error'; }
        //  ... error: no page, no permissions, no service ...

        $doc = new DOMDocument();
    $doc->loadHTML($result['content']);
    $divs = $doc->getElementsByTagName('g');

    if (count($divs)) {
        foreach ($divs as $div) {
            if ($count) {
                echo ', ';
            }
            echo $div->getAttribute('id').', ';
            $count = $count + 1;
        }
    }
}
