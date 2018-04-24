<?php


require (CMS_LIBPATH.CMS_SYSPATH."autoload.inc");
require (CMS_LIBPATH.CMS_SYSPATH."s_msg.inc");
require CMS_LIBPATH.CMS_SYSPATH."s_common_functions.php"; 
require(CMS_LIBPATH.CMS_SYSPATH."s_get.php");
require(CMS_LIBPATH.CMS_SYSPATH."c_data.php");
require(CMS_LIBPATH.CMS_SYSPATH."d_data.php");
require(CMS_LIBPATH.CMS_SYSPATH."s_data.php");
require(CMS_LIBPATH.CMS_SYSPATH."s_cash.php");
require(CMS_LIBPATH.CMS_SYSPATH."s_session_data.php");
require(CMS_LIBPATH.CMS_SYSPATH."s_msgerr.php");
require CMS_EXTRAPATH."vlib/vlibTemplate.php" ;

$err_level = error_reporting(0);
Db::connect(CMS_DBSERVER, CMS_DBUSER, CMS_DBPASS, CMS_DBASE);
error_reporting($err_level);

require CMS_LIBPATH."s_include.inc";