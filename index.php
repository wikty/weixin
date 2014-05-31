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
                
                // handle various MsgType
                switch($postObj->MsgType){
                    case "text":
                        $resultStr = $this->handleText($postObj);
                        break;
                    case "event":
                        $resultStr = $this->handleEvent($postObj);
                        break;
                }
                echo $resultStr;

        }else {
        	echo "";
        	exit;
        }
    }

    public function handleText($postObj){
        $keyword = trim($postObj->Content);
        if(!empty($keyword)){
/////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////should parse $keyword to logical
            $contentStr = "你好！每天一菜现处于开发阶段，码农正在流汗，很快就能使用。";
            $resultStr = $this->generateTextResponse($postObj, $contentStr);
            return $resultStr;
        }
        else{
            $contentStr = "你好！每天一菜现处于开发阶段，码农正在流汗，很快就能使用。";
            $resultStr = $this->generateTextResponse($postObj, $contentStr);
            return $resultStr;
        }
    }

    public function handleEvent($postObj){
        $contentStr = "";
        // handle various event
        switch($postObj->Event){
            case "subscribe":
                $contentStr = "感谢您关注【每天一菜】".
                "\n"."微信号：dailycookbook".
                "\n"."我们致力于为您提供一本全面，方便易用的百科式菜谱，每天一道菜让你成为巧厨能手。".
                "\n"."目前平台功能如下：".
                "\n"."【1】 输入食材名查找菜谱，如输入：酸奶，韭菜，菠萝".
                "\n"."【2】 输入完整菜名或菜名的一部分，如输入：".
                "\n"."【3】 输入地域菜系名，如输入：日本料理，法国菜，赣菜".
                "\n"."更多内容，敬请期待...";
                break;
            case "unsubscribe":
                $contentStr = "您的离开让我很忧伤！";
                break;
            default:
                $contentStr = "Unknow Event: ".$postObj->Event;
                break;
        }
        $resultStr = $this->generateTextResponse($postObj, $contentStr);
        return $resultStr;
    }

    public function generateTextResponse($postObj, $contentStr, $flag=0){
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>";
        $msgType = 'text';
        $time = time();
        $resultStr = sprintf($textTpl, $postObj->FromUserName, 
            $postObj->ToUserName, $time, $msgType, $contentStr, $flag);
        return $resultStr;

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
