/* Internal Quatation items  */
function add_deal_item(name, price, currency_id, qty, vat, item_id, total, vat_total, item_total,
                        to_client_item  , print
)
{
	if(!name)			name = "";
	if(!price)			price = "0.00";
	if(!currency_id)	currency_id = 4;
	if(!qty)			qty = "1";
	if(!vat)			vat = 1;
	if(!item_id)		item_id = "";
	if(!total)			total = "0.00";
	if(!vat_total)		vat_total = "0.00";
	if(!item_total)		item_total = "0.00";
	if(!to_client_item)	to_client_item = "254";
    if(!print)    print = "0";

	var el;
	var row=insert_row("tbl_q_items",-1);
	var nr=row.id + 99;
	var nRowNr=row.id - 1;

	if(nr % 2==0) row.className="even";

	cell = insert_cell(row);
	cell.className = 'center';
	cell.innerHTML = nRowNr+'. ';

    cell = insert_cell(row);
    cell.className = 'center';
    if (print ==1) cell.innerHTML = '<img src="/images/active.gif">';

	cell=insert_cell(row);
    el=insert_form_element("select","to_client_item["+nr+"]",to_client_item,cell,"to_client_item_list","select-mini");

	cell=insert_cell(row);
	cell.innerHTML = "<textarea name='item_name["+nr+"]' cols='30' rows='1'>"+name+"</textarea>";

	cell=insert_cell(row, 'right');
	el=insert_form_element("text","item_price["+nr+"]",price,cell,"","input-n");

	cell=insert_cell(row);
	el=insert_form_element("select","item_currency_id["+nr+"]",currency_id,cell,"currency_list","mini2");

	cell=insert_cell(row);
	el=insert_form_element("text","item_qty["+nr+"]",qty,cell,"","input-n");
	el.style.width="30px";

	cell = insert_cell(row, 'right');
	//cell.className = 'right';
	cell.innerHTML = total;




	cell = insert_cell(row, 'right');
	cell.innerHTML = "<strong>"+ item_total +"</strong>";

    cell=insert_cell(row);
    el=insert_form_element("select","item_vat["+nr+"]",vat,cell,"vat_list","select-mini2");
    
    if (vat =="") {
        
        el.getElementsByTagName('option')[2].selected = 'selected';
    }
 
    if (vat == 6)
    {
        cell = insert_cell(row, 'center');
        cell.innerHTML = '-';
    }
    else
    {
          cell = insert_cell(row, 'right');
        cell.innerHTML = vat_total;
    }

	cell=insert_cell(row);
	if (item_id)
	{
		el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
		el.onclick= function(is_e)
			{ if(confirm('Vai tiešām vēlaties dzēst ierakstu?'))
				{set_value('del_item_id',item_id);this.form.submit();};
			};

	}

	insert_form_element("hidden","item_id["+nr+"]",item_id,cell,"","");
}

/* Quatation items for clients */
function add_dqci_item(item_id,print,position, item, total, vat, delta, total_client,vat_id_client,vat_value_client,vat_client)

