<?php
/**
* funkcija halt_error() izvada lietotaajam formateetu
* kljuudas pazinjojumu
* Uz kugja ieleik snqo tabulā, kā error ierakstu
*
* @param string $cErrorMessage - kļūdas paziņojuma teksts
* @param int/boolean $nHalt 1/true - apturet skripta izpildi
*/
function halt_error($cErrorMessage, $nHalt=0)
{
       if(isset($_SESSION["username"]))
            $sUser = $_SESSION["username"];
        else
            $sUser = "system";

	if ($nHalt)
	{
	    if (defined(PRINT_TYPE) && PRINT_TYPE == PRINT_TYPE_AJAX) {
            /**
            * AJAX skriptiem (json)
            */
            $aDirectJson['error'] = "System error!\n$cErrorMessage";
            echo json_encode($aDirectJson);
	    } else {
    		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
    		echo "<html>\n";
    		echo "<head>\n";
    		echo "<title>System Errorda</title>\n";
    		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
    		echo "</head>\n";
    		echo "<body>\n";
    		echo "<p>".$cErrorMessage."</p> ".
    			 "<p>User:".$_SESSION["username"]."</p>".
    			 "<p>Time:".date('Y.m.d H:i:s')."</p>".
    			 '<form method="get" action="/"><input type="button" onclick="document.location=\'/\'" value="Turpin?t" /></form>'.
    			 "<p>Send this error to: ".CMS_SUPPORT_EMAIL."</p>";
    		print_r($_REQUEST);
    		echo "</body>\n";
    		echo "</html>\n";
            error_log("HALT");
        }
	} else
	{
	    if (defined('PRINT_TYPE') && PRINT_TYPE == PRINT_TYPE_AJAX) {
            $aDirectJson['error'] = "System error!\n$cErrorMessage";
	    } else {
                        
    		echo "<p>".$cErrorMessage."</p> ".
    			 "<p>User:".$sUser."</p>".
    			 "<p>Time:".date('Y.m.d H:i:s')."</p>";
    		print_r($_REQUEST);
        }
	}

    error_log($cErrorMessage);
    error_log("User:".$sUser);
    error_log(var_export($_REQUEST, true));
    $bt=debug_backtrace();

    $sp=0;
    $aTrace = array();
    $aTrace[] = $cErrorMessage;
    $aTrace[] = var_export($_REQUEST, true);
    foreach($bt as $k=>$v){
        extract($v);
        $file=substr($file,1+strrpos($file,"/"));
        //if($file=="db.php")continue; // the db object
        $sLine=str_repeat(" ",++$sp); //spaces(++$sp);
        $sLine.="file=$file, line=$line, function=$function";
        error_log($sLine);
        $aTrace[] = $sLine;

    }

    if(!RPL_MASTER){
        /**
         * uz kugja sql error liek out rindaa
         */
        if (function_exists('error_out')) {
            include_once CMS_LIBPATH.'sync.php';
        }
        error_out(FALSE,implode(PHP_EOL,$aTrace),0);
    }

    if ($nHalt) exit;
}

/*
*	funkcija mysql_get() atgriezh mysql query rezultaatu, bet neveiksmes gadiijumaa izvada lietotaajam
*	kljuudas pazinjojumu un aptur skripta izpildi
*
*	$cQuery - sql vaicaajuma teksts
*
*	funkcija veiksmiigas izpildes gadiijumaa atgriezh mysql vaicaajuma rezultaatu
*/

