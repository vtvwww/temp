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
