<?php
function fetchcuisine($cuisineId){
    $url = 'http://apis.juhe.cn/cook/index';
    $appkey = 'key=76ea9927c15a9f8f04cf8fc4cf9e0712';
    $querystring = $appkey.'&cid='.$cuisineId;
    $json = file_get_contents($url.'?'.$querystring);
    $data = json_decode($json, true);
    $result = array();
    if(empty($data['result']) return $result;
    foreach($data['result']['data'] as $item){
        array_push($result, array($item['title'] => $item['id']));
    }
    return $result;
}

function fetchcookbook($cookbookId){
    $url = 'http://apis.juhe.cn/cook/queryid';
    $appkey = 'key=76ea9927c15a9f8f04cf8fc4cf9e0712';
    $querystring = $appkey.'&id='.$cookbookId;
    $json = file_get_contents($url.'?'.$querystring);
    $data = json_decode($json, true);
    $result = array();
    if(empty($data['result'])) return $result;
    $cookbook = $data['result']['data'][0];
    return $cookbook;
}

?>
