<?
require("../config.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_init.inc");
$nId = getNumber('id');
if (empty($nId)) die('no file specified');

print("<img src='/?mod=903&print=ajax&action=show_file&d_file_id=".$nId."' border='0' />");
?>