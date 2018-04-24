function add_cost_item(id,unit_id,name,amount,price,discount,total)
{
	if (!id) id=""; 
	if (!name) name=""; 
	if (!unit_id) unit_id=""; 
	if (!amount) amount="1"; 
	if (!price) price="0.00";
	if (!discount) discount="0.00";
	if (!total) total="0.00";

	var el;
	var row=insert_row("tbl_items",0);
	var nr=row.id;

	if(nr % 2==0) row.className="even";

	var cell=insert_cell(row);

	el = document.createElement('textarea');
	el.setAttribute("id","item_name["+nr+"]");
	el.setAttribute("name","item_name["+nr+"]");
	el.setAttribute("cols","30");
	el.setAttribute("rows","2");
	cell.appendChild(el);
	el.innerHTML = name;

	cell = insert_cell(row);
	insert_form_element("select","item_unit_id["+nr+"]",unit_id,cell,"units_list","select-0");

	cell = insert_cell(row);
	el = insert_form_element("text","item_amount["+nr+"]",amount,cell,"","right");
	el.size = 4;

	cell = insert_cell(row);
	el = insert_form_element("text","item_price["+nr+"]",price,cell,"","");
	el.size = 7;

	cell = insert_cell(row);
	el = insert_form_element("text","item_discount["+nr+"]",discount,cell,"","right");
	el.size = 4;
	cell.appendChild(document.createTextNode("%"));

	cell = insert_cell(row);
	cell.innerHTML = "<strong>"+total+"</strong>&nbsp;";
	cell.className = 'right';

	cell=insert_cell(row);
	if (id)
	{
		el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('del_item_id',id);this.form.submit();};
		else
			el.onclick= function(e){set_value('del_item_id',id);this.form.submit();};
	}
	
	insert_form_element("hidden","item_id["+nr+"]",id,cell,"","");
}