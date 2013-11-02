/*
* Функция реализации ед. измерения учета
*
* */
$(function () {
    $('select[name="data[accounting][main_u_id]"]').live('change', function (e) {
        var main_unit   = $(this);
        var main_unit_duplication = $(this).parent().parent().parent().find('select[name="data[accounting][main_u_id_duplication]"]');
        main_unit_duplication.val(main_unit.val());
    });
});

$(function () {
    $('select[name^="data[accounting][typesizes]"]').live('change', function (e) {
        var table = $(this).parent().next();
        if ($(this).val() == "A"){
            table.removeClass("hidden");
        }else{
            table.addClass("hidden");
        }
    });
});
