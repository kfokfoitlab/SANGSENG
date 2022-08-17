const origin_font_size = 24;
let big_help_title = 0;
let bg_color = 1;


$(document).ready(function(){

    // 글자크게보기 쿠키 처리
    // let $cookie = document.cookie;
    // $cookie = $cookie.split("; ");
    // $.each($cookie, function(key, val){
    //     console.log(val);
    //     let c = val.split("=");
    //     if(c[0] == "show_big_text" && c[1] == "1"){
    //         $("#show-big-text").prop("checked", true);
    //         $("#big-help-title").show();
    //         big_help_title = 1;
    //     }
    //     else {
    //         $("#show-big-text").prop("checked", false);
    //         $("#big-help-title").hide();
    //         big_help_title = 0;
    //     }
    // });

    // $("button.action-focus-first").click(function(){
    //     //console.log($("header").next().find("input, select, textarea, a").eq(0));
    //     //$("section").eq(1).find(":visible:enabled:first").focus();
    //     console.log($("[tabindex=100]").eq(0));
    //     $("[tabindex=100]").eq(0).focus();
    // });

    // $("button.action-font-size-up").click(function(){
    //     const size = parseInt($("body").css("font-size").replace("px", ""));
    //     const change_size = size + 2;
    //     $("body").css("font-size", change_size + "px");
    // });

    // $("button.action-font-size-down").click(function(){
    //     const size = parseInt($("body").css("font-size").replace("px", ""));
    //     const change_size = size - 2;
    //     $("body").css("font-size", change_size + "px");
    // });

    // $("button.action-font-size-origin").click(function(){
    //     $("body").css("font-size", origin_font_size + "px");
    // });

    // $("button.action-change-bg-color").click(function(){
    //     $.changeBgColor();
    // });

    // 글자 크게 보기
    // $("#show-big-text").change(function(){
    //     const tp = $(this).prop("checked");
    //     if(tp == true){
    //         $("#big-help-title").show();
    //         big_help_title = 1;
    //     }
    //     else{
    //         $("#big-help-title").hide();
    //         big_help_title = 0;
    //     }
    //
    //     // 설정값 쿠키 저장 (30일)
    //     let date = new Date(Date.now() + (86400*30));
    //     date = date.toUTCString();
    //     document.cookie = "show_big_text="+big_help_title+"; path=/; expires=" + date;
    // });

    // $("#big-help-title #close").click(function(){
    //     $("#show-big-text").prop("checked", false);
    //     $("#big-help-title").hide();
    //     big_help_title = 0;
    // });

    // 우편번호 검색
    $(".modal-search-post").click(function(){
        $("#modal-search-post").modal("show");
        $.daumPostcode();
    });

    // Mask 폰번호
    $(".mask-phone").mask('0099-0009-0000');
    // Mask 사업자등록번호
    $(".mask-business-number").mask('000-00-00000');
    // 금액
    $(".mask-pay").mask('#,##0', {reverse: true});

    // notes
    $.each($("textarea.editor"), function(key, val){
        $.wysiwig("textarea.editor");
    });

    // dropify
    $.dropify($(".dropify"));

    // pagination
    $(".pagination").click(function(){
        const page = $(this).attr("data-page");
        $("input:hidden[name='page']").val(page);
        $("form[name='form-search']").submit();
    });
});


$(document).on("mouseenter", "a, button, input, select, img, textarea", function(){
    if(!big_help_title) return ;

    const tag = $(this)[0].tagName; 
    $.bigTitle($(this), tag);
});
$(document).on("mouseleave", "a, button, input, select, img", function(){
    if(!big_help_title) return ;

    $.bigTitleClear();
});



