<?
Define("S_SESSION_LOGIN", 1);
Define("S_SESSION_LOGOUT", 2);
Define("S_SESSION_LOGOUT_SYSTEM", 3);

function start_user_session($aParams)
{
	session_start();
	if (!session_id()) session_id(uniqid('kvs'));


	$nIpLong = ip2ulong(getenv("REMOTE_ADDR"));
	//parbauda ip
/*	if(!empty($nIpLong ) && ! s_valid_ip("S",array("where"=>"ip.from_address <= '".$nIpLong."' and ip.to_address >= '".$nIpLong."'"),$aRes))
	{
		header("Location:/login.php?error=6");
		exit;
	}
*/
	$_SESSION['username'] = $aParams['login'];
	$_SESSION['user_id'] = $aParams['id'];
//	$_SESSION['susr_name'] = $aParams['name'];
	$_SESSION['role_id'] = $aParams['role_id'];

	mysql_get("insert into s_session(session_id,ip,x_ip,last_activity,user_id)".
		"values (".
			"'".session_id()."',".
			"'".getenv("REMOTE_ADDR")."',".
			"'".getenv("HTTP_X_FORWARDED_FOR")."',".
			"now(),".
			"'".$aParams["id"]."'".
		")");
	
	$_SESSION["db_id"]=Db::$mysqli->insert_id;

	if(s_user("S",array("id"=>$aParams["id"]),$aUser))
	{
		$_SESSION["office_id"]=$aUser[0]["primary_office_id"];
		//last login date, ip
		mysql_get("UPDATE s_user ".
				"SET last_login_date=now(), ".
				"last_login_ip='".$nIpLong."' ".
				"WHERE id='".$aUser[0]['id']."'");
	}

	$aSessionLog = array();
	$aSessionLog['user_id'] = $_SESSION['user_id'];
	$aSessionLog['session_id'] = session_id();
	$aSessionLog['ip'] = getenv("REMOTE_ADDR");
	$aSessionLog['x_ip'] = getenv("HTTP_X_FORWARDED_FOR");
	$aSessionLog['type_id'] = S_SESSION_LOGIN;

	s_session_log('I',$aSessionLog,$aRes);

/*	$nScstId = curUserCustomer($aParams['susr_id']);
	if ($nScstId) $_SESSION['scst_id'] = $nScstId;

	if (isset($aParams['set_cookie']))
	{
		//setcookie('cepums',md5($aUser['susr_email']).$aUser['susr_password'],time()+180,'',false,true);
		setcookie('cepums',md5($aParams['susr_email']).$aParams['susr_password'],time()+CMS_SESSION_EXPIRE_TIME);
	}
	else cookie_delete('cepums');

	$tNow = db_now();
	$aSession['sses_start'] = $tNow;
	$aSession['sses_end'] = db_expire();
	$aSession['sses_susr_id'] = $aParams['susr_id'];
	$aSession['sses_ip'] = getenv("HTTP_X_FORWARDED_FOR")?getenv("HTTP_X_FORWARDED_FOR"):getenv("REMOTE_ADDR");
	$aSession['sses_session_id'] = session_id();

	sses_session('I',$aSession,$aRes);

	
	$_SESSION['db_id'] = Db::$mysqli->insert_id;
	$_SESSION['session_id'] = $aSession['sses_session_id'];
*/
	return true;
}


function s_login($sLogin=false, $sPassword)
{
	if(!$sLogin) return false;
	$rRes = mysql_get("select u.* ".
				"FROM s_user u  ".
					"LEFT OUTER JOIN s_office i on u.primary_office_id = i.id  ".				
				"WHERE u.login = '".$sLogin."' ".
					"and u.password = '".$sPassword."' and u.STATUS=1 ");
	
//	$rRes = mysql_get("SELECT * FROM susr_user WHERE susr_status = 1 AND susr_login = '".$sLogin."' ".
//		"AND susr_password = MD5('".$sPassword."') ");
	if ($rRes->num_rows == 1)
		if ($aUser = $rRes->fetch_assoc())
		{
			//parbauda ip
//			$nIpLong = ip2ulong(getenv("REMOTE_ADDR"));
//			if(!empty($nIpLong ) && ! s_valid_ip("S",array("where"=>"ip.from_address <= '".$nIpLong."' and ip.to_address >= '".$nIpLong."'"),$aRes))
//			{
//				header("Location:/login.php?error=6");
//				exit;
//				return false;
//			}

			start_user_session($aUser);
			return true;
		}

//	global $oMessage;
//	$oMessage->addError('','Nekorekts lietotājs vai parole!');
	return false;
}

function s_logout($sText=false)
{
	$sSessionId=session_id();

	$nUserId=isset($_SESSION['user_id'])?$_SESSION['user_id']:'';

	$aLog = array(
		'user_id'	=> $nUserId, 
		'session_id'=> session_id(), 
		'ip' => getenv("REMOTE_ADDR"),
		'x_ip' => getenv("HTTP_X_FORWARDED_FOR"),
		'type_id'	=> S_SESSION_LOGOUT );
	if($sText)  $aLog['notes'] = $sText;

	s_session_log('I',$aLog,$aRes);
	mysql_get("DELETE FROM s_session WHERE session_id = '".$sSessionId."'");
	$_SESSION=array();
	session_unset();
	session_destroy();
	return true;
}

function s_system_logout($sText=false, $sSessionId=false)
{
	if(!$sSessionId) $sSessionId=session_id();
	$aLog = array(
		'session_id'=> $sSessionId, 
		'type_id'	=> S_SESSION_LOGOUT_SYSTEM);
	if($sText)  $aLog['notes'] = $sText;
	if(!empty($_SESSION['user_id']))   $aLog['user_id'] = $_SESSION['user_id'];

	if(empty($aLog['user_id']) && $sSessionId)
	{
		$rRes = mysql_get("select user_id from s_session where session_id='".$sSessionId."' ");
		if ($rRes->num_rows > 0 && $aRow = $rRes->fetch_assoc())
			$aLog['user_id'] = $aRow['user_id'];
	}

	s_session_log('I',$aLog,$aRes);
	mysql_get("DELETE FROM s_session WHERE session_id = '".$sSessionId."'");
	if($sSessionId==session_id())
	{
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			// ajax request
			http_response_code (401);
		}
		else {
			$_SESSION=array();
			session_unset();
			session_destroy();
			header("Location:/login.php");
		}
		exit;
	}
	return true;
}

function s_is_session()
{
	//return true;
	return (!empty($_SESSION['user_id']));
}


?>