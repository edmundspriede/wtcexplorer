var MailWidget = function() {

    var $_mail_widget  = null;
    var $_reference_id = null;

    function init() {
        $_mail_widget = $('#mail-widget');

        if (!$_mail_widget.length) {
            return;
        }

        $_reference_id = $_mail_widget.attr('data-reference');
        $_doc_type = $_mail_widget.attr('data-doc');

        if (!$_reference_id) {
            return;
        }

        _loadContent();

        $_mail_widget.on('click', '.refresh', _refreshContent);
        $_mail_widget.on('click', '.open', _openMessage);
        $_mail_widget.on('click', '.close', _closeMessage);
    }

    function _loadContent() {
        $.get(
            '/index.php?mod=905&doc='+ $_doc_type + '&reference=' + $_reference_id + '&print=ajax&action=init',
            function(data) {
                try {
                    var json = $.parseJSON(data);
                    if (json.error) {
                        $_mail_widget.html(json.error);
                    } else {
                        $_mail_widget.html(json.html);
                    }
                } catch (e) {
                    $_mail_widget.html('Mail Wdiget Error!<br/>' + data);
                }
            }
        );
    }

    function _refreshContent() {

        $('.refresh i', $_mail_widget).addClass('fa-spin');

        $.post(
            '/index.php?mod=905&doc=' + $_doc_type + '&reference=' + $_reference_id + '&print=ajax',
            {action: 'load'}
        )
            .done(function(data) {
                $('.refresh i', $_mail_widget).removeClass('fa-spin');
                try {
                    var json = $.parseJSON(data);
                    if (json.error) {
                        $_mail_widget.html(json.error);
                    } else {
                        $_mail_widget.html(json.html);
                    }
                } catch (e) {
                    $_mail_widget.html('Mail Wdiget Error!<br/>' + data);
                }
            })
            .fail(function(xhr, status, error) {
                $('.refresh i', $_mail_widget).removeClass('fa-spin');
            });
    }

    function _openMessage() {

        $('.ajax-loader', $_mail_widget).show();
        var mId = $(this).attr('data-id');

        $.post(
            '/index.php?mod=905&reference=' + $_reference_id + '&print=ajax',
            {action: 'open', messageId: mId}
        )
            .done(function(data) {
                $('.ajax-loader', $_mail_widget).hide();
                try {
                    var json = $.parseJSON(data);
                    if (json.error) {
                        $_mail_widget.html(json.error);
                    } else {
                        $_mail_widget.find("[data-id='" + mId + "']").removeClass('unread').addClass('read');
                        $_mail_widget.append(json.html);
                    }
                } catch (e) {
                    $_mail_widget.html('Mail Wdiget Error!<br/>' + data);
                }
            })
            .fail(function(xhr, status, error) {
                $('.ajax-loader', $_mail_widget).hide();
            });
    }

    function _closeMessage() {
        $('#open-modal-message').remove();
        return false;
    }

    return {
        init: init
    };
}();

$(document).ready(function() {
    MailWidget.init();
});