var ajaxUrl = "/?mod=270&print=ajax";
$(function () {
    if($("#dim_tree").length > 0){
        $("#dim_tree").jstree({
            "json_data" : {
                "ajax" : {
                    "url" : ajaxUrl,
                    "data" : function (n) {
                        return {
                            id : n.attr ? n.attr("id") : 0
                        };
                    }
                }
            },
            "ui" : {
                "select_limit" : 1,
                "selected_parent_close" : "select_parent"
            },
            "plugins" : [ "themes", "json_data", "ui" ]
        }
        );
        $("#dim_tree").on("click", "a", function(e) {
            var nId = $(this).parent().attr("id");
            $.getJSON
            (
                ajaxUrl,
                {
                    action: "get_form",
                    id: nId
                }
                , function(json){

                    var sMessage = json.message;
                    if (!json.error)
                    {
                        $('#dim_form').html(json.html);
                        $( "#tabs" ).tabs();
                    }
                    else
                    {
                        sMessage = sMessage + '<br>';
                        for (k in json.error)
                        {
                            sMessage = sMessage + ' - ' + json.error[k] + '<br>';
                        }

                    }
                //$('#bct_td_message').html(sMessage);
                }
                );

        })

        $('#dim_form').on('click', '#save_dim_form', function(event){
            var formData = $('#dim_edit_form').serialize();
            formData = formData + '&action=save_form';

            $.getJSON(
                ajaxUrl,
                formData,
                function(json){

                    var sMessage = json.message;
                    if (!json.error){
                        $('#dim_form').html('');
                        var tree = jQuery.jstree._reference("#dim_tree");
                        var currentNode = tree._get_node(null, false);
                        var parentNode = tree._get_parent(currentNode);
                        tree.refresh(parentNode);
                    }else{
                        alert(json.error);
                    }

                }
                );

        })

        $('#dim_form').on('click', '#delete_dim', function(event){
            var formData = $('#dim_edit_form').serialize();
            formData = formData + '&action=delete_level';

            $.getJSON(
                ajaxUrl,
                formData,
                function(json){

                    var sMessage = json.message;
                    if (!json.error){
                        $('#dim_form').html('');
                        var tree = jQuery.jstree._reference("#dim_tree");
                        var currentNode = tree._get_node(null, false);
                        var parentNode = tree._get_parent(currentNode);
                        tree.refresh(parentNode);
                    }else{
                        alert(json.error);
                    }

                }
                );

        })

        $('#dim_form').on('click', '#save_dim_add_form', function(event){
            var formData = $('#dim_add_form').serialize();
            formData = formData + '&action=save_add_form';

            $.getJSON(
                ajaxUrl,
                formData,
                function(json){

                    var sMessage = json.message;
                    if (!json.error){
                        $('#dim_form').html('');
                        var tree = jQuery.jstree._reference("#dim_tree");
                        var currentNode = tree._get_node(null, false);
                        var parentNode = tree._get_parent(currentNode);
                        tree.refresh(parentNode);
                    }else{
                        alert(json.error);
                    }

                }
                );

        })
    }else{

        $('#save_dim_add_root_form').click(function() {
            $('#dim_add_root_form').submit();
        });
    }

});