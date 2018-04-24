<?
define ('REF_TMPL_CSS',6);
define ('REF_TMPL_HTML',7);

function s_template($cAction, $aParams, &$aResult)
{
	$aResult=array();
	$sIdName = 'id';
	$sTable = 's_template';
	switch ($cAction) 
	{
		case 'S': 
			$sWhere = ' 1 ';
			if (isset($aParams[$sIdName])) $sWhere.=" AND ".$sIdName."='".$aParams[$sIdName]."' ";
			if (isset($aParams['name'])) $sWhere.=" AND t.name = '".$aParams['name']."' ";
			if (isset($aParams['where'])) $sWhere.=" AND ".$aParams["where"]." ";
			
			$nLimit = empty($aParams['limit'])?RECS_MAX_LIMIT:$aParams['limit'];
			$nOffset = empty($aParams['offset'])?0:$aParams['offset'];
			$sPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
			$nSelected = empty($aParams['selected'])?'':$aParams['selected'];
			$sOrderBy = empty($aParams['order_by'])?"name":$aParams['order_by'];
		
			$sQuery = "SELECT t.id, t.name, t.description FROM ".$sTable." t";
				
			$rRes=mysql_get($sQuery." WHERE ".$sWhere." ORDER BY ".$sOrderBy." LIMIT ".$nOffset.", ".$nLimit);

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$sPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
		break;
		case 'U':

			if(empty($aParams[$sIdName]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$cSetString=" SET ".paramsToSet($aParams,array('name','description'));
			mysql_get("UPDATE ".$sTable.$cSetString." WHERE ".$sIdName."='".$aParams[$sIdName]."'");

			return true;
		break;

		case 'I':

			$cSetString=" SET ".paramsToSet($aParams,array('name','description'));
			mysql_get("INSERT INTO ".$sTable.$cSetString);
			$aResult=array($sIdName=>Db::$mysqli->insert_id);

			return true;
		break;

		case "D":
			if(empty($aParams[$sIdName]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}
			
			mysql_get("DELETE FROM ".$sTable." WHERE ".$sIdName."='".$aParams[$sIdName]."' ");
			return true;
			break;

		default: die ('bad param in '.$sTable);
	}
	return false;
}

function s_template_item($cAction, $aParams, &$aResult)
{
	$aResult=array();
	$sIdName = 'id';
	$sTable = 's_template_item';
	switch ($cAction) 
	{
		case 'S': 
			$sWhere = ' 1 ';
			if (isset($aParams[$sIdName])) $sWhere.=" AND ".$sIdName."='".$aParams[$sIdName]."' ";
			if (isset($aParams['template_id'])) $sWhere.=" AND ti.template_id = '".$aParams['template_id']."' ";
			if (isset($aParams['where'])) $sWhere.=" AND ".$aParams["where"]." ";
			
			$nLimit = empty($aParams['limit'])?RECS_MAX_LIMIT:$aParams['limit'];
			$nOffset = empty($aParams['offset'])?0:$aParams['offset'];
			$sPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
			$nSelected = empty($aParams['selected'])?'':$aParams['selected'];
			$sOrderBy = empty($aParams['order_by'])?"ti.add_time DESC":$aParams['order_by'];
		
			$sQuery = "SELECT ".
				"ti.id, ".
				"NULLIF(DATE_FORMAT(ti.add_time,'%d.%m.%Y %H:%i:%s'),'00.00.0000 00:00') add_time, ".
				"ti.template_id, ".
				"ti.tmpl_type, ".
				"ti.is_active, ".
				"u.login ".
				"FROM ".$sTable." ti ".
				"LEFT JOIN s_user u ON u.id = ti.user_id ".
				
			$rRes=mysql_get($sQuery." WHERE ".$sWhere." ORDER BY ".$sOrderBy." LIMIT ".$nOffset.", ".$nLimit);

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$sPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
		break;
		case 'U':

			if(empty($aParams[$sIdName]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$cSetString=" SET ".paramsToSet($aParams,array('template_id','tmpl_type','is_active'));
			mysql_get("UPDATE ".$sTable.$cSetString." WHERE ".$sIdName."='".$aParams[$sIdName]."'");

			return true;
		break;

		case 'I':

			$cSetString=" SET add_time = NOW(), ".paramsToSet($aParams,array('template_id','tmpl_type','is_active','user_id'));
			mysql_get("INSERT INTO ".$sTable.$cSetString);
			$aResult=array($sIdName=>Db::$mysqli->insert_id);

			return true;
		break;

		case 'D':
			if(empty($aParams[$sIdName]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}
			
			mysql_get("DELETE FROM ".$sTable." WHERE ".$sIdName."='".$aParams[$sIdName]."' ");
			return true;
			break;

		default: die ('bad param in '.$sTable);
	}
	return false;
}

class sTemplate
{
	function is_unique_name($nId,$sName)
	{
		if (s_template('S',array('name'=>$sName),$aRes))
		{
			if (empty($nId)) return false;
			else return ($nId == $aRes[0]['id']);
		}
		else
			return true;
	}

	function get_items($nTemplateId,$nType)
	{
		$aResult = array();
		$rRes = mysql_get("SELECT ti.id, ".
			"NULLIF(DATE_FORMAT(ti.add_time,'%d.%m.%Y %H:%i:%s'),'00.00.0000 00:00') add_time, ".
			"ti.is_active, f.id file_id, f.filename, u.login ".
			"FROM s_template_item ti LEFT JOIN d_file f ON f.reference_type = ".$nType.
			" AND f.referencing_id = ti.id ".
			"LEFT JOIN s_user u ON u.id = ti.user_id ".
			"WHERE ti.tmpl_type = ".$nType." AND ti.template_id ='".$nTemplateId."' ".
			"ORDER BY ti.add_time DESC");
		if ($rRes->num_rows > 0) while ($aRow = $rRes->fetch_assoc()) $aResult[] = $aRow;
		return $aResult;
	}

	function remove_item($nId)
	{
		if (s_template_item('S',array('id'=>$nId),$aRes))
		{
			$aParams['referencing_id'] = $nId;
			$aParams['reference_type'] = $aRes[0]['tmpl_type'];

			if (d_file('S',$aParams,$aFiles))
				foreach($aFiles as $v) d_file('D',array('id'=>$v['id']),$aRes);
			s_template_item('D',array('id'=>$nId),$aRes);		
		}
	}

	function remove($nId)
	{
		if (s_template_item('S',array('template_id'=>$nId),$aRes))
			foreach ($aRes as $v) sTemplate::remove_item($v['id']);
		s_template('D',array('id'=>$nId),$aRes);
	}
}
?>