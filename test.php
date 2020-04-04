<?php
function _httpGet($url)
{

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}
echo _httpGet("http://api.weicistudy.com/gaozhong/weici/course/course/test?course_id=68&is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&app_id=8&device=0&platform=1");
