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
        "exportTitle": "구매기업 목록",
        "ajax": {
             url: "/"+_CONTROLLER+"/getList"
            ,type: "POST"
            ,data: function(data){
                data.columns[2].search.value = $("#search-company-name").val();
                data.columns[3].search.value = $("#search-company_code").val();
                data.columns[4].search.value = $("#search-seller_name").val();
                data.columns[6].search.value = $("#search-status").val();

            }
        },
        "order": [
             [0, "desc"]
        ],
        "columns": [
             {title: "idx", data: "idx", visible: false}
            ,{title: "아이디", data: "email", visible: true, className: "text-nowrap"}
            ,{title: "기업명", data: "company_name", visible: true, className: "text-nowrap"}
            ,{title: "사업자등록번호", data: "company_code", visible: true, className: "text-nowrap"}
            ,{title: "담당자", data: "seller_name", visible: true, className: "text-nowrap"}
            ,{title: "연락처", data: "phone", visible: true, className: "text-nowrap"}
            ,{title: "진행상황", data: "status", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";

                    switch(data){
                        case "0":
                            html = "<span class='badge bg-info'>가입신청</span>";
                            break;
                        case "1":
                            html = "<span class='badge bg-primary'>심사중</span>";
                            break;
                        case "5":
                            html = "<span class='badge bg-success'>승인</span>";
                            break;
                        case "7":
                            html = "<span class='badge bg-danger'>거절</span>";
                            break;
                        case "9":
                            html = "<span class='badge bg-danger'>탈퇴</span>";
                            break;
                    }

                    return html;
                }
            }
            ,{title: "진행상황변경", data: "idx", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
                    html += "<input class='btn btn-info btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate("+data+",1)' value='심사중'>";
                    html += "<input class='btn btn-success btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",5)' value='승인'>";
                    html += "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",7)' value='거절'>";
                    html += "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",9)' value='탈퇴'>";

                    return html;
                }
            }
            /*
            ,{title: "상태", data: "status", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";

                    switch(data){
                        case "0":
                        case "1":
                            html = "<span class='badge bg-info'>심사중</span>";
                            break;
                        case "5":
                            html = "<span class='badge bg-primary'>등록완료</span>";
                            break;
                        case "7":
                            html = "<span class='badge bg-danger'>등록거절</span>";
                            break;
                        case "9":
                            html = "<span class='badge bg-dark'>탈퇴</span>";
                            break;
                    }

                    return html;
                }
            }
            */
            ,{title: "등록일시", data: "register_date", visible: true}
            ,{title: "상세보기", data: "uuid", visible: true, orderable: false, className: "text-center noExport",
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
    console.log(`ids, status : ${idx, status}`)
}