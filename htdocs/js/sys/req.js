var req;

function parseURL(params,statusContent,url,afterfunction) 
{
	
	if(!url)
		url="/xmlstuff.php";

	if(statusContent!="")
		afterFunction=function(){processReqChange(statusContent,afterfunction)};
	else
		afterFunction=function(){};

	// branch for native XMLHttpRequest object
    if (window.XMLHttpRequest) 
	{
        req = new XMLHttpRequest();
        req.onreadystatechange = function(){afterFunction()};
		req.open("POST", url, true);
		req.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=utf-8'); 
		req.send(params);
	// branch for IE/Windows ActiveX version
    } 
	else 
		if (window.ActiveXObject) 
		{
			req = new ActiveXObject("Microsoft.XMLHTTP");
			if (req) 
			{
				req.onreadystatechange = function(){afterFunction()};
				req.open( "POST", url, false ); 
				req.setrequestheader('Content-Type','application/x-www-form-urlencoded; charset=utf-8'); 
				req.send(params);
			}
		}
}

function processReqChange(statusContent,afterfunction) 
{
    // only if req shows "complete"
    if (req.readyState == 4) 
	{
        // only if "OK"
        if (req.status == 200) 
		{
			var xmldoc = req.responseXML;

			if(get_element(statusContent))
			{
				afterfunction();
				setMsg(statusContent);
			}
				
        } 
		else 
		{
            alert("There was a problem retrieving the XML data:\n" + req.statusText);
        }
    }
}

function setMsg(contentId,el)
{
	if(!el)
		el="";

	if(el=="close")
	{
		get_element(contentId).innerHTML="";
		get_element(contentId).style.display="none";
	}
	else
	{
		get_element(contentId).innerHTML="<p style='color:green'><b>Dati veiksmīgi saglabāti!</b></p>";
		var t=setTimeout("setMsg('"+contentId+"','close')",1000);
	}
}

function doUpdate(data) 
{
	if ( data != null ) 
	{
		document.getElementById("body_div").innerHTML = data;
	}
}

function getXMLdata(url)
{
	if (document.implementation && document.implementation.createDocument)
	{
		xmlDoc = document.implementation.createDocument("", "", null);
		xmlDoc.onload = createTable;
	}
	else if (window.ActiveXObject)
	{
		xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.onreadystatechange = function () {
			if (xmlDoc.readyState == 4) createTable()
		};
 	}
	else
	{
		alert('Your browser can\'t handle this script');
		return;
	}
	xmlDoc.load(url);
}
