/*
* Функция запроса вариантов по выбранному параметру
* форма редактирования ПАРАМЕТРОВ для элементов
*
* */
$(function () {
    $('select[name^="data[options]"][name$="[option_id]"]').live('change', function (e) {
        var option_select  = $(this);
        var variant_select = $(this).parent().parent().find('select[name^="data[options]"][name$="[ov_id]"]');

        if (option_select.val() == 0){
            variant_select.empty();
        }else{
            var url = fn_url('uns_options.get_variants&option_id=' + option_select.val());
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
});
