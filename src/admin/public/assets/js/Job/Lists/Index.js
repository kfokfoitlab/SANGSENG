$(document).ready(function(){

    // datatable {{{
    var dt_option = {
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        //"dom": 'lt<"float-left"i>p',
        //"dom": 'ltip',
        "exportFilename": "KFO_장애인취업포털_구직목록",
        "exportTitle": "전체 구직 목록",
        "ajax": {
             url: "/"+_CONTROLLER+"/getList"
            ,type: "POST"
            ,data: function(data){
                data.columns[1].search.value = $("#search-title").val();
                data.columns[2].search.value = $("#search-user-name").val();
                data.columns[6].search.value = $("#search-daterange").val();

            }
        },
        "order": [
             [0, "desc"]
        ],
        "columns": [
             {title: "idx", data: "idx", visible: false}
            ,{title: "회사", data: "title", visible: true, className: "text-nowrap"}
            ,{title: "상품번호", data: "user_name", visible: true, className: "text-nowrap"}
            ,{title: "상품명", data: "resume_title", visible: true}
            ,{title: "카테고리", data: "view_count", visible: true}
            ,{title: "가격", data: "favorites", visible: true}
            ,{title: "수량", data: "register_date", visible: true, className: "text-nowrap"}
            ,{title: "판매기간", data: "title", visible: true, className: "text-nowrap"}
            ,{title: "부가세", data: "user_name", visible: true, className: "text-nowrap"}
            ,{title: "등록일", data: "resume_title", visible: true}
            ,{title: "수정일", data: "view_count", visible: true}
            ,{title: "상태", data: "favorites", visible: true,
                "render": function( data, type, row, meta ){
                    let html = "";

                    switch(data){
                        case "0":
                        case "1":
                            html = "<span class='badge bg-info'>승인대기</span>";
                            break;
                        case "5":
                            html = "<span class='badge bg-success'>승인</span>";
                            break;
                        case "7":
                            html = "<span class='badge bg-danger'>반려</span>";
                            break;
                    }

                    return html;
                }
            }
            ,{title: "상태변경", data: "register_date", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
                    html += "<input class='btn btn-info btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate("+data+",0)' value='승인대기'>";
                    html += "<input class='btn btn-success btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate("+data+",1)' value='승인'>";
                    html += "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",7)' value='거절'>";

                    return html;
                }
            }
            ,{title: "추천 순위", data: "idx", visible: true, className: "text-nowrap",
                "render": function(data, type, row, meta) {
                    let html = "";
                    html += "<select class='form-select form-select-sm' name='product_ranking'>";
                    html += "<option value='9999'>선택안함</option>";
                    html += "<option value='1'>1</option>";
                    html += "<option value='2'>2</option>";
                    html += "<option value='3'>3</option>";
                    html += "<option value='4'>4</option>";
                    html += "<option value='5'>5</option>";
                    html += "<option value='6'>6</option>";
                    html += "<option value='7'>7</option>";
                    html += "<option value='8'>8</option>";
                    html += "<option value='9'>9</option>";
                    html += "<option value='10'>10</option>";
                    html += "</select>";
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

