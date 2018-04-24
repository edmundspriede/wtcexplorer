function add_payment_item(
            item_id, invoice_id, invoice_number,
            amount, amount_orginal, currency_id,
            diff, invoice_amt,invoice_date
){
	if (!item_id) item_id=""; 
	if (!invoice_id) invoice_id=""; 
	if (!invoice_number) invoice_number=""; 
	if (!amount) amount="0.00";
	if (!amount_orginal) amount_orginal = '';
	if (!invoice_amt) invoice_amt = '';
	if (!currency_id) currency_id = 0;

	var el;
	var table = get_element('table_item_list');

	var nr = table.rows.length;
	var row = table.insertRow(nr-1);
//	row=insert_row("payment_tbl",0);

	if(nr % 2==0) row.className="even";

	var cell=insert_cell(row);
	if(invoice_id != '')
		cell.innerHTML = '<a class="more" href="/?mod=163&id='+invoice_id+'" >'+invoice_number+'&nbsp;</a> ';

    cell = insert_cell(row, "right");
    cell.innerHTML = invoice_date;

	cell = insert_cell(row, "right");
	cell.innerHTML = invoice_amt;

	cell = insert_cell(row, "right");
	el = insert_form_element("text","fpit_amount["+nr+"]",amount,cell,"","input-n");
	//cell.innerHTML = '<input type="text" style="width:60px" class="input-n" id="fpit_base_amount['+nr+']" name="fpit_base_amount['+nr+']" value="'+amount+'" /> ';

	cell = insert_cell(row);
	el = insert_form_element("select", "fpit_currency_id["+nr+"]", currency_id, cell, "currency_list", "select-0");

	cell = insert_cell(row, "right");
	cell.innerHTML = amount_orginal;
    
    cell = insert_cell(row, "right");
    cell.innerHTML = diff;

	cell=insert_cell(row, "right");
	if (item_id)
	{
		cell.innerHTML = '<input type="button" class="button-delete" id="item_'+nr+'" onClick="set_value('+"'"+"del_item_id"+"'"+","+item_id+');this.form.submit()" /> ';
	}

	cell.innerHTML += '<input type="hidden" name="fpit_id['+nr+']" value="'+item_id+'" /> ';
	el = insert_form_element("hidden","invoice_id["+nr+"]",invoice_id,cell,"","input-0");
	insert_form_element("hidden","fpit_currency_id["+nr+"]",currency_id,cell,"","input-0");
}


/*function add_payment(id,account_id,payment_date,payment_number,payment_amount,invoice_number,object_number)
{
	if (!id) id="";
	if (!object_number) object_number=""; 
	if (!invoice_number) invoice_number=""; 
	if (!account_id) account_id=""; 
	if (!payment_number) payment_number="";
	if (!payment_date) payment_date="";
	if (!payment_amount) payment_amount="";

	row=insert_row("payment_tbl",0);
	nr = row.id;
	
	if(nr % 2==0)
		row.className="even";

	calfunc=function(el,prefix)
	{
		var b=el.id.indexOf('\[');
		var e=el.id.indexOf('\]');

		var suff=el.id.substring(b+1,e);
		showCalendar(prefix+"["+suff+"]");
	};	


	cell=insert_cell(row);
	insert_form_element("select", "account_id["+nr+"]",account_id,cell,'acc_sel','select-2');
	
	cell=insert_cell(row);
	insert_form_element("text", "payment_date["+nr+"]",payment_date,cell,'','input-d');

	// receive date calendar
	cell=insert_cell(row);
	var el=insert_form_element("button", "buttons["+nr+"]","",cell,'','button-cal');
	if(el.attachEvent)
		el.onclick= function(){calfunc(this,'payment_date');};
	else
		el.onclick= function(e){calfunc(this,'payment_date');};

	cell=insert_cell(row);
	insert_form_element("text", "payment_number["+nr+"]",payment_number,cell,'','input-0');

	cell=insert_cell(row);
	insert_form_element("text", "payment_amount["+nr+"]",payment_amount,cell,'','input-1');

	cell=insert_cell(row);
	insert_form_element("text", "js_invoice_number["+nr+"]",invoice_number,cell,'','input-0');

	cell=insert_cell(row);
	insert_form_element("text", "object_number["+nr+"]",object_number,cell,'','input-0');

	
	// id + delete
	cell=insert_cell(row);
	if(id)
	{
		var el=insert_form_element("button","","",cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('delete_item_id',id);this.form.submit()};
		else
			el.onclick= function(e){set_value('delete_item_id',id);this.form.submit()};
	}
	insert_form_element("hidden", "id["+nr+"]",id,cell,'','');
}*/
