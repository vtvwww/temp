
/*  Материалы определенной категории     */
/*  --- Готовые изделий                  */
/*  Склад метизов и Подшипников          */
/*****************************************/
$(function () {

    // Смена типа документа
    $('select[name^="data_store[document]"][name$="[type]"]').live('change', function (e) {
        var type   = $('select[name^="data_store[document]"][name$="[type]"]');
        var object_from     = $('select[name^="data_store[document]"][name$="[object_from]"]');
        var object_to       = $('select[name^="data_store[document]"][name$="[object_to]"]');

        object_from.empty();
        object_to  .empty();

        if (type.val() > 0){
            var url = fn_url('uns_moving_stores.get_object_from&type=' + type.val());
            $.ajaxRequest(
                url,
                {
                    hidde: false,
                    method: 'post',
                    callback: function(data){
                        // Если Акт изменения остатка
                        if ((data.aio == "Y") || (data.ro == "Y")  || (data.as_vlc == "Y") ){
//                            $('select#object_to').prev().empty().append('Склад Где:');
                            $('select#object_from').prev().attr('class', '');
                            $('select#object_from').parent().addClass('hidden');
                            object_to.append(data.object_from);

                        }else{
//                            $('select#object_to').prev().empty().append('Склад Откуда:');
                            $('select#object_from').prev().attr('class', 'cm-required cm-integer-more-0');
                            $('select#object_from').parent().removeClass('hidden');
                            object_from.append(data.object_from);
                        }
                    }
                }
            );
        }
    });

    // Смена ОБЪЕКТА ОТКУДА
    $('select[name^="data_store[document]"][name$="[object_from]"]').live('change', function (e) {
        var type   = $('select[name^="data_store[document]"][name$="[type]"]');
        var object_from     = $('select[name^="data_store[document]"][name$="[object_from]"]');
        var object_to       = $('select[name^="data_store[document]"][name$="[object_to]"]');

        object_to  .empty();

        if ((type.val() > 0) && (object_from.val() > 0)){
            var url = fn_url('uns_moving_stores.get_object_to&type=' + type.val() + '&object_from=' + object_from.val());
            $.ajaxRequest(
                url,
                {
                    hidde: false,
                    method: 'post',
                    callback: function(data){
                        object_to.append(data.object_to);
                    }
                }
            );
        }
    });




    /***************************************************************************/
    /***************************************************************************/
    /***************************************************************************/

    // Смена типа позиции
    $('select[name^="data_store[document_items]"][name$="[item_type]"]').live('change', function (e) {
        var document_type    = $('select#document_type');
        var item_type        = $(this);
        var item_category_id = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[item_cat_id]"]');
        var item_name_id     = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[item_id]"]');
        var item_quantity    = $(this).parent().parent().find( 'input[name^="data_store[document_items]"][name$="[quantity]"]');
        var item_u_id        = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[u_id]"]');
        var item_typesize    = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[typesize]"]');
        var item_weight      = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[weight]"]');
        var item_processing  = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[processing]"]');

        item_category_id.empty();
        item_name_id    .empty();
        item_quantity   .val(1);
        item_u_id       .empty();
        item_typesize   .empty();
        item_weight     .empty();

        if ((item_type.val() == "M") || (item_type.val() == "D") || (item_type.val() == "P") || (item_type.val() == "PF") || (item_type.val() == "PA") ){
            $.ajaxRequest(
                fn_url('uns_moving_stores.document_items'),
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



    // Смена КАТЕГОРИИ позиции
    $('select[name^="data_store[document_items]"][name$="[item_cat_id]"]').live('change', function (e) {
        var document_type    = $('select#document_type');
        var item_type        = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[item_type]"]');
        var item_cat_id      = $(this);
        var item_name_id     = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[item_id]"]');
        var item_quantity    = $(this).parent().parent().find( 'input[name^="data_store[document_items]"][name$="[quantity]"]');
        var item_u_id        = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[u_id]"]');
        var item_typesize    = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[typesize]"]');
        var item_weight      = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[weight]"]');

        item_name_id    .empty();
        item_quantity   .val(1);
        item_u_id       .empty();
        item_typesize   .empty();
        item_weight     .empty();

        if (item_cat_id.val() > 0){
            $.ajaxRequest(
                fn_url('uns_moving_stores.document_items'),
                {
                    hidde: false,
                    method: 'post',
                    data: {
                        event           : "change__item_cat_id",
                        document_type   : document_type.val(),
                        item_type       : item_type.val(),
                        item_cat_id     : item_cat_id.val()
                    },
                    callback: function(data){
                        item_name_id.append(data.options);
                        // Для автоматического выбора первой позиции
                        if ($("select#auto_select_name").val() == "Y"){
                            item_name_id.find("option:nth-child(2)").attr("selected", "selected").change();
                        }
                    }
                }
            );
        }
    });


    // Смена ПОЗИЦИИ
    $('select[name^="data_store[document_items]"][name$="[item_id]"]').live('change', function (e) {
        var document_type    = $('select#document_type');
        var item_type        = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[item_type]"]');
        var item_cat_id      = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[item_cat_id]"]');
        var item_id          = $(this);
        var item_quantity    = $(this).parent().parent().find( 'input[name^="data_store[document_items]"][name$="[quantity]"]');
        var item_u_id        = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[u_id]"]');
        var item_typesize    = $(this).parent().parent().find('select[name^="data_store[document_items]"][name$="[typesize]"]');
        var item_weight      = $(this).parent().parent().find( 'input[name^="data_store[document_items]"][name$="[weight]"]');
        var item_balance     = $(this).parent().parent().find( 'div.balance');

        var object_from     = $('select[name^="data_store[document]"][name$="[object_from]"]');
        var object_to       = $('select[name^="data_store[document]"][name$="[object_to]"]');

    //    item_quantity   .val(1);
        item_u_id       .empty();
        item_typesize   .empty();
        item_weight     .empty();
        item_balance    .empty();

        if (item_id.val() > 0){
            $.ajaxRequest(
                fn_url('uns_moving_stores.document_items'),
                {
                    hidde: false,
                    method: 'post',
                    data: {
                        event           : "change__item_id",
                        document_type   : document_type.val(),
                        item_type       : item_type.val(),
                        item_cat_id     : item_cat_id.val(),
                        item_id         : item_id.val(),
                        object_to       : object_to.val(),
                        object_from     : object_from.val(),
                    },
                    callback: function(data){
                        item_u_id       .append(data.options);
                        item_typesize   .append(data.typesizes);
                        item_weight     .val(data.weight);
                        item_balance    .append(data.balance);
                        item_quantity   .focus().select();
                    }
                }
            );
        }
    });


});