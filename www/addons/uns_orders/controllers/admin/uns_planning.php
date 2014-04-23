<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
}
$pl = new planning();

//**************************************************************************
// РАСЧЕТЫ ПЛАНИРОВАНИЯ
//**************************************************************************

if ($mode == "calc"){
    fn_print_r("получить список месяцев за текущий и предыдущий года");
    $year_months = $pl->get_last_months(TIME);

    // Получить Фактические продажи насосов по каждому месяцу
    $sales = $pl->get_sales($year_months);

    fn_print_r($sales);


}

















function fn_uns_planning__search ($controller){
    $params = array(
        'month',
        'year',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}
