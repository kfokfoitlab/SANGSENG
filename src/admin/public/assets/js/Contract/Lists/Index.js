$(document).ready(function(){
    // datatable {{{
    var dt_option = {
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        //"dom": 'lt<"float-left"i>p',
        //"dom": 'ltip',
        "exportFilename": "KFO_장애인취업포털_기업목록",
        "exportTitle": "계약 목록",
        "ajax": {
            url: "/"+_CONTROLLER+"/getList"
            ,type: "POST"
            ,data: function(data){
                data.columns[3].search.value = $("#search-contract_no").val();
                data.columns[2].search.value = $("#search-buyer_company").val();
                data.columns[1].search.value = $("#search-seller_company").val();
                data.columns[7].search.value = $("#search-contract_status").val();
            }
        },
        "order": [
            [0, "desc"]
        ],
        "columns": [
            {title: "No", data: "idx", visible: false}
            ,{title: "판매자", data: "seller_company", visible: true, className: "text-nowrap"}
            ,{title: "구매자", data: "buyer_company", visible: true, className: "text-nowrap"}
            ,{title: "계약번호", data: "contract_no", visible: true, className: "text-nowrap"}
            ,{title: "상품명", data: "product_name", visible: true, className: "text-nowrap"}
            ,{title: "계약금액", data: "product_price", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ) {
                    var html = "";
                    html += "<td";
                    html += "   class='nowrap'>";
                    html +=  data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    html += "원</td>";
                    return html;
                }
            }
            , {
                title: "계약등록일", data: "register_date", visible: true, className: "text-nowrap",
                "render": function (data, type, row, meta) {
                    var html = "";
                    html += "<td";
                    html += "   class='nowrap'>";
                    html += data.substring(0, 10);
                    html += "</td>";
                    return html;
                }
            }
            ,{title: "진행상황", data: "contract_status", visible: false, className: "text-nowrap"}
            ,{title: "진행상황 보기", data: {"contract_status":"contract_status","uuid":"uuid"}, visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
                    var uuid = data['uuid'];
                    switch(data['contract_status']){
                        case "0":
                        case "1":
                            html = "<span id ='check' class='badge bg-info'>승인대기</span>";
                            break;
                        case "2":
                            html = "<span id ='check' class='badge bg-primary'>진행중</span>";
                            break;
                        case "5":
                            html = "<span id ='check' class='badge bg-success'>계약완료</span>";
                            break;
                        case "7":
                            html = "<span id ='check' class='badge bg-danger'>반려</span>";
                            break;
                        case "8":
                            html = "<span  id ='check' class='badge bg-dark'>삭제</span>";
                            break;
                        case "9":
                            html = "<span  id ='check' class='badge bg-dark'>계약취소</span>";
                            break;
                    }
                    return html;
                }
            }
            ,{title: "계약서 전송", data: {"idx":"idx","buyer_email":"buyer_email","seller_email":"seller_email","uuid":"uuid","buyer_name":"buyer_name","seller_name":"seller_name","buyer_uuid":"buyer_uuid","seller_uuid":"seller_uuid","buyer_company":"buyer_company","seller_company":"seller_company",
						"contract_status":"contract_status","product_name":"product_name"}, visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    var buyer_email = data['buyer_email'];
                    var seller_email = data['seller_email'];
                    var uuid = data['uuid'];
                    var buyer_name = data['buyer_name'];
                    var seller_name = data['seller_name'];
                    var buyer_uuid = data['buyer_uuid'];
                    var buyer_company = data['buyer_company'];
                    var seller_company = data['seller_company'];
                    var seller_uuid = data['seller_uuid'];
                    var contract_status = data['contract_status'];
                    var product_name = data['product_name'];
                    let html = "";
                    if(contract_status == 1) {
                        html += "<input class='btn btn-warning btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='contract_email(" + data['idx'] + ",2,\"" + buyer_email + "\",\"" + seller_email + "\",\"" + uuid + "\",\"" + buyer_name + "\",\"" + seller_name + "\",\"" + seller_uuid + "\",\"" + buyer_uuid + "\",\"" + buyer_company + "\",\"" + seller_company + "\",\"" + product_name + "\")' value='계약서 전송'>";
                    }else if(contract_status == 8){
                        html += "<input class='btn btn-dark btn-sm m-1' style='font-size: 12px;color: white' type='button' value='삭제처리'>";
                    }else{
                        html += "<input class='btn btn-secondary btn-sm m-1' style='font-size: 12px;color: white' type='button' value='전송완료'>";
                    }
                      return html;
                    }
                }
            ,{title: "삭제", data: "idx", visible: true, className: "text-nowrap",
            "render": function( data, type, row, meta ){
                let html = "";
                    html += "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='contractDelete(" + data + ")' value='삭제'>";
                return html;
                }
            }
            ,{title: "계약서 조회", data: "workflow_id", visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
                    html += "<input type = 'button' class='btn btn-warning btn-sm m-2' style='font-size: 12px;color: white' onClick='contractView(" + data + ")' value='조회'>";
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
});

function statusUpdate(idx,status){
    location.href = "/"+_CONTROLLER+"/statusUpdate?idx="+idx+"&status="+status;
}

function contractDelete(idx){
	if(confirm("삭제하시겠습니까?")) {
		location.href = "/" + _CONTROLLER + "/ContractDelete?idx=" + idx;
	}else {
		return false;
	}
}


