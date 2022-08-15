$(document).ready(function(){

    // datatable {{{
    var dt_option = {
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        //"dom": 'lt<"float-left"i>p',
        //"dom": 'ltip',
        "exportFilename": "KFO_장애인취업포털_채용목록",
        "exportTitle": "전체 채용 목록",
        "ajax": {
             url: "/"+_CONTROLLER+"/getList"
            ,type: "POST"
            ,data: function(data){
                data.columns[1].search.value = $("#search-title").val();
                data.columns[2].search.value = $("#search-recommended").val();
                data.columns[3].search.value = $("#search-progress").val();
                data.columns[4].search.value = $("#search-profession").val();
                data.columns[5].search.value = $("#search-employment-type").val();
                data.columns[6].search.value = $("#search-career").val();
                data.columns[10].search.value = $("#search-daterange").val();

                /*
                data.columns[2].search.value = $("#search-elements").val();
                data.columns[4].search.value = $("#search-text").val();
                data.columns[12].search.value = $("#search-lot1").val();
                data.columns[13].search.value = $("#search-lot2").val();
                data.columns[14].search.value = $("#search-lot3").val();
                */
            }
        },
        "order": [
             [0, "desc"]
        ],
        "columns": [
             {title: "idx", data: "idx", visible: false}
            ,{title: "제목", data: "title", visible: true, className: "text-nowrap"}
            ,{title: "추천여부", data: "recommended", orderable: false, visible: true, className: "text-center text-nowrap",
                "render": function( data, type, row, meta ){
                    if(data == null){
                        return "<button class='btn btn-sm btn-outline-primary action-disable-recommended' data-uuid='"+row.uuid+"'>추천 등록</button>";
                    }
                    else {
                        return "<button class='btn btn-sm btn-primary px-3 action-enable-recommended' data-uuid='"+row.uuid+"'>추천중</button>";
                    }
                }
            }
            ,{title: "공개여부", data: "status", orderable: false, visible: true, className: "text-center text-nowrap",
                "render": function( data, type, row, meta ){
                    switch(data){
                        case "1": return "<span class='badge text-primary'>공개됨</span>"; break;
                        case "3": return "<span class='badge text-dark'>비공개</span>"; break;
                        case "7": return "<span class='badge text-danger'>공개거절</span>"; break;
                    }
                }
            }
            ,{title: "진행여부", data: "progress", orderable: false, visible: true, className: "text-center text-nowrap",
                "render": function( data, type, row, meta ){
                    if(data == 1){
                        if(row.status == 1){
                            return "<span class='badge bg-success'>채용중</span>";
                        }
                        else {
                            return "<span class='badge bg-dark'>비공개</span>";
                        }
                    }
                    else {
                        return "<span class='badge bg-dark'>마감됨</span>";
                    }
                }
            }
            ,{title: "직무", data: "profession", visible: false}
            ,{title: "고용형태", data: "employment_type", visible: false}
            ,{title: "경력", data: "career", visible: false}
            ,{title: "직무", data: "profession_title", orderable: false, visible: true, className: "text-nowrap"}
            ,{title: "고용형태", data: "employment_type_title", orderable: false, visible: true, className: "text-nowrap"}
            ,{title: "필요경력", data: "career_title", orderable: false, visible: true, className: "text-nowrap"}
            ,{title: "접수", data: "receipt_count", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    return data + "건";
                }
            }
            ,{title: "등록일시", data: "register_date", visible: true, className: "text-nowrap"}
            ,{title: "마감일", data: "receipt_expire_date", visible: true, className: "text-nowrap"}
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
        "drawCallback": function( settings ) {
            // 추천 해제
            $("button.action-enable-recommended").click(function(){
                const uuid = $(this).attr("data-uuid");
                $.disableRecommended(uuid);
            });
            // 추천 등록
            $("button.action-disable-recommended").click(function(){
                const uuid = $(this).attr("data-uuid");
                $.enableRecommended(uuid);
            });
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


$.extend({
    "enableRecommended": function(uuid)
    { //{{{
        $.ajax({
             url : "/" + _CONTROLLER + "/RecommendSubmit/enable/" + uuid
            ,dataType:"json"
            ,type: "GET"
            ,success: function(res){
                dt.ajax.reload(false, null);
            }
            ,error: function(res){
                console.log("error :", res);
            }
        });
    } //}}}

    ,"disableRecommended": function(uuid)
    { //{{{
        $.ajax({
             url : "/" + _CONTROLLER + "/RecommendSubmit/disable/" + uuid
            ,dataType:"json"
            ,type: "GET"
            ,success: function(res){
                dt.ajax.reload(false, null);
            }
            ,error: function(res){
                console.log("error :", res);
            }
        });

    } //}}}
});

