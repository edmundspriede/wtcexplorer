function swap_desk(current)
{
		var h=desk_set.length;
		for(var j=0;j<h;j++)
		{
			deskhref=get_element("deskitem_"+desk_set[j]);
			if (deskhref)
			{
				if (desk_set[j]==current)
					deskhref.className='sel';
				else				
					deskhref.className='';

				desk=get_element("modcontainer_"+desk_set[j]);
				if (desk) desk.style.display="none";
			}
		}

		try
		{
			if (current==current_desk)
			{
				getElement("tl_idx").style.display="block";
				getElement("tl_content").style.display="block";
			}
			else
			{
				getElement("tl_idx").style.display="none";
				getElement("tl_content").style.display="none";
			}
		}
		catch(e) {}
		
		desk=get_element("modcontainer_"+current);
		if (desk) desk.style.display="block";
}


function light_desk(current)
{
	if (document.getElementById)
	{
		var h=desk_set.length;
		for(var j=0;j<h;j++)
		{
			if (document.getElementById("deskitem_"+desk_set[j]))
			{
				if (document.getElementById("deskitem_"+desk_set[j]).className!='sel')
				{
					if (desk_set[j]==current) document.getElementById("deskitem_"+desk_set[j]).className='sel';
					else document.getElementById("deskitem_"+desk_set[j]).className='';

				}
			}
		}
	}
}


function dim_desk(current)
{
	if (document.getElementById)
	{
		var h=desk_set.length;
		for(var j=0;j<h;j++)
		{
			if (document.getElementById("deskitem_"+desk_set[j]))
			{
				if (document.getElementById("deskitem_"+desk_set[j]).className!='sel')
				{
					document.getElementById("deskitem_"+desk_set[j]).className='';
				}
			}
		}
	}
}