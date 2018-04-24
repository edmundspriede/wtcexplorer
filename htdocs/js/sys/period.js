function add_period(id,name,from_date,to_date,is_active)
{
	if (!id) id="";
	if (!from_date) from_date="";
	if (!to_date) to_date="";

	var row=insert_row("tbl_periods",0);
	var nr = row.id;
	
	if(nr % 2==0) row.className="even";

	var cell=insert_cell(row);
	insert_form_element("text", "name["+nr+"]",name,cell,'','input-3');

	cell=insert_cell(row);
	insert_form_element("text", "from_date["+nr+"]",from_date,cell,'','input-d');

	cell=insert_cell(row);
	var el=insert_form_element("button", "fdcal"+nr,"",cell,'','button-cal');
	if(el.attachEvent)
		el.onclick= function(){showCalendar("from_date["+nr+"]");};
	else
		el.onclick= function(e){showCalendar("from_date["+nr+"]");};

	cell=insert_cell(row);
	insert_form_element("text", "to_date["+nr+"]",to_date,cell,'','input-d');

	cell=insert_cell(row);
	var el=insert_form_element("button", "tdcal"+nr,"",cell,'','button-cal');
	if(el.attachEvent)
		el.onclick= function(){showCalendar("to_date["+nr+"]");};
	else
		el.onclick= function(e){showCalendar("to_date["+nr+"]");};

	// is active ?
	cell = insert_cell(row);
	el = insert_form_element("checkbox",'is_active['+nr+']','',cell,'','cb');
	if (is_active == 1)
		el.checked = true;
	if(el.attachEvent)
		el.onclick= function(){uncheck_others(this);};
	else
		el.onclick= function(e){uncheck_others(this);};

	// id + delete
	cell=insert_cell(row);
	if(id)
	{
		var el=insert_form_element("button","","",cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('del_period_id',id);this.form.submit()};
		else
			el.onclick= function(e){set_value('del_period_id',id);this.form.submit()};
	}
	insert_form_element("hidden", "id["+nr+"]",id,cell,'','');
}

function uncheck_others(cbox)
{
	var el = ''; var i=0; var state = cbox.checked;
	if (state)
		for (i=0;i<cbox.form.elements.length;i++)
		{
			el = cbox.form.elements[i];
			if (el.type=='checkbox' && !el.disabled)
					el.checked = false;
		}
	cbox.checked = state;
	return false;
}