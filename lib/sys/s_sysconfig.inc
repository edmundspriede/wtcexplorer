<?


// =============================== class ==============================
class sSysConfig 
{
	function select_custom( $aParams, &$aResult)
	{
		$aResult=array();
		$sIdName="id";

		$cWhere="1=1";
		if (!empty($aParams[$sIdName])) $cWhere.=" and c.".$sIdName."='".$aParams[$sIdName]."' ";
		if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
		if (isset($aParams["parent_id"]))
		{
			if ($aParams["parent_id"]==_NULL)
				$cWhere.=" and c.parent_id = 'null' ";
			else
				$cWhere.=" and c.parent_id='".$aParams["parent_id"]."' ";
		}
	
		$nLimit = empty($aParams['limit'])?RECS_MAX_LIMIT:$aParams['limit'];
		$nOffset = empty($aParams['offset'])?'0':$aParams['offset'];
		$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		$sOrderBy = empty($aParams['order_by'])?"tree_order":$aParams['order_by'];

		$sQuery="SELECT ".
			"c.id, ".
			"c.parent_id, ".
			"c.is_parent, ".
			"c.data_value value, ".
			"c.key_name, ".
			"c.name, ".
			"c.tree_order, ".
			"c.level, ".
			"c.level-1 pad_level, ".
			"c.order_id, ".
			"REPEAT('&nbsp;&nbsp;&nbsp;', c.level-1) padd, ".
			"IF(parent.name is null, c.name, CONCAT(parent.name, ' / ', c.name)) long_name, ".
			"parent.name parent_name ".
		"FROM s_sysconfig c ".
			"LEFT OUTER JOIN s_sysconfig parent ON parent.id=c.parent_id ".
			" ";

		$rRes=mysql_get($sQuery." WHERE ".$cWhere." ORDER BY ".$sOrderBy." LIMIT ".$nOffset.", ".$nLimit);

		if ($rRes->num_rows > 0) 
		{
			$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
			return true;
		}
		return false;
	}

	function isValid($aSave)
	{
		$result=true;

		if (empty($aSave["key_name"]))
		{
			addMessage(array("msg"=>get_msg_text(4200)));
			$result=false;
		}
		else
		{	$sWhere = '';
			if(!empty($aSave["id"]))
				$sWhere = 'id != '.$aSave["id"];
			if(s_sysconfig('S',array('key'=>$aSave["key_name"],'where'=>$sWhere),$aRes))
			{
				addMessage(array("msg"=>get_msg_text(4201)." ".$aRes[0]['name'], "fieldid"=>"key_name"));
				$result=false;
			}
		}

		if (empty($aSave["name"]))
		{
			addMessage(array("msg"=>get_msg_text(4000)));
			$result=false;
		}

		return $result;
	}

	function update ($nId)
	{
		if (empty($nId)) return false;
		
		if (s_sysconfig('S',array('id'=>$nId),$aSelf))
		{
			$sOrder = '';
			$aSelf[0]['level'] = 1;

			// parent
			if (!empty($aSelf[0]['parent_id']) && 
				s_sysconfig('S',array('id'=>$aSelf[0]['parent_id']),$aParent))
			{
				if($aParent[0]['is_parent'] != 1)
					mysql_get("UPDATE s_sysconfig SET is_parent = 1 WHERE id = ".$aParent[0]['id']); 
				$sOrder = $aParent[0]['tree_order'];
				$aSelf[0]['level'] = $aParent[0]['level'] + 1;
			}

			$aSelf[0]['tree_order'] = $sOrder.$aSelf[0]['order_id'].$aSelf[0]['key_name'];
				
			// children
			if (s_sysconfig('S',array('parent_id'=>$aSelf[0]['id']),$aChildren))
				 $aSelf[0]['is_parent'] = 1;
			else $aSelf[0]['is_parent'] = 0;

			s_sysconfig('U',$aSelf[0],$aRes);

			// update children
			if($aSelf[0]['is_parent'] == 1 && !empty($aChildren))
				foreach ($aChildren as $aChild)
					sSysConfig::update($aChild['id']);

			return true;
		}
		return false;
	}

