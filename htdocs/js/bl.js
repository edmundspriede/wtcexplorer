function add_party(id, name, party_type)
{
    if(!id) id = '';
    if(!name) name = '';
    else name = name.replace(/<br>/g, "\n");
    if(!party_type) party_type = '';

    var row=insert_row("tbl_party", 1);
    var nr = row.id;
	
    cell = insert_cell(row);
    insert_form_element("checkbox", "party["+id+"]", 'X', cell);

    cell = insert_cell(row);
    cell.innerHTML = '<textarea name="bl_party_name['+nr+']" class="textarea-2" style="width:200px;height:60px;">'+name+'</textarea>';
	
    insert_form_element("hidden", "bl_party_id["+nr+"]", id, cell);
	
    cell = insert_cell(row);
    cell.innerHTML = "Type: ";
    el = insert_form_element("text", "bl_party_party_type["+nr+"]", party_type, cell, "", "input-n");
    el.setAttribute("maxlength", "2");
    el.style.width = "20px";

    cell = insert_cell(row);

    if (id)
    {
        el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
        el.onclick= function(is_e)
        {
            if(confirm('Are you sure?'))

            {
                set_value('del_party_id',id);
                this.form.submit();
            };
        };
		
    }

}

function add_container(id, container_no, size_id, type_id, height, ccont_id,seals, bruto, netto, status, bct_status,edim_date)
{
    if(!id) id = '';
    if(!container_no) container_no = '';
    if(!size_id) size_id = '';
    if(!type_id) type_id = '';
    if(!height) height = '';
    if(!seals) seals = '';
    if(!bruto) bruto = '';
    if(!netto) netto = '';
    if(!status) status = '';
    if(!ccont_id) ccont_id = 0;

    var row=insert_row("tbl_containers", 1);
    var nr = row.id;

    if(nr % 2==0) row.className="even";

    cell = insert_cell(row);
    insert_form_element("checkbox", "container["+id+"]", 'X', cell);

    cell = insert_cell(row);
    el = insert_form_element("text", "bl_container_container_no["+nr+"]", container_no, cell, "", "input-1");
    el.setAttribute("maxlength", 11);
    el.style.width="85px";

    cell = insert_cell(row);
    //	insert_form_element("select", "container_size_id["+nr+"]", size_id, cell, "", "select-0");
    el = insert_form_element("text", "bl_container_size["+nr+"]", size_id, cell, "", "input-n");
    el.style.width = "20px";
    el.setAttribute("maxlength", 2);

    cell = insert_cell(row);
    //	insert_form_element("select", "container_type_id["+nr+"]", type_id, cell, "", "select-0");
    el = insert_form_element("text", "bl_container_type["+nr+"]", type_id, cell, "", "input-n");
    el.style.width = "30px";
    el.setAttribute("maxlength", 3);

    cell = insert_cell(row);
    el = insert_form_element("text", "bl_container_height["+nr+"]", height, cell, "", "input-n");
    el.style.width = "20px";
    el.setAttribute("maxlength", 2);

    cell=insert_cell(row);
    el=insert_form_element("select","ccont_id["+nr+"]",ccont_id,cell,"ccont_iso_list","select-0");

    cell = insert_cell(row);
    cell.innerHTML = '<textarea name="bl_container_seals['+nr+']" class="input-0">'+seals+'</textarea>';

    cell = insert_cell(row);
    cell.innerHTML = "Bruto <input type='text' name='bl_container_bruto["+nr+"]' value='"+bruto+"' class='input-0' /><br />Netto <input type='text' name='bl_container_netto["+nr+"]' value='"+netto+"' class='input-0' />";

    cell = insert_cell(row);
    el = insert_form_element("text", "bl_container_status["+nr+"]", status, cell, "", "input-n");
    el.style.width = "15px";
    el.setAttribute("maxlength", 1);

    // bct_status
    cell = insert_cell(row);
    if (id)
    {
        if (edim_date)
            cell.innerHTML = edim_date;
        else
        {
            cell.innerHTML = bct_status + '<br /><a id="bct_book_'+id+'">Book</a>' ;
            $('#bct_book_'+id).click(function ()
            {
                ShowBookingPanel(this,id);
            }
            );
        }
    }

    //delete button
    cell = insert_cell(row);
    if (id)
    {
        el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
        el.onclick= function(is_e)
        {
            if(confirm('Are you sure?'))

            {
                set_value('del_container_id',id);
                this.form.submit();
            };
        };
    }
    insert_form_element("hidden", "bl_container_id["+nr+"]", id, cell);
}

