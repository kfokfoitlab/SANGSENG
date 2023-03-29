 $("#BuyerValidation").submit(function(){

    if($("#emailCheck").val() == 0){
        alert("이메일 중복체크를 해주세요");
        return false;
    }
    var email = $("#email").val();
    if(!CheckEmail(email)) {
        alert('잘못된 형식의 이메일입니다');
        $("input[name='email']").focus();
        return false;
    }

    var pwd = $("#password").val();
    var confirm_password = $("#confirm_password").val();
     if(!sellerPwCheck(pwd)) {
         return false;
     }

     if(pwd != confirm_password){
         alert("비밀번호가 일치하지 않습니다.");
         $("#pwd-check").text("비밀번호가 일치하지 않습니다.")
         return false;
     }

    var phone = $("#phone").val();
    if(!checkPhone(phone)) {
        alert('올바른 휴대폰번호를 입력해주세요.')
        return false;
    }


    var companyCode = $("#company_code").val();
    if(!checkCompanyCode(companyCode)) {
        alert('올바른 사업자등록번호를 입력해주세요')
        return false;
    }
    if(!$('input:radio[name="classification"]').is(':checked')){
        $('#classificationCheck').show();
        $('input:radio[name="classification"]:eq(1)').focus();
        return false;
    };

    var workersCount = parseInt($("#workers").val().trim());
    var workersMild = parseInt($("#mild_disabled").val().trim());
    var workersSeverely = parseInt($("#severely_disabled").val().trim());
    var disabled = workersMild+workersSeverely;
    if(workersCount < disabled) {
        alert('장애인 근로자 수가 상시 근로자 수 보다 많습니다.');
        $("input[name='workers']").focus();
        return false;
    }
     var file = $("#buyer_doc").val()
     var extension = file.split(".").pop()
     var extension_check =['jpg','jpeg','png','gif','pdf','PNG','JPG','PDF','BMP','bmp','GIF'];
     if(extension_check.indexOf(extension) == -1){
         alert("허용되지않은 파일 확장자입니다.")
         return false;
     }
    return true;
});
function CheckEmail(str){
    var reg_email = /^([0-9a-zA-Z_\.-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,2}$/;
    if(!reg_email.test(str)) {
        return false;
    }else {
        return true;
    }
}

function buyerPwCheck(str){
    var num = str.search(/[0-9]/g);
    var eng = str.search(/[a-z]/ig);
    var spe = str.search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);
    if(str.length < 8 || str.length > 20){
        alert("비밀번호 8자리 ~ 20자리 이내로 입력해주세요.");
        $("#pwd-text").text("비밀번호 형식이 올바르지 않습니다.")
        return false;
    }else if(str.search(/\s/) != -1){
        alert("비밀번호는 공백 없이 입력해주세요.");
        $("#pwd-text").text("비밀번호 형식이 올바르지 않습니다.")

        return false;
    }else if(num < 0 || eng < 0 || spe < 0 ){
        alert("비밀번호 영문,숫자,특수문자를 혼합하여 입력해주세요.");
        $("#pwd-text").text("비밀번호 형식이 올바르지 않습니다.")
        return false;
    }else {
        return true;
    }
}

function checkPhone(num){
    var reg_phone = /^(010|011|016|017|018|019)-[0-9]{3,4}-[0-9]{4}$/;
    if(!reg_phone.test(num)) {
        return false;
    } else {
        return true;
    }
}

function checkCompanyCode(num){
    var reg_companyCd = /^[0-9]{3}-[0-9]{2}-[0-9]{5}$/;
    if(!reg_companyCd.test(num)) {
        return false;
    } else {
        return true;
    }
}