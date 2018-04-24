<?php
/**
 * funkcija halt_error() izvada lietotaajam formateetu
 * kljuudas pazinjojumu un aptur skripta izpildi
 * error logā saglabā pilnu backtrase
 * 
 * @param cahr $cErrorMessage kļūdas paziņojuma teksts
 * @param int $nHalt 0 - neapstādināt programmas darbību, 1 - apstādināt
 */
function halt_error($cErrorMessage, $nHalt=0)
{
       if(isset($_SESSION["username"]))
            $sUser = $_SESSION["username"];
        else
            $sUser = "system";

	if ($nHalt)
	{

    		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
    		echo "<html>\n";
    		echo "<head>\n";
    		echo "<title>System Errorda</title>\n";
    		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
    		echo "</head>\n";
    		echo "<body>\n";
    		echo "<p>".$cErrorMessage."</p> ".
    			 "<p>User:".$sUser."</p>".
    			 "<p>Time:".date('Y.m.d H:i:s')."</p>".
    			 '<form method="get" action="/"><input type="button" onclick="document.location=\'/\'" value="Turpin?t" /></form>'.
    			 "<p>Send this error to: ".CMS_SUPPORT_EMAIL."</p>";
    		print_r($_REQUEST);
    		echo "</body>\n";
    		echo "</html>\n";
            error_log("HALT");
        
	} else
	{
	    
                        
    		echo "<p>".$cErrorMessage."</p> ".
    			 "<p>User:".$sUser."</p>".
    			 "<p>Time:".date('Y.m.d H:i:s')."</p>";
    		print_r($_REQUEST);
        }
	
    error_log($cErrorMessage);
    error_log("User:".$sUser);
    error_log(var_export($_REQUEST, true));
    $bt=debug_backtrace();
    $sp=0;
    foreach($bt as $k=>$v)
    {
        extract($v);
        $file=substr($file,1+strrpos($file,"/"));
        //if($file=="db.php")continue; // the db object
        $sLine=str_repeat(" ",++$sp); //spaces(++$sp);
        $sLine.="file=$file, line=$line, function=$function";
        error_log($sLine);

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
function mysql_get($cQuery)
{
	global $nLastMySQLErrorCode;
	$aParams=func_get_args();
	if (!isset($aParams[1]))
	{			
		$rQuery = Db::$mysqli->query($cQuery) or halt_error (Db::$mysqli->error." SQL query: \"".$cQuery."\"", 1);
		return $rQuery;
	}
	else
	{
		if ($aParams[1])
		{
			$rQuery = Db::$mysqli->query($cQuery);
			if ($rQuery)
				return $rQuery;
			else
			{
				$nLastMySQLErrorCode=Db::$mysqli->errno;  //secon param should be passed by reference
                                halt_error (Db::$mysqli->error." SQL query: \"".$cQuery."\"");
				return $rQuery;
			}
		}
		else
		{
			$rQuery = Db::$mysqli->query($cQuery) or halt_error (Db::$mysqli->error." SQL query: \"".$cQuery."\"");
			return $rQuery;
		}
	}

}

function fetchData($rRes,$cPrefix="",$nSelectedId="")
{
   $aResult=array();

   if ($rRes->num_rows>0)
       while ($aRow=$rRes->fetch_assoc())
       {
           if(!empty($cPrefix))
               $aRow=addKeyPrefix($aRow,$cPrefix);

           if(!empty($nSelectedId))
           {
               if($aRow[$cPrefix."id"]==$nSelectedId?$nSelectedId:"")
                   $aRow["selected"]=1;
           }

           $aResult[]=$aRow;
       }
   return $aResult;
}


function setCurrentOffice($nOfficeId) {	

	if (!isset($nOfficeId)) die("incorrect office");

	$cSQL = "select count(*) cnt from s_office where id='".$nOfficeId."'";
	
	if ($aRow=mysql_get($cSQL->fetch_assoc())) 
	{
		if ($aRow["cnt"]==1) $_SESSION["office_id"]=$nOfficeId;
	}
	 else die("incorrect office");
}

function setCurrentPeriod()
{
	$rRes = mysql_get("SELECT id, NULLIF(DATE_FORMAT(from_date,'%d.%m.%Y'),'00.00.0000') from_date, ".
		"NULLIF(DATE_FORMAT(to_date,'%d.%m.%Y'),'00.00.0000') to_date FROM s_period WHERE is_active = 1");
	if ($aRow = $rRes->fetch_assoc())
	{
		$_SESSION['period_id'] = $aRow['id'];
		$_SESSION['period_from_date'] = $aRow['from_date'];
		$_SESSION['period_to_date'] = $aRow['to_date'];
	}
	else
	{
		if (isset($_SESSION['period_id'])) unset ($_SESSION['period_id']);
		if (isset($_SESSION['period_from_date'])) unset ($_SESSION['period_from_date']);
		if (isset($_SESSION['period_to_date'])) unset ($_SESSION['period_to_date']);
	}
		
}

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}


function paramsToSet($aParams,$aAllowed,$skipEmpty = false)
/*
 * Creates SQL query from $aParams
 * $who Uldis Shilovs
 * $when 25.05.2005
 */
{
	if (is_array($aParams))
	{
		$cReturn="";
		foreach($aParams as $cKey=>$cValue) {
            if ($skipEmpty && empty($cValue)) {
                continue;
            }

            if (in_array($cKey, $aAllowed)) //if param is allowed to be set
            {
                // add slashes or remove slashes
                if (strpos(str_replace("\'", "", $cValue), "'") != false)
                    $cValue = addslashes($cValue);
                //if value==dd.mm.YYYY
                if (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/', $cValue))
                    $cReturn .= $cKey . '=STR_TO_DATE(\'' . $cValue . '\',GET_FORMAT(DATE, \'EUR\')), ';
                //if value==dd.mm.YYYY hh:ii:ss
                elseif (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} \d{2}\:\d{2}\:\d{2}$/', $cValue))
                    $cReturn .= $cKey . '=STR_TO_DATE(\'' . $cValue . '\',\'%d.%m.%Y %H:%i:%s\'), ';
                //if value==dd.mm.YYYY hh:ii
                elseif (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} \d{2}\:\d{2}$/', $cValue))
                    $cReturn .= $cKey . '=STR_TO_DATE(\'' . $cValue . ':00\',\'%d.%m.%Y %H:%i:%s\'), ';
                else {
                    if ($cValue === 'NULL')
                        $cReturn .= $cKey . "=NULL, ";
                    else
                        $cReturn .= $cKey . '=\'' . $cValue . '\', ';
                }
            }
        }

		//cuts comma from string end
		if(!empty($cReturn))
		{
			$l=strlen($cReturn)-1;
			for($l;$l>0;$l--)
				if($cReturn[$l]==',')
				{
					$cReturn=substr($cReturn, 0, $l);
					break;
				}
		}

		return $cReturn;
	}
	else
		return false;

}

function addKeyPrefix($aParams,$cPrefix)
/*
 * Adds prefix for each array key
 * $who Uldis Shilovs
 * $when 24.05.2005
 */
{
	if (is_array($aParams))
	{
		foreach($aParams as $cKey=>$cValue)
			$aTemp[$cPrefix.$cKey]=$cValue;

		unset($aParams);
		return $aTemp;
	}
	else
		return false;
}

function dateCompare($sDateA, $sDateB, $sOp)
{
	$nDateA = dateToNum($sDateA);
	$nDateB = dateToNum($sDateB);
	switch ($sOp)
	{
		case '=': case '==':
			if ($nDateA == $nDateB) return true;
		break;
		case '>':
			if ($nDateA > $nDateB) return true;
		break;
		case '>=':
			if ($nDateA >= $nDateB) return true;
		break;
		case '<=':
			if ($nDateA <= $nDateB) return true;
		break;
		case '>=':
			if ($nDateA >= $nDateB) return true;
		break;
		default:
	}
	return false;
}

function dateToNum($sDate)
{
	if (preg_match("^[0-3]\d\.[01]\d\.\d\d\d\d$",$sDate,$aMatches))
	{
		$number = substr($aMatches[0],-4,4).substr($aMatches[0],3,2).substr($aMatches[0],0,2);
		return intval($number);
	}
	return 0;
}


function validateIBAN($str)
//fja pārbauda iban korektību
//1. Parvieto pirmos četrus simbolus klienta konta numura labaja puse.
//2. Parveido burtus par cipariem.
//3. Kontrolcipari ir pareizi, ja, izmantojot MOD 97-10, iegust skaitli 1.
{
	//aizmet pirmos 4 simbolus uz beigaam
	$start=substr($str,0,4);
	$str=substr($str,4);
	$str=$str.$start;

	//paarveido par int
	$str=stringToInt($str);

	return mod97($str)==1?true:false;
}
function mod97($str)
{
	unset($ret);
	while($str!=""){
		if (!isset($ret)) {
			$ret = substr($str, 0, 9);
			$str = substr($str, 9);
		} else {
			$lenght = 9 - strlen($ret);
			$ret .= substr($str, 0, $lenght);
			$str = substr($str, $lenght);
		}
		$ret = $ret%97;
	}

	return $ret;
}
function stringToInt($str)
{
	$temp="";
	for($i=0;$i<strlen($str);$i++)
	{
		if(is_numeric($str[$i]))
			$temp.=$str[$i];
		else
			$temp.=(ord($str[$i])-55);
	}
	return $temp;
}


function make_pages($nOffset = 0,$nCount,$nRecsPerPage,&$aResult)
{
	$aResult = array();
	if ($nCount > $nRecsPerPage)
	{
		// link to the same page, just w/ different offset
		$sLink = "/?".array2suffix($_GET,array('offset'));

		// add <<
		if ($nOffset > 0)
		{
			$nPrevOffset = $nOffset - $nRecsPerPage;
			if ($nPrevOffset < 0) $nPrevOffset = 0;
			$aResult[] = array('page_no'=>"&laquo;&laquo;",'link'=>$sLink."&amp;offset=".$nPrevOffset);
		}

		$nPageNo = 1;
		for ($i = 0; $i < $nCount; $i += $nRecsPerPage)
		{
			$aTmp = array('page_no'=>$nPageNo,'link'=>$sLink."&amp;offset=".$i);
			if ($nOffset == $i) $aTmp['current'] = 1;
			$aResult[] = $aTmp;
			$nPageNo ++;
		}

		// add >>
		if ($nOffset < ($nCount - $nRecsPerPage))
		{
			$nNextOffset = $nOffset + $nRecsPerPage;
			$aResult[] = array('page_no'=>"&raquo;&raquo;",'link'=>$sLink."&amp;offset=".$nNextOffset);
		}

		return true;
	}
	return false;
}

function array2suffix($array,$aExceptions=NULL)
{
	if (empty($array) || !is_array($array)) return false;
	$sSuffix = '';
	$bFirst = true;
	foreach ($array as $k=>$v)
		if (empty($aExceptions) || !is_array($aExceptions) || !in_array($k,$aExceptions))
		{
			if ($bFirst)
			{
				$bFirst = false;
                                
                                if (!is_array($v))
				    $sSuffix .= $k."=".$v;
                                
                                //ep array
                                else {
                                    
                                   foreach($v as $kk => $vv){
                                       
                                      $sSuffix .= $k."[]=".$vv; 
                                   } 
                                }
                                    
			}
			else
                                if (!is_array($v)){
                                    $sSuffix .= "&amp;".$k."=".$v;
                                } else {
                                   
                                    //ep array
                             
                                    
                                   foreach($v as $kk => $vv){
                                       
                                      $sSuffix .= "&amp;".$k."[]=".$vv; 
                                   } 
                                }
                                    
                                
                
				
		}
	return $sSuffix;
}

function make_scroll_buttons($nOffset,&$aList,&$oTmpl, $nRecsPerPage = RECS_PER_PAGE)
{
	$sLink = "/?".array2suffix($_GET,array('offset')).'&amp;offset=';
	$sTag = '<div id="paging">';

	if ($nOffset > 0)
	{
		$nPrevOffset = $nOffset - $nRecsPerPage;
		if ($nPrevOffset < 0) $nPrevOffset = 0;
		$sTag .= '<a class="page-left" href="'.$sLink.$nPrevOffset.'" title="'.get_msg_text(3310).'"></a>';
	}

	if (count($aList) > $nRecsPerPage)
	{
		$nNextOffset = $nOffset + $nRecsPerPage;
		$sTag .= '<a class="page-right" href="'.$sLink.$nNextOffset.'" title="'.get_msg_text(3311).'"></a>';
		array_pop($aList);
	}

	$sTag .= '</div>';
	$oTmpl->SetVar('paging_buttons',$sTag);

}

function ip2ulong($sIp)
//unsigned
{
	return sprintf( "%u", ip2long($sIp) );
}

function jsEscape(&$a)
{

	if (empty($a)) 
		return;

	if (is_array($a))
	{
		foreach ($a as $k=>$v)
			jsEscape($a[$k]);

		return;
	}

	if (is_string($a))
		$a=preg_replace("/\r?\n/", "\\n", addslashes($a));

	return;
}

class Db {
    public static $mysqli;

    public static function connect($server, $user, $password, $database) {
        mysqli_report(MYSQLI_REPORT_STRICT);
        if(self::$mysqli) {
            throw new Exception("Already connected");
        }
        self::$mysqli = mysqli_connect($server, $user, $password, $database);
        if(!self::$mysqli) {
            halt_error(mysqli_error());
        }

        //HACK: this should be removed this is added only for the code:
        //      update x set y = '' where y is integer to work
        $q = self::$mysqli->query("SELECT @@sql_mode mode;");
        $r = $q->fetch_assoc();
        if(is_array($r) && isset($r['mode'])) {
            $parts = explode(',', $r['mode']);
            foreach($parts as $i => $part) {
                if($part === 'STRICT_TRANS_TABLES') {
                    unset($parts[$i]);
                    break;
                }
            }
            $sql_mode = implode(',', $parts);
            self::$mysqli->query("SET sql_mode = '$sql_mode';");
        }

        self::$mysqli->query('SET NAMES utf8');
        self::$mysqli->set_charset('utf8');
    }
}


?>