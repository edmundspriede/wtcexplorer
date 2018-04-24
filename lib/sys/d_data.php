<?php

function d_table_name($cAction, $aParams, &$aResult)
{
	$aResult=array();
	switch ($cAction)
	{
		case "S":
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and b.id='".$aParams["id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";

			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";

			$rRes=mysql_get("SELECT ".
				"b.id batch_id, ".
				"b.filename, ".
				"b.status, ".
				"b.merchant_id, ".
				"NULLIF(DATE_FORMAT(b.upload_date,'%d.%m.%Y'),'00.00.0000') upload_date, ".
				"NULLIF(DATE_FORMAT(b.process_date,'%d.%m.%Y'),'00.00.0000') process_date, ".
				"b.file_content, ".
				"b.employee_id ".
			"FROM d_batch b ".
				" WHERE   1=1 and ".$cWhere." order by b.id desc");

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
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$aAParams=array("filename","merchant_id","employee_id");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";

			mysql_get("UPDATE d_batch  ".$cSetString.
				" where id='".$aParams["id"]."'");

			return true;
			break;
		case "I":
			$aAParams=array("filename","status","merchant_id","upload_date","file_content","employee_id");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";

			mysql_get("insert into d_batch ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;

		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			mysql_get("DELETE FROM d_batch WHERE id=".$aParams["id"]." ");
			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function d_account($cAction, $aParams, &$aResult)
{
	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" AND a.id='".$aParams["id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" AND ".$aParams["where"]." ";
			
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
		
			$rRes=mysql_get("SELECT ".
				"a.id, ".
				"a.name, ".
				"a.number, ".
				"a.bank_id, ".
				"b.name bank_name, ".
				"b.swift_code bank_swift_code ".
				"FROM d_account a ".
				"LEFT OUTER JOIN c_bank b ON b.id = a.bank_id ".
				"WHERE ".$cWhere." ORDER BY a.bank_id, a.name");

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

			$aAParams=array("bank_id","number","name");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE d_account  ".$cSetString.
				" where id='".$aParams["id"]."'");

			return true;
			break;
		case "I":
			$aAParams=array("bank_id","number","name");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("INSERT INTO d_account ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;

		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}
			
			mysql_get("DELETE FROM d_account WHERE id=".$aParams["id"]." ");
			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function d_comment($cAction, $aParams, &$aResult)
{
	$aResult=array();
	switch ($cAction)
	{
		case "S":
			$cWhere="1=1";
			if (!empty($aParams["referencing_id"])) $cWhere.=" and c.referencing_id='".$aParams["referencing_id"]."' ";
			if (!empty($aParams["comment_type"])) $cWhere.=" and c.comment_type='".$aParams["comment_type"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";

			$cPrefix = empty($aParams['prefix'])?'':$aParams['prefix'].'_';
			$nSelected = empty($aParams['selected'])?'':$aParams['selected'];

			$rRes=mysql_get("SELECT ".
				"c.id, ".
				"c.comment_type, ".
				"c.user_id, ".
				"NULLIF(DATE_FORMAT(c.comment_date,'%d.%m.%Y'),'00.00.0000') comment_date, ".
				"c.comment_text, ".
				"c.referencing_id, ".
				"c.comment_type_id, ".
				"c.total, ".
				"ct.name comment_type_name, ".
				"ct.short_name comment_type_shortname, ".
				"CONCAT(u.name,' ',u.surname) user_full_name, ".
				"u.login ".
				"FROM d_comment c ".
				"LEFT OUTER JOIN s_user u ON u.id=c.user_id ".
				"LEFT OUTER JOIN c_comment ct on ct.id=c.comment_type_id ".
				" WHERE ".$cWhere." ORDER BY c.comment_date DESC, c.id DESC");

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,$nSelected);
				return true;
			}
			return false;
			break;

		case "I":
			$aParams["comment_date"]=date("d.m.Y");
			$aAParams=array("comment_type", "user_id", "comment_date", "comment_text", "referencing_id", "comment_type_id", "total");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";

			mysql_get("insert into d_comment ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;

		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			mysql_get("DELETE FROM d_comment WHERE id=".$aParams["id"]." ");
			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function d_file($cAction, $aParams, &$aResult)
{
	$aResult=array();
	switch ($cAction)
	{
		case "S":
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and f.id='".$aParams["id"]."' ";
			if (!empty($aParams["referencing_id"])) $cWhere.=" and f.referencing_id='".$aParams["referencing_id"]."' ";
			if (!empty($aParams["reference_type"])) $cWhere.=" and f.reference_type='".$aParams["reference_type"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";

			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";

			$rRes=mysql_get($sSql = "SELECT ".
				"f.id, ".
				"f.referencing_id, ".
				"f.reference_type, ".
				"f.file_type, ".
				"f.filename,".
				"f.description ".
			"FROM d_file f ".
				" WHERE ".$cWhere." order by f.id ");
                        //echo $sSql;
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

		case "I":

			$aAParams=array("referencing_id","reference_type","file_type","description","filename");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";

			mysql_get("INSERT INTO d_file  ".$cSetString."");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;
		case "D":

			if(empty($aParams["id"]))
			{
				addMessage(array("code"=>'DB_SYS_ERR'));
				return false;
			}

			mysql_get("DELETE FROM d_file WHERE id=".$aParams["id"]."");

			$fName=CMS_UPLOADPATH.$aParams["id"].".dat";
			if(file_exists ($fName))
				unlink($fName);

			return true;
			break;
		default:
			addMessage(array("code"=>'DB_SYS_ERR'));
	}
	return false;
}

function d_user_task($cAction, $aParams, &$aResult)
{
	$aResult=array();
	switch ($cAction)
	{
		case 'S':
			$sWhere="1=1";
			if (!empty($aParams["id"])) $sWhere.=" AND ut.id='".$aParams["id"]."' ";
			if (!empty($aParams["user_id"])) $sWhere.=" AND ut.user_id='".$aParams["user_id"]."' ";
			if (!empty($aParams["referencing_id"])) $sWhere.=" AND ut.referencing_id='".$aParams["referencing_id"]."' ";
			if (!empty($aParams["reference_type"])) $sWhere.=" AND ut.reference_type='".$aParams["reference_type"]."' ";
			if (!empty($aParams["where"])) $sWhere.=" AND ".$aParams["where"]." ";

			$sPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";

			$rRes=mysql_get("SELECT ".
				"ut.id, ".
				"ut.user_id, ".
				"NULLIF(DATE_FORMAT(ut.from_date,'%d.%m.%Y'),'00.00.0000') from_date," .
				"NULLIF(DATE_FORMAT(ut.to_date,'%d.%m.%Y'),'00.00.0000') to_date," .
				"ut.referencing_id, ".
				"CONCAT(u.name,' ',u.surname) user_full_name, ".
				"ut.position_id, ".
				"sp.name position_name, ".
				"ut.reference_type ".
				"FROM d_user_task ut ".
				"LEFT OUTER JOIN s_user u ON u.id = ut.user_id ".
				"LEFT OUTER JOIN c_salary_position sp ON sp.id = ut.position_id ".
				" WHERE ".$sWhere." ORDER BY ut.id ");

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$sPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}

			break;
		case 'U':
			if(empty($aParams['id']))
			{
				addMessage(array('code'=>'DB_SYS_ERR'));
				return false;
			}

			$aAParams=array('user_id','from_date','to_date','position_id');
			mysql_get("UPDATE d_user_task SET ".paramsToSet($aParams,$aAParams)." WHERE id='".$aParams["id"]."'");

			return true;
			break;
		case 'I':

			$aAParams=array('user_id','from_date','to_date','referencing_id','reference_type','position_id');
			mysql_get("INSERT INTO d_user_task SET ".paramsToSet($aParams,$aAParams));
			$aResult=array("id"=>Db::$mysqli->insert_id);
			return true;

			break;
		case 'D':

			if(empty($aParams["id"]))
			{
				addMessage(array("code"=>'DB_SYS_ERR'));
				return false;
			}

			mysql_get("DELETE FROM d_user_task WHERE id=".$aParams["id"]."");

			return true;
			break;
		default: die ('bad params');
	}
	return false;
}


function d_dates($cDate='')
{
	$aDates=mysql_get("SELECT ".
		"DATE_FORMAT(CURDATE(),'%d.%m.%Y') cur_day, ".
		"DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -1 Day), '%d.%m.%Y') prev_day, ".
		"DATE_FORMAT(MAKEDATE( YEAR(NOW()),DAYOFYEAR(now())-DAYOFWEEK(now())+2), '%d.%m.%Y') cur_week_first_day, ".
		"DATE_FORMAT(MAKEDATE( YEAR(NOW()),DAYOFYEAR(now())-DAYOFWEEK(now())+8), '%d.%m.%Y') cur_week_last_day, ".
		"DATE_FORMAT(STR_TO_DATE(CONCAT(PERIOD_ADD(DATE_FORMAT(CURDATE(),'%Y%m'),-2),'01'),'%Y%m%d'), '%d.%m.%Y') three_months_first_day, ".
		"DATE_FORMAT(STR_TO_DATE(CONCAT(PERIOD_ADD(DATE_FORMAT(CURDATE(),'%Y%m'),-1),'01'),'%Y%m%d'), '%d.%m.%Y') prev_month_first_day, ".
		"DATE_FORMAT(LAST_DAY(  STR_TO_DATE(CONCAT(PERIOD_ADD(DATE_FORMAT(CURDATE(),'%Y%m'),-1),'01'),'%Y%m%d')   ), '%d.%m.%Y') prev_month_last_day, ".
		"CONCAT('01.01.', DATE_FORMAT(CURDATE(),'%Y')) cur_year_first_day, ".
		"CONCAT('31.12.', DATE_FORMAT(CURDATE(),'%Y')) cur_year_last_day, ".
		"CONCAT('01.01.', DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -1 YEAR),'%Y')) prev_year_first_day, ".
		"CONCAT('31.12.', DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -1 YEAR),'%Y')) prev_year_last_day, ".
		"CONCAT('01.', DATE_FORMAT(CURDATE(),'%m.%Y')) cur_month_first_day, ".
		"DATE_FORMAT(LAST_DAY(CURDATE()), '%d.%m.%Y') cur_month_last_day, ".
        "DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 30 Day), '%d.%m.%Y') default_validity ".           
		"FROM s_user LIMIT 1")->fetch_assoc();

	if(!empty($cDate))
		return $aDates[$cDate];
	else
		return $aDates;
}

function swap_order($cHow,$aParams)
{
	if(empty($aParams["table_name"]) || empty($aParams["id"]))
		die("unable to swap");

	$cWhere=!empty($aParams["where"])?$aParams["where"]:"";



	$rRes=mysql_get("SELECT order_id,id FROM ".$aParams["table_name"]." ".
				" WHERE id=".$aParams["id"]." ".$cWhere."");

	if ($rRes->num_rows==0)
		return false;
	$aObj1=$rRes->fetch_assoc();

	switch ($cHow)
	{
		case "UP":
			$rRes=mysql_get("SELECT order_id,id FROM ".$aParams["table_name"]." ".
						" WHERE order_id<'".$aObj1["order_id"]."' ".$cWhere." order by order_id desc ");
			break;

		case "DOWN":
			$rRes=mysql_get("SELECT order_id,id FROM ".$aParams["table_name"]." ".
						" WHERE order_id>'".$aObj1["order_id"]."' ".$cWhere." order by order_id");
			break;
		default:
			return false;
			break;
	}

	if ($rRes->num_rows==0)
		return false;
	$aObj2=$rRes->fetch_assoc();

	mysql_get("UPDATE ".$aParams["table_name"]." set ".
		"order_id='".$aObj1["order_id"]."' ".
		"WHERE id='".$aObj2["id"]."'");

	mysql_get("UPDATE ".$aParams["table_name"]." set ".
		"order_id='".$aObj2["order_id"]."' ".
		"WHERE id='".$aObj1["id"]."'");


}

function get_order($cWhich,$cTable)
{
	if(empty($cTable))
		die("unable to find max order");

	switch ($cWhich)
	{
		case "MAX":
			$rRes=mysql_get("SELECT max(order_id) order_id FROM ".$cTable." ");
			$aRow=$rRes->fetch_assoc();

			$aRow["order_id"]++;

			break;

		case "MIN":
			$rRes=mysql_get("SELECT min(order_id) order_id FROM ".$cTable." ");
			$aRow=$rRes->fetch_assoc();

			if($aRow["order_id"]>1)
				$aRow["order_id"]--;

			break;
	}

	return $aRow["order_id"];
}

function in_period($sDate)
// ???????????????
{
	if (isset($_SESSION['period_id']))
	{
		$rRes = mysql_get("SELECT IF (STR_TO_DATE('".$sDate."',GET_FORMAT(DATE,'EUR')) >= ".
			"STR_TO_DATE('".$_SESSION['period_from_date']."',GET_FORMAT(DATE,'EUR')) AND ".
			"STR_TO_DATE('".$sDate."',GET_FORMAT(DATE,'EUR')) <= ".
			"STR_TO_DATE('".$_SESSION['period_to_date']."',GET_FORMAT(DATE,'EUR')),'yes','no') in_period ");

		if ($rRes->fetch_row()[0] == 'yes')
			return true;
	}
	return false;
}


function prepare_html(&$aAssoc,$aFields = NULL)
{
	if ($aFields === NULL)
		$aAssoc = array_map("escape_req",$aAssoc);
	else
	{
		foreach ($aAssoc as $k => $v)
		{
			if (in_array($k,$aFields)) $aAssoc[$k] = escape_req($v);
		}
	}
}

function escape_req($val)
{
	if ($val === '') return $val;
	$res = str_replace("\\r\\n","\n",$val); //newlines for textareas
	$res = stripslashes($res);
	return htmlspecialchars($res);
}


function escape_js($val)
{
    if ($val === '') return $val;

    $res = addslashes($val);
    $res = str_replace(array("\r\n","\n"),"\\".'n',$res); //newlines for textareas
    return htmlspecialchars($res);
}

function prepare_js(&$aAssoc,$aFields = NULL)
{
    if ($aFields === NULL)
        $aAssoc = array_map("escape_js",$aAssoc);
    else
    {
        foreach ($aAssoc as $k => $v)
        {
            if (in_array($k,$aFields)) $aAssoc[$k] = escape_js($v);
        }
    }
}

?>