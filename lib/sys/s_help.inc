<?

function s_help($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and h.id='".$aParams["id"]."' ";
			if (!empty($aParams["module_id"])) $cWhere.=" and h.module_id='".$aParams["module_id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"h.id, ".
				"h.module_id, ".
				"h.title, ".
				"m.name module_name, ".
				"d.name desk_name, ".
				"h.order_id ".
			"FROM s_help h,s_module m,s_desk d ".
				" WHERE m.id=h.module_id and m.desk_id=d.id and ".$cWhere." order by h.order_id ");

			if ($rRes->num_rows==0) 
				return false;
			while ($aRow=$rRes->fetch_assoc())
			{
				if(!empty($cPrefix))
					$aRow=addKeyPrefix($aRow,$cPrefix);

				if(!empty($aParams["selected"]))
				{
					if($aRow["id"]==$aParams["selected"]?$aParams["selected"]:"")
						$aRow["selected"]=1;
				}

				$aResult[]=$aRow;
			}

			return true;
			break;
	
		case "U":
			if(empty($aParams["id"]))
				die("no id");
	//				addMessage('DB_SYS_ERR');

			$aAParams=array("module_id","desk_id","order_id","title");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE s_help  ".$cSetString.
				" where id='".$aParams["id"]."'");

			return true;
			break;

		case "I":
			
			$aAParams=array("module_id","desk_id","order_id","title");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("insert into s_help ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;
		case "D":
			if(empty($aParams["id"]))
				die("no id");
						
			mysql_get("DELETE from s_help where id='".$aParams["id"]."'");

			return true;
			break;
		default:
			die('db erro');
			//addMessage('DB_SYS_ERR');
	}
	return false;
}

function s_help_item($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and i.id='".$aParams["id"]."' ";
			if (!empty($aParams["help_id"])) $cWhere.=" and i.help_id='".$aParams["help_id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"i.id, ".
				"i.help_id, ".
				"i.is_pic, ".
				"i.order_id, ".
				"i.item_description ".
			"FROM s_help_item i ".
				" WHERE   1=1 and ".$cWhere." order by order_id");

			if ($rRes->num_rows==0) 
				return false;
			while ($aRow=$rRes->fetch_assoc())
			{
				if(!empty($cPrefix))
					$aRow=addKeyPrefix($aRow,$cPrefix);

				if(!empty($aParams["selected"]))
				{
					if($aRow["id"]==$aParams["selected"]?$aParams["selected"]:"")
						$aRow["selected"]=1;
				}

				$aResult[]=$aRow;
			}

			return true;
			break;
	
		case "U":
			if(empty($aParams["id"]))
				die("no id");
	//				addMessage('DB_SYS_ERR');

			$aAParams=array("help_id","is_pic","item_description");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE s_help_item  ".$cSetString.
				" where id='".$aParams["id"]."'");

			return true;
			break;

		case "I":
			
			$aAParams=array("help_id","is_pic","item_description","order_id");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("insert into s_help_item ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;
		case "D":
			if(empty($aParams["id"]))
				die("no id");
						
			mysql_get("DELETE from s_help_item where id='".$aParams["id"]."'");

			return true;
			break;
		default:
			die('db erro');
			//addMessage('DB_SYS_ERR');
	}
	return false;
}

function s_help_field($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and f.id='".$aParams["id"]."' ";
			if (!empty($aParams["help_id"])) $cWhere.=" and f.help_id='".$aParams["help_id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"f.id, ".
				"f.help_id, ".
				"f.name, ".
				"f.field_type_id, ".
				"f.order_id, ".
				"f.field_description, ".
				"t.name type_name".
			" FROM s_help_field f,s_help_field_type t ".
				" WHERE f.field_type_id=t.id and ".$cWhere." order by f.order_id");

			if ($rRes->num_rows==0) 
				return false;
			while ($aRow=$rRes->fetch_assoc())
			{
				if(!empty($cPrefix))
					$aRow=addKeyPrefix($aRow,$cPrefix);

				if(!empty($aParams["selected"]))
				{
					if($aRow["id"]==$aParams["selected"]?$aParams["selected"]:"")
						$aRow["selected"]=1;
				}

				$aResult[]=$aRow;
			}

			return true;
			break;
	
		case "U":
			if(empty($aParams["id"]))
				die("no id");
	//				addMessage('DB_SYS_ERR');

			$aAParams=array("help_id","name","field_type_id","field_description");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE s_help_field  ".$cSetString.
				" where id='".$aParams["id"]."'");

			return true;
			break;

		case "I":
			
			$aAParams=array("help_id","name","field_type_id","order_id","field_description");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("insert into s_help_field ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;
		case "D":
			if(empty($aParams["id"]) && empty($aParams["help_id"]) )
				die("no id");

			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and id='".$aParams["id"]."' ";
			if (!empty($aParams["help_id"])) $cWhere.=" and help_id='".$aParams["help_id"]."' ";
						
			mysql_get("DELETE from s_help_field where ".$cWhere."");

			return true;
			break;
		default:
			die('db erro');
			//addMessage('DB_SYS_ERR');
	}
	return false;
}

function s_help_field_type($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and t.id='".$aParams["id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"t.id, ".
				"t.name ".
			"FROM s_help_field_type t ".
				" WHERE   1=1 and ".$cWhere." order by t.name");

			if ($rRes->num_rows==0) 
				return false;
			while ($aRow=$rRes->fetch_assoc())
			{
				if(!empty($cPrefix))
					$aRow=addKeyPrefix($aRow,$cPrefix);

				if(!empty($aParams["selected"]))
				{
					if($aRow["id"]==$aParams["selected"]?$aParams["selected"]:"")
						$aRow["selected"]=1;
				}

				$aResult[]=$aRow;
			}

			return true;
			break;
	
		default:
			die('db erro');
			//addMessage('DB_SYS_ERR');
	}
	return false;
}






?>