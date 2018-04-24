function getElement(id, alwaysObject)
{
	var elem;
	try
	{
		if (document.all)
			elem = document.all[id];
		else
			elem = document.getElementById(id);
		if (!elem && alwaysObject) elem = {};
		return elem;
	}
	catch (e)
	{
		return null;
	}
}

function optionValue(name)
{
	try
	{
		return getElement(name).options[getElement(name).selectedIndex].value;
	}
	catch (e)
	{
		return null;
	}
}

function selectLength(name)
{
	try
	{
		return getElement(name).options.length;
	}
	catch (e)
	{
		return null;
	}
}


function optionText(name)
{
	try
	{
		return getElement(name).options[getElement(name).selectedIndex].text;
	}
	catch (e) 
	{
		return null;
	}
}

function selectOption(select, value)
{
	if (!value)
		value="";
	else
		value=value.toString();
	try
	{
		for (var i=0; i<select.options.length; i++)
		{
			if (select.options[i].value.toString()==value)
			{
				select.selectedIndex=i;
				return true;
			}
		}
	}
	catch (e) {}
	return false;
}
/*
M.Junkers
Funkcijas NEW_CELL - pievieno  dotajai tabulas rindai jaunu ievades shuunu ar lauku tajaa
	row - rinas objekts
	input_type - 1 - input, 2 - dropdown klonesana (jabut noraadiitam drop_down_name
	name - lauka nosaukums prieksh submit formas
	size - html input lauka size atributs
	max_size  - maksimalais input garums
	display_style - block, ja ir dropdowns
	value - vertîba
	select_name - id select tipa objektam
*/

function new_cell(row,input_type, name, size, max_size,disabled,display_style,value,select_name)
{

	cell = row.insertCell();
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

function onenter_submit(formname)
//funkcija submito formo,nospiezhot ENTER, jaapadod formas name
{
	if (window.event.keyCode == 13)
	{
	   document.forms[""+formname+""].submit();
	   return true;
	}
	else
	   return false;
}

function enterPress()
//funkcija atgrieþ true vai false
{
	if (window.event.keyCode == 13)
	   return true;
	else
	   return false;
}
