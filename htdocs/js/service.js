function opt_text(combo_id,opt_val)
{
	if (!opt_val) return '';
	var combo=get_element(combo_id);
	if (combo)
		for(i =0; i < combo.options.length; i++)
			if (combo.options[i].value==opt_val)
				return combo.options[i].innerHTML;
	return 'error';
}

function add_service_item(id,instrument_id,type_id,model,serial_no,manuf_id,status_id,cnt,notes)
{
	if (!id) id="";
	if (!instrument_id) instrument_id=""; 
	if (!type_id) type_id=""; 
	if (!model) model=""; 
	if (!serial_no) serial_no="";
	if (!manuf_id) manuf_id="";
	if (!status_id) status_id="";
	if (!cnt) cnt=1;
	if (!notes) notes="";

	var row=insert_row("tbl_items",1);
	var nr = row.id;
	
	if(nr % 2==0)
		row.className="even";
	
	var cell=insert_cell(row);

	if (instrument_id >0)
	{
		cell.innerHTML='<a href="/?mod=2100&amp;id='+instrument_id+'"><img src="/images/more.gif"/></a>';
		
		cell=insert_cell(row);
		cell.innerHTML=opt_text('type_list',type_id)+'<input type="hidden" id="item_type_id['+nr+']" name="item_type_id['+nr+']"/>';
		set_value('item_type_id['+nr+']',type_id);

		cell=insert_cell(row);
		cell.innerHTML=model+'<input type="hidden" id="item_model['+nr+']" name="item_model['+nr+']"/>';
		set_value('item_model['+nr+']',model);

		cell=insert_cell(row);
		cell.innerHTML=serial_no+'<input type="hidden" id="item_serial_no['+nr+']" name="item_serial_no['+nr+']"/>';
		set_value('item_serial_no['+nr+']',serial_no);

		cell=insert_cell(row);
		cell.innerHTML=opt_text('manuf_list',manuf_id)+'<input type="hidden" id="item_manuf_id['+nr+']" name="item_manuf_id['+nr+']"/>';
		set_value('item_manuf_id['+nr+']',manuf_id);

	}
	else
	{
		cell=insert_cell(row);
		var elStr='<select id="item_type_id['+nr+']" name="item_type_id['+nr+']" class="select-1">';
		elStr+=get_element('type_list').innerHTML;
		elStr+='</select>';
		cell.innerHTML=elStr;
		set_value('item_type_id['+nr+']',type_id);

		cell=insert_cell(row);
		cell.innerHTML='<input type="text" class="input-1" id="item_model['+nr+']" name="item_model['+nr+']" maxlength="50" value="'+model+'"/>';

		cell=insert_cell(row);
		cell.innerHTML='<input type="text" class="input-1" id="item_serial_no['+nr+']" name="item_serial_no['+nr+']" maxlength="50" value="'+serial_no+'"/>';

		cell=insert_cell(row);
		elStr='<select id="item_manuf_id['+nr+']" name="item_manuf_id['+nr+']" class="select-1">';
		elStr+=get_element('manuf_list').innerHTML;
		elStr+='</select>';
		cell.innerHTML=elStr;
		set_value('item_manuf_id['+nr+']',manuf_id);
	}

	cell=insert_cell(row);
	elStr='<select id="item_status_id['+nr+']" name="item_status_id['+nr+']" class="select-1">';
	elStr+=get_element('status_list').innerHTML;
	elStr+='</select>';
	cell.innerHTML=elStr;
	if (status_id)
		get_element('item_status_id['+nr+']').value=status_id;

	cell=insert_cell(row);
	cell.innerHTML='<input type="text" size="3" id="item_cnt['+nr+']" name="item_cnt['+nr+']" value="'+cnt+'" maxlength="4"/>';

	cell=insert_cell(row);
	cell.innerHTML='<textarea class="textarea-1" id="item_notes['+nr+']" name="item_notes['+nr+']">'+notes+'</textarea>';

	cell=insert_cell(row);
	elStr='';
	if (id)
	{
		elStr='<input type="button" class="button-delete" onclick="set_value(\'del_item_id\','+id+');document.forms.DataForm.submit();"/>';
		elStr+='<input type="hidden" id="item_id['+nr+']" name="item_id['+nr+']" value="'+id+'"/>';
	}
	elStr+='<input type="hidden" id="item_instrument_id['+nr+']" name="item_instrument_id['+nr+']" value="'+instrument_id+'"/>';
	cell.innerHTML=elStr;
}

function checkClientSave()
{
	if (get_element('client_id').value >0)
	{
		set_value('action','save');
		document.forms.DataForm.submit();
	}
	else
		alert('Vispirms jānorāda pasūtītājs!');
	return 0;
}