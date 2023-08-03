var selected_level = new Array(0, 0);

$(document).ready(function(){

    // init impairment - level0
    $.draw_impairment(0);

    // draggable
    $( ".impairment-box .list-group" ).sortable({
         placeholder: "ui-state-highlight"
        ,update: function(event, ui){
            let this_level = ui.item[0].dataset.thisLevel;
            $.sorting(this_level);
        }
    });

    // add impairment
    $(".impairment-list input[name='title']").keydown(function(e){
        if(e.keyCode == 13){
             $(this).siblings(".action-add-impairment").click();
        }
    });
    $("button.action-add-impairment").click(function(){
        $.add_impairment(this);
    });

    // update impairment title
    $("button.action-update-impairment").click(function(){
        $.update_impairment(this);
    });

    // update maximum storage
    $("button.action-update-storage").click(function(){
        $.update_storage(this);
    });

    // delete impairment title
    $("button.action-delete-impairment").click(function(){
        $.delete_impairment(this);
    });

});


// click list-group
$(document).on("click", ".impairment-box .list-group-item", function(){
    $.click_list_group(this);
});


$.extend({
    "click_list_group": function(obj)
    { // {{{

        var idx = $(obj).attr("data-idx");
        var this_level = $(obj).attr("data-this-level");
            
        selected_level[this_level] = idx;
        selected_level[eval(this_level)+1] = 0;

        $.deactive_list_group(obj);
        $(obj).toggleClass("active");

        // 최상위 레벨이면 선택한 분류 disable하고 최하위 레벨 닫기
        if(this_level == 0){
            $.impairment_close(2);
            $.impairment_close(3);

            $("#impairment-level1 .card-footer .action-delete-impairment").addClass("disabled");
            $("#impairment-level1 .card-footer .action-update-impairment").addClass("disabled");
            $("#impairment-level1 .card-footer input[name='title']").val("").prop("disabled", true);

            $("#impairment-level2 .card-footer .action-delete-impairment").addClass("disabled");
            $("#impairment-level2 .card-footer .action-update-impairment").addClass("disabled");
            $("#impairment-level2 .card-footer input[name='title']").val("").prop("disabled", true);

            $("#impairment-level3 .card-footer .action-delete-impairment").addClass("disabled");
            $("#impairment-level3 .card-footer .action-update-impairment").addClass("disabled");
            $("#impairment-level3 .card-footer input[name='title']").val("").prop("disabled", true);
        }
        else if(this_level == 1){
            $.impairment_close(3);
            $("#impairment-level2 .card-footer .action-delete-impairment").addClass("disabled");
            $("#impairment-level2 .card-footer .action-update-impairment").addClass("disabled");
            $("#impairment-level2 .card-footer input[name='title']").val("").prop("disabled", true);
        }
        else if(this_level == 2){
            $("#impairment-level3 .card-footer .action-delete-impairment").addClass("disabled");
            $("#impairment-level3 .card-footer .action-update-impairment").addClass("disabled");
            $("#impairment-level3 .card-footer input[name='title']").val("").prop("disabled", true);
        }

        // 수정하기 enable
        let selected_title = $(obj).find("div.title").text();
        let maximum_storage = $(obj).attr("data-maximum-storage");
        $(obj).parents(".card").find(".card-footer .action-delete-impairment").removeClass("disabled");
        $(obj).parents(".card").find(".card-footer .action-update-impairment").removeClass("disabled");
        $(obj).parents(".card").find(".card-footer .action-update-storage").removeClass("disabled");
        $(obj).parents(".card").eq(0).find(".card-footer input[name='title']").prop("disabled", false).val(selected_title);
        $(obj).parents(".card").eq(0).find(".card-footer input[name='maximum_storage']").prop("disabled", false).val(maximum_storage);

        // 하위레벨 리스트 출력
        if(this_level < 3){
            let next_level = eval(this_level) + 1;
            $.draw_impairment(next_level, idx);
        }

    }, // }}}

    "deactive_list_group": function(obj)
    { // {{{
        let top_layer = $(obj).parents(".impairment-box");
        top_layer.find(".list-group-item").removeClass("active");
    }, // }}}
    
    "add_impairment": function(obj)
    { // {{{
        let top_layer = $(obj).parents(".impairment-box");
        let this_level = top_layer.attr("data-level");
        let new_title = top_layer.find("input[name='title']");

        if(!new_title.val()){
            new_title.focus();
            return false;
        }

        var json_data = {
             "this-level": this_level
            ,"new-title": new_title.val()
            ,"level0-idx": selected_level[0]
            ,"level1-idx": selected_level[1]
            ,"level2-idx": selected_level[2]
        };

        $.ajax({
             url: "/"+_CONTROLLER+"/Request/ADD"
            ,async: false
            ,data: json_data
            ,dataType: "json"
            ,type: "post"
            ,success: function(res){

                // 다시 그리기
                $.redraw_impairment(this_level);

                // 신규 생성한 데이터 자동 클릭
                var new_obj = $(".impairment-box .list-group-item").last();
                $.click_list_group(new_obj);

            }
            ,error: function(res){
                console.log("error", res);
            }
            ,complete: function(){
                new_title.val("");
            }
        });


    }, // }}}

    "update_impairment": function(obj)
    { // {{{
        let top_layer = $(obj).parents(".impairment-box");
        let this_level = top_layer.attr("data-level");
        let title = $(obj).siblings("input[name='title']");
        let idx = selected_level[this_level];

        if(!title.val()){
            title.focus();
            return false;
        }

        var json_data = {
             "idx": idx
            ,"title": title.val()
        };

        $.ajax({
             url: "/"+_CONTROLLER+"/Request/UPDATE"
            ,async: false
            ,data: json_data
            ,dataType: "json"
            ,type: "post"
            ,success: function(res){
                // 다시 그리기
                $.redraw_impairment(this_level);

            }
            ,error: function(res){
                console.log("error", res);
            }
        });

    }, // }}}

    "update_storage": function(obj)
    { // {{{
        let top_layer = $(obj).parents(".impairment-box");
        let this_level = top_layer.attr("data-level");
        let maximum_storage = $(obj).siblings("input[name='maximum_storage']");
        let idx = selected_level[this_level];

        if(!maximum_storage.val()){
            maximum_storage.focus();
            return false;
        }

        var json_data = {
             "idx": idx
            ,"maximum_storage": maximum_storage.val()
        };

        $.ajax({
             url: "/"+_CONTROLLER+"/Request/UPDATE_STORAGE"
            ,async: false
            ,data: json_data
            ,dataType: "json"
            ,type: "post"
            ,success: function(res){
                // 다시 그리기
                $.redraw_impairment(this_level);

            }
            ,error: function(res){
                console.log("error", res);
            }
        });

    }, // }}}

    "delete_impairment": function(obj)
    { // {{{
        let top_layer = $(obj).parents(".impairment-box");
        let this_level = top_layer.attr("data-level");
        let idx = selected_level[this_level];

        if(!confirm("삭제하시겠습니까? 복구가 불가능합니다.")){
            return false;
        }

        var json_data = {
             "idx": idx
        };

        $.ajax({
             url: "/"+_CONTROLLER+"/Request/DELETE"
            ,async: false
            ,data: json_data
            ,dataType: "json"
            ,type: "post"
            ,success: function(res){

                if(res == 0){
                    alert("하위 데이터가 존재하여 삭제할 수 없습니다.");
                    return false;
                }
                else{

                    // 다시 그리기
                    selected_level[this_level] = 0; // 선택 취소
                    $.redraw_impairment(this_level);

                    // 하위 선택 닫기
                    if(this_level < 2){
                        $.impairment_close(eval(this_level)+1);
                    }
                }

            }
            ,error: function(res){
                console.log("error", res);
            }
        });

    }, // }}}

    "draw_impairment": function(level, parent_idx = null)
    { // {{{
        $("#impairment-level"+level+" .impairment-list").show();
        $("#impairment-level"+level+" .alert").hide();

        $.ajax({
             url: "/"+_CONTROLLER + "/Request/GET"
            ,async: false
            ,data: {"this-level": level, "parent_idx": parent_idx}
            ,dataType: "json"
            ,type: "post"
            ,success: function(res){

                $("#impairment-level" + level + " .list-group").html("");

                var templete = $("#template .list-group-item")
                var obj;
                $.each(res, function(key, val){
                    obj = ""; 
                    obj = templete.clone();

                    if(val.idx == selected_level[level]){
                        obj.addClass("active");
                    }

                    obj.attr("data-idx", val.idx);
                    obj.attr("data-this-level", val.this_level);
                    obj.attr("data-level0-idx", val.level0_idx);
                    obj.attr("data-level1-idx", val.level1_idx);
                    obj.attr("data-level2-idx", val.level2_idx);
                    obj.attr("data-maximum-storage", val.maximum_storage);
                    obj.find(".title").text(val.title);

                    $("#impairment-level" + level + " .list-group").append(obj);
                });
            }
            ,error: function(res){``
                console.log("error", res);
            }
        }); 
    }, // }}}

    "redraw_impairment": function(this_level)
    { // {{{
        $.draw_impairment(this_level, selected_level[eval(this_level)-1]);
    }, // }}}

    "impairment_close": function(this_level)
    { // {{{
        selected_level[this_level] = 0;
        $("#impairment-level"+this_level+" .list-group").html("");
        $("#impairment-level"+this_level+" .impairment-list").hide();
        $("#impairment-level"+this_level+" .alert").show();
    }, // }}}

    "sorting": function(this_level)
    { // {{{
        var target_layer = $(".impairment-box[data-level='"+this_level+"']");

        var sorting_idx = new Array();
        $.each(target_layer.find(".list-group-item"), function(key, val){
            sorting_idx[key] = $(val).attr("data-idx");
        });

        var json_data = {"this_level": this_level, "idx": sorting_idx};
        $.ajax({
             url: "/"+_CONTROLLER + "/Request/SORT"
            ,data: json_data
            ,dataType: "json"
            ,type: "post"
            ,success: function(res){
                return;
            }
            ,error: function(res){
                console.log("error", res);
            }
        });

    }, // }}}
});


