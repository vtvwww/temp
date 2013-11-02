/*
* Функция реализации добавления материалов
*
* */
$(function () {

// Смена типа позиции ДЕТАЛЬ - МАТЕРИАЛ
$('select[name^="data[packing_list]"][name$="[item_type]"]').live('change', function (e) {
    var item_type        = $(this);
    var item_category_id = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[item_cat_id]"]');
    var item_name_id     = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[item_id]"]');
    var item_quantity    = $(this).parent().parent().find('input[name^="data[packing_list]"][name$="[quantity]"]');
    var item_u_id        = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[u_id]"]');
    var item_typesize    = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[typesize]"]');

    item_category_id.empty();
    item_name_id    .empty();
    item_quantity   .val(1);
    item_u_id       .empty();
    item_typesize   .empty();

    var url = fn_url('uns_ajax.packing_list.item_type&item_type=' + item_type.val());
    $.ajaxRequest(
        url,
        {
            hidde: false,
            method: 'post',
            callback: function(data){
                item_category_id.append(data.options);
            }
        }
    );
});

// Смена КАТЕГОРИИ позиции
$('select[name^="data[packing_list]"][name$="[item_cat_id]"]').live('change', function (e) {
    var item_type        = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[item_type]"]');
    var item_cat_id      = $(this);
    var item_name_id     = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[item_id]"]');
    var item_quantity    = $(this).parent().parent().find('input[name^="data[packing_list]"][name$="[quantity]"]');
    var item_u_id        = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[u_id]"]');
    var item_typesize    = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[typesize]"]');

    item_name_id    .empty();
    item_quantity   .val(1);
    item_u_id       .empty();
    item_typesize   .empty();


    var url = fn_url('uns_ajax.packing_list.item_cat_id&item_type=' + item_type.val() + '&item_cat_id=' + item_cat_id.val());
    $.ajaxRequest(
        url,
        {
            hidde: false,
            method: 'post',
            callback: function(data){
                item_name_id.append(data.options);
            }
        }
    );
});

// Смена ПОЗИЦИИ
$('select[name^="data[packing_list]"][name$="[item_id]"]').live('change', function (e) {
    var item_type        = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[item_type]"]');
    var item_cat_id      = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[item_cat_id]"]');
    var item_id          = $(this);
    var item_quantity    = $(this).parent().parent().find('input[name^="data[packing_list]"][name$="[quantity]"]');
    var item_u_id        = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[u_id]"]');
    var item_typesize    = $(this).parent().parent().find('select[name^="data[packing_list]"][name$="[typesize]"]');

    item_quantity   .val(1);
    item_u_id       .empty();
    item_typesize   .empty();

    var url = fn_url('uns_ajax.packing_list.item_id&item_type=' + item_type.val() + '&item_id=' + item_id.val());
    $.ajaxRequest(
        url,
        {
            hidde: false,
            method: 'post',
            callback: function(data){
                item_u_id.append(data.options);
                item_typesize.append(data.typesizes);
            }
        }
    );
});


// Смена ТИПА ЗАМЕЩЕНИЯ
$('select[name^="data[packing_list]"][name$="[pplr_type]"]').live('change', function (e) {
    var spans            = $(this).parent().parent().find('span[class^="series_item"]');
    var pplr_type        = $(this);
    var replacement_item = $(this).parent().parent().next();

    // 1.
    spans.removeClass("series_item").removeClass("series_item_0").removeClass("series_item_D").removeClass("series_item_R");
    spans.addClass("series_item" + "_" + pplr_type.val());

    // 2.
    if (pplr_type.val() == "R"){
        replacement_item.removeClass("hidden");
    }else{
        replacement_item.addClass("hidden");
    }
});


});
