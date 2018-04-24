function add_trade_item(item_id, trade_id, date, type_id, price, payment, invoice, is_payed, invoice_id, item_payment_nr)
{
	if (!item_id) item_id=""; 
	if (!trade_id || trade_id=='0') trade_id="";
	if (!date) date=""; 
	if (!type_id) type_id=""; 
	if (!price) price="0.00";
	if (!payment || payment=='0.00') payment=""; 
	if (!invoice || invoice=='0.00') invoice=""; 
	if (!is_payed) is_payed=""; 
	if (!invoice_id) invoice_id=""; 
	if (!item_payment_nr) item_payment_nr = "2/4";
	
		var el;
	var row=insert_row("tbl_payment",1);
	var nr=row.id;

	if(nr % 2==0) row.className="even";

	cell = insert_cell(row);
	cell.className = 'center';
	//insert_form_element("text","item_payment_nr["+nr+"]",item_payment_nr,cell,"", "input-n");
	if (item_payment_nr) cell.innerHTML = "2/4";

	cell=insert_cell(row);
	el=insert_form_element("text","item_date["+nr+"]",date,cell,"","input-0");

	el=insert_form_element("button","calender","",cell,"","button-cal");
	if(el.attachEvent) is_e=''; else is_e='e';
	el.onclick= function(is_e){showCalendar("item_date["+nr+"]")};
	
	cell=insert_cell(row);
	el=insert_form_element("text","item_price["+nr+"]",price,cell,"","input-n");

	cell = insert_cell(row);
	insert_form_element("select","item_type_id["+nr+"]",type_id,cell,"payment_type_list","select-0");


	cell = insert_cell(row);
	el=insert_form_element("checkbox","is_payed["+nr+"]",is_payed,cell,"","cb");
	el.disabled = true;

	cell = insert_cell(row);
	//cell.className = 'right';
	if(payment) cell.innerHTML = ""+ payment +"";

	cell = insert_cell(row);
	cell.className = 'right';
	if(invoice_id) 
	{
		cell.innerHTML = "<a class='more' href='/?mod=163&id="+invoice_id+"'>"+ invoice +"</a>";
		if(invoice!=price) cell.style.backgroundColor = '#FFB7B7';
	}
	else if(item_id && type_id==1) 
	{
		el = insert_form_element("button","_a_"+nr,'',cell,"","button-add");
		el.onclick= function(is_e){location.href="/?mod=163&view_mode=edit&trade_payment_id="+item_id+""};
	}

	cell=insert_cell(row);
	if (item_id)
	{
		el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
		el.onclick= function(is_e)
			{ if(confirm('Vai tiešām vēlaties dzēst ierakstu?')) 
				{set_value('del_item_id',item_id);this.form.submit();};
			};
		
	}
	
	insert_form_element("hidden","item_id["+nr+"]",item_id,cell,"","");
	insert_form_element("hidden","item_trade_id["+nr+"]",trade_id,cell,"","");
}


function switch_trade(type)
{
	var displayName = 'none';
	var isIE = get_element('bank_row').attachEvent; // I.Exporer
	if(isIE) displayRow = 'block';
	else	 displayRow = 'table-row';

	// ----
	if(type == '1' || type == '3') 	displayName = 'none'; 
	else displayName = displayRow;

	typeOne = new  Array('bank_row', 'bank_2_row', 'time_row')
	for(i=0; typeOne[i]; i++) 
		get_element(typeOne[i]).style.display = displayName;

	// apdrosinashana
	insure = new  Array('insure_row', 'due_row', 'id_row', 'polise_row')
	if(type == '3')
	{
		displayName = displayRow;
		get_element('cnt_th').innerHTML= 'Parakstītā prēmija';
		get_element('send_date_th').innerHTML = 'Izrakstīšanas datums *';
		get_element('accept_date_th').innerHTML = 'Sākuma datums *';
		get_element('notes_th').innerHTML = 'Rēķina Nr';
	}
	else
	{
		displayName = 'none';
		get_element('cnt_th').innerHTML = 'Summa / Skaits';
		get_element('send_date_th').innerHTML = 'Iesniegšanas datums *';
		get_element('accept_date_th').innerHTML = 'Akceptēšanas datums';
		get_element('notes_th').innerHTML = 'Piezīmes';
	}
	for(i=0; insure[i]; i++)
		get_element(insure[i]).style.display = displayName;
}