$(document).ready(function(){
    // 이력서 선택시 미리보기
    $("select[name='resume_uuid']").change(function(){
        $.resumePreview($(this).val());
    });
});


$.extend({
    "resumePreview": function(uuid)
    { //{{{
        $.ajax({
             url: "/Management/User/Resume/DetailPreview/"+uuid
            ,dataType: "html"
            ,type: "GET"
            ,success: function(res){
                $("#preview-resume").html(res);
            }
            ,error: function(res){
                console.log(res);
            }
        }); 
    } //}}}
});

