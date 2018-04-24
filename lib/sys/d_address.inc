<?
//  c_region, c_territory, c_parish
//  !!!! 'cascading' dzeeshana (see "D" cases).
//  Tas noziimee, ka c_parish insertaa obligaati vajag gan region_id, gan territory_id !


function c_region($cAction, $aParams, &$aResult)
{
	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere=" WHERE 1=1 ";
			if (empty($aParams["id"])) 
			{
				if (!empty($aParams["code"])) $cWhere.="AND r.code='".$aParams["code"]."' ";
				if (!empty($aParams["where"])) $cWhere.="AND ".$aParams["where"]." ";
			}
			else
				$cWhere.=" AND r.id='".$aParams["id"]."' ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"r.id, ".
				"r.code, ".
				"r.name ".
				"FROM c_region r ".$cWhere."ORDER BY r.name");

			if ($rRes->num_rows==0) 
				return false;
			while ($aRow=$rRes->fetch_assoc())
			{
				if(!empty($cPrefix))
					$aRow=addKeyPrefix($aRow,$cPrefix);

				if(!empty($aParams["selected"]))
				{
					if($aRow[$cPrefix."id"]==$aParams["selected"]?$aParams["selected"]:"")
						$aRow["selected"]=1;
				}

				$aResult[]=$aRow;
			}
			return true;
			break;
	
		case "U":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$aAParams=array("code","name");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE c_region ".$cSetString.
				" WHERE id='".$aParams["id"]."'");

			return true;
			break;
		case "I":
			$aAParams=array("code","name");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("INSERT INTO c_region ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;

		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}
			
			mysql_get("DELETE FROM c_region WHERE id=".$aParams["id"]." ");
			
			c_territory("D",array("region_id"=>$aParams["id"]),$aRes);

			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}
function c_territory($cAction, $aParams, &$aResult)
{
	// var dzeest gan peec id, gan peec region_id

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere=" WHERE 1=1 ";
			if (empty($aParams["id"])) 
			{
				if (!empty($aParams["region_id"])) $cWhere.="AND t.region_id='".$aParams["region_id"]."' ";
				if (!empty($aParams["code"])) $cWhere.="AND t.code='".$aParams["code"]."' ";
				if (!empty($aParams["where"])) $cWhere.="AND ".$aParams["where"]." ";
			}
			else
				$cWhere.=" AND t.id='".$aParams["id"]."' ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"t.id, ".
				"t.name, ".
				"NULLIF(t.code,0) code, ".
				"t.region_id ".
				"FROM c_territory t ".$cWhere."ORDER BY t.region_id, t.name");

			if ($rRes->num_rows==0) 
				return false;
			while ($aRow=$rRes->fetch_assoc())
			{
				if(!empty($cPrefix))
					$aRow=addKeyPrefix($aRow,$cPrefix);

				if(!empty($aParams["selected"]))
				{
					if($aRow[$cPrefix."id"]==$aParams["selected"]?$aParams["selected"]:"")
						$aRow["selected"]=1;
				}

				$aResult[]=$aRow;
			}
			return true;
			break;
	
		case "U":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$aAParams=array("region_id","name","code");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE c_territory ".$cSetString.
				" WHERE id='".$aParams["id"]."'");

			return true;
			break;
		case "I":
			$aAParams=array("region_id","name","code");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("INSERT INTO c_territory ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;

		case "D":
			if(empty($aParams["id"]))
			{
				if (empty($aParams["region_id"]))
				{
					addMessage('DB_SYS_ERR');
					return false;
				}
				else
				{
					c_parish("D",array("region_id"=>$aParams["region_id"]),$aRes);
					$cWhere="region_id='".$aParams["region_id"]."'";
				}
			}
			else 
			{
				c_parish("D",array("territory_id"=>$aParams["id"]),$aRes);
				$cWhere="id='".$aParams["id"]."'";
			}
			
			mysql_get("DELETE FROM c_territory WHERE ".$cWhere);
			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function c_parish($cAction, $aParams, &$aResult)
{
	// var dzeest peec id, territory_id, region_id

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere=" WHERE 1=1 ";
			if (empty($aParams["id"])) 
			{
				if (!empty($aParams["territory_id"])) $cWhere.="AND p.territory_id='".$aParams["territory_id"]."' ";
				if (!empty($aParams["region_id"])) $cWhere.="AND p.region_id='".$aParams["region_id"]."' ";
				if (!empty($aParams["code"])) $cWhere.="AND p.code='".$aParams["code"]."' ";
				if (!empty($aParams["where"])) $cWhere.="AND ".$aParams["where"]." ";
			}
			else
				$cWhere.=" AND p.id='".$aParams["id"]."' ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"p.id, ".
				"p.code, ".
				"p.name, ".
				"p.region_id, ".
				"p.territory_id ".
				"FROM c_parish p ".$cWhere."ORDER BY p.name");

			if ($rRes->num_rows==0) 
				return false;
			while ($aRow=$rRes->fetch_assoc())
			{
				if(!empty($cPrefix))
					$aRow=addKeyPrefix($aRow,$cPrefix);

				if(!empty($aParams["selected"]))
				{
					if($aRow[$cPrefix."id"]==$aParams["selected"]?$aParams["selected"]:"")
						$aRow["selected"]=1;
				}

				$aResult[]=$aRow;
			}
			return true;
			break;
	
		case "U":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$aAParams=array("code","name","territory_id","region_id");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE c_parish ".$cSetString.
				" WHERE id='".$aParams["id"]."'");

			return true;
			break;
		case "I":
			$aAParams=array("code","name","territory_id","region_id");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("INSERT INTO c_parish ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;

		case "D":
			if(empty($aParams["id"]))
				if(empty($aParams["region_id"]))
					if(empty($aParams["territory_id"]))
					{
						addMessage('DB_SYS_ERR');
						return false;
					}
					else $cWhere="territory_id='".$aParams["territory_id"]."'";
				else $cWhere="region_id='".$aParams["region_id"]."'";
			else $cWhere="id='".$aParams["id"]."'";
			
			mysql_get("DELETE FROM c_parish WHERE ".$cWhere);
			return true;
			break;
		default:
			die ('invalid command');
	}
	return false;
}
?>