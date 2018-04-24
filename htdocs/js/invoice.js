function add_invoice_item(
    name, price, currency_id, qty, vat, item_id,
    total, vat_total, item_total, base_total,
    order_id,
    order_number,order_item_id,item_order_id,
    dim_date_from,dim_date_to,
    dim_l1_id, dim_l2_id, dim_l3_id,
    dim_l1_code, dim_l2_code, dim_l3_code
    )
{
    if(!name)			name = "";
    if(!price)			price = "0.00";
    if(!currency_id)	currency_id = 0;
    if(!qty)			qty = "";
    if(!vat)			vat = "";
    if(!item_id)		item_id = "";
    if(!total)			total = "0.00";
    if(!vat_total)		vat_total = "0.00";
    if(!item_total)		item_total = "0.00";
    if(!base_total)		base_total = "0.00";
    if(!order_id)		order_id = "";
    if(!item_order_id)	item_order_id = "";
    if(!order_item_id)	order_item_id = "";
    if(!order_number)	order_number = "";

    if(!dim_date_from) dim_date_from = "";//document.getElementById('service_date').value;
    if(!dim_date_to) dim_date_to = '';

    if(!dim_l1_id) dim_l1_id = "";
    if(!dim_l2_id) dim_l2_id = "";
    if(!dim_l3_id) dim_l3_id = "";

    if(!dim_l1_code) dim_l1_code = "";
    if(!dim_l2_code) dim_l2_code = "";
    if(!dim_l3_code) dim_l3_code = "";

    var el;
    var row=insert_row("tbl_invoice",-1);
    var nr=row.id - 1;

    if(nr % 2==0) row.className="even"; else row.className="odd";

    cell = insert_cell(row,'center');
    cell.innerHTML = nr;

    cell = insert_cell(row,'center nowrap');
    var oi = '';
    if(item_order_id > 0)
        oi = item_order_id;
    if(order_id > 0)
        oi = order_id;

    /**
    *Orders
    */
    if(oi > 0){
        cell.innerHTML = "<a href='/?mod=640&amp;id="+oi+"' class='more'>"+order_number+"</a>" +
        "<input type='button' class='button-change'> ";
        insert_form_element("text","order_item_number["+nr+"]",'',cell,"","input-n");
        insert_form_element("hidden","order_item_id["+nr+"]",order_item_id,cell,"","");
        
    }else{
        //el=insert_form_element("select","order_item_id["+nr+"]",order_item_id,cell,"order_item_list","select-2");
        /**
         *autocomplete input & hidden field
         */
        insert_form_element("text","order_item_number["+nr+"]",'',cell,"","input-n");
        insert_form_element("hidden","order_item_id["+nr+"]",order_item_id,cell,"","");
    }
        
    cell=insert_cell(row);
    cell.innerHTML = "<textarea name='item_name["+nr+"]' id='item_name["+nr+"]' cols='30' rows='1'>"+name+"</textarea>";

    cell=insert_cell(row, 'right');
    el=insert_form_element("text","item_price["+nr+"]",price,cell,"","input-n");

    cell=insert_cell(row);
    el=insert_form_element("select","item_currency_id["+nr+"]",currency_id,cell,"currency_list","select-mini2");

    cell=insert_cell(row);
    el=insert_form_element("text","item_qty["+nr+"]",qty,cell,"","input-n");
    el.style.width="30px";

    cell = insert_cell(row, 'right');
    cell.innerHTML = total;
    insert_form_element("hidden","item_amt["+nr+"]",total,cell,"","");

    cell=insert_cell(row);
    el=insert_form_element("select","item_vat["+nr+"]",vat,cell,"vat_list","select-mini");

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

    cell = insert_cell(row, 'right');
    cell.innerHTML = "<strong class=\"total\">"+ item_total +"</strong>"; //class="total" used in save_dimension();
    insert_form_element("hidden","item_id["+nr+"]",item_id,cell,"","");

    cell=insert_cell(row);
    /**
     * del poga
     * ja ir ieraksts DB, tad dzēš ar submit, bet ja nav tad izdzēš tabulas rindu
     */
    el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
    el.onclick= function(is_e){
        if(confirm('Are you sure?')){
            if (item_id){
                set_value('del_item_id',item_id);
                this.form.submit();
            }else{
                delete_field(this);
            }
        };
    };


    cell=insert_cell(row, 'hidden_dimension num');
    cell.innerHTML = base_total;

    cell=insert_cell(row, 'hidden_dimension cal');
    cell.innerHTML = dim_date_from;

    cell=insert_cell(row, 'hidden_dimension nowrap cal');
    cell.innerHTML = dim_date_to;

    
    cell=insert_cell(row, 'hidden_dimension');
    if(dim_l2_id){
        cell.innerHTML = dim_l1_code;
        insert_form_element("hidden","",dim_l1_id,cell,"","");
    }

    // listboxus nevar saladet, jo vajadzigs ajax, salik hiddenus ar vertibaam
    cell=insert_cell(row, 'hidden_dimension');
    if(dim_l2_code){
        cell.innerHTML = dim_l2_code;
        insert_form_element("hidden","",dim_l2_id,cell,"","");
    }

    // listboxus nevar saladet, jo vajadzigs ajax, salik hiddenus ar vertibaam
    cell=insert_cell(row, 'hidden_dimension');
    if(dim_l3_code){
        cell.innerHTML = dim_l3_code;
        insert_form_element("hidden","",dim_l3_id,cell,"","");
    }

    cell=insert_cell(row, 'hidden_dimension nowrap');
    if(dim_l1_code){
        el=insert_form_element("input","","",cell,"","button-edit2");
    } else {
        if(!item_id) {
            $('.hidden_dimension').hide();
        } else {
            el=insert_form_element("input","","",cell,"","button-add");
        }
    }
    

    
}

