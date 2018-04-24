function importXML(cWhat,nVal)
{
	if(cWhat=="bank_branch")
	{
		afterFunction=function(){createBankBranchList()};
		url="/xml.php?mod=550&bank_id="+nVal;
	}

	if(cWhat=="territory")
	{
		afterFunction=function(){createTeritoryList()};
		url="/xml.php?mod=550&region_id="+nVal;
	}

	if(cWhat=="parish")
	{
		afterFunction=function(){createParishList()};
		url="/xml.php?mod=550&territory_id="+nVal;
	}

	if (document.implementation && document.implementation.createDocument)
	{
		xmlDoc = document.implementation.createDocument("", "", null);
		xmlDoc.onload = function(){afterFunction()};
	}
	else if (window.ActiveXObject)
	{
		xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.onreadystatechange = function () {
			if (xmlDoc.readyState == 4) 
				afterFunction()
		};
 	}
	else
	{
		alert('Your browser can\'t handle this script');
		return;
	}
	
	xmlDoc.load(url);
}

function createTeritoryList()
{
	var x = xmlDoc.getElementsByTagName('root');
	
	var elem=get_element('territory_id');
	elem.innerHTML="";
	get_element('parish_id').innerHTML="";
	
	elem.options.add(document.createElement("OPTION"));
	elem.options[0].text ='-';
	elem.options[0].value =0;
	
	
	for (i=0; i<x[0].childNodes.length; i++)
	{
		if(x[0].childNodes[i].nodeType!=3)
		{
			var opt = document.createElement("OPTION");
			opt.value = x[0].childNodes[i].getAttribute("value");
			opt.text = x[0].childNodes[i].firstChild.nodeValue;
					 
			elem.options.add(opt); //ieselektee klienta izveeleeto filiaali,
		
			if (opt.value==get_value('sel_territory_id'))
			{
				opt.selected="selected";

			}		
		}
		
	}

	if (get_value('sel_territory_id')>0)
	importXML('parish',get_value('sel_territory_id'));

}

function createParishList()
{
	var x = xmlDoc.getElementsByTagName('root');
	
	var elem=get_element('parish_id');
	elem.innerHTML="";
	
	elem.options.add(document.createElement("OPTION"));
	elem.options[0].text ='-';
	elem.options[0].value =0;
	
	
	for (i=0; i<x[0].childNodes.length; i++)
	{
		if(x[0].childNodes[i].nodeType!=3)
		{
			var opt = document.createElement("OPTION");
			opt.value = x[0].childNodes[i].getAttribute("value");
			opt.text = x[0].childNodes[i].firstChild.nodeValue;
					 
			elem.options.add(opt); //ieselektee klienta izveeleeto filiaali,
		
			if (opt.value==get_value('sel_parish_id'))
			{
				opt.selected="selected";
			}		
		}
		
	}
}

function createBankBranchList()
{
	var x = xmlDoc.getElementsByTagName('root');
	
	var elem=get_element('bank_branch_id');
	elem.innerHTML="";
	
	elem.options.add(document.createElement("OPTION"));
	elem.options[0].text ='-';
	elem.options[0].value =0;
	
	
	for (i=0; i<x[0].childNodes.length; i++)
	{
		if(x[0].childNodes[i].nodeType!=3)
		{
			var opt = document.createElement("OPTION");
			opt.value = x[0].childNodes[i].getAttribute("value");
			opt.text = x[0].childNodes[i].firstChild.nodeValue;
					 
			elem.options.add(opt); //ieselektee klienta izveeleeto filiaali,
		
			if (opt.value==get_value('sel_bank_branch_id'))
			{
				opt.selected="selected";
			}		
		}
		
	}
}


function init_territory()
{
	get_element('sel_territory_id').value="";
	get_element('territory_id').innerHTML="";
	get_element('sel_parish_id').value="";
	get_element('parish_id').innerHTML="";
}