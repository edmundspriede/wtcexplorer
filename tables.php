<?php

function blocks($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams["number"])) $cWhere.=" and number='".$aParams["number"]."' ";
		        if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
                        
                        $nLimit = empty($aParams['limit'])?99999999999:$aParams['limit'];
                        $sOrderBy = empty($aParams['order_by'])? " timestamp DESC " : $aParams['order_by'];
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"b.number, ".
				"b.hash, ".
				"b.miner, ".
				"b.data, ".
                                "b.data_readable, ".
				"b.nonce, ".
				"b.size, ".
				"b.checked, ".
				"b.comment ".
                                
			"FROM blocks b ".
                                
			" WHERE ".$cWhere);

			if (mysql_num_rows($rRes) > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;
	
		case "U":
			if(empty($aParams["number"]))
				addMessage('DB_SYS_ERR');

			$aAParams=array();
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE blocks ".$cSetString.
				" where id='".$aParams["number"]."'");

			return true;
			break;

		case "I":
			
			$aAParams=array("number", "hash" ,"miner" , "data", "nonce" , "size" , "checked" , "comment", "timestamp" );
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("REPLACE into blocks ".$cSetString." ");
			$aResult=array("id"=>mysql_insert_id());

			return true;
			break;
		case "D":
			
			return false;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}



function pools($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and id='".$aParams["id"]."' ";
		        if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
                        
                        $nLimit = empty($aParams['limit'])?99999999999:$aParams['limit'];
                        $sOrderBy = empty($aParams['order_by'])? " id " : $aParams['order_by'];
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"p.id, ".
				"p.name, ".
				"p.miner, ".
				"p.expression, ".
				"p.color, ".
				"p.comment ".
				
                                
			"FROM pools p ".
                                
			" WHERE ".$cWhere);

			if (mysql_num_rows($rRes) > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;
	
		case "U":
			if(empty($aParams["number"]))
				addMessage('DB_SYS_ERR');

			$aAParams=array();
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE blocks ".$cSetString.
				" where id='".$aParams["number"]."'");

			return true;
			break;

		case "I":
			
			$aAParams=array("name", "miner" , "expression" ,  "comment" , "color");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("insert into pools ".$cSetString." ");
			$aResult=array("id"=>mysql_insert_id());
                
			return true;
			break;
		case "D":
			mysql_get("DELETE from pools WHERE id='".$aParams["id"]."'");
			return true;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}