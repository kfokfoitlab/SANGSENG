$(document).ready(function(){
    $("form[name='form-submit']").submit(function(){

        // 비밀번호 변경
        if($("input[name='password']").val() != $("input[name='confirm_password']").val()){
            alert("비밀번호 확인이 맞지 않습니다.");
            return false;
        }

        return true;
    });
});

// 주장애, 장애부위 선택
$(document).on("change", "#search-impairment-type0, #search-impairment-type1", function(){
    $.chooseImpairmentType($(this));
})



$.extend({
    "chooseImpairmentType": function(el)
    { //{{{
        const this_level = el.attr("data-this-level");
        const idx = el.val();

        if(this_level == 0){
            $("#search-impairment-type1 option[value!='']").hide();
            $("#search-impairment-type1 option[data-level0_idx='"+idx+"']").show();
            $("#search-impairment-type1").val("").prop("selected", true);
            $("#search-impairment-type1").val("").prop("selected", true);
        }
    } //}}}
});