function contract_email(idx,status,buyer_email,seller_email,uuid,buyer_name,seller_name,seller_uuid,buyer_uuid,buyer_company,seller_company,product_name){
    const options = {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            Authorization: 'esignon mgjv4YvW5hJwZet8rc43lk6PzONAcr7q//EZ3V8MkNs5U8dE8qw4amjpNMtaCupRi645Rz2K9/T4N1ylBhHzFI2tn0C/h7VLu5CHAa75N+GtMtKi1qAovYx7PKgNI1PokB4o1mlEK5s='
        },
        body: JSON.stringify({
            language: 'ko',
            is_preview: false,
            recipient_list: [
                {order: 1, email: buyer_email, name: buyer_name},
                {order: 2, email: seller_email, name: seller_name},
            ],
            field_list: [{name: 'buyer_uuid', value: buyer_uuid}, {name: 'seller_uuid', value: seller_uuid},
							{name: 'updateType', value: 'all'},{name:'buyer_company',value: buyer_company},{name:'seller_company',value: seller_company},{name:'Contract_Name',value:product_name}],
            workflow_name: buyer_company +" 기업과 " + seller_company + " 기업의 계약서" ,
            template_id: 9
        })
    };
    fetch('https://docs.esignon.net/api/v3/workflows/start?offset=%2B09%3A00', options)
        .then(response => response.json())
        .then(response => location.href = "/"+_CONTROLLER+"/contractSubmit?idx="+idx+"&status="+status+"&workflow_id="+response["workflow_id"])
		//.then(response => console.log(response))
        .catch(err => console.error(err));

}
function Contract_reduction(workflow_id,pworkflow_id) {
    const options = {
        method: 'GET',
        headers: {
            accept: 'application/json',
            Authorization: 'esignon mgjv4YvW5hJwZet8rc43lk6PzONAcr7q//EZ3V8MkNs5U8dE8qw4amjpNMtaCupRi645Rz2K9/T4N1ylBhHzFI2tn0C/h7VLu5CHAa75N+GtMtKi1qAovYx7PKgNI1PokB4o1mlEK5s='
        }
    };
    var workflow_key = "";
    var contract_reduction = 0;
    var complete_reduction = 0;
    var workflow = "";
    var product_quantity = "";
    for (var i = 0; i < workflow_id.length; i++) {
        workflow_key = workflow_id[i];
        fetch('https://docs.esignon.net/api/v3/workflows/' + workflow_key + '?offset=%2B09%3A00', options)
            .then(response => response.json())
            .then(response => {

                $.each(response['field_list'], function (idx, row) {
                    if (response['field_list'][idx].name == "Contract_Cost") {
                        // console.log(response);
                        contract_reduction = response['field_list'][idx].value;
                        complete_reduction = parseInt(contract_reduction.replace(/,/g, ""));
                    }
                    if (response['field_list'][idx].name == "Contract_EA") {
                        product_quantity = response['field_list'][idx].value;
                    }
                })

                workflow = response['workflow_id'];
                $('#complete_reduction').val(complete_reduction);
                $('#product_quantity').val(product_quantity);
                $('#workflow_id').val(workflow);
                $('#pworkflow_id').val(pworkflow_id);
              //  $("#statusForm").submit();
            })
            .catch(err => console.error(err));
    }
}

function contract_update(field_name,field_value) {
	var field_name = field_name;
	var field_value = field_value;
	const options = {
		method: 'GET',
		headers: {
			accept: 'application/json',
			Authorization: 'esignon mgjv4YvW5hJwZet8rc43lk6PzONAcr7q//EZ3V8MkNs5U8dE8qw4amjpNMtaCupRi645Rz2K9/T4N1ylBhHzFI2tn0C/h7VLu5CHAa75N+GtMtKi1qAovYx7PKgNI1PokB4o1mlEK5s='
		}
	};

	fetch('https://docs.esignon.net/api/v3/workflows/search-with-value?offset=%2B09%3A00&template_id=9&field_name='+field_name+'&field_value='+field_value+'', options)
			.then(response => response.json())
			.then(response => {
                var workflow_id = [];
                var pworkflow_id = [];
                var j = 0;
                var k = 0;
                for(var i =0; i <response['workflow_list'].length; i++){
                    if(response['workflow_list'][i]['status'] == "Complete"){
                        workflow_id[j] = response['workflow_list'][i]['workflow_id'];
                        j++;
                    }
                    if(response['workflow_list'][i]['status'] == "Playing") {
                        pworkflow_id[k] = response['workflow_list'][i]['workflow_id'];
                        k++;
                    }
                }
                Contract_reduction(workflow_id,pworkflow_id);
			})
			.catch(err => console.error(err));
}

function contractView(workflow_id){
    if(workflow_id == "" || workflow_id == null){
        alert("아직 계약서가 전송되지 않았습니다.")
    }else {
        const options = {
            method: 'GET',
            headers: {
                accept: 'application/json',
                Authorization: 'esignon mgjv4YvW5hJwZet8rc43lk6PzONAcr7q//EZ3V8MkNs5U8dE8qw4amjpNMtaCupRi645Rz2K9/T4N1ylBhHzFI2tn0C/h7VLu5CHAa75N+GtMtKi1qAovYx7PKgNI1PokB4o1mlEK5s='
            }
        };
        fetch('https://docs.esignon.net/api/v3/workflows/' + workflow_id + '?offset=%2B09%3A00', options)
            .then(response => response.json())
            .then(response => /*console.log(response)*/ {
                if (response['download_url'] == "") {
                    window.open(response['playing_url']);
                } else {
                    window.open(response['download_url']);
                }
            })
            .catch(err => console.error(err));
    }
}
