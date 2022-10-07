$(document).ready(function () {
	
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
			url: "/" + _CONTROLLER + "/getList"
			, type: "POST"
			, data: function (data) {
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
			, {title: "제목", data: "title", visible: true, className: "text-nowrap"}
			, {title: "작성자", data: "title", visible: true, className: "text-nowrap"}
			, {title: "등록일", data: "title", visible: true, className: "text-nowrap"}
			, {title: "수정일", data: "title", visible: true, className: "text-nowrap"}
			, {
				title: "사용여부", data: "status", orderable: false, visible: true, className: "text-center text-nowrap",
				"render": function (data, type, row, meta) {
					switch (data) {
						case "1":
							return "<span class='badge text-primary'>사용</span>";
							break;
						case "3":
							return "<span class='badge text-dark'>미사용</span>";
							break;
					}
				}
			}
			, {
				title: "사용여부 변경",
				data: {"idx": "idx", "status_comment": "status_comment"},
				visible: true,
				className: "text-nowrap",
				"render": function (data, type, row, meta) {
					let html = "";
					html += "<input class='btn btn-info btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate(" + data['idx'] + ",1)' value='사용'>";
					html += "<input class='btn btn-success btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate(" + data['idx'] + ",5)' value='미사용'>";
					
					return html;
				}
			}
			, {title: "영상순서", data: "title", visible: true, className: "text-nowrap"}
			, {
				title: "삭제", data: "idx", visible: true,
				"render": function (data, type, row, meta, data1) {
					let html = "";
					html = "<button class='btn btn-outline-danger btn-sm m-1' onClick='deleteWorker(" + data + ")'>삭제</button>";
					return html;
				}
			}
		],
		"initComplete": function (settings, json) {
			$("form[name='form-searchbar'] .search").keypress(function (event) {
				if (event.keyCode == 13) {
					$("form[name='form-searchbar']").submit();
				}
			});
			
			$("form[name='form-searchbar']").submit(function (e) {
				e.preventDefault();
				dt.ajax.reload();
				return false;
			});
			
			$(".filter").change(function () {
				dt.ajax.reload();
			});
		},
		"drawCallback": function (settings) {
			// 추천 해제
			$("button.action-enable-recommended").click(function () {
				const uuid = $(this).attr("data-uuid");
				$.disableRecommended(uuid);
			});
			// 추천 등록
			$("button.action-disable-recommended").click(function () {
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
	"enableRecommended": function (uuid) { //{{{
		$.ajax({
			url: "/" + _CONTROLLER + "/RecommendSubmit/enable/" + uuid
			, dataType: "json"
			, type: "GET"
			, success: function (res) {
				dt.ajax.reload(false, null);
			}
			, error: function (res) {
				console.log("error :", res);
			}
		});
	} //}}}
	
	, "disableRecommended": function (uuid) { //{{{
		$.ajax({
			url: "/" + _CONTROLLER + "/RecommendSubmit/disable/" + uuid
			, dataType: "json"
			, type: "GET"
			, success: function (res) {
				dt.ajax.reload(false, null);
			}
			, error: function (res) {
				console.log("error :", res);
			}
		});
		
	} //}}}
});
