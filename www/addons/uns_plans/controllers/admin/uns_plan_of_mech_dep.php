<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);
$pl = new plan_of_sales();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    return;
}

// Общие шаблоны
$months = array(1=>"Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
$view->assign('months', $months);

if ($mode == "manage") {
    if (is__more_0($_REQUEST["month"], $_REQUEST["year"], fn_parse_date($_REQUEST["current_day"]))){
        $view->assign('search', $_REQUEST);


        //======================================================================
        // 1. СЕРИИ НАСОСОВ
        //======================================================================
        list($pump_series) = fn_uns__get_pump_series(array('only_active' => true,'group_by_types'=>true, "view_in_plans"=>"Y",));
        $view->assign('pump_series', $pump_series);


        //======================================================================
        // 2. ПОЛУЧЕНИЕ ПЛАНОВОЙ ПОТРЕБНОСТИ НА ВЫБРАННЫЙ МЕСЯЦ
        //======================================================================
        $p = array(
            "with_count"        => true,
            "with_sum"          => true,
            "with_items"        => true,
            "group_by_item"     => true,
        );
        $p = array_merge($_REQUEST, $p);
        $plan = array_shift(array_shift(fn_uns__get_plans($p)));
        $requirement = array();
        foreach ($plan["group_by_item"]["S"] as $id=>$v){
            $requirement["curr_month"][$id] = $v["quantity"];
            $requirement["next_month"][$id] = $v["quantity_add"]-$v["quantity"];
        }
        $requirement["curr_month"]["total"] = array_sum($requirement["curr_month"]);
        $requirement["next_month"]["total"] = array_sum($requirement["next_month"]);
        $view->assign("requirement", $requirement);


        //======================================================================
        // 3. ПОЛУЧЕНИЕ БАЛАНСА ГОТОВОЙ ПРОДУКЦИИ ПО СЕРИЯМ НА ПЕРВОЕ ЧИСЛО ВЫБРАННОГО МЕСЯЦА
        //======================================================================
        $p = array(
            "time_from"                 => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1" . " 00:00:00"),
            "time_to"                   => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1" . " 00:00:01"),
            "total_balance_of_details"  => "Y",
        );
        list($balances) = fn_uns__get_balance_sgp($p, true, true, true);
        $pumps      = array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id, "only_active"=>true,)));
        $sgp = array();
        if (is__array($balances)){
            foreach ($balances as $type){
                foreach ($type as $group){
                    foreach ($group["items"] as $id=>$v){
                        $sgp[$pumps[$id]["ps_id"]] += $v["konech"];
                    }
                }
            }
        }
        $sgp["total"] = array_sum($sgp);
        $view->assign("sgp", $sgp);


        //======================================================================
        // 4. РАСЧЕТ НАЧАЛЬНОГО ПЛАНА ПРОИЗВОДСТВА
        //======================================================================
        $initial_production_plan = array();
        list($pump_series) = fn_uns__get_pump_series(array("only_active" => true, "view_in_plans"=>"Y",));
        foreach ($pump_series as $id=>$ps){
            // тек. месяц
            $curr_month = $sgp[$id]-$requirement["curr_month"][$id];
            if ($curr_month >=0){
                $initial_production_plan["curr_month"][$id] = 0;
            }else{
                $initial_production_plan["curr_month"][$id] = abs($curr_month);
            }

            // след. месяц
            if ($curr_month >=0){
                if (($curr_month - $requirement["next_month"][$id]) >= 0){
                    $initial_production_plan["next_month"][$id] = 0;
                }else{
                    $initial_production_plan["next_month"][$id] = abs($curr_month - $requirement["next_month"][$id]);
                }
            }else{
                $initial_production_plan["next_month"][$id] = abs(0 - $requirement["next_month"][$id]);
            }

        }
        $initial_production_plan["curr_month"]["total"] = array_sum($initial_production_plan["curr_month"]);
        $initial_production_plan["next_month"]["total"] = array_sum($initial_production_plan["next_month"]);
        $view->assign("initial_production_plan", $initial_production_plan);


        //======================================================================
        // 5. РАСЧЕТ ЗАДЕЛА ОЖИДАЮЩЕГО СБОРКУ
        //======================================================================

    }
}


function fn_uns_plan_of_mech_dep__search ($controller){
    $params = array(
        "month",
        "year",
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}
