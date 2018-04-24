function insert_column(id,title,field_type_id,parent_classifier_id)
{
	row=insert_row("classifier_tbl",1);
	nr = row.id-2;
	cell=insert_cell(row);
	insert_form_element("text", "title["+nr+"]",title,cell,'','input-2');

	cell=insert_cell(row);
	insert_form_element("select", "field_type_id["+nr+"]",field_type_id,cell,'field_type_list','select-1');
	
	cell=insert_cell(row);
	var el=insert_form_element("select", "parent_classifier_id["+nr+"]",parent_classifier_id,cell,'classifier_list','select-1');
	el.disabled=true;
	insert_form_element("hidden", "id["+nr+"]",id,cell,'','');
	
	if(id!='')
	{
		cell=insert_cell(row);
		var url=location.href;
		params="classifier_id="+get_value('classifier_id')+"&delete_classifier="+id;
		var el=insert_form_element("button", "",'',cell,'','button-delete');
		if( el.attachEvent )
			el.onclick= function(){if (confirm('Press ok to delete!')) {delete_field(this);parseURL(params,'',url);}};
		else
			el.onclick= function(e){if (confirm('Press ok to delete!')) {delete_field(this);parseURL(params,'',url);}};
	}
}

