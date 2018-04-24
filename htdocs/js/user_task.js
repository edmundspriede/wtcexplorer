function add_task(id, user_id, to_date)
{
	if (!id) id="";
	if (!user_id) instrument_id=""; 
	if (!to_date) type_id=""; 

	var row=insert_row("tbl_tasks",1);
	var nr = row.id;
	
	if(nr % 2==0)
		row.className="even";
	
	var cell=insert_cell(row);

	insert_form_element("select","task_user_id["+nr+"]",user_id,cell,"user_id","select-3");

/*	var elStr='<select id="task_user_id['+nr+']" name="task_user_id['+nr+']" class="select-2">';
	elStr+=get_element("user_id").innerHTML;
	elStr+='</select>';
	cell.innerHTML=elStr;
	if (user_id) set_value('task_user_id['+nr+']',user_id);

	cell=insert_cell(row);
	cell.innerHTML='<input type="text" class="input-d" id="task_to_date['+nr+']" name="task_to_date['+nr+']" value="'+to_date+'"/>';

	cell=insert_cell(row);
	cell.innerHTML='<input type="button" class="button-cal" onclick="showCalendar(\'task_to_date['+nr+']\')"/>';
*/
	cell=insert_cell(row);
	cell.cssText='width:99%';
	if (id)
	{
		elStr='<input type="button" class="button-delete" onclick="set_value(\'del_task_id\',\''+id+'\');this.form.submit()"/>';
		elStr+='<input type="hidden" id="task_id['+nr+']" name="task_id['+nr+']" value="'+id+'"/>';
		cell.innerHTML=elStr;
	}
	else cell.innerHTML='&nbsp;';
}

function add_group(d_id,c_id)
{
	if (!c_id) c_id=""; if (!d_id) d_id=""; 
	var row=insert_row("tbl_groups",1);
	var nr = row.id;
	
	if(nr % 2==0) row.className="even";
	
	var cell=insert_cell(row);
	var el=document.createElement('select');
	el.setAttribute('id','c_group_id['+nr+']');
	el.setAttribute('name','c_group_id['+nr+']');
	el.className='select-2';
	var src=get_element('groups_sel');
	for (i=0; i<src.options.length; i++) 
		el.options[i]= new Option(src.options[i].text,src.options[i].value);
	el.style.display='block';
	cell.appendChild(el);
	set_value('c_group_id['+nr+']',c_id);

	cell=insert_cell(row);
	if (d_id)
	{
		var elStr='<input type="button" class="button-delete" onclick="set_value(\'del_group_id\',\''+d_id+'\');this.form.submit()"/>';
		elStr+='<input type="hidden" id="d_group_id['+nr+']" name="d_group_id['+nr+']" value="'+d_id+'"/>';
		cell.innerHTML=elStr;
	}
}

function contact_mod(asnew)
{
	if (get_element('client_id').value>0)
	{
		var row = get_element('contact_tr');
		if (get_element('edit_contact').value=='yes')
		{
			if (asnew)
			{
				put_contact(get_element('contact_id').value,1);
				set_value('contact_id',0);
				row.style.display='';
				set_value('edit_contact','new');
			}
			else
			{
				row.style.display='none';
				set_value('edit_contact','');
			}
			return false;
		}
		
		if (get_element('edit_contact').value=='')
		{
			if (asnew)
			{
				put_contact(get_element('contact_id').value,1);
				set_value('contact_id',0);
				set_value('edit_contact','new');
			}
			else
			{
				put_contact(get_element('contact_id').value);
				set_value('edit_contact','yes');

			}
			row.style.display='';
			return false;
		}
	
		if (get_element('edit_contact').value=='new')
		{
			set_value('edit_contact','');
			row.style.display='none';
			return false;
		}
	}
	else
		alert('Vispirms jânorâda klients!');
	return false;
}

function add_property(id, property_id, text)
{
	if (!text) text = '';
	if (!property_id) property_id = 0;

	var row=insert_row("tbl_props",1);
	var nr = row.id;
	if(nr % 2==0) row.className="even";
	
	var cell = insert_cell(row);
	insert_form_element("select","property_id["+nr+"]",property_id,cell,"props_list","select-1");

	cell = insert_cell(row);
	insert_form_element("input","property_value["+nr+"]",text,cell,"","input-3");

	cell = insert_cell(row);
	if (id)
	{
		insert_form_element("hidden","client_property_id["+nr+"]",id,cell,"","");
		var el = insert_form_element("button","del_prop["+nr+"]","",cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('del_prop_id',id);this.form.submit()};
		else
			el.onclick= function(e){set_value('del_prop_id',id);this.form.submit()};
	}
}

function add_search_group(id )
{
	if (!id) id = '';
	var row=insert_row("tbl_search",0);
	var nr = row.id;

//	var cell = insert_th(row, nr+"");
	var cell = insert_cell(row);
	cell = insert_cell(row);
	insert_form_element("select","s_group_id["+nr+"]",id,cell,"groups_sel","select-2");
}