function add_cmr_item(item_id, cmr_nr,  sh_in,truck,trailer,cases,bruto)
{
    if(!item_id)		item_id = "";
    if(!cmr_nr)			cmr_nr = "";
    if(!sh_in)			sh_in = "";
    if(!truck)			truck = "";
    if(!trailer)		trailer = "";
    if(!cases)			cases = "0";
    if(!bruto)	        bruto = 0;

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
        {
            if(confirm('Are you sure?'))

            {
                set_value('del_crm_item_id',item_id);
                this.form.submit();
            };
        };
        insert_form_element("hidden","DCMR_ID["+nr+"]",item_id,cell,"","");

    }

}

function check_and_save()
{
    if (get_element('state').checked && !confirm('Vai Jus tiesam velaties anulet rekinu?'))
        return false;
  
    set_value('action','save');
    document.forms.DataForm.submit();
    return false;
}

function disable_reminder_button()
{
    var btn=get_element('btn_reminder');
    if (btn)
    {
        btn.disabled=true;
        btn.style.color='#999';
    }
    return false;
}

function delete_payer()
{
    set_value('payer_id','');
    if (get_element('id').value != '')
        document.forms.DataForm.submit();
    else
    {
        get_element('p_name').value = get_element('p_reg_no').value = '';
        get_element('btn_payer_del').style.display='none';
    }
    return false;
}

function switch_invoice_type(invoice_type, value)
{
    if(!value) value = '';

    if(invoice_type == 1)
        get_element('td_number').innerHTML="<input type='text' class='input-2' name='manual_number' id='manual_number' value='"+value+"' />";
    else
        get_element('td_number').innerHTML = value;
}



