Pusher.logToConsole = true;

var pusher = new Pusher('b0420134976e49cf16f6', {
    cluster: 'ap3'
});

$(document).ready(function(){
    $("textarea[name='chat']").keyup(function(event){
        if(event.keyCode == 13){
            $("button.action-send-chat").click();
        }
    });

    $("button.action-send-chat").click(function(){
        const element = $("textarea[name='chat']");
        if($.trim(element.val()) && $.trim(element.val()) != ""){
            $.Send(element);
            $.DrawChatMySelf(element.val());
        }
        else {
            element.val("");
        }
    });

    $.Receive(channel_uuid);

    setTimeout(function(){
        $('#chat-wrap').scrollTop($('#chat-wrap')[0].scrollHeight);
    }, 300);
});


$.extend({
    // user 이벤트로 보냄.
    "Send": function(el)
    { //{{{
        const chat = el.val();
        const channel = channel_uuid
        const message = chat;

        $.ajax({
             url: "/"+_CONTROLLER+"/Send"
            ,data: {channel: channel, message: message, chat_type: "user"}
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


    // company 이벤트로 온 것만 받음
    ,"Receive": function(channel)
    { //{{{

        var channel = pusher.subscribe(channel);

        channel.bind('company', function(data) {
            $.DrawChat(data);
        });

    } //}}}

    ,"DrawChat": function(data)
    { //{{{
        let html  = "";
            html += '<div class="chat-left p-3" style="max-width:80%">';
            html += '  <div class="d-flex">';
            html += '    <div>';
            html += '      <figure class="image">';
            html += '        <img src="/Image/'+data.profile_img_uuid+'" alt="" style="width:48px;">';
            html += '      </figure>';
            html += '    </div>';
            html += '    <div class="ms-2">';
            html += '      <h6>'+data.name+'</h6>';
            html += '      <p class="bg-light rounded p-3">'+data.message+'</p>';
            html += '      <div class="small text-muted">';
            html += '        2022-09-10 1:23:21';
            html += '      </div>';
            html += '    </div>';
            html += '  </div>';
            html += '</div>';

        $("#chat-wrap").append(html);
        $("textarea[name='chat']").val("").select();

        $('#chat-wrap').scrollTop($('#chat-wrap')[0].scrollHeight);

    } //}}}

    // 내가 보낸 메세지
    ,"DrawChatMySelf": function(message)
    { //{{{

        const timezoneOffset = new Date().getTimezoneOffset() * 60000;
        const timezoneDate = new Date(Date.now() - timezoneOffset);
        const date = timezoneDate.toISOString().substr(0, 16).replace("T", " ");

        let html  = "";
            html += '<div class="chat-right p-3">';
            html += '  <div class="w-100">';
            html += '    <div class="ms-auto" style="max-width:80%">';
            html += '      <div class="float-end bg-light rounded p-3">'+message+'</div>';
            html += '      <div class="text-end small text-muted" style="clear: both">';
            html += '        ' + date;
            html += '      </div>';
            html += '    </div>';
            html += '  </div>';
            html += '</div>';

        $("#chat-wrap").append(html);
        $("textarea[name='chat']").val("").select();

        $('#chat-wrap').scrollTop($('#chat-wrap')[0].scrollHeight);

    } //}}}
});

