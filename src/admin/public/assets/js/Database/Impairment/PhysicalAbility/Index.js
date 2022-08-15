$(document).ready(function(){
    // draggable
    $( ".sortable" ).sortable({
         placeholder: "ui-state-highlight"
        ,items: "tr:not(.new)"
        ,handle: ".handle"
    });


    // 저장하기
    $("button.action-save-submit").click(function(){
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


// 신규 제목 추가
$(document).on("submit", "form[name='add-title']", function()
{ //{{{
    const el = $(this);
    $.addTitle(el);
    return false;

}); //}}}

// 신규 항목 추가
$(document).on("submit", "form[name='add-item']", function()
{ //{{{
    const el = $(this);
    $.addItem(el);
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

        element.find("input[name='code']").val(el.find("input[name='new_code']").val());
        element.find("input[name='title']").val(el.find("input[name='new_title']").val());

        element.find("input[name='new_item']").focus();
        el.find("input[name='new_code']").val("");
        el.find("input[name='new_title']").val("");

    } //}}}

    ,"addItem": function(el)
    { //{{{
        const template = $("#template-item").children();
        const element = template.clone();
              element.addClass("mb-1");
        el.before(element);

        let score = element.find("input[name='score']").attr("data-default");
            score = (el.find("input[name='new_score']").val())?el.find("input[name='new_score']").val():score;
        console.log(score);

        element.find("input[name='item']").val(el.find("input[name='new_item']").val());
        element.find("input[name='score']").val(score);

        el.find("input[name='new_score']").val("");
        el.find("input[name='new_item']").val("").focus();

        //element.find("input[name='new_item']").focus();
        //el.val("");

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
            let code = $(val).find("input[name='code']").val();
            let title = $(val).find("input[name='title']").val();

            if(!code){
                alert("코드번호를 입력해 주세요.");
                $(val).find("input[name='code']").focus();
                ok = 0;
                return false;
            }
            if(!title){
                alert("제목을 입력해 주세요.");
                $(val).find("input[name='title']").focus();
                ok = 0;
                return false;
            }

            if(!$(this).find("td.item > .input-group").length){
                alert("등록할 항목이 없습니다.");
                $(this).find("input[name='new_item']").focus();
                ok = 0;
                return false;
            }

            $.each($(this).find("td.item > .input-group"), function(key1, val1){
                let item = $(val1).find("input[name='item']").val();
                let score = $(val1).find("input[name='score']").val();

                if(!item){
                    alert("항목명을 입력해 주세요.");
                    $(val1).find("input[name='item']").focus();
                    ok = 0;
                    return false;
                }
                if(!score){
                    alert("중증도 점수를 입력해 주세요.");
                    $(val1).find("input[name='score']").focus();
                    ok = 0;
                    return false;
                }

                let d = {code: code, title: title, item: item, score: score};
                data.push(d);
            });

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
