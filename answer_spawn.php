<?php

//获取列表和数据
$data = json_decode(file_get_contents('u3.txt'), true);
$data = $data['data'];
$list = json_decode(file_get_contents('list.txt'), true);

$bucket = array();
for ($i = 0; $i < count($data); $i++) {
    $bucket[$data[$i]['id']] = $data[$i];
}

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
$headers[] = 'Connection: close';
$headers[] = 'User-Agent: okhttp/2.7.0';

function _httpPost($url, $post_fields, $headers)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}

/* "user_code":"18265156276",
        "class_id":47246,
        "app_id":8,
        "type":14,
        "source_id":"",
        "book_id":0,
        "unit_id":0,
        "data":[
            {
                "word_id":0,
                "test_id":58630,
                "answer":"definitely",
                "duration":0,
                "result":0,
                "finish_time":1585983532,
                "src":1
            },]
 * */

//插入答案
for ($x = 0; $x < count($list['data']['stage'][0]['days']); $x++) {
    //初始化参数
    $answer = array();
    $answer['user_code'] = "18265156276";
    $answer['class_id'] = 47246;
    $answer['app_id'] = 8;
    $answer['type'] = 14;
    $answer['source_id'] = "";
    $answer['book_id'] = 0;
    $answer['unit_id'] = 0;
    //初始化答案
    $list_min = $list['data']['stage'][0]['days'][$x]['tasks'][0]['task_detail'];
    $title = $list['data']['stage'][0]['days'][$x]['title'];

    for ($i = 0; $i < count($list_min); $i++) {
        if ($bucket[$list_min[$i]['content_id']]) {
            $answer['data'][$i]['word_id'] = $bucket[$list_min[$i]['content_id']]['word_id'];
            $answer['data'][$i]['test_id'] = $list_min[$i]['content_id'];
            if (strpos($bucket[$list_min[$i]['content_id']]['answer'], '|'))
                $answer['data'][$i]['answer'] = strstr($bucket[$list_min[$i]['content_id']]['answer'], '|', true);
            else
                $answer['data'][$i]['answer'] = $bucket[$list_min[$i]['content_id']]['answer'];
            $answer['data'][$i]['duration'] = 3;
            $answer['data'][$i]['result'] = 0;
            $answer['data'][$i]['finish_time'] = time() + $i;
            $answer['data'][$i]['src'] = 1;
        }
    }
    $post_data[0] = $answer;
    echo (json_encode($post_data)) . "\n";
    $post_fields = "is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&app_id=8&device=0&platform=1&json_data=" .
        urlencode(json_encode($post_data));
    echo _httpPost("http://api.weicistudy.com/gaozhong/weici/group/learning/upload", $post_fields, $headers) . "\n";
}

