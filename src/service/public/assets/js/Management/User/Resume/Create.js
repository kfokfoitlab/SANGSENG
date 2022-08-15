$(document).ready(function(){

});

// 학력 추가
$(document).on("click", "button.action-add-school", function(){ $.addSchool(); });
// 학력 삭제
$(document).on("click", "button.action-remove-school", function(){ $.removeSchool($(this)); });

// 경력 추가
$(document).on("click", "button.action-add-career", function(){ $.addCareer(); });
// 경력 삭제
$(document).on("click", "button.action-remove-career", function(){ $.removeCareer($(this)); });

// 수상내역 추가
$(document).on("click", "button.action-add-award", function(){ $.addAward(); });
// 수상내역 삭제
$(document).on("click", "button.action-remove-award", function(){ $.removeAward($(this)); });

// 수상내역 추가
$(document).on("click", "button.action-add-license", function(){ $.addLicense(); });
// 수상내역 삭제
$(document).on("click", "button.action-remove-license", function(){ $.removeLicense($(this)); });


$.extend({
    "addSchool": function()
    { //{{{
        let template = $("#template-school").children().clone();

        $("#school-wrap").append(template);
    } //}}}

    ,"removeSchool": function(el)
    { //{{{
        el.parents(".resume-block").remove();
    } //}}}

    ,"addCareer": function()
    { //{{{
        let template = $("#template-career").children().clone();

        $("#career-wrap").append(template);
    } //}}}

    ,"removeCareer": function(el)
    { //{{{
        el.parents(".resume-block").remove();
    } //}}}

    ,"addAward": function()
    { //{{{
        let template = $("#template-award").children().clone();

        $("#award-wrap").append(template);
    } //}}}

    ,"removeAward": function(el)
    { //{{{
        el.parents(".resume-block").remove();
    } //}}}

    ,"addLicense": function()
    { //{{{
        let template = $("#template-license").children().clone();

        $("#license-wrap").append(template);
    } //}}}

    ,"removeLicense": function(el)
    { //{{{
        el.parents(".resume-block").remove();
    } //}}}
});

