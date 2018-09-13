<?php
// If the tags.json can be used, this function don't need
function fetchtags($parentId){
    if(empty($parentId)) $parentId = '';
    $url = 'http://apis.juhe.cn/cook/category';
    $appkey = 'key=76ea9927c15a9f8f04cf8fc4cf9e0712';
    $querystring = $appkey.'&parentid='.$parentId;
    $json = file_get_contents($url.'?'.$querystring);
    $data = json_decode($json, true);
    $result = array();
    if(empty($data['result'])) return $result;
    foreach($data['result'] as $parentitem){
        foreach($parentitem['list'] as $item){
            $result[$item['name']] = $item['id'];
        }
    }
    return $result;
}

function fetchtag($tagId){
    $url = 'http://apis.juhe.cn/cook/index';
    $appkey = 'key=76ea9927c15a9f8f04cf8fc4cf9e0712';
    $querystring = $appkey.'&cid='.$tagId;
    $json = file_get_contents($url.'?'.$querystring);
    $data = json_decode($json, true);
    $result = array();
    if(empty($data['result'])) return $result;
    foreach($data['result']['data'] as $item){
        array_push($result, array('title' => $item['title'], 'id' => $item['id']));
    }
    return $result;
}

// If the cuisines.json can be used, this function don't need
function fetchcuisines(){
    return fetchtags(10002);
}

function fetchcuisine($cuisineId){
    return fetchtag($cuisineId);
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

function fetchcookbooks($keyword){
    $url = 'http://apis.juhe.cn/cook/query.php';
    $appkey = 'key=76ea9927c15a9f8f04cf8fc4cf9e0712';
    $querystring = $appkey.'&menu='.urlencode($keyword);
    $json = file_get_contents($url.'?'.$querystring);
    $data = json_decode($json, true);
    $result = array();
    if(empty($data['result'])) return $result;
    foreach($data['result']['data'] as $item){
        array_push($result, array('title' => $item['title'], 'id' => $item['id']));
    }
    return $result;
}
?>