function add_export_container(id, container_no, description,ccont_id,packages,seals,
    gross_weight, net_weight,bct_status,edim_date)
{
    if(!id) id = '';
    if(!container_no) container_no = '';
    if(!description) description = '';
    if(!seals) seals = '';
    if(!ccont_id) ccont_id = 0;
    if(!packages) packages = 0;
    if(!gross_weight) gross_weight = 0;
    if(!net_weight) net_weight = 0;
    if(!bct_status) bct_status = 'N';
    if(!edim_date) edim_date = '';

    var row=insert_row("tbl_containers", 1);
    var nr = row.id;

    if(nr % 2==0) row.className="even";

    //	cell = insert_cell(row);
    //	insert_form_element("checkbox", "container["+id+"]", '', cell);

    cell = insert_cell(row);
    el = insert_form_element("text", "bl_container_container_no["+nr+"]", container_no, cell, "", "input-1");
    el.setAttribute("maxlength", 11);
    el.style.width="85px";

    cell=insert_cell(row);
    el=insert_form_element("select","ccont_id["+nr+"]",ccont_id,cell,"ccont_iso_list","select-0");

    cell = insert_cell(row);
    cell.innerHTML = '<textarea name="description['+nr+']" class="input-0">'+description+'</textarea>';

    cell = insert_cell(row);
    el = insert_form_element("text", "packages["+nr+"]", packages, cell, "", "input-n");
    el.style.width = "20px";

    cell = insert_cell(row);
    cell.innerHTML = '<textarea name="bl_container_seals['+nr+']" class="input-0">'+seals+'</textarea>';

    cell = insert_cell(row);
    el = insert_form_element("text", "bl_container_netto["+nr+"]", net_weight, cell, "", "input-n");

    cell = insert_cell(row);
    el = insert_form_element("text", "bl_container_bruto["+nr+"]", gross_weight, cell, "", "input-n");

    // bct_status
    cell = insert_cell(row);
    if (id)
    {
        if (edim_date)
            cell.innerHTML = edim_date;
        else
        {
            cell.innerHTML = bct_status + '<br /><a id="bct_book_'+id+'">Book</a>' ;
            $('#bct_book_'+id).click(function ()
            {
                ShowBookingPanel(this,id);
            }
            );
        }
    }

    //delete button
    cell = insert_cell(row);
    if (id)
    {
        el=insert_form_element("button","__"+nr,'',cell,"","button-delete");
        el.onclick= function(is_e)
        {
            if(confirm('Are you sure?'))

            {
                set_value('del_container_id',id);
                this.form.submit();
            };
        };
    }
    insert_form_element("hidden", "bl_container_id["+nr+"]", id, cell);
}

function print_items(myContainerForm, myPartyForm, order_id, doc_id, lang)
{
    var formContainerCount = myContainerForm.length;
    var formPartyCount = myPartyForm.length;
    var str;
    var container = "";
    var party = "";

    for(var i = 0; i < formContainerCount; i++)
    {
        if(myContainerForm.elements[i].type=="checkbox")
        {
            if (myContainerForm.elements[i].checked==true)
            {
                str=myContainerForm.elements[i].id;
                if(str.match(/container/))
                {
                    str=str.substring(str.lastIndexOf("[")+1)
                    str=str.replace("]","");
                    container=container+","+str;
                }
            }
        }
    }
	
    if (container!="")
        container=container.substring(1);

    for(var i = 0; i < formPartyCount; i++)
    {
        if(myPartyForm.elements[i].type=="checkbox")
        {
            if (myPartyForm.elements[i].checked==true)
            {
                str=myPartyForm.elements[i].id;
                if(str.match(/party/))
                {
                    str=str.substring(str.lastIndexOf("[")+1)
                    str=str.replace("]","");
                    party=party+","+str;
                }
            }
        }
    }

    if (party!="")
        party=party.substring(1);


    window.open("/?mod=670&id="+order_id+"&doc_id="+doc_id+"&lang="+lang+"&container="+container+"&party="+party, "_blank");
}
function print_items_a(myContainerForm, myPartyForm, order_id, doc_id)
{
    var formContainerCount = myContainerForm.length;
    var formPartyCount = myPartyForm.length;
    var str;
    var container = "";
    var party = "";
    var nosacijums = "";

    for(var i = 0; i < formContainerCount; i++)
    {
        if(myContainerForm.elements[i].type=="checkbox")
        {
            if (myContainerForm.elements[i].checked==true)
            {
                str=myContainerForm.elements[i].id;
                if(str.match(/container/))
                {
                    str=str.substring(str.lastIndexOf("[")+1)
                    str=str.replace("]","");
                    container=container+","+str;
                }
            }
        }
    }
	
    if (container!="")
        container=container.substring(1);

    for(var i = 0; i < formPartyCount; i++)
    {
        if(myPartyForm.elements[i].type=="checkbox")
        {
            if (myPartyForm.elements[i].checked==true)
            {
                str=myPartyForm.elements[i].id;
                if(str.match(/party/))
                {
                    str=str.substring(str.lastIndexOf("[")+1)
                    str=str.replace("]","");
                    party=party+","+str;
                }
            }
        }
        if(myPartyForm.elements[i].name=="nosacijums")
        {
            nosacijums = myPartyForm.elements[i].value;
        }
        
    }

    if (party!="")
        party=party.substring(1);


    window.open("/?mod=670&id="+order_id+"&doc_id="+doc_id+"&container="+container+"&party="+party + "_blank&nosacijums="+nosacijums);
}

