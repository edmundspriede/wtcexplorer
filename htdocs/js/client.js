function add_phone(d_id, phone)
{
	if (!d_id) d_id='';
	if (!phone) phone='';

	var row=insert_row("tbl_phones",1);
	var nr=row.id;

	if(nr % 2==0) row.className="even";
/**/
//	var row=get_element("phone_row");

//	cell=get_element('phone_cell');
	cell=insert_cell(row);
//	var nr=cell.id;
	insert_form_element("text","phone["+nr+"]",phone,cell,"","input-1");

	cell=insert_cell(row);
	insert_form_element("hidden","phone_id["+nr+"]",d_id,cell,"","");
	if (d_id)
	{
		el=insert_form_element("button","_"+nr+"]",'',cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('del_phone_id',d_id);this.form.submit();};
		else
			el.onclick= function(e){set_value('del_phone_id',d_id);this.form.submit();};
	}
}
function add_attrib(d_id, type_id)
{
	if (!d_id) d_id='';
	if (!type_id) type_id='';

	var row=insert_row("tbl_attribs",1);
	var nr=row.id;

	if(nr % 2==0) row.className="even";

	cell=insert_cell(row);
	insert_form_element("select","type_id["+nr+"]",type_id,cell,"c_group_list","select-1");

	cell=insert_cell(row);
	insert_form_element("hidden","group_id["+nr+"]",d_id,cell,"","");
	if (d_id)
	{
		el=insert_form_element("button","_"+nr+"]",'',cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('del_group_id',d_id);this.form.submit();};
		else
			el.onclick= function(e){set_value('del_group_id',d_id);this.form.submit();};
	}
}

function add_contact(name,phone_prefix, phone, mobile, fax_prefix, fax,email, title,id,def)
{	
	if (!id) id="";
	if (!name) name=""; 
	if (!phone) phone_prefix="";
	if (!phone) phone="";
    if (!mobile) mobile="";     
	if (!fax) fax_prefix = "";
	if (!fax) fax="";
	if (!email) email="";
	if (!title) title="";
    if (!def) def="0";

	var row=insert_row("table_item_list",1);
	var nr = Math.round(row.id/2); // must not be 0 !!!
	
	if(nr % 2==0) row.className="even";

	var cell = insert_cell(row);
	cell.innerHTML="<strong>Name</strong>";
	cell.className="right";
	cell=insert_cell(row);
	insert_form_element("text", "contact_name["+nr+"]",name,cell,'','input-2');

	cell = insert_cell(row);
	cell.innerHTML="<strong>Phone</strong>";
	cell.className="right";
	cell=insert_cell(row);
	cell.innerHTML="+";
	el = insert_form_element("text", "contact_phone_prefix["+nr+"]",phone_prefix,cell,'','input-0');
	el.setAttribute("maxlength", "3");
	el.style.width = "25px";
	el.style.margin = "1px";
	el = insert_form_element("text", "contact_phone["+nr+"]",phone,cell,'','input-1');
    el.style.margin = "1px";
    cell = insert_cell(row);
    cell.innerHTML="<strong>Mobile</strong>";
    cell.className="right";
    cell=insert_cell(row);  
    el = insert_form_element("text", "contact_mobile["+nr+"]",mobile,cell,'','input-1');
    el.style.margin = "1px";
    row=insert_row("table_item_list",1);
    if(nr % 2==0) row.className="even";
    
	cell = insert_cell(row);
	cell.innerHTML="<strong>Fax</strong>";
	cell.className="right";
	cell=insert_cell(row);
	cell.innerHTML="+";
	el = insert_form_element("text", "contact_fax_prefix["+nr+"]",fax_prefix,cell,'','input-0');
	el.setAttribute("maxlength", "3");
	el.style.width = "25px";
	el.style.margin = "1px";
	el = insert_form_element("text", "contact_fax["+nr+"]",fax,cell,'','input-1');
	el.style.margin = "1px";

	

	cell = insert_cell(row);
	cell.innerHTML="<strong>E-mail</strong>";
	cell.className="right";
	cell=insert_cell(row);
	insert_form_element("text", "contact_email["+nr+"]",email,cell,'','input-2');

    row=insert_row("table_item_list",1);
    if(nr % 2==0) row.className="even";
    
	cell = insert_cell(row);
	cell.innerHTML="<strong>Position</strong>";
	cell.className="right";
	cell=insert_cell(row);
	el = insert_form_element("text", "contact_title["+nr+"]",title,cell,'','input-n');
	el.style.width = "170px";
    cell = insert_cell(row);
    cell.innerHTML="<strong>Default</strong>";
    cell.className="right";
    cell=insert_cell(row);
    if (def == 1) checked = 1; else checked = 0;
    el = insert_form_element("checkbox", "contact_default["+nr+"]",1,cell,'','input-n', checked);
    
	cell=insert_cell(row);
	cell.className="right";
	cell.colSpan=2;
	
	if (id)
	{
		cell.innerHTML="<strong>Delete</strong>";
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
		get_element('code_reg_no').innerHTML="Registation No.";
		get_element('code_name').innerHTML="Name*";
		get_element('code_reg_address').innerHTML="Legal address";
		get_element('code_address').innerHTML="Office address";
	//	get_element('address_type_th').innerHTML="Juridiskā adrese";
		
		get_element('first_name').style.display='none';
		get_element('last_name').style.display='none';
		get_element('registered_name').style.display='inline';
	}
	else
	{
		get_element('code_reg_no').innerHTML="Person ID";
		get_element('code_name').innerHTML="Name Surname*";
		get_element('code_reg_address').innerHTML="Registration address";
		get_element('code_address').innerHTML="Office address";
	//	get_element('address_type_th').innerHTML="Deklarētā adrese";

		get_element('registered_name').style.display='none';
		get_element('first_name').style.display='inline';
		get_element('last_name').style.display='inline';
	}
}

function add_account(id, bank_name, swift, number)
{
	if (!id) id = "";
//	if (!client_id) client_id = "";
	if (!bank_name) bank_name = "";
	if (!number) number = "";
	if (!swift) swift = "";

	var row=insert_row("tbl_accounts", 1);
	var nr = row.id;
	
	if(nr % 2==0)
		row.className="even";

	var cell = insert_cell(row);
	cell.innerHTML="<strong>Bank</strong>";
	cell.className = "right";

	cell=insert_cell(row);
	el = insert_form_element("text", "account_bank_name["+nr+"]", bank_name, cell, "", "input-3");
	el.style.width = "170px";

	cell = insert_cell(row);
	cell.innerHTML="<strong>Swift</strong>";
	cell.className = "right";
	
	cell=insert_cell(row);
	el=insert_form_element("text", "account_swift["+nr+"]", swift, cell, "", "input-0");
	el.style.width="100px";

	cell = insert_cell(row);
	cell.innerHTML="<strong>Account</strong>";
	cell.className="right";

	cell=insert_cell(row);
	el = insert_form_element("text", "account_number["+nr+"]", number, cell, "", "input-3");
	el.style.width = "160px";

	cell=insert_cell(row);
	cell.className="right";

	if (id)
	{
	//	cell.innerHTML="<strong>Dzēst</strong>";
		var el = insert_form_element('button', '_'+nr,'',cell,'','button-delete');
		if(el.attachEvent) is_e=''; else is_e='e';
		el.onclick= function(is_e){set_value('del_bank_id',id);this.form.submit();};
	}
	
	insert_form_element("hidden", "account_id["+nr+"]",id,cell,'','');
}