{
	if(!item)			item = "";
	if(!delta)			delta = "0.00";
	if(!total_client)	total_client = "0.00";
	if(!vat_id_client)	vat_id_client = "0";
	if(!vat_client)		vat_client = "";
    if(!print)    print = "0";

	var el;
	var row=insert_row("table_dqci",0);
	var nr=row.id;
	var nRowNr=row.id - 1;

	if(nr % 2==0) row.className="even";

    var bIsCommon = (position == 254);

    /* POSITION */
	cell = insert_cell(row);
	cell.className = 'center';
    if (bIsCommon)
	    cell.innerHTML = '-';
    else
	    cell.innerHTML = position+'. ';


    /* PRINT */
    cell = insert_cell(row);
    cell.className = 'center';
    if (print ==1) cell.innerHTML = '<img src="/images/active.gif">';

    /* ITEM */
	cell=insert_cell(row);
    if (bIsCommon)
	    cell.innerHTML = item;
    else
	    cell.innerHTML = "<textarea name='DQCI_ITEM["+nr+"]' cols='30' rows='1'>"+item+"</textarea>";

    /* TOTAL */
	cell = insert_cell(row, 'right');
	cell.innerHTML = total;

    /* VAT */
	cell = insert_cell(row, 'right');
	cell.innerHTML = vat;

    /* DELTA */
	cell = insert_cell(row, 'right');
	cell.innerHTML = delta;

    /* TOTAL CLIENT */
	cell=insert_cell(row, 'right');
    if (bIsCommon)
        cell.innerHTML = total_client;
    else
	    el=insert_form_element("text","DQCI_TOTAL_CLIENT["+nr+"]",total_client,cell,"","input-n");

    /* VAT% */
    if (bIsCommon)
    {
        cell = insert_cell(row, 'right');
        cell.innerHTML = vat_value_client;
    }
    else
    {
        cell=insert_cell(row);
        el=insert_form_element("select","DQCI_VAT_ID_CLIENT["+nr+"]",vat_id_client,cell,"vat_list","select-mini2");
    }

    /* VAT VALUE */
    if (vat_id_client == 6)
    {
        cell = insert_cell(row, 'center');
        cell.innerHTML = '-';
    }
    else
    {
        cell = insert_cell(row, 'right');
        cell.innerHTML = vat_client;
    }

    /* HIDDENS */
    if (!bIsCommon)
    {
	    insert_form_element("hidden","DQCI_ID["+nr+"]",item_id,cell,"","");
	    insert_form_element("hidden","DQCI_POSTION["+nr+"]",position,cell,"","");
    }
}

function add_order_item(
                    name, price,
                    currency_id, qty,
                    vat, item_id,
                    total, vat_total,
                    item_total, to_client_item,
                    client_item, invoice_id,
                    invoice_number
)
{
	if(!name)			name = "";
	if(!price)			price = "0.00";
	if(!currency_id)	currency_id = 0;
	if(!qty)			qty = "1";
	if(!vat)			vat = "";
	if(!item_id)		item_id = "";
	if(!total)			total = "0.00";
	if(!vat_total)		vat_total = "0.00";
	if(!item_total)		item_total = "0.00";
	if(!to_client_item)	to_client_item = "0";
        if(!invoice_number)	invoice_number = "";
        if(!invoice_id)	invoice_id = "";
    if(!client_item)    client_item = "0";    
    if(!invoice_id)    invoice_id = "";
    if(!client_item)    client_item = "0";
     if(!print)    print = "0";                             

	var el;
	var row=insert_row("tbl_payment",-2);
	var nr=parseInt(row.id) + 99;
	var nRowNr=row.id - 1;

	if(nr % 2==0) row.className="even";

	cell = insert_cell(row,'center');
	cell.innerHTML = nRowNr+'. ';
    
        cell = insert_cell(row,'center');
    
        if (print ==1){
            cell.innerHTML = '<img src="/images/active.gif">';
        }

	cell=insert_cell(row);
        if (item_id){
            el=insert_form_element("checkbox","to_client_item2["+nr+"]",to_client_item,cell,"");
        }
        
        
	//el=insert_form_element("select","to_client_item["+nr+"]",to_client_item,cell,"to_client_item_list","select-mini");

	//cell=insert_cell(row,'center');
        //if(invoice_id){
        //    cell.innerHTML = '<a href="/?mod=164&id=' + invoice_id + '" class="more">' + invoice_number + '</a>';
        //}else{
        //    cell.innerHTML = '-';
        //}

        cell=insert_cell(row,'center');
        cell.innerHTML = ' - ';

        cell=insert_cell(row,'center');
        cell.innerHTML = ' - ';

        cell=insert_cell(row,'center');
        cell.innerHTML = ' - ';

        cell=insert_cell(row);
	cell.innerHTML = "<textarea name='item_name["+nr+"]' class='textarea-2'>"+name+"</textarea>";

	cell=insert_cell(row, 'right');
	el=insert_form_element("text","item_price["+nr+"]",price,cell,"","input-n");

	cell=insert_cell(row);
	el=insert_form_element("select","item_currency_id["+nr+"]",currency_id,cell,"currency_list");

	cell=insert_cell(row);
	el=insert_form_element("text","item_qty["+nr+"]",qty,cell,"","input-n3");
	//el.style.width="30px";

	cell = insert_cell(row, 'right');
	//cell.className = 'right';
	cell.innerHTML = total + '<input type="hidden" name="item_total['+nr+']" value="'+total+'" />';

	


	cell = insert_cell(row, 'right');
	//cell.className = 'right';
	cell.innerHTML = "<strong>"+ item_total +"</strong>";
    
    cell=insert_cell(row);
    el=insert_form_element("select","item_vat["+nr+"]",vat,cell,"vat_list","select-mini");

    if (vat == 6){
        /**
         * neapliekas ar PVN
         */
        cell = insert_cell(row, 'center');
        cell.innerHTML = '-';
    }else{
        /**
         * apliekas ar PVN
         */
        cell = insert_cell(row, 'right');
        cell.innerHTML = vat_total;
    }

    /**
     * poga deleted un hiddeni
     */
    cell=insert_cell(row);
    if (item_id){
            el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
            el.onclick= function(is_e)
                    { if(confirm('Vai tiešām vēlaties dzēst ierakstu?'))
                            {set_value('del_item_id',item_id);this.form.submit();};
                    };

    }else{
        /**
         * poga deleted
         */
        el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
        el.onclick= function(is_e){
            if(confirm('Vai tiešām vēlaties dzēst ierakstu?')){
                $(this).parent().parent().remove();
            }
        }
    }

    insert_form_element("hidden","item_id["+nr+"]",item_id,cell,"","");
    insert_form_element("hidden","client_item["+nr+"]",client_item,cell,"","");    
    insert_form_element("hidden","to_client_item["+nr+"]",nr,cell,"","");    
}

