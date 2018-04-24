<? 
//XML datu nolasiishanai no db
require("../config.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_init.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_session.inc");
require(CMS_LIBPATH."d_client.inc");

header('Content-Type: text/xml');
$xml_output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"; 

//x_insurance_product komisiju
if(isset($_REQUEST['product_id']) && isset($_REQUEST['client_id']) )
{
	$xml_output .= "<root>\n"; 

	$sWhere = " 1 and x.client_id='".$_REQUEST["client_id"]."' "	
			." and x.product_id='".$_REQUEST["product_id"]."' ";

	if(x_insurance_product('S',array('where'=>$sWhere),$aRes))
		foreach($aRes as $k=>$v)
		{
				$xml_output .= "\t\t<option value=\"".$v['id']."\">".$v['amount']."</option>\n"; 
			//	$xml_output .= "<commission>".$v['amount']."</commission>\n"; 
		}

	$xml_output .= "</root>"; 
	echo $xml_output;
}


?>