<?php
/*
* Klasifikatoru apstrādes funkcijas!
* Visām datu apstrādes funkcijām ir kopēji sekojoši principi
* function b_example($cAction, $aParams, &$rResult)
*	$cAction - norāda kāda veida darbība jāveic
		 I - insert, 
		 U - update,  jānorāda id
		 D - delete,  jānorāda id
		 S - select,  jānorāda id vai where, var būt prefix, tiek agriestas arī saistīto klasifikatoru vērtības
*
*	$aParams - parametru masīvs, kurš tiek nodots funkcijai
*		standarta parametri:
*			prefix - ko pielikt atgriežamo lauku priekšā, ja gadījumā lauku nosaukumi var dublēties
*			where - where nosacījums atsevišķiem meklēšanas sarakstiem
	$rResult - rezulāti, var būt gan masīvs gam $res no SQL vaicājuma, ja tiek atgriezti vairāki ieraksti
*/

function c_status($cAction, $aParams, &$aResult)
//ja fjai nepadod id, tad atgriezh pointeri failu sarakstu
{
	switch ($cAction) 
	{
		case "S":
			if(isset($aParams["id"])&&$aParams["id"]>0) 
				$cWhere=" WHERE id='".$aParams["id"]."'";
			else
				$cWhere="";

			$cPrefix=(isset($aParams["prefix"])&&$aParams["prefix"])?$aParams["prefix"]."_":"";

			$rRes=mysql_get("SELECT s.id, s.name, s.order_id ".
			"FROM c_status s ".$cWhere." order by s.order_id");
			
			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
		default:
			die ('bad params');
	}
}

function c_classifier($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and c.id='".$aParams["id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT c.id, c.name, c.order_string FROM c_classifier c ".
				" WHERE 1 AND ".$cWhere." order by c.name");

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;
	
		default:
			die ('bad params');
	}
	return false;
}