/**
 * Order items for client add new row
 */
function add_for_client_item(name, price, currency_id, qty, vat, to_client_item) {
    
    if(!name)			name = "";
    if(!price)			price = "0.00";
    if(!currency_id)	currency_id = 0;
    if(!qty)			qty = "1";
    if(!vat)			vat = "";
    if(!to_client_item)	to_client_item = "254";

    var table_id = "tbl_for_client";
    var el;

    table = get_element(table_id);
    var offset = -2;

    var row=insert_row(table_id,offset);
    var nRowNr=parseInt(row.id) - 1;
    var nr=200 + nRowNr;

    if(nr % 2==0) row.className="even";

    cell = insert_cell(row,'center');
    cell.innerHTML = nRowNr;

    cell = insert_cell(row,'center');
    cell.innerHTML = '-';

    cell = insert_cell(row,'center');
    cell.innerHTML = '-';
    cell = insert_cell(row,'center');
    cell.innerHTML = '-';
    cell = insert_cell(row,'center');
    cell.innerHTML = '-';

    cell=insert_cell(row);
    cell.innerHTML = "<textarea name='item_name["+nr+"]' class='textarea-2'>"+name+"</textarea>";

    cell=insert_cell(row, 'right');
    el=insert_form_element("text","item_price["+nr+"]",price,cell,"","input-n");

    cell=insert_cell(row, 'right');
    el=insert_form_element("select","item_currency_id["+nr+"]",currency_id,cell,"currency_list");

    cell=insert_cell(row, 'right');
    el=insert_form_element("text","item_qty["+nr+"]",qty,cell,"","input-n3");

    cell = insert_cell(row, 'right');
    //cell.innerHTML = '-';
    cell.innerHTML = qty*price;
    

    cell = insert_cell(row, 'right');
    cell.innerHTML = '-';

    cell=insert_cell(row, 'right');
    el=insert_form_element("select","item_vat["+nr+"]",vat,cell,"vat_list","select-mini");
    
    cell = insert_cell(row, 'right');
    cell.innerHTML = '-';
    el=insert_form_element("hidden","item_total["+nr+"]",qty*price,cell,"");

    /**
     * poga deleted
     */
    cell=insert_cell(row, 'right');
    el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
    el.onclick= function(is_e){
        if(confirm('Vai tiešām vēlaties dzēst ierakstu?')){
            $(this).parent().parent().remove();
        }
    }

    insert_form_element("hidden","item_type["+nr+"]",'C',cell,"","");
    insert_form_element("hidden","client_item["+nr+"]",nRowNr,cell,"","");
    insert_form_element("hidden","to_client_item["+nr+"]","0",cell,"","");
}

