$(document).ready(function(){

    // datatable {{{
    var dt_option = {
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        //"dom": 'lt<"float-left"i>p',
        //"dom": 'ltip',
        "exportFilename": "KFO_장애인취업포털_인재목록",
        "exportTitle": "인재목록",
        "ajax": {
            url: "/"+_CONTROLLER+"/getList"
            ,type: "POST"
            ,data: function(data){
                data.columns[2].search.value = $("#search-daterange").val();
                data.columns[4].search.value = $("#search-recommended").val();
            }
        },
        "order": [
            [0, "desc"]
        ],
        "columns": [
            {title: "idx", data: "idx", visible: false}
            ,{title: "약관 종류", data: "category", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
                    switch(data){
                        case "Terms/TermsOfService":
                            html = "<span class='text-success'>사이트이용약관</span>";
                            break;
                        case "Terms/PrivacyPolicy":
                            html = "<span class='text-danger'>개인정보 수집 및 이용 동의</span>";
                            break;
                        case "Terms/Subscribe":
                            html = "<span class='text-info'>광고성 정보 수신</span>";
                            break;
                        case "Terms/ServiceLevelAgreement":
                            html = "<span class='text-green'>책임한계 및 법적고시</span>";
                            break;
                        case "Terms/AdditionalService":
                            html = "<span class='text-primary'>이메일 주소 무단 수집거부</span>";
                            break;
                    }
                    return html;
                }}
            ,{title: "등록일", data: "register_date", visible: true, className: "text-nowrap"}
            ,{title: "수정일", data: "update_date", visible: true, className: "text-nowrap"}
            ,{title: "사용시작일", data: "use_date", visible: true, className: "text-nowrap"}
            ,{title: "미사용 일", data: "unused_date", visible: true, className: "text-nowrap"}
            ,{title: "사용여부", data: "terms_status", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
                    switch(data){
                        case "1":
                            html = "<span class='text-info'>신규등록</span>";
                            break;
                        case "2":
                            html = "<span class='text-success'>사용중</span>";
                            break;
                        case "3":
                            html = "<span class='text-danger'>미사용</span>";
                            break;
                    }
                    return html;
                }}
            ,{title: "진행상황변경", data: "idx", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
                    html = html + "<input class='btn btn-success btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",2)' value='사용'>";
                    html = html + "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",3)' value='미사용'>";
                    return html;
                }
            }
            ,{title: "상세보기", data: {"idx":"idx","search_type":"search_type"},visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ,data1){
                    let html = "";
                    html += "<a";
                    html += "   class='button btn-secondary btn-sm m-1'";
                    html += "   href='/"+_CONTROLLER+"/Detail?idx="+data['idx']+"' target='_self'>";
                    html += "상세보기";
                    html += "</a>";
                    return html;
                }
            }
        ],
        "initComplete": function(settings, json)
        {
            $("form[name='form-searchbar'] .search").keypress(function(event){
                if(event.keyCode == 13){
                    $("form[name='form-searchbar']").submit();
                }
            });

            $("form[name='form-searchbar']").submit(function(e){
                e.preventDefault();
                dt.ajax.reload();
                return false;
            });

            $(".filter").change(function(){
                dt.ajax.reload();
            });
        },
    };

    dt = $.dataTables($("#datatables"), dt_option);

    /*
    $("#datatables tbody").on("dblclick", "tr", function()
    { // {{{
            $(this).find("a").get(0).click();
    }); // }}}
    */


    // }}}

});
function statusUpdate(idx,status){
    location.href = "/"+_CONTROLLER+"/statusUpdate?idx="+idx+"&status="+status;
}
function deleteWorker(idx){
    if(confirm("해당 근로자를 삭제하시겠습니까?")){
        location.href = "/"+_CONTROLLER+"/deleteWorker?idx="+idx
    }else{
        return false;
    }
}

