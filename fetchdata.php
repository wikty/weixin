<?php
// If the cuisines.json can be used, this function don't need
function fetchcuisines(){
    $url = 'http://apis.juhe.cn/cook/category';
    $appkey = 'key=76ea9927c15a9f8f04cf8fc4cf9e0712';
    $parentid = 'parentid=10002';
    $querystring = $appkey."&".$parentid;
    $json = file_get_contents($url."?".$querystring);
    $data = json_decode($json, true);
    $result = array();
    if(empty($data)) return $result;
    foreach($data['result'][0]['list'] as $item){
        $result[$item['name']] = $item['id'];
    }
    return $result;
}

function fetchcuisine($cuisineId){
    $url = 'http://apis.juhe.cn/cook/index';
    $appkey = 'key=76ea9927c15a9f8f04cf8fc4cf9e0712';
    $querystring = $appkey.'&cid='.$cuisineId;
    $json = file_get_contents($url.'?'.$querystring);
    $data = json_decode($json, true);
    $result = array();
    if(empty($data['result'])) return $result;
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
