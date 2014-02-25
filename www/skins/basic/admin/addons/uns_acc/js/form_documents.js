/*
* Функция реализации добавления материалов
*
* */
$(function () {

// *****************************************************************************
// ИНФОРМАЦИЯ О ДОКУМЕНТЕ
// *****************************************************************************
    // Смена типа документа
    $('select[name^="data[document]"][name$="[type]"]').live('change', function (e) {
        var type   = $('select[name^="data[document]"][name$="[type]"]');
        var object_from     = $('select[name^="data[document]"][name$="[object_from]"]');
        var object_to       = $('select[name^="data[document]"][name$="[object_to]"]');

        object_from.empty();
        object_to  .empty();

        if (type.val() > 0){
            var url = fn_url('acc_documents.get_object_from&type=' + type.val());
            $.ajaxRequest(
                url,
                {
                    hidde: false,
                    method: 'post',
                    callback: function(data){
                        // Поле Дата плавки
                        var date_cast_div = $('input[name^="data[document]"][name$="[date_cast]"]').parent();
                        if (data.date_cast == "Y"){
                            date_cast_div.removeClass('hidden');
                            date_cast_div.find('label').addClass('cm-required');
                        }else{
                            date_cast_div.addClass('hidden');
                            date_cast_div.find('label').removeClass('cm-required');
                        }

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
    $('select[name^="data[document]"][name$="[object_from]"]').live('change', function (e) {
        var type   = $('select[name^="data[document]"][name$="[type]"]');
        var object_from     = $('select[name^="data[document]"][name$="[object_from]"]');
        var object_to       = $('select[name^="data[document]"][name$="[object_to]"]');

        object_to  .empty();

        if ((type.val() > 0) && (object_from.val() > 0)){
            var url = fn_url('acc_documents.get_object_to&type=' + type.val() + '&object_from=' + object_from.val());
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


// *****************************************************************************
// ИНФОРМАЦИЯ О ПОЗИЦИЯХ ДОКУМЕНТА
// *****************************************************************************
    // Смена типа позиции
    $('select[name^="data[document_items]"][name$="[item_type]"]').live('change', function (e) {
        var document_type    = $('select#document_type');
        var item_type        = $(this);
        var item_category_id = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[item_cat_id]"]');
        var item_name_id     = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[item_id]"]');
        var item_quantity    = $(this).parent().parent().find( 'input[name^="data[document_items]"][name$="[quantity]"]');
        var item_u_id        = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[u_id]"]');
        var item_typesize    = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[typesize]"]');
        var item_weight      = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[weight]"]');

        item_category_id.empty();
        item_name_id    .empty();
        item_quantity   .val(1);
        item_u_id       .empty();
        item_typesize   .empty();
        item_weight     .empty();

        if ((item_type.val() == "M") || (item_type.val() == "D") || (item_type.val() == "P") || (item_type.val() == "PF") || (item_type.val() == "PA") ){
            $.ajaxRequest(
                fn_url('acc_documents.document_items'),
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
                    }
                }
            );
        }
    });

// Смена КАТЕГОРИИ позиции
$('select[name^="data[document_items]"][name$="[item_cat_id]"]').live('change', function (e) {
    var document_type    = $('select#document_type');
    var item_type        = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[item_type]"]');
    var item_cat_id      = $(this);
    var item_name_id     = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[item_id]"]');
    var item_quantity    = $(this).parent().parent().find( 'input[name^="data[document_items]"][name$="[quantity]"]');
    var item_u_id        = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[u_id]"]');
    var item_typesize    = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[typesize]"]');
    var item_weight      = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[weight]"]');

    item_name_id    .empty();
    item_quantity   .val(1);
    item_u_id       .empty();
    item_typesize   .empty();
    item_weight     .empty();

    if (item_cat_id.val() > 0){
        $.ajaxRequest(
            fn_url('acc_documents.document_items'),
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
                }
            }
        );
    }
});

// Смена ПОЗИЦИИ
$('select[name^="data[document_items]"][name$="[item_id]"]').live('change', function (e) {
    var document_type    = $('select#document_type');
    var item_type        = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[item_type]"]');
    var item_cat_id      = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[item_cat_id]"]');
    var item_id          = $(this);
    var item_quantity    = $(this).parent().parent().find( 'input[name^="data[document_items]"][name$="[quantity]"]');
    var item_u_id        = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[u_id]"]');
    var item_typesize    = $(this).parent().parent().find('select[name^="data[document_items]"][name$="[typesize]"]');
    var item_weight      = $(this).parent().parent().find( 'input[name^="data[document_items]"][name$="[weight]"]');
    var item_balance     = $(this).parent().parent().find( 'div.balance');

    item_quantity   .val(1);
    item_u_id       .empty();
    item_typesize   .empty();
    item_weight     .empty();
    item_balance    .empty();

    if (item_id.val() > 0){
        $.ajaxRequest(
            fn_url('acc_documents.document_items'),
            {
                hidde: false,
                method: 'post',
                data: {
                    event           : "change__item_id",
                    document_type   : document_type.val(),
                    item_type       : item_type.val(),
                    item_cat_id     : item_cat_id.val(),
                    item_id         : item_id.val()
                },
                callback: function(data){
                    item_u_id       .append(data.options);
                    item_typesize   .append(data.typesizes);
                    item_weight     .val(data.weight);
                    item_balance    .append(data.balance);
                }
            }
        );
    }
});

});
