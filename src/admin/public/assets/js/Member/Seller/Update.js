
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
