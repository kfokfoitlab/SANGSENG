
var datatables_lang_kor = {
	"decimal" : "",
	"emptyTable" : "데이터가 없습니다.",
	"info" : "_START_ - _END_ (총 _TOTAL_ 개의 데이터)",
	"infoEmpty" : "0건",
	"infoFiltered" : "(전체 _MAX_ 개 데이터 중 검색결과)",
	"infoPostFix" : "",
	"thousands" : ",",
	"lengthMenu" : "_MENU_ 개씩 보기",
	"loadingRecords" : "로딩중...",
	"processing" : "처리중...",
	"search" : "검색 : ",
	"zeroRecords" : "검색된 데이터가 없습니다.",
	"paginate" : {
		"first" : "첫 페이지",
		"last" : "마지막 페이지",
		"next" : "다음",
		"previous" : "이전"
	},
	"aria" : {
		"sortAscending" : " :  오름차순 정렬",
		"sortDescending" : " :  내림차순 정렬"
	}
}


$(document).ready(function(){

    // dropify
    $(".dropify").dropify({
        "messages": {
            default: '업로드할 파일을 이곳에 끌어오거나 클릭하세요.',
            replace: '교체할 파일을 이곳에 끌어오거나 클릭하세요.',
            remove: '삭제',
            error: '요청을 처리하지 못했습니다.'
        }
    });

    // notes
    $.each($("textarea.notes"), function(key, val){
        $.wysiwig("textarea.notes");
    });

    // datepicker
    $.each($("input.daterange"), function(key, val){
        $.dateRangePicker($(this));
    });

    // 우편번호 검색
    $(".modal-search-post").click(function(){
        $("#modal-search-post").modal("show");
        $.daumPostcode();
    });

    // Mask 폰번호
    $(".mask-phone").mask('099-0009-0000');
    // Mask 사업자등록번호
    $(".mask-business-number").mask('000-00-00000');
    // 금액
    $(".mask-pay").mask('#,##0', {reverse: true});

    // dropify
    $.dropify($(".dropify"));

});


