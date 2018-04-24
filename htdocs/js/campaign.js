function add_group(id,name,checked)
{
	if (!id) return false;
	if (!name) name='';
	
	var row=insert_row("tbl_groups",0);
	//nr = row.id;
	
	var cell = insert_cell(row);
	cell.innerHTML=name;
	cell.cssText="white-space:nowrap";
	cell=insert_cell(row);
	var el = document.createElement('INPUT');
	el.type='checkbox';
	el.className='cb';
	el.setAttribute('id','groupbox['+id+']');
	el.setAttribute('name','groupbox['+id+']');
	cell.appendChild(el);
		if (checked) el.setAttribute('checked','checked');
		
	return false;
}


function add_field(caption,key,checked)
{
	if (!key) return false;
	if (!caption) caption='';
	
	var row=insert_row("tbl_fields",1);
	//nr = row.id;
	
	var cell = insert_cell(row);
	cell.innerHTML=caption;
	cell.cssText="white-space:nowrap";
	cell=insert_cell(row);
	
	var el = document.createElement('INPUT');
	el.type='checkbox';
	el.className='cb';
	el.setAttribute('id','fieldkeys['+key+']');
	el.setAttribute('name','fieldkeys['+key+']');
	cell.appendChild(el);
	if (checked) 
		 el.setAttribute('checked','checked');

	return false;
}

function trimAll(sString)
{
	while (sString.substring(0,1) == ' ')
		sString = sString.substring(1, sString.length);
	while (sString.substring(sString.length-1, sString.length) == ' ')
		sString = sString.substring(0,sString.length-1);

	return sString;
}

function get_mailaddrs(delim)
{
	if (!delim) delim=', ';
	var table=get_element('tbl_results');
	var i=0; var row; var cell; var emails=''; var cb; var el;
	for (i=2; i<table.rows.length-1; i++)
	{
		row = table.rows[i];
		cell = row.cells[row.cells.length-2];
		cb = row.cells[row.cells.length-1].childNodes[0];
		if (trimAll(cell.innerHTML) != '' && cb.checked)
		{
			emails += (trimAll(cell.innerHTML)); emails += delim;
		}
	}
	if (emails != '')
		emails = emails.substring(0,emails.length - delim.length);

	el = get_element('mail_area');
	el.value=emails;
	el.style.display='';
	el.focus();
	get_element('btn_close').style.display='';
}

function insert_subsection(caption)
{
	if (!caption) caption='';
	var row = insert_row('tbl_fields',1);
	var cell = insert_cell(row);
	cell.colSpan=2;
	cell.className=('subsection');
	cell.innerHTML=caption;
	return false;
}