<?php

//set session timeout
ini_set('session.gc_maxlifetime', SP_SESSION_TIMEOUT * 60);
session_set_cookie_params(SP_SESSION_TIMEOUT * 60);

session_start();
$_sess_name = session_name();
$_sess_id = session_id();

// Update cookie ;)
setcookie($_sess_name, $_sess_id, time() + SP_SESSION_TIMEOUT * 60);


//$rRes = mysql_get('SELECT session_id from s_session WHERE last_activity<=SUBTIME(now(),"00:'.SP_SESSION_TIMEOUT.':00")');
//if ($rRes->num_rows > 0)
//{
//	$bReset = false;
//	$aResult = fetchData($rRes,'','');
//	foreach($aResult as $k=>$aRow)
//		s_system_logout("Time-out", $aRow['session_id']);
//}

//sesijas datu paarbaude
if (!isset($_SESSION["user_id"])) 
	s_system_logout(get_msg_text(3012));

$rIpCheck=mysql_get("select user_id from s_session WHERE session_id='".session_id()."' and ip='".getenv("REMOTE_ADDR")."' and x_ip='".getenv("HTTP_X_FORWARDED_FOR")."'");
if ($rIpCheck->num_rows!=1)	s_system_logout(get_msg_text(3014));

//Lietotaaja peedeejaas darbiibas laika saglabaashana
mysql_get("update s_session set last_activity=now() WHERE session_id='".session_id()."'");


// noklusētais modulis (id=3)
if (s_user_config("S",array("user_id"=>$_SESSION['user_id'], "key_id"=>S_USER_KEY_MODULE),$aConfig) && !empty($aConfig[0]['value']) && is_numeric($aConfig[0]['value']) && s_user_perm("S",array("module_id"=>$aConfig[0]['value']),$aRes))
	$nDefaultId = $aConfig[0]['value'];
else
	$nDefaultId = 450;

$nModuleId = modcode2id(getString('mod',array('default'=>$nDefaultId)));

if ($nModuleId === false || !s_user_perm("S",array("module_id"=>$nModuleId),$nPermType))
{
    halt_error(get_msg_text(3052)." <a href='/'>".get_msg_text(3300)."</a>");  
	exit;
}
else
	$_SESSION["perm_type"]=$nPermType;
