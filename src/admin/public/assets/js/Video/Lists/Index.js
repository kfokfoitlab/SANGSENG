$(document).ready(function () {
	
	// datatable {{{
	var dt_option = {
		"searching": false,
		"processing": true,
		"serverSide": true,
		"autoWidth": false,
		//"dom": 'lt<"float-left"i>p',
		//"dom": 'ltip',
		"exportFilename": "홍보영상관리",
		"exportTitle": "전체 목록",
		"ajax": {
			url: "/" + _CONTROLLER + "/getList"
			, type: "POST"
			, data: function (data) {
				data.columns[1].search.value = $("#search-video-title").val();
				data.columns[6].search.value = $("#search-status").val();
			}
		},
		"order": [
			[0, "desc"]
		],
		"columns": [
			{title: "idx", data: "idx", visible: false}
			, {title: "제목", data: "video_title", visible: true, className: "text-nowrap"}
			, {title: "메인영상", data: "main_video_ori", visible: true, className: "text-nowrap"}
			, {title: "서브영상1", data: "sub_video1_ori", visible: true, className: "text-nowrap"}
			, {title: "서브영상2", data: "sub_video2_ori", visible: true, className: "text-nowrap"}
			,{title: "등록일", data: "register_date", visible: true,
				"render": function( data, type, row, meta ) {
					var html = "";
					html += "<td";
					html += "   class='text-nowrap'>";
					html +=  data.substring(0,10);
					html += "</td>";
					return html;
				}
			}
			, {
				title: "사용여부", data: "video_status", orderable: false, visible: true, className: "text-center text-nowrap",
				"render": function (data, type, row, meta) {
					switch (data) {
						case "1":
							return "<span class='badge text-primary'>신규영상</span>";
							break;
						case "2":
							return "<span class='badge text-dark'>사용중</span>";
							break;
						case "3":
							return "<span class='badge text-dark'>미사용</span>";
							break;
						case "9":
							return "<span class='badge text-dark'>삭제</span>";
							break;
					}
				}
			}
			, {
				title: "사용여부 변경",
				data: "idx",
				visible: true,
				className: "text-nowrap",
				"render": function (data, type, row, meta) {
					let html = "";
					html += "<input class='btn btn-info btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate(" + data + ",2)' value='사용'>";
					html += "<input class='btn btn-success btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate(" + data + ",3)' value='미사용'>";
					
					return html;
				}
			}
			, {
				title: "삭제", data: "idx", visible: true,
				"render": function (data, type, row, meta, data1) {
					let html = "";
					html = "<button class='btn btn-outline-danger btn-sm m-1' onClick='statusUpdate(" + data + ",9)'>삭제</button>";
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
