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

});
