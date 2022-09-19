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
                        case "9":
                            html = "<span  id ='check' class='badge bg-dark'>계약취소</span>";
                            break;
                    }
/*
                    html = "<span class='badge bg-info'>승인대기</span>";

                            html = "<button onclick='statusCheck(\""+uuid+"\");' class='badge bg-primary'>진행중</button>";

                            html = "<button class='badge bg-success'>계약완료</button>";

                            html = "<span class='badge bg-danger'>반려</span>";

                            html = "<span class='badge bg-dark'>계약취소</span>";*/


                    return html;
                }
            }
            ,{title: "진행상황변경", data: {"idx":"idx","buyer_email":"buyer_email","seller_email":"seller_email","uuid":"uuid","buyer_name":"buyer_name","seller_name":"seller_name","buyer_uuid":"buyer_uuid","seller_uuid":"seller_uuid","buyer_company":"buyer_company","seller_company":"seller_company"}, visible: true, className: "text-nowrap",
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
                    let html = "";
                    html += "<input class='btn btn-info btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='statusUpdate("+data["idx"]+",1)' value='승인대기'>";
                    html += "<input class='btn btn-primary btn-sm m-1' style='font-size: 12px;color: white' type='button' onClick='contract_email("+data['idx']+",2,\""+buyer_email+"\",\""+seller_email+"\",\""+uuid+"\",\""+buyer_name+"\",\""+seller_name+"\",\""+seller_uuid+"\",\""+buyer_uuid+"\",\""+buyer_company+"\",\""+seller_company+"\")' value='진행'>";
                    html += "<input class='btn btn-success btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data["idx"]+",5)' value='계약완료'>";
                    html += "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data["idx"]+",7)' value='반려'>";
                    html += "<input class='btn btn-danger btn-sm m-1' style='font-size: 12px;' type='button' onClick='statusUpdate("+data["idx"]+",9)' value='계약취소'>";

                    return html;
                }
            }
            ,{title: "계약상태 보기", data: "uuid", visible: true, orderable: false, className: "text-center noExport",
                "render": function( data, type, row, meta ){
                    var html = "";
                    html = "<button onclick='statusCheck(\""+data+"\");'class='badge bg-primary'>상태보기</button>";

               /*     html += "<a";
                    html += "   class='btn btn-sm btn-outline-primary text-nowrap'";
                    html += "   href='/"+_CONTROLLER+"/Detail/"+data+"' target='detail'>";
                    html += "   상세보기";
                    html += "</a>";*/

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
function contract_email(idx,status,buyer_email,seller_email,uuid,buyer_name,seller_name,seller_uuid,buyer_uuid,buyer_company,seller_company){
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
            field_list: [{name: 'buyer_uuid', value: buyer_uuid}, {name: 'seller_uuid', value: seller_uuid}],
            workflow_name: buyer_company +" 기업과" + seller_company + " 기업의 계약서" ,
            template_id: 6,
        })
    };
// workflowid response로 받아서 계약서 insert할때 넣기(key값)
     fetch('https://docs.esignon.net/api/v3/workflows/start?offset=%2B09%3A00', options)
        .then(response => response.json())
        .then(response => location.href = "/"+_CONTROLLER+"/contractSubmit?idx="+idx+"&status="+status+"&workflow_id="+response["workflow_id"])
         .then(response => console.log(response))
        .catch(err => console.error(err));

    /* let text = response.text();
    alert(text);*/

 //   location.href = "/"+_CONTROLLER+"/statusUpdate?idx="+idx+"&status="+status;
}

async function statusCheck(uuid) {
    var result ="";
    const options = {
        method: 'GET',
        headers: {
            Accept: 'application/json',
            Authorization: 'esignon jlxfF8HAeRw1/8iUN5OVSH+060OTnZ+j7vRJdTHLFVSMzuM3n4MCaavEg6S0rFMpVNTkFsgGBWJ2usJ1j9T8uni3QARD+1L1cLc7W+PJ/M9dMoyAruRZ1C3NQusJ88gQ0utugU+hNRE='
        }
    };
    await fetch('https://docs.esignon.net/api/v3/workflows/search-with-value?offset=%2B09%3A00&template_id=6&field_name=uuid&field_value=' + uuid + '', options)
        .then(response => response.json())
       /* .then(response => response['workflow_list'][0]['status'])*/
/*        .then(response => console.log(response))*/
        .then(response => {
            console.log(response);
            var data = response['workflow_list'][0]['status']
            console.log(data);
            if(data =="Playing"){
                $("#check").html("진행중입니다")
            }else if(data === "Complate"){
                $("#check").html("dddd")
            }else{
                $("#check").html("aaaaa")
            }
        })
        .catch(err => console.error(err));
}

const options = {
    method: 'GET',
    headers: {
        Accept: 'application/json',
        Authorization: 'esignon jlxfF8HAeRw1/8iUN5OVSH+060OTnZ+j7vRJdTHLFVSMzuM3n4MCaavEg6S0rFMpVNTkFsgGBWJ2usJ1j9T8uni3QARD+1L1cLc7W+PJ/M9dMoyAruRZ1C3NQusJ88gQ0utugU+hNRE='
    }
};

fetch('https://docs.esignon.net/api/v3/workflows?offset=%2B09%3A00&page=1&date_condition=created_date&step=progress&imminent=false&important=false&template_id=6', options)
    .then(response => response.json())
   // .then(response => console.log(response['workflow_list'].length))
    .then(response =>{
        var workflow_id = "";
        for(var i =0; i< response['workflow_list'].length; i++){
            workflow_id = response['workflow_list'][i]['workflow_id'];
            console.log(workflow_id);
           // location.href = "/"+_CONTROLLER+"/statusUpdate?workflow_id="+workflow_id;
        }

    })
    .catch(err => console.error(err));