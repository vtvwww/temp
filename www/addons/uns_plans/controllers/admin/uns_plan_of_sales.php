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

    // обновить план продаж по конкретному насосу со страницы ПЛАН ПРОИЗВОДСТВА
    if($mode == 'update_ps_plan'){
        fn_uns__upd_plan_item($_REQUEST["month"], $_REQUEST["year"], $_REQUEST['data']);
//        if($id !== false){
//            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
//        }
//        fn_delete_notification('changes_saved');
//        $suffix = "update&plan_id={$id}&selected_section={$_REQUEST['selected_section']}";
        return array(CONTROLLER_STATUS_REDIRECT, "uns_plan_of_mech_dep" . "." . "manage"."?mark_id=".$_REQUEST["data"]["item_id"]."#".$_REQUEST["data"]["item_id"]);

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

                    }elseif ($_REQUEST["item_type"] == "D"){
                        $p = array(
                            'only_active' => true,
                            'group_by_categories'=>true,
                        );
                        list($category_details) = fn_uns__get_details($p);
                        $view->assign("f_type", "select_by_group");
                        $view->assign("f_options", "details");
                        $view->assign("f_option_id", "detail_id");
                        $view->assign("f_option_value", "detail_name");
                        $view->assign("f_optgroups", $category_details);
                        $view->assign("f_optgroup_label", "dcat_name");
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
    if (!is__more_0($_REQUEST["month"], $_REQUEST["year"]/*, $_REQUEST["week_supply"]*/)){
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
//    $ps_id = array(76,66); // только К8/18 --- К290/30

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
    list($pump_series, $sales, $sales_ukr_exp, $analysis, $plan, $ps_order) = $pl->calculation($params);
    $view->assign('pump_series',    $pump_series);
    $view->assign('sales',          $sales);
//    $view->assign('$sales_ukr_exp', $sales_ukr_exp);
    $view->assign('analysis',       $analysis);
    $view->assign('plan',           $plan);
    $view->assign('ps_order',       $ps_order);
    $view->assign('search',         $params);
    $view->assign('search',         $_REQUEST);

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

    // Детали по категориям
    list($category_details) = fn_uns__get_details(array('only_active' => true,'group_by_categories'=>true,));
    $view->assign('category_details', $category_details);
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
        $sales = fn_uns__get_sales_pump_series_by_period(array_keys($pump_series), strtotime($begin), strtotime($end), explode("_", $_REQUEST["select_sgp"]));
        $view->assign('sales', $sales);

        // ПОЛУЧИТЬ ПРОЦЕНТЫ ВЫПОЛНЕНИЯ ПЛАНА ПРОДАЖ
        $percs = array();
        foreach (array_keys($pump_series) as $id){
            $p = (int) ($plan["group_by_item"]["S"][$id]["ukr_curr"] + $plan["group_by_item"]["S"][$id]["exp_curr"]);
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

if ($mode == "edit_sales" and defined('AJAX_REQUEST')) {
    //--------------------------------------------------------------------------
    // ПОЛУЧЕНИЕ СТАТИСТИКИ/ГРАФИКА ПРОДАЖ
    //--------------------------------------------------------------------------
    $graphs_key = substr(md5(microtime().mt_rand()), 0, 10);
    $params = array(
        "ps_id"                         => $_REQUEST["item_id"],
        "month"                         => $_REQUEST["month"],
        "year"                          => $_REQUEST["year"],
        "week_supply"                   => 0,
        "koef_plan_prodazh"             => 0,
        "graphs_key"                    => $graphs_key,
        "display_sale_of_current_month" => true,
    );

    list($pump_series, $sales, $sales_ukr_exp, $analysis, $plan, $ps_order) = $pl->calculation($params);
    $view->assign('pump_series',    $pump_series);
    $view->assign('sales',          $sales);
    $view->assign('analysis',       $analysis);
    $view->assign('plan',           $plan);
    $view->assign('ps_order',       $ps_order);
    $view->assign('search',         $_REQUEST);
    $view->assign('ps_id',          $_REQUEST["item_id"]);
    $view->assign("graphs_key",     $graphs_key);

    //--------------------------------------------------------------------------
    // СПИСОК ИМЕЮЩИХСЯ ЗАКАЗОВ ПО ВЫБРАННОЙ СЕРИИ НАСОСОВ
    //--------------------------------------------------------------------------
    list($customers) = fn_uns__get_customers();
    $view->assign('customers', $customers);

    $p = array(
        "sorting_schemas"   => "view_in_sgp",
        "only_active"       => true,
        "group_orders"      => "UKR",
        "remaining_time"    => true,
        "info_RO"           => true,
        "full_info"         => true,
        "with_items"        => true,
        "item_type"         => array("P", "PF", "PA"),
        "item_id"           => db_get_fields(UNS_DB_PREFIX."SELECT p_id FROM ?:pumps WHERE ps_id = ?i", $_REQUEST["item_id"]),
    );
    list($orders, $search) = fn_acc__get_orders(array_merge($_REQUEST, $p));

    // Убираем пустые заказы
    if (is__array($orders)){
        foreach ($orders as $o_id=>$o){
            if (!is__array($o["items"])) unset($orders[$o_id]);
        }
    }

    // orders_ukr
    $view->assign("orders_ukr", $orders["ukr"]);

    // orders_exp
    unset($orders["ukr"]);
    $view->assign("orders_exp", $orders);

/*    $orders = null;
    $p = array(
        "with_items"            =>true,
        "remaining_time"        =>true,
        "date_finished_begin"   => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1"),             // Временной диапазон текущего и следующего месяца начало
        "date_finished_end"     => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1 +2 month")-1,  // Временной диапазон текущего и следующего месяца конец
        "sorting_schemas"       => "view_in_sgp",
    );
    $all_orders = array_shift(fn_acc__get_orders($p));
    if (is__array($all_orders)){
        foreach ($all_orders as $order_id=>$order){
            foreach ($order["items"] as $oi_id=>$item){
                if (in_array($item["item_type"], array("P", "PF", "PA")) and $item["ps_id"] == $_REQUEST["item_id"]){
                    $ukr_exp = ($customers[$order["customer_id"]]["to_export"] == "Y")?"EXP":"UKR";
                    $orders[$ukr_exp][$order_id]["comment"]             = $order["comment"];
                    $orders[$ukr_exp][$order_id]["status"]              = $order["status"];
                    $orders[$ukr_exp][$order_id]["date_updated"]        = $order["date_updated"];
                    $orders[$ukr_exp][$order_id]["date_finished"]       = $order["date_finished"];
                    $orders[$ukr_exp][$order_id]["remaining_time"]      = $order["remaining_time"];
                    $orders[$ukr_exp][$order_id]["customer_id"]         = $order["customer_id"];
                    $orders[$ukr_exp][$order_id]["customer_name"]       = $customers[$order["customer_id"]]["name"];
                    $orders[$ukr_exp][$order_id]["customer_short_name"] = $customers[$order["customer_id"]]["name_short"];
                    $orders[$ukr_exp][$order_id]["items"][$oi_id]       = $item;
                }
            }
        }
    }*/
//    fn_print_r($orders);
    $view->assign('orders', $orders);

    //--------------------------------------------------------------------------
    // ПРОДАЖИ ЗА ТЕКУЩИЙ МЕСЯЦ
    //--------------------------------------------------------------------------
    list($pumps) = fn_uns__get_pumps(array("only_active"=>true, "ps_id"=>$_REQUEST["item_id"],));
    $params = array(
        "time_from"     => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1"),
        "time_to"       => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1 +1 month")-1,
        "type"          => 7,               // Расходные ордера
        "o_id"          => 19,   // Склад готовой продукции АЛЕКСАНДРИЯ
        "only_active"   => true,
        "item_type"     => array("P", "PF", "PA"),
        "item_id"       => array_keys($pumps),
        "with_items"    => true,
        "info_category" => false,
        "info_item"     => true,
        "info_unit"     => false,
        "sorting_schemas"=>"view_asc",
    );
    list($docs) = fn_uns__get_documents($params);
//    fn_print_r($docs);

    $sales = null;
    $pumps_keys = array_keys($pumps);
    foreach ($docs as $document_id=>$d){
        foreach ($d["items"] as $di_id=>$item){
            if (in_array($item["item_type"], array("P", "PF", "PA")) and in_array($item["item_id"], $pumps_keys)){
                $ukr_exp = ($customers[$d["customer_id"]]["to_export"] == "Y")?"EXP":"UKR";
                $sales[$ukr_exp][$document_id]["date"]                = $d["date"];
                $sales[$ukr_exp][$document_id]["customer_id"]         = $d["customer_id"];
                $sales[$ukr_exp][$document_id]["customer_name"]       = $customers[$d["customer_id"]]["name"];
                $sales[$ukr_exp][$document_id]["customer_short_name"] = $customers[$d["customer_id"]]["name_short"];
                $sales[$ukr_exp][$document_id]["items"][$di_id]       = $item;
            }
        }
    }
    $view->assign('sales', $sales);
    $view->assign('pumps', $pumps);
//    fn_print_r($sales, $pumps);


    //--------------------------------------------------------------------------
    // ДАННЫЕ ДЛЯ РЕДАКТИРОВАНИЯ ПЛАНА ПРОДАЖ
    //--------------------------------------------------------------------------
    if (is__more_0($_REQUEST["month"], $_REQUEST["year"], $_REQUEST["item_id"])){
        $sql =db_quote("
                SELECT
                  ?:_plan_items.pi_id,
                  ?:_plan_items.item_id,
                  ?:_plan_items.item_type,
                  ?:_plan_items.ukr_curr,
                  ?:_plan_items.ukr_next,
                  ?:_plan_items.exp_curr,
                  ?:_plan_items.exp_next,
                  ?:_plan_items.forced_status
                FROM ?:_plan_items
                  LEFT JOIN ?:_plans ON (?:_plans.plan_id = ?:_plan_items.plan_id)
                WHERE
                      ?:_plans.month = ?i
                  and ?:_plans.year = ?i
                  and ?:_plan_items.item_type = ?s
                  and ?:_plan_items.item_id = ?i
                  ",
            $_REQUEST["month"],
            $_REQUEST["year"],
            $_REQUEST["item_type"],
            $_REQUEST["item_id"]
        );
        $ps_plan = db_get_row(UNS_DB_PREFIX . $sql);

        $view->assign('ps_plan',    $ps_plan);
        $view->assign('item_id',    $_REQUEST["item_id"]);
        $view->assign('item_type',  $_REQUEST["item_type"]);
        $view->assign('month',      $_REQUEST["month"]);
        $view->assign('year',       $_REQUEST["year"]);

        $php_curr_month = strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1");
        $php_next_month = strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1 +1 month");
        $curr_month     = $months[date("n", $php_curr_month)]  . " " . date("Y", $php_curr_month);
        $next_month     = $months[date("n", $php_next_month)]  . " " . date("Y", $php_next_month);
        $view->assign("tpl_curr_month",  $curr_month);
        $view->assign("tpl_next_month",  $next_month);
    }
}




function fn_uns_plan_of_sales__search ($controller){
    $params = array(
        "month",
        "year",
        "week_supply",
        "years_for_analysis",
        "koef_plan_prodazh",
        "select_sgp",
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}
