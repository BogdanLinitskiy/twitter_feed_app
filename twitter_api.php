<?php

$token = '1898376746-FeTIcgHExNlKcBNaNYAPdW4G3r8UmunEJWu9Mik';
$token_secret = 'iAPsREv22YG9BItIIpF9DcF3K7qEiNT7qR1usTpvwfrsn';
$consumer_key = '6lmomZLqZcBj4IEpKsoZAQqxk';
$consumer_secret = 'LtnciTfGVJl5SmvqR6qqx8Xch5qrIYYFut408ChAerhD1oRbx2';

$host = 'api.twitter.com';
$method = 'GET';
$path = '/1.1/statuses/user_timeline.json'; // api call path

$query = array( // в ключе 'screen_name' и 'count' указываются имя пользователя и кол-во(максимальное) твиттов
    'screen_name' => 'h3h3productions',
    'count' => '25'
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


$i=0;
foreach ($twitter_data as $tweet_data) {
    foreach ($tweet_data as $key => $data) {
        if($key=='in_reply_to_screen_name')
        {
            $rt_reply[$i] =$data;
        }
        elseif ($key == 'entities'){
            foreach($data as $key_=>$entity){
                foreach ($entity as $key__=> $value) {
                    foreach ($value as $index=> $name) {
                        if($index =='screen_name'){
                            $rt_screen_name[$i] = $name;
                        }elseif ($index == 'name'){
                            $rt_name[$i] = $name;
                        }
                    }
                }
            }
        }

        elseif($key == 'user') {
            foreach ($data as $key_ => $user) {
                if ($key_ == 'name') {
                    $names[] = $user;
                } elseif ($key_ == 'screen_name') {
                    $screen_names[] = $user;
                } elseif ($key_ == 'profile_image_url') {
                    $images[] = $user;
                }
            }
        }

        elseif($key == 'retweeted_status') {
            foreach ($data as $key_ => $item) {
                if ($key_ == 'user') {
                    foreach ($item as $key__ => $tweet) {
                        if ($key__ == 'profile_image_url') {
                            $rt_image[$i] = $tweet;
                        }elseif($key__ == 'screen_name'){
                                $rt_name[$i] = $tweet;

                        }
                    }
                }

            }
        }

        elseif(is_object($data)) {
            continue;
        } else {
            if ($key == 'text') {
                $texts[] = $data;
            }
            if ($key == 'created_at') {
                $created_at[] = $data;
            }
            if ($key == 'id_str') {
                $source[] = $data;
            }
        }
    }
    $i++;
}

