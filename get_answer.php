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

$start = _httpGet("http://api.weicistudy.com/activity/open?is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&version_id=1&app_id=8&device=0&platform=1");
//var_dump($start);
file_put_contents('origin.json', $start);
$start = json_decode($start, true);
//var_dump($start);
$start = $start['data'];
for ($i = 0; $i < count($start); $i++) {
    echo $start[$i]['activity_name'] . "\n";
    mkdir($start[$i]['activity_name']);
    $data_id = _httpGet("http://api.weicistudy.com/activity/sub?is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&id=" . $start[$i]['id'] . "&app_id=8&device=0&platform=1");
    file_put_contents($start[$i]['activity_name'] . '/origin.json', $data_id);
    $data_id = json_decode($data_id, true);
    $data_id = $data_id['data'];
    for ($j = 0; $j < count($data_id); $j++) {
//        echo "__".$data_id[$j]['alias']."\n";
        mkdir($start[$i]['activity_name'] . '/' . $data_id[$j]['alias']);

        $list = _httpGet("http://api.weicistudy.com/gaozhong/weici/course/course/stage?course_id=" . $data_id[$j]['data_id'] . "&is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&app_id=8&device=0&platform=1");
        file_put_contents($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/list.json', $list);
        $list = json_decode($list, true);
        $data = _httpGet("http://api.weicistudy.com/gaozhong/weici/course/course/test?course_id=" . $data_id[$j]['data_id'] . "&is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&app_id=8&device=0&platform=1");
        file_put_contents($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/data.json', $data);
        $data = json_decode($data, true);
        if (!$data['data']) continue;
        $data = $data['data'];

        $bucket = array();
        for ($k = 0; $k < count($data); $k++) {
            $bucket[$data[$k]['id']] = $data[$k];
        }

        for ($l = 0; $l < count($list['data']['stage'][0]['days']); $l++) {
            $list_min = $list['data']['stage'][0]['days'][$l]['tasks'][0]['task_detail'];
            $title = $list['data']['stage'][0]['days'][$l]['title'];
            $answer = '';

            for ($m = 0; $m < count($list_min); $m++) {
                if ($bucket[$list_min[$m]['content_id']]) {
                    $answer .= ($m + 1) . "\n";
                    $answer .= "题目:" . $bucket[$list_min[$m]['content_id']]['subject'] . "\n";
                    $answer .= "答案:" . $bucket[$list_min[$m]['content_id']]['answer'];
                    $answer .= "\n\n";
                }
            }
            file_put_contents($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/' . $title . ".txt", $answer);
        }

    }
}