/*
* Функция реализации добавления материалов
*
* */
$(function () {

    // Смена КАТЕГОРИИ материала
    $('select[name^="data[sheet]"][name$="[mcat_id]"]').live('change', function (e) {
        var mcat_id         = $(this);
        var material_id     = $('select[name^="data[sheet]"][name$="[material_id]"]');

        material_id.empty();

        if (mcat_id.val() > 0){
            $.ajaxRequest(
                fn_url('uns_sheets.get_materials'),
                {
                    hidde: false,
                    method: 'post',
                    data: {
                        event       : "change__mcat_id",
                        mcat_id     : mcat_id.val()
                    },
                    callback: function(data){
                        material_id.append(data.options);
                    }
                }
            );
        }
    });

    //**************************************************************************
    // GET_LIST_DETAILS
    //**************************************************************************
    $('select[name^="data[sheet]"][name$="[material_id]"]').live('change', function (e) {
        get_list_details ();
    });

    $('a#get_list_details').live('click', function (e) {
        get_list_details ();
    });

    function get_list_details (){
        var material_id     = $('select[name^="data[sheet]"][name$="[material_id]"]');
        var list_details    = $("div#list_details");

        list_details.empty();

        if (material_id.val() > 0){
            $.ajaxRequest(
                fn_url('uns_sheets.get_list_details'),
                {
                    hidde: false,
                    method: 'post',
                    data: {
                        event       : "change__material_id",
                        material_id : material_id.val()
                    },
                    callback: function(data){
                        list_details.append(data.list_details);
                    }
                }
            );
        }
    }

    //**************************************************************************
    // insert into table
    //**************************************************************************
    $('a.add_detail').live('click', function(){
        var d_id = $(this).attr("detail_id");
        $("table.sheet_details tbody.data").empty().append($("div.data_detail__" + d_id + " table tbody").html());
        $("input#details").val(d_id);
    });







});
