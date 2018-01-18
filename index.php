<?php

$token = '1898376746-FeTIcgHExNlKcBNaNYAPdW4G3r8UmunEJWu9Mik';
$token_secret = 'iAPsREv22YG9BItIIpF9DcF3K7qEiNT7qR1usTpvwfrsn';
$consumer_key = '6lmomZLqZcBj4IEpKsoZAQqxk';
$consumer_secret = 'LtnciTfGVJl5SmvqR6qqx8Xch5qrIYYFut408ChAerhD1oRbx2';

$host = 'api.twitter.com';
$method = 'GET';
$path = '/1.1/statuses/user_timeline.json'; // api call path

$query = array( // query parameters
    'screen_name' => 'dreadisfat',
    'count' => '15'
);

$oauth = array(
    'oauth_consumer_key' => $consumer_key,
    'oauth_token' => $token,
    'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
    'oauth_timestamp' => time(),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_version' => '1.0'
);

$oauth = array_map("rawurlencode", $oauth); // must be encoded before sorting
$query = array_map("rawurlencode", $query);

$arr = array_merge($oauth, $query); // combine the values THEN sort

asort($arr); // secondary sort (value)
ksort($arr); // primary sort (key)

// http_build_query automatically encodes, but our parameters
// are already encoded, and must be by this point, so we undo
// the encoding step
$querystring = urldecode(http_build_query($arr, '', '&'));

$url = "https://$host$path";

// mash everything together for the text to hash
$base_string = $method."&".rawurlencode($url)."&".rawurlencode($querystring);

// same with the key
$key = rawurlencode($consumer_secret)."&".rawurlencode($token_secret);

// generate the hash
$signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));

// this time we're using a normal GET query, and we're only encoding the query params
// (without the oauth params)
$url .= "?".http_build_query($query);
$url=str_replace("&amp;","&",$url); //Patch by @Frewuill

$oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
ksort($oauth); // probably not necessary, but twitter's demo does it

// also not necessary, but twitter's demo does this too
function add_quotes($str) { return '"'.$str.'"'; }
$oauth = array_map("add_quotes", $oauth);

// this is the full value of the Authorization line
$auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

// if you're doing post, you need to skip the GET building above
// and instead supply query parameters to CURLOPT_POSTFIELDS
$options = array( CURLOPT_HTTPHEADER => array("Authorization: $auth"),
    //CURLOPT_POSTFIELDS => $postfields,
    CURLOPT_HEADER => false,
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false);

// do our business
$feed = curl_init();
curl_setopt_array($feed, $options);
$json = curl_exec($feed);
curl_close($feed);

$twitter_data = json_decode($json);

echo '<pre>';

foreach ($twitter_data as $tw){
    foreach ($tw as $key=>$t){
        if($key =='user') {
            foreach ($t as $k => $ttt) {
                if ($k == 'name') {
                    $names[] = $ttt;
                }
            }
        }
        if(is_object($t)) {
            continue;
        }else{
            if($key =='text'){
                $texts[] = $t;
            }
            if($key =='created_at'){
                $created_at[] = $t;
            }
        }
    }
    for($i=0;$i<count($names);$i++){
        echo "<p>$names[$i]</p>";
        echo "<p>$texts[$i]</p>";
        echo "<p>$created_at[$i]</p>";
        echo "<br>";
    }

};