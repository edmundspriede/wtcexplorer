function add_flow_pair(id,prev_id,next_id,bsfl_roles,bsfl_role_names)
{
	if (!id) id='';
	if (!prev_id) prev_id='';
	if (!next_id) next_id='';
    if (!bsfl_roles) bsfl_roles='';
    if (!bsfl_role_names) bsfl_role_names='';



	var row=insert_row('tbl_items',1);
	var nr = row.id;

	if(nr % 2==0) row.className='even';
//                  alert("PANTS");
	var cell=insert_cell(row);
	insert_form_element('select','i_prev_id['+nr+']',prev_id,cell,'csta_sel','select-3');

	var cell=insert_cell(row);
	insert_form_element('select','i_next_id['+nr+']',next_id,cell,'csta_sel','select-3');

    var cell=insert_cell(row,'center');
    insert_clsf_field("textNode", "",bsfl_role_names, cell,'');

    var cell=insert_cell(row,'center');
    insert_clsf_field("button", "","add_state_roles("+nr+")", cell,'button-add');
    insert_clsf_field("hidden", "s_flow_roles["+nr+"]",bsfl_roles, cell,'');

	// id + delete
	cell=insert_cell(row);
	if(id)
	{
		var el=insert_form_element('button','','',cell,'','button-delete');
		if( el.attachEvent )
			el.onclick= function(){set_value('del_item_id',id);this.form.submit()};
		else
			el.onclick= function(e){set_value('del_item_id',id);this.form.submit()};
	}
	insert_form_element('hidden', 'i_id['+nr+']',id,cell,'','');

}

function add_state_roles(nr)
{
flow_id = document.getElementById('i_id['+nr+']').value;

type_html=make_type_checkbox(nr, flow_id);

my_window= window.open ("","mywindow1","status=1,width=300,height=350");
my_window.document.write(type_html);
}

function add_roles(field_id, form)
{
  var a_temp_roles = new Array();
  var a_names = new Array();

  checkboxs = form.getElementsByTagName("input");
  for(var i=0; i<checkboxs.length; i++)
  {
    if(checkboxs[i].checked)
      a_temp_roles.push(checkboxs[i].value);
  }

//  alert(a_names.toString());

  set_value('s_flow_roles['+field_id+']',a_temp_roles.toString());
//document.getElementById('DataForm').submit();
set_value('form_action','save');
validateForm();

my_window.close();
}
