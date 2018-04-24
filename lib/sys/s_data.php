<?php
function s_location($aParams = array())
{
	$aGet=$aParams;
	if (empty($aParams['mod']))	$aGet['mod']=S_THIS_MODULE;

	$sURL="/?";
	$bFirst=true;
	foreach ($aGet as $k=>$v)
		if ($bFirst)
		{
			$sURL.=$k."=".$v;
			$bFirst=false;
		}
		else $sURL.="&".$k."=".$v;
	header("Location: ".$sURL);
	exit;
}

function s_module_log($cAction, $aParams, &$aResult)
{
	switch ($cAction)
	{
		case "I":
			if(LOG_INSERT==1)
			{
				mysql_get("INSERT INTO s_module_log set ".
					"log_type ='".$aParams["log_type"]."', ".
					"module_id ='".$aParams["module_id"]."', ".
					"message ='".$aParams["message"]."', ".
					"duration ='".$aParams["duration"]."', ".
					"size ='".$aParams["size"]."', ".
					"log_date =NOW() ".
					"");
				return true;
			}
			else
				return false;
		break;
		default:
			die("Invalid command");
	}
	return false;
}

function s_perm($cAction,$aParams,&$aResult)
{
	switch($cAction)
	{
		case "S":

			$aResult=array();
			$cWhere=(empty($aParams["role_id"]))?"":"WHERE role_id=".$aParams["role_id"]." ";
			$cPrefix=(empty($aParams["prefix"]))?"":$aParams["prefix"]."_";
			$rRes=mysql_get("SELECT ".
				"p.id perm_id, ".
				"p.role_id, ".
				"p.module_id perm_module_id, ".
				"p.perm_type, ".
				"m.id module_id, ".
				"m.desk_id, ".
				"m.parent_id, ".
				"d.id  desk_desk_id, ".
				"d.name desk_name, ".
				"m.name module_name ".
				"FROM s_module m ".
				"LEFT OUTER JOIN s_perm p ON p.module_id=m.id and p.role_id='".$aParams["role_id"]."' ".
				"LEFT OUTER JOIN s_desk d ON d.id=m.desk_id ".
				"ORDER BY m.desk_id,m.parent_id ");

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
		break;

		case "I":

			$aAParams=array("role_id","module_id","perm_type");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("INSERT INTO s_perm  ".$cSetString);
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;

		break;

		case "U":

			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$aAParams=array("role_id","module_id","perm_type");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("UPDATE s_perm  ".$cSetString." WHERE id='".$aParams["id"]."'");

			return true;

		break;

		case "D":

			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			mysql_get("DELETE FROM s_perm WHERE id=".$aParams["id"]." ");
			return true;

		break;

		default: die ('invalid command');
	}
}

function s_role($cAction, $aParams, &$aResult)
{
	// Māris Cakuls 25.04.2006

	$aResult=array();
	switch ($cAction)
	{
		case 'S':
			$cWhere='1';
			if (!empty($aParams['id'])) $cWhere.=" AND r.id='".$aParams['id']."' ";
			if (!empty($aParams['where'])) $cWhere.=" AND ".$aParams['where']." ";

			$cPrefix = empty($aParams['prefix'])?'':$aParams['prefix'].'_';
			$sOrderBy = empty($aParams['order_by'])?'r.name':$aParams['order_by'];

			$rRes=mysql_get("SELECT ".
				"r.id, r.name, r.status, r.comment ".
				"FROM s_role r ".
				"WHERE ".$cWhere." ORDER BY ".$sOrderBy);

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

			$aAParams=array('name','status','comment');

			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("UPDATE s_role ".$cSetString." WHERE id='".$aParams["id"]."'");

			return true;
		break;
		case "I":
			$aAParams=array('name','status','comment');
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("INSERT INTO s_role ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
		break;
		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			mysql_get("DELETE FROM s_role WHERE id=".$aParams["id"]." ");
			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function s_menu($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction)
	{
		case "S":
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and m.id='".$aParams["id"]."' ";
			if (!empty($aParams["parent_id"])) $cWhere.=" and m.parent_id='".$aParams["parent_id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";

			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]:"";

			$rRes=mysql_get("SELECT ".
				"DISTINCT(m.id), ".
				"m.name, ".
				"m.top_name, ".
				"m.sub_name, ".
				"m.scriptname, ". //
				"m.desk_id, ". //
				"m.tmpl_name, ". //
				"m.hidden, ".
				"m.order_id, ".
				"m.parent_id ".
			"FROM s_module m,s_perm p,s_user_role ur ".
				" WHERE p.module_id=m.id and ur.user_id='".$_SESSION["user_id"]."'and ur.role_id=p.role_id and ".$cWhere." order by m.order_id ");

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;

		default:
			die('DB_SYS_ERR');
	}
	return false;
}

function is_module_active($moduleCode) {
    $m = null;
    return s_module('S', ['where' => "(m.code = '$moduleCode' AND m.status = 1)"], $m);
}

function s_module($cAction, $aParams, &$aResult)
{
	switch ($cAction)
	{
		case "S":

			$aResult=array();

			$cWhere="WHERE 1=1 ";
			$cOrder=(empty($aParams["order_by"]))?"ORDER BY m.order_id":"ORDER BY ".$aParams["order_by"];
			$cWhere.=(empty($aParams["id"]))?"":"AND m.id=".$aParams["id"]." ";
			$cWhere.=(empty($aParams["where"]))?"":"AND ".$aParams["where"]." ";
			$cPrefix=(isset($aParams["prefix"]))?$aParams["prefix"]."_":"";


			$rRes=mysql_get("SELECT ".
				"m.id, ".
				"m.name, ".
				"m.desk_id, ".
				"m.status, ".
				"m.hidden, ".
				"m.scriptname, ".
				"m.tmpl_name, ".
				"m.top_name, ".
				"m.sub_name, ".
				"m.order_id, ".
				"NULLIF(m.parent_id,0) parent_id, ".
				"m.code, ".
				"m.comment, ".
				"d.id desk_id, ".
				"d.name desk_name, ".
				"d.status desk_status ".
				"FROM s_module m ".
				"LEFT OUTER JOIN s_desk d ON d.id = m.desk_id ".$cWhere.$cOrder);

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
		break;
		case "I":

			$aAParams=array("id","name","desk_id","status","scriptname","tmpl_name","top_name",
							"sub_name","parent_id","scriptname","comment","hidden", "order_id","code");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";

			mysql_get("insert into s_module ".$cSetString." ");
			$aResult["id"]=	Db::$mysqli->insert_id;
			return true;

		break;
		case "U":

			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$aAParams=array("name","desk_id","status","scriptname","tmpl_name","top_name",
							"sub_name","parent_id","scriptname","comment","hidden", "order_id","code");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";

			mysql_get("UPDATE s_module  ".$cSetString." where id='".$aParams["id"]."'");
			return true;

		break;
		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}
			return mysql_get("DELETE FROM s_module WHERE id=".$aParams["id"]." ", 1);
			return true;
		break;
		default:
			die('invalid command');
	}
	return false;
}





function s_desk($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction)
	{
		case "S":
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and d.id='".$aParams["id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";

			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";

			$rRes=mysql_get("SELECT ".
				"d.id, ".
				"d.name, ".
				"d.STATUS, ".
				"d.COMMENT ".
			"FROM s_desk d ".
				" WHERE 1=1 and ".$cWhere." order by d.name ");

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
		break;
		default:
			die('bad params');
	}
	return false;
}


