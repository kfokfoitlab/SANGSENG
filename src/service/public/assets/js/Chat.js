Pusher.logToConsole = true;

var pusher = new Pusher('b0420134976e49cf16f6', {
    cluster: 'ap3'
});

$(document).ready(function(){
    $("textarea[name='chat']").keydown(function(event){
        if(event.keyCode == 13){
            $("button.action-send-chat").click();
        }
    });

    $("button.action-send-chat").click(function(){
        const element = $("textarea[name='chat']");
        $.Send(element);
        $.DrawChat(element.val());
    });

    $.Receive("my-channel");
});


$.extend({
    "Send": function(el)
    { //{{{
        const chat = el.val();
        const channel = "my-channel";
        const message = chat;

        $.ajax({
             url: "/"+_CONTROLLER+"/Send"
            ,data: {channel: channel, message: message}
            ,dataType: "json"
            ,type: "POST"
            ,success: function(res){
                //console.log("ok");
                
                //$.DrawChat(res.message);
            }
            ,error: function(res){
                console.log("error");
            }
        });
    } //}}}

    ,"Receive": function(channel)
    { //{{{

        var channel = pusher.subscribe(channel);

        channel.bind('my-event', function(data) {
            $.DrawChat(data.message);
            //alert(JSON.stringify(data));
        });

    } //}}}

    ,"DrawChat": function(message)
    { //{{{
        let html  = "";
            html += "<div class='border p-3 my-2'>"+message+"</div>"

        $("#chat-wrap").append(html);
        $("textarea[name='chat']").val("").select();

    } //}}}
});