function addMonth(now) {
    if (now.getMonth() == 11) {
        return new Date(now.getFullYear() + 1, 0, now.getDate());
    } else {
        return  new Date(now.getFullYear(), now.getMonth() + 1, now.getDate());
    }
}

function ShowBookingPanel(tag,iBlContainerId)
{

    $('#bct_book_form').fadeOut();
    $('#bct_table_form').show();
    $('#bct_table_form_buttons').show();
    //$('#bct_book_form').css({'display':'inherit'});

    //set values
    $('#bct_bl_container_id').val(iBlContainerId);

    var dt = new Date();
    //var nMonth = currentTime.getMonth();
    //dt = addMonth(dt);
    //dt = addMonth(dt);
    nMonth = dt.getMonth() + 1;
    nDay = dt.getDate();
    nYear = dt.getFullYear();
    $('#bct_date_from').val(nDay + '.' + nMonth + '.' + nYear);

    dt = addMonth(dt);
    nMonth = dt.getMonth() + 1;
    nDay = dt.getDate();
    nYear = dt.getFullYear();
    $('#bct_date_to').val(nDay + '.' + nMonth + '.' + nYear);

    //pozicioneejam
    var fold = $(window).height() + $(window).scrollTop();

    var off = $(tag).offset();
    var h = $(tag).outerHeight();
    var panelHeight = parseInt($('#bct_book_form').css('height'), 10);
    //  var panelWidth = parseInt($('#bct_book_form').css('width'), 10);
    var panelWidth = 250;

    // Panelis bus arpus viewport
    if (fold < (off.top - h) - panelHeight) {
        var dif = (((off.top+h) - panelHeight) - fold) + 20;
        $('#bct_book_form').css({
            top:((off.top+h)-dif),
            left:off.left - panelWidth,
            position:'absolute'
        });
    }
    else {
        // Nopozicionejam paneli
        $('#bct_book_form').css({
            top:(off.top+h),
            left:off.left - panelWidth,
            position:'absolute'
        });
    }
    $('#bct_book_form').show();


}

function hidePartyForm(){
    $('#tr_party_edit_1').hide();
    $('#tr_party_edit_2').hide();
    $('#tr_party_edit_3').hide();
    $('#parties_name').val('');
    $('#parties_client_id').val('');
    $('#party_type').val('');
}


