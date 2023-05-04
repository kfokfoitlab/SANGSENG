$(document).ready(function(){

    // datatable {{{
    var dt_option = {
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        //"dom": 'lt<"float-left"i>p',
        //"dom": 'ltip',
        "exportFilename": "상품목록",
        "exportTitle": "상품목록",
        "ajax": {
             url: "/"+_CONTROLLER+"/getList"
            ,type: "POST"
            ,data: function(data){
                data.columns[1].search.value = $("#search-company_name").val();
                data.columns[2].search.value = $("#search-product_no").val();
                data.columns[4].search.value = $("#search-product_category").val();
                data.columns[9].search.value = $("#search-status").val();
            }
        },
        "order": [
             [0, "desc"]
        ],
        "columns": [
             {title: "idx", data: "idx", visible: false}
            ,{title: "회사명", data: "company_name", visible: true, className: "text-nowrap"}
            ,{title: "상품번호", data: "product_no", visible: true, className: "text-nowrap"}
            ,{title: "상품명", data: "product_name", visible: true}
            ,{title: "카테고리(대분류)", data: "product_category", visible: true}
            ,{title: "카테고리(중분류)", data: "product_category2", visible: true}
            ,{title: "가격", data: "product_price", visible: true,
                "render": function( data, type, row, meta ) {
                    var html = "";
                    html += "<td";
                    html += "   class='nowrap'>";
                    html +=  data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    html += "원</td>";
                    return html;
                }
            }
            ,{title: "수량", data: "product_quantity", visible: true, className: "text-nowrap"}
            ,{title: "등록일", data: "register_date", visible: true,
                "render": function( data, type, row, meta ) {
                    var html = "";
                    html += "<td";
                    html += "   class='nowrap'>";
                    html +=  data.substring(0,10);
                    html += "</td>";
                    return html;
                }
            }
            ,{title: "수정일", data: "update_date", visible: true}
            ,{title: "상태", data: "status", visible: true,
                "render": function( data, type, row, meta ){
                    let html = "";

                    switch(data){
                        case "0":
                        case "1":
                            html = "<span class='badge bg-info'>승인대기</span>";
                            break;
                        case "3":
                            html = "<span class='badge bg-info'>수정 요청</span>";
                            break;
                        case "5":
                            html = "<span class='badge bg-success'>승인</span>";
                            break;
                        case "7":
                            html = "<span class='badge bg-danger'>반려</span>";
                            break;
                        case "8":
                            html = "<span class='badge bg-warning'>삭제요청</span>";
                            break;
                        case "9":
                            html = "<span class='badge bg-dark'>삭제</span>";
                            break;
                    }

                    return html;
                }
            }
            ,{title: "상태변경", data: {"idx":"idx","status_comment":"status_comment"}, visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
                    html += "<input class='btn btn-info btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate("+data['idx']+",1)' value='승인대기'>";
                    html += "<input class='btn btn-success btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate("+data['idx']+",5)' value='승인'>";
                   html += "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='window.open(\"/Product/Lists/StatusComment?idx="+data['idx']+"&status_comment="+data['status_comment']+"\", \"_blank\", \"width=500,height=330,toolbars=no,scrollbars=no\"); return false;' value='반려'>";
                    html += "<input class='btn btn-dark btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data['idx']+",9)' value='삭제'>";

                    return html;
                }
            }
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
        "drawCallback": function( settings ) {
        }
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