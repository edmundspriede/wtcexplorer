
function add_group(id,name,checked,parent_id)
{
	if (!id) return false;
	if (!name) name='';
	if (!parent_id || parent_id==0) parent_id='';
	
	var row=insert_row("tbl_groups",0);
	//nr = row.id;
	
	var cell = insert_cell(row);
	cell.innerHTML=name;
	if(!parent_id)cell.style.fontWeight = "bold";

	if(!parent_id)
	{
		cell=insert_cell(row);		
		el=insert_form_element("checkbox","parent["+id+"]",'',cell,"","cb");
		if(el.attachEvent) is_e=''; else is_e='e';
		el.onclick= function(is_e){toggle_boxes(this.form,"groupbox["+id+"]",this.checked)};
		parent_id = id; // lai paðu arî iezîmç
	}
	else
		cell.colSpan = '2';

	cell=insert_cell(row);
	insert_form_element("checkbox","groupbox["+parent_id+"]["+id+"]",checked,cell,"","cb");

	return false;
}
