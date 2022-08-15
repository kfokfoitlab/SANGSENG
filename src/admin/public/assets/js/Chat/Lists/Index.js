$(document).ready(function(){

    // datatable {{{
    var dt_option = {
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        //"dom": 'lt<"float-left"i>p',
        //"dom": 'ltip',
        "exportFilename": "KFO_장애인취업포털_채팅목록",
        "exportTitle": "전체 채팅 목록",
        "ajax": {
             url: "/"+_CONTROLLER+"/getList"
            ,type: "POST"
            ,data: function(data){
                data.columns[1].search.value = $("#search-user-name").val();
                data.columns[2].search.value = $("#search-company-name").val();
                data.columns[4].search.value = $("#search-daterange").val();
            }
        },
        "order": [
             [0, "desc"]
        ],
        "columns": [
             {title: "idx", data: "idx", visible: false}
            ,{title: "인재명", data: "user_name", visible: true, className: "text-nowrap"}
            ,{title: "기업명", data: "company_name", visible: true, className: "text-nowrap"}
            ,{title: "대화수", data: "chat_count", visible: true, className: "text-nowrap text-center", width: "72px",
                "render": function( data, type, row, meta ){
                    return data + "건";
                }
            }
            ,{title: "최근대화일시", data: "update_date", visible: true, className: "text-nowrap text-center", width: "156px"}
            ,{title: "등록일시", data: "register_date", visible: true, className: "text-nowrap text-center", width: "156px"}
            ,{title: "상세보기", data: "uuid", visible: true, orderable: false, className: "text-center noExport", width: "96px",
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

    // }}}

});

