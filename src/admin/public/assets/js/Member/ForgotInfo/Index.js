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
                data.columns[1].search.value = $("#search-name").val();
                data.columns[2].search.value = $("#search-email").val();
                data.columns[6].search.value = $("#search-daterange").val();

                /*
                data.columns[1].search.value = $("#search-daterange").val();
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
						,{title: "타입", data: "search_type", visible: true, className: "text-nowrap",
						"render": function( data, type, row, meta ){
							let html = "";
			
							switch(data){
								case "0":
								case "1":
									html = "<span class='badge bg-warning'>ID찾기</span>";
									break;
								case "2":
									html = "<span class='badge bg-danger'>PW찾기</span>";
									break;
							}
			
							return html;
						}}
            ,{title: "아이디", data: "user_id", visible: true, className: "text-nowrap"}
            ,{title: "연락처", data: "user_phone", visible: true, className: "text-nowrap"}
            ,{title: "회사이름", data: "company_name", visible: true, className: "text-nowrap"}
            ,{title: "담당자", data: "manager_name", visible: true, className: "text-nowrap"}
						,{title: "진행상황", data: "status", visible: true, className: "text-nowrap",
							"render": function( data, type, row, meta ){
								let html = "";
				
								switch(data){
									case "0":
									case "1":
										html = "<span class='badge bg-info'>대기중</span>";
										break;
									case "5":
										html = "<span class='badge bg-primary'>처리완료</span>";
										break;
									case "7":
										html = "<span class='badge bg-danger'>처리거절</span>";
										break;
								}
				
								return html;
							}}
            
            ,{title: "진행상황변경", data: "idx", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
										html = "<input class='btn btn-secondary btn-sm' style='font-size: 12px;' type='button' onClick='#("+data+")' value='진행변경'>";
                    return html;
                }
            }
            ,{title: "등록일시", data: "register_date", visible: true}
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