function add_brocker(d_id, user_id, percent, koef)
{
	if (!d_id) d_id='';
	if (!user_id) user_id='';
	if (!percent) percent='0';
	if (!koef) koef='0';

	var row=insert_row("tbl_brocker",1);
	var nr=row.id;

	if(nr % 2==0) row.className="even";

	cell=insert_cell(row);
	insert_form_element("select","user_id["+nr+"]",user_id,cell,"user_list","select-2");

	cell=insert_cell(row);
	el=insert_form_element("text","percent["+nr+"]",percent,cell,"","input-n");
//	el.style.width = "50px";

//	cell = insert_cell(row, 'right');
//	cell.innerHTML = koef;

	cell=insert_cell(row);
	insert_form_element("hidden","brocker_id["+nr+"]",d_id,cell,"","");
	if (d_id)
	{
		el=insert_form_element("button","_"+nr+"]",'',cell,"","button-delete");
		if( el.attachEvent )
			el.onclick= function(){set_value('del_brocker_id',d_id);this.form.submit();};
		else
			el.onclick= function(e){set_value('del_brocker_id',d_id);this.form.submit();};
	}
}


function switch_deal(type)
{
	var displayName = 'none';
	var isIE = get_element('bank_row').attachEvent; // I.Exporer
	if(isIE) displayRow = 'block';
	else	 displayRow = 'table-row';

	// ----
	if(type == '1' || type == '3') 	displayName = 'none'; 
	else displayName = displayRow;

	typeOne = new  Array('bank_row', 'bank_2_row', 'time_row')
	for(i=0; typeOne[i]; i++) 
		get_element(typeOne[i]).style.display = displayName;

	// apdrosinashana
	insure = new  Array('insure_row', 'due_row', 'id_row', 'polise_row')
	if(type == '3')
	{
		displayName = displayRow;
		get_element('cnt_th').innerHTML= 'Parakstītā prēmija';
		get_element('send_date_th').innerHTML = 'Izrakstīšanas datums *';
		get_element('accept_date_th').innerHTML = 'Sākuma datums *';
		get_element('notes_th').innerHTML = 'Rēķina Nr';
	}
	else
	{
		displayName = 'none';
		get_element('cnt_th').innerHTML = 'Summa / Skaits';
		get_element('send_date_th').innerHTML = 'Iesniegšanas datums *';
		get_element('accept_date_th').innerHTML = 'Akceptēšanas datums';
		get_element('notes_th').innerHTML = 'Piezīmes';
	}
	for(i=0; insure[i]; i++)
		get_element(insure[i]).style.display = displayName;
}

function add_shipper(id,details)
{
	if(!id) id = "";
	if(!details) details = "";

	var row=insert_row("tbl_order",0);
	var nr=row.id;

	var cell = insert_cell(row);
	cell = insert_cell(row);	
	//insert_form_element("textarea","shipping_details["+nr+"]",details, cell, '',"textarea-3");
	cell.innerHTML="<textarea class='textarea-3' name='shipping_details["+nr+"]'>"+details+"</textarea>";
}



/// ========== xml ===========

