$(document).ready(function(){
    // draggable
    $( ".sortable" ).sortable({
         placeholder: "ui-state-highlight"
        ,items: "tr:not(.new)"
        ,handle: ".handle"
    });


    // 저장하기
    $("button.action-save-submit").click(function(){
        if(confirm("저장하시겠습니까? 기존 데이터를 덮어 씌웁니다.")){
            $("form[name='form-item']").submit();
        }

        return false;




        const migration = $.save();
        const form = $("form[name='form-submit']");

        if(migration.result == "success"){
            if(confirm("저장하시겠습니까? 기존 데이터를 덮어 씌웁니다.")){
                form.find("input:hidden[name='json_data']").val(migration.json_data);
                form.submit();
            }
        }
    });
});


// 신규 타이틀 추가
$(document).on("submit", "form[name='add-title']", function()
{ //{{{
    const el = $(this);
    $.addTitle(el);
    return false;

}); //}}}

// 제목 삭제
$(document).on("click", "button.action-remove-title", function()
{ // {{{
    if(confirm('삭제할까요?')){
        $.removeTitle($(this));
    }
}); // }}}

// 항목 삭제
$(document).on("click", "button.action-remove-item", function()
{ // {{{
    $.removeItem($(this));
}); // }}}



$.extend({
    "addTitle": function(el)
    { //{{{

        const template = $("#template-title > table > tbody").children();
        const element = template.clone();
        $("#data-table .sortable").append(element);

        element.find("input[name='title[]']").val(el.find("input[name='new_title']").val());
        element.find("input[name='icon_html[]']").val(el.find("input[name='new_icon_html']").val());
        element.find("input[name='description[]']").val(el.find("input[name='new_description']").val());

        element.find("input[name='new_item']").focus();
        el.find("input[name='new_title']").val("");
        el.find("input[name='new_icon_html']").val("");
        el.find("input[name='new_description']").val("");

    } //}}}

    ,"removeTitle": function(el)
    { //{{{
        el.parents("tr").remove();
    } //}}}

    ,"removeItem": function(el)
    { //{{{
        el.parents("div.input-group").remove();
    } //}}}

    ,"save": function()
    { //{{{
        let data = new Array();
        let ok = 1;
        $.each($(".sortable tr.lists"), function(key, val){
            let title = $(val).find("input[name='title']").val();
            let icon_html = $(val).find("input[name='icon_html']").val();
            let description = $(val).find("input[name='description']").val();

            if(!title){
                alert("제목을 입력해 주세요.");
                $(val).find("input[name='title']").focus();
                ok = 0;
                return false;
            }

            let d = {title: title, icon_html: icon_html, description: description};
            data.push(d);

        });

        if(ok == 1){
            const json_data = JSON.stringify(data);
            return {result: "success", json_data: json_data};
        }
        else {
            return {result: "faild"};
        }

    } //}}}
});


