

var conn = new WebSocket('wss://app.kls.lv:8888');

conn.onopen = function(e) {
    console.log("Connection established!");
    
    $('#statusbox').css("background-color",  '#82e0aa');
    $('#statusbox').html('<i class="fa fa-bolt"></i> Connected!');
    
};

conn.onmessage = function(e) {
    console.log(e.data);
    
    var jsonObject = JSON.parse(e.data);
    var action = jsonObject.action;
    var orderid = jsonObject.orderid;
    var action = jsonObject.action;
    var salesid = jsonObject.salesid;
    
    var alertitem =  $("#alertbox .alert").last().clone();
    alertitem.append('<a href="#">Order '+action+' : Id '+salesid+'</a>');
    alertitem.css("display", "block");
    
    if (action == 'create')  alertitem.attr('class', "alert alert-info alert-dismissible");
    else if (action == 'update')  alertitem.attr('class', "alert alert-success alert-dismissible");
    
    $("#alertbox").prepend(alertitem);
    
    
};


