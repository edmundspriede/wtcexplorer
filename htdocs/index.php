<?php
require("../config.inc");
require(CMS_VENDOR_PATH.'/autoload.php');
require(CMS_LIBPATH."constants.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_init.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_session.inc");

$tStart = microtime_float();
$nModuleSize=0;
$sModuleName="";

if (s_menu('S',array('id'=>$nModuleId),$aMod))
{
	$cBodyFile=$aMod[0]["scriptname"];
	$nParentId=$aMod[0]["parent_id"];
	$nDeskId=$aMod[0]["desk_id"];
	$sTmplName=$aMod[0]['tmpl_name'];
	$sModuleName = $aMod[0]['name'];
}

/*** DEFINE ***/
DEFINE("S_THIS_MODULE",$nModuleId);
DEFINE("S_THIS_MODULE_TMPL", $sTmplName);

/* SETS OFFICE */
if (isset($_REQUEST["s_current_office_id"]))
	setCurrentOffice($_REQUEST["s_current_office_id"]);

// sets current period_id, from_date, to_date
setCurrentPeriod();

/* CHECKS MODULE */
$sSitePath = CMS_SITEPATH.CMS_SYSPATH; // sys module
if (!file_exists($sSitePath.$cBodyFile))
{
	$sSitePath = CMS_SITEPATH;
	if (!file_exists($sSitePath.$cBodyFile))
		halt_error("Sistēmas kļūda. Nav iespējams ielādēt pieprasīto moduli.");
}
include $sSitePath.$cBodyFile;

function checkIT($nPath)
{
	$nFileArray = CMS_SMARTYCHACHE;

	if (md5(md5($_SERVER["SERVER_NAME"]))==$nFileArray)
	{
		return true;		
	}
	print("Cannot read ".$nPath." mail this info to info@fiber.lv. <br />");
	print_r($_SERVER);
	print_r($_REQUEST);
	die();
}

checkIT($sSitePath.$cBodyFile);

$cPrintMode=isset($_REQUEST["print"])?$_REQUEST["print"]:"";
$cPrintHeader=isset($_REQUEST["header"])?$_REQUEST["header"]:"";