$(function(){

    //labojamas DIM rindas sakumvertibas
    var sDimDateFrom;
    var sDimDateTo;
    var sDimLevel1Id;
    var sDimLevel1Value;
    var sDimLevel2Id;
    var sDimLevel2Value;
    var sDimLevel3Id;
    var sDimLevel3Value;

    //noklusejuma stavokli
    $('.hidden_dimension').hide();
    $('#hide_dimensions').parent().hide();
    //$('.select_disabled').attr('disabled', 'disabled');

    /**
     * aizpilda Accepter selectbokšus,jo vlib iekš ciklus neļauj likt citus ciklus
     */
    $('select[name="accept_to_user_id"]').each(function(i){
        $(this).replaceWith($('#temp_user_list').clone().attr({invoice: $(this).data('invoice'), id: 'user_list'+i, name: 'accept_to_user_id'}))
    })


    /**
     * piefikse datumu un user id pie akceptetaja izveles, ja dokuments jau saglabats
     */
    $("table").on('change', 'select[name="accept_to_user_id"]', function(event) {
        event.stopPropagation();
        if ($(this).val() == '0') {
            return false;
        }
        select_selement = $(this)
        /**
         * ajax pieprasījums, kur updeito accept_to_user_id un accept_to_user_date laukus
         */
        $.post('/?mod=904&print=ajax', //MODULE_INVOICE_AJAX
        {
            action : "accept_to" ,
            accept_to_user_id : select_selement.val(),
            invoice_id: select_selement.attr('invoice')
        },
        function(data) {
            data = $.parseJSON(data);
            if (data["error"]){
                $("#error_info").show();
                $("#error_info").html(data["error"]);
                return false;
            } else {
                select_selement.parent().append('<span style="color:green">Accepter saved!</span>')
            }
        });
    });

    $("table").on('change', 'input[name="assign_to_accountant"]', function(event) {
        event.stopPropagation();
        assign_checkbox_element = $(this)
        if(this.checked) {
            /**
             * ajax pieprasījums, kur updeito accept_date lauku
             */
            $.post('/?mod=904&print=ajax', //MODULE_INVOICE_AJAX
            {
                action : "assign_to_accountant" ,
                invoice_id: assign_checkbox_element.data('invoice')
            },
            function(data) {
                data = $.parseJSON(data);
                if (data["error"]){
                    $("#error_info").show();
                    $("#error_info").html(data["error"]);
                    return false;
                } else {
                    assign_checkbox_element.parent().append('<span style="color:green"> Assigned!</span>')
                }
            });
        }
    });

    $("input[name='accept_checkbox']").change(function(event) {
        event.stopPropagation();
        checkbox_element = $(this)
        if(this.checked) {
            /**
             * ajax pieprasījums, kur updeito accept_date lauku
             */
            $.post('/?mod=904&print=ajax', //MODULE_INVOICE_AJAX
            {
                action : "update_accept_date" ,
                invoice_id: checkbox_element.data('invoice')
            },
            function(data) {
                data = $.parseJSON(data);
                if (data["error"]){
                    $("#error_info").show();
                    $("#error_info").html(data["error"]);
                    return false;
                } else {
                    checkbox_element.parent().append('<span style="color:green">Accepted!</span>')
                    checkbox_element.prop('disabled', true)
                    if (typeof(checkbox_element.parent().parent().find('.assign_cell input[type="checkbox"]').val()) == 'undefined') {
                        checkbox_element.parent().parent().find('.assign_cell').html(
                        '<input type="checkbox" name="assign_to_accountant" data-invoice="'+
                        checkbox_element.data('invoice')+'" title="Assigned to accountant?"/>')
                    }
                }
            });
        }
    });

    //show dimension tabulas kolonas
    $('#show_dimensions').on('click',function() {
        $(this).parent().hide().next().show();
        $('.hidden_dimension').show();
        //$('td.hidden_dimension.nowrap').css('display', 'block');
        return false;
    });

    //hide dimension tabulas kolonas
    $('#hide_dimensions').on('click',function() {
        $(this).parent().hide().prev().show();
        $('.hidden_dimension').hide();
        return false;
    });

    // kalendara kontrole
    $('#tbl_invoice').on('click','input.button-cal',function() {
        var sCalndarInputId = $(this).prev().attr('id');
        showCalendar(sCalndarInputId);
    })

    //click edit dimension row
    $('#tbl_invoice').on('click','input.button-edit2,input.button-add',function() {

        // cell date from
        var elCell = $(this).parent().prev().prev().prev().prev().prev();
        var sValue = $(elCell).text();
        if(sValue.length == 0){
            sValue = $('#service_date').val();
        }
        sDimDateFrom = sValue;
        $(elCell).html(
            '<input type="text" name="dim_date_from" id="dim_date_from" value="'+ sValue +'" class="input-d"/>' +
            '<input type="button" class="button-cal" />');

        // cell date to
        elCell = $(elCell).next();
        var sValue = $(elCell).text();
        if(sValue.length == 0){
            sValue = $('#service_date').val();
        }
        sDimDateTo = sValue;
        $(elCell).html('<input type="text" name="dim_date_to" id="dim_date_to" value="'+ sValue +'" class="input-d"/>'+
            '<input type="button" class="button-cal" />');

        // cell level 1
        elCell = $(elCell).next();
        var nDim1Id = $(elCell).children('input').val();
        sDimLevel1Id = nDim1Id;
        sDimLevel1Value = $(elCell).text();
        $(elCell).html('<select name="dim_l1_id" />');
        var elSelect = $(elCell).children('select');
        $('#root_dimension_select_box').find('option').clone().appendTo($(elSelect));
        $(elSelect).val(nDim1Id);

        // cell level 2
        elCell = $(elCell).next();
        var nDim2Id = $(elCell).children('input').val();
        sDimLevel2Id = nDim2Id;
        sDimLevel2Value = $(elCell).text();

        $(elCell).html('<select name="dim_l2_id" />');
        var elSelect = $(elCell).children('select');
        set_selectbox_items(2, nDim1Id, elSelect,nDim2Id);

        // cell level 3
        elCell = $(elCell).next();
        var nDim3Id = $(elCell).children('input').val();
        sDimLevel3Id = nDim3Id;
        sDimLevel3Value = $(elCell).text();

        $(elCell).html('<select name="dim_l3_id" />');
        var elSelect = $(elCell).children('select');
        set_selectbox_items(3, nDim2Id, elSelect,nDim3Id);
        
        //add buttons: save & cancel
        elCell = $(elCell).next();
        $(elCell).html(
            '<input type="button" title="Save" class="button-save">' +
            '<input type="button" title="Cancel" class="button-cancel">'
            );

        return false;
    });

    //pie selectbox izmaiņas lejupielādēt nākošā selectboxa saturu
    $('#tbl_invoice').on('change','[name="dim_l1_id"],[name="dim_l2_id"]',function() {
        var nDimValue = $(this).val();
        var sThisName = $(this).attr('name');
        var elThisRow = $(this).parent().parent();
        var elL1Selectbox = $(elThisRow).find('[name="dim_l1_id"]');
        var elL2Selectbox = $(elThisRow).find('[name="dim_l2_id"]');
        var elL3Selectbox = $(elThisRow).find('[name="dim_l3_id"]');
        //console.log('changed. dim value: '+dimension_value);

        if(sThisName == 'dim_l1_id' && nDimValue != 0) {
            //iztiram ieprieksejos selectbox itemus
            elL2Selectbox.html('');
            elL3Selectbox.html('');
            set_selectbox_items(2, nDimValue, elL2Selectbox);
            elL2Selectbox.attr('disabled', false);
            elL3Selectbox.attr('disabled', true);
        }
        if(sThisName == 'dim_l2_id' && nDimValue != 0) {
            //iztiram ieprieksejos selectbox itemus
            elL3Selectbox.html('');
            set_selectbox_items(3, nDimValue, elL3Selectbox);
            elL3Selectbox.attr('disabled', false);
        }
    });

    // save or cancel edit dimension row

    $('#tbl_invoice').on('click','input.button-save,input.button-cancel',function() {
        //var sButtonVal = $(this).val();
        console.log('tbl_invoice click save/cancel');
        var elThisRow = $(this).parent().parent();
        var elDateFrom = $(elThisRow).find('[name=dim_date_from]');
        var elDateTo = $(elThisRow).find('[name=dim_date_to]');
        var elL1Selectbox = $(elThisRow).find('[name="dim_l1_id"]');
        var elL2Selectbox = $(elThisRow).find('[name="dim_l2_id"]');
        var elL3Selectbox = $(elThisRow).find('[name="dim_l3_id"]');

        if($(this).hasClass('button-save')){
            console.log('tbl_invoice click save');
            var item_id = $(elThisRow).find('[id^=item_id]').val();
            var date_from = $(elDateFrom).val();
            var date_to = $(elDateTo).val();
            var amt = $(elDateFrom).parent().prev().text();
            
            save_dimension(
                elL3Selectbox,
                'd_invoice_item',
                item_id,
                elL1Selectbox.val(),
                elL2Selectbox.val(),
                elL3Selectbox.val(),
                amt,
                date_from,
                date_to
                );

        }

        if($(this).hasClass('button-cancel')){
            $(elL1Selectbox).parent().html(sDimLevel1Value + '<input type="hidden" value="'+sDimLevel1Id+'" />');
            $(elL2Selectbox).parent().html(sDimLevel2Value + '<input type="hidden" value="'+sDimLevel2Id+'" />');
            $(elL3Selectbox).parent().html(sDimLevel3Value + '<input type="hidden" value="'+sDimLevel3Id+'" />');

            $(elDateFrom).parent().text(sDimDateFrom);
            $(elDateTo).parent().text(sDimDateTo);


            $(this).parent().html('<input type="button" class="button-edit2" />');

        }

    })

    function set_selectbox_items(nLevel, nParentLevelId, SelectboxSelector,nValue) {
        $.post('/?mod=270&print=ajax',
        {
            action: "get_selectbox_values",
            level: nLevel,
            parent_level_id: nParentLevelId

        },
        function(data) {

            data = $.parseJSON(data);
            /**
            * kljudu apstade
            */
            if (data["error"]){
                $("#error_info").show();
                $("#error_info").html(data["error"]);
                return false;
            }

            /**
           * aizpilda nākošā dimensijas līmeņa selectboxu
           */
            SelectboxSelector.append('<option value="0">-Izvēlies-</option>');
            $.each(data, function(key, val) {
                SelectboxSelector.append($("<option></option>")
                    .attr("value",val.data.id)
                    .text(val.data.title));
            });
            if(nValue){
                $(SelectboxSelector).val(nValue);
            }
        });
    }
    function save_dimension(ResultSelector, table_name, record_id, l1_id, l2_id, l3_id, amt, date_from, date_to) {
        
        if (!record_id) {
            $("#error_info").show();
            $("#error_info").html('can not add dimensions to unsaved item');
            alert('can not add dimensions to unsaved item');
            
            //save order_item
            //d_invoice_item(empty($v['id']) ? 'I' : 'U', $v, $aRes);
            
            return false;
        } else {
            console.log('record_id value ok');
        }
        
        $.post('/?mod=270&print=ajax',
        {
            action: "save_dim_data",
            table_name: table_name,
            record_id: record_id,
            l1_id: l1_id,
            l2_id: l2_id,
            l3_id: l3_id,
            amt: amt,
            date_from: date_from,
            date_to: date_to
        },
        function(data) {

            data = $.parseJSON(data);
            /**
            * kljudu apstade
            */
            if (data["error"]){
                $("#error_info").show();
                $("#error_info").html(data["error"]);
                alert(data["error"]);
                return false;

            }

            //edit rindu partaisa par texta rindu
            var elThisRow = $(ResultSelector).parent().parent();

            //dates
            var elDateFrom = $(elThisRow).find('[name=dim_date_from]');
            var elDateTo = $(elThisRow).find('[name=dim_date_to]');

            //listbox
            var elL1Selectbox = $(elThisRow).find('[name="dim_l1_id"]');
            var elL2Selectbox = $(elThisRow).find('[name="dim_l2_id"]');
            var elL3Selectbox = $(elThisRow).find('[name="dim_l3_id"]');

            //date from
            var date_from = $(elDateFrom).val();
            $(elDateFrom).parent().text(date_from);

            //date to
            var date_to = $(elDateTo).val();
            $(elDateTo).parent().text(date_to);

            //dim1
            var sText = $(elL1Selectbox).find("option:selected").text();
            var nValue = $(elL1Selectbox).val();
            $(elL1Selectbox).parent().html(sText + '<input type="hidden" value="'+nValue+'" />');

            //dim2
            var sText = $(elL2Selectbox).find("option:selected").text();
            var nValue = $(elL2Selectbox).val();
            $(elL2Selectbox).parent().html(sText + '<input type="hidden" value="'+nValue+'" />');

            //dim3
            var sText = $(elL3Selectbox).find("option:selected").text();
            var nValue = $(elL3Selectbox).val();
            $(elL3Selectbox).parent().html(sText + '<input type="hidden" value="'+nValue+'" />');


            $(elThisRow).children().last().html('<input type="button" class="button-edit2" />');

            return false;
            
        });
    }

    /**
     * file drag&drop
     */
    if ($.fn.fileupload) {
    $('#fileupload').fileupload({
        dataType: 'json',
        url : '/?mod=903&print=ajax',
        dropZone : '#attacment_list',
        done: function (e, data) {
            $.each(data.result, function (index, file) {
                var sRow = 
                '<a>' + file.name + '</a>'
                + '<input type="button" class="button-delete" title="Delete file">'
                + '<input type="hidden" value="'+file.d_file_id+'">'
                + '<br />'
                ;
                $('#attacment_list').append(sRow);
            });

        }
    });
    }


    /**
     *nonjem dra&drop no visa ekrāna
     */
    $(document).bind('drop dragover', function (e) {
        e.preventDefault();
    });

    /**
     * failu dzēšana
     */
    $('#attacment_list').on('click','.button-delete',function(e){
        if(!confirm('Vai tiešām vēlaties dzēst failu?'))
            return false;
        var elName = $(this).prev();
        var elDel = $(this);
        var elHidden = $(this).next();
        var elBr = $(this).next().next();
        $.post('/?mod=903&print=ajax',
        {
            action          : "del_file" ,
            d_file_id       : $(this).next().val()

        },
        function(data) {

            data = $.parseJSON(data);

            /**
            * kljudu apstade
            */
            if (data["error"]){
                $("#error_info").show();
                $("#error_info").html(data["error"]);
                return false;
            }

            /**
             * izdzesh HTML rindu
             */
            $(elName).remove();
            $(elDel).remove();
            $(elHidden).remove();
            $(elBr).remove();

        });

    });

    /**
     * failu atvēršana
     */
    $('#attacment_list').on('click','a',function(e){
        window.open('/?mod=903&print=ajax&action=show_file&d_file_id='+$(this).next().next().val());
    });
    
    /**
     *paslepj visus order item input laukus
     */
    $('[id^="order_item_id"][value!="0"][value!=""]').prev().hide();

    $('#tbl_invoice').find('.button-change').click(function(){
        var $elTr = $(this).parent().parent();
        $($elTr).find('.more').hide(200);
        $(this).hide(200);
        $($elTr).find('[id^="order_item_number"]').show(200);
    })
    /**
     * order item autocompleet
     */
    $(document).on("keydown.autocomplete",'[id^="order_item_number"]',function(e){
        $(this).autocomplete({
            autoFocus: true,
            delay: 0,
            minLength: 2,
            source: function( request, response ){

                var lastXhr = $.getJSON(
                    "/?mod=902" + //d_order_ajax.php
                    "&print=ajax" +
                    "&action=order_item_outocompleet" +
                    "&invoice_type=" + $('#invoice_type').val(),
                    request,
                    function( data, status, xhr ) {
                        if ( xhr === lastXhr ) {
                            response( data );
                        }
                    }
                    )
            },
            select: function(event, ui) {
                /**
                    * set hidden field id on select
                    */
                var $elTr = $(this).parent().parent();
                $($elTr).find('[id^="order_item_id"]').val(ui.item.id);
                $($elTr).find('[id^="item_name"]').html(ui.item.item_name);
                $($elTr).find('[id^="item_price"]').val(ui.item.item_price);
                $($elTr).find('[id^="item_currency_id"]').val(ui.item.item_currency_id);
                $($elTr).find('[id^="item_qty"]').val(ui.item.item_qty);
                $($elTr).find('[id^="item_vat"]').val(ui.item.item_vat);

            }
        });
    });


});