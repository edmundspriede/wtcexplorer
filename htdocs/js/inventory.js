/*
function importEmployeeXML(val)
{
	if (document.implementation && document.implementation.createDocument)
	{
		xmlDoc = document.implementation.createDocument("", "", null);
		xmlDoc.onload = createEmployeeList;
	}
	else if (window.ActiveXObject)
	{
		xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.onreadystatechange = function () {
			if (xmlDoc.readyState == 4) createEmployeeList()
		};
 	}
	else
	{
		alert('Your browser can\'t handle this script');
		return;
	}

	xmlDoc.load("xml_data.php?client_id="+val);
}*/

/*
function createEmployeeList()
{
	var x = xmlDoc.getElementsByTagName('root');

	var elem=document.getElementById('employees_list');

	if(get_element('selected_employee'))
		selectedEmp=get_value('selected_employee');
	else
		selectedEmp=0;

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
		
			if (opt.value==selectedEmp)
			{
				opt.selected="selected";
			}		
		}	
	}
}*/

function add_bank_branch(bb_id,bb_swift,bb_name)
{
	if (!bb_id) bb_id="";
	if (!bb_swift) bb_swift=""; 
	if (!bb_name) bb_name=""; 

	var row=insert_row("tbl_bank_branch",1);
	var nr = row.id;
	
	if(nr % 2==0)
		row.className="even";

	var cell=insert_cell(row);
	insert_form_element("text", "bb_swift["+nr+"]",bb_swift,cell,'','input-1');

	cell=insert_cell(row);
	insert_form_element("text", "bb_name["+nr+"]",bb_name,cell,'','input-3');
	
	// id + delete
	cell=insert_cell(row);
	if(bb_id)
	{
		var el=insert_form_element("button","","",cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('delete_bb_id',bb_id);this.form.submit()};
		else
			el.onclick= function(e){set_value('delete_bb_id',bb_id);this.form.submit()};
	}
	insert_form_element("hidden", "bb_id["+nr+"]",bb_id,cell,'','');
}