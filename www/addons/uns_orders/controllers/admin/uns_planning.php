<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

$pl = new planning();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
}

//**************************************************************************
// РАСЧЕТЫ ПЛАНИРОВАНИЯ
//**************************************************************************

if ($mode == "calc"){
    if (!is__more_0($_REQUEST["month"], $_REQUEST["year"], $_REQUEST["week_supply"], $_REQUEST["years_for_analysis"])){
        fn_set_notification("E", "Ошибка", "Правильно укажите исходные данные");
    }

    $ps_id = array(29,37,77,76,65,66); // только К8/18 --- К290/30

    // 1. Параметры для расчета плана производства
    $params = array(
        "ps_id"             => $ps_id,
        "month"             => $_REQUEST["month"],
        "year"              => $_REQUEST["year"],
        "week_supply"       => $_REQUEST["week_supply"],
        "years_for_analysis"=> $_REQUEST["years_for_analysis"],
    );

    // 2. Расчет плана производства
    $plan = $pl->calc($params);
    $view->assign('plan', $plan);

    // Серии насосов
    list($pump_series) = fn_uns__get_pump_series(array('only_active' => true,));
    $view->assign('pump_series', $pump_series);


    $view->assign('search', $_REQUEST);

    /*
    //    fn_print_r("получить список месяцев за текущий и предыдущий года");
        $year_months = $pl->get_last_months(TIME);

        // Получить Фактические продажи насосов по каждому месяцу
    //    $sales = $pl->get_sales($year_months);

        $char_config = array("dir_charts"=>DIR_ROOT."/var/charts/");
        $chart = new uns_chart($char_config);
        $chart_data = array(
            array(
                "Янв"=>12,
                "Фев"=>12,
                "Мар"=>13,
                "Май"=>13,
                "Апр"=>13,
                "Июн"=>13,
                "Июл"=>13,
                "Авг"=>13,
                "Сен"=>13,
                "Окт"=>13,
                "Ноя"=>13,
                "Дек"=>13,
            ),
        );

        $chart->get_chart($chart_data);

    //    fn_print_r($sales);


        $data = array(
            2013 => array(
                "months" => array(
                    1=> array(
                        "pump_series" => array(
                            29 => array(
                                "quantity"=>6,
                            ),
                        ),
                    ),
                    2=> array(),
                    3=> array(),
                    4=> array(),
                    5=> array(),
                    6=> array(),
                    7=> array(),
                    8=> array(),
                    9=> array(),
                   10=> array(),
                   11=> array(),
                   12=> array(),
                ),
            ),

        );


        $view->assign('planning', $data);*/


}

















function fn_uns_planning__search ($controller){
    $params = array(
        "month",
        "year",
        "week_supply",
        "years_for_analysis",
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}