function import_commission()
{
	var commission=document.getElementById('commission_percent');
	var commission_show=document.getElementById('commission_show');
	commission.value = 0;
	commission_show.value = 0;

	if (document.implementation && document.implementation.createDocument)
	{
		xmlDoc = document.implementation.createDocument('','', null);
		xmlDoc.onload = function(){put_commission_value()};
	}
	else if (window.ActiveXObject)
	{
		xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.onreadystatechange = function () 
		{
			if (xmlDoc.readyState == 4)
			{ 		
				put_commission_value();				
			}
		};
 	}
	else
	{
		alert('Jusu parlukprogramma neatbalsta XML');
		return;
	}
	
	var reg=document.getElementById('product_id');
	var product=reg.options[reg.selectedIndex].value;
	var reg=document.getElementById('insure_id');
	var insure=reg.options[reg.selectedIndex].value;
	if(!product || !insure) return false;

	xmlDoc.load("xml_data.php?product_id="+product+"&client_id="+insure);
}

function put_commission_value()
{
	var commission=document.getElementById('commission_percent');
	var commission_show=document.getElementById('commission_show');
	var x = xmlDoc.getElementsByTagName('root');
	
	for (i=0; i<x[0].childNodes.length; i++)
		if(x[0].childNodes[i].nodeType!=3)
		{
			commission.value = x[0].childNodes[i].firstChild.nodeValue;
			commission_show.value = commission.value;
		}
}

function add_cmr_item(item_id, cmr_nr,  sh_in,truck,trailer,cases,bruto)
{
    if(!item_id)        item_id = "";
    if(!cmr_nr)            cmr_nr = "";
    if(!sh_in)            sh_in = "";
    if(!truck)            truck = "";
    if(!trailer)        trailer = "";
    if(!cases)            cases = "0";
    if(!bruto)            bruto = 0;

    var el;
    var row=insert_row("tbl_dcmr",1);
    var nr=row.id - 1;

    if(nr % 2==0) row.className="even";

    cell = insert_cell(row);
    cell.className = 'center';

    cell.innerHTML = nr;

       cell=insert_cell(row, 'right');
    el=insert_form_element("text","DCMR_NUMBER["+nr+"]",cmr_nr,cell,"","input-n");


       cell=insert_cell(row, 'right');
    el=insert_form_element("text","DCMR_SH_IN_NR["+nr+"]",sh_in,cell,"","input-n");

     cell=insert_cell(row, 'right');
    el=insert_form_element("text","DCMR_CAR_NUMBER["+nr+"]",truck,cell,"","input-n");

     cell=insert_cell(row, 'right');
    el=insert_form_element("text","DCMR_TRAILER_NUMBER["+nr+"]",trailer,cell,"","input-n");

    cell=insert_cell(row, 'right');
    el=insert_form_element("text","DCMR_CASES["+nr+"]",cases,cell,"","input-n");

    cell=insert_cell(row, 'right');
    el=insert_form_element("text","DCMR_BRUTO["+nr+"]",bruto,cell,"","input-n");
    cell=insert_cell(row);
    if (item_id)
    {
        el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
        el.onclick= function(is_e)
            { if(confirm('Are you sure?'))
                {set_value('del_crm_item_id',item_id);this.form.submit();};
            };
           insert_form_element("hidden","DCMR_ID["+nr+"]",item_id,cell,"","");

    }

}

