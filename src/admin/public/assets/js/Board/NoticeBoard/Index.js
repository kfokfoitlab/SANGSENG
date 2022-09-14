$(document).ready(function(){

    // datatable {{{
    var dt_option = {
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        //"dom": 'lt<"float-left"i>p',
        //"dom": 'ltip',
        "exportFilename": "KFO_장애인취업포털_게시판관리",
        "exportTitle": "공지사항",
        "ajax": {
             url: "/"+_CONTROLLER+"/getList"
            ,type: "POST"
            ,data: function(data){
                data.columns[1].search.value = $("#search-title").val();
                data.columns[2].search.value = $("#search-id").val();
                data.columns[3].search.value = $("#search-content").val();
            }
        },
        "order": [
             [0, "desc"]
        ],
        "columns": [
             {title: "idx", data: "idx", visible: false}
            ,{title: "제목", data: "title", visible: true, className: "text-nowrap w-50 text-center"}
						,{title: "작성자", data: "user_id", visible: true, className: "text-nowrap"}
						,{title: "내용", data: "content", visible: false, className: "text-nowrap"}
				  	,{title: "등록일", data: "register_date", visible: true, className: "text-nowrap"}
					  ,{title: "수정일", data: "update_date", visible: true, className: "text-nowrap"}
						,{title: "사용여부", data: "board_status", visible: true, className: "text-nowrap",
							"render": function( data, type, row, meta ){
								let html = "";
				
								switch(data){
									case "1":
										html = "<span class='badge bg-secondary'>사용</span>";
										break;
									case "2":
										html = "<span class='badge bg-danger'>미사용</span>";
										break;
								}
				
								return html;
							}}
            
            ,{title: "사용여부변경", data: "idx", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
										html = "<input class='button btn-secondary btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate("+data+",1)' value='사용'>";
										html = html + "<input class='button btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",2)' value='미사용'>";
                    return html;
                }
            }
						,{title: "삭제", data: "idx", visible: true, className: "text-nowrap",
						"render": function( data, type, row, meta ){
							let html = "";
							html = html + "<input class='btn btn-outline-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",7)' value='삭제'>";
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
function noticeRegister(){
	location.href = "/"+_CONTROLLER+"/noticeRegister?";
}

