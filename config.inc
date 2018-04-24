<?php
//exit('Uzgaidiet! Sistemas modernizacija. (Uldis)');
// Zemaak ir saita dalju atrashanaas celji, nemainiit, ja saits netiek paarlikts uz cita servera
// ;;;
//error_reporting (E_ERROR | E_WARNING | E_PARSE );
error_reporting(E_ALL ^ E_STRICT ^ E_DEPRECATED);
ini_set("display_errors", 1);

ini_set('session.gc_maxlifetime', 2 * 60 * 60);
ini_set('session.cookie_lifetime', 2 * 60 * 60);
//ini_set('session.save_path', '/home/chroot/websites/net.kls.lv/upload/session');


define("LOG_INSERT",1);
define("RECS_PER_PAGE",30);
define("RECS_MAX_LIMIT",1000000);

define("_NULL",chr(10).chr(2));
define ("CMS_PATH_SEP",'/');

define ("CMS_BASEPATH",		dirname(__FILE__)."/");

define ("CMS_ETCPATH", 		CMS_BASEPATH."etc/");
define ("CMS_DOCPATH",		CMS_BASEPATH."htdocs/");
define ("CMS_LIBPATH",		CMS_BASEPATH."lib/");
define ("CMS_SITEPATH",		CMS_BASEPATH."site_res/");
define ("CMS_BASETPLPATH",	CMS_BASEPATH."templates/");
define ("CMS_EXTRAPATH",	CMS_BASEPATH."extra/");
define ("CMS_PRINTPATH",	CMS_BASEPATH."print/");
define ("CMS_VENDOR_PATH",	CMS_BASEPATH.'vendor'.CMS_PATH_SEP);
define ("CMS_SYSPATH", 		"sys/");
define ("CMS_TMPPATH", 		CMS_BASEPATH."tmp/");

define ("CMS_SMARTYCHACHE",	(md5(md5($_SERVER["SERVER_NAME"]))));
define ("CMS_PHPPATH",		"/usr/local/apache/bin/");
define ("CMS_SERVER",		"kls.alaidd.org");


	
// Mysql datu baazes serveris, nosaukums, lietotaajs un parole
define ('CMS_DBSERVER', '127.0.0.1');
define ('CMS_DBASE', 'wtc');
define ('CMS_DBUSER', 'root');
define ('CMS_DBPASS', '');
define ("CMS_SMTP",		"mail.itc.neonet.lv");
define ("CMS_SMTP_USER",		"orders@kls.lv");
define ("CMS_SMTP_PASS",		"26ea44Ve3");


define ("CMS_SUPPORT_EMAIL", "uldisnelsons@gmail.com");
define("USE_WEBSOCKET", true);



define('SP_SESSION_TIMEOUT',30);