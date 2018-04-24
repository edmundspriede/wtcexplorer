<?php
require("../config.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_init.inc");

$nName = getString('name');
$nType = getString('type');

if (empty($nName)) die('no file name specified');
if (empty($nType)) die('no file type specified');

$sFileName = CMS_UPLOADPATH.$nName;
if (!file_exists($sFileName)) die('ERROR: file is missing '.$sFileName);

if ($file = fopen($sFileName,"r"))
{
	header("Cache-Control: private\n");
	header("Pragma: private\n");		
	header("Content-Type: application/octet-stream\n"); 
	header("Content-Length: ".filesize($sFileName)."\n");
        
        if ($nType == '1cxml')
            header("Content-Disposition: attachment; filename=\""."1C-".date('d-m-Y_H-i').".xml"."\"\n");
        else      
	    header("Content-Disposition: attachment; filename=\"".$nName.".".$nType."\"\n");
	header("Content-Transfer-Encoding: binary\n");
	header("Content-Description: Compressed folder\n" );
	fpassthru($file);
}
else die("ERROR: file is corrupt");
?>