<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);
$pl = new plan_of_sales();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $suffix = '';

    if($mode == 'update'){
        $id = fn_uns__upd_plan(is__more_0($_REQUEST['plan_id'])?$_REQUEST['plan_id']:0, $_REQUEST['data']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&plan_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }


    if (defined('AJAX_REQUEST') and $mode == 'plan_items'){
        switch ($_REQUEST['event']){
            case "change__item_type": // Произошла смена ТИПА
                if(in_array($_REQUEST['item_type'], array('D', 'S'))){
                    $options = "<option value='0'>---</option>";
                    if ($_REQUEST["item_type"] == "S"){
                        $p = array(
                            'only_active' => true,
                            'group_by_types'=>true,
                        );
                        list($pump_series) = fn_uns__get_pump_series($p);
                        $view->assign("f_type", "select_by_group");
                        $view->assign("f_options", "pump_series");
                        $view->assign("f_option_id", "ps_id");
                        $view->assign("f_option_value", "ps_name");
                        $view->assign("f_optgroups", $pump_series);
                        $view->assign("f_optgroup_label", "pt_name_short");
                        $view->assign('f_simple_2', true);
                    }
                }
                $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                $ajax->assign('options', $options);
            break;
        }
        exit;
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


// Общие шаблоны
$months = array(1=>"Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
$view->assign('months', $months);



//**************************************************************************
// Анализ продаж
//**************************************************************************
if ($mode == "calculation"){
    if (!is__more_0($_REQUEST["month"], $_REQUEST["year"], $_REQUEST["week_supply"])){
        $view->assign("error", "Y");
        $p = array(
            "month"             => date('n', TIME),
            "year"              => date('Y', TIME),
            "week_supply"       => 2,
            "koef_plan_prodazh" => 20,
        );
        $view->assign('search', $p);
        return;
    }

//    $ps_id = array(29,37,77,76,65,66,78,79,80); // только К8/18 --- К290/30
    $ps_id = array(76,66); // только К8/18 --- К290/30

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

    // 3. Список имеющихся заказов
    $orders = array_shift(fn_acc__get_orders(array("only_active"=>true)));
    $view->assign('orders', $orders);

    // КЛИЕНТЫ
    list($customers) = fn_uns__get_customers();
    $view->assign('customers', $customers);

}


//**************************************************************************
// УПРАВЛЕНИЕ ПЛАНАМИ ПРОДАЖ
//**************************************************************************
if($mode == 'update' or $mode == 'add'){
    fn_add_breadcrumb("Планы продаж", $controller . ".manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}

if ($mode == "manage") {
    $p = array(
        "with_count" => true,
        "with_sum" => true,
    );
    $p = array_merge($_REQUEST, $p);
    list($plans, $search) = fn_uns__get_plans($p, UNS_ITEMS_PER_PAGE);
    $view->assign('plans', $plans);
    $view->assign('search', $search);
}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['plan_id']) or !is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT plan_id FROM ?:_plans WHERE plan_id = ?i", $_REQUEST['plan_id']))){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }
    $p = array(
        "with_items"=> true,
        "with_sum"  => true,
        "plan_id"   => $_REQUEST['plan_id'],
        "type"      => "sales",
    );
    $plan = array_shift(array_shift(fn_uns__get_plans($p)));
    $view->assign('plan', $plan);

    // Серии насосов
    list($pump_series) = fn_uns__get_pump_series(array('only_active' => true,'group_by_types'=>true, "view_in_plans"=>"Y",));
    $view->assign('pump_series', $pump_series);
}

if($mode == 'delete'){
    if (is__more_0($_REQUEST["plan_id"])){
        fn_uns__del_plan($_REQUEST['plan_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


//******************************************************************************
// ОТСЛЕЖИВАНИЕ
//******************************************************************************
if ($mode == "tracking") {
    if (is__more_0($_REQUEST["month"], $_REQUEST["year"])){

        // СЕРИИ НАСОСОВ
        list($pump_series) = fn_uns__get_pump_series(array('only_active' => true,'group_by_types'=>true, "view_in_plans"=>"Y",));
        $view->assign('pump_series', $pump_series);


        // ПЛАН ПРОДАЖ на выбранный месяц
        $p = array(
            "with_count"        => true,
            "with_sum"          => true,
            "with_items"        => true,
            "group_by_item"     => true,
        );
        $p = array_merge($_REQUEST, $p);
        $plan = array_shift(array_shift(fn_uns__get_plans($p)));
        $view->assign('plan', $plan);
        $view->assign('search', $p);

        // ФАКТИЧЕСКИЕ ПРОДАЖИ на выбранный месяц
        $pump_series = array_shift(fn_uns__get_pump_series(array('only_active' => true, "view_in_plans"=>"Y",)));
        $begin  = $_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1" . " 00:00:00";
        $end    = $_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . date("t", strtotime($begin))  . " 23:59:59";
        $sales = fn_uns__get_sales_pump_series_by_period(array_keys($pump_series), strtotime($begin), strtotime($end));
        $view->assign('sales', $sales);

        // ПОЛУЧИТЬ ПРОЦЕНТЫ ВЫПОЛНЕНИЯ ПЛАНА ПРОДАЖ
        $percs = array();
        foreach (array_keys($pump_series) as $id){
            $p = (int) $plan["group_by_item"]["S"][$id]["quantity"];
            $s = (int) $sales[$id];
            $ovf    = "N"; // Флаг переполнения
            $done   = 0;
            $left   = 0;
            $right  = 0;
            if ($p > 0 and $s > 0){
                if ($p > $s){
                    $done     = fn_fvalue(100*$s/$p, 0);
                    $left     = $done;
                    $right    = 0;
                }elseif ($p < $s) {
                    $ovf      = "Y";
                    $done     = "+" . fn_fvalue(100*$s/$p-100, 0);
                    $left     = 100;
                    $right    = fn_fvalue(100*$s/$p, 0);
                }elseif ($p == $s) {
                    $done     = 100;
                    $left     = 100;
                    $right    = 0;
                }
            }elseif ($p == 0 and $s > 0){
                $ovf      = "Y";
                $done     = "+" . fn_fvalue(100*$s/1, 0);
                $left     = 0;
                $right    = fn_fvalue(100*$s/1, 0);

            }elseif ($p > 0 and $s == 0){
                $done     = 0;
                $left     = 0;
                $right    = 0;
            }elseif ($p == 0 and $s == 0){
                $done     = 0;
                $left     = 0;
                $right    = 0;
            }

            $percs[$id]["done"]     = $done;
            $percs[$id]["left"]     = $left;
            $percs[$id]["right"]    = $right;
            $percs[$id]["ovf"]      = $ovf;

            if ($left > 0 and $right == 0){
                $percs[$id]["d"]    = $left;
                $percs[$id]["o"]    = 0;
                $percs[$id]["title"]= "Выполнено $done%";

            }elseif ($left == 0 and $right > 0){
                $percs[$id]["d"]    = 100;
                $percs[$id]["o"]    = 0;
                $percs[$id]["title"]= "Перевыполнение на $done%";

            }elseif ($left > 0 and $right > 0){
                $percs[$id]["d"]    = 100;
                $percs[$id]["o"]    = 0;
                $percs[$id]["title"]= "Перевыполнение на $done%";

            }
        }



        $view->assign('percs', $percs);
    }
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