$nLastMySQLErrorCode=0;
$bGlobalMysqlGetDebug = FALSE;
function mysql_get($cQuery)
{
    global $bGlobalMysqlGetDebug;
    /**
     * loggings konkretam pietotajam
     */
    if (defined('CMS_DEBUG_MYSQL_USER_ID')
         && isset($_SESSION["user_id"])
         && CMS_DEBUG_MYSQL_USER_ID == $_SESSION["user_id"]
         ) {
            $sDir = CMS_UPLOADPATH . 'sql';
            if (!file_exists($sDir)) {
                mkdir($sDir);
            }
            $sFile = $sDir . '/user_'.CMS_DEBUG_MYSQL_USER_ID.'_'.date('ymd').'.log';
            $fp = fopen($sFile, 'a');
            fwrite($fp, date('H:i:s').' '.$cQuery.PHP_EOL);
            fclose($fp);                    
    /**
     * loggings ieslegts
     */        
    } elseif (  $bGlobalMysqlGetDebug ) {
            $sDir = CMS_UPLOADPATH . 'sql';
            if (!file_exists($sDir)) {
                mkdir($sDir);
            }
            $sFile = $sDir . '/debug_'.date('ymd').'.log';
            $fp = fopen($sFile, 'a');
            fwrite($fp, date('H:i:s').' '.$cQuery.PHP_EOL);
            fclose($fp);
        }
        
        
	global $nLastMySQLErrorCode;
	$aParams=func_get_args();
	if (!isset($aParams[1]))
	{
		$rQuery = mysql_query($cQuery);
    	if ($rQuery)
    		return $rQuery;
    	else
    	{
            halt_error (mysql_error()." SQL query: \"".$cQuery."\"");
    		return $rQuery;
    	}

	}
	else
	{
		if ($aParams[1])
		{
			$rQuery = mysql_query($cQuery);
			if ($rQuery)
				return $rQuery;
			else
			{
				$nLastMySQLErrorCode=mysql_errno();  //secon param should be passed by reference
                halt_error (mysql_error()." SQL query: \"".$cQuery."\"");
				return $rQuery;
			}
		}
		else
		{
		$rQuery = mysql_query($cQuery);
    	if ($rQuery)
    		return $rQuery;
    	else
    	{
            halt_error (mysql_error()." SQL query: \"".$cQuery."\"");
    		return $rQuery;
    	}
		}
	}

}



/*
*	funkcija check_module paarbauda vai lietotaajam ir pieejams parametraa noraadiitais modulis
*	$nModuleId - skaitliska modulja id veertiiba
*	funkcija atgriezh true vai false
*/
function check_module($nModuleId)
{


	$rModCheck=mysql_get(
				"select m.* from ".
				"s_module m, s_perm p, s_role r where ".
				"m.id='".$nModuleId."' and r.id='".$_SESSION["role_id"].
				"' and r.id=p.role_id and m.id=p.module_id ".
				"and m.status=1 and r.status=1"
				);

	if (mysql_num_rows($rModCheck)) 
		return true;
	else
		return false;
}



/* funkcija vr_baltic2latin() aizvieto latvieshu burtus ar latiinju burtiem un simbolu ' ' ar '_'
* $cString - teksts ar latvieshu burtiem;
* funkcija atgriezh tekstu ar latiinju burtiem
*/
function vr_baltic2latin($cString)
{
	$aPattern=array(0=>"/Ā/", 1=>"/Č/",2=>"/Ē/", 3=>"/Ģ/", 4=>"/Ī/", 5=>"/Ķ/", 6=>"/Ļ/", 7=>"/Ņ/", 8=>"/Š/", 9=>"/Ū/", 10=>"/Ž/", 11=>"/\ /");
	$aReplace=array(0=>"A", 1=>"C",2=>"E", 3=>"G", 4=>"I", 5=>"K", 6=>"L", 7=>"N", 8=>"S", 9=>"U", 10=>"Z", 11=>"_");
	return preg_replace ( $aPattern, $aReplace, $cString);
}
/* AddressToString paliigf-ja*/
function Decode($cValue,$cIf,$cThen,$cElse)
{
	if ($cValue==$cIf)
	{
		return $cThen;
	}
	else
	{
		return $cElse;
	}
}

function AddToErrors($cErrCode, $cErrMsg)
{
	global $aDataErrors;
	$aError=array(
				"error_code" => $cErrCode,
				"error_type" => 2,
				"error_text" => $cErrMsg
			);
	$aDataErrors[]=$aError;
}