$.extend({
    "bigTitle": function(el, tag)
    { // {{{
        let title;
        let href;
        let type;

        switch(tag){
            case "A":
                title = (el.attr("title"))? el.attr("title") : el.text();
                href = el.attr("href"); 
                target = el.attr("target"); 
                if(el.children().length && el.children()[0].tagName == "IMG"){
                    title = el.children().attr("alt");
                }
                else {
                    if(href && !target){
                        title += " 바로가기";
                    }
                    else if(href && target){
                        title += " 새창열기";
                    }
                }

                break;

            case "BUTTON":
                title = (el.attr("title"))? el.attr("title") : el.text();
                type = el.attr("type"); 
                if(type == "submit"){
                    title += " 전송하기";
                }
                else {
                    title += " 버튼";
                }

                break;

            case "TEXTAREA":
                title = (el.attr("title"))? el.attr("title") : el.text();
                title += " 텍스트 영역 입력";
                break;

            case "INPUT":
                if(el.attr("type") == "text"){
                    title = el.attr("title");
                    title += " 입력상자";
                }
                else if(el.attr("type") == "email"){
                    title = el.attr("title");
                    title += " 이메일 입력상자";
                }
                else if(el.attr("type") == "password"){
                    title = el.attr("title");
                    title += " 비밀번호 입력상자";
                }
                else if(el.attr("type") == "number"){
                    title = el.attr("title");
                    title += " 숫자형식 입력상자";
                }
                else if(el.attr("type") == "checkbox"){
                    title = el.attr("title");
                    title += " 체크박스";
                }
                else if(el.attr("type") == "radio"){
                    title = el.attr("title");
                    title += " 라디오버튼";
                }

                break;

            case "SELECT":
                title = el.attr("title");
                title += " 선택상자";

                break;

            case "IMG":
                title = el.attr("alt");
                title += " 이미지";

                break;
        }

        $("#big-help-title p").text(title);
    } // }}}

    ,"bigTitleClear": function()
    { // {{{
        $("#big-help-title p").text("");
    } // }}}

    ,"changeBgColor": function()
    { // {{{
        if(bg_color){
            $("body").addClass("dark-mode");
            bg_color = 0;
        }
        else {
            $("body").removeClass("dark-mode");
            bg_color = 1;
        }
    } // }}}

    ,"wysiwig": function(obj)
    { // {{{
        tinymce.init({
            selector: obj,
            menubar: false,
            plugins: 'lists table code',
            toolbar1: 'undo redo | fontsize | bold italic | forecolor | backcolor',
            toolbar2: 'alignleft aligncenter alignright | bullist numlist | table',
            statusbar: false,
            forced_root_block: 'div',
            content_style: "body {font-size: 18pt;}"
        });
    } // }}}

    ,"dropify": function(el)
    { //{{{
        const drp = el.dropify({
            "messages": {
                default: '업로드할 파일을 이곳에 끌어오거나 클릭하세요.',
                replace: '교체할 파일을 이곳에 끌어오거나 클릭하세요.',
                remove: '삭제',
                error: '요청을 처리하지 못했습니다.'
            }
        });

        drp.on('dropify.beforeClear', function(event, element) {
            return confirm("파일을 삭제하시겠습니까?");
        });
        drp.on('dropify.afterClear', function(event, element) {
            const file_uuid = element.input.parents(".dropify-wrapper").parent().find("input:hidden[name='pre_file[]']").val();
            element.input.parents(".dropify-wrapper").parent().find("input:hidden[name='pre_file[]']").val("");
            element.input.parents(".dropify-wrapper").parent().find("input:hidden[name='remove_file[]']").val(file_uuid);

        });

    } //}}}

    ,"daumPostcode": function()
    { //{{{
        var element_wrap = document.getElementById('post-wrap');

        // 현재 scroll 위치를 저장해놓는다.
        var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);

        // 주소 -> 좌표 변환기
        var geocoder = new kakao.maps.services.Geocoder();

        new daum.Postcode({
            oncomplete: function(data) {
                console.log(data);

                var addr = '';
                var extraAddr = '';

                //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
                if(data.userSelectedType === 'R'){
                    // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                    // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                    if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있고, 공동주택일 경우 추가한다.
                    if(data.buildingName !== '' && data.apartment === 'Y'){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                    if(extraAddr !== ''){
                        extraAddr = ' (' + extraAddr + ')';
                    }
                    // 조합된 참고항목을 해당 필드에 넣는다.
                    //document.getElementById("sample3_extraAddress").value = extraAddr;

                } else {
                    //document.getElementById("sample3_extraAddress").value = '';
                }

                $("#post_code").val(data.zonecode);
                $("#address_road").val(data.roadAddress);
                $("#address_jibun").val(data.jibunAddress);
                $("a.close-modal").click();

                $("#post_code").siblings("label").addClass("active");
                $("#address_road").siblings("label").addClass("active");
                $("#address_jibun").siblings("label").addClass("active");
                $("#address_detail").siblings("label").addClass("active");
                setTimeout(function(){
                    $("#address_detail").focus();
                }, 300);

                // 좌표 저장
                geocoder.addressSearch(data.roadAddress, function(result, status){
                    $("#coordinate_x").val(result[0].x);
                    $("#coordinate_y").val(result[0].y);
                });

                return ;


            },
            // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
            onresize : function(size) {
                element_wrap.style.height = size.height+'px';
            },
            width : '100%',
            height : '100%'
        }).embed(element_wrap);

        // iframe을 넣은 element를 보이게 한다.
        element_wrap.style.display = 'block';

    } //}}}
});
