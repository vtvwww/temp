/*
* Функция реализации добавления материалов
*
* */
$(function () {
    // Получить список материалов по выбранной категории
    $('select[name^="data[accounting][materials]"][name$="[mcat_id]"]').live('change', function (e) {
        var val = $(this);
        var variant_select = $(this).parent().parent().find('select[name^="data[accounting][materials]"][name$="[material_id]"]');
        var unit_select = $(this).parent().parent().find('select[name^="data[accounting][materials]"][name$="[u_id]"]');

        var saq_iaq = $('span.add_quantity, input.add_quantity');
        saq_iaq.addClass('hidden');
        $('input.add_quantity_state').val('D');

        unit_select.empty();

        if (val.val() == 0){
            variant_select.empty();
        }else{
            var url = fn_url('uns_ajax.uns_details.get_materials&cat_id=' + val.val());
            $.ajaxRequest(
                url,
                {
                    hidde: false,
                    method: 'post',
                    callback: function(data){
                        variant_select.empty().append(data.options);
                    }
                }
            );
        }
    });

    // Получить список ед. измерений по выбранному материалу
    $('select[name^="data[accounting][materials]"][name$="[material_id]"]').live('change', function (e) {
        var val = $(this);
        var variant_select = $(this).parent().parent().find('select[name^="data[accounting][materials]"][name$="[u_id]"]');

        var saq_iaq = $('span.add_quantity, input.add_quantity');
        saq_iaq.addClass('hidden');
        $('input.add_quantity_state').val('D');



        if (val.val() == 0){
            variant_select.empty();
        }else{
            var url = fn_url('uns_ajax.uns_details.get_material_units&material_id=' + val.val());
            $.ajaxRequest(
                url,
                {
                    hidde: false,
                    method: 'post',
                    callback: function(data){
                        variant_select.empty().append(data.options);

                        $('input.add_quantity_state').val(data.add_quantity_state);
                        if (data.add_quantity_state == "A"){
                            saq_iaq.removeClass('hidden');
                        }else{
                            saq_iaq.addClass('hidden');
                        }
                    }
                }
            );
        }
    });
});