function StatussToString($nStatuss)
{
	switch ($nStatuss)
	{
		case 0: return "Dzēsts";
		case 1: return "Aktīvs";	
		default: return false;
	}

}

function StatussToImage($nStatuss)
{
	switch ($nStatuss)
	{
		case 0: return "(!I?M!G?I!IMG!I?M!G?0!)";//"<IMG src='/images/ico_status_blocked.gif'>";
		case 1: return "(!I?M!G?I!IMG!I?M!G?1!)";//"<IMG src='/images/ico_status_active.gif'>";
		default: return false;
	}
}


function FetchOCIError()
{
	$aParams = func_get_args();

	if (isset($aParams[0])) {	
		$aErrors = OCIError($aParams[0]);
	} else {
		$aErrors = OCIError();
	}

	$cOutput = isset($aErrors["message"])?$aErrors["message"]:"";
	return $cOutput;
}

//temporary patch for the quote issue
function text_encode($text)
{
	return strtr(ora_dequote($text),Array("'"=>'&#039;','"'=>'&#034;'));
	
}

function ora_quote($text)
{
	return strtr($text,Array("'"=>"`"));
}
function ora_dequote($text)
{
	return strtr($text,Array("`"=>"'"));
}

function capitalize($text)
{
//	return preg_replace("/([a-z\xE0-\xFF])/e","chr(ord('\\1')-32)",ora_quote($text));
	return ora_quote($text);
}


/*
funkcija pārbauda, vai parole:
	- ir 6-20 simbolus gara
	- nesatur tukšumus
	- satur vismaz 4 latīņu burtus 
	- satur vismaz 2 ciparus

	atgriež:
		true	ja parole atbilst nosacījumiem
		false	ja parole neatbilst nosacījumiem
*/
function check_password_rules($cPassword){	
	//vai paroles garums ir 6-20 
	if ( (strlen($cPassword) > 20) || (strlen($cPassword) < 6)	) {
		return false;
	}

	//vai satur tukšumus? ja satur, tad mums neder
	if (preg_match("/(.*[\s].*)/", $cPassword)) {
		return false;
	}
	
	//vai ir vismaz 4  simboli no kopas A-Z (latīņu burti)
//	if (!preg_match("/(.*[A-Z].*){4}/", $cPassword)) {
//		return false;
//	}
	
	//vai ir vismaz 2 simboli no kopas 0-9
//	if (!preg_match("/(.*[0-9].*){2}/", $cPassword))	 {
//		return false;
//	}

	return true;	//ja tikām tik tālu, tad parole ir OK
}


function setCurrentOffice($nOfficeId) {	

	if (!isset($nOfficeId)) die("incorrect office");

	$cSQL = "select count(*) cnt from s_office where id='".$nOfficeId."'";
	
	if ($aRow=mysql_fetch_assoc(mysql_get($cSQL))) 
	{
		if ($aRow["cnt"]==1) $_SESSION["office_id"]=$nOfficeId;
	}
	 else die("incorrect office");
}

function cash_tbx($units)
{
		$units=abs($units);  // meedz gadiities, kad summa ir miinusaa
		$len=strlen($units);
		$outstr="";
		for ($i=0;$i<$len;$i++)
		{
			$current=substr($units,$i,1);
			switch ($current)
			{
				case '0': $textual=" ";break;
				case '1': $textual=" vien";break;
				case '2': $textual=" div";break;
				case '3': $textual=" trīs";break;
				case '4': $textual=" četr";break;
				case '5': $textual=" piec";break;
				case '6': $textual=" seš";break;
				case '7': $textual=" septiņ";break;
				case '8': $textual=" astoņ";break;
				case '9': $textual=" deviņ";break;
			}

			switch ($len-$i)
			{
				case 1:
					$textual.=isset($teen)&&$teen?($current!='0'?"padsmit ":"desmit "):($current=='1'?"s ":($current!='0'&&$current!='3'?"i ":" "));
				break;

				case 2:
					$textual=$current!='1'?($current!='0'?$textual."desmit ":" "):" ";
					$teen=$current=='1'?true:false;
				break;
				case 3:
					$textual=$current=='0'?" ":($current=='1'?" viens simts ":($current=='3'?$textual." simti ":$textual."i simti "));
				break;
				case 4:$textual.=$current=='0'?" ":" ";break;

			}

			$outstr.=$textual;
		}

		return $outstr;
}


