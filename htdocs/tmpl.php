<?
require("../config.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_init.inc");

$nId = getNumber('id'); if (empty($nId)) die('template file not specified');

$rRes = Db::$mysqli->query("SELECT IF(ti.tmpl_type = 6,'css','dat') ext, f.id file_id, f.filename FROM s_template_item ti INNER JOIN d_file f ".
	"ON f.referencing_id = ti.id AND f.reference_type = ti.tmpl_type WHERE ti.id = '".$nId."' LIMIT 0,1") or die('error 83610t');

if ($rRes->num_rows < 1) die("File does not exist");

if ($aRow = $rRes->fetch_assoc())
{
	$sFilePath = CMS_PRINTPATH.$aRow['file_id'].".".$aRow['ext'];
	if (!file_exists($sFilePath)) die('file does not exist');
	if ($file = fopen($sFilePath,"r"))
	{
		header("Cache-Control: private\n");
		header("Pragma: private\n");		
		header("Content-type: image/pjpeg\n");
		header("Content-Type: application/octet-stream\n"); 
		header("Content-Length: ".filesize($sFilePath)."\n");
		header("Content-Disposition: attachment; filename=\"".$aRow["filename"]."\"\n");
		header("Content-Transfer-Encoding: binary\n");
		header("Content-Description: Compressed folder\n" );
		fpassthru($file);
	}
	else die("ERROR: file is corrupt");
}
else die('error 0673234');
?>