<?php
// failu nedzeest, jabut visos projektos, kautvai tukss

// =============== defines for system files functions ===============
define('S_USER_KEY_MODULE', 3);
define('S_USER_KEY_LB_LIST_MODE', 13);

define ("CMS_MD5BEFORE","nj67u");
define ("CMS_MD5HASHPREFIX","KVS0");

//event
define ("D_EVENT_OBJECT",1);
define ("D_EVENT_CLIENT",2);
define ("D_EVENT_EVENT",3);
//comments
define ("D_COMMENT_ORDER", 7);
define ("D_COMMENT_CLIENT", 8);
define ("D_COMMENT_QUOTATION", 9);
define ("D_COMMENT_INVOICE", 10);
define ("D_COMMENT_LBORDER", 11);
// roles
define ("S_ROLE_CONTROL", 1); //vadiba
define ("S_ROLE_ADMIN", 2);
define ("S_ROLE_BROKER", 3);
// mob, sms
define ("S_USER_PHONE_TYPE", 5);
define ("S_USER_PHONE_NAME", 6);
// file
define ("FILE_REF_DEAL", 10);
define ("FILE_REF_EVENT", 11);
define ("FILE_REF_CLIENT", 12);
define ("FILE_REF_ORDER", 13);
define ("FILE_REF_QUOTATION", 14);
define ("FILE_REF_MOUT", 15);
define ("FILE_REF_INVOICE", 16);
define ("FILE_REF_LB_ORDER", 17);

//deal result/ status
define ("D_DEAL_RESULT_GOD",1); //Noslēgta
define ("D_DEAL_RESULT_BAD",4); //Bojāta
define ("D_DEAL_RESULT_BREAK",5); //Pārtraukta
define ("D_DEAL_RESULT_CANCEL",6); //Anulēta

//IMAP
define ("S_USER_EMAIL_USER",        7);
define ("S_USER_EMAIL_PASS",        8);
define ("S_USER_EMAIL_HOST",        9);
define ("S_USER_EMAIL_PORT",       10);
define ("S_USER_EMAIL_ENCRIPTION", 11);

// =============== globals ===============
$aMsgText[1520] = 'Datumam jābūt formātā dd.mm.YYYY!'; 




?>