function c_field_type($cAction, $aParams, &$aResult)
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
				"t.id, t.name, t.class_name FROM c_field_type t ".
				" WHERE 1 AND ".$cWhere." ORDER BY t.name");

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;
	
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function c_classifier_column($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and f.id='".$aParams["id"]."' ";
			if (!empty($aParams["classifier_id"])) $cWhere.=" and f.classifier_id='".$aParams["classifier_id"]."' ";
			if (!empty($aParams["classifier_name"])) $cWhere.=" and c.name='".$aParams["classifier_name"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"f.id, ".
				"f.title, ".
				"f.field_type_id, ".
				"f.parent_classifier_id, ".
				"f.classifier_id, ".
				"c.name table_name, ".
				"t.name field_type, ".
				"t.class_name field_class ".
			"FROM c_classifier_field f,c_classifier c,c_field_type t ".
				" WHERE t.id=f.field_type_id and c.id=f.classifier_id and ".$cWhere." order by f.title");

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;
	
		case "U":
			if(empty($aParams["id"]))
				addMessage('DB_SYS_ERR');

			$aAParams=array("title","field_type_id","parent_classifier_id","classifier_id");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE c_classifier_field  ".$cSetString.
				" where id='".$aParams["id"]."'");

			return true;
			break;

		case "I":
			
			$aAParams=array("title","field_type_id","parent_classifier_id","classifier_id");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("insert into c_classifier_field ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;
		case "D":
			mysql_get("DELETE from c_classifier_field WHERE id='".$aParams["id"]."'");
			return true;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function c_classifier_field($cAction, $aParams, &$aResult)
{

	$aResult=array();
	
	if(empty($aParams["table_name"]))
		addMessage('DB_SYS_ERR');
	else
		$cTable=$aParams["table_name"];
	
	unset($aParams["table_name"]);
	
	switch ($cAction) 
	{
		case "S": 

			//get table columns
			c_classifier_column("S",array("classifier_name"=>$cTable),$aColumns);

			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and f.id='".$aParams["id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
			
			$cQuery = "SELECT ";

			$cOrderBy="";								
			if(!empty($aColumns))
			{
				foreach($aColumns as $k=>$aColumn)
				{
					$cQuery.="f.".$aColumn["title"].",";
					if ($aColumn["title"]=="order_id")		
						$cOrderBy=" ORDER BY f.name ";	
				}
			}
			$cQuery.="f.id FROM ".$cTable." f WHERE 1 AND ".$cWhere.$cOrderBy;		
			
			$rRes=mysql_get($cQuery);
			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;
	
		case "U":

			if(empty($aParams["id"]))
				addMessage('DB_SYS_ERR');
			else
				$nId=$aParams["id"];
			unset($aParams["id"]);
			unset($aParams["order_id"]);
			
			$cSetString="set ".paramsToSet($aParams,array_keys($aParams))."";
			
			mysql_get("UPDATE ".$cTable."  ".$cSetString.
				" where id='$nId'");

			return true;
			break;

		case "I":
			
			$cSetString="set ".paramsToSet($aParams,array_keys($aParams))."";			
			
			mysql_get("insert into ".$cTable." ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;
		case "D":
			mysql_get("DELETE from ".$cTable." WHERE id='".$aParams["id"]."'");
			return true;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function select_classifier($aParams,&$aResult)
{
	$aResult = array();
	$sPrefix = '';
	$sWhere = ' 1 ';
        if (isset($aParams['prefix'])) $sPrefix = $aParams['prefix'].'_';
	if (isset($aParams[$sPrefix.'id'])) $sWhere .= " AND ".$sPrefix."id= ".$aParams[$sPrefix.'id'];
	if (isset($aParams['where'])) $sWhere .= " AND ".$aParams['where'];
	
        
        //ep selected
        
        if (isset($aParams['selected']) ) {
            
           $selected = ", CASE ".$aParams['selected']['selected_name']. " WHEN '".$aParams['selected']['selected_value']."' THEN 1 ELSE NULL END  selected ";              
        }

	$sQuery = "SELECT * ";
        
        if (isset($selected)) $sQuery .= $selected;
                
        $sQuery .= " FROM ".$aParams['table']." WHERE ".$sWhere;
	if (isset($aParams['order_by'])) $sQuery .= " ORDER BY ".$aParams['order_by'];
	$rRes = mysql_get($sQuery);
	if ($rRes->num_rows > 0)
	{
		$aResult = fetchData($rRes,$sPrefix,'');
	//	while($aRow = $rRes->fetch_assoc()) $aResult[] = $aRow;
		return true;
	}
	return false;
}

function c_unit($cAction, $aParams, &$aResult)
//funkcija atgriezh vienibas
{
	switch ($cAction) 
	{
		case "S":
			
			$cWhere=" 1=1 ";
			if (!empty($aParams["id"])) 
				$cWhere.=" and u.id=".$aParams["id"];
							   
			$cPrefix=!empty($aParams["prefix"])?$aParams["prefix"]."_":"";

			$rRes=mysql_get("SELECT ".
				"u.id , u.name FROM c_unit u WHERE ".$cWhere);

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
		break;

		default:
			die("Invalid command");
	}
	return false;
}

function c_bank_branch($cAction, $aParams, &$aResult)
{
	// var dzeest gan peec id, gan bank_id

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere=" WHERE 1=1 ";
			if (empty($aParams["id"])) 
			{
				if (!empty($aParams["bank_id"])) $cWhere.="AND bb.bank_id='".$aParams["bank_id"]."' ";
				if (!empty($aParams["where"])) $cWhere.="AND ".$aParams["where"]." ";
			}
			else
				$cWhere.=" AND bb.id='".$aParams["id"]."' ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"bb.id, bb.bank_id, bb.swift_code, bb.name ".
				"FROM c_bank_branch bb ".$cWhere."ORDER BY bb.name");

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;
	
		case "U":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$aAParams=array("bank_id","name","swift_code");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE c_bank_branch ".$cSetString.
				" WHERE id='".$aParams["id"]."'");

			return true;
			break;
		case "I":
			$aAParams=array("bank_id","name","swift_code");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("INSERT INTO c_bank_branch ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;

		case "D":
			if(empty($aParams["id"]))
				if(empty($aParams["bank_id"]))
				{
					addMessage('DB_SYS_ERR');
					return false;
				}
				else $cWhere="bank_id=".$aParams["bank_id"];
			else $cWhere="id=".$aParams["id"];
			
			mysql_get("DELETE FROM c_bank_branch WHERE ".$cWhere);
			

			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function c_bank($cAction, $aParams, &$aResult)
{
	// var dzeest gan peec id, gan bank_id

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere=" WHERE 1=1 ";
			if (empty($aParams["id"])) 
			{
				if (!empty($aParams["where"])) $cWhere.="AND ".$aParams["where"]." ";
			}
			else
				$cWhere.=" AND b.id='".$aParams["id"]."' ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"b.id, ".
				"b.swift_code, ".
				"b.name ".
				"FROM c_bank b ".$cWhere."ORDER BY b.name");

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;
	
		case "U":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$aAParams=array("name","swift_code");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE c_bank ".$cSetString.
				" WHERE id='".$aParams["id"]."'");

			return true;
			break;
		case "I":
			$aAParams=array("name","swift_code");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("INSERT INTO c_bank ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;

		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}
		
			mysql_get("DELETE FROM c_bank WHERE id=".$aParams["id"]);
		
			c_bank_branch("D",array("bank_id"=>$aParams["id"]),$aRes);
			
			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

// ??????????
function c_cost($cAction, $aParams, &$aResult)
//funkcija atgriezh vienibas
{
	switch ($cAction) 
	{
		case "S":
			
			$cWhere=" 1=1 ";
			if (!empty($aParams["id"])) $cWhere.=" and c.id=".$aParams["id"];
			if (!empty($aParams["position_id"])) $cWhere.=" and c.position_id=".$aParams["position_id"];
							   
			$cPrefix=!empty($aParams["prefix"])?$aParams["prefix"]."_":"";
			$nSelected = empty($aParams['selected'])?'':$aParams['selected'];

			$rRes=mysql_get("SELECT c.id, c.position_id, c.name FROM c_cost c where ".$cWhere);

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,$nSelected);
				return true;
			}
			return false;
		break;

		default:
			die("Invalid command");
	}
	return false;
}

// ??????????
function c_tame_position($cAction, $aParams, &$aResult)
{
	switch ($cAction) 
	{
		case "S":
			
			$cWhere=" 1=1 ";
			if (!empty($aParams["id"])) 
				$cWhere.=" and p.id=".$aParams["id"];
							   
			$cPrefix=!empty($aParams["prefix"])?$aParams["prefix"]."_":"";
			$nSelected = empty($aParams['selected'])?'':$aParams['selected'];

			$rRes=mysql_get("SELECT p.id, p.name FROM c_tame_position p where ".$cWhere);

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,$nSelected);
				return true;
			}
			return false;
		break;

		default:
			die("Invalid command");
	}
	return false;
}

function c_state($cAction, $aParams, &$aResult)
{
	switch ($cAction) 
	{
		case "S":
			if(isset($aParams["id"])&&$aParams["id"]>0) 
				$cWhere=" WHERE id='".$aParams["id"]."'";
			else
				$cWhere="";

			$cPrefix=(isset($aParams["prefix"])&&$aParams["prefix"])?$aParams["prefix"]."_":"";

			$rRes=mysql_get("SELECT ".
						"id, ".
						"name ".
						"FROM c_state ".$cWhere." ".
						"ORDER by id ");
			
			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
		default:
			die ('bad params');
	}
}

?>