<?php
function paramsToSet($aParams,$aAllowed)
/*
 * Creates SQL query from $aParams
 * $who Uldis Shilovs
 * $when 25.05.2005
 */
{
    if (is_array($aParams))
    {
        $cReturn="";
        foreach($aParams as $cKey=>$cValue)
        {
            //if param is allowed to be set
            if (in_array($cKey, $aAllowed))
            {
            // add slashes or remove slashes
                if(strpos(str_replace("\'","",$cValue),"'")!=false)
                        $cValue =  addslashes($cValue);

                //if value==dd.mm.YYYY hh:mm
                if (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\ [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $cValue))
                    $cReturn.=$cKey.'=STR_TO_DATE(\''.$cValue.'\',concat(GET_FORMAT(DATE, \'EUR\'),\' %H:%i:%S\')), ';
                elseif (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\ [0-9]{2}\:[0-9]{2}$/', $cValue))
                    $cReturn.=$cKey.'=STR_TO_DATE(\''.$cValue.'\',concat(GET_FORMAT(DATE, \'EUR\'),\' %H:%i\')), ';
                //if value==dd.mm.YYYY
                elseif (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/', $cValue))
                    $cReturn.=$cKey.'=STR_TO_DATE(\''.$cValue.'\',GET_FORMAT(DATE, \'EUR\')), ';
                else
                {
                    if (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} \d{2}\:\d{2}\:\d{2}$/', $cValue))
                        $cReturn.=$cKey.'=STR_TO_DATE(\''.$cValue.'\',\'%d.%m.%Y %H:%i:%s\'), ';
                    else
                    {
                        if ($cValue === 'NULL' || is_null($cValue))
                            $cReturn.=$cKey."=NULL, ";
                        else
                            $cReturn.=$cKey.'=\''.$cValue.'\', ';
                    }
                }
            }
        }

        //cuts comma from string end
        if(!empty($cReturn))
        {
            $l=strlen($cReturn)-1;
            for($l;$l>0;$l--)
            {
                if($cReturn[$l]==',')
                {
                    $cReturn=substr($cReturn, 0, $l);
                    break;
                }
            }
        }

        return $cReturn;
    }
    else
        return false;

}

function addKeyPrefix($aParams,$cPrefix)
/*
 * Adds prefix for each array key
 * $who Uldis Shilovs
 * $when 24.05.2005
 */
{
    if (is_array($aParams))
    {
        foreach($aParams as $cKey=>$cValue)
            $aTemp[$cPrefix.$cKey]=$cValue;

        unset($aParams);
        return $aTemp;
    }
    else
        return false;
}

function s_update($cObject,$aParams)
// updeito summ??ros laukus atbilsto??ajiem objekitem no tiem atbilsto??aj??m item tabul??m
// INVOICE - total_price, total_vat, total
{
    switch ($cObject)
    {
        case "INVOICE":

            if(isset($aParams["client_chg"]))//ja notikusi klientu mainja
            {
                mysql_get("UPDATE d_invoice set ".
                    "client_id='".$aParams["client_id"]."'".
                    " WHERE id='".$aParams["id"]."'");
                return true;
                break;
            }
            if(isset($aParams["add_payer"]))//ja notikusi klientu mainja
            {
                mysql_get("UPDATE d_invoice set ".
                    "payer_id=".$aParams["payer_id"]."".
                    " WHERE id='".$aParams["id"]."'");
                return true;
                break;
            }

            $aInvoice=mysql_fetch_assoc(mysql_get("SELECT ".
                "total_price_correction, ".
                "total_price, ".
                "is_vat,".
                "total_vat_correction,".
                "total_vat,".
                "total".
                " FROM d_invoice ".
                "WHERE id='".$aParams["id"]."'"));

            $res=mysql_get("SELECT ".
                " SUM(total) total_price ".
                "FROM d_invoice_item  ".
                " WHERE invoice_id='".$aParams["id"]."'" );
            $aRes=mysql_fetch_assoc($res);
            $total_price=round($aRes["total_price"],2)+$aInvoice["total_price_correction"];
            $total_vat=round($aRes["total_price"]*0.18,2)+$aInvoice["total_vat_correction"];


            $res=mysql_get("SELECT ".
                " SUM(payment_amount) payment_total ".
                "FROM d_invoice_payment  ".
                " WHERE invoice_id='".$aParams["id"]."'" );
            $aRes=mysql_fetch_assoc($res);
            $payment_total=round($aRes["payment_total"],2);


            if($aInvoice["is_vat"]==1)
                $total=$total_price+$total_vat;
            else
            {
                $total=$total_price;
                $total_vat=0;
            }

            mysql_get("UPDATE d_invoice set ".
            "total_price='".$total_price."',".
            "total_price_vat='".$total."',".
            "total_vat='".$total_vat."',".
            "payment_total='".$payment_total."',".
            "payment_left='".($total-$payment_total)."',".
            "total='".$total."'".
            " WHERE id='".$aParams["id"]."'");
        return true;
        break;

        case "SALARY":

            if(isset($aParams["id"])&&($aParams["id"]))
            {
                mysql_get("UPDATE d_salary set ".
                    " price='".$aParams["price"]."',".
                    " discount='".$aParams["discount"]."'".
                    " WHERE id='".$aParams["id"]."'");
                return true;
                break;
            }
        break;

        case "OBJECT":
            if(empty($aParams["id"]))
                return false;
            if(!d_object("S", array("id"=>$aParams["id"]), $aObject))
                return false;
            else $aObject=$aObject[0];
            // Izrakstītie rēķini (gr??matojamie)
            $aRow=mysql_fetch_assoc(mysql_get("SELECT sum(ii.price*ii.cnt) invoice_total ".
                "FROM d_invoice_item ii, d_invoice i ".
                "WHERE ".
                "ii.invoice_id=i.id ".
                "and i.invoice_type=1 ".
                "and ii.object_id='".$aParams["id"]."'"));
            $nInvoiceTotal=$aRow["invoice_total"];

            /* izrakstiitie avans reekini */
            $aRow=mysql_fetch_assoc(mysql_get("SELECT sum(ii.price*ii.cnt) invoice_total_prepayment ".
                "FROM d_invoice_item ii, d_invoice i ".
                "WHERE ".
                "ii.invoice_id=i.id ".
                "and i.invoice_type=2 ".
                "and ii.object_id='".$aParams["id"]."'"));
            $nInvoiceTotalPrepayment=$aRow["invoice_total_prepayment"];


            //Saņemtie maks??jumi
            $aRow=mysql_fetch_assoc(mysql_get("SELECT sum(payment_amount) payment_total ".
                "FROM d_invoice_payment ip, d_invoice i ".
                "WHERE ".
                "ip.invoice_id=i.id ".
                "and ip.object_id='".$aParams["id"]."'"));
            $nPaymentTotal=$aRow["payment_total"];

            s_sysconfig("S",array("key"=>"vat"),$aSysConf);

            if ($aObject["vat"]>0)
                $nVat=$aObject["vat"];
            else
                $nVat=$aSysConf[0]["value"];

            $nPriceTotal=round($aObject["price_total"],2);
            $nVatTotal=round($nPriceTotal*$nVat/100,2);
            $nPriceSum=round($nPriceTotal+$nVatTotal,2);

            mysql_get("UPDATE d_object set ".
            "price_total='".$nPriceTotal."',".
            "payment_total='".$nPaymentTotal."',".
            "invoice_total='".$nInvoiceTotal."',".
            "invoice_total_prepayment='".$nInvoiceTotalPrepayment."',".
            "vat='".$nVat."',".
            "vat_total='".$nVatTotal."',".
            "price_sum ='".$nPriceSum."'".
            " WHERE id='".$aParams["id"]."'");
        return true;
        break;

        default: die ("Invalid object");
    }
}


function log_favourites()
{
}


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

            if (mysql_num_rows($rRes)==0)
                return false;
            while ($aRow=mysql_fetch_assoc($rRes))
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
            $aResult=array("id"=>mysql_insert_id());

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

function swap_order($cHow,$aParams)
{
    if(empty($aParams["table_name"]) || empty($aParams["id"]))
        die("unable to swap");

    $cWhere=!empty($aParams["where"])?$aParams["where"]:"";



    $rRes=mysql_get("SELECT order_id,id FROM ".$aParams["table_name"]." ".
                " WHERE id=".$aParams["id"]." ".$cWhere."");

    if (mysql_num_rows($rRes)==0)
        return false;
    $aObj1=mysql_fetch_assoc($rRes);

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

    if (mysql_num_rows($rRes)==0)
        return false;
    $aObj2=mysql_fetch_assoc($rRes);

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
            $aRow=mysql_fetch_assoc($rRes);

            $aRow["order_id"]++;

            break;

        case "MIN":
            $rRes=mysql_get("SELECT min(order_id) order_id FROM ".$cTable." ");
            $aRow=mysql_fetch_assoc($rRes);

            if($aRow["order_id"]>1)
                $aRow["order_id"]--;

            break;
    }

    return $aRow["order_id"];
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
                "c.comment_type, ".
                //"ct.name comment_type_name, ".
                //"ct.short_name comment_type_shortname, ".
                "CONCAT(u.name,' ',u.surname) user_full_name, ".
                "u.login ".
                "FROM d_comment c, s_user u ".
                //"LEFT OUTER JOIN c_comment ct on ct.id=c.comment_type_id ".
                " WHERE u.id=c.user_id and ".$cWhere." ORDER BY c.comment_date DESC, c.id DESC");

            if (mysql_num_rows($rRes) > 0)
            {
                $aResult = fetchData($rRes,$cPrefix,$nSelected);
                return true;
            }
            return false;
            break;

        case "I":
            $aParams["comment_date"]=date("d.m.Y");
            $aAParams=array("comment_type", "user_id", "comment_date", "comment_text", "referencing_id");
            $cSetString="set ".paramsToSet($aParams,$aAParams)."";

            mysql_get("insert into d_comment ".$cSetString." ");
            $aResult=array("id"=>mysql_insert_id());

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

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function d_dates($cDate='')
{
    $aDates=mysql_fetch_assoc(mysql_get("SELECT ".
        "DATE_FORMAT(CURDATE(),'%d.%m.%Y') cur_day, ".
        //"DATE_FORMAT(CURDATE() - 1, '%d.%m.%Y') prev_day, ".
        "DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -1 Day), '%d.%m.%Y') prev_day, ".
        "DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -1 WEEK), '%d.%m.%Y') prev_seven_day, ".
        "DATE_FORMAT(STR_TO_DATE(CONCAT(PERIOD_ADD(DATE_FORMAT(CURDATE(),'%Y%m'),-2),'01'),'%Y%m%d'), '%d.%m.%Y') three_months_first_day, ".
        "DATE_FORMAT(STR_TO_DATE(CONCAT(PERIOD_ADD(DATE_FORMAT(CURDATE(),'%Y%m'),-1),'01'),'%Y%m%d'), '%d.%m.%Y') prev_month_first_day, ".
        "DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -1 WEEK), '%d.%m.%Y') prev_seven_day, ".
        "DATE_FORMAT(LAST_DAY(  STR_TO_DATE(CONCAT(PERIOD_ADD(DATE_FORMAT(CURDATE(),'%Y%m'),-1),'01'),'%Y%m%d')   ), '%d.%m.%Y') prev_month_last_day, ".
        "CONCAT('01.01.', DATE_FORMAT(CURDATE(),'%Y')) cur_year_first_day, ".
        "CONCAT('31.12.', DATE_FORMAT(CURDATE(),'%Y')) cur_year_last_day, ".
        "CONCAT('01.', DATE_FORMAT(CURDATE(),'%m.%Y')) cur_month_first_day, ".
        "DATE_FORMAT(LAST_DAY(CURDATE()), '%d.%m.%Y') cur_month_last_day, ".
        "CONCAT('01.', DATE_FORMAT(DATE_ADD(CURDATE(), interval 1 MONTH),'%m.%Y')) next_month_first_day, ".
        "DATE_FORMAT(LAST_DAY(DATE_ADD(CURDATE(), interval 1 MONTH)), '%d.%m.%Y') next_month_last_day ".            
        "FROM s_user LIMIT 1"));

        $aToday = getDate();
        if ($aToday['wday'] == 0) $aToday['wday'] = 7; // fix sunday

        $aDates['cur_week_monday'] = date('d.m.Y',mktime(0,0,0,$aToday['mon'],$aToday['mday']-$aToday['wday']+1,$aToday['year']));
        $aDates['cur_week_sunday'] = date('d.m.Y',mktime(0,0,0,$aToday['mon'],$aToday['mday']-$aToday['wday']+7,$aToday['year']));

        $aDates['next_month_last_day'] = date('d.m.Y',mktime(0,0,0,$aToday['mon']+2,0,$aToday['year']));

    if(!empty($cDate))
        return $aDates[$cDate];
    else
        return $aDates;
}

function d_get_id($cData, $aParams)
{
    switch ($cData)
    {
        case "INVOICE":
            $cTableName="d_invoice";
            break;
        case "OBJECT":
            $cTableName="d_object";
            break;
        default:
            addMessage(array('msg'=>"Incorrect table name"));
            return 0;
    }
    if (empty($aParams["field"])||empty($aParams["value"]))
    {
        addMessage(array('msg'=>"Incorrect params"));
        return 0;
    }

    $cClause=$aParams["field"]."=".$aParams["value"]."";

    $res=mysql_get("SELECT id FROM ".$cTableName." WHERE ".$cClause);
    $nRows=mysql_num_rows($res);
    if($nRows==0)
    {
        $sWhat = ($cData == 'INVOICE')?'rēķins':'pasūtījums';
        addMessage(array('msg'=>'T??ds '.$sWhat.'('.$aParams['value'].') neeksistē'));
        return 0;
    }

    if($nRows>1)
    {
        addMessage(array('msg'=>"too many records with ".$cClause));
        return 0;
    }
    $aRow=mysql_fetch_assoc($res);

    return $aRow["id"];
}

function dateCompare($sDateA, $sDateB, $sOp)
{
    $nDateA = dateToNum($sDateA);
    $nDateB = dateToNum($sDateB);
    switch ($sOp)
    {
        case '=': case '==':
            if ($nDateA == $nDateB) return true;
        break;
        case '>':
            if ($nDateA > $nDateB) return true;
        break;
        case '>=':
            if ($nDateA >= $nDateB) return true;
        break;
        case '<=':
            if ($nDateA <= $nDateB) return true;
        break;
        case '>=':
            if ($nDateA >= $nDateB) return true;
        break;
        default:
    }
    return false;
}

function dateToNum($sDate)
{
    if (preg_match("^[0-3]\d\.[01]\d\.\d\d\d\d$",$sDate,$aMatches))
    {
        $number = substr($aMatches[0],-4,4).substr($aMatches[0],3,2).substr($aMatches[0],0,2);
        return intval($number);
    }
    return 0;
}


function validateIBAN($str)
//fja p??rbauda iban korektību
//1. Parvieto pirmos četrus simbolus klienta konta numura labaja puse.
//2. Parveido burtus par cipariem.
//3. Kontrolcipari ir pareizi, ja, izmantojot MOD 97-10, iegust skaitli 1.
{
    function mod97($str){
        unset($ret);
        while($str!=""){
            if (!isset($ret)) {
                $ret = substr($str, 0, 9);
                $str = substr($str, 9);
            } else {
                $lenght = 9 - strlen($ret);
                $ret .= substr($str, 0, $lenght);
                $str = substr($str, $lenght);
            }
            $ret = $ret%97;
        }

        return $ret;
    }

    function stringToInt($str)
    {
        $temp="";
        for($i=0;$i<strlen($str);$i++)
        {
            if(is_numeric($str[$i]))
                $temp.=$str[$i];
            else
                $temp.=(ord($str[$i])-55);
        }
        return $temp;
    }

    //aizmet pirmos 4 simbolus uz beigaam
    $start=substr($str,0,4);
    $str=substr($str,4);
    $str=$str.$start;

    //paarveido par int
    $str=stringToInt($str);

    return mod97($str)==1?true:false;
}

function array2suffix($array,$aExceptions=NULL)
{
    if (empty($array) || !is_array($array)) return false;
    $sSuffix = '';
    $bFirst = true;
    foreach ($array as $k=>$v)
        if (empty($aExceptions) || !is_array($aExceptions) || !in_array($k,$aExceptions))
        {
            if ($bFirst)
            {
                $bFirst = false;
                $sSuffix .= $k."=".$v;
            }
            else
                $sSuffix .= "&amp;".$k."=".$v;
        }
    return $sSuffix;
}

function make_pages($nOffset = 0,$nCount,$nRecsPerPage,&$aResult)
{
    $aResult = array();
    if ($nCount > $nRecsPerPage)
    {
        // link to the same page, just w/ different offset
        $sLink = "/?".array2suffix($_GET,array('offset'));

        // add <<
        if ($nOffset > 0)
        {
            $nPrevOffset = $nOffset - $nRecsPerPage;
            if ($nPrevOffset < 0) $nPrevOffset = 0;
            $aResult[] = array('page_no'=>"&laquo;&laquo;",'link'=>$sLink."&amp;offset=".$nPrevOffset);
        }

        $nPageNo = 1;
        for ($i = 0; $i < $nCount; $i += $nRecsPerPage)
        {
            $aTmp = array('page_no'=>$nPageNo,'link'=>$sLink."&amp;offset=".$i);
            if ($nOffset == $i) $aTmp['current'] = 1;
            $aResult[] = $aTmp;
            $nPageNo ++;
        }

        // add >>
        if ($nOffset < ($nCount - $nRecsPerPage))
        {
            $nNextOffset = $nOffset + $nRecsPerPage;
            $aResult[] = array('page_no'=>"&raquo;&raquo;",'link'=>$sLink."&amp;offset=".$nNextOffset);
        }

        return true;
    }
    return false;
}

function make_scroll_buttons($nOffset,&$aList,&$oTmpl,$nRecPerPage=RECS_PER_PAGE)
{
    $sLink = "/?".array2suffix($_GET,array('offset')).'&amp;offset=';
    $sTag = '<div id="paging">';

    if ($nOffset > 0)
    {
        $nPrevOffset = $nOffset - $nRecPerPage;
        if ($nPrevOffset < 0) $nPrevOffset = 0;
        $sTag .= '<a class="page-left" href="'.$sLink.$nPrevOffset.'" title="previous page"></a>';
    }

    if (count($aList) > $nRecPerPage)
    {
        $nNextOffset = $nOffset + $nRecPerPage;
        $sTag .= '<a class="page-right" href="'.$sLink.$nNextOffset.'" title="next page"></a>';
        array_pop($aList);
    }

    $sTag .= '</div>';
    $oTmpl->SetVar('paging_buttons',$sTag);
}

function addRule($sFieldId,$sType)
{
    global $aValidationRules;
    $aValidationRules[] = array('id'=>$sFieldId,'type'=>$sType);
}

function full_path($sType, $nId)
{
    if (empty($sType) || empty($nId))
        return false;

    $sQuery = $sPrefix = '';    

    if ($sType == "category")
    {
        $sPrefix = "wctg_";
        $sQuery="SELECT wctg_id, wctg_wctg_id, wctg_name FROM wctg_category WHERE 1 ";
    }        
    elseif ($sType == "location")
    {
        $sPrefix = "wlct_";
        $sQuery="SELECT wlct_id, wlct_wlct_id, wlct_name FROM wlct_location WHERE 1 ";
    }    
    elseif ($sType == "account")
    {
        $sPrefix = "facn_";
        $sQuery="SELECT facn_id, facn_facn_id, facn_name FROM facn_account WHERE 1 ";
    }    
    while ($nId != 'null')
    {
        $rRes = mysql_get($sQuery." AND ".$sPrefix."id = ".$nId);
        if (mysql_num_rows($rRes) > 0) 
        {
            $aTmp = fetchData($rRes,'','');
            if (isset($aTmpResult))
                $aTmpResult[] = array('id'=>$aTmp[0][$sPrefix."id"], 'name'=>$aTmp[0][$sPrefix."name"]);
            else
                $aTmpResult = array();

            if (empty($aTmp[0][$sPrefix.$sPrefix."id"]))
                $nId = 'null';
            else
                $nId = $aTmp[0][$sPrefix.$sPrefix."id"];
        }
    }

    if (!empty($aTmpResult))
        for ($i=count($aTmpResult)-1; $i>=0; $i--)
            $aResult[] = $aTmpResult[$i];    
    else
        return false;

    return $aResult;
}

function set_cur_bvsl_id($nBvslId = NULL)
{
     
    
    if ($nBvslId > 0) 
    {
        
        //ep adding previous vessel id to avoid error in jexecution page
        $_SESSION['prev_bvsl_id'] = $_SESSION['bvsl_id'];
        $_SESSION['bvsl_id'] = $nBvslId;
        return $nBvslId;
    }

    if(RPL_MASTER) {
    
        if (office_bvsl(array('office_id'=>$_SESSION['office_id'],'limit'=>1),$aRes))
        {
            $_SESSION['bvsl_id'] = $aRes[0]['bvsl_id'];            
            return $_SESSION['bvsl_id'];
        }
    } else {
        $sql_bvsl_id = "SELECT bvsl_id FROM bvsl_vessel WHERE bvsl_code = '".RPL_CODE."';";
        $rRes = mysql_get($sql_bvsl_id);
        $row = mysql_fetch_assoc($rRes);
        $_SESSION['bvsl_id'] = $row['bvsl_id']; 
        return $_SESSION['bvsl_id'];
    }

    return NULL;
}



function get_cur_bvsl_id($previous = false)
{
    if (!empty($_SESSION['prev_bvsl_id']) && $previous){
        
         return $_SESSION['prev_bvsl_id'];
         
    } else {
    
        if (empty($_SESSION['bvsl_id']))
            return set_cur_bvsl_id();
        return $_SESSION['bvsl_id'];
    }
}

//  kategorijaam lietojam atsevishkju sessijas mainiigo

function set_cur_bvsl_id_ctg($nBvslId = NULL)
{
    

    if ($nBvslId > 0) 
    {
        $_SESSION['bvsl_id_ctg'] = $nBvslId;
        if  ($nBvslId != SYSTEM_VESSEL) set_cur_bvsl_id($nBvslId);
        return $nBvslId;
    }

    if (office_bvsl(array('office_id'=>$_SESSION['office_id'],'limit'=>1),$aRes))
    {
        $_SESSION['bvsl_id_ctg'] = $aRes[0]['bvsl_id'];            
        return $_SESSION['bvsl_id_ctg'];
    }

    return NULL;
}

function get_cur_bvsl_id_ctg()
{
    if (empty($_SESSION['bvsl_id_ctg']))
        return set_cur_bvsl_id_ctg();
    return $_SESSION['bvsl_id_ctg'];
}

function office_bvsl($aParams,&$aResult)
{
    if(!RPL_MASTER)
    {
      $sql_bvsl_id = "SELECT bvsl_id FROM bvsl_vessel WHERE bvsl_code = '".RPL_CODE."';";
      $rRes = mysql_get($sql_bvsl_id);
      $row = mysql_fetch_assoc($rRes);
      $aParams['bvsl_id'] = $row['bvsl_id'];
    }

    $aResult = array();
    $nOfficeId = isset($aParams['office_id'])?$aParams['office_id']:$_SESSION['office_id'];
    $sWhere = "bvof.bvof_s_office_id = ".$nOfficeId;
    if (isset($aParams['bvsl_id'])) $sWhere .= " AND bvsl.bvsl_id = ".$aParams['bvsl_id'];
    $sPrefix = isset($aParams['prefix'])?$aParams['prefix'].'_':'';
    $nLimit = isset($aParams['limit'])?$aParams['limit']:99999999;

    $rRes = mysql_get("SELECT bvsl.bvsl_id, bvsl.bvsl_name FROM ".
        "bvof_vessel_office bvof INNER JOIN bvsl_vessel bvsl ON bvsl.bvsl_id = bvof.bvof_bvsl_id ".
        "WHERE ".$sWhere." ORDER BY bvsl.bvsl_name LIMIT 0,".$nLimit);
    
    if (mysql_num_rows($rRes) > 0)
    {
        $aResult = fetchData($rRes,$sPrefix,NULL);
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
    $res = str_replace(array("\\r\\n","\\n"),"\n",$val); //newlines for textareas
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