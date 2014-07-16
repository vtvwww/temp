/*
* Функция реализации добавления материалов
*
* */
$(function () {

// Смена КАТЕГОРИИ материала
$('select[name^="kit_details"][name$="[dcat_id]"]').live('change', function (e) {
    var dcat_id         = $(this);
    var detail_id       = $(this).parent().parent().find('select[name^="kit_details"][name$="[detail_id]"]');
    var detail_id       = $(this).parent().parent().find('select[name^="kit_details"][name$="[detail_id]"]');

    detail_id.empty();

    if (dcat_id.val() > 0){
        $.ajaxRequest(
            fn_url('uns_kits.get_details'),
            {
                hidde: false,
                method: 'post',
                data: {
                    event       : "change__dcat_id",
                    dcat_id     : dcat_id.val()
                },
                callback: function(data){
                    detail_id.append(data.options);
                }
            }
        );
    }
});

});

function multi_select(s){
    s.parent().parent().parent().parent().parent().find('tbody select[name$="[quantity]"]').val(s.val());
}
