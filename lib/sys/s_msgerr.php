<?
$GLOBALS['RESULT_CODES']=array();
$GLOBALS['RESULT_CODES']['SAVE_SUCCESS']=array('text'=>get_msg_text(2000)); 

$GLOBALS['ERROR_CODES']=array();
$GLOBALS['ERROR_CODES']['NOINPUT']=array('text'=>get_msg_text(1010));
$GLOBALS['ERROR_CODES']['NOTNUMERIC']=array('text'=>get_msg_text(1100));
$GLOBALS['ERROR_CODES']['TOOSMALL']=array('text'=>get_msg_text(1110));
$GLOBALS['ERROR_CODES']['TOOBIG']=array('text'=>get_msg_text(1120));
$GLOBALS['ERROR_CODES']['DB_ERR']=array('text'=>get_msg_text(1200));
$GLOBALS['ERROR_CODES']['DB_DENIED']=array('text'=>get_msg_text(1300));
$GLOBALS['ERROR_CODES']['USER_INPUT_ERR']=array('text'=>get_msg_text(1000));

function addMessage($aParam)
{
	global $aMessages;

	$err=array();
	$cField=!empty($aParam["fieldname"])?$aParam["fieldname"]:"";
	$aParam["type"]=!empty($aParam["type"])?$aParam["type"]:2;
	

	if(!empty($aParam["code"]))
	{
		switch($aParam["type"])
		{
			//FAILURE
			case 2:
				$cErrorText=$GLOBALS['ERROR_CODES'][$aParam["code"]]["text"];
			break;
			//SUCCESS
			case 1:
				$cErrorText=$GLOBALS['RESULT_CODES'][$aParam["code"]]["text"];
			break;
			default:
				$cErrorText="nekorekti parametri";
			
		}
		$err=array('msg'=>$cErrorText,'fieldname'=>$cField);
	}
	
	if(!empty($aParam["msg"]))
	{
		$err=array('msg'=>$aParam["msg"],'fieldname'=>$cField);	
	}

	if(!empty($aParam["fieldid"]))
	{
		$err["fieldid"]=$aParam["fieldid"];	
	}

	if(!empty($aParam["type"]))
	{
		$err["type"]=$aParam["type"];	
	}

	$aMessages['MSG'][]=$err;
}

function getMessages(&$cMsgType)
{
	global $aMessages;
	if(hasMessages())
	{
		$aErr=$aMessages['MSG'];
		$cError="";

		foreach($aErr as $k=>$a)
		{
			$cTmp="";
			
			if($a["type"]==1)
			{
				$cTmp.="Success! ";
				$cMsgType=1;
			}
			else
			{
				if(!empty($a["fieldname"]))
					$cTmp.=" <b>".$a["fieldname"]."</b> ";
				$cMsgType=2;
			}

			$cTmp.=$a["msg"]." <br>";

			//if such a error not exists
			if(!strstr($cError, $cTmp))
				$cError.=$cTmp;
		}

		clearMessages();
		return $cError;
	}
	return false;

}

function getErrorFieldIds()
{
	global $aMessages;
	$aErrorIdes=array();
	if(hasMessages())
	{
		$aErr=$aMessages['MSG'];

		foreach($aErr as $k=>$a)
		{
			if(!empty($a["fieldid"]))
				$aErrorIdes[]=array("id"=>$a["fieldid"]);
		}
		
		return $aErrorIdes;
	}
	return '';
}

function hasMessages()
{
	global $aMessages;
	if (isset($aMessages['MSG']))
	{
		return count($aMessages['MSG']);
	}
	else return false;
}

function clearMessages()
{
	global $aMessages;
	unset($aMessages['MSG']);
}

function setMessages()
{
	global $oDirectHTML;
	global $oHead;

	$oMsg=new vlibTemplate(CMS_SYSPATH."_msg.html");
	
	/* get messages and error field ids*/
	$aErroredFields=getErrorFieldIds();
	$cMSG=getMessages($a);
	$oMsg->setVar("MSG",$cMSG);
	$cContent=$oMsg->grab();

	if(isset($oHead))
	{
		$oHead->setVar("MSG",$cContent);
		$oHead->setLoop("error_ids", $aErroredFields);
	}

	if(isset($oDirectHTML))
	{
		$oDirectHTML->setVar("MSG",$cContent);
		$oDirectHTML->setLoop("error_ids", $aErroredFields);
	}

}

function addRule($sFieldId,$sType)
// for .js check input data
{
	global $aValidationRules;
	$aValidationRules[] = array('id'=>$sFieldId,'type'=>$sType);
}

function get_msg_text($sCode=false)
{
	global $aMsgText;
	if($sCode && !empty($aMsgText[$sCode])) 
		return $aMsgText[$sCode];
	return ' ';
}

?>