/*
* Функция запроса единиц измерения свойственных выбранной характеристике
* форма редактирования ХАРАКТЕРИСТИК для элементов
*
* */
$(function () {
    $('select[name^="data[features]"][name$="[feature_id]"]').live('change', function (e) {
        var feature_select  = $(this);
        var unit_select = $(this).parent().parent().find('select[name^="data[features]"][name$="[u_id]"]');

        if (feature_select.val() == 0){
            unit_select.empty();
        }else{
            var url = fn_url('uns_features.get_units&feature_id=' + feature_select.val());
            $.ajaxRequest(
                url,
                {
                    hidde: false,
                    method: 'post',
                    callback: function(data){
                        unit_select.empty().append(data.options);
                    }
                }
            );
        }
    });
});
