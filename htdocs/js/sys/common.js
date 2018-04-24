function get_element(id)
{
	var elem;
	if(document.getElementById(id))
	{
		elem = document.getElementById(id)
		return elem;
	}
	else
	{
		return false;
	}
}

function get_element_type(id)
{
	var type;
	if(get_element(id))
	{
		type = get_element(id).type;
		return type;
	}
	else
	{
		return null;
	}
}

function get_value(id)
{
	var object=get_element(id);
	var type=get_element_type(id);

	//ja dropdown
	if(type=="select-one")
		return object.options[object.selectedIndex].value;
	else
		return object.value;

	return null;
}


function set_value(id,value,parent)
//funkcijai padod objekta id un vertibu
//funkcija uzseto vertibu
//ja ir parents tad var no pop up uz parentu setot
{
	var object;
	var type;

	if(!parent)
	{
		if(!get_element(id))
			return false;

		object=get_element(id);
		type=get_element_type(id);
	}
	else
	{
		if(!self.opener.document.getElementById(id))
			return false;

		object=self.opener.document.getElementById(id);
		type=self.opener.document.getElementById(id).type;
	}

	if(type=="hidden" || type=="text" || type=="button")
		object.value=value;

	//ja select box
	if(type=="select-one")
	{
		for (i=0;i<object.options.length;i++)
		{
			if (object.options[i].value == value)
			{
			   object.selectedIndex = i;
			   break;
			}
		}
	}
}



function insert_form_element(input_type,name,value,parent_name,cloning_object,class_name, checked)
{
	var parent=get_element(parent_name)?get_element(parent_name):parent_name;
	var inp;

   
	if(cloning_object)
		inp = get_element(cloning_object).cloneNode(true);
	else
	{
		if (input_type=="select")
			inp = document.createElement("select");
		else
		{
			inp = document.createElement("input");
			inp.type = input_type;
		}
	}

	if (name)
	{
		inp.name = name;
		inp.id = name;
	}

	if (class_name)
	{
		inp.className=class_name;

		/* ja datums */
		if(class_name=="input-d")
		{
			inp.maxLength=10;
		}
	}

	inp.value = value;

	parent.appendChild(inp);

	if (input_type=="checkbox")
    {
		if(checked==1)
			inp.checked = true;
		else
			inp.checked = false;
    }

	return inp;

}

function insert_row(table_id,offset,rowClass)
{
	var table;
	var nr;
	var row;

	if(get_element(table_id))
	{
		table = get_element(table_id);
		nr = table.rows.length-1+offset;
		row = table.insertRow(nr);
		row.id=nr;

		if(rowClass!="")
		{
			if(nr % 2==0)
				row.className=rowClass;
		}
		return row;
	}

}

function insert_cell(parentRow,classname)
{
	var cell=parentRow.insertCell(parentRow.childNodes.length);
	cell.className=classname;
	return(cell);
}

function insert_th(parentRow,content)
{
	oTh = document.createElement("TH");
	oTh.innerHTML = content;
	parentRow.appendChild(oTh);
	return oTh;
}

function delete_field(currCell)
{
	var row=currCell.parentNode.parentNode;
	currCell.parentNode.parentNode.parentNode.deleteRow(row.rowIndex);
}


function round(number,X) {
	// rounds number to X decimal places, defaults to 2
	X = (X!="" ? 2 : X);
	return Math.round(number*Math.pow(10,X))/Math.pow(10,X);
}

function toggle_all_boxes(myForm,box)
{
	var formElementCount = myForm.length;

	for(var i = 0; i < formElementCount; i++)
	{
		if(myForm.elements[i].type=="checkbox")
		{
			myForm.elements[i].checked=box.checked;
		}
	}
}

function pop_up(strURL, strType, strHeight, strWidth)
{
	var newWin = null;
	var strHeight = 450;
	var strWidth = 450;
	var strType = "fixed";

	if (newWin != null && !newWin.closed)
	  	newWin.close();
	var strOptions="";
	if (strType=="console")
		strOptions="resizable,height="+
		strHeight+",width="+strWidth;
	if (strType=="fixed")
	   	strOptions="status,scrollbars=yes,height="+
		strHeight+",width="+strWidth;
	if (strType=="elastic")
	   	strOptions="toolbar,menubar,scrollbars,"+
		"resizable,location,height="+
		strHeight+",width="+strWidth;
 	newWin = window.open(strURL, 'newWin', strOptions);
 	newWin.focus();
	return true;
}

function popUpMsq(msg)
{
	overlib(msg,FGCOLOR,'#FFFFFFF',BGCOLOR,'#ff3e00',STICKY,SNAPX,0,BORDER,3,RIGHT,FIXX);
	return nd();
}

function expand_collapse(pic)
{
	var row=get_element(pic);
	var row_display=row.style.display;

	if(row_display=="none")
	{
		row.style.display="";
		return true;
	}
	else
	{
		row.style.display="none";
		return false;

	}
}

// ======= CB ====== 
function toggle_boxes(form,prefix,chkd)
{
	if (!form) return false;
	if (!prefix) prefix='';
	var el = ''; var id = ''; var i=0;
	for (i=0;i<form.elements.length;i++)
	{
		el = form.elements[i];
		if (el.type=='checkbox' && !el.disabled)
		{
			id = el.getAttribute('id');
			if (id && id.substring(0,prefix.length) == prefix)
			{
				if (chkd) el.checked=true;
				else 	  el.checked=false;
			}
		}
	}
	return false;
}

function new_cell_test(row, input_type, name, size, max_size, disabled, display_style, value, select_name)
{
	cell = row.insertCell(row.childNodes.length);

	if (input_type=="select") // 
		var inp = document.getElementById(""+select_name+"").cloneNode(true);
	else
	{
		var inp = document.createElement("input");
		inp.type = input_type;
	}

	if (name) inp.name = name+"["+nr+"]";
	if (name) inp.id = name+"_"+nr;
	if (size) inp.size = size;
	if (max_size) inp.maxLength=max_size;
	if (disabled) inp.disabled = disabled;
	if (display_style) inp.style.display = display_style;
	if (value) inp.value = value;
	if (input_type=="checkbox") inp.value = 1;

	cell.appendChild(inp);

	if (input_type=="checkbox") inp.checked = true;

	return cell;
}

function new_cell(row,input_type, name, size, max_size,disabled,display_style,value,select_name)
{

	cell = row.insertCell(row.childNodes.length);
	if (input_type=="select") // 
	var inp = document.getElementById(select_name).cloneNode(true);
	 else
	{
		var inp = document.createElement("input");
		inp.type = input_type;
	}

	if (name) inp.name = name+"["+nr+"]";
	if (name) inp.id = name+"_"+nr;
	if (size) inp.size = size;
	if (max_size) inp.maxLength=max_size;
	if (disabled==true) inp.disabled = true;
	if (display_style) inp.style.display = display_style;
	if (value) inp.value = value;

	cell.appendChild(inp);
	if (input_type=="checkbox")
	{
		if(value==1)inp.checked = true; else inp.checked = false;
	}


	return cell;
}

/*
function go(url,confirmtext)
{
	if(confirmtext)
	{
		if(confirm(confirmtext))
			location.href=url;
	}
	else
		location.href=url;
}*/