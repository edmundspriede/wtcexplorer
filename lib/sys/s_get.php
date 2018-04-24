<?php

function getNumber($cName,$aParams=array())
{
	return parseParams($cName,"number",$aParams);
}

function getString($cName,$aParams=array())
{
	return parseParams($cName,"string",$aParams);
}

function getDatums($cName,$aParams=array())
{
	return parseParams($cName,"date",$aParams);
}

function getTime($cName,$aParams=array())
{
	return parseParams($cName,"time",$aParams);
}

function getDateTime($cName,$aParams=array())
{
	return parseParams($cName,"datetime",$aParams);
}

function getCb($cName,$aParams=array())
{
	if(!array_key_exists('key',$aParams))
	{
		if(isset($_REQUEST[$cName]))
			return 1;
		else
			return 2;
	}
	else
		if(isset($_REQUEST[$cName][$aParams["key"]]))
			return 1;
		else
			return 2;
}

function parseParams($cName,$cType,$aParams)
/*/ params can hold:
//		key=>value   -- key if 2dimensional
//		default=>value
//		notnull         -- not null
//		max=>value
//		min=>value
//		id=>value		-- field id	
/*/
{
	$xDefaultValue="";//default
	$nFieldKey = FALSE;//key
	$xValue="";
	$nFieldId=$cName; //ja id nav padots
	$OK=1;

	if(!empty($aParams))
	{
		//ja 2dimensiju
		if(array_key_exists('key',$aParams))
		{
			$nFieldKey=$aParams["key"];
			$nFieldId.="[".$nFieldKey."]";
		}

		//ja padota default vertiba
		if(array_key_exists('default',$aParams))
			$xDefaultValue=$aParams["default"];
		
		//ja padots lauka id
		if(array_key_exists('id',$aParams))
			$nFieldId=$aParams["id"];
	}
	//dabuj vertibu no $_REQUEST
	$xValue=getReqValue($cName,$nFieldKey);

	if(!empty($aParams))
	{
		$aValues=array_values($aParams);

		foreach($aParams as $k=>$v)
		{
			if($v==="notnull")
			{
				if(!checkNotEmpty($cName,$nFieldId,$xValue))
				{
					$xValue=$xDefaultValue;
					$OK=0;
				}
			}

			if ($v==='null')
			{
				if ($xValue == '0' || $xValue == '')
					return 'NULL';
			}

			if($OK && array_key_exists('max',$aParams))
			{
				if(!checkMax($cName,$nFieldId,$xValue,$aParams["max"]))
					$OK=0;
			}

			if($OK && array_key_exists('min',$aParams))
			{
				if(!checkMin($cName,$nFieldId,$xValue,$aParams["min"]))
					$OK=0;
			}
		}

	}

	if($OK && $xValue!="")
	{
		switch ($cType) 
		{
			case "number":
			if (!checkIfNumeric($cName,$nFieldId,$xValue)) 
			{
				$OK=0;
			}
			break;
			case "date":
			if (!checkDateFormat($cName,$nFieldId,$xValue)) 
			{
				$OK=0;
			}
			break;
			case "time":
			if (!checkTimeFormat($cName,$nFieldId,$xValue))
			{
				$OK=0;
			}
			break;
			case "datetime":
			if (!checkDateTimeFormat($cName,$nFieldId,$xValue))
			{
				$OK=0;
			}
			break;
			case "checkbox":
			if (!checkCb($cName,$nFieldId,$xValue)) 
			{
				$OK=0;
			}
			break;

		} 
	}
	else
		$xValue=$xDefaultValue;

	return $xValue;
}

function getReqValue($cName,$nKey = FALSE)
{
	if($nKey === FALSE)
		$value=(isset($_REQUEST[$cName])?$_REQUEST[$cName]:"");
	else
	{
		//if 2 dimensioanl array
		$value=(isset($_REQUEST[$cName][$nKey])?$_REQUEST[$cName][$nKey]:"");
	}

	if(!is_array($value))
	{
		$value = trim($value);
		$value = Db::$mysqli->real_escape_string($value);
		//$value = htmlspecialchars($value);
		//$value= str_replace('%', '', $value);
	}
		
	if (get_magic_quotes_gpc() == 1) 
	{
		$value = stripslashes($value);
	}
		
	return $value;
}


function checkNotEmpty($cName,$nFieldId,&$xValue)
{
	if (empty($xValue)) 
	{
		$xValue='';
		addMessage(array("code"=>"NOINPUT","fieldname"=>$cName,"fieldid"=>$nFieldId,"fieldvalue"=>$xValue));
		return false;
	}
	return true;
}

function checkMax($cName,$nFieldId,&$xValue,$max)
{
	if(!empty($xValue))
	{
		if (strlen($xValue) > (int) $max) 
		{
			$xValue='';
			addMessage(array("code"=>"TOOBIG","fieldname"=>$cName,"fieldid"=>$nFieldId,"fieldvalue"=>$xValue));
			return false;
		}
	}
	return true;
}

