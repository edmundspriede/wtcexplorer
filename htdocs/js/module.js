
function insert_column(id,title,field_type_id,is_primary)
{
	row=insert_row("classifier_tbl",1);
	nr = row.id-2;
	cell=insert_cell(row);
	insert_form_element("text", "title["+nr+"]",title,cell,'','input-2');
                                     
	cell=insert_cell(row);
	insert_form_element("select", "field_type_id["+nr+"]",field_type_id,cell,'field_type_list','select-1');
	
	//cell=insert_cell(row);
	//var el=insert_form_element("select", "parent_classifier_id["+nr+"]",parent_classifier_id,cell,'classifier_list','select-1');
	//el.disabled=true;
	insert_form_element("hidden", "id["+nr+"]",id,cell,'','');

	cell = insert_cell(row)
	cell.style.textAlign = 'center';
	var el = document.createElement('input');
	el.type = 'checkbox';
	el.checked = (is_primary == 1)?true:false;
	el.setAttribute('id','is_primary['+nr+']');
	el.setAttribute('name','is_primary['+nr+']');
	cell.appendChild(el);
	if( el.attachEvent )
		el.onclick= function(){uncheck_others(el.form,this)};
	else
		el.onclick= function(e){uncheck_others(el.form,this)};

	
	cell=insert_cell(row);
	if(id!='' && is_primary != 1)
	{
		var url=location.href;
		params="classifier_id="+get_value('classifier_id')+"&delete_classifier="+id;
		var el=insert_form_element("button", "",'',cell,'','button-delete');
		if( el.attachEvent )
			el.onmouseup= function(){if (confirm('Press ok to delete!')) {set_value('del_id',id);set_value('form_action','delete');document.forms.DataForm.submit()}};
		else
			el.onmouseup= function(e){if (confirm('Press ok to delete!')) {set_value('del_id',id);set_value('form_action','delete');document.forms.DataForm.submit()}};
	}
}

function uncheck_others(form,box)
{
	for(var i = 0; i < form.length; i++)
		if(form.elements[i].type == "checkbox")
				form.elements[i].checked = false;

	box.checked = true;
}

function add_db_column(id, field_name, role_id, access_level)
{
    if (!field_name) field_name="";
    if (!role_id) role_id="";
    if (!access_level) access_level="";

    row=insert_row("tbl_doc",0);

    if (!id)
    {
      id="";
      del_id = null;
      nr = row.id;
    }
    else
    {
      nr = id;
      del_id = id;
    }

    row.id = "item_"+id;
    if(!type_generated)
    {
        type_html=make_type_listbox();
        type_generated=true;
    }

    cell=insert_cell(row,'left level_2');
    insert_clsf_field("text", "field_name["+nr+"]",field_name, cell,'');
    insert_clsf_field("hidden", "r_id["+nr+"]",id, cell,'');
    insert_clsf_field("hidden", "item_del["+nr+"]",'', cell,'');
//    insert_clsf_field("hidden", "verif_id["+nr+"]",'', cell,'');

    cell=insert_cell(row,'center');
    insert_clsf_field("select", "role_id["+nr+"]",role_id, cell,'');
    
    cell=insert_cell(row,'center');
    insert_clsf_field("select_manual", "access_level["+nr+"]",access_level, cell,'');

    cell=insert_cell(row,'center');
    insert_clsf_field("button", "","delete_item("+del_id+", this)", cell,'button-delete');
}

function insert_clsf_field(input_type,name,value,parent_name,class_name)
{
    var parent=get_element(parent_name)?get_element(parent_name):parent_name;
    var inp;
        if (input_type=="hidden")
        {
            inp = document.createElement("input");
            inp.type = input_type;
            inp.name = name;
            inp.id = name;
            if (class_name)
                inp.className=class_name;
            inp.value = value;
            parent.appendChild(inp);
        }
        else if (input_type=="button")
        {
            inp = document.createElement("input");
            inp.type = input_type;
            if(value)
                inp.value = name;
            if (class_name)
                inp.className=class_name;
            
            parent.appendChild(inp);
            inp.setAttribute("onclick",value);
        }
        else if (input_type=="textNode")
        {
            inp = document.createTextNode(value);
            parent.appendChild(inp);
        }
        else if (input_type=="checkbox")
        {
            inp = document.createElement("input");
            inp.type = input_type;
            inp.name = name;
            inp.id = name;
            if (class_name)
                inp.className=class_name;
            if(value==1)    
                inp.checked=true

            inp.value = 1;
            parent.appendChild(inp);
        }
        else if (input_type=="text")
        {
            inp = document.createElement("input");
            inp.type = input_type;
            inp.name = name;
            inp.id = name;
            if (class_name)
                inp.className=class_name;
            inp.value = value;
            parent.appendChild(inp);
        }
        else if (input_type=="select")
        {
            inp = document.createElement("select");
            inp.id = inp.name = name;
            inp.innerHTML = type_html;
            for (var i=0;i<inp.length;i++)
            {
                if(inp.options[i].value==value)
                inp.selectedIndex=i;
            }
            parent.appendChild(inp);
        }
        else if (input_type=="select_manual")
        {
            inp = document.createElement("select");
            inp.id = inp.name = name;
            inp.innerHTML ='<option value="1">None</option><option value="2">Read</option><option value="3">Edit</option>';
            for (var i=0;i<inp.length;i++)
            {
                if(inp.options[i].value==value)
                inp.selectedIndex=i;
            }
            parent.appendChild(inp);
        }

    return inp;

}


function make_type_listbox(type)
{
    var parameters = "role_list_box=1";
    var html_list_box = ccl_ajax_handler(parameters);
    return html_list_box;
}

function make_type_checkbox(nr, id)
{
    var parameters = "role_checkbox=1&state_input_nr="+nr+"&flow_id="+id;
    var html_checkbox = ccl_ajax_handler(parameters);
    return html_checkbox;
}

function ccl_ajax_handler(parameters)
{

    http_request = false;
      if (window.XMLHttpRequest) {
         http_request = new XMLHttpRequest();
         if (http_request.overrideMimeType) {
            http_request.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
         }
      }
      if (!http_request) {
         alert('Cannot create XMLHTTP instance');
         return false;
      }
      http_request.open('POST', 'ajax_requests.php', false);
      http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      http_request.setRequestHeader("Content-length", parameters.length);
      http_request.setRequestHeader("Connection", "close");
      http_request.send(parameters);
      
      return http_request.responseText;
}