	function save($aSave=false)
	{
		if (empty($aSave["id"]))
		{
			s_sysconfig("I", $aSave, $aTmp);
			$aSave["id"]=$aTmp["id"];
		}
		else
		{
			s_sysconfig("U", $aSave, $aTmp);
		}
		
		sSysConfig::update($aSave['id']);
		return $aSave["id"];
	}

	function remove($nId,$nDelId)
	{
		if (empty($nId) || empty($nDelId)) return false;
		$bError = false;
		$sAttributes = '';
		
		// ja ir child, tad nedzēš
		if (s_sysconfig('S',array('parent_id'=>$nDelId),$aRes)) 
		{
			addMessage(array('msg'=>get_msg_text(4210)));
			return false;
		}

		if (s_sysconfig('S',array('id'=>$nDelId),$aRes))
		{
			$nParentId = $aRes[0]['parent_id'];
			
			// delete 
			if (!$bError && mysql_get("DELETE FROM s_sysconfig WHERE id = '".$nDelId."'",true))
			{
				// ja nav vairaak childu izdzeestaas kategorijas parentam, to vajag uzsetot
				if (!empty($nParentId))
				{
					if (!s_sysconfig('S',array('parent_id'=>$nParentId),$aRes))
						mysql_get("UPDATE s_sysconfig SET is_parent = 0 WHERE id = ".$nParentId);
				}

				// ja dzeesh nevis subcat, bet currento
				if ($nId == $nDelId) s_location(array('id'=>$nParentId));
				else s_location(array('id'=>$nId));
			}
			else
				addMessage(array('msg'=>get_msg_text(4211)));
		}
	}

	function expandTree($nId)
	{
		if (empty($nId))
			return array();
			
		$aChild=array();
		$aParentTree=array();

		//selects children
		if (s_sysconfig("S", array("parent_id"=>$nId),$aTmp))
			foreach ($aTmp as $k=>$v)
				$aChild[]=array("id"=>$v["id"]);

		//if get parents tree
		if (s_sysconfig("S", array("id"=>$nId),$aRow))
		{
			$aParentTree = sSysConfig::expandTree($aRow[0]["parent_id"]);
				
			// itselft if there ar no more parents
			if (count($aParentTree)==0)
				$aParentTree[] = array("id"=>$nId);
		}

		return array_merge($aParentTree,$aChild);
	}

	function aFullPath($nId)
	{
		$aResult = array();
		while(!empty($nId))
		{
			$rRes = mysql_get("SELECT id, parent_id, name FROM s_sysconfig ".
				"WHERE id = '".$nId."'");
			if ($aRow = $rRes->fetch_assoc())
			{
				array_unshift($aResult,$aRow);
				$nId = $aRow['parent_id'];
			}
		}
		return $aResult;
	}

	function move($nId=false, $nNewParentId='NULL')
	{
		if (!$nId) return false;
		if (s_sysconfig('S',array('id'=>$nId),$aSelf))
		{
			mysql_get("UPDATE s_sysconfig SET parent_id = ".$nNewParentId." WHERE id = ".$nId);

			if ($nNewParentId != 'NULL');
				mysql_get("UPDATE s_sysconfig SET is_parent = 1 WHERE id = ".$nNewParentId);

			// old parent
			if (!empty($aSelf[0]['parent_id']) && !s_sysconfig('S',array('parent_id'=>$aSelf[0]['parent_id']),$aRes))
				mysql_get("UPDATE s_sysconfig SET is_parent = 0 WHERE id = ".$aSelf[0]['parent_id']);

			sSysConfig::update($nId);
			return true;
		}
		return false;
	}
}


?>