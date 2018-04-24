
function add_bank_branch(bb_id,bb_swift,bb_name)
{
	if (!bb_id) bb_id="";
	if (!bb_swift) bb_swift=""; 
	if (!bb_name) bb_name=""; 

	var row=insert_row("tbl_bank_branch",1);
	var nr = row.id;
	
	if(nr % 2==0)
		row.className="even";

	var cell=insert_cell(row);
	insert_form_element("text", "bb_swift["+nr+"]",bb_swift,cell,'','input-1');

	cell=insert_cell(row);
	insert_form_element("text", "bb_name["+nr+"]",bb_name,cell,'','input-3');
	
	// id + delete
	cell=insert_cell(row);
	if(bb_id)
	{
		var el=insert_form_element("button","","",cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('delete_bb_id',bb_id);this.form.submit()};
		else
			el.onclick= function(e){set_value('delete_bb_id',bb_id);this.form.submit()};
	}
	insert_form_element("hidden", "bb_id["+nr+"]",bb_id,cell,'','');
}