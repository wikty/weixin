<?php
function fetchCuisine(){
    $url = 'http://apis.juhe.cn/cook/category';
    $appkey = 'key=76ea9927c15a9f8f04cf8fc4cf9e0712';
    $parentid = 'parentid=10002';
    $querystring = $appkey."&".$parentid;
    $json = file_get_contents($url."?".$querystring);
    $data = json_decode($json, true);
    $result = array();
    foreach($data['result'][0]['list'] as $item){
        array_push($result, array($item['name'] => $item['id']));
    }
    file_put_contents("cuisine.json", json_encode($result));
}
?>
