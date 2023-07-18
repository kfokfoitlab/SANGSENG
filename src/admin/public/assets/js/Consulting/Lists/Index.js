$(document).ready(function(){

    // datatable {{{
    var dt_option = {
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        //"dom": 'lt<"float-left"i>p',
        //"dom": 'ltip',
        "exportFilename": "KFO_장애인취업포털_기업목록",
        "ajax": {
            url: "/"+_CONTROLLER+"/getList"
            ,type: "POST"
            ,data: function(data){
                data.columns[1].search.value = $("#search-company").val();
                data.columns[2].search.value = $("#search-user-name").val();
                data.columns[5].search.value = $("#search-status").val();

            }
        },
        "order": [
            [0, "desc"]
        ],
        "columns": [
            {title: "idx", data: "idx", visible: false}
            ,{title: "기업명/담당부서", data: "company_name_department", visible: true, className: "text-nowrap"}
            ,{title: "담당자 성함", data: "user_name", visible: true, className: "text-nowrap"}
            ,{title: "담당자 연락처", data: "user_phone", visible: true, className: "text-nowrap"}
            ,{title: "이메일", data: "user_email", visible: true, className: "text-nowrap"}
            ,{title: "진행상황", data: "status", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";

                    switch(data){
                        case "1":
                            html = "<span class='badge bg-primary'>대기</span>";
                            break;
                        case "2":
                            html = "<span class='badge bg-warning'>보류</span>";
                            break;
                        case "3":
                            html = "<span class='badge bg-success'>승인</span>";
                            break;
                        case "9":
                            html = "<span class='badge bg-dark'>취소</span>";
                            break;
                    }

                    return html;
                }
            }
            ,{title: "진행상황변경", data: "idx", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
                    html += "<input class='btn btn-primary btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate("+data+",1)' value='대기'>";
                    html += "<input class='btn btn-warning btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",2)' value='보류'>";
                    html += "<input class='btn btn-success btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",3)' value='승인'>";
                    html += "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",9)' value='취소'>";

                    return html;
                }
            }
            ,{title: "등록일시", data: "register_date", visible: true}
            ,{title: "상세보기", data: "idx", visible: true, orderable: false, className: "text-center noExport",
                "render": function( data, type, row, meta ){
                    var html = "";
                    html += "<a";
                    html += "   class='btn btn-sm btn-outline-primary text-nowrap'";
                    html += "   href='/"+_CONTROLLER+"/Detail/"+data+"' target='detail'>";
                    html += "   상세보기";
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

