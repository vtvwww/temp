$(function () {

    //==========================================================================
    // СМЕНА ТИПА ПОЗИЦИИ
    //==========================================================================
    $('select[name^="order[document_items]"][name$="[item_type]"]').live('change', function (e) {
        var document_type    = $('select#document_type');
        var item_type        = $(this);
        var item_category_id = $(this).parent().parent().find('select[name^="order[document_items]"][name$="[item_cat_id]"]');
        var item_name_id     = $(this).parent().parent().find('select[name^="order[document_items]"][name$="[item_id]"]');
        var item_quantity    = $(this).parent().parent().find( 'input[name^="order[document_items]"][name$="[quantity]"]');
        var item_u_id        = $(this).parent().parent().find('select[name^="order[document_items]"][name$="[u_id]"]');
        var item_typesize    = $(this).parent().parent().find('select[name^="order[document_items]"][name$="[typesize]"]');
        var item_weight      = $(this).parent().parent().find('select[name^="order[document_items]"][name$="[weight]"]');
        var item_processing  = $(this).parent().parent().find('select[name^="order[document_items]"][name$="[processing]"]');

        item_category_id.empty();
        item_name_id    .empty();
        item_quantity   .val(1);
        item_u_id       .empty();
        item_typesize   .empty();
        item_weight     .empty();

        if ((item_type.val() == "M") || (item_type.val() == "D") || (item_type.val() == "P") || (item_type.val() == "PF") || (item_type.val() == "PA") ){
            $.ajaxRequest(
                fn_url('uns_order.document_items'),
                {
                    hidde: false,
                    method: 'post',
                    data: {
                        event           : "change__item_type",
                        document_type   : document_type.val(),
                        item_type       : item_type.val()
                    },
                    callback: function(data){
                        item_category_id.append(data.options);
                        if ((item_type.val() == "P") || (item_type.val() == "PF") || (item_type.val() == "PA") ){
                            item_processing.addClass("hidden");
                        }else{
                            item_processing.removeClass("hidden");
                        }
                    }
                }
            );
        }
    });

});