function SetAllQuantities(FormName, FieldName)
{
    alert(FieldName);
    if(!document.forms[FormName])
        return;
    var objQuantities = document.forms[FormName].elements[FieldName];
    if(!objQuantities)
        return;
    var countQuantities = objQuantities.length;

    // set the  value for all fields
    QtyValue=document.forms[FormName].elements['quantity'];
    alert(QtyValue+'  '+countQuantities);
    for(var i = 0; i < countQuantities; i++)
        objQuantities[i].value = QtyValue;


}
//var enable_for_client_grouping = true;
$(function(){

    //check if for client data already exists
    //if($('#tbl_for_client').find('.button-delete')) {
    //    enable_for_client_grouping = false
    //}

    /**
     * for internal & client itemiem sakarto currency listboxus
     */
    $('#currency_list').find('option').clone().appendTo($('#tbl_payment').find('[id^="item_currency_id"]'));
    $('#tbl_payment').find('[id^="item_currency_id"]').each(function() {
        $(this).val($(this).next().val());
    })

    $('#currency_list').find('option').clone().appendTo($('#tbl_for_client').find('[id^="item_currency_id"]'));
    $('#tbl_for_client').find('[id^="item_currency_id"]').each(function() {
        $(this).val($(this).next().val());
    })

    /**
     * for internal & client itemiem sakarto VAT listboxus
     */
    $('#vat_list').find('option').clone().appendTo($('#tbl_payment').find('[id^="item_vat"]'));
    $('#tbl_payment').find('[id^="item_vat"]').each(function() {
        $(this).val($(this).next().val());
    })

    $('#vat_list').find('option').clone().appendTo($('#tbl_for_client').find('[id^="item_vat"]'));
    $('#tbl_for_client').find('[id^="item_vat"]').each(function() {
        $(this).val($(this).next().val());
    })

//el=insert_form_element("select","to_client_item["+nr+"]",to_client_item,cell,"to_client_item_list","select-mini");
    //$('#to_client_item_list').find('option').clone().appendTo($('#tbl_payment').find('[id^="to_client_item"]'));
    //$('#tbl_payment').find('[id^="to_client_item"]').each(function() {
    //    $(this).val($(this).next().val());
    //})

//kopēšana caur checkboxiem:
// saķeksē itemus kurus vajag apvienot un, nospiežot Copy group pogu, 
// apvienotais items tiek ielikts for client sadaļā
$("#copy_to_for_client_wrapper").hide();
var checkbox_item_arr = Array();
var n = 0;
$("#tbl_payment").on("change", "input[id^='to_client_item2']", function(){

  if($(this).is(':checked')) {
    console.log('checked!');
    //add item if only it does not exist in queue
    var item_position = $.inArray($(this).val(), checkbox_item_arr);
    if(item_position == -1) {
        checkbox_item_arr[n] = $(this).data('row');
    }
    n++;
  } else {
      console.log('unchecked!');
      //remove array element if it exists in queue
      var item_position = $.inArray($(this).val(), checkbox_item_arr);
      if(item_position != -1) {
          checkbox_item_arr.splice(item_position, 1);
      }
  }
  console.log('checkbox_item_arr.length: '+checkbox_item_arr.length);
  if(checkbox_item_arr.length == 0) {
      console.log($("input[id=copy_to_for_client]"));
      $("#copy_to_for_client_wrapper").hide();
  } else {
      $("#copy_to_for_client_wrapper").show();
  }
  $('.join_video_list').html('');
  $.each(checkbox_item_arr, function(key, value) {
     if(value !== undefined) {
         $('.join_video_list').append('<li>'+value+'</li>');
     }

  });
  console.log(checkbox_item_arr);
});


$("input[id='copy_to_for_client']").click(function() {
  
  //te notiek grupas kopēšana uz For client sadaļu
  var name = $('textarea[name=item_name\\['+checkbox_item_arr[0]+'\\]]').val();
  var qty = $('#item_qty\\['+checkbox_item_arr[0]+'\\]').val();
  var vat = $('#item_vat\\['+checkbox_item_arr[0]+'\\]').val();
  console.log('vat: '+vat);
  var currency_id = $('#item_currency_id\\['+checkbox_item_arr[0]+'\\]').val();
  var price = 0;
  
  $.each(checkbox_item_arr, function(key, value) {
      
      console.log('key: ' + key + ' val: ' + value);
      var item_price = $('#item_price\\['+value+'\\]').val();
      price = price + parseInt(item_price);
      
      //check if all currencies are equal
      var item_currency_id = $('#item_currency_id\\['+value+'\\]').val();
      if (currency_id != item_currency_id) {
          currency_id = 0; //different currencies so don't select any currency
      }
  });
  add_for_client_item(name, price, currency_id, qty, vat);
  //jāaprēķina kopējā grupas summa (Price/Sum)
  
  //te beidzas grupas kopēšana uz For client sadaļu
  
    $("#copy_to_for_client_wrapper").hide();
    //reset state
    
    $("[id^=to_client_item2]").attr("checked", false);
    checkbox_item_arr = Array();
    n = 0;
    return false;
});
    
    
    
    
    
    
})