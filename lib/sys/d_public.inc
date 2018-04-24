<?

function d_public($cAction, $aParams, &$aResult)
{
	$aResult=array();
	$sTableName="d_public";
	$sIdName="id";

	switch ($cAction)
	{
		case "S":
			$cWhere="1=1";
			if (isset($aParams[$sIdName])) $cWhere.=" and ".$sIdName."='".$aParams[$sIdName]."' ";
			if (!empty($aParams["token"])) 	$cWhere.=" and token='".$aParams["token"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";

			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
			$nLimit = empty($aParams['limit'])?99999999999:$aParams['limit'];
			$nOffset = empty($aParams['offset'])?0:$aParams['offset'];

			$rRes=mysql_get("SELECT ".
				"id, ".
				"token, ".
				"params ".
			"FROM ".$sTableName." ".
				" WHERE ".$cWhere." ".
				" LIMIT ".$nOffset.", ".$nLimit);

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;

		case "I":

			$aAParams=array("token", "params");

			mysql_get("INSERT INTO ".$sTableName." SET ".paramsToSet($aParams,$aAParams)." ");
			$aResult=array($sIdName=>Db::$mysqli->insert_id);

			return true;
			break;
		case "U":
			if(empty($aParams[$sIdName]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$aAParams=array("token", "params");

			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE ".$sTableName." ".$cSetString.
				" WHERE ".$sIdName."='".$aParams[$sIdName]."'");

			return true;
			break;
		case "D":

			if(empty($aParams[$sIdName]))
			{
				addMessage(array("code"=>'DB_SYS_ERR'));
				return false;
			}

			mysql_get("DELETE FROM ".$sTableName." WHERE ".$sIdName."=".$aParams[$sIdName]." ");

			return true;
			break;
		default:
			addMessage(array("code"=>'DB_SYS_ERR'));
	}
	return false;
}


// ===== class ====

class dPublic
{
	function isValid($aSave)
	{
		$result=true;

		if (empty($aSave["token"]))
		{
			addMessage(array("msg"=>"Token must be given!"));
			$result=false;
		}

		if (empty($aSave["params"]))
		{
			addMessage(array("msg"=>"Params must be given!"));
			$result=false;
		}

		return $result;
	}

	function save($aSave)
	{

		if (empty($aSave["id"]))
		{

			d_public("I", $aSave, $aTmp);
			$aSave["id"]=$aTmp["id"];
		}
		else
			d_public("U", $aSave, $aTmp);
		
		return $aSave["id"];
	}

	function getToken($sParams)
	// Atgriež Hash funkciju, kur lieto kā token no datajiem parametriem
	{

		if (empty($sParams))
			return false;
			
		if (defined(CMS_SMARTYCHACHE))
			$sKey=CMS_SMARTYCHACHE;
		else
			$sKey="123";

		return md5($sKey.$sParams);
	}
	function setToken($sParams)
	// Izveido hash ierakstu d_public tabulā, pēc kuras publiski var dabūt parametrus 
	{

		if (empty($sParams))
			return false;
		
		$aSave = array();
		$aSave["token"] = self::getToken($sParams);
		$aSave["params"] = $sParams;

		if (d_public("S",array("token"=>$aSave["token"]), $aTmp))
			return $aSave["token"];

		if (!self::isValid($aSave))
			return false;

		self::save($aSave);


	}


}


?>