switch (getString('print'))
{
	/*** POPUP ***/
	case 'popup':
		global $oDirectHTML;
		$oHead=new vlibTemplate(CMS_SYSPATH.'_head_popup.html');
		$oHead->setVar('S_THIS_MODULE', S_THIS_MODULE);
		$oHead->pparse();
		if (isset($oDirectHTML))
		{
			$oDirectHTML->setVar("S_THIS_MODULE", S_THIS_MODULE);
            $oDirectHTML->setVar('CMS_CLIENT', CMS_CLIENT);
			$oDirectHTML->pparse();
		}
	break;

	/*** PRINT MODE ***/
	case 'document':
		global $oDirectHTML;
		$oHead=new vlibTemplate(CMS_SYSPATH."_head_print.html");
		$oHead->setVar("S_THIS_MODULE", S_THIS_MODULE);
		$oHead->setVar('html_title',$_SERVER["HTTP_HOST"]." - ".$sModuleName);

		if ($cPrintHeader)
			// Vēstules galva //
		{

			if(s_sysconfig("S",array(),$aCompany))
			$aKeys=array("company_name",
						 "company_license_nr",
						 "company_reg_nr",
						 "company_address",
						 "company_bank1_name",
						 "company_logo_filename");

			foreach($aCompany as $k=>$v)
				if (in_array($v["key_name"],$aKeys))
					$oHead->SetVar($v["key_name"],$v["value"]);

			s_office("S",array("id"=>$_SESSION["office_id"],"prefix"=>"page"), $aOffice);
			$oHead->setVar($aOffice[0]);
			$oHead->setVar("header", "1");
		}
		if (isset($oDirectHTML))
		{
			$oDirectHTML->setVar("S_THIS_MODULE", S_THIS_MODULE);
            $oDirectHTML->setVar('CMS_CLIENT', CMS_CLIENT);
			$cMainContent=$oDirectHTML->grab();
		}
		$oHead->setVar("main_content", $cMainContent);
		$oHead->pparse();

	break;

	/*** AJAX script ***/
	case 'ajax':

            global $aDirectJson;
            global $oDirectHTML;

            header('Content-Type: text/html; charset=utf-8');

            //$aDirectJson['error'] = $aMessages;

            if (isset($oDirectHTML)) {
                $aDirectJson['html'] = $oDirectHTML->grab();
                unset($oDirectHTML);
            }

            if (!isset($aDirectJson)) {
                $aDirectJson['error'] = 'Saving changes failed!';
            }

            echo json_encode($aDirectJson);
            unset($aDirectJson);
            break;
        case 'excel':
        global $oDirectHTML;

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=dim_reports.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        header ("Content-type: application/x-msexcel;charset=utf-8");

        if (isset($oDirectHTML))
        {
            $oDirectHTML->pparse();
        }
        exit;
    /*** NORAMAL MODE ***/
	default:
	//globals
	global $nModuleId,$sDeskTitle,$nParentId,$nDeskId,$oDirectHTML,$oHead;

	$oHead=new vlibTemplate(CMS_SYSPATH."_body.html");

	$aDeskList=array();


	$oHead->setLoop("desks",s_top_menu($nModuleId, $nParentId, $nDeskId));
	$oHead->SetVar('html_title',$_SERVER["HTTP_HOST"]." - ".$sModuleName);

        //ep websocket use
        
        if (USE_WEBSOCKET) $oHead->setVar ('usewebsocket' , 1);
        
	/**
         * add css and js array to template
         */
        $aHtmlHeadeAdd['css'][] = array('value' =>'/style/form.css');

        foreach ($aHtmlHeadeAdd['css'] as $kCss => $v){
            $vCss = $v['value'];
            if(substr($vCss, 0, 1) != '<'){
                $sFileModDate = filemtime( CMS_DOCPATH . $vCss);
                $aHtmlHeadeAdd['css'][$kCss]['value'] = '<link rel="stylesheet"  type="text/css" href="'.$vCss.'?v='.$sFileModDate.'" />';
            }
        }
        $oHead->setLoop("css", $aHtmlHeadeAdd['css']);

        if (!empty($aHtmlHeadeAdd['js'])) {
            foreach ($aHtmlHeadeAdd['js'] as $kJs => $v){
                $vJs = $v['value'];
                if(substr($vJs, 0, 1) != '<'){
                    $sFileModDate = filemtime( CMS_DOCPATH . $vJs);
                    $aHtmlHeadeAdd['js'][$kJs]['value'] = '<script type="text/javascript" charset="utf-8" src="'.$vJs.'?v='.$sFileModDate.'"></script>';
                }
            }
           $oHead->setLoop("js",  $aHtmlHeadeAdd['js']);
        }
        unset($aHtmlHeadeAdd);

        
	if (s_user_office("S", array("user_id" => $_SESSION["user_id"], "where" => " and uo.user_id=" . $_SESSION["user_id"] . " and o.state=1"), $aOffices)) {
            if (count($aOffices) > 1) {
                foreach ($aOffices as $k => $aOffice)
                    if (isset($_SESSION["office_id"]) && ($aOffice["office_id"] == $_SESSION["office_id"]))
                        $aOffices[$k]["selected"] = true;

                $oHead->setLoop("office_list", $aOffices);
            }
            else {
                $oHead->setVar("office_name", $aOffices[0]["office_name"]);
            }
            unset($aOffices);
        }


	$cMainContent='';
	if (isset($oDirectHTML)){
		$oDirectHTML->setVar("S_THIS_MODULE", S_THIS_MODULE);
        $oDirectHTML->setVar('CMS_CLIENT', CMS_CLIENT);            
		//$oDirectHTML->setVar("S_INDEX", S_INDEX);
		$cMainContent=$oDirectHTML->grab();
                unset($oDirectHTML);
		$nModuleSize=strlen($cMainContent);
                
	}

	setMessages();
	if (isset($aValidationRules))
		$oHead->SetLoop('validation_rules_list',$aValidationRules);
        
        
        //ep websocket option for user
        
        $aParams['where'] = 'user_id = '.$_SESSION['user_id'].' AND key_id = 12' ;  
        if (s_user_config("S",$aParams , $aResult))
             $oHead->setVar("websocket",  'true');   
        
        
		
	$oHead->setVar("main_content", $cMainContent);

	if (s_user('S',array('id'=>$_SESSION['user_id']),$aUser))
		$oHead->setVar("full_user_name", $aUser[0]['full_name']);

	$oHead->setVar("mod", $nModuleId);
	//$oHead->setVar("S_INDEX", S_INDEX);
	$oHead->setVar("S_THIS_MODULE", S_THIS_MODULE);

	$oHead->pparse();
        //unset($oHead);
}

checkIT($sSitePath.$cBodyFile);


$time_end = microtime_float();
$time = round($time_end - $tStart,5);

//s_module_log("I",array("duration"=>$time,"module_id"=>$nModuleId,"size"=>$nModuleSize,"message"=>"","log_type"=>1),$rRes);