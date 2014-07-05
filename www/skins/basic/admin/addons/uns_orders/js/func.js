$(function () {

    //==========================================================================
    // СМЕНА ТИПА ПОЗИЦИИ
    //==========================================================================
    $('select[name^="order_data[document_items]"][name$="[item_type]"]').live('change', function (e) {
        var item_type        = $(this);
        var item_cat_id      = $(this).parent().parent().find('[name^="order_data[document_items]"][name$="[item_cat_id]"]');
        var item_name_id     = $(this).parent().parent().find('[name^="order_data[document_items]"][name$="[item_id]"]');
        var item_quantity    = $(this).parent().parent().find('[name^="order_data[document_items]"][name$="[quantity]"]');
        var item_weight      = $(this).parent().parent().find('[name^="order_data[document_items]"][name$="[weight]"]');
        var item_weight_item    = $(this).parent().parent().find('span.weight');
        var item_weight_total   = $(this).parent().parent().find('span.total_weight');

        item_cat_id     .empty();
        item_name_id    .empty();
        item_quantity   .val(0);
        item_weight     .empty();
        item_weight_item.empty();
        item_weight_total.empty();

        if ((item_type.val() == "M") || (item_type.val() == "D") || (item_type.val() == "P") || (item_type.val() == "PF") || (item_type.val() == "PA") ){
            $.ajaxRequest(
                fn_url('uns_orders.document_items'),
                {
                    hidde: false,
                    method: 'post',
                    data: {
                        event           : "change__item_type",
                        item_type       : item_type.val()
                    },
                    callback: function(data){
                        item_cat_id.append(data.options);
                    }
                }
            );
        }
    });


    //==========================================================================
    // Смена КАТЕГОРИИ позиции
    //==========================================================================
    $('select[name^="order_data[document_items]"][name$="[item_cat_id]"]').live('change', function (e) {
        var item_type        = $(this).parent().parent().find('select[name^="order_data[document_items]"][name$="[item_type]"]');
        var item_cat_id      = $(this);
        var item_id          = $(this).parent().parent().find('[name^="order_data[document_items]"][name$="[item_id]"]');
        var item_quantity    = $(this).parent().parent().find('[name^="order_data[document_items]"][name$="[quantity]"]');
        var item_weight      = $(this).parent().parent().find('[name^="order_data[document_items]"][name$="[weight]"]');
        var item_weight_item    = $(this).parent().parent().find('span.weight');
        var item_weight_total   = $(this).parent().parent().find('span.total_weight');

        item_id         .empty();
        item_quantity   .val(0);
        item_weight     .empty();
        item_weight_item.empty();
        item_weight_total.empty();

        if (item_cat_id.val() > 0){
            $.ajaxRequest(
                fn_url('uns_orders.document_items'),
                {
                    hidde: false,
                    method: 'post',
                    data: {
                        event           : "change__item_cat_id",
                        item_type       : item_type.val(),
                        item_cat_id     : item_cat_id.val()
                    },
                    callback: function(data){
                        item_id.append(data.options);
                    }
                }
            );
        }
    });


    //==========================================================================
    // Смена позиции
    //==========================================================================
    $('select[name^="order_data[document_items]"][name$="[item_id]"]').live('change', function (e) {
        var item_type        = $(this).parent().parent().find('select[name^="order_data[document_items]"][name$="[item_type]"]');
        var item_cat_id      = $(this).parent().parent().find('[name^="order_data[document_items]"][name$="[item_cat_id]"]');
        var item_id          = $(this);
        var item_quantity    = $(this).parent().parent().find('[name^="order_data[document_items]"][name$="[quantity]"]');
        var item_weight      = $(this).parent().parent().find('[name^="order_data[document_items]"][name$="[weight]"]');
        var item_weight_item    = $(this).parent().parent().find('span.weight');
        var item_weight_total   = $(this).parent().parent().find('span.total_weight');

        item_weight     .empty();
        item_weight_item.empty();
        item_weight_total.empty();

        if (item_cat_id.val() > 0){
            $.ajaxRequest(
                fn_url('uns_orders.document_items'),
                {
                    hidde: false,
                    method: 'post',
                    data: {
                        event           : "change__item_id",
                        item_type       : item_type.val(),
                        item_id     : item_id.val()
                    },
                    callback: function(data){
                        item_weight.val(data.weight);
                        item_weight_item.append(data.weight);
                        item_weight_total.empty().append(item_quantity.val()*data.weight);
                    }
                }
            );
        }
    });


    //==========================================================================
    // Смена количества
    //==========================================================================
    $('select[name^="order_data[document_items]"][name$="[quantity]"]').live('change', function (e) {
        var item_weight_total   = $(this).parent().parent().parent().find('span.total_weight');
        var item_weight         = $(this).parent().parent().parent().find('[name^="order_data[document_items]"][name$="[weight]"]').val();
        var t =  Math.round(parseInt($(this).val())*parseFloat(item_weight)*10)/10;
        item_weight_total.empty().append(t);
    });


});