$.extend({
    "dataTables": function(obj, option)
    { // {{{
        let y = moment().format("Y");
        let m = moment().format("MM");
        let d = moment().format("DD");
        let h = moment().format("HH");
        var suffix = y+m+d+"T"+h;

        var basic_option = {
            "lengthMenu": [ [10, 25, 50, 100, 300, 500, 1000], [10, 25, 50, 100, 300, 500, 1000]]
            ,"searching": false
            ,"language": datatables_lang_kor
            ,"autoWidth": true
            ,"processing": true
            ,"serverSide": true
            //,"dom": '<"float-right"B>lrt<"float-left"i>p'
            ,"dom": '<"float-end"B>lt<"float-start"i>p'
            ,"buttons": [
                {
                     "extend": "excelHtml5"
                    ,"className": "btn btn-sm bg-white"
                    ,"text": "<i class='fas fa-file-excel text-success' style='font-size: 2em;'>"
                    ,"filename": function(){
                        return document.title+suffix;
                    }
                    ,"exportOptions": {
                        "columns": "thead th:not(.noExport)",
                        "modifier": {
                            "page": "current"
                        }
                    }
                },
                {
                     "extend": "pdfHtml5"
                    ,"className": "btn btn-sm bg-white"
                    ,"text": "<i class='fas fa-file-pdf text-danger' style='font-size: 2em;'>"
                    ,"stripHtml": true
                    ,"pageSize": 'A4'
                    ,"exportOptions": {
                         "columns": "thead th:not(.noExport)"
                        ,"modifier": {
                            "page": "current"
                        }
                    }
                    ,"customize": function(doc) {
                        doc.defaultStyle.fontSize = 7;
                        doc.styles.tableHeader.fontSize = 7; 

                        var arr2 = $('.img-fluid').map(function(){
                            return this.src;
                        }).get();

                        // 이미지 필드 찾기
                        let img_key;
                        $.each(doc.content[1].table.body[0], function(key, val){
                            if(val.text == "이미지"){
                                img_key = key;
                                return false;
                            }
                        });
                        for (var i = 0, c = 1; i < arr2.length; i++, c++) {
                            doc.content[1].table.body[c][img_key] = {
                                image: arr2[i],
                                width: 16 
                            }
                        }
                    }
                    ,"filename": function(){
                        return document.title+suffix;
                    }
                },
                {
                     "extend": "print"
                    ,"className": "btn btn-sm bg-white"
                    ,"text": "<i class='fas fa-print text-dark' style='font-size: 2em;'>"
                    ,"filename": function(){
                        return document.title+suffix;
                    }
                    ,"customize": function(doc) {
                        $(doc.document.body)
                        .css( 'font-size', '7pt' );

                        $(doc.document.body).find( 'img' )
                            .css("width", "72px");
                        $(doc.document.body).find( 'table' )
                            .addClass( 'small' )
                            .css( 'font-size', 'inherit' );
                    }
                    ,"exportOptions": {
                        "stripHtml": false,
                        "columns": "thead th:not(.noExport)",
                        "modifier": {
                            "page": "current"
                        }
                    }
                },
            ]
        }
        var dt_option = Object.assign(basic_option, option);


        var datatables = $(obj).DataTable( dt_option );
        $('.dataTables_length').addClass('bs-select');

        return datatables;
    }, // }}}

    "wysiwig": function(obj)
    { // {{{
        tinymce.init({
            selector: obj,
            menubar: false,
            plugins: 'lists table code',
            toolbar1: 'undo redo | fontsize | bold italic | forecolor | backcolor',
            toolbar2: 'alignleft aligncenter alignright | bullist numlist | table',
            statusbar: false,
            forced_root_block: 'div'
        });
    }, // }}}

    "dateRangePicker": function(obj)
    { // {{{

        var korean_daterangepicker = {
             format: 'YYYY-MM-D'
            ,customRangeLabel: '직접 지정'
            ,applyLabel: '적용'
            ,cancelLabel: '취소'
            ,separator: " - "
            ,daysOfWeek: [
                "일",
                "월",
                "화",
                "수",
                "목",
                "금",
                "토"
            ]
            ,"monthNames": [
                 "1월"
                ,"2월"
                ,"3월"
                ,"4월"
                ,"5월"
                ,"6월"
                ,"7월"
                ,"8월"
                ,"9월"
                ,"10월"
                ,"11월"
                ,"12월"
            ]
            ,"firstDay": 0
        };

        $(obj).daterangepicker({
            autoUpdateInput: false,
            startDate: null,
            endDate: null,
            //startDate: moment().subtract(6, "days"),
            //endDate: moment(),
            locale: korean_daterangepicker,
            ranges: {
               '전체': [null, null],
               '오늘': [moment(), moment()],
               '어제': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               '지난 7일': [moment().subtract(6, 'days'), moment()],
               '지난 30일': [moment().subtract(29, 'days'), moment()],
               '이번달': [moment().startOf('month'), moment().endOf('month')],
               '저번달': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
               '지난 3개월': [moment().subtract(2, 'month').startOf('month'), moment()],
               '지난 6개월': [moment().subtract(5, 'month').startOf('month'), moment()],
               '지난 12개월': [moment().subtract(11, 'month').startOf('month'), moment()]
            }
        }, function(start, end, label) {

            if(label == "전체"){
                $(this.element).val("");
            }
            else{
                var d = start.format('YYYY-MM-DD') + "~" + end.format('YYYY-MM-DD');
                $(this.element).val(d);
            }

            $("form[name='form-searchbar']").submit();

        });

    }, // }}}

    "dropify": function(el)
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

    }, //}}}

    "daumPostcode": function()
    { //{{{
        var element_wrap = document.getElementById('post-wrap');

        // 현재 scroll 위치를 저장해놓는다.
        var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);

        // 주소 -> 좌표 변환기
        var geocoder = new kakao.maps.services.Geocoder();

        new daum.Postcode({
            oncomplete: function(data) {

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
                //$("a.close-modal").click();
                $("#modal-search-post").modal("hide");

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
