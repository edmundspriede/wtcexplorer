<?
require("../config.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_init.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_session.inc");

if (!s_module('S',array('id'=>$nModuleId),$aMod))
	halt_error("Sistēmas kļūda. Nav iespējams ielādēt pieprasīto moduli.");

if (!file_exists(CMS_SITEPATH.$aMod[0]['scriptname']))
	halt_error("Sistēmas kļūda. Nav iespējams ielādēt pieprasīto moduli.");

if (!file_exists(CMS_BASETPLPATH.CMS_SYSPATH.'_print_body.html'))
	halt_error("Sistēmas kļūda. Nav iespējams ielādēt pieprasīto moduli.");

$oBody = new vlibTemplate(CMS_SYSPATH."_print_body.html");

$oBody->SetVar('module_name',$aMod[0]['name']);

global $aVlib;
global $aCSS;

include CMS_SITEPATH.$aMod[0]['scriptname'];

$sContent = '';

if (isset($aVlib))
	foreach ($aVlib as $k => $v)
	{
		if (isset($_GET['debug']))	print_r($v);
		$sContent .= $v->grab();
	}

if (is_array($aCSS)) $oBody->SetLoop('css_list',$aCSS);

$oBody->SetVar('template_content',$sContent);

$oBody->pparse();
?>