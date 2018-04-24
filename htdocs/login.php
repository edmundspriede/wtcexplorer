<?php
require("../config.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_init.inc");

$cLogin = getString('login');
$cPassword = MD5(getString('password'));
$cSubmit = getString('submit_bttn');
$nLogout = getString('logout');
$nErrorCode = getNumber('error',array('default'=>""));

if ($nLogout==1)
{
	if(!s_is_session()) session_start();
	s_logout();
	$nErrorCode=5;
}

$cIpAddr=getenv("HTTP_X_FORWARDED_FOR")?getenv("HTTP_X_FORWARDED_FOR"):getenv("REMOTE_ADDR");

if ($cSubmit)
{
	if(s_login($cLogin, $cPassword))
		 header("Location:/");
	else header("Location:/login.php?error=1");
	exit;
}

session_start();

if (isset($_SESSION["username"])) 
{
	header("Location:/");exit;
}

$oBody=new vlibTemplate(CMS_SYSPATH."_login.html");

switch ($nErrorCode)
{
	case 1:
		$oBody->setVar("title",get_msg_text(3000));
		$oBody->setVar("text",get_msg_text(3100));
		$aButtons=array();
		$aButtons[]=array("text"=>get_msg_text(3300),"onclick"=>"document.location='/login.php';");
		$oBody->SetLoop("buttons",$aButtons);
		break;
	case 2:
		$oBody->setVar("title",get_msg_text(3010));
		$oBody->setVar("text",get_msg_text(3110));
		$aButtons=array();
		$aButtons[]=array("text"=>get_msg_text(3300),"onclick"=>"document.location='/login.php';");
		$oBody->SetLoop("buttons",$aButtons);
		break;
	case 3:
		$oBody->setVar("title",get_msg_text(3020));
		$oBody->setVar("text",get_msg_text(3120));
		$aButtons=array();
		$aButtons[]=array("text"=>get_msg_text(3300),"onclick"=>"document.location='/login.php';");
		$oBody->SetLoop("buttons",$aButtons);
		break;
	case 4:
		$oBody->setVar("title",get_msg_text(3030));
		$oBody->setVar("text", get_msg_text(3130));
		$aButtons=array();
		$aButtons[]=array("text"=>get_msg_text(3300),"onclick"=>"document.location='/login.php';");
		$oBody->SetLoop("buttons",$aButtons);
		break;
	case 5:
		$oBody->setVar("title",get_msg_text(3040));
		$oBody->setVar("text",get_msg_text(3140));
		$aButtons=array();
		$aButtons[]=array("text"=>get_msg_text(3300),"onclick"=>"document.location='/login.php';");
		$oBody->SetLoop("buttons",$aButtons);
		break;
	case 6:
		$oBody->setVar("title",get_msg_text(3050));
		$oBody->setVar("text",get_msg_text(3150));
		$aButtons=array();
		$aButtons[]=array("text"=>get_msg_text(3300),"onclick"=>"document.location='/login.php';");
		$oBody->SetLoop("buttons",$aButtons);
		break;

}


$oBody->setVar("logo", sysconfig_value("company_logo_filename"));
$oBody->setVar("error_code",$nErrorCode);
$oBody->pparse();
?>