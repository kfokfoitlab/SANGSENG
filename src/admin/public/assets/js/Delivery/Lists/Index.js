$(document).ready(function(){
	
	// datatable {{{
	var dt_option = {
		"searching": false,
		"processing": true,
		"serverSide": true,
		"autoWidth": false,
		//"dom": 'lt<"float-left"i>p',
		//"dom": 'ltip',
		"exportFilename": "상생몰_배송현황",
		"exportTitle": "배송현황 목록",
		"ajax": {
			url: "/"+_CONTROLLER+"/getList"
			,type: "POST"
			,data: function(data){
				data.columns[3].search.value = $("#contract_no").val();
				data.columns[2].search.value = $("#buyer_company").val();
				data.columns[1].search.value = $("#seller_company").val();
				data.columns[7].search.value = $("#search-disability_degree").val();
				//data.columns[8].search.value = $("#search-status").val();
			}
		},
		"order": [
			[0, "desc"]
		],
		"columns": [
			{title: "idx", data: "idx", visible: false}
			,{title: "판매자", data: "seller_company", visible: true, className: "text-nowrap"}
			,{title: "구매자", data: "buyer_company", visible: true, className: "text-nowrap"}
			,{title: "계약번호", data: "contract_no", visible: true, className: "text-nowrap"}
			,{title: "상품명", data: "product_name", visible: true, className: "text-nowrap"}
			,{title: "계약금액", data: "product_price", visible: true, className: "text-nowrap"}
			,{title: "계약등록일", data: "register_date", visible: true, className: "text-nowrap"}
			,{title: "배송 수(건)", data: "delivery_total_cnt", visible: true, className: "text-nowrap"}
			,{title: "배송 대기(건)", data: "delivery_wait_cnt", visible: true, className: "text-nowrap"}
			,{title: "배송 중(건)", data: "delivery_ready_cnt", visible: true, className: "text-nowrap"}
			,{title: "배송 완료(건)", data: "delivery_end_cnt", visible: true, className: "text-nowrap"}
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
function deleteWorker(idx){
	if(confirm("해당 근로자를 삭제하시겠습니까?")){
		location.href = "/"+_CONTROLLER+"/deleteWorker?idx="+idx
	}else{
		return false;
	}
}

