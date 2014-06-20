<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);
$pl = new plan_of_sales();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if ($mode == "analysis_of_details"){
        fn_print_r($_REQUEST);
    }
}

// Общие шаблоны
$months = array(1=>"янв", "фев", "мар", "апр", "май", "июн", "июл", "авг", "сен", "окт", "ноя", "дек");
$view->assign('months', $months);
$data = array();

if ($mode == "manage") {
    if (is__more_0($_REQUEST["month"], $_REQUEST["year"], $_REQUEST["months_supply"], fn_parse_date($_REQUEST["current_day"]))){
        $view->assign('search', $_REQUEST);
        $data["month"]         = $_REQUEST["month"];
        $data["year"]          = $_REQUEST["year"];
        $data["months_supply"] = $_REQUEST["months_supply"];
        $data["current_day"]   = $_REQUEST["current_day"];

        //======================================================================
        // 0. Предопределение текущего и следующего месяца
        //======================================================================
        $php_curr_month = strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1");
        $php_next_month = strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1 +1 month");
        $view->assign("tpl_curr_month", $months[date("n", $php_curr_month)] . "." . date("y", $php_curr_month));
        $view->assign("tpl_next_month", $months[date("n", $php_next_month)] . "." . date("y", $php_next_month));
        $data["tpl_curr_month"] = $months[date("n", $php_curr_month)] . "." . date("y", $php_curr_month);
        $data["tpl_next_month"] = $months[date("n", $php_next_month)] . "." . date("y", $php_next_month);


        //======================================================================
        // 1. СЕРИИ НАСОСОВ
        //======================================================================
        list($pump_series) = fn_uns__get_pump_series(array('only_active' => true,'group_by_types'=>true, "view_in_plans"=>"Y",));
        $view->assign('pump_series', $pump_series);
        $data["pump_series"] = $pump_series;


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
            $requirement["curr_month"][$id] = $v["ukr_curr"]+$v["exp_curr"];
            $requirement["next_month"][$id] = $v["ukr_next"]+$v["exp_next"];
        }
        $requirement["curr_month"]["total"] = array_sum($requirement["curr_month"]);
        $requirement["next_month"]["total"] = array_sum($requirement["next_month"]);
        $view->assign("requirement", $requirement);
        $data["requirement"] = $requirement;


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
        $data["sgp"]    = $sgp;


        //======================================================================
        // 3/1. ПОЛУЧЕНИЕ БАЛАНСА ГОТОВОЙ ПРОДУКЦИИ ПО СЕРИЯМ НА ТЕКУЩИЙ ДЕНЬ
        //======================================================================
        $p = array(
            "time_from"                 => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1" . " 00:00:00"),
            "time_to"                   => strtotime(date("Y-m-d", fn_parse_date($_REQUEST["current_day"])) . " 23:59:59")            ,
            "total_balance_of_details"  => "Y",
        );
        list($balances) = fn_uns__get_balance_sgp($p, true, true, true);
        $pumps      = array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id, "only_active"=>true,)));
        $sgp_current_day = array();
        if (is__array($balances)){
            foreach ($balances as $type){
                foreach ($type as $group){
                    foreach ($group["items"] as $id=>$v){
                        $sgp_current_day[$pumps[$id]["ps_id"]] += $v["konech"];
                    }
                }
            }
        }
        $sgp_current_day["total"] = array_sum($sgp_current_day);
        $view->assign("sgp_current_day", $sgp_current_day);
        $data["sgp_current_day"]    = $sgp_current_day;


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
        $data["initial_production_plan"]    = $initial_production_plan;


        //======================================================================
        // 5/6. РАСЧЕТ ЗАДЕЛА ОЖИДАЮЩЕГО СБОРКУ
        //======================================================================
        // Это те партии, которые открыты и не имеют активных документов "ВЫПУСК НАСОСОВ"
        // на расчетный день
        // todo очень плохая реализация - запрашиваются сразу все партии

        $p = array(
            "with_doc_type_VN"          => true,
        );
        $kits       = array_shift(fn_acc__get_kits($p));
        $end        = strtotime(date("Y-m-d", fn_parse_date($_REQUEST["current_day"])) . " 23:59:59");
        $begin      = strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1" . " 00:00:00");

        // Расчет  задела ожидающего сборку ------------------------------------
        $kits_zadel = array();
        foreach ($kits as $kit_id=>$v){
            if ($v["date_open"]<=$end and (!isset($v["date_close"]) or $v["date_close"]>$end or (isset($v["date_close"]) and $v["status"] != "Z"))){
                $kits_zadel[$kit_id] = $v;
            }
        }
        $zadel = array();
        foreach ($kits_zadel as $kit_id=>$v){
            $zadel[$pumps[$v["p_id"]]["ps_id"]] += $v["p_quantity"];
            if (is__array(($v["VN"]))){
                foreach ($v["VN"] as $pump_item){
                    $zadel[$pumps[$v["p_id"]]["ps_id"]] -= $pump_item["quantity"];
                }
            }
        }
        $zadel["total"] = array_sum($zadel);
        $view->assign("zadel", $zadel);
        $data["zadel"]    = $zadel;

        // Расчет  выполнено ---------------------------------------------------
        $kits_done = array();
        foreach ($kits as $kit_id=>$v){
            if ($v["date_open"]<=$end
                and
                (is__more_0($v["date_close"]) and $v["date_close"]>=$begin and $v["date_close"]<=$end)
            ){
                $kits_done[$kit_id] = $v;
            }
        }
        $done = array();
        foreach ($kits_done as $kit_id=>$v){
            foreach ($v["VN"] as $pump_item){
                $done[$pumps[$v["p_id"]]["ps_id"]] += $pump_item["quantity"];
            }
        }
        $done["total"] = array_sum($done);
        $view->assign("done", $done);
        $data["done"]    = $done;


        //======================================================================
        // 3/2. РАСЧЕТ ЗАПРЕТА НА ИЗГОТОВЛЕНИЕ НАСОСА
        //======================================================================
        $prohibition = array();
        list($pump_series) = fn_uns__get_pump_series(array("only_active" => true, "view_in_plans"=>"Y",));
        foreach ($pump_series as $ps_id=>$ps){
            // трехмесячные(1.5) или четырехмесячные(2) продажи
            $s = ($_REQUEST["months_supply"]*0.5)*($requirement["curr_month"][$ps_id] + $requirement["next_month"][$ps_id]);
            $prohibition[$ps_id] = (($sgp_current_day[$ps_id]+$zadel[$ps_id])>=fn_fvalue($s,0))?"Y":"N";
        }
        $view->assign("prohibition", $prohibition);
        $data["prohibition"]    = $prohibition;

        //======================================================================
        // 7. РАСЧЕТ "ОСТАЛОСЬ"
        //======================================================================
        $remaining_production_plan = array();
        foreach ($pump_series as $id=>$ps){
            // тек. месяц ------------------------------------------------------
            $curr_month = ($zadel[$id]+$done[$id])-$initial_production_plan["curr_month"][$id];
            if ($curr_month >=0){
                $remaining_production_plan["curr_month"][$id] = 0;
            }else{
                $remaining_production_plan["curr_month"][$id] = abs($curr_month);
            }

            // след. месяц -----------------------------------------------------
            if ($curr_month >=0){
                if (($curr_month - $initial_production_plan["next_month"][$id]) >= 0){
                    $remaining_production_plan["next_month"][$id] = 0;
                }else{
                    $remaining_production_plan["next_month"][$id] = abs($curr_month - $initial_production_plan["next_month"][$id]);
                }
            }else{
                $remaining_production_plan["next_month"][$id] = abs(0 - $initial_production_plan["next_month"][$id]);
            }
        }
        $remaining_production_plan ["curr_month"]["total"] = array_sum($remaining_production_plan ["curr_month"]);
        $remaining_production_plan ["next_month"]["total"] = array_sum($remaining_production_plan ["next_month"]);
        $view->assign("remaining_production_plan", $remaining_production_plan );
        $data["remaining_production_plan"]    = $remaining_production_plan;

        //======================================================================
        // 8. Упрощенное формирование сслыки
        //======================================================================
        $analysis_links = array();
        foreach ($pump_series as $id=>$ps){
            $analysis_links[$id]["id"]      = $id;
            $analysis_links[$id]["name"]    = $ps["ps_name"];

            $href = array(
                "ps_id"             => $id,
                "ps_name"           => $ps["ps_name"],
                "month"             => $_REQUEST["month"],
                "year"              => $_REQUEST["year"],
                "current_day"       => fn_parse_date($_REQUEST["current_day"]),
                "tpl_curr_month"    => $months[date("n", $php_curr_month)] . "." . date("y", $php_curr_month),
                "tpl_next_month"    => $months[date("n", $php_next_month)] . "." . date("y", $php_next_month),
                "rpp_curr_month"    => $remaining_production_plan["curr_month"][$id],
                "rpp_next_month"    => $remaining_production_plan["next_month"][$id],
                "sgp"               => $sgp[$id],
                "sgp_current_day"   => $sgp_current_day[$id],
                "zadel"             => ($zadel[$id])?$zadel[$id]:0,
                "done"              => ($done[$id])?$done[$id]:0,
            );
            $analysis_links[$id]["href"]    = "uns_plan_of_mech_dep.analysis_of_pump?" . http_build_query($href);
        }
        $view->assign("analysis_links", $analysis_links);
        $data["analysis_links"]    = $analysis_links;


        // СОХРАНЕНИЕ ДАННЫХ В СЕССИЮ
        $_SESSION["uns_plan_of_mech_dep"] = $data;
    }
}


