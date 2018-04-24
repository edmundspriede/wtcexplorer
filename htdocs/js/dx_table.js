/*
function addrow(tableid,idpattern)
{
	table=document.getElementById(tableid);
	if (table)
	{
		var mama=table.rows[table.rows.length-1];
		if (mama.className=='cloneroot')
		{
			var newrowindex=table.rows.length-1;
			table.insertRow(newrowindex);
			mamacount=mama.cells.length;
			for (var i=0;i<mamacount;i++)
			{
				table.rows[newrowindex].insertCell(i);
				var content=mama.cells[i].innerHTML;
				var regexp=new RegExp(idpattern,'g');
				table.rows[newrowindex].cells[i].innerHTML=content.replace(regexp,newrowindex);
			}
		}
	}
}

function delrow(tableid,rowid)
{
	if (confirm('Vai tiešām vēlaties dzēst izvēlēto elementu?'))
	{
		table=document.getElementById(tableid);
		if (table)
		{
			table.rows[rowid].style.display='none';
			delcellcount=table.rows[rowid].cells.length;
			for(var i=0;i<delcellcount;i++)
			{
				var matchDelId=new RegExp('(name=[^\[]*)id(_?[0-9]*\[[0-9]*\])','');
				if (table.rows[rowid].cells[i])
				{
					var indata=table.rows[rowid].cells[i].innerHTML;
					if (indata.match(matchDelId))
					{
						table.rows[rowid].cells[i].innerHTML=indata.replace(matchDelId,'$1id_del$2');
					}
					else
					{
						table.rows[rowid].deleteCell();	
					}
				}
			}
		}
	}
}
*/
var indexmap = new Array();

function addCalcGroup(id,number,name)
{
	if (!id) id = '';
	if (!number) number = '';
	if (!name) name = '';

	var t_main = get_element('tbl_main');
	
	if (t_main)
	{
		var newrowindex = t_main.rows.length - 4;
		t_main.insertRow(newrowindex);
		var cell = insert_cell(t_main.rows[newrowindex]);
		cell.colSpan = 2;

		var gr_clone = get_element('tbl_group_clone');

		var content = gr_clone.innerHTML;

		var regexp=new RegExp("@FSGID@",'g');
		cell.innerHTML = content.replace(regexp,newrowindex);

		set_value("grupaid["+newrowindex+"]",id);
		set_value("grupanumurs["+newrowindex+"]",number==''?newrowindex:number);
		set_value("grupanos["+newrowindex+"]",name);

		if (id != '') indexmap[id] = newrowindex;
	}
}

function addGroupRinda(index,group_id,id,numurs,nosaukums,unit_id,cena,koef)
{
	if (!id) id='';
	if (!numurs) numurs='';
	if (!nosaukums) nosaukums='';
	if (!unit_id) unit_id='';
	if (!cena) cena='';
	if (!koef) koef='';
	if (!index) index = '';
	
	if (index == '') index = indexmap[group_id];

	//group_id += '';
	var t_target = get_element("vieniba_"+index);

	var newrowindex = t_target.rows.length;
	t_target.insertRow(newrowindex);

	var content = get_element("tr_clone_rinda").innerHTML;

	var regexp=new RegExp("@FSGID@",'g');
	content = content.replace(regexp,index);

	var regexp=new RegExp("@FSRID@",'g');
	t_target.rows[newrowindex].innerHTML = content.replace(regexp,newrowindex);

	set_value("viennumurs_"+index+"["+newrowindex+"]",numurs==''?newrowindex:numurs);
	set_value("vienid_"+index+"["+newrowindex+"]",id);
	set_value("viennosaukums_"+index+"["+newrowindex+"]",nosaukums);
	set_value("vienvienibas_"+index+"["+newrowindex+"]",unit_id);
	set_value("viencena_"+index+"["+newrowindex+"]",cena);
	set_value("vienkoef_"+index+"["+newrowindex+"]",koef);

	return 0;
}

function addGroupPiemaksa(index,group_id,id,nosaukums,likme)
{
	if (!id) id='';
	if (!nosaukums) nosaukums='';
	if (!likme) unit_id='';
	if (!index) index = '';
	
	if (index == '') index = indexmap[group_id];

	//group_id += '';
	var t_target = get_element("piemaksa_"+index);

	var newrowindex = t_target.rows.length;
	t_target.insertRow(newrowindex);

	var content = get_element("tr_clone_piemaksa").innerHTML;

	var regexp=new RegExp("@FSGID@",'g');
	content = content.replace(regexp,index);

	var regexp=new RegExp("@FSPID@",'g');
	t_target.rows[newrowindex].innerHTML = content.replace(regexp,newrowindex);

	set_value("piemid_"+index+"["+newrowindex+"]",id);
	set_value("piemnos_"+index+"["+newrowindex+"]",nosaukums);
	set_value("piemlikme_"+index+"["+newrowindex+"]",likme);

	return 0;
}

function addGroupKoef(index,group_id,id,nosaukums,likme)
{
	if (!id) id='';
	if (!nosaukums) nosaukums='';
	if (!likme) likme='';
	if (!index) index = '';
	
	if (index == '') index = indexmap[group_id];

	//group_id += '';
	var t_target = get_element("koef_"+index);

	var newrowindex = t_target.rows.length;
	t_target.insertRow(newrowindex);

	var content = get_element("tr_clone_koef").innerHTML;

	var regexp=new RegExp("@FSGID@",'g');
	content = content.replace(regexp,index);

	var regexp=new RegExp("@FSKID@",'g');
	t_target.rows[newrowindex].innerHTML = content.replace(regexp,newrowindex);

	set_value("koefid_"+index+"["+newrowindex+"]",id);
	set_value("koefnos_"+index+"["+newrowindex+"]",nosaukums);
	set_value("koeflikme_"+index+"["+newrowindex+"]",likme);

	return 0;
}