function cash_text($value)
{
	$outstr='';
	if (floor($value)<=999999999999) 
	{
		$threedigits=explode("X",strrev(preg_replace("/([0-9]{1,3})/i","X\$0",strrev(strval(floor($value))))));
		$threedigits=array_reverse($threedigits);
		$outstr.=isset($threedigits[4])&&$threedigits[4]? ($threedigits[4]=='1'?cash_tbx($threedigits[4])." miljards ":cash_tbx($threedigits[4])." miljardi "):"";
		$outstr.=isset($threedigits[3])&&$threedigits[3]?	($threedigits[3]=='1'?cash_tbx($threedigits[3])." miljons ":cash_tbx($threedigits[3])." miljoni "):"";
		$outstr.=isset($threedigits[2])&&$threedigits[2]?($threedigits[2]=='1'?cash_tbx($threedigits[2])." tūkstotis ":cash_tbx($threedigits[2])." tūkstoši "):"";
		$outstr.=isset($threedigits[1])&&$threedigits[1]?(substr($threedigits[1],strlen($threedigits[1])-1,1)=='1'&&substr($threedigits[1],strlen($threedigits[1])-2,2)!='11'?cash_tbx($threedigits[1])." lats ":cash_tbx($threedigits[1])." lati "):"";

	}
	else
	{
		 return false;
	}
	
	if ($outstr=='') {$outstr="nulle lati";}

	$coins=round(100*(round($value,2)-floor($value)));
	$coins=strlen($coins)==1?"0".$coins:(strlen($coins)==0?"00":$coins);
	$coins.=substr($coins,1,1)=='1'&&$coins!='11'?" santīms":" santīmi";

	$outstr.=" un ".$coins;
	return $outstr;
}
// end of cash-number-to-text converter

function detect_smartphone(&$sPhone)
{
   
    
   $iphone = strpos($_SERVER['HTTP_USER_AGENT'],'iPhone');
   $android = strpos($_SERVER['HTTP_USER_AGENT'],'Android');
   $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],'webOS');
   $ipod = strpos($_SERVER['HTTP_USER_AGENT'],'iPod');
   
   if ($iphone) { $sPhone = 'iphone'; return 1; }
   else if  ($android) { $sPhone = 'android'; return 1; }    
   else if  ($palmpre) { $sPhone = 'palm'; return 1; }       
   else if  ($ipod) { $sPhone = 'ipod'; return 1; }  
   else return 0;
           
} 

function DateDMY2YMD($s) {
    $s = str_replace(array('.','/'), '-', $s);
    $a = explode('-', $s);
    if (count($a) != 3) {
        halt_error('Ilegal date format');
        return FALSE;
    }
    list($day, $month, $year) = $a;
    return date("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
}

function DateYMD2DMY($s) {
    $s = str_replace(array('.','/'), '-', $s);
    $a = explode('-', $s);
    if (count($a) != 3) {
        halt_error('Ilegal date format:'.$s);
        return FALSE;
    }
    list($year, $month, $day) = $a;
    return date("d-m-Y", mktime(0, 0, 0, $month, $day, $year));
}

/**
 *  f-ja page_array prieksh paginator, aprkina lapu skaitu
 */
function page_array($nNumRows,$nRecsPerPage = 20,$nPage = 1)
{
    $aPages = array();
    while($nNumRows>0)
    {
        $nP = count($aPages) + 1;
        if ($nP == $nPage)
            $aPages[] = array('page'=>$nP,'active'=>'on');
        else
            $aPages[] = array('page'=>$nP,'active'=>'off');
        $nNumRows -= $nRecsPerPage;
    }
    return $aPages;
}
?>