<?php
/**
  * wechat php test
  */

// depend module
include('./fetchdata.php');

// depend function
function nsprintf($format, $argsarray){
    foreach($argsarray as $key=>$value){
        $format = preg_replace('/{{\s*'.$key.'\s*}}/', $value, $format);
    }
    return $format;
}

//define your token
define("TOKEN", "xiaowenbin_999");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();
$wechatObj->responseMsg();



class wechatCallbackapiTest
{
    private $helpText = '';
    private $helpCommands = array();
    private $feedbackText = '';
    
    public function __construct(){
        $this->helpText = "目前平台功能如下：".
                        "\n"."【0】 平台正在测试阶段，现在仅支持帮助功能和输入一串数字来返回一个菜谱".
                        "\n"."【1】 输入食材名查找菜谱，如输入：酸奶食材，韭菜食材，菠萝食材".
                        "\n"."【2】 输入完整菜名或菜名的一部分，如输入：韭菜炒蛋皮菜名，果茶菜名".
                        "\n"."【3】 输入地域菜系名，如输入：日本料理菜系，法国菜菜系，赣菜菜系".
                        "\n"."【4】 输入标签查询所属的菜谱，如输入：功效标签，人群标签，疾病标签".
                        "\n"."【5】 帮助命令：帮助，help，h";
        $this->helpCommands = array('help', 'h', '帮助');
        $this->feedbackText = '{{ prefix }}【{{ title }}】{{ suffix }}{{ content }}{{ tips }}';
    }

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

