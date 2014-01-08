$(function () {
//    $('select[name^="data[sheet]"][name$="[mcat_id]"]').live('change', function (e) {

    function get_document_type (v){
        var t = v.parent().parent().parent().parent().parent().parent().find("input[name='motion[document][document_type_name]']").val();
        return t;
    }

    function get_document_quantity_prih (v){
        return v.parent().parent().parent().parent().parent().parent().parent().find('table.simple:eq(1) tbody tr:eq(' + v.attr('add_attr') + ') select:eq(0)');
    }

    function get_document_status_prih (v){
        return v.parent().parent().parent().parent().parent().parent().parent().find('table.simple:eq(1) tbody tr:eq(' + v.attr('add_attr') + ') select:eq(1)');
    }

    function get_document_object_to (v){
        return v.parent().parent().parent().find('select[name="motion[document][object_to]"]');
    }

    function upd_document_quantity_prih (v){
        var document_type = get_document_type(v);
        var quantity_rash = v;
        var quantity_prih = get_document_quantity_prih(v);
        quantity_prih.val(quantity_rash.val());
    }

    function upd_document_status_prih (v){
        var document_type = get_document_type(v);
        var status_rash   = v;
        var status_prih   = get_document_status_prih(v);
        if (document_type == "VCP"){
            status_prih.val(status_rash.val());
        }else{
            if (document_type == "VCP_COMPLETE"){
                status_prih.val("C");
            }
        }
    }



    // Смена ОБЪЕКТА
    $('select[name="motion[document][object_from]"]').live('change', function (e) {
        var mc_1 = 10;
        var mc_2 = 14;
        var object_from = $(this);
        var object_to = get_document_object_to($(this));
        var document_type = $(this).parent().parent().find("input[name='motion[document][document_type_name]']").val();
        if (document_type == "VCP"){
            if (object_from.val() == mc_1){
                object_to.val(mc_2);
            }else{
                if (object_from.val() == mc_2){
                    object_to.val(mc_1);
                }
            }
        }else{
            if (document_type == "VCP_COMPLETE"){
                object_to.val(object_from.val());
            }
        }
    });

    // Смена кол-ва РАСХОДА
    $('select[name^="motion[document_items]"][name$="[quantity]"]').live('change', function (e) {
        upd_document_quantity_prih($(this));
        upd_document_status_prih($(this));
    });

    // Смена статуса обработки РАСХОДА
    $('select[name^="motion[document_items]"][name$="[processing]"]').live('change', function (e) {
        upd_document_status_prih($(this));
    });


});