function checkMin($cName,$nFieldId,&$xValue,$max)
{
	if(!empty($xValue))
	{
		if (strlen($xValue) < (int) $max) 
		{
			$xValue='';
			addMessage(array("code"=>"TOOSMALL","fieldname"=>$cName,"fieldid"=>$nFieldId,"fieldvalue"=>$xValue));
			return false;
		}
	}
	return true;
}

function checkIfNumeric($cName,$nFieldId,&$xValue)
{
	if (!is_numeric($xValue)) 
	{
		$xValue='';
		addMessage(array("code"=>"NOTNUMERIC","fieldname"=>$cName,"fieldid"=>$nFieldId,"fieldvalue"=>$xValue));
		return false;
	}
	else
		$xValue=floatval(strip_tags($xValue));

	return true;
}

function float($str, $set=FALSE)
//returns flaot value from string
{           
	if(preg_match("/([0-9\.,-]+)/", $str, $match))
    {
		// Found number in $str, so set $str that number
        $str = $match[0];
          
        if(strstr($str, ','))
        {
			// A comma exists, that makes it easy, cos we assume it separates the decimal part.
            $str = str_replace('.', '', $str);    // Erase thousand seps
            $str = str_replace(',', '.', $str);    // Convert , to . for floatval command
              
            return floatval($str);
         }
         else
         {
            // No comma exists, so we have to decide, how a single dot shall be treated
            if(preg_match("/^[0-9]*[\.]{1}[0-9-]+$/", $str) == TRUE && $set['single_dot_as_decimal'] == TRUE)
            {
				// Treat single dot as decimal separator
                return floatval($str);
                  
            }
            else
            {
				// Else, treat all dots as thousand seps
                $str = str_replace('.', '', $str);    // Erase thousand seps
                return floatval($str);
            }               
		}
	}
    else
    {
		// No number found, return zero
        return 0;
    }
}

function checkTimeFormat($cName,$nFieldId,&$xValue)
{
	if(empty($xValue)) return false;

	// Time jasastav no 2 divciparu skaitljiem, kas atdaliti ar ':' - hh:mm
	$sMsg = '';
	if(strlen($xValue) == 2) $xValue = $xValue.":00";
	if(strlen($xValue) == 1) $xValue = "0".$xValue.":00";
	$nHour = substr($xValue, 0, 2);
	$nMinutes = substr($xValue, 3, 2);

	if( substr($xValue, 2, 1) != ':' )
		$sMsg .= "Time must be in format <b>hh:mm</b>!";
	elseif(strlen($xValue) != 5)
		$sMsg .= "Hour and minutes each must consist from 2 digits (hh:mm)!";
	elseif( ! is_numeric($nHour) ||	! is_numeric($nMinutes) )
		$sMsg .= "Time must be in numeric!";
	else
	{
		if( $nHour<0 || $nHour>23)
			$sMsg .= "Hour must be from 0 to 23!<br/>";
		if( $nMinutes<0 || $nMinutes>59)
			$sMsg .= "Minutes must be from 0 to 59!";
	}

	if($sMsg!='')
	{
		addMessage(array('msg'=>$sMsg,"fieldname"=>$cName,"fieldid"=>$nFieldId,"fieldvalue"=>$xValue));
		return false;
	}
	return true;
}

function checkDateTimeFormat($cName,$nFieldId,&$xValue)
{
	if(empty($xValue)) return false;

	$aDateTime = explode(' ', $xValue);

	if (count($aDateTime) != 2) {
		addMessage(
			array(
				'msg'        => 'DateTime must be in format dd.mm.yyyy hh:mm',
				"fieldname"  => $cName,
				"fieldid"    => $nFieldId,
				"fieldvalue" => $xValue
			)
		);
		$xValue = '';
		return false;
	}

	$dateValue = $aDateTime[0];
	$timeValue = $aDateTime[1];

	if (!checkDateFormat($cName, $nFieldId, $dateValue)) {
		return false;
	}

	if (!checkTimeFormat($cName, $nFieldId, $timeValue)) {
		return false;
	}

	$xValue = $dateValue . ' ' . $timeValue;

	return true;
}

/**
 *	check if input is of a given date format and valid
 *	
 *	does NOT check if format is e.g. 01 or 1
 *	please be careful with year format yy!
 *	0-69 -> 2001/2069 | 70-99 -> 1970/1999
 */
function checkDateFormat($cName,$nFieldId,&$xValue, $format='dd.mm.YYYY', $seperator='.')
{
	$format = strtolower($format);
	
	// check if given seperator is found in dateformat
	if (!stristr($xValue, $seperator))
	{
		$xValue='';
		addMessage(array("msg"=>get_msg_text(1510), "fieldname"=>$cName, "fieldid"=>$nFieldId, "fieldvalue"=>$xValue));
		return 0;
	}
	// TODO: add formats like DD-MM
	/*$arrValidFormats = array('yy' . $seperator . 'mm' . $seperator . 'dd', 
						'yy' . $seperator . 'dd' . $seperator . 'mm', 
						'yyyy' . $seperator . 'mm' . $seperator . 'dd', 
						'yyyy' . $seperator . 'dd' . $seperator . 'mm', 
						'mm' . $seperator . 'dd' . $seperator . 'yy', 
						'mm' . $seperator . 'dd' . $seperator . 'yyyy', 
						'dd' . $seperator . 'mm' . $seperator . 'yy', 
						'dd' . $seperator . 'mm' . $seperator . 'yyyy');
	
	// check if the passed dateformat is valid
	if (!in_array($format, $arrValidFormats)) 
	{
		$xValue='';
		addMessage(array("msg"=>get_msg_text(1520),"fieldname"=>$cName,"fieldid"=>$nFieldId, "fieldvalue"=>$xValue));
		return 0;
	}*/

	if (!preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/', $xValue))
	{
		$xValue='';
		addMessage(array("msg"=>get_msg_text(1520), "fieldname"=>$cName, "fieldid"=>$nFieldId, "fieldvalue"=>$xValue));
		return 0;	
	}

	
	// get the day/month/year and check for validity
	// day depending from month and year between 1 and 28/29/30/31
	// month between 1 and 12
	// year between 0 and 9999
		
	$formParts = explode($seperator, $format);
	$parts = explode($seperator,$xValue);
		
	$order = array(); // yy, mm, dd
	$year_format = '';
	// put the parts in a specific order
	for ($i=0; $i<sizeof($formParts); $i++) {
		switch ($formParts[$i]) {
		case 'yy':
			$order[0] = $parts[$i];
			$year_format = 'yy';
			break;
		case 'yyyy':
			$order[0] = $parts[$i];
			$year_format = 'yyyy';
			break;
		case 'mm':
			$order[1] = $parts[$i];
			break;
		case 'dd':
			$order[2] = $parts[$i];
			break;
		default:
			break;
		}
	}
		
// finally check the day/month/year for validity
		
	$error = false;
		
	if (!checkYear($order[0], $year_format)) 
	{
		$xValue='';
		$error = true;
	}
	if (!checkMonth($order[1])) 
	{
		$xValue='';
		$error = true;
	}
	if (!checkDay($order[2], $order[1], $order[0], $year_format)) 
	{
		$xValue='';
		$error = true;
	}
	
	if ($error) 
	{
		$xValue='';
		addMessage(array("msg"=>get_msg_text(1500), "fieldname"=>$cName,"fieldid"=>$nFieldId, "fieldvalue"=>$xValue));
		return 0;
	}
	return 1;
}

/**
 *	checks for a valid year
 *	@access private
 *	@desc checks for a valid year
 *	@param int $year
 *	@param String $year_format
 *	@return void
 */

function checkYear($year, $year_format)
{
	if (!is_numeric($year)) {
		return false;
	}
	if ($year < 1 || $year > 2037) {
		return false;
	}
	// check, if 4 digits are given when yyyy is used
	if ($year_format == 'yyyy' && (strlen(strval($year)) != 4)) {
		return false;
	}
	return true;
}
	
/**
 *	checks for a valid month
 *	@access private
 *	@desc checks for a valid month
 *	@param int $month
 *	@return void
 */

function checkMonth($month)
{
	if (!is_numeric($month)) {
		return false;
	}
	if ($month < 1 || $month > 12) {
		return false;
	}
	return true;
}
	
/**
 *	checks for a valid day
 *	@access private
 *	@desc checks for a valid day
 *	@param int $day
 *	@param int $month
 *	@param int $year
 *	@param String $year_format
 *	@return void
 */

function checkDay($day, $month, $year, $year_format)
{
	if (!is_numeric($day)) {
		return false;
	}
	if (!checkYear($year, $year_format)) {
		return false;
	}
	if (!checkMonth($month)) {
		return false;
	}
	
	// workaround for timestamp if year is before 1970
	$check = strval($year);
	if ( (strlen($check) == 4) && ($year < 1970) ) {
		$lastDayOfMonth = 31;
	} else { // use timestamp for precise check
		$lastDayOfMonth = date('j', mktime(0,0,0,$month+1,0,$year));
	}
	
	if ($day < 1 || $day > $lastDayOfMonth) {
		return false;
	}
	
	return true;
}

function in_multi_array($search_str, $multi_array)
{
   if(!is_array($multi_array))
       return 0;
   if(in_array($search_str, $multi_array))
       return 1;   
  
   foreach($multi_array as $key => $value)
   {
       if(is_array($value))
       {
           $found = in_multi_array($search_str, $value);
           if($found)
               return 1;
          
       }
       else
       {
           if($key==$search_str)
               return 1;
       }
   }
   return 0;   
}
?>