function s_office($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction)
	{
		case "S":
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and o.id='".$aParams["id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";

			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";

			$rRes=mysql_get("SELECT ".
				"o.id, ".
				"o.name, ".
				"o.registered_name, ".
				"o.bank_id, ".
				"o.account_no, ".
				"o.state, ".
				"o.adress, ".
				"o.phone, ".
				"o.fax, ".
				"o.email, ".
				"o.manager_id, ".
				"o.code ".
				"FROM s_office o WHERE ".$cWhere." order by o.name ");

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;

		case "I":

			$aAParams=array("name","registered_name", "bank_id","name","account_no","state","adress", "phone", "fax", "email", "manager_id", "code");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";
			mysql_get("INSERT INTO s_office ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);
			return true;

		break;

		case "U":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			$aAParams=array("name","registered_name", "bank_id","name","account_no","state","adress", "phone", "fax", "email", "manager_id", "code");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";

			mysql_get("UPDATE s_office  ".$cSetString.
				" WHERE id='".$aParams["id"]."'");

			return true;
			break;

		default:
			die('DB_SYS_ERR');
	}
	return false;
}


function s_user_role($cAction,$aParams,&$aResult)
{
	switch($cAction)
	{
		case "S":
			$cWhere="WHERE 1=1 ";
			$cOn="";
			$aResult=array();

			$cPrefix=empty($aParams["prefix"])?"":$aParams["prefix"]."_";

			if (empty($aParams["id"]))
			{
				if(!empty($aParams["role_id"]) && $aParams["role_id"]>0)
					$cWhere.="AND ur.role_id=".$aParams["role_id"]." ";

				if(!empty($aParams["user_id"]) && $aParams["user_id"]>0)
					$cOn.="AND ur.user_id=".$aParams["user_id"]." ";
				else
					$cOn.="AND (ur.user_id IS NULL OR ur.user_id=0) ";
			}
			else
			{
				$cWhere.="AND ur.id=".$aParams["id"]." ";
			}

			$cQuery=("SELECT ".
				"ur.id, ".
				"ur.user_id, ".
				"r.id role_id, ".
				"r.name role_name, ".
				"r.status role_status, ".
				"r.comment role_comment ".
				"FROM s_role r ".
				"LEFT OUTER JOIN s_user_role ur ".
					"ON r.id=ur.role_id ".$cOn.$cWhere);
			$rRes=mysql_get($cQuery);

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;

		break;

		case "I":

			$aAParams=array("role_id","user_id");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";
			mysql_get("INSERT INTO s_user_role ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);
			return true;

		break;

		case "D":

			// izdzeesham peec viena no parametriem
			$cWhere="WHERE ";
			if (empty($aParams["id"]))
				if(empty($aParams["user_id"]))
					if(empty($aParams["role_id"])) die('no params');
					else $cWhere.="role_id=".$aParams["role_id"];
				else $cWhere.="user_id=".$aParams["user_id"];
			else $cWhere.="id=".$aParams["id"];
			mysql_get("DELETE FROM s_user_role ".$cWhere);
			return true;
		break;

		default: die('invalid command');
	}
}

function s_user_office($cAction,$aParams,&$aResult)
{
	// ja padod user_id caur params, atgriez, kuros ofisos ir/nav useris
	// ja pados user_id caur where UN params, atgiez ofisus, kuros ir useris
	// ja paramos nav user id, atgriez ofisus, kuros nav neviena usera
	// ( office LEFT JOIN user_office ON __user_id__ )

	switch ($cAction)
	{
		case "S":
			$aResult=array();

			$cPrefix=empty($aParams["prefix"])?"":$aParams["prefix"]."_";
			$cWhere="WHERE 1=1 ";
			$cWhere.=empty($aParams["where"])?"":$aParams["where"];
			$cOn="";
			$sOrderBy=empty($aParams['order_by'])?'o.name':$aParams['order_by'];

			if (empty($aParams["id"]))
			{
				if(!empty($aParams["office_id"]) && $aParams["office_id"]>0)
					$cWhere.="AND uo.office_id=".$aParams["office_id"]." ";

				if(!empty($aParams["user_id"]) && $aParams["user_id"]>0)
					$cOn.="AND uo.user_id=".$aParams["user_id"]." ";
				else
					$cOn.="AND (uo.user_id IS NULL OR uo.user_id=0) ";
			}
			else
			{
				$cWhere.="AND ur.id=".$aParams["id"]." ";
			}

			$rRes=mysql_get("SELECT ".
				"uo.id, ".
				"uo.user_id, ".
				"o.id office_id, ".
				"o.name office_name, ".
				"o.registered_name office_registered_name, ".
				"o.state office_state, ".
				"o.adress office_address, ".
				"o.phone office_phone, ".
				"o.fax office_fax, ".
				"o.email office_email, ".
				"o.manager_id office_manager_id, ".
				"o.code office_code ".
				"FROM s_office o ".
				"LEFT OUTER JOIN s_user_office uo ".
					"ON o.id = uo.office_id ".$cOn.$cWhere.' ORDER BY '.$sOrderBy);

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;

		break;

		case "I":

			$aAParams=array("office_id","user_id");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";
			mysql_get("INSERT INTO s_user_office ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);
			return true;

		break;

		case "D":

			// izdzeesham peec viena no parametriem
			$cWhere="WHERE ";
			if (empty($aParams["id"]))
				if(empty($aParams["user_id"]))
					if(empty($aParams["office_id"])) die('no params');
					else $cWhere.="office_id=".$aParams["office_id"];
				else $cWhere.="user_id=".$aParams["user_id"];
			else $cWhere.="id=".$aParams["id"];
			mysql_get("DELETE FROM s_user_office ".$cWhere);
			return true;

		break;

		default: die('invalid command');
	}
}

function s_user_event($cAction,$aParams,&$aResult)
{
   switch ($cAction)
   {
       case "S":

           $aResult=array();
           $cPrefix=empty($aParams["prefix"])?"":$aParams["prefix"]."_";
           $nLimit=empty($aParams["limit"])?40:$aParams["limit"];
			$sOrderBy = empty($aParams['order_by'])?'ue.event_datetime DESC, ue.cnt DESC':$aParams['order_by'];
           if (empty($aParams["event_type"])) die ('no params');
			
		   if (empty($aParams["user_id"]))
			   $nUserId = $_SESSION["user_id"];
		   else
			   $nUserId = $aParams["user_id"];

           switch($aParams["event_type"])
           {
               case 1: // object
                   $cQuery="SELECT ".
                       "ue.cnt, ".
						"NULLIF(DATE_FORMAT(ue.event_datetime,'%d.%m.%Y'),'00.00.0000') event_datetime, ".
						"t.name object_type_name, ".
						"t.short_name type_short_name, ".
                       "d.id object_id, ".
                       "d.number object_number, ".
						"NULLIF(DATE_FORMAT(d.due_date,'%d.%m.%Y'),'00.00.0000') due_date, ".
						"IF(DATEDIFF(NULLIF(d.due_date,'00.00.0000'),CURDATE())<0, 1,0) expired, ".
						"DATEDIFF(NULLIF(d.due_date,'00.00.0000'),CURDATE()) date_diff, ".
                       "d.name ".
                       "FROM s_user_event ue ".
						"LEFT OUTER JOIN d_object d on d.id=ue.referencing_id ".
						"LEFT OUTER JOIN c_object t on t.id=d.object_type_id ".
                       "WHERE ue.event_type=".D_EVENT_OBJECT." ".
                         "AND ue.user_id=".$nUserId." ";
                   $sOrderBy.=", d.id ";
               break;
               case 2: // client
                   $cQuery="SELECT ".
                       "ue.cnt, ".
						"NULLIF(DATE_FORMAT(ue.event_datetime,'%d.%m.%Y'),'00.00.0000') event_datetime, ".
                       "c.id client_id, ".
                       "c.registration_no, ".
                       "c.name ".
                       "FROM s_user_event ue ".
						"LEFT OUTER JOIN d_client c on c.id=ue.referencing_id ".
                       "WHERE ue.event_type=".D_EVENT_CLIENT." ".
                         "AND ue.user_id=".$nUserId." ";
                   $sOrderBy.=", c.name ";
               break;
               default: die ('invalid params');
           }

           $cQuery.="ORDER BY ".$sOrderBy." LIMIT ".$nLimit;

           $rRes=mysql_get($cQuery);
		   
			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
			break;

       case "I":
           if (empty($aParams["user_id"]) || empty($aParams["event_type"]) || empty($aParams["referencing_id"]))
               die('missing params');

           // check if there is such record already
           $rRes=mysql_get("SELECT id FROM s_user_event WHERE ".
                   "user_id=".$aParams["user_id"]." AND ".
                   "referencing_id=".$aParams["referencing_id"]." AND ".
                   "event_type=".$aParams["event_type"]);

			$aParams["event_datetime"]=date("d.m.Y");
           if ($rRes->num_rows==0) // insert new if none
           {
			$aParams["cnt"]=1;
			$aAParams=array("user_id","event_type","referencing_id","cnt","event_datetime");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";

			mysql_get("insert into s_user_event ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);
           }
           else // update the existing one
           {
			$aAParams=array("event_datetime");
			$cSetString="SET cnt=cnt+1, ".paramsToSet($aParams,$aAParams)."";

               $aRow=$rRes->fetch_assoc();
			mysql_get("UPDATE s_user_event ".$cSetString." WHERE id='".$aRow['id']."'");

//               mysql_get("UPDATE s_user_event SET cnt=cnt+1, event_datetime='".$aParams["event_datetime"]."' WHERE id=".$aRow["id"]);
           }

           return true;
       break;
       case "D":

           if(empty($aParams["id"]))
           {
               addMessage('DB_SYS_ERR');
               return false;
           }
           mysql_get("DELETE FROM s_user_event WHERE id=".$aParams["id"]." ");
           return true;
       break;
       default: die ('invalid command');
   }
}

function s_office_user($cAction,$aParams,&$aResult)
{
	$aResult=array();
	switch ($cAction)
	{
		case "S":
			$cWhere="1=1";
			if (!empty($aParams["office_id"])) $cWhere.=" and uo.office_id='".$aParams["office_id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";

			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";

			$rRes=mysql_get("SELECT ".
				"u.id, ".
				"CONCAT(u.name,' ',u.surname) user_name ".
			"FROM s_user_office uo,s_user u ".
				" WHERE ".$cWhere." and u.id=uo.user_id order by u.name");

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

function s_user_perm($cAction,$aParams,&$aResult)
{
	// atgriezh moduljus, kuri ir pieejami lietotaajam no SESSION
	// ja padod module_id, atgriezh permission tipu (perm_type)
	switch($cAction)
	{
		case "S":

			$aResult=array();
			$cWhere="WHERE ur.user_id=".$_SESSION["user_id"];
			$cPrefix=empty($aParams["prefix"])?"":$aParams["prefix"]."_";

			if (empty($aParams["module_id"]))
			{
				$cColumn="DISTINCT p.module_id";
			}
			else
			{
				$cColumn=" MAX(p.perm_type) perm_type";
				$cWhere.=" AND module_id=".$aParams["module_id"];
			}

			$rRes=mysql_get("SELECT ".$cColumn.
					" FROM s_user_role ur ".
					"LEFT JOIN s_perm p ON p.role_id=ur.role_id ".$cWhere);
			if ($rRes->num_rows<1)
				return false;

			if (empty($aParams["module_id"]))
			{
				while($aRow=$rRes->fetch_assoc())
				{
					if (!empty($aRow["module_id"]))	$aResult[]=$cPrefix.$aRow["module_id"];
				}
			}
			else
			{
				$aRow=$rRes->fetch_assoc();

				if($aRow["perm_type"]=="")
					return false;

				$aResult=$cPrefix.$aRow["perm_type"];
			}

			return true;

		break;
		default: die ('invalid command');
	}
}

function s_office_perm($cObject,$aParams,&$bPerm)
//Funkcija parbauda vai currentajam officam ir atljauja uz
// $aParams jaapadod id
{
	$bPerm=1;

	if(!$bPerm)
	{
		addMessage(array("msg"=>get_msg_text(3160)));
		return false;
	}
}

function s_sysconfig($cAction, $aParams, &$aResult)
{
	$aResult=array();
	$sTableName="s_sysconfig";
	$sIdName="id";

	switch ($cAction)
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams[$sIdName])) $cWhere.=" and ".$sIdName."='".$aParams[$sIdName]."' ";
			if (!empty($aParams["key"])) $cWhere.=" and key_name='".$aParams["key"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
			if (isset($aParams["parent_id"])) 
			{
				if ($aParams["parent_id"]==null)
					$cWhere.=" and parent_id is null ";
				else
					$cWhere.=" and parent_id='".$aParams["parent_id"]."' ";
			}
			
			$nLimit = empty($aParams['limit'])?RECS_MAX_LIMIT:$aParams['limit'];
			$nOffset = empty($aParams['offset'])?0:$aParams['offset'];
			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
			$sOrderBy = empty($aParams['order_by'])?"tree_order, key_name":$aParams['order_by'];

			$sQuery="SELECT ".
				"id, ".
				"key_name, ".
				"data_value, ".
				"data_value value, ".
				"name, ".
				"name caption, ".
				"is_viewable, ".
				"is_editable, ".
				"parent_id, ".
				"is_parent, ".
				"level, ".
				"REPEAT('&nbsp;&nbsp;&nbsp;', level-1) padd, ".
				"tree_order, ".
				"order_id ".
			"FROM s_sysconfig ";

			$rRes=mysql_get($sQuery." WHERE ".$cWhere." ORDER BY ".$sOrderBy." LIMIT ".$nOffset.", ".$nLimit);

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			break;

		case "U":
			if(empty($aParams["id"]))
				die("no s_sysconfig id");

		//	mysql_get("UPDATE s_sysconfig set data_value ='".$aParams["datavalue"]."' "."where id='".$aParams["id"]."'");

			$aAParams=array(
				"key_name", 
				"data_value", 
				"name", 
				"parent_id", 
				"is_viewable", 
				"is_editable", 
				"is_parent", 
				"tree_order", 
				"order_id", 
				"level");

			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("UPDATE ".$sTableName." ".$cSetString.
				" WHERE ".$sIdName."='".$aParams[$sIdName]."'");
			return true;
		break;
		case "I":
			$aAParams=array(
				"key_name", 
				"data_value", 
				"name", 
				"is_viewable", 
				"is_editable", 
				"parent_id", 
				"is_parent", 
				"order_id", 
				"tree_order", 
				"level");

			$cSetString="set ".paramsToSet($aParams,$aAParams)."";			
			
			mysql_get("INSERT INTO ".$sTableName." ".$cSetString." ");
			$aResult=array($sIdName=>Db::$mysqli->insert_id);

			return true;
			break;
		case "D":
			if(empty($aParams[$sIdName]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}
	
			mysql_get("DELETE FROM ".$sTableName." WHERE ".$sIdName."='".$aParams[$sIdName]."' ");
			return true;
			break;

		default:
			die("Invalid command");
	}
	return false;
}

function sysconfig_value($sKey)
{
	if (empty($sKey)) 
		return false;

	$rRes=mysql_get("SELECT ".
		"c.data_value value ".
	"FROM s_sysconfig c WHERE  c.key_name='".$sKey."'");

	if ($rRes->num_rows!=1) 
		return false;

	$aRow=$rRes->fetch_assoc();

	return $aRow["value"];
}
function user_config_value($nUserId, $sKey)
{
	if (empty($sKey)||empty($nUserId)) 
		return false;

	$rRes=mysql_get("SELECT ".
		"uc.data_value value ".
	"FROM s_user_key uk, s_user_config uc WHERE uk.id=uc.key_id AND uk.key_name='".$sKey."' AND uc.user_id='".$nUserId."'");

	if ($rRes->num_rows!=1) 
		return false;

	$aRow=$rRes->fetch_assoc();

	return $aRow["value"];
}


function s_user_config($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction)
	{
		case "S":
			$cWhere=" 1=1 ";
			if (!empty($aParams["id"])) $cWhere.=" and c.id='".$aParams["id"]."' ";
			if (!empty($aParams["user_id"])) $cWhere.=" and c.user_id='".$aParams["user_id"]."' ";
			if (!empty($aParams["key_id"])) $cWhere.=" and c.key_id='".$aParams["key_id"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";

			//if ($cWhere==" 1=1 ") die("no parameters");

			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";

			$rRes=mysql_get("SELECT ".
				"c.id , c.user_id, c.key_id, c.data_value value ".
				"FROM s_user_config c WHERE ".$cWhere);
			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			return false;
		break;
		case "I":
		mysql_get("INSERT INTO s_user_config set ".
				"user_id ='".$aParams["user_id"]."', ".
				"key_id ='".$aParams["key_id"]."', ".
				"data_value ='".$aParams["data_value"]."' ".
				" ");

			$aResult=array("id"=>Db::$mysqli->insert_id);
			return true;
		break;
		case "U":
			mysql_get("UPDATE s_user_config set ".
				"key_id ='".$aParams["key_id"]."', ".
				"data_value ='".$aParams["data_value"]."' ".
			"where id='".$aParams["id"]."'");
			return true;
		break;

		case "D":
			if (empty($aParams["id"])) die ('no id');
			else
			{
				mysql_get("DELETE FROM s_user_config WHERE id='".$aParams["id"]."'");
				return true;
			}
		break;

		default:
			die("Invalid command");
	}
	return false;
}

function s_user_key($cAction, $aParams, &$aResult)
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
				"uk.id, ".
				"uk.key_name, ".
				"uk.COMMENT, ".
				"uk.default_value ".
				"FROM s_user_key uk ".
				"WHERE ".$cWhere." ORDER BY uk.key_name");

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

			$aAParams=array("key_name","COMMENT","default_value");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";

			mysql_get("UPDATE s_user_key  ".$cSetString.
				" WHERE id='".$aParams["id"]."'");

			return true;
			break;
		case "I":
			$aAParams=array("key_name","COMMENT","default_value");
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("INSERT INTO s_user_key ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;

		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			mysql_get("DELETE FROM s_user_key WHERE id=".$aParams["id"]." ");
			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function s_top_menu($nCurModId = 0, $nCurParentId = 0, $nCurDeskId = 0)
{
	$rRes = mysql_get("SELECT d.name desk_name, m.* ".
		"FROM s_desk d LEFT OUTER JOIN s_module m ON m.desk_id = d.id ".
		"INNER JOIN ".
			"(SELECT DISTINCT p.module_id FROM s_user_role ur, s_perm p ".
			"WHERE p.role_id = ur.role_id AND ur.user_id = ".$_SESSION['user_id'].") am ".
		"ON am.module_id = m.id ".
		"WHERE d.STATUS = 1 AND m.status = 1 AND m.id = m.parent_id ".
		"ORDER BY d.id, m.order_id");

	if ($rRes->num_rows > 0)
	{
		//$aDeskModules = fetchData($rRes);
		$nPrevDeskId = -1;
		$aDesk['modules']=array();
		$aResult = array();
		while ($aMod = $rRes->fetch_assoc())
		{
			if ($aMod['desk_id'] != $nPrevDeskId)
			{
				if ($nPrevDeskId > 0)
				{
					unset ($aDesk['selected']);
					$aDesk['id'] = $nPrevDeskId;
					if ($aDesk['id'] == $nCurDeskId)
					{
						$sDeskTitle = $aMod['desk_name'];  // global
						$aDesk['selected']=1;
					}
					$aDesk['name'] = $sPrevDeskName;
					$nPrevDeskId = $aMod['desk_id'];
					$aResult[]=$aDesk;
					$aDesk['modules']=array();
				}

				$nPrevDeskId = $aMod['desk_id'];
				$sPrevDeskName = $aMod['desk_name'];
			}

			if ($aMod['id'] == $nCurModId || $aMod['id'] == $nCurParentId)	$aMod['selected'] = 1;
			$aDesk['modules'][]=$aMod;
		}

		unset ($aDesk['selected']);
		$aDesk['id'] = $nPrevDeskId;
		if ($aDesk['id'] == $nCurDeskId)
		{
			$sDeskTitle = $aMod['desk_name'];  // global
			$aDesk['selected']=1;
		}
		$aDesk['name'] = $sPrevDeskName;
		$nPrevDeskId = $aMod['desk_id'];
		$aResult[]=$aDesk;

		$rRes = NULL;
	}
	else die('no modules');
	return $aResult;
}

function s_user($cAction, $aParams, &$aResult)
{
	// Māris Cakuls 24.04.2006

	$aResult=array();
	switch ($cAction)
	{
		case 'S':
			$cWhere='1';
			if (!empty($aParams['id'])) $cWhere.=" AND u.id='".$aParams['id']."' ";
			if (!empty($aParams['login'])) $cWhere.=" AND u.login='".$aParams['login']."' ";
			if (!empty($aParams['where'])) $cWhere.=" AND ".$aParams['where']." ";

			$cPrefix = empty($aParams['prefix'])?'':$aParams['prefix'].'_';
			$sOrderBy = empty($aParams['order_by'])?"u.status, u.name, u.surname":$aParams['order_by'];

			$rRes=mysql_get("SELECT ".
				"u.id, ".
				"u.name, ".
				"u.surname, ".
				"CONCAT(u.name,' ',u.surname)  full_name, ".
				"u.role_id, ".
				"u.login, ".
				"u.password, ".
				"NULLIF(u.identity_number, '0') identity_number, ".
				"u.primary_office_id, ".
				"u.email, ".
				"u.phone, ".
				"u.title, ".
				"u.last_login_ip, ".
				"NULLIF(DATE_FORMAT(u.last_login_date,'%d.%m.%Y'),'00.00.0000') last_login_date, ".
				"NULLIF(DATE_FORMAT(u.last_login_date,'%H:%i'),'00:00') last_login_time, ".
				"u.status ".
				"FROM s_user u ".
				"WHERE ".$cWhere." ORDER BY ".$sOrderBy);

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

			$aAParams=array('name','role_id','status','login','surname','identity_number', 'primary_office_id','email','phone','title', 'last_login_ip', 'last_login_date');

			if (!empty($aParams['password']))
			{
				$aAParams[] = 'password';
				$aParams['password'] = MD5($aParams['password']);
			}
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("UPDATE s_user  ".$cSetString." WHERE id='".$aParams["id"]."'");

			return true;
		break;
		case "I":
			$aAParams=array('name','role_id','status','login','surname','identity_number', 'primary_office_id','email','phone','title', 'last_login_ip', 'last_login_date');

			if (!empty($aParams['password']))
			{
				$aAParams[] = 'password';
				$aParams['password'] = MD5($aParams['password']);
			}
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("INSERT INTO s_user ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
		break;
		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			mysql_get("DELETE FROM s_user WHERE id=".$aParams["id"]." ");
			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}

function s_period($cAction, $aParams, &$aResult)
{
	// Māris Cakuls 24.04.2006

	$aResult=array();
	switch ($cAction)
	{
		case 'S':
			$cWhere='1';
			if (!empty($aParams['id'])) $cWhere.=" AND p.id='".$aParams['id']."' ";
			if (!empty($aParams['where'])) $cWhere.=" AND ".$aParams['where']." ";

			$cPrefix = empty($aParams['prefix'])?'':$aParams['prefix'].'_';
			$nSelected = empty($aParams['selected'])?'':$aParams['selected'];
			$sOrderBy = empty($aParams['order_by'])?"p.from_date DESC, p.to_date DESC":$aParams['order_by'];

			$rRes=mysql_get("SELECT ".
				"p.id, ".
				"NULLIF(DATE_FORMAT(p.from_date,'%d.%m.%Y'),'00.00.0000') from_date, ".
				"NULLIF(DATE_FORMAT(p.to_date,'%d.%m.%Y'),'00.00.0000') to_date, ".
				"p.name, ".
				"p.is_active ".
				"FROM s_period p ".
				"WHERE ".$cWhere." ORDER BY ".$sOrderBy);

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,$nSelected);
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

			if (!empty($aParams['is_active']) && $aParams['is_active'] == 1)
				mysql_get('UPDATE s_period SET is_active = 2');

			$aAParams=array('name','from_date','to_date','is_active');
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("UPDATE s_period  ".$cSetString." WHERE id='".$aParams["id"]."'");

			return true;
		break;
		case "I":

			if (!empty($aParams['is_active']) && $aParams['is_active'] == 1)
				mysql_get('UPDATE s_period SET is_active = 2');

			$aAParams=array('name','from_date','to_date','is_active');
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("INSERT INTO s_period ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
		break;
		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}

			mysql_get("DELETE FROM s_period WHERE id=".$aParams["id"]." ");
			return true;
			break;
		default:
			addMessage('DB_SYS_ERR');
	}
	return false;
}


function s_valid_ip($cAction, $aParams, &$aResult)
//funkcija atlasa uzdevumus peec objekta id
{
	switch ($cAction)
	{
		case "S":
			$cWhere='1';
			if (!empty($aParams['id'])) $cWhere.=" AND ip.id='".$aParams['id']."' ";
			if (!empty($aParams['where'])) $cWhere.=" AND ".$aParams['where']." ";

			$cPrefix = empty($aParams['prefix'])?'':$aParams['prefix'].'_';

			$rRes=mysql_get("select ".
				"ip.id ,".
				"ip.name, ".
				"ip.from_ip, ".
				"ip.to_ip, ".
				"ip.from_address, ".
				"ip.to_address ".
				"from s_valid_ip ip ".
				"where ".$cWhere." order by ip.id");

			if ($rRes->num_rows > 0)
			{
				$aResult = fetchData($rRes,$cPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
		break;
		case "I":
			$aParams['from_address']=ip2ulong($aParams['from_ip']);
			$aParams['to_address']= ip2ulong($aParams['to_ip']);

			$aAParams=array('name','from_address','to_address', 'from_ip','to_ip');
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("INSERT INTO s_valid_ip ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;

		break;
		case "U":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}
			$aParams['from_address']=ip2ulong($aParams['from_ip']);
			$aParams['to_address']= ip2ulong($aParams['to_ip']);

			$aAParams=array('name','from_address','to_address', 'from_ip','to_ip');
			$cSetString="SET ".paramsToSet($aParams,$aAParams)."";

			mysql_get("UPDATE s_valid_ip  ".$cSetString." WHERE id='".$aParams["id"]."'");

			return true;

		break;
		case "D":
			if(empty($aParams["id"]))
			{
				addMessage('DB_SYS_ERR');
				return false;
			}
			 mysql_get("DELETE from s_valid_ip  Where id='".$aParams["id"]."' ");
			return true;
		break;
		default:
			die("Invalid command");
	}
	return false;
}

function s_session_log($cAction, $aParams, &$aResult)
{
	$aResult = array();
	$sTable = 's_session_log';
	$sIdName = 'id';

	switch ($cAction) 
	{
		case 'S': 
			$sWhere = ' 1 ';
			if (isset($aParams[$sIdName])) $sWhere.=" AND s.".$sIdName."='".$aParams[$sIdName]."' ";
			if (isset($aParams['user_id'])) $sWhere .= "AND s.user_id ='".$aParams['user_id']."' ";
			if (isset($aParams['where'])) $sWhere.=" AND ".$aParams['where'];
			
			$nLimit = empty($aParams['limit'])?RECS_MAX_LIMIT:$aParams['limit'];
			$nOffset = empty($aParams['offset'])?0:$aParams['offset'];
			$sPrefix=(!empty($aParams['prefix']))?$aParams['prefix'].'_':'';
			$sOrderBy = empty($aParams['order_by'])?'s.date desc, s.id Desc':$aParams['order_by'];

			$sQuery = "SELECT ".
					"s.id, ".
					"NULLIF(DATE_FORMAT(s.date, '%d.%m.%Y %H:%i:%s'),'00.00.0000 00:00:00') datetime, ".
					"NULLIF(DATE_FORMAT(s.date, '%d.%m.%Y'),'00.00.0000') date, ".
					"NULLIF(DATE_FORMAT(s.date, '%H:%i'),'00:00') time, ".
					"s.user_id, ".
					"s.type_id, ".
					"NULLIF(s.ip, '0') ip, ".
					"NULLIF(s.x_ip, '0') x_ip, ".
					"s.session_id, ".
					"s.notes, ".
					"CONCAT(u.name,' ', u.surname) user_name ".
				"FROM ".$sTable." s ".
					"LEFT OUTER JOIN s_user u ON u.id = s.user_id ";
			$rRes = mysql_get($sQuery." WHERE ".$sWhere." ORDER BY ".$sOrderBy." LIMIT ".$nOffset.", ".$nLimit);

			if ($rRes->num_rows > 0) 
			{
				$aResult = fetchData($rRes,$sPrefix,empty($aParams['selected'])?'':$aParams['selected']);
				return true;
			}
			break;
		case 'U':
					
			$aAParams=array('user_id','type_id','ip','x_ip','session_id');
			$cSetString=' SET '.paramsToSet($aParams,$aAParams);
		//	mysql_get("UPDATE ".$sTable." ".$cSetString." WHERE ".$sIdName."='".$aParams[$sIdName]."'");

			return true;
			break;
		case 'I':
			
			$aAParams=array('user_id','type_id','ip','x_ip','session_id','notes');

			$cSetString = ' SET date=now(), '.paramsToSet($aParams,$aAParams);			
			
			mysql_get("INSERT INTO ".$sTable." ".$cSetString);
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

		default: die('bad params');
	}
	return false;
}

function parse_left_menu($aParams)
{
	global $oDirectHTML;
	global $oHead;

	$oApp=new vlibTemplate(CMS_SYSPATH."_left_menu.html");
	$aMenu=array();

	if(!empty($aParams["module_id"]))
	{
		s_menu("S",array("id"=>$aParams["module_id"]),$aModule);

		if(s_menu("S",array("parent_id"=>$aModule[0]["parent_id"]),$aModules))
		{
			foreach($aModules as $k=>$aModule)
			{
				/*if($aModule["id"]==$aModule["parent_id"])
					$aModule["name"]="Main";*/

				$aModule["referencing_name"]=!empty($aParams["referencing_name"])?$aParams["referencing_name"]:"";
				$aModule["referencing_id"]=!empty($aParams["referencing_id"])?$aParams["referencing_id"]:"";
				$aModule["sufix"]=!empty($aParams["sufix"])?$aParams["sufix"]:"";

				if(isset($aParams["bodysuffix"]))
					$cBodySuffix=$aParams["bodysuffix"];
				else
					$cBodySuffix=$aModule["referencing_name"]."=".$aModule["referencing_id"];


				if($aModule["id"]==$aParams["module_id"])
				{
					$aModule["self"]=1;
					$aMenu[]=$aModule;
				}
				elseif($aModule["hidden"]!=1)
					$aMenu[]=$aModule;
			}
			if(count($aMenu)>1)
				$oApp->setLoop("menu_loop",$aMenu);


			$oApp->setVar("body_suffix",$cBodySuffix);

			if(!empty($aParams["title"]))
				$oApp->setVar("title",$aParams["title"]);

		}
	}

	$cContent=$oApp->grab();

	if(isset($oDirectHTML))
	{
		$oDirectHTML->setVar("left_menu",$cContent);
	}

}

function modcode2id($xCode)
{
	if (!empty($xCode))
	{
		if (is_numeric($xCode)) return intval($xCode);

		$code = strtoupper($xCode);
		$rRes = mysql_get("SELECT id FROM s_module WHERE code ='".$code."'");
		if ($rRes !== false && $rRes->num_rows == 1 && $aRow = $rRes->fetch_assoc())
			return $aRow['id'];
	}
	return false;
}


function assign_template($sName,&$oTmpl,$bUseCSS = true)
{
	$nId = get_template_file_id($sName,7);
	if ($nId > 0)
	{
		$sFile = CMS_PRINTPATH.$nId.".dat";
		if (!file_exists($sFile)) die('Template '.$sName.' file does not exist');
		$oTmpl = new vlibTemplate($sFile);
	}
	else die('HTML template '.$sName.' not found');

	if ($bUseCSS)
	{
		$nId = get_template_file_id($sName,6);
		if ($nId > 0)
		{
			global $aCSS;
			$sFile = "/print/".$nId.".css";
			$aCSS[] = array('css_filename'=>$sFile);
		}
	}
}
function get_template_file_id($sName,$nType)
{
	$rRes = mysql_get("SELECT f.id FROM s_template s ".
	"INNER JOIN s_template_item si ON si.template_id = s.id ".
	"INNER JOIN d_file f ON f.referencing_id = si.id AND f.reference_type = ".$nType.
	" WHERE s.name = '".$sName."' ORDER BY si.add_time DESC LIMIT 0,1;");
	if ($aRow = $rRes->fetch_row()[0]) return $aRow;
	return -1;
}

?>