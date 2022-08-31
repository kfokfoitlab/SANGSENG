$(document).ready(function(){
    $("form[name='form-submit']").submit(function(){
        const email = $("input[name='email']").val();
        if(!CheckEmail(email)) {
            alert('잘못된 형식의 이메일입니다');
            $("input[name='email']").focus();
            return false;
        }

        const pwd = $("input[name='password']").val();
        if(!checkPassword(pwd)) {
            return false;
        }

        if($("input[name='password']").val() != $("input[name='confirm_password']").val()){
            alert("비밀번호 확인이 맞지 않습니다.");
            return false;
        }

        const phone = $("input[name='phone']").val();
        if(!checkPhone(phone)) {
            alert('올바른 휴대폰번호를 입력해주세요.')
            return false;
        }

        const companyCode = $("input[name='company_code']").val();
        if(!checkCompanyCode(companyCode)) {
            alert('올바른 사업자등록번호를 입력해주세요')
            return false;
        }

        return true;
    });
});

// 주장애, 장애부위 선택
// $(document).on("change", "#search-impairment-type0, #search-impairment-type1", function(){
//     $.chooseImpairmentType($(this));
// })



// $.extend({
//     "chooseImpairmentType": function(el)
//     { //{{{
//         const this_level = el.attr("data-this-level");
//         const idx = el.val();
//
//         if(this_level == 0){
//             $("#search-impairment-type1 option[value!='']").hide();
//             $("#search-impairment-type1 option[data-level0_idx='"+idx+"']").show();
//             $("#search-impairment-type1").val("").prop("selected", true);
//             $("#search-impairment-type1").val("").prop("selected", true);
//         }
//     } //}}}
// });

function CheckEmail(str){
    const reg_email = /^([0-9a-zA-Z_\.-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,2}$/;
    if(!reg_email.test(str)) {
        return false;
    }else {
        return true;
    }
}

function checkPassword(str){
    const num = str.search(/[0-9]/g);
    const eng = str.search(/[a-z]/ig);
    const spe = str.search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);

    if(str.length < 8 || str.length > 20){
        alert("비밀번호 8자리 ~ 20자리 이내로 입력해주세요.");
        return false;
    }else if(str.search(/\s/) != -1){
        alert("비밀번호는 공백 없이 입력해주세요.");
        return false;
    }else if(num < 0 || eng < 0 || spe < 0 ){
        alert("비밀번호 영문,숫자,특수문자를 혼합하여 입력해주세요.");
        return false;
    }else {
        return true;
    }
}

function checkPhone(num){
    const reg_phone = /^(010|011|016|017|018|019)-[0-9]{3,4}-[0-9]{4}$/;
    if(!reg_phone.test(num)) {
        return false;
    } else {
        return true;
    }
}

function checkCompanyCode(num){
    const reg_phone = /^[0-9]{3}-[0-9]{2}-[0-9]{5}$/;
    if(!reg_phone.test(num)) {
        return false;
    } else {
        return true;
    }
}