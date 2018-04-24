<?

/*
 * This script is copyright PHPGarage.com (On Line Construction Inc.). It may be used,
 * changed, and distributed freely as long as this message and/or some type of recognition
 * is given to PHPGarage.com or On Line Construction Inc.
 * 
 * http://www.phpgarage.com
 * http://www.onlineconstructioninc.com
 *  
 */
function getImgByType($name)
{
	if($_FILES[$name]['type'] == "image/pjpeg" || $_FILES[$name]['type'] == "image/jpeg")
	{
		$im = imagecreatefromjpeg($_FILES[$name]['tmp_name']);
	}
	elseif($_FILES[$name]['type'] == "image/x-png" || $_FILES[$name]['type'] == "image/png")
	{
		$im = imagecreatefrompng($_FILES[$name]['tmp_name']);
	}
	elseif($_FILES[$name]['type'] == "image/gif")
	{
		$im = imagecreatefromgif($_FILES[$name]['tmp_name']);
	}

	return $im;
}

function getImgArrayByType($name,$k)
{
	if($_FILES[$name]['type'][$k] == "image/pjpeg" || $_FILES[$name]['type'][$k] == "image/jpeg")
	{
		$im = imagecreatefromjpeg($_FILES[$name]['tmp_name'][$k]);
	}
	elseif($_FILES[$name]['type'][$k] == "image/x-png" || $_FILES[$name]['type'][$k] == "image/png")
	{
		$im = imagecreatefrompng($_FILES[$name]['tmp_name'][$k]);
	}
	elseif($_FILES[$name]['type'][$k] == "image/gif")
	{
		$im = imagecreatefromgif($_FILES[$name]['tmp_name'][$k]);
	}

	return $im;
}

function ResizeImage($im,$maxwidth,$maxheight,$name)
{
	$width = imagesx($im);
	$height = imagesy($im);
	$RESIZEWIDTH=false;
	$RESIZEHEIGHT=false;

	if(($maxwidth && $width > $maxwidth) || ($maxheight && $height > $maxheight))
	{
		if($maxwidth && $width > $maxwidth){
			$widthratio = $maxwidth/$width;
			$RESIZEWIDTH=true;
		}
		if($maxheight && $height > $maxheight){
			$heightratio = $maxheight/$height;
			$RESIZEHEIGHT=true;
		}
		if($RESIZEWIDTH && $RESIZEHEIGHT)
		{
			if($widthratio < $heightratio)
			{
				$ratio = $widthratio;
			}
			else
			{
				$ratio = $heightratio;
			}
		}
		elseif($RESIZEWIDTH)
		{
			$ratio = $widthratio;
		}
		elseif($RESIZEHEIGHT)
		{
			$ratio = $heightratio;
		}

    	$newwidth = $width * $ratio;
        $newheight = $height * $ratio;

		if(function_exists("imagecopyresampled"))
		{
      		$newim = imagecreatetruecolor($newwidth, $newheight);
      		imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}
		else
		{
			$newim = imagecreate($newwidth, $newheight);
      		imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}
        ImageJpeg ($newim,$name);
		ImageDestroy ($newim);
	}
	else
	{
		ImageJpeg ($im,$name);
	}
}

function d_image($cAction, $aParams, &$aResult)
{

	$aResult=array();
	switch ($cAction) 
	{
		case "S": 
			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and i.id='".$aParams["id"]."' ";
			if (!empty($aParams["referencing_id"])) $cWhere.=" and i.referencing_id='".$aParams["referencing_id"]."' ";
			if (!empty($aParams["reference_type"])) $cWhere.=" and i.reference_type='".$aParams["reference_type"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";

			$cPrefix=(!empty($aParams["prefix"]))?$aParams["prefix"]."_":"";
			
			$rRes=mysql_get("SELECT ".
				"i.id, i.referencing_id , i.reference_type,i.status ".
			"FROM d_image i ".
			"WHERE ".$cWhere." ORDER BY i.order_id");

			if ($rRes->num_rows==0) 
				return false;
			
			while ($aRow=$rRes->fetch_assoc())
			{
				if(!empty($cPrefix))
					$aRow=addKeyPrefix($aRow,$cPrefix);

				$aResult[]=$aRow;
			}
			
			return true;
		break;
		case "I":
			$aAParams=array("referencing_id", "reference_type","status","order_id");
			$cSetString="set ".paramsToSet($aParams,$aAParams)."";
			
			mysql_get("insert into d_image ".$cSetString." ");
			$aResult=array("id"=>Db::$mysqli->insert_id);

			return true;
			break;
		case "D":

			$cWhere="1=1";
			if (!empty($aParams["id"])) $cWhere.=" and i.id='".$aParams["id"]."' ";
			if (!empty($aParams["referencing_id"])) $cWhere.=" and i.referencing_id='".$aParams["referencing_id"]."' ";
			if (!empty($aParams["reference_type"])) $cWhere.=" and i.reference_type='".$aParams["reference_type"]."' ";
			if (!empty($aParams["where"])) $cWhere.=" and ".$aParams["where"]." ";
			
			if($cWhere=="1=1")
				die("cannot delete from d_image");
			
			if(d_image("S",array("where"=>$cWhere),$aDimg))
			{
				foreach($aDimg as $k=>$a)
				{
					mysql_get("DELETE from d_image where id='".$a["id"]."' ");

					if(file_exists(CMS_UPLOADPATH.$a["id"].".dat"))
						unlink(CMS_UPLOADPATH.$a["id"].".dat");
				}
			}

			return true;
			break;
	}
}

?>

