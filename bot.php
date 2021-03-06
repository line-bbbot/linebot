<?php
	/* DATA :: INPUT from LINE-app */
	
		$INPUTContent	= file_get_contents('php://input');
		$INPUTJSON 		= json_decode($INPUTContent,true);
		$INPUTMsg		= $INPUTJSON['events'][0]['message']['text'];
	
	/* DATA :: LINE Section */
	
		$LINEMsgID				= "";
		$LINEChannelAccessToken	= "1240gB1PZXyeYmLiXj/EXQ0lzrkqe6O+D3JGwz5GVw4m544xt1bP/s1XXCfQeUnsuiY3R+7EGn0xfE6Saow6c+pH6xUVy56ee1BXJqaqiKVMZvCXPt4iybhIo73CE8SFmGtSuu9Z3EpCZxBZEokveQdB04t89/1O/w1cDnyilFU=
";
		$LINEURLMain			= "https://api.line.me/v2/bot/message";
		$LINEURLByMsgType		= array(
									"POSTReply"		=> $LINEURLMain."/reply",
									"POSTPush" 		=> $LINEURLMain."/push",
									"POSTMulticast" => $LINEURLMain."/multicast",
									"GETContent"	=> $LINEURLMain."/".$LINEMsgID."/content"
								  );

	/* DATA and OPERATION :: Database Section */
	
		$DBAPIKey		= "gEd3E2gniu_jJsug_KdfopukhrMghFyC";
		$DBQueryString	= '&q={"request":"'.$INPUTMsg.'"}';
		$DBName			= "duckduck";
		$DBUsername		= "linebot2";
		$DBURLMain		= "https://api.mlab.com/api/1/databases/".$DBName."/collections/".$DBUsername."?apiKey=".$DBAPIKey;
		$DBURLQuery		= $DBURLMain.$DBQueryString;
		
		$DBQuery		= file_get_contents($DBURLQuery);
		$DBJSON			= json_decode($DBQuery);
		$DBDataSize		= sizeof($DBJSON);
	
	/* DATA and OPERATION :: Send value(s) to Database and/or reply back to LINE-app Section */
	
		$SVCmdType	= array(
						"*สอน*"
					  );
		$SVCmdSign	= array(
						"[",
						"]"
					  );
		
		$SVMethod	= array("POST","GET");
		$SVHeader	= array(
						"Content-Type: application/json",
						"Authorization: Bearer {".$LINEChannelAccessToken."}"
					  );
					  
		if(strpos($INPUTMsg,$SVCmdType[0]) !== false){
			if(strpos($INPUTMsg,$SVCmdType[0]) !== false){
				$SVValueExtraction	= explode("|",str_replace($SVCmdSign,"",str_replace($SVCmdType[0],"",$INPUTMsg)));
				$SVRequest			= $SVValueExtraction[0];
				$SVReply			= $SVValueExtraction[1];
				
				$SVJSON				= json_encode(
										array(
											"request"	=> $SVRequest,
											"reply"		=> $SVReply
										)
									  );
				$SVOption			= array(
										"http"	=> array(
													"method"	=> $SVMethod[0],
													"header"	=> $SVHeader[0],
													"content"	=> $SVJSON
												   )
									  );
				
				$SVContent 			= stream_context_create($SVOption);
				file_get_contents($DBURLMain,false,$SVContent);
				
				$SVPOSTValue['replyToken']			= $INPUTJSON['events'][0]['replyToken'];
				$SVPOSTValue['messages'][0]['type']	= "text";
				$SVPOSTValue['messages'][0]['text']	= "ขอบคุณสำหรับการสอนคร๊าบบบ";
				
				$LINEURLFinal	= $LINEURLByMsgType['POSTReply'];
			}
		}else{
			if($DBDataSize > 0){
				foreach($DBJSON as $SVReply){
					$SVPOSTValue['replyToken']			= $INPUTJSON['events'][0]['replyToken'];
					$SVPOSTValue['messages'][0]['type']	= "text";
					$SVPOSTValue['messages'][0]['text']	= $SVReply->reply;
				}
			}else{
				$SVPOSTValue['replyToken']			= $INPUTJSON['events'][0]['replyToken'];
				$SVPOSTValue['messages'][0]['type']	= "text";
				$SVPOSTValue['messages'][0]['text']	= "แง แง แง, สอนผมหน่อย สอนผมหน่อย ผมจะได้รู้เรื่อง, นะ นะ นะคร๊าบบบ. สอนผมแบบนี้นะ : *สอน*[คำสอน|คำตอบ]";
			}
			
			$LINEURLFinal	= $LINEURLByMsgType['POSTReply'];
		}
		
		$SVCURL			= curl_init();
			curl_setopt($SVCURL,CURLOPT_URL,$LINEURLFinal);
			curl_setopt($SVCURL,CURLOPT_HEADER,false);
			curl_setopt($SVCURL,CURLOPT_POST,true);
			curl_setopt($SVCURL,CURLOPT_HTTPHEADER,$SVHeader);
			curl_setopt($SVCURL,CURLOPT_POSTFIELDS,json_encode($SVPOSTValue));
			curl_setopt($SVCURL,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($SVCURL,CURLOPT_SSL_VERIFYPEER,false);
		$SVCURLResult	= curl_exec($SVCURL);
		curl_close($SVCURL);
		
		//echo "RESULT : ".$SVCURLResult;
		//echo "<br>";
		echo "INPUTContent : ";
		print_r($INPUTContent);
		echo "<br>";
		echo "SVPOSTValue : ";
		print_r($SVPOSTValue);
?>
