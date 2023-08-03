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
				data.columns[1].search.value = $("#search-company-name").val();
				data.columns[2].search.value = $("#search-worker-name").val();
				data.columns[6].search.value = $("#search-working_status").val();
				data.columns[7].search.value = $("#search-disability_degree").val();
				data.columns[8].search.value = $("#search-status").val();
			}
		},
		"order": [
			[0, "desc"]
		],
		"columns": [
			{title: "idx", data: "idx", visible: false}
			,{title: "회사이름", data: "company_name", visible: true, className: "text-nowrap"}
			,{title: "근로자", data: "worker_name", visible: true, className: "text-nowrap"}
			,{title: "생년월일", data: "worker_birth", visible: true, className: "text-nowrap"}
			,{title: "계약시작일", data: "worker_term_start", visible: true, className: "text-nowrap"}
			,{title: "계약종료일", data: "worker_term_end", visible: true, className: "text-nowrap"}
			,{title: "근무상태", data: "working_status", visible: true, className: "text-nowrap",
				"render": function( data, type, row, meta ){
					let html = "";
					
					switch(data){
						case "1":
							html = "<span class='text-success'>근무</span>";
							break;
						case "2":
							html = "<span class='text-danger'>퇴직</span>";
							break;
						case "3":
							html = "<span class='text-info'>휴직</span>";
							break;
					}
					
					return html;
				}}
			,{title: "장애유형", data: "disability_degree", visible: true, className: "text-nowrap",
				"render": function( data, type, row, meta ){
					let html = "";
					
					switch(data){
						case "1":
							html = "<span class='text-danger'>중증</span>";
							break;
						case "2":
							html = "<span class='text-warning'>경증</span>";
							break;
					}
					
					return html;
				}}
			,{title: "진행상황", data: "status", visible: true, className: "text-nowrap",
				"render": function( data, type, row, meta ){
					let html = "";
					
					switch(data){
						case "0":
						case "1":
							html = "<span class='badge bg-info'>대기</span>";
							break;
						case "5":
							html = "<span class='badge bg-success'>승인</span>";
							break;
						case "7":
							html = "<span class='badge bg-danger'>반려</span>";
							break;
					}
					
					return html;
				}}
			
			,{title: "진행상황변경", data: "idx", visible: true, className: "text-nowrap",
				"render": function( data, type, row, meta ){
					let html = "";
					html = "<input class='btn btn-info btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate("+data+",1)' value='대기'>";
					html = html + "<input class='btn btn-success btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",5)' value='승인'>";
					html = html + "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data+",7)' value='반려'>";
					
					return html;
				}
			}
			,{title: "상세보기", data: {"idx":"idx","search_type":"search_type"},visible: true, className: "text-nowrap",
				"render": function( data, type, row, meta ,data1){
					let html = "";
					html += "<a";
					html += "   class='button btn-secondary btn-sm m-1'";
					html += "   href='/"+_CONTROLLER+"/Detail?idx="+data['idx']+"' target='_self'>";
					html += "상세보기";
					html += "</a>";
					return html;
				}
			}
			,{title: "등록일시", data: "register_date", visible: true}
			, {title: "삭제", data: "idx", visible: true,
				"render": function (data, type, row, meta, data1) {
					let html = "";
					html = "<button class='btn btn-outline-danger btn-sm m-1' onClick='deleteWorker("+data+")'>삭제</button>";
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
	//console.log(dt_option);
	/*
	$("#datatables tbody").on("dblclick", "tr", function()
	{ // {{{
			$(this).find("a").get(0).click();
	}); // }}}
	*/
	
	
	// }}}

});
function statusUpdate(idx,status){
	var page_num_class = document.getElementsByClassName('paginate_button page-item active');
	var page_num = page_num_class[0].firstChild.dataset.dtIdx;
	alert(page_num);
	var Data = {
		"idx":idx,
		"status":status
	}
	$.ajax({
		method: 'POST',
		url : "/" + _CONTROLLER + "/statusUpdate",
		data: Data,
		contentType: "json",
		success: function (result) {
			if (result != "") {

				alert("변경되었습니다.");
				//location.reload();
			} else {
				alert("변경불가");
			}
		},
		error: function () {
			alert("오류가 발생했습니다. 관리자에게 문의해주세요");
		}
	});
	//location.href = "/"+_CONTROLLER+"/statusUpdate?idx="+idx+"&status="+status;
}
function deleteWorker(idx){
	if(confirm("해당 근로자를 삭제하시겠습니까?")){
		location.href = "/"+_CONTROLLER+"/deleteWorker?idx="+idx
	}else{
		return false;
	}
}