            if(in_array($keyword, $this->helpCommands)){                            // help
                $contentStr = $this->helpText;
            }
            elseif(filter_var($keyword, FILTER_VALIDATE_INT)){                      // cookbook
                $cookbook = fetchcookbook($keyword); // fetch cookbook by id
                if(!empty($cookbook)){
                    // no "\n".'简介： '.$cookbook['imtro'].
                    // no ['steps']['img']
                    $contentStr = '【菜谱】'.$cookbook['title'].
                             "\n".'【编号】'.$cookbook['id'].
                             "\n".'【标签】'.$cookbook['tags'].
                             "\n".'【主料】'.$cookbook['ingredients'].
                             "\n".'【辅料】'.$cookbook['burden'].
                             "\n".'【步骤】';
                    foreach($cookbook['steps'] as $step){
                        $contentStr .= "\n".$step['step'];
                    }
                }
                else{
                    $contentStr = '系统中没有编号【'.$keyword.'】的菜谱，出现该状况的原因有：'.
                            "\n".'【1】可能实际输入的编号跟你期望的输入不一样，请仔细【核对编号】'.
                            "\n".'【2】如果你是【新手】在无意中输入了一串数字，那么现在你应该知道输入数字意味着查询特定编号的菜单，具体方法请输入“帮助”，“help”，进行查询'.
                            "\n".'【3】如果你确认自己输入的【编号无误】，那应该是我们的数据变动了，如果你愿意帮助我们提升服务质量，您可以给我发邮件：xiaowenbin_999@163.com';
                }
            }
            else{                                       // other options: dishes, food, cuisine, tag, ...
                $prefixStr = mb_substr($keyword, 0, -2, "UTF-8"); // query word
                $suffixStr = mb_substr($keyword, -2, 2, "UTF-8"); // query key
                switch($suffixStr){
                    case "菜名":
                    case "食材":
                        $contentStr = $suffixStr;
                        // $contentStr = '';
                        // $cookbooks = fetchcookbooks($prefixStr);
                        // if(!empty($cookbooks)){
                        //     $content = '';
                        //     foreach($cookbooks as $cookbook){
                        //         $content .= "\n".$cookbook['title'].'（编号：'.$cookbook['id'].'）';
                        //     }
                        //     $contentStr = nsprintf($this->feedbackText, array(
                        //         'prefix' => '',
                        //         'title' => $prefixStr,
                        //         'suffix' => '的菜谱有：',
                        //         'content' => $content,
                        //         'tips' => "\n".'【温馨提示】查询菜谱请输入相应的编号。'
                        //     ));
                        // }
                        // if(empty($contentStr)){
                        //     if($suffixStr == '菜名'){
                        //         $contentStr = nsprintf($this->feedbackText, array(
                        //             'prefix' => 'Sorry，系统中没有菜谱',
                        //             'title' => $prefixStr,
                        //             'suffix' => '',
                        //             'content' => "\n".'或者你应将该菜名换成习惯的称呼',
                        //             'tips' => "\n".'请试试别的。'
                        //         ));
                        //     }
                        //     else{// is 食材
                        //         $contentStr = nsprintf($this->feedbackText, array(
                        //             'prefix' => 'Sorry，系统中没有以',
                        //             'title' => $prefixStr,
                        //             'suffix' => '为食材的菜谱',
                        //             'content' => '',
                        //             'tips' => "\n".'请试试别的。'
                        //         ));
                        //     }
                        // }
                        break;
                    case "菜系":
                        $contentStr = $suffixStr;

                        // $json = file_get_contents('./cuisines.json');
                        // $cuisines = json_decode($json, true);
                        // $contentStr = '';
                        // if(array_key_exists($prefixStr, $cuisines)){
                        //     $cuisineId = $cuisines[$prefixStr];
                        //     $cookbooks = fetchcuisine($cuisineId);
                        //     if(!empty($cookbooks)){
                        //         $content = '';
                        //         foreach($cookbooks as $cookbook){
                        //             $content .= "\n".$cookbook['title'].'（编号：'.$cookbook['id'].'）';
                        //         }
                        //         $contentStr = nsprintf($this->feedbackText, array(
                        //             'prefix' => '',
                        //             'title' => $prefixStr,
                        //             'suffix' => '下的菜谱有：',
                        //             'content' => $content,
                        //             'tips' => "\n".'【温馨提示】查询菜谱请输入相应的编号。'
                        //         ));
                        //     }
                        //  }
                         // if(empty($contentStr)){
                         //    $contentStr = nsprintf($this->feedbackText, array(
                         //        'prefix' => 'Sorry，系统中没有你要查找的菜系名',
                         //        'title' => $prefixStr,
                         //        'suffix' => '',
                         //        'content' => '',
                         //        'tips' => "\n".'请试试别的。'
                         //    ));
                         // }
                        break;
                    case "标签":
                        $contentStr = $suffixStr;

                        // $json = file_get_contents('./tags.json');
                        // $tags = json_decode($json, true);
                        // $contentStr = '';
                        // if(array_key_exists($prefixStr, $tags)){
                        //     $tagId = $tag[$prefixStr];
                        //     $cookbooks = fetchtag($tagId);
                        //     echo '';
                        //     if(!empty($cookbooks)){
                        //         $content = '';
                        //         foreach($cookbooks as $cookbook){
                        //             $content .= "\n".$cookbook['title'].'（编号：'.$cookbook['id'].'）';
                        //         }
                        //         $contentStr = nsprintf($this->feedbackText, array(
                        //             'prefix' => '',
                        //             'title' => $prefixStr,
                        //             'suffix' => '标签下的菜单有：',
                        //             'content' => $content,
                        //             'tips' => "\n".'【温馨提示】查询菜谱请输入相应的编号。'
                        //         ));
                        //     }
                        // }
                        // if(empty($contentStr)){
                        //     $contentStr = nsprintf($this->feedbackText, array(
                        //         'prefix' => 'Sorry，系统中没有你要查找的标签',
                        //         'title' => $prefixStr,
                        //         'suffix' => '',
                        //         'content' => '',
                        //         'tips' => "\n".'请试试别的。'
                        //     ));
                        // }
                        break;
                    default:// default is $keyword as 菜名
                        $contentStr = $keyword;
                        // $contentStr = '';
                        // $cookbooks = fetchcookbooks($keyword);
                        // if(!empty($cookbooks)){
                        //     $content = '';
                        //     foreach($cookbooks as $cookbook){
                        //         $content .= "\n".$cookbook['title'].'（编号：'.$cookbook['id'].'）';
                        //     }
                        //     $contentStr = nsprintf($this->feedbackText, array(
                        //         'prefix' => '',
                        //         'title' => $keyword,
                        //         'suffix' => '的菜谱有：',
                        //         'content' => $content,
                        //         'tips' => "\n".'【温馨提示】查询菜谱请输入相应的编号。'
                        //     ));
                        // }
                        // if(empty($contentStr)){
                        //     $contentStr = nsprintf($this->feedbackText, array(
                        //         'prefix' => 'Sorry，系统中没有菜谱',
                        //         'title' => $keyword,
                        //         'suffix' => '',
                        //         'content' => "\n".'或者你应将该菜名换成习惯的称呼',
                        //         'tips' => "\n".'请试试别的。'
                        //     ));
                        // }
                        break;
                }
            }
            
            $resultStr = $this->generateTextResponse($postObj, $contentStr);
            return $resultStr;
        }
        else{
            $contentStr = "不在沉默中爆发就在沉默中死亡";
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
                $this->helpText.
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
