<?php
$remote = false;
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

//获取主页网格视图
echo "开始获取主网格视图\n";
if ($remote) {
    $start = _httpGet("http://api.weicistudy.com/activity/open?is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&version_id=1&app_id=8&device=0&platform=1");
    echo "主网格视图获取成功\n";
    file_put_contents('main.json', $start);
} else {
    $start = file_get_contents('main.json');
}

$start = json_decode($start, true);
//var_dump($start);
$start = $start['data'];
for ($i = 0; $i < count($start); $i++) {
    echo $start[$i]['activity_name'] . "\n";
    //创建一级目录
    if (!is_dir($start[$i]['activity_name']))
        mkdir($start[$i]['activity_name']);
    //根据上一个接口获取的id来获取data_id. data_id用于获取对应的索引和数据集合
    if ($remote) {
        $data_id = _httpGet("http://api.weicistudy.com/activity/sub?is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&id=" . $start[$i]['id'] . "&app_id=8&device=0&platform=1");
        file_put_contents($start[$i]['activity_name'] . '/origin.json', $data_id);
    } else $data_id = file_get_contents($start[$i]['activity_name'] . '/origin.json');
    $data_id = json_decode($data_id, true);
    $data_id = $data_id['data'];
    for ($j = 0; $j < count($data_id); $j++) {
        //创建二级目录
        if (!is_dir($start[$i]['activity_name'] . '/' . $data_id[$j]['alias']))
            mkdir($start[$i]['activity_name'] . '/' . $data_id[$j]['alias']);
        //激活该data_id
//        echo _httpPost("http://api.weicistudy.com/gaozhong/weici/course/course/save", "course_id=" . $data_id[$j]['data_id'] . "&is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&type=0&app_id=8&device=0&platform=1");
//        echo "\n";
        //根据data_id获取索引集合

        if ($remote) {
            echo "开始获取索引集合-" . $data_id[$j]['alias'] . "\n";
            $list = _httpGet("http://api.weicistudy.com/gaozhong/weici/course/course/stage?course_id=" . $data_id[$j]['data_id'] . "&is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&app_id=8&device=0&platform=1");
            file_put_contents($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/list.json', $list);
            echo "索引集合获取成功-" . $data_id[$j]['alias'] . "\n";
        } else {
            $list = file_get_contents($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/list.json');
        }
        echo "--" . $data_id[$j]['alias'] . "\n";
        $list = json_decode($list, true);
        //根据data_id获取数据集合
        if ($remote) {
            echo "开始获取数据集合-" . $data_id[$j]['alias'] . "\n";
            $data = _httpGet("http://api.weicistudy.com/gaozhong/weici/course/course/test?course_id=" . $data_id[$j]['data_id'] . "&is_wifi=1&app_version=331&user_code=18265156276&bound_id=479f887d2eeb49be884f2dfcccae03351&session=eb6319e589284e4cb7ac9a1fb943f5d9&app_id=8&device=0&platform=1");
            file_put_contents($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/data.json', $data);
            echo "数据集合获取成功-" . $data_id[$j]['alias'] . "\n";
        } else {
            $data = file_get_contents($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/data.json');
        }
        $data = json_decode($data, true);
        //由于未知的原因部分data_id读取不到
        if (!$data['data']) continue;
        $data = $data['data'];
        //数据入桶,便于使用索引读取
        $bucket = array();
        for ($k = 0; $k < count($data); $k++) {
            $bucket[$data[$k]['id']] = $data[$k];
        }
//        for ($l = 0; $l < count($list['data']['stage'][0]['days']); $l++) {
        //从索引集合中分离出单个页的索引
        $stage = $list['data']['stage'];
        for ($n = 0; $n < count($stage); $n++) {
            //创建三级目录
            if (!is_dir($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/' . $stage[$n]['stage_name']))
                mkdir($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/' . $stage[$n]['stage_name']);
            echo "----" . $stage[$n]['stage_name'] . "\n";
            $days = $stage[$n]['days'];
            for ($o = 0; $o < count($days); $o++) {
                //创建四级目录
                if (!is_dir($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/' . $stage[$n]['stage_name'] . '/' . $days[$o]['stage_day']))
                    mkdir($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/' . $stage[$n]['stage_name'] . '/' . $days[$o]['stage_day']);
                $tasks = $days[$o]['tasks'];
                for ($p = 0; $p < count($tasks); $p++) {
                    $title = $tasks[$p]['task_name'];
                    $answer = "";
                    $list_min = $tasks[$p]['task_detail'];
                    for ($q = 0; $q < count($list_min); $q++) {
                        //根据单个索引获取桶中的数据
                        if (isset($list_min[$q]))
                            if (isset($bucket[$list_min[$q]['content_id']])) {
                                $answer .= ($q + 1) . "\n";
//                                $answer .= "题目:" . $bucket[$list_min[$q]['content_id']]['subject'] . "\n";
//                                $answer .= "答案:" . $bucket[$list_min[$q]['content_id']]['answer'];
//                                $answer .= "\n\n";
                                $answer .= "题目类型:";
                                switch ($bucket[$list_min[$q]['content_id']]['question']) {
                                    case 0:
                                    {
                                        $answer .= "听力\n";
                                        break;
                                    }
                                    case 1:
                                    {
                                        break;
                                    }
                                    case 2:
                                    {
                                        break;
                                    }
                                    case 3:
                                    {
                                        break;

                                    }
                                    case 4:
                                    {
                                        break;

                                    }
                                    case 5:
                                    {
                                        break;

                                    }
                                    case 6:
                                    {
                                        break;

                                    }
                                    case 7:
                                    {
                                        break;
                                    }
                                    case 8:
                                    {
                                        break;

                                    }
                                    case 9:
                                    {
                                        break;

                                    }
                                    case 10:
                                    {
                                        break;

                                    }

                                }
                            } else echo "未定义" . $list_min[$q]['content_id'] . "在文件 " . $title . ".txt" . " 中 day" . $days[$o]['stage_day'] . "\n";
                    }
                    //单个页写入到四级目录中
                    if (is_file($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/' . $stage[$n]['stage_name'] . '/' . $days[$o]['stage_day'] . '/' . $title . ".txt"))
                        unlink($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/' . $stage[$n]['stage_name'] . '/' . $days[$o]['stage_day'] . '/' . $title . ".txt");
                    file_put_contents($start[$i]['activity_name'] . '/' . $data_id[$j]['alias'] . '/' . $stage[$n]['stage_name'] . '/' . $days[$o]['stage_day'] . '/' . $title . ".txt", $answer);
                }
            }
        }


    }
}