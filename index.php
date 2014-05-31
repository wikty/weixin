<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "xiaowenbin_999");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();
$wechatObj->responseMsg();



class wechatCallbackapiTest
{
	public function valid()
    {
        // For first time, weixin server GET request
        // has querystring: signature, nonce, timestamp, echostr
        // To become a developer, if valid signature is ok
        // you should echo the echostr
        
        // $echoStr = $_GET["echostr"];
        // //valid signature , option
        // if($this->checkSignature()){
        // 	 echo $echoStr; // just for first initial
        // 	 exit;

        // }

        
        // Now you are a developer, weixin server GET
        // request querystring: signature, nonce, timestamp
        // so you can valid signature too.

        if($this->checkSignature()){
            // the request can be trusted from weixin server
            // now you can process the request
            echo '';
        }
        else{
            // refuse requestes that not come form  wexin server
            echo 'This server is only accepted requests come from weixin';
            exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "你好！每天一菜现处于开发阶段，码农正在流汗，很快就能使用。";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "你好！每天一菜现处于开发阶段，码农正在流汗，很快就能使用。";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>