function delRow(what,sid)
{
	if (sid && sid != '')
	{
		var elem = '';
		switch(what)
		{
			case 'rinda':  elem = 'r_del_ids'; break;
			case 'piemaksa':  elem = 'p_del_ids'; break;
			case 'koef':  elem = 'k_del_ids'; break;
			default: return false;
		}
		var el = get_element(elem);
		var id = get_element(sid).value;
		if (id != '') el.value = el.value + "_" + id;
	}

	var row = get_element(sid).parentNode.parentNode;
	row.innerHTML = '';
}

function delGrupa(sid)
{
	if (!confirm("Vai tiešām vēlaties dzēst visu grupu?")) return 0;
	if (sid && sid != '')
	{
		var id = get_element(sid).value;
		if (id != '') 
		{
			var el = get_element('g_del_ids');
			el.value = el.value + "_" + id;
		}
	}

	// l0l
	var row = get_element(sid).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
	row = row.parentNode.parentNode;
	row.innerHTML = '';
	return 0;
}

function addKopPiemaksa(id,nosaukums,likme)
{
	if (!id) id = '';
	if (!likme) likme = '0.00';
	if (!nosaukums) nosaukums = '';

	get_element('tr_kop_piem').style.display = '';

	var row=insert_row("tbl_kop_piem",1);
	var nr = row.id;
	
	var cell=insert_cell(row);
	var el = insert_form_element("text", "kp_nosaukums["+nr+"]",nosaukums,cell,'','');
	el.size = 90;

	cell=insert_cell(row);
	insert_form_element("text", "kp_likme["+nr+"]",likme,cell,'','input-n');

	// id + delete
	cell=insert_cell(row);
	insert_form_element("hidden", "kp_id["+nr+"]",id,cell,'','');

	var el=insert_form_element("button","","",cell,"","button-delete");
	if( el.attachEvent )
		el.onclick= function(){delKopPiemaksa(id,nr);};
	else
		el.onclick= function(e){delKopPiemaksa(id,nr);};
}

function addKopKoef(id,nosaukums,koef)
{
	if (!id) id = '';
	if (!koef) likme = '1.00';
	if (!nosaukums) nosaukums = '';

	get_element('tr_kop_koef').style.display = '';

	var row=insert_row("tbl_kop_koef",1);
	var nr = row.id;
	
	var cell=insert_cell(row);
	var el = insert_form_element("text", "kk_nosaukums["+nr+"]",nosaukums,cell,'','');
	el.size = 90;

	cell=insert_cell(row);
	insert_form_element("text", "kk_koef["+nr+"]",koef,cell,'','input-n');

	// id + delete
	cell=insert_cell(row);
	insert_form_element("hidden", "kk_id["+nr+"]",id,cell,'','');

	var el=insert_form_element("button","","",cell,"","button-delete");
	if( el.attachEvent )
		el.onclick= function(){delKopKoef(id,nr);};
	else
		el.onclick= function(e){delKopKoef(id,nr);};
}

function delKopKoef(id,nr)
{
	table = get_element('tbl_kop_koef');
	table.rows[nr].innerHTML = '';
	if (id && id != '')
	{
		var el = get_element('kk_del_ids');
		el.value = el.value + "_" + id;
	}
}

function delKopPiemaksa(id,nr)
{
	table = get_element('tbl_kop_piem');
	table.rows[nr].innerHTML = '';
	if (id && id != '')
	{
		var el = get_element('kp_del_ids');
		el.value = el.value + "_" + id;
	}
}

function validate_numbers()
{
	var i = 0; var id = ''; var err = false;
	var elems = document.forms.DataForm.elements;
	for (i = 0; i < elems.length; i++)
	{
		if (elems[i].type == 'text')
		{
			id = elems[i].id;
			if (id.substr(0,9) == 'piemlikme' ||
				id.substr(0,6) == 'piemid' ||
				id.substr(0,6) == 'koefid' ||
				id.substr(0,9) == 'koeflikme' ||
				id.substr(0,6) == 'vienid' ||
				id.substr(0,8) == 'viencena' ||
				id.substr(0,8) == 'vienkoef' ||
				id.substr(0,8) == 'kp_likme' ||
				id.substr(0,7) == 'kk_koef' ||
				id.substr(0,16) == 'discount_percent')
			{
				if (isNumber(trimAll(elems[i].value)))
					elems[i].style.background = "#FFF";
				else
				{
					elems[i].style.background = "#F99";
					err = true;
				}
			}
		}
	}

	if (err) alert('Dati netika saglabāti! Atzīmētajos laukos jābūt skaitļiem!');
	return !err;
}

function isNumber(s)
{
	s += '';
	if (s == '' ) return true;
	if (s == ".") return false;
	return (s.search(/^\d*\.?\d*$/) == -1)?false:true;
}

function trimAll(sString)
{
	while (sString.substring(0,1) == ' ')
		sString = sString.substring(1, sString.length);
	
	while (sString.substring(sString.length-1, sString.length) == ' ')
		sString = sString.substring(0,sString.length-1);
	
	return sString;
}

function show_totals(b)
{
	get_element('tbl_totals').style.display = b?'':'none';
	return 0;
}