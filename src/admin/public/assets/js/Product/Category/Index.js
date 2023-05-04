$(document).ready(function(){

    // datatable {{{
    var dt_option = {
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        //"dom": 'lt<"float-left"i>p',
        //"dom": 'ltip',
        "exportFilename": "카테고리",
        "exportTitle": "카테고리",
        "ajax": {
            url: "/"+_CONTROLLER+"/getList"
            ,type: "POST"
            ,data: function(data){
                data.columns[1].search.value = $("#search-category_type1").val();
                data.columns[2].search.value = $("#search-category_type2").val();
                data.columns[3].search.value = $("#search-category_type3").val();
            }
        },
        "order": [
            [0, "desc"]
        ],
        "columns": [
            {title: "idx", data: "idx", visible: false}
            ,{title: "대분류", data: "category_type1", visible: true, className: "text-nowrap"}
            ,{title: "중분류", data: "category_type2", visible: true, className: "text-nowrap"}
            ,{title: "소분류", data: "category_type3", visible: true}
            ,{title: "사용여부", data: "del_yn", visible: true,
                "render": function( data, type, row, meta ){
                    let html = "";

                    switch(data){
                        case "N":
                            html = "<span class='badge bg-info'>사용</span>";
                            break;
                        case "Y":
                            html = "<span class='badge bg-danger'>삭제</span>";
                            break;
                    }

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


});
function CategoryRegister(){
    location.href = "/"+_CONTROLLER+"/CategoryRegister";
}