var aMsg = new Array();

aMsg[001] = " un "; //" and "; 
aMsg[100] = " ir obligāts.<br/>";//" must not be empty.<br/>"; 
aMsg[110] = " jābūt skaitlim.<br/>"; //" must be numeric.<br/>";
aMsg[200] = "Nekorekts datums.<br/>"; //"Not a valid date.<br/>";
aMsg[210] = "Nekorekta diena.<br/>"; // "Not a valid day.<br/>"; 
aMsg[220] = "Nekorekta mēnesis.<br/>"; // "Not a valid month.<br/>";
aMsg[230] = "Datumam jābūt formātā 'DD.MM.YYYY'.<br/>"; // "The date format should be 'DD.MM.YYYY'.<br/>"; 
aMsg[240] = "Gadam jābūt starp "; // "Year must be between "; 

var errorMessages = '';

function isNumber(s)
{
	s += '';
	if (s == '' ) return true;
	if (s == ".") return false;
	return (s.search(/^\d*\.?\d*$/) == -1)?false:true;
}


function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){

	error = '';
	if (dtStr == '') return '';
	var dtCh= ".";
	var minYear=1900;
	var maxYear=2200;
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strDay=dtStr.substring(0,pos1)
	var strMonth=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		//alert("The date format should be : DD.MM.YYYY")
		//errorMessages += "The date format should be : DD.MM.YYYY\n";
		error = aMsg[230];
		return error;
	}
	if (strMonth.length<1 || month<1 || month>12){
		error = aMsg[220];
		return error;
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		error = aMsg[210];
		return error;
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		error = aMsg[240] + minYear + aMsg[001] + maxYear +"<br/>";
		return error;
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		error = aMsg[200];
		return error;
	}

	return '';
}

function trimAll(sString)
{
	while (sString.substring(0,1) == ' ')
		sString = sString.substring(1, sString.length);
	
	while (sString.substring(sString.length-1, sString.length) == ' ')
		sString = sString.substring(0,sString.length-1);
	
	return sString;
}

function getTitle(id){
	var title = '';
	if (id)	{
		var el = get_element(id);
		if (el){
			title = el.getAttribute('title');
			if (!title || title == '') title = id;
		}
	}
	return "<strong>"+title+":&nbsp;</strong>";
}

function validateField(id,type)
{
	var err = false;
	var el = document.getElementById(id);
	if (el)
	{
		var fieldval = trimAll(el.value);
		var tmp_error = '';
		switch (type)
		{
			case 'date': 
				tmp_error = isDate(fieldval);
				if (tmp_error != '') 
				{
					err = true;
					errorMessages += getTitle(id) + tmp_error;
				}
			break;
			case 'number':
				if (!isNumber(fieldval)){
					err = true;
					errorMessages += getTitle(id) + aMsg[110];
				}
			break;
			case 'notnull':
				if (fieldval == ''){
					err = true;
					errorMessages += getTitle(id) + aMsg[100];
				}
			break; 
			default:
		}		
		if (err) el.style.background = "#EAA";
	}
	return !err;
}

function put_error_icon(html)
{
	if (!html) return false;
	var td = get_element("error_td");
	if (td)
	{
		var im = get_element("error_icon");
		if (im) td.removeChild(im);
		im = document.createElement('img');
		im.src = "images/error.gif";
		im.style.cursor = "pointer";
		im.setAttribute("id","error_icon");
		
		if( im.attachEvent )
		{
			im.onmouseover = function(){popUpMsq(html)};
			im.onmouseout = function(){nd()};
		}
		else
		{
			im.onmouseover = function(e){popUpMsq(html)};
			im.onmouseout = function(e){nd()};
		}
		td.appendChild(im);
	}
	return false;
}

function unmark_field(id)
{
	if (id && id != '')
	{
		var el = get_element(id)
		if (el) el.style.background = '';
	}
}