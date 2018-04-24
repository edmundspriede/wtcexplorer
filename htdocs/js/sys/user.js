function add_user_config_item(cfg_item_id,key_id,key_value)
{
	if (!cfg_item_id) item_id="";
	if (!key_id) key_id="0";
	if (!key_value) key_value="";

	var row=insert_row("usr_conf_tbl",0);
	var nr = row.id;

	if(nr % 2==0) row.className="even";

	var cell=insert_cell(row);
	insert_form_element("select", "userkey_id["+nr+"]",key_id,cell,'userkey_id','select-3');

	cell=insert_cell(row);
	insert_form_element("text", "key_value["+nr+"]",key_value,cell,'','input-3');

	// id + delete
	cell=insert_cell(row);
	if(cfg_item_id)
	{
		var el=insert_form_element("button","","",cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('delete_item_id',cfg_item_id);this.form.submit()};
		else
			el.onclick= function(e){set_value('delete_item_id',cfg_item_id);this.form.submit()};
	}
	insert_form_element("hidden", "cfg_item_id["+nr+"]",cfg_item_id,cell,'','');
}