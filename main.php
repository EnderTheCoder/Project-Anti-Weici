<?php
$data = json_decode(file_get_contents('u3.txt'), true);
$data = $data['data'];
$list = json_decode(file_get_contents('list.txt'), true);

$bucket = array();
for ($i = 0; $i < count($data); $i++) {
    $bucket[$data[$i]['id']] = $data[$i];
}

for ($x = 0; $x < count($list['data']['stage'][0]['days']); $x++) {
    $list_min = $list['data']['stage'][0]['days'][$x]['tasks'][0]['task_detail'];
    $title = $list['data']['stage'][0]['days'][$x]['title'];


    $answer = '';

    for ($i = 0; $i < count($list_min); $i++) {
        if ($bucket[$list_min[$i]['content_id']]) {
            $answer .= ($i + 1) . "\n";
            $answer .= "题目:" . $bucket[$list_min[$i]['content_id']]['subject'] . "\n";
            $answer .= "答案:" . $bucket[$list_min[$i]['content_id']]['answer'];
            $answer .= "\n\n";
        }
    }

    var_dump($answer);
    file_put_contents($title . ".txt", $answer);
}