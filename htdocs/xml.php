<? 
//XML datu nolasiishanai no db
require("../config.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_init.inc");
//require(CMS_LIBPATH.CMS_SYSPATH."s_session.inc");

global $sDirectXML;

$nModuleId = getNumber("mod");
$cBodyFile = "";

if (s_module('S',array('id'=>$nModuleId),$aMod))
	$cBodyFile = $aMod[0]["scriptname"];

//* CHECKS MODULE *
if (!file_exists(CMS_SITEPATH.$cBodyFile))
	halt_error("Sistēmas kļūda. Nav iespējams ielādēt pieprasīto moduli.");

include CMS_SITEPATH.$cBodyFile;

if (!empty($sDirectXML))
{
	header('Content-Type: text/xml');
	print ("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
	print ($sDirectXML);
}


?>