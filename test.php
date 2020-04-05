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

function _httpPost($url, $fields)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}


for ($i = 1; $i <= 100; $i++) {
    echo _httpPost("http://api.weicistudy.com/gaozhong/weici/course/course/save", "course_id=" . $i . "&is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&type=0&app_id=8&device=0&platform=1");
    $list = _httpGet("http://api.weicistudy.com/gaozhong/weici/course/course/stage?course_id=" . $i . "&is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&app_id=8&device=0&platform=1");
    $list_json = json_decode($list, true);
    if ($list_json['data'])
        file_put_contents('origin/' . $i . '_list.json', $list);
    $data = _httpGet("http://api.weicistudy.com/gaozhong/weici/course/course/test?course_id=" . $i . "&is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&app_id=8&device=0&platform=1");
    $data_json = json_decode($data, true);
    if ($data_json['data'])
        file_put_contents('origin/' . $i . '_data.json', $data);
    echo $i . "\n";
}