if ($mode == "analysis_of_details"){
    if (!is__array($_SESSION["uns_plan_of_mech_dep"])) return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    $data = $_SESSION["uns_plan_of_mech_dep"];
    $view->assign("data", $data);

    $ps_ids = null;
//    switch ($action){
//        case "allowance":
//            foreach ($data["prohibition"] as $k=>$v){
//                if ($v == "N") $ps_ids[] = $k;
//            }
//            break;
//
//        case "prohibition":
//            foreach ($data["prohibition"] as $k=>$v){
//                if ($v == "Y") $ps_ids[] = $k;
//            }
//            break;
//
//        case "all":
//            $ps_ids = array_keys($data["prohibition"]);
//            break;
//    }

    // =========================================================================
    // 1. Отобрать насосы, которые стоят в плане.
    // =========================================================================
    // ОСТАЛОСЬ (тек. + след.) - (ЗАДЕЛ + ВЫПОЛНЕНО) > 0, если "ДА", то этот насос включить в анализ
    $analysis_ps_ids = null;
    list($pump_series) = fn_uns__get_pump_series(array("only_active" => true, "view_in_plans"=>"Y",));
    foreach ($pump_series as $ps_id=>$ps){
        $diff = /*3*0.5**/($data["remaining_production_plan"]["curr_month"][$ps_id] + $data["remaining_production_plan"]["next_month"][$ps_id])
              /*- ($data["zadel"][$ps_id] + $data["done"][$ps_id])*/;
        if ($diff > 0){
            $analysis_ps_ids[$ps_id]["remaining"] = $diff;
        }
    }
//    $analysis_ps_ids = array_slice($analysis_ps_ids, 0, 2, true);
    $view->assign("pump_series", $pump_series);
    $view->assign("pump_series_by_type", $data["pump_series"]);


    // =========================================================================
    // 2. ПОЛУЧИТЬ СПИСОК ДЕТАЛЕЙ ИДУЩИЕ НА ЭТИ ПЛАНОВЫЕ НАСОСЫ
    // =========================================================================
    foreach ($analysis_ps_ids as $ps_id=>$ps){
        $pump = array_shift(array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id))));
        $set = fn_uns__get_packing_list_by_pump($pump["p_id"], "D", true);
        list($details) = fn_uns__get_details(array("detail_id"=>array_keys($set), "with_material_info" => true, "with_material_info" => true,));
        // Объединить данные
        foreach ($details as $k=>$v){
            $details[$k] = array_merge($details[$k], $set[$k]);
        }

        // БАЛАНС ПО ДЕТАЛЯМ ===================================================
        $p = array();
        $p["time_from"] = $data["current_day"];
        $p["time_to"]   = $data["current_day"];
        list ($p['time_from'], $p['time_to']) = fn_create_periods($p);
        $p["detail_id"] = $p["item_id"] = array_keys($details);
        $p["check_dcat_id"]      = false;
        list($balances) = fn_uns__get_balance_mc_sk_su($p, true, true, false);
        if (is__array($balances)){
            foreach ($balances as $o_id=>$groups){
                foreach ($groups as $g){
                    foreach ($g["items"] as $item_id=>$i){
                        $details[$item_id]["balance"][$o_id]["processing_konech"]   = $i["processing_konech"];
                        $details[$item_id]["balance"][$o_id]["complete_konech"]     = $i["complete_konech"];
                        $details[$item_id]["balance"][$o_id]["konech"]              = $i["konech"];
                    }
                }
            }
        }

        // БАЛАНС ПО ОТЛИВКАМ ==================================================
        $material_id = array();
        foreach ($details as $data){
            if ($data["mclass_id"] == 1){
                $material_id[] = $data["material_id"];
            }
        }

        $p = array(
            "plain"         => false,
            "all"           => true,
            "o_id"          => array(8),  // Склад литья
            "item_type"     => "M",
            "add_item_info" => false,
            "view_all_position" => "Y",
            "mclass_id"     => 1,
            "item_id"       => $material_id,
        );
        $p["time_from"] = $data["current_day"];
        $p["time_to"]   = $data["current_day"];
        list ($p['time_from'], $p['time_to']) = fn_create_periods($p);
        list($balances) = fn_uns__get_balance($p);
        foreach ($details as $d_id=>$data){
            if ($data["mclass_id"] == 1){
                $details[$d_id]["balance_material"] = fn_fvalue($balances[$data["material_id"]]["ko"]);
            }
        }
        $analysis_ps_ids[$ps_id]["pump"] = $pump;
        $analysis_ps_ids[$ps_id]["details"] = $details;

    }
    $view->assign("analisys_of_pumps", $analysis_ps_ids);
}


