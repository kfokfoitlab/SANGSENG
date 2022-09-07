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
            {title: "No", data: "idx", visible: true}
            ,{title: "판매자", data: "seller_company", visible: true, className: "text-nowrap"}
            ,{title: "구매자", data: "buyer_company", visible: true, className: "text-nowrap"}
            ,{title: "계약번호", data: "contract_no", visible: true, className: "text-nowrap"}
            ,{title: "상품명", data: "product_name", visible: true, className: "text-nowrap"}
            ,{title: "계약금액", data: "product_price", visible: true, className: "text-nowrap"}
            ,{title: "계약등록일", data: "register_date", visible: true, className: "text-nowrap"}
            ,{title: "진행상황", data: "contract_status", visible: false, className: "text-nowrap"}
            ,{title: "진행상황 보기", data: {"contract_status":"contract_status","uuid":"uuid"}, visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    let html = "";
                    var uuid = data['uuid'];
                    var result = statusCheck();
                            html = "<span class='badge bg-info'>승인대기</span>";

                            html = "<button onclick='statusCheck(\""+uuid+"\");' class='badge bg-primary'>진행중</button>";

                            html = "<button class='badge bg-success'>계약완료</button>";

                            html = "<span class='badge bg-danger'>반려</span>";

                            html = "<span class='badge bg-dark'>계약취소</span>";


                    return html;
                }
            }
            ,{title: "진행상황변경", data: {"idx":"idx","buyer_email":"buyer_email","seller_email":"seller_email","uuid":"uuid","buyer_name":"buyer_name","seller_name":"seller_name"}, visible: true, className: "text-nowrap",
                "render": function( data, type, row, meta ){
                    var buyer_email = data['buyer_email'];
                    var seller_email = data['seller_email'];
                    var uuid = data['uuid'];
                    var buyer_name = data['buyer_name'];
                    var seller_name = data['seller_name'];
                    let html = "";
                    html += "<input class='btn btn-info btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate("+data["idx"]+",1)' value='승인대기'>";
                    html += "<input class='btn btn-primary btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='contract_email("+data['idx']+",2,\""+buyer_email+"\",\""+seller_email+"\",\""+uuid+"\",\""+buyer_name+"\",\""+seller_name+"\")' value='진행'>";
                    html += "<input class='btn btn-success btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data["idx"]+",5)' value='계약완료'>";
                    html += "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data["idx"]+",7)' value='반려'>";
                    html += "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data["idx"]+",9)' value='계약취소'>";

                    return html;
                }
            }
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
function contract_email(idx,status,buyer_email,seller_email,uuid,buyer_name,seller_name){
    const options = {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            Authorization: ' esignon jlxfF8HAeRw1/8iUN5OVSH+060OTnZ+j7vRJdTHLFVSMzuM3n4MCaavEg6S0rFMpVNTkFsgGBWJ2usJ1j9T8uni3QARD+1L1cLc7W+PJ/M9dMoyAruRZ1C3NQusJ88gQ0utugU+hNRE='
        },
        body: JSON.stringify({
            language: 'ko',
            is_preview: false,
            recipient_list: [
                {order: 1, email: buyer_email, name: buyer_name},
                {order: 2, email: seller_email, name: seller_name}
            ],
            field_list: [{name: 'uuid', value: uuid}],
            workflow_name: "계약서",
            template_id: 6,
        })
    };
// workflowid response로 받아서 계약서 insert할때 넣기(key값)
     fetch('https://docs.esignon.net/api/v3/workflows/start?offset=%2B09%3A00', options)
        .then(response => response.json())
        .then(response => location.href = "/"+_CONTROLLER+"/statusUpdate?idx="+idx+"&status="+status+"&workflow_id="+response["workflow_id"])
         .then(response => console.log(response))
        .catch(err => console.error(err));

    /* let text = response.text();
    alert(text);*/

 //   location.href = "/"+_CONTROLLER+"/statusUpdate?idx="+idx+"&status="+status;
}

async function statusCheck(uuid){
    var result ="";
    const options = {
        method: 'GET',
        headers: {
            Accept: 'application/json',
            Authorization: 'esignon jlxfF8HAeRw1/8iUN5OVSH+060OTnZ+j7vRJdTHLFVSMzuM3n4MCaavEg6S0rFMpVNTkFsgGBWJ2usJ1j9T8uni3QARD+1L1cLc7W+PJ/M9dMoyAruRZ1C3NQusJ88gQ0utugU+hNRE='
        }
    };
    const response =  await fetch('https://docs.esignon.net/api/v3/workflows/search-with-value?offset=%2B09%3A00&template_id=6&field_name=uuid&field_value='+uuid+'', options)
        .then(response => response.json())
        .then( function(response){
            result =response['workflow_list'][0]['status'];
            if(result == "Playing"){
                alert("진행중인 계약입니다");
            }else if(result == "Complete"){
                alert("완료된 계약입니다. 진행상황변경을 해주세요");
            }
            return result;
        })
        .catch(err => console.error(err));
    return response;
}

