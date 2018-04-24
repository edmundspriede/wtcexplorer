
function add_search_product(id )
{
	if (!id) id = '';
	var row=insert_row("tbl_search",0);
	var nr = row.id;

//	var cell = insert_th(row, nr+"");
	var cell = insert_cell(row, 'right');
//	cell = insert_cell(row);
	cell.colSpan = "2";
	insert_form_element("select","s_group_id["+nr+"]",id,cell,"product_list","select-3");
}

function add_product(d_id, c_id)
{
	if (!d_id) d_id='';
	if (!c_id) c_id='';

	var row=insert_row("tbl_products",1);
	var nr=row.id;

	if(nr % 2==0) row.className="even";

	cell=insert_cell(row);
	el=insert_form_element("select","c_product_id["+nr+"]",c_id,cell,"product_list","select-3");
//	el.style.width = '300px';

	cell=insert_cell(row);
	insert_form_element("hidden","d_product_id["+nr+"]",d_id,cell,"","");
	if (d_id)
	{
		el=insert_form_element("button","_"+nr+"]",'',cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('del_product_id',d_id);this.form.submit();};
		else
			el.onclick= function(e){set_value('del_product_id',d_id);this.form.submit();};
	}
}

function add_refuse(d_id, c_id)
{
	if (!d_id) d_id='';
	if (!c_id) c_id='';

	var row=insert_row("tbl_refuse",1);
	var nr=row.id;

	if(nr % 2==0) row.className="even";

	cell=insert_cell(row);
	el= insert_form_element("select","c_refuse_id["+nr+"]",c_id,cell,"refuse_list","select-3");
//	el.style.width = '300px';

	cell=insert_cell(row);
	insert_form_element("hidden","d_refuse_id["+nr+"]",d_id,cell,"","");
	if (d_id)
	{
		el=insert_form_element("button","_"+nr+"]",'',cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('del_refuse_id',d_id);this.form.submit();};
		else
			el.onclick= function(e){set_value('del_refuse_id',d_id);this.form.submit();};
	}
}

function add_contact(name,phone,fax,email, position,id)
{	
	if (!id) id="";
	if (!name) name=""; 
	if (!phone) phone="";
	if (!phone) fax="";
	if (!email) email="";
	if (!position) position="";

	var row=insert_row("table_item_list",1);
	var nr = Math.round(row.id/2); // must not be 0 !!!
	
	if(nr % 2==0) row.className="even";

	var cell = insert_cell(row);
	cell.innerHTML="<strong>Nosaukums</strong>";
	cell.className="right";
	cell=insert_cell(row);
	insert_form_element("text", "contact_name["+nr+"]",name,cell,'','input-2');

	cell = insert_cell(row);
	cell.innerHTML="<strong>Tālrunis</strong>";
	cell.className="right";
	cell=insert_cell(row);
	insert_form_element("text", "contact_phone["+nr+"]",phone,cell,'','input-1');

	cell = insert_cell(row);
	cell.innerHTML="<strong>Fakss</strong>";
	cell.className="right";
	cell=insert_cell(row);
	insert_form_element("text", "contact_fax["+nr+"]",fax,cell,'','input-1');

	row=insert_row("table_item_list",1);
	if(nr % 2==0) row.className="even";

	cell = insert_cell(row);
	cell.innerHTML="<strong>E-pasts</strong>";
	cell.className="right";
	cell=insert_cell(row);
	insert_form_element("text", "contact_email["+nr+"]",email,cell,'','input-2');

	cell = insert_cell(row);
	cell.innerHTML="<strong>Amats</strong>";
	cell.className="right";
	cell=insert_cell(row);
	insert_form_element("text", "contact_position["+nr+"]",position,cell,'','input-1');

	cell=insert_cell(row);
	cell.className="right";
	cell.colSpan=2;
	
	if (id)
	{
		cell.innerHTML="<strong>Dzēst</strong>";
		var el = insert_form_element('button', '_'+nr,'',cell,'','button-delete');
		if(el.attachEvent)
			el.onclick= function(){set_value('del_contact_id',id);this.form.submit();};
		else
			el.onclick= function(e){set_value('del_contact_id',id);this.form.submit();};
	}

	insert_form_element("hidden", "contact_id["+nr+"]",id,cell,'','');
}

function switch_client_type(bool)
{
	if (bool) // J
	{
		get_element('code_type_th').innerHTML="Reģistrācijas Nr";
		get_element('address_type_th').innerHTML="Juridiskā adrese";
		get_element('code_name').innerHTML="Nosaukums*";
		
		get_element('dcmp_firstname').style.display='none';
		get_element('dcmp_surname').style.display='none';
		get_element('dcmp_registered_name').style.display='inline';
	}
	else
	{
		get_element('code_type_th').innerHTML="Personas kods";
		get_element('address_type_th').innerHTML="Deklarētā adrese";
		get_element('code_name').innerHTML="Vārds Uzvārds*";

		get_element('dcmp_registered_name').style.display='none';
		get_element('dcmp_firstname').style.display='inline';
		get_element('dcmp_surname').style.display='inline';
	}
}