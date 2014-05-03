<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

$pl = new plan_of_sales();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
}
//**************************************************************************
// Анализ продаж
//**************************************************************************

if ($mode == "test_graph"){
    $pl->test_graph($params);
}

if ($mode == "calculation"){
    if (!is__more_0($_REQUEST["month"], $_REQUEST["year"], $_REQUEST["week_supply"])){
        $view->assign("error", "Y");
        $p = array(
            "month"             => date('n', TIME)+1,
            "year"              => date('Y', TIME),
            "week_supply"       => 2,
            "koef_plan_prodazh" => 20,
        );
        $view->assign('search', $p);
        return;
    }

//    $ps_id = array(/*29,37,77,*/76/*,65,66,78,79,80*/); // только К8/18 --- К290/30

    // 1. Параметры для расчета плана производства
    $graphs_key = substr(md5(microtime().mt_rand()), 0, 10);
    $params = array(
        "ps_id"             => $ps_id,
        "month"             => $_REQUEST["month"],
        "year"              => $_REQUEST["year"],
        "week_supply"       => $_REQUEST["week_supply"],
        "koef_plan_prodazh" => $_REQUEST["koef_plan_prodazh"],
        "graphs_key"        => $graphs_key,
    );
    $view->assign("graphs_key",     $graphs_key);

    // 2. Расчет плана производства
    list($pump_series, $sales, $analysis, $plan, $ps_order) = $pl->calculation($params);
    $view->assign('pump_series',    $pump_series);
    $view->assign('sales',          $sales);
    $view->assign('analysis',       $analysis);
    $view->assign('plan',           $plan);
    $view->assign('ps_order',       $ps_order);
    $view->assign('search',         $params);

//    fn_print_r($pump_series, $sales, $analysis);


    $view->assign('search', $_REQUEST);
    $months = array(1=>"Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
    $view->assign('months', $months);

    // 3. Список имеющихся заказов
    $orders = array_shift(fn_acc__get_orders(array("only_active"=>true)));
    $view->assign('orders', $orders);

    // КЛИЕНТЫ
    list($customers) = fn_uns__get_customers();
    $view->assign('customers', $customers);












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

















function fn_uns_plan_of_sales__search ($controller){
    $params = array(
        "month",
        "year",
        "week_supply",
        "years_for_analysis",
        "koef_plan_prodazh",
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}
