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
        $result[$item['name']] = $item['id'];
    }
    file_put_contents("cuisines.json", json_encode($result));
}

function fetchTags(){
    $url = 'http://apis.juhe.cn/cook/category';
    $appkey = 'key=76ea9927c15a9f8f04cf8fc4cf9e0712';
    $querystring = $appkey;
    $json = file_get_contents($url.'?'.$querystring);
    $data = json_decode($json, true);
    $result = array();
    if(empty($data['result'])) return $result;
    foreach($data['result'] as $parentitem){
        foreach($parentitem['list'] as $item){
            $result[$item['name']] = $item['id'];
        }
    }
    file_put_contents('tags.json', json_encode($result));
}


// 写了这个获取二维码图片的函数，
// 到最后却发现订阅号不能索要二维码，
// 系统仅仅在账户中为我提供了一个二维码图片将就者用吧
function fetchTicket($sceneId, $access_token){// my first quick response code $sceneId = 1
    $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.urlencode($access_token); // TOKEN
    $json = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$sceneId.'}}}';

    $response = postjson($url, $json); // postjson define in this page
    $result = json_decode($response);
    return $result['ticket'];
}

function fetchQR($ticket){
    $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
    $response = myrequest($url, array(), array(), 'GET', 'https');
    file_put_contents('QR.jpg', $response);
}

function nsprintf($format, $argsarray){
    foreach($argsarray as $key=>$value){
        $format = preg_replace('/{{\s*'.$key.'\s*}}/', $value, $format);
    }
    return $format;
}

function myrequest($url, $header=array(), $data=array(), $method='GET', $protocol='http'){
  // $url should be like this: http://baidu.com
  // $header and $data should be assoc array
    $data = http_build_query($data);
  
    $headerStr = '';
    foreach($header as $key=>$value){
        $headerStr .= $key.': '.$value."\r\n";
    }
  
    $options = array(
        $protocol => array(
            'method' => $method,
            'header' => $headerStr,
            'content' => $data,
            'timeout' => 15*60
            )
        );
    $context = stream_context_create($options);
  
    $response = file_get_contents($url, false, $context);
    return $response;
}

function postjson($url, $jsonStr, $accept='json'){
    // $url like this: http://localhost/test.php
    // $jsonStr is json string(json_encode(assoc) can generate it)
    // $accept is the except response format
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: '.mb_strlen($jsonStr),
        'Accept: '.$accept
        ));
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;

}

?>
