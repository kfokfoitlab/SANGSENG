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
				data.columns[4].search.value = $("#search-title").val();
				data.columns[5].search.value = $("#search-id").val();
				data.columns[6].search.value = $("#search-content").val();
			}
		},
		"order": [
			[0, "desc"]
		],
		"columns": [
			{title: "idx", data: "idx", visible: false}
			,{title: "답변여부", data: "board_status", visible: true, className: "text-nowrap",
				"render": function( data, type, row, meta ){
					let html = "";
					
					switch(data){
						case "1":
							html = "<span class='badge bg-info'>답변대기</span>";
							break;
						case "2":
							html = "<span class='badge bg-success'>답변완료</span>";
							break;
					}
					
					return html;
				}}
			,{title: "제목", data: {"title":"title", "idx":"idx"}, visible: true, className: "text-nowrap w-50 text-center",
				"render": function( data, type, row, meta ){
					let html = "";
					html = "<a href='/"+_CONTROLLER+"/QuestionsDetail?idx="+data['idx']+"' class='w-50 text-center' style='font-size: 15px;font-weight: bold;color: black'>"+data['title']+"</a>"
					return html;
				}}
			,{title: "담당자", data: "manager_name", visible: true, className: "text-nowrap"}
			,{title: "제목", data: "title", visible: false, className: "text-nowrap"}
			,{title: "작성자", data: "user_email", visible: true, className: "text-nowrap"}
			,{title: "내용", data: "content", visible: false, className: "text-nowrap"}
			,{title: "회사명", data: "user_company_name", visible: true, className: "text-nowrap"}
			,{title: "핸드폰", data: "user_phone", visible: true, className: "text-nowrap"}
			,{title: "등록일", data: "register_date", visible: true, className: "text-nowrap"}
			,{title: "수정일", data: "update_date", visible: true, className: "text-nowrap"}
			,{title: "삭제", data: "idx", visible: true, className: "text-nowrap",
				"render": function( data, type, row, meta ){
					let html = "";
					html = html + "<input class='btn btn-outline-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='QuestionsDelete("+data+")' value='삭제'>";
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
function QuestionsDelete(idx){
	if(confirm("삭제하시겠습니까?")) {
		location.href = "/" + _CONTROLLER + "/Delete?idx=" + idx;
	}
}
function QuestionsDetail(){
	location.href = "/"+_CONTROLLER+"/QuestionsDetail";
}