//==============================================================================
// АНАЛИЗ ИНДИВИДУАЛЬНО ПО НАСОСУ
if (defined('AJAX_REQUEST') and  $mode == "analysis_of_pump"){
    $pumps = array_shift(fn_uns__get_pumps(array("ps_id"=>$_REQUEST["ps_id"])));
    if (is__array($pumps)){
        foreach ($pumps as $p_id=>$p){
            $set = fn_uns__get_packing_list_by_pump($p_id, "D", true);
            list($details) = fn_uns__get_details(array("detail_id"=>array_keys($set), "with_material_info" => true));
            foreach ($details as $k=>$v){
                $details[$k] = array_merge($details[$k], $set[$k]);
            }

            // БАЛАНС ПО ДЕТАЛЯМ
            $p = array();
            $p["time_from"] = $_REQUEST["current_day"];
            $p["time_to"]   = $_REQUEST["current_day"];
            list ($p['time_from'], $p['time_to']) = fn_create_periods($p);
            $p["detail_id"] = $p["item_id"] = array_keys($details);
            $p["check_dcat_id"]      = false;
            list($balances) = fn_uns__get_balance_mc_sk_su($p, true, true, false);
            if (is__array($balances)){
                foreach ($balances as $o_id=>$groups){
                    foreach ($groups as $g){
                        foreach ($g["items"] as $item_id=>$i){
                            $details[$item_id]["balance"][$o_id]["processing_konech"]   = $i["processing_konech"];
                            $details[$item_id]["balance"][$o_id]["complete_konech"]     = $i["complete_konech"];
                            $details[$item_id]["balance"][$o_id]["konech"]              = $i["konech"];
                        }
                    }
                }
            }

            // БАЛАНС ПО ОТЛИВКАМ
            $material_id = array();
            foreach ($details as $data){
                if ($data["mclass_id"] == 1){
                    $material_id[] = $data["material_id"];
                }
            }

            $p = array(
                "plain"         => false,
                "all"           => true,
                "o_id"          => array(8),  // Склад литья
                "item_type"     => "M",
                "add_item_info" => false,
                "view_all_position" => "Y",
                "mclass_id"     => 1,
                "item_id"       => $material_id,
            );
            $p["time_from"] = $_REQUEST["current_day"];
            $p["time_to"]   = $_REQUEST["current_day"];
            list ($p['time_from'], $p['time_to']) = fn_create_periods($p);
            list($balances) = fn_uns__get_balance($p);
            foreach ($details as $d_id=>$data){
                if ($data["mclass_id"] == 1){
                    $details[$d_id]["balance_material"] = fn_fvalue($balances[$data["material_id"]]["ko"]);
                }
            }

            // -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-
            $pumps[$p_id]["details"] = $details;
        }
    }
    $view->assign("pumps", $pumps);
    $view->assign("data", $_REQUEST);
}


function fn_uns_plan_of_mech_dep__search ($controller){
    $params = array(
        "month",
        "year",
        "months_supply",
        "current_day",
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}
