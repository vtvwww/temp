$(function () {

// *****************************************************************************
// ИНФОРМАЦИЯ О ПОЗИЦИЯХ
// *****************************************************************************
    // Смена типа позиции
    $('select[name^="data[plan_items]"][name$="[item_type]"]').live('change', function (e) {
        var item_type           = $(this);
        var item_id             = $(this).parent().parent().find('select[name^="data[plan_items]"][name$="[item_id]"]');
        var item_quantity       = $(this).parent().parent().find( 'input[name^="data[plan_items]"][name$="[quantity]"]');
        var item_quantity_add   = $(this).parent().parent().find( 'input[name^="data[plan_items]"][name$="[quantity_add]"]');

        item_quantity.val("");
        item_id.empty();

        if ((item_type.val() == "S") || (item_type.val() == "D")){
            $.ajaxRequest(
                fn_url('uns_plan_of_sales.plan_items'),
                {
                    hidde: false,
                    method: 'post',
                    data: {
                        event           : "change__item_type",
                        item_type       : item_type.val()
                    },
                    callback: function(data){
                        item_id.append(data.options);
                        item_quantity.val(1);
                        item_quantity_add.val(1);
                    }
                }
            );
        }
    });

    // Смена позиции насоса
    $('select[name^="data[plan_items]"][name$="[item_id]"]').live('change', function (e) {
        var item_quantity       = $(this).parent().parent().find( 'input[name^="data[plan_items]"][name$="[quantity]"]');
        var item_quantity_add   = $(this).parent().parent().find( 'input[name^="data[plan_items]"][name$="[quantity_add]"]');
        item_quantity.focus().select();
    });
});

// *************************************************************************
// АНАЛИЗ ПЛАНА ПРОИЗВОДСТВА
// *************************************************************************
function calc_ps_set (plan, ps_id, pump_id, details_list){
//    alert (ps_id + " & " + pump_id);
    var details = details_list.split("-");
    for (var i = 0; i < details.length; i++) {
        //Исходные данные
        var id = ps_id + "_" + pump_id + "_" + details[i];
        var total_of_detail     = parseInt($("input[name='ps_set_data__" + id + "[total_of_details]']").val());
        var details_per_pump    = parseInt($("input[name='ps_set_data__" + id + "[details_per_pump]']").val());
        var cast_per_detail     = parseFloat($("input[name='ps_set_data__" + id + "[cast_per_detail]']").val()).toFixed(1);
        var total_of_cast       = parseInt($("input[name='ps_set_data__" + id + "[total_of_cast]']").val());
        var str = "";

        // 1. ПЛАН НАСОСОВ
        if (plan == 0) str = '<span class="zero" style="font-size: 17px;">0</span>';
        else str = '<span style="font-size: 17px;">' + plan*details_per_pump + '</span>';
        $("td.plan_of_pumps_" + id).empty().append(str);

        // 2. ДЕФИЦИТ ДЕТАЛЕЙ
        var deficit_of_detail = total_of_detail - plan*details_per_pump;
        if (deficit_of_detail < 0)
            str = '<span class="info_warning_block bold" style="font-size: 16px;">' + deficit_of_detail +'</span>';
        else
            str = '<span class="zero" style="font-size: 16px;">' + deficit_of_detail + '</span>';
        $("td.deficit_of_details_" + id).empty().append(str);

        // 3. ДЕФИЦИТ ЗАГОТОВОК
        var deficit_of_cast = 0;
        if (deficit_of_detail < 0){
            deficit_of_cast = deficit_of_detail*cast_per_detail+total_of_cast;
            deficit_of_cast = Math.round(deficit_of_cast*10)/10;
        }

        if (deficit_of_cast < 0)
            str = '<span class="info_warning_block bold" style="font-size: 16px;">' + deficit_of_cast +'</span>';
        else
            str = '<span class="zero" style="font-size: 16px;">' + deficit_of_cast + '</span>';
        $("td.deficit_of_casts_" + id).empty().append(str);
    }
}