$(function(){

    if($("#tbl_party_list").length > 0){
        /**
     * party list del poga
     */
        $("#tbl_party_list").on("click", "input.button-delete", function(event){
            var elTr = $(this).parent().parent();
            lastXhr = $.getJSON(
                "/?mod=901" +
                "&print=ajax" +
                "&action=delete_party" +
                "&id=" + $(this).attr('data-key'),
                function( data, status, xhr ) {
                    if ( xhr === lastXhr ) {
                        $(elTr).hide('slow');
                        $(elTr).remove();
                    }

                }
                );
        });

        /**
             * hide parties input form on load
            */
        hidePartyForm();

        /**
     * parties list
     */
        lastXhr = $.getJSON(
            "/?mod=901" +
            "&print=ajax" +
            "&action=list" +
            "&order_id=" + $('#order_id').val(),
            function( data, status, xhr ) {
                if ( xhr === lastXhr ) {
                    if (data['error']){
                        alert(data['error']);
                        return;
                    }
                    if (data['html']){
                        $('#tbl_party_list').html(data['html']);
                    }

                }

            }
            )
       

        /**
             * show parties input form
             */
        $('#button_add_parties').click(function(){
            $('#tr_party_edit_1').show('slow');
            $('#tr_party_edit_2').show('slow');
            $('#tr_party_edit_3').show('slow');
        })

        /**
             * hide parties input form on cancel
             */
        $('#cancel_new_party').click(function(){
            hidePartyForm();
        })

        /**
             * party auto compleet
             */
        $('#parties_name').autocomplete({
            autoFocus: true,
            delay: 0,
            minLength: 2,
            source: function( request, response ){

                var lastXhr = $.getJSON(
                    "/?mod=900" +
                    "&print=ajax" +
                    "&action=client_autocomplete",
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
                $('#parties_client_id').val(ui.item.id);

            }
        });

        /**
             * party form save
             */
        $('#add_new_party').click(function(){
            if ($('#parties_name').val().length == 0){
                alert('Please provide party name!');
                return FALSE;
            }

            /**
                 * save data and refresh list
                 */
            lastXhr = $.getJSON(
                "/?mod=901" +
                "&print=ajax" +
                "&action=add_party" +
                "&order_id=" + $('#order_id').val() +
                "&client_id=" + $('#parties_client_id').val() +
                "&type=" + $('#party_type').val(),
                function( data, status, xhr ) {
                    if ( xhr === lastXhr ) {
                        if (data['html']){
                            $('#tbl_party_list').html(data['html']);
                        }
                        hidePartyForm();
                    }

                }
                )
        })
    }
    /**
    * booking form
    */
    if($('#bct_book_form').length>0){
        $('#bct_book_form').hide();
        $('#bct_cancel').click(function(){
            $('#bct_book_form').hide();
        })
        $('#bct_close').click(
            function()
            {
                $('#bct_book_form').hide();
                $('#bct_table_form').show();
                $('#bct_table_form_buttons').show();
                $('#bct_table_message').hide();
                $('#bct_table_message_buttons').hide();
            }
            )


        $('#bct_table_message').hide();
        $('#bct_table_message_buttons').hide();
        $('#bct_book').click
        (
            function()
            {
                var nBlContainerId = $('#bct_bl_container_id').val();
                var sBookingNr = $('#bct_booking_nr').val();
                var sDateFrom = $('#bct_date_from').val();
                var sDateTo = $('#bct_date_to').val();


                $('#bct_table_form').hide();
                $('#bct_table_form_buttons').hide();
                $('#bct_table_message').show();
                $('#bct_table_message_buttons').show();
                $('#bct_td_message').html('<img src="/images/ajax-loader.gif">');

                var sUrl = 'booking_ajax.php?'
                $.getJSON
                (
                    sUrl,
                    {
                        bl_container_id: nBlContainerId,
                        booking_nr: sBookingNr,
                        bct_date_from: sDateFrom ,
                        bct_date_to: sDateTo
                    }
                    , function(json)
                    {


                        var sMessage = json.message;
                        if (!json.error)
                        {
                            $('#bct_book_'+nBlContainerId).unbind('click').parent().html(sDateFrom);
                        }
                        else
                        {
                            sMessage = sMessage + '<br>';
                            for (k in json.error)
                            {
                                sMessage = sMessage + ' - ' + json.error[k] + '<br>';
                            }

                        }
                        $('#bct_td_message').html(sMessage);
                    }
                    );
            }
            )
    }
}
)

function import_booking(feeder,ovessel,origin, loadport, dischport,destport, etd_feeder, etd_voyage, eta_voyage, eta_destination) {
    
    $('#voyage_vessel_name').val(ovessel);
    $('#feeder_voyage_vessel_name').val(feeder);
    $('#feeder_voyage_pol').val(loadport);
    $('#feeder_voyage_pod').val(loadport);
    $('#voyage_pol').val(loadport);
    $('#voyage_pod').val(dischport);
    $('#feeder_voyage_ets').val(etd_feeder);
    $('#voyage_ets').val(etd_voyage);
    $('#voyage_eta').val(eta_voyage);
   
    
    
    
    
}