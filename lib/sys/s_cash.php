<?

// ======= cash_text =======
function cash_text($value)
{
	// ja summa ir miinusaa
	if ($value<0)
	{
		$value=abs($value);  
		$sMinus='- ';
	}
	else
		$sMinus='';

	$outstr='';
	if (floor($value)<=RECS_MAX_LIMIT) 
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
	return $sMinus.$outstr;
}

function cash_tbx($units)
{
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
// end of cash-number-to-text converter


?>