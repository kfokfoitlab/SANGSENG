
// bookmark 등록
$(document).on("click", "button.action-add-bookmark", function(){
    const user_uuid = $(this).attr("data-user-uuid");
    const application_uuid = $(this).attr("data-application-uuid");

    $.bookmark($(this), "add", user_uuid, application_uuid);
});
// bookmark 제거
$(document).on("click", "button.action-remove-bookmark", function(){
    const user_uuid = $(this).attr("data-user-uuid");
    const application_uuid = $(this).attr("data-application-uuid");

    $.bookmark($(this), "remove", user_uuid, application_uuid);
});



$.extend({
    "bookmark": function(el, type, user_uuid, application_uuid)
    { //{{{
        $.ajax({
             url: "/"+_CONTROLLER+"/Bookmark"
            ,data: {type: type, user_uuid: user_uuid, application_uuid: application_uuid}
            ,dataType: "json"
            ,type: "POST"
            ,success: function(res){
                if(type == "add"){
                    $(el).removeClass("action-add-bookmark");
                    $(el).addClass("action-remove-bookmark");

                    $(el).children().removeClass("far fa-2x fa-bookmark text-dark");
                    $(el).children().addClass("fas fa-2x fa-bookmark text-primary");
                }
                else {
                    $(el).removeClass("action-remove-bookmark");
                    $(el).addClass("action-add-bookmark");

                    $(el).children().removeClass("fas fa-2x fa-bookmark text-primary");
                    $(el).children().addClass("far fa-2x fa-bookmark text-dark");
                }
            }
            ,error: function(res){
                console.log("error: " + res);
            }
        });
    } //}}}
});
