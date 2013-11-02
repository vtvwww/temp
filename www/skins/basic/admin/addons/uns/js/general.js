function mark_item (target){
    $('td.mark').removeClass('mark');
    target.parent().parent().find('td').addClass('mark');
}
