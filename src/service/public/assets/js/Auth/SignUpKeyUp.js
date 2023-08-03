$(document).ready(function(){
    $("#password").keyup(function(){
        var password = $("#password").val();
        var confirm_password = $("#confirm_password").val();
        const num = password.search(/[0-9]/g);
        const eng = password.search(/[a-z]/ig);
        const spe = password.search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);
        if(password.length < 8 || password.length > 20){
            $("#pwd-text").text("비밀번호 형식이 올바르지 않습니다.").css("color","red")
        }else if(password.search(/\s/) != -1){
            $("#pwd-text").text("비밀번호 형식이 올바르지 않습니다.").css("color","red")
        }else if(num < 0 || eng < 0 || spe < 0 ){
            $("#pwd-text").text("비밀번호 형식이 올바르지 않습니다.").css("color","red")
        }else {
            $("#pwd-text").text("사용가능한 비밀번호 입니다.").css("color","green")
        }
        if(password == confirm_password){
            $("#pwd-check").text("비밀번호가 일치합니다.").css("color","green")
        }else{
            $("#pwd-check").text("비밀번호가 일치하지 않습니다.").css("color","red")
        }
    });

    $("#confirm_password").keyup(function(){
        var password = $("#password").val();
        var confirm_password = $("#confirm_password").val();

        if(password == confirm_password){
            $("#pwd-check").text("비밀번호가 일치합니다.").css("color","green")
        }else{
            $("#pwd-check").text("비밀번호가 일치하지 않습니다.").css("color","red")
        }
    })

    $("#phone").keyup(function(){
        var phone = $("#phone").val();
        const reg_phone = /^(010|011|016|017|018|019)-[0-9]{3,4}-[0-9]{4}$/;
        if(!reg_phone.test(phone)) {
            $("#phone-text").text("휴대폰 번호 형식이 올바르지 않습니다.").css("color","red")
        } else {
            $("#phone-text").text("사용가능한 휴대폰 번호입니다.").css("color","green")
        }
    })
    $("#company_code").keyup(function(){
        var company_code = $("#company_code").val();
        const reg_companyCode = /^[0-9]{3}-[0-9]{2}-[0-9]{5}$/;
        if(!reg_companyCode.test(company_code)) {
            $("#companyCode-text").text("사업자등록번호 형식이 올바르지 않습니다.").css("color","red")
        } else {
            $("#companyCode-text").text("사용가능한 사업자등록번호 입니다.").css("color","green")
        }
    })
});
