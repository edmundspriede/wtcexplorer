function get_xml(afterFunction,url)
//17.01.2006 uldis
//jaapadod funkcija kas izpildiisies peec xml ielaades
//jaapadod url peec kura uztaisiis xml
{

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
		return false;
	}
	
	xmlDoc.load(url);
	return true;

}

function load_data(who,tbl)
//17.01.2006 uldis
//jaapadod who: contact
//jaapadod tbl: tabulas id
//funkcija iet cauri visai tabulai un ieliek formas laukos veertiibas no xml
{
	if(who=="contact")
	{
		var x = xmlDoc.getElementsByTagName('contact');

	}

	oTable=get_element(tbl);

	var oRow;
	var curr_row, curr_cell;

	for (curr_row = 0; curr_row < oTable.rows.length; curr_row++)
	{
		oRow = oTable.rows[curr_row];

		for (curr_cell = 0; curr_cell < oRow.cells.length; curr_cell++)
		{
			var nodelist = oRow.cells[curr_cell].childNodes;
			for(i = 0; i < nodelist.length; i++)
			{
				el = nodelist[i];
				if(el.type=="hidden" || el.type=="text" || el.type=="select-one")
				{
					for (i=0; i<x[0].childNodes.length; i++)
					{
						if(x[0].childNodes[i].nodeType!=3)
						{
							var field_name=x[0].childNodes[i].nodeName;

							if(x[0].childNodes[i].firstChild.nodeValue=="empty_value")
								field_value="";
							else
								field_value=x[0].childNodes[i].firstChild.nodeValue;

							if(el.name==field_name)
							{
								if(el.type=="select-one")
								{
									el.options[el.selectedIndex].value=field_value;
								}
								else
									el.value=field_value;
							}

						}
					}
				}
			}
		}
	}
}

function submit_table_data(table_id,params,parent_id,afterfunction)
/*17.01.2006 uldis
* funkcijai jaapadod tabulas id un tad taa visus tabulas formas datus sasien vienaa stringaa
*
*/
{
	oTable=get_element(table_id);

	var oRow;
	var curr_row, curr_cell;

	for (curr_row = 0; curr_row < oTable.rows.length; curr_row++)
	{
		oRow = oTable.rows[curr_row];

		for (curr_cell = 0; curr_cell < oRow.cells.length; curr_cell++)
		{
			var nodelist = oRow.cells[curr_cell].childNodes;
			for(i = 0; i < nodelist.length; i++)
			{
				el = nodelist[i];
				if(el.type=="hidden" || el.type=="text" || el.type=="select-one")
				{
					el_name=el.name;

					if(el.type=="select-one")
					{
						el_value=el.options[el.selectedIndex].value;
					}
					else
						el_value=el.value;

					params+="&"+el_name+"="+el_value;

				}
			}

		}
	}	
	
	parseURL(params,parent_id,'',afterfunction);
}

function insert_formclass_row(table_id,th_name,el_type,el_name,el_value,el_parent,el_class)
/*funkcija kas uztaisa jaunu row ar th un td */
{
	var row=insert_row(table_id,1);

	//uztaisa th ar nosaukumu
	var th=insert_th(row,th_name);
	th.style.width="100px";

	cell=insert_cell(row);
	var el=insert_form_element(el_type,el_name,el_value,cell,el_parent,el_class);

	return el;
}

function get_contact(parent_id,contact_id,client_id)
/* funkcija kas uzzimee kontakta formu ar datiem */
{
	if(!client_id) client_id="";
	get_element(parent_id).innerHTML="";

	var tbl = document.createElement('TABLE');
	tbl.id="contact_details_"+contact_id;
	tbl.className="form-list";
	get_element(parent_id).appendChild(tbl);
	
	//ieliek jaunu row
	/*row=insert_row(tbl.id,1);
	cell=insert_cell(row,"section");
	cell.colSpan=2;*/

	insert_formclass_row(tbl.id,"Nosaukums","text","name","","","input-2");
	insert_formclass_row(tbl.id,"Amats","text","position","","","input-1");
	insert_formclass_row(tbl.id,"Telefons","text","phone","","","input-1");
	insert_formclass_row(tbl.id,"E-pasts","text","email","","","input-2");
	insert_formclass_row(tbl.id,"Fakss","text","fax","","","input-1");

	//ieliek jaunu row
	row=insert_row(tbl.id,1);
	cell=insert_cell(row,"button");
	cell.colSpan=2;
	
	/* ieliekam hidden lauku ar id*/
	insert_form_element("hidden","id","",cell,"","");
	insert_form_element("hidden","client_id",client_id,cell,"","");
	
	//ieliekam submit pogu
	var el=insert_form_element("button","savebutton","Saglabāt",cell,"","button-0");
	var url_suffix ="save_data=1&contactdata=1"; 

	afterSubmitFnc=function(){location.href=location.href};
	
	if(el.attachEvent)
		el.onclick= function(){submit_table_data(tbl.id,url_suffix,parent_id,afterSubmitFnc);};
	else
		el.onclick= function(e){submit_table_data(tbl.id,url_suffix,parent_id,afterSubmitFnc);};

	if(contact_id>0)
	{
		//funkcija kas izpildiisies peec xml ielaades
		afterFunction=function(){load_data("contact",tbl.id,contact_id)};
		url="/xmlstuff.php?load_data=1&contact_id="+contact_id;
		
		get_xml(afterFunction,url);
	}

}

function add_client_link(client_name,client_id)
{
	table = self.opener.document.getElementById("client_link_table");

	nr = table.rows.length;
	row = table.insertRow(nr);
	row.id=nr;
	nr++;
	
	cell=insert_cell(row);
	cell.innerHTML=client_name;
	
	cell=insert_cell(row);
	/* apraksta lauks */
	inp = self.opener.document.createElement("input");
	inp.type = "text";
	inp.name = inp.id = "l_description["+nr+"]";
	inp.className="input-4";
	cell.appendChild(inp);

	/*client_id lauks*/
	inp = self.opener.document.createElement("input");
	inp.type = "hidden";
	inp.name = inp.id = "l_linked_client_id["+nr+"]";
	inp.value=client_id;

	cell.appendChild(inp);

}

function add_user_task(id,user_id,to_date)
{
	row=insert_row("user_task_table",0,"");
	nr=row.id;

	cell=insert_cell(row);
	insert_form_element("hidden","task_id["+nr+"]",id,cell,"","");
	insert_form_element("select","task_user_id["+nr+"]",user_id,cell,"user_list","select-2");
	/*el=insert_form_element("button","calender","",cell,"","button-cal");
	if( el.attachEvent )
		el.onclick= function(){showCalendar("task_to_date["+nr+"]")};
	else
		el.onclick= function(e){showCalendar("task_to_date["+nr+"]")};*/

	cell=insert_cell(row);
	el=insert_form_element("text","task_to_date["+nr+"]",to_date,cell,"","input-0");

	// id + delete
	cell=insert_cell(row);
	var el=insert_form_element("button", "",'',cell,'','button-delete');

	if(id!='')
	{
		var url=location.href;
		params="delete_task="+id;
		if( el.attachEvent )
			el.onclick= function(){delete_field(this);parseURL(params,'',url);};
		else
			el.onclick= function(e){delete_field(this);parseURL(params,'',url);};
	}
	else
	{
		if( el.attachEvent )
			el.onclick= function(){delete_field(this);};
		else
			el.onclick= function(e){delete_field(this);};
	}

}


