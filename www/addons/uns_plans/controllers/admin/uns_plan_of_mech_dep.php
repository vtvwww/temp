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
$months_roman = array(1=>"I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
$months = array(1=>"янв", "фев", "мар", "апр", "май", "июн", "июл", "авг", "сен", "окт", "ноя", "дек");
$view->assign('months', $months);
$months_full = array(1=>"январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь");
$view->assign('months_full', $months_full);

$data = array();


//==============================================================================
// РАСЧЕТ ОБЩЕГО ПЛАНА ПРОИЗВОДСТВА
//==============================================================================
if ($mode == "manage") {
    if (is__more_0($_REQUEST["month"], $_REQUEST["year"], $_REQUEST["months_supply"], fn_parse_date($_REQUEST["current_day"]))){
        $_REQUEST["type_of_production_plan"] = ($_REQUEST["type_of_production_plan"] == "actual")?"actual":"parties";
        $_REQUEST["analisys_of_production_plan"] = "Y"; // Анализ плана принудительно всегда!
        $view->assign('search', $_REQUEST);
        $data["month"]                  = $_REQUEST["month"];
        $data["year"]                   = $_REQUEST["year"];
        $data["months_supply"]          = $_REQUEST["months_supply"];
        $data["current_day"]            = $_REQUEST["current_day"];
        $data["type_of_production_plan"]= $_REQUEST["type_of_production_plan"];

        //======================================================================
        // 0. ПРЕДОПРЕДЕЛЕНИЕ ТЕКУЩЕГО И СЛЕДУЮЩЕГО МЕСЯЦА
        //======================================================================
        $php_curr_month  = strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1");
        $php_next_month  = strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1 +1 month");
        $php_next2_month = strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1 +2 month");
        $php_next3_month = strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1 +3 month");

        // Представление в арабских цифрах
        $curr_month_arab    = $months[date("n", $php_curr_month)]  . "." . date("y", $php_curr_month);
        $next_month_arab    = $months[date("n", $php_next_month)]  . "." . date("y", $php_next_month);
        $next2_month_arab   = $months[date("n", $php_next2_month)] . "." . date("y", $php_next2_month);
        $next3_month_arab   = $months[date("n", $php_next3_month)] . "." . date("y", $php_next3_month);
        $data["tpl_curr_month"] =   $curr_month_arab;
        $data["tpl_next_month"] =   $next_month_arab;
        $data["tpl_next2_month"] =  $next2_month_arab;
        $data["tpl_next3_month"] =  $next3_month_arab;
        $view->assign("tpl_curr_month",  $curr_month_arab);
        $view->assign("tpl_next_month",  $next_month_arab);
        $view->assign("tpl_next2_month", $next2_month_arab);
        $view->assign("tpl_next3_month", $next3_month_arab);

        // Представление в римских цифрах
        $curr_month_roman["month"]  = $months[date("n", $php_curr_month)];
        $curr_month_roman["year"]   = date("Y", $php_curr_month);
        $next_month_roman["month"]  = $months[date("n", $php_next_month)];
        $next_month_roman["year"]   = date("Y", $php_next_month);
        $next2_month_roman["month"] = $months[date("n", $php_next2_month)];
        $next2_month_roman["year"]  = date("Y", $php_next2_month);
        $next3_month_roman["month"] = $months[date("n", $php_next3_month)];
        $next3_month_roman["year"]  = date("Y", $php_next3_month);

        $data["tpl_curr_month_roman"]   = $curr_month_roman;
        $data["tpl_next_month_roman"]   = $next_month_roman;
        $data["tpl_next2_month_roman"]  = $next2_month_roman;
        $data["tpl_next3_month_roman"]  = $next3_month_roman;
        $view->assign("tpl_curr_month_roman",  $curr_month_roman);
        $view->assign("tpl_next_month_roman",  $next_month_roman);
        $view->assign("tpl_next2_month_roman", $next2_month_roman);
        $view->assign("tpl_next3_month_roman", $next3_month_roman);


        //======================================================================
        // 1. СЕРИИ НАСОСОВ
        //======================================================================
        list($pump_series) = fn_uns__get_pump_series(array("only_active" => true, "group_by_types"=>true, "view_in_plans"=>"Y",));
        $view->assign("pump_series", $pump_series);
        $data["pump_series"] = $pump_series;

        //======================================================================
        // 1.1. ОПРЕДЕЛЕНИЕ ВЕСА СЕРИЙ НАСОСОВ
        //======================================================================
        $ps_weight = null;
        $weights = null;
        $pumps = array_shift(fn_uns__get_pumps(array('only_active' => true,)));
        if (is__array($pumps)){
            foreach ($pumps as $p_id=>$p){
                if (!is__more_0($ps_weight[$p["ps_id"]])){
                    $ps_weight[$p["ps_id"]] = fn_fvalue($p["weight_p"]);
                }
            }
        }
        $view->assign('ps_weight', $ps_weight);

        //======================================================================
        // 2. ПОЛУЧЕНИЕ ПЛАНОВОЙ ПОТРЕБНОСТИ НА ВЫБРАННЫЙ МЕСЯЦ
        //======================================================================
        $pump_series = array_shift(fn_uns__get_pump_series(array("only_active" => true, "view_in_plans"=>"Y",)));
        $p = array(
            "with_count"        => true,
            "with_sum"          => true,
            "with_items"        => true,
            "group_by_item"     => true,
        );
        $p = array_merge($_REQUEST, $p);
        $plan = array_shift(array_shift(fn_uns__get_plans($p)));
        $data["plan"] = $plan;
        $view->assign("plan", $plan);
        $requirement = array();
        foreach ($plan["group_by_item"]["S"] as $id=>$v){ // Отбор только по насосам
            if  (!is__array($pump_series[$id])) continue;
            // +0 мес. = тек. мес. - план продаж
            $requirement["curr_month"][$id]  = $v["ukr_curr"]+$v["exp_curr"];

            // +1 мес. = след. мес. - план продаж
            $requirement["next_month"][$id]  = $v["ukr_next"]+$v["exp_next"];

            // +2 мес. = след. след. мес. - среднее (тек. мес. и след. мес.)
            $requirement["next2_month"][$id] = floor(($requirement["curr_month"][$id]+$requirement["next_month"][$id])/2);

            // +3 мес. = 50% от +2 мес.
            $requirement["next3_month"][$id] = floor(0.5*$requirement["next2_month"][$id]);

            // ВЕС
            $weights["requirement"]["curr_month"][$id]  = $ps_weight[$id]*$requirement["curr_month"][$id];
            $weights["requirement"]["next_month"][$id]  = $ps_weight[$id]*$requirement["next_month"][$id];
            $weights["requirement"]["next2_month"][$id] = $ps_weight[$id]*$requirement["next2_month"][$id];
            $weights["requirement"]["next3_month"][$id] = $ps_weight[$id]*$requirement["next3_month"][$id];
        }
        $requirement["curr_month"]["total"] = array_sum($requirement["curr_month"]);
        $requirement["next_month"]["total"] = array_sum($requirement["next_month"]);
        $requirement["next2_month"]["total"] = array_sum($requirement["next2_month"]);
        $requirement["next3_month"]["total"] = array_sum($requirement["next3_month"]);

        $view->assign("requirement", $requirement);
        $data["requirement"] = $requirement;


        //======================================================================
        // 3. ПОЛУЧЕНИЕ БАЛАНСА ГОТОВОЙ ПРОДУКЦИИ ПО СЕРИЯМ НА ПЕРВОЕ ЧИСЛО ВЫБРАННОГО МЕСЯЦА и на тек. день
        //======================================================================
        $p = array(
            "time_from"                 => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1" . " 00:00:00"),
            "time_to"                   => strtotime(date("Y-m-d", fn_parse_date($_REQUEST["current_day"])) . " 23:59:59"),
            "total_balance_of_details"  => "Y",
            "without_sets_of_details"   => true, // не учитывать насосные комплекты: корпус в сборе или ротор в сборе
        );
        list($balances) = fn_uns__get_balance_sgp($p, true, true, true);
        $pumps      = array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id, "only_active"=>true,)));
        $sgp = array();
        $sgp_current_day = array();
        if (is__array($balances)){
            foreach ($balances as $type){
                foreach ($type as $group){
                    foreach ($group["items"] as $id=>$v){
                        $sgp[$pumps[$id]["ps_id"]] += $v["nach"];
                        $weights["sgp"][$pumps[$id]["ps_id"]]  += $ps_weight[$pumps[$id]["ps_id"]]*$v["nach"];

                        $sgp_current_day[$pumps[$id]["ps_id"]] += $v["konech"];
                        $weights["sgp_current_day"][$pumps[$id]["ps_id"]]  += $ps_weight[$pumps[$id]["ps_id"]]*$v["konech"];
                    }
                }
            }
        }

        $sgp["total"] = array_sum($sgp);
        $sgp_current_day["total"] = array_sum($sgp_current_day);

        $view->assign("sgp", $sgp);
        $view->assign("sgp_current_day", $sgp_current_day);

        $data["sgp"]    = $sgp;
        $data["sgp_current_day"]    = $sgp_current_day;


        //======================================================================
        // 4. РАСЧЕТ НАЧАЛЬНОГО ПЛАНА ПРОИЗВОДСТВА НАСОСОВ
        //======================================================================
        $initial_production_plan = array();
        $initial_production_plan_parties = array();
        list($pump_series) = fn_uns__get_pump_series(array("only_active" => true, "view_in_plans"=>"Y",));
        foreach ($pump_series as $id=>$ps){
            // тек. мес.
            $deficit_curr = ($requirement["curr_month"][$id]) - $sgp[$id];
            if ($deficit_curr < 0) $deficit_curr = 0;

            // след. мес.
            $deficit_next = ($requirement["curr_month"][$id]+$requirement["next_month"][$id]) - $sgp[$id] - $deficit_curr;
            if ($deficit_next < 0) $deficit_next = 0;

            // след. след. мес.
            $deficit_next2 = ($requirement["curr_month"][$id]+$requirement["next_month"][$id]+$requirement["next2_month"][$id]) - $sgp[$id] - $deficit_curr - $deficit_next;
            if ($deficit_next2 < 0) $deficit_next2 = 0;

            // след. след. след. мес.
            $deficit_next3 = ($requirement["curr_month"][$id]+$requirement["next_month"][$id]+$requirement["next2_month"][$id]+$requirement["next3_month"][$id]) - $sgp[$id] - $deficit_curr - $deficit_next - $deficit_next2;
            if ($deficit_next3 < 0) $deficit_next3 = 0;

            $weights["initial_production_plan"]["curr_month"][$id]  = $ps_weight[$id]*$deficit_curr;
            $weights["initial_production_plan"]["next_month"][$id]  = $ps_weight[$id]*$deficit_next;
            $weights["initial_production_plan"]["next2_month"][$id] = $ps_weight[$id]*$deficit_next2;
            $weights["initial_production_plan"]["next3_month"][$id] = $ps_weight[$id]*$deficit_next3;

            $initial_production_plan["curr_month"][$id]     = $deficit_curr;
            $initial_production_plan["next_month"][$id]     = $deficit_next;
            $initial_production_plan["next2_month"][$id]    = $deficit_next2;
            $initial_production_plan["next3_month"][$id]    = $deficit_next3;

            // =================================================================
            // Расчет кратности партий =========================================
            // =================================================================
            $party_min  = $ps["party_size_min"];
            $party_max  = $ps["party_size_max"];
            $party_step = $ps["party_size_step"];
            $total = $deficit_curr + $deficit_next + $deficit_next2 + $deficit_next3;

            $curr_month = 0;
            $next_month = 0;
            $next2_month = 0;
            $next3_month = 0;

            //------------------------------------------------------------------
            // 1).  0 -  0 -  0 -  0
            if ($deficit_curr == 0 and $deficit_next == 0 and $deficit_next2 == 0 and $deficit_next3 == 0){
            }


            //------------------------------------------------------------------
            // 2).  0 -  0 -  0 - !0
            if ($deficit_curr == 0 and $deficit_next == 0 and $deficit_next2 == 0 and $deficit_next3 > 0){
                if ($party_min >= $total){
                    $next3_month = $party_min;

                }elseif ($party_max >= $total){
                    for ($party_curr = $party_min; $party_curr <= $party_max; $party_curr += $party_step){
                        if ($party_curr >= $total){
                            $next3_month = $party_curr;
                            break;
                        }
                    }

                }else{
                    $next3_month = $party_max;
                }
            }


            //------------------------------------------------------------------
            // 3).  0 -  0 - !0 - *
            if ($deficit_curr == 0 and $deficit_next == 0 and $deficit_next2 > 0){
                if ($party_min >= $total){
                    $next2_month = $party_min;

                }elseif ($party_max >= $total){
                    for ($party_curr = $party_min; $party_curr <= $party_max; $party_curr += $party_step){
                        if ($party_curr >= $total){
                            $next2_month = $party_curr;
                            break;
                        }
                    }

                }elseif ($party_max + $party_min >= $total){
                    $next2_month = $party_max;
                    $next3_month = $party_min;

                }elseif ($party_max + $party_max >= $total){
                    for ($party_curr = $party_min; $party_curr <= $party_max; $party_curr += $party_step){
                        if ($party_max + $party_curr >= $total){
                            $next2_month = $party_max;
                            $next3_month = $party_curr;
                            break;
                        }
                    }

                }else{
                    $next2_month = $party_max;
                    $next3_month = $party_max;
                }
            }


            //------------------------------------------------------------------
            // 4).  0 -  !0 -  * -  *
            if ($deficit_curr == 0 and $deficit_next > 0){
                if ($party_min >= $total){
                    $next_month = $party_min;

                }elseif ($party_max >= $total){
                    for ($party_curr = $party_min; $party_curr <= $party_max; $party_curr += $party_step){
                        if ($party_curr >= $total){
                            $next_month = $party_curr;
                            break;
                        }
                    }

                }elseif ($party_max + $party_min >= $total){
                    $next_month  = $party_max;
                    $next2_month = $party_min;

                }elseif ($party_max + $party_max >= $total){
                    for ($party_curr = $party_min; $party_curr <= $party_max; $party_curr += $party_step){
                        if ($party_max + $party_curr >= $total){
                            $next_month = $party_max;
                            $next2_month = $party_curr;
                            break;
                        }
                    }

                }elseif ($party_max + $party_max + $party_min >= $total){
                    $next_month  = $party_max;
                    $next2_month = $party_max;
                    $next3_month = $party_min;

                }elseif ($party_max + $party_max + $party_max >= $total){
                    for ($party_curr = $party_min; $party_curr <= $party_max; $party_curr += $party_step){
                        if ($party_max + $party_max + $party_curr >= $total){
                            $next_month  = $party_max;
                            $next2_month = $party_max;
                            $next3_month = $party_curr;
                            break;
                        }
                    }

                }else{
                    $next_month  = $party_max;
                    $next2_month = $party_max;
                    $next3_month = $party_max;
                }
            }

            //------------------------------------------------------------------
            // 5).  !0 -  * -  * -  *
            if ($deficit_curr > 0){
                if ($party_min >= $total){
                    $curr_month = $party_min;

                }elseif ($party_max >= $total){
                    for ($party_curr = $party_min; $party_curr <= $party_max; $party_curr += $party_step){
                        if ($party_curr >= $total){
                            $curr_month = $party_curr;
                            break;
                        }
                    }

                }elseif ($party_max + $party_min >= $total){
                    $curr_month  = $party_max;
                    $next_month  = $party_min;

                }elseif ($party_max + $party_max >= $total){
                    for ($party_curr = $party_min; $party_curr <= $party_max; $party_curr += $party_step){
                        if ($party_max + $party_curr >= $total){
                            $curr_month = $party_max;
                            $next_month = $party_curr;
                            break;
                        }
                    }

                }elseif ($party_max + $party_max + $party_min >= $total){
                    $curr_month  = $party_max;
                    $next_month  = $party_max;
                    $next2_month = $party_min;

                }elseif ($party_max + $party_max + $party_max >= $total){
                    for ($party_curr = $party_min; $party_curr <= $party_max; $party_curr += $party_step){
                        if ($party_max + $party_max + $party_curr >= $total){
                            $curr_month  = $party_max;
                            $next_month  = $party_max;
                            $next2_month = $party_curr;
                            break;
                        }
                    }

                }elseif ($party_max + $party_max + $party_max + $party_min >= $total){
                    $curr_month  = $party_max;
                    $next_month  = $party_max;
                    $next2_month = $party_max;
                    $next3_month = $party_min;

                }elseif ($party_max + $party_max + $party_max + $party_max >= $total){
                    for ($party_curr = $party_min; $party_curr <= $party_max; $party_curr += $party_step){
                        if ($party_max + $party_max + $party_max + $party_curr >= $total){
                            $curr_month  = $party_max;
                            $next_month  = $party_max;
                            $next2_month = $party_max;
                            $next3_month = $party_curr;
                            break;
                        }
                    }

                }else{
                    $curr_month  = $party_max;
                    $next_month  = $party_max;
                    $next2_month = $party_max;
                    $next3_month = $party_max;
                }

            }

            $initial_production_plan_parties["curr_month"][$id]     = $curr_month;
            $initial_production_plan_parties["next_month"][$id]     = $next_month;
            $initial_production_plan_parties["next2_month"][$id]    = $next2_month;
            $initial_production_plan_parties["next3_month"][$id]    = $next3_month;

            $weights["initial_production_plan_parties"]["curr_month"][$id]  = $ps_weight[$id]*$curr_month;
            $weights["initial_production_plan_parties"]["next_month"][$id]  = $ps_weight[$id]*$next_month;
            $weights["initial_production_plan_parties"]["next2_month"][$id] = $ps_weight[$id]*$next2_month;
            $weights["initial_production_plan_parties"]["next3_month"][$id] = $ps_weight[$id]*$next3_month;
        }
        $initial_production_plan["curr_month"]["total"] = array_sum($initial_production_plan["curr_month"]);
        $initial_production_plan["next_month"]["total"] = array_sum($initial_production_plan["next_month"]);
        $initial_production_plan["next2_month"]["total"] = array_sum($initial_production_plan["next2_month"]);
        $initial_production_plan["next3_month"]["total"] = array_sum($initial_production_plan["next3_month"]);

        $initial_production_plan_parties["curr_month"]["total"] = array_sum($initial_production_plan_parties["curr_month"]);
        $initial_production_plan_parties["next_month"]["total"] = array_sum($initial_production_plan_parties["next_month"]);
        $initial_production_plan_parties["next2_month"]["total"] = array_sum($initial_production_plan_parties["next2_month"]);
        $initial_production_plan_parties["next3_month"]["total"] = array_sum($initial_production_plan_parties["next3_month"]);

        $view->assign("initial_production_plan", $initial_production_plan);
        $data["initial_production_plan"]    = $initial_production_plan;
        $view->assign("initial_production_plan_parties", $initial_production_plan_parties);
        $data["initial_production_plan_parties"]    = $initial_production_plan_parties;


        //======================================================================
        // 5/6. РАСЧЕТ ПАРТИЙ В "ЗАДЕЛЕ" И В "ВЫПОЛНЕНО"
        //======================================================================
        $pumps      = array_shift(fn_uns__get_pumps(array("only_active"=>true, "without_sets_of_details" => true,)));
        $pumps_ids  = array_keys($pumps);

        //----------------------------------------------------------------------
        // НА 1-е ЧИСЛО МЕСЯЦА
        // Расчет в заделе -----------------------------------------------------
        $p = array(
            "tracking_type"             => "zadel",
            "tracking_time_begin"       => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1" . " 00:00:00")-2,
            "tracking_time_end"         => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1" . " 00:00:00")-1,
            "with_doc_type_VN"          => true,
        );
        $kits_zadel = array_shift(fn_acc__get_kits($p));
        $zadel = array();
        foreach ($kits_zadel as $kit_id=>$v){
            if (in_array($v["p_id"], $pumps_ids)){
                $ps_id = $pumps[$v["p_id"]]["ps_id"];
                $zadel[$ps_id] += $v["p_quantity"];
                $weights["zadel"][$ps_id]  += $ps_weight[$ps_id]*$v["p_quantity"];
                if (is__array(($v["VN"]))){
                    foreach ($v["VN"] as $pump_item){
                        $zadel[$ps_id] -= $pump_item["quantity"];
                        $weights["zadel"][$ps_id]  -= $ps_weight[$ps_id]*$pump_item["quantity"];
                    }
                }
            }
        }

        // Расчет в выполнено --------------------------------------------------
        $p["tracking_type"] = "done"; // Получить те партии, которые "собраны"
        $kits_done = array_shift(fn_acc__get_kits($p));
        $done = array();
        foreach ($kits_done as $kit_id=>$v){
            if (in_array($v["p_id"], $pumps_ids)){
                $ps_id = $pumps[$v["p_id"]]["ps_id"];
                if (is__array(($v["VN"]))){
                    foreach ($v["VN"] as $pump_item){
                        $done[$ps_id] += $pump_item["quantity"];
                        $weights["done"][$ps_id]  += $ps_weight[$ps_id]*$pump_item["quantity"];
                    }
                }
            }
        }
        $zadel["total"]     = array_sum($zadel);
        $done["total"]      = array_sum($done);
        $data["zadel"]      = $zadel;
        $data["done"]       = $done;
        $view->assign("zadel",  $zadel);
        $view->assign("done",   $done);

        //----------------------------------------------------------------------
        // НА тек. ЧИСЛО МЕСЯЦА
        // Расчет в заделе -----------------------------------------------------
        $p = array(
            "tracking_type"             => "zadel",
            "tracking_time_begin"       => strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1" . " 00:00:00"),
            "tracking_time_end"         => strtotime(date("Y-m-d", fn_parse_date($_REQUEST["current_day"])) . " 23:59:59"),
            "with_doc_type_VN"          => true,
        );
        $kits_zadel = array_shift(fn_acc__get_kits($p));
        $zadel_current_day = array();
        foreach ($kits_zadel as $kit_id=>$v){
            if (in_array($v["p_id"], $pumps_ids)){
                $ps_id = $pumps[$v["p_id"]]["ps_id"];
                $zadel_current_day[$ps_id] += $v["p_quantity"];
                $weights["zadel"][$ps_id]  += $ps_weight[$ps_id]*$v["p_quantity"];
                if (is__array(($v["VN"]))){
                    foreach ($v["VN"] as $pump_item){
                        $zadel_current_day[$ps_id] -= $pump_item["quantity"];
                        $weights["zadel"][$ps_id]  -= $ps_weight[$ps_id]*$pump_item["quantity"];
                    }
                }
            }
        }

        // Расчет в выполнено --------------------------------------------------
        $p["tracking_type"] = "done"; // Получить те партии, которые "собраны"
        $kits_done = array_shift(fn_acc__get_kits($p));
        $done_current_day = array();
        foreach ($kits_done as $kit_id=>$v){
            if (in_array($v["p_id"], $pumps_ids)){
                $ps_id = $pumps[$v["p_id"]]["ps_id"];
                if (is__array($v["VN"])){
                    foreach ($v["VN"] as $pump_item){
                        $done_current_day[$ps_id] += $pump_item["quantity"];
                        $weights["done"][$ps_id]  += $ps_weight[$ps_id]*$pump_item["quantity"];
                    }
                }
            }
        }
        $zadel_current_day["total"]     = array_sum($zadel_current_day);
        $done_current_day["total"]      = array_sum($done_current_day);
        $data["zadel_current_day"]      = $zadel_current_day;
        $data["done_current_day"]       = $done_current_day;
        $view->assign("zadel_current_day",  $zadel_current_day);
        $view->assign("done_current_day",   $done_current_day);


        //======================================================================
        // 3/2. РАСЧЕТ ЗАПРЕТА НА ИЗГОТОВЛЕНИЕ НАСОСА
        //======================================================================
        $prohibition = array();
        list($pump_series) = fn_uns__get_pump_series(array("only_active" => true, "view_in_plans"=>"Y",));
        foreach ($pump_series as $ps_id=>$ps){
            // трехмесячные(1.5) или четырехмесячные(2) продажи
            $s = floor(($_REQUEST["months_supply"]*0.5)*($requirement["curr_month"][$ps_id] + $requirement["next_month"][$ps_id]));
            if (($sgp_current_day[$ps_id]+$zadel_current_day[$ps_id])>=$s){
                $prohibition[$ps_id] = "Y";
            }
        }
        $view->assign("prohibition", $prohibition);
        $data["prohibition"]    = $prohibition;

        //======================================================================
        // 7. РАСЧЕТ "ОСТАЛОСЬ"
        //======================================================================
        // на 1-е число 00:00
        $remaining_production_plan = array();
        $remaining_production_plan_parties = array();
        foreach ($pump_series as $id=>$ps){
            $balance = $zadel[$id]+$done[$id];
            //------------------------------------------------------------------
            // ФАКТИЧЕСКИЙ ПЛАН ПРОИЗВОДСТВА
            //------------------------------------------------------------------
            // тек. мес.
            $deficit_curr = ($initial_production_plan["curr_month"][$id]) - $balance;
            if ($deficit_curr < 0) $deficit_curr = 0;

            // след. мес.
            $deficit_next = ($initial_production_plan["curr_month"][$id]+$initial_production_plan["next_month"][$id]) - $balance - $deficit_curr;
            if ($deficit_next < 0) $deficit_next = 0;

            // след. след. мес.
            $deficit_next2 = ($initial_production_plan["curr_month"][$id]+$initial_production_plan["next_month"][$id]+$initial_production_plan["next2_month"][$id]) - $balance - $deficit_curr - $deficit_next;
            if ($deficit_next2 < 0) $deficit_next2 = 0;

            // след. след. след. мес.
            $deficit_next3 = ($initial_production_plan["curr_month"][$id]+$initial_production_plan["next_month"][$id]+$initial_production_plan["next2_month"][$id]+$initial_production_plan["next3_month"][$id]) - $balance - $deficit_curr - $deficit_next - $deficit_next2;
            if ($deficit_next3 < 0) $deficit_next3 = 0;

            $remaining_production_plan["curr_month"][$id] = $deficit_curr;
            $remaining_production_plan["next_month"][$id] = $deficit_next;
            $remaining_production_plan["next2_month"][$id] = $deficit_next2;
            $remaining_production_plan["next3_month"][$id] = $deficit_next3;
            $weights["remaining_production_plan"]["curr_month"][$id]  = $ps_weight[$id]*$deficit_curr;
            $weights["remaining_production_plan"]["next_month"][$id]  = $ps_weight[$id]*$deficit_next;
            $weights["remaining_production_plan"]["next2_month"][$id] = $ps_weight[$id]*$deficit_next2;
            $weights["remaining_production_plan"]["next3_month"][$id] = $ps_weight[$id]*$deficit_next3;


            //------------------------------------------------------------------
            // ПАРТИЙНЫЙ ПЛАН ПРОИЗВОДСТВА
            //------------------------------------------------------------------
            // тек. мес.
            $deficit_curr = ($initial_production_plan_parties["curr_month"][$id]) - $balance;
            if ($deficit_curr < 0) $deficit_curr = 0;

            // след. мес.
            $deficit_next = ($initial_production_plan_parties["curr_month"][$id]+$initial_production_plan_parties["next_month"][$id]) - $balance - $deficit_curr;
            if ($deficit_next < 0) $deficit_next = 0;

            // след. след. мес.
            $deficit_next2 = ($initial_production_plan_parties["curr_month"][$id]+$initial_production_plan_parties["next_month"][$id]+$initial_production_plan_parties["next2_month"][$id]) - $balance - $deficit_curr - $deficit_next;
            if ($deficit_next2 < 0) $deficit_next2 = 0;

            // след. след. след. мес.
            $deficit_next3 = ($initial_production_plan_parties["curr_month"][$id]+$initial_production_plan_parties["next_month"][$id]+$initial_production_plan_parties["next2_month"][$id]+$initial_production_plan_parties["next3_month"][$id]) - $balance - $deficit_curr - $deficit_next - $deficit_next2;
            if ($deficit_next3 < 0) $deficit_next3 = 0;

            $remaining_production_plan_parties["curr_month"][$id] = $deficit_curr;
            $remaining_production_plan_parties["next_month"][$id] = $deficit_next;
            $remaining_production_plan_parties["next2_month"][$id] = $deficit_next2;
            $remaining_production_plan_parties["next3_month"][$id] = $deficit_next3;
            $weights["remaining_production_plan_parties"]["curr_month"][$id]  = $ps_weight[$id]*$deficit_curr;
            $weights["remaining_production_plan_parties"]["next_month"][$id]  = $ps_weight[$id]*$deficit_next;
            $weights["remaining_production_plan_parties"]["next2_month"][$id] = $ps_weight[$id]*$deficit_next2;
            $weights["remaining_production_plan_parties"]["next3_month"][$id] = $ps_weight[$id]*$deficit_next3;
        }
        $remaining_production_plan["curr_month"]["total"] = array_sum($remaining_production_plan ["curr_month"]);
        $remaining_production_plan["next_month"]["total"] = array_sum($remaining_production_plan ["next_month"]);
        $remaining_production_plan["next2_month"]["total"] = array_sum($remaining_production_plan ["next2_month"]);
        $remaining_production_plan["next3_month"]["total"] = array_sum($remaining_production_plan ["next3_month"]);
        $view->assign("remaining_production_plan", $remaining_production_plan );
        $data["remaining_production_plan"]    = $remaining_production_plan;

        $remaining_production_plan_parties["curr_month"]["total"] = array_sum($remaining_production_plan_parties ["curr_month"]);
        $remaining_production_plan_parties["next_month"]["total"] = array_sum($remaining_production_plan_parties ["next_month"]);
        $remaining_production_plan_parties["next2_month"]["total"] = array_sum($remaining_production_plan_parties ["next2_month"]);
        $remaining_production_plan_parties["next3_month"]["total"] = array_sum($remaining_production_plan_parties ["next3_month"]);
        $view->assign("remaining_production_plan_parties", $remaining_production_plan_parties);
        $data["remaining_production_plan_parties"]    = $remaining_production_plan_parties;

        // на тек. число 23:59
        $remaining_production_plan_current_day = array();
        $remaining_production_plan_parties_current_day = array();
        foreach ($pump_series as $id=>$ps){
            $balance = $zadel_current_day[$id]+$done_current_day[$id];
            //------------------------------------------------------------------
            // ФАКТИЧЕСКИЙ ПЛАН ПРОИЗВОДСТВА
            //------------------------------------------------------------------
            // тек. мес.
            $deficit_curr = ($initial_production_plan["curr_month"][$id]) - $balance;
            if ($deficit_curr < 0) $deficit_curr = 0;

            // след. мес.
            $deficit_next = ($initial_production_plan["curr_month"][$id]+$initial_production_plan["next_month"][$id]) - $balance - $deficit_curr;
            if ($deficit_next < 0) $deficit_next = 0;

            // след. след. мес.
            $deficit_next2 = ($initial_production_plan["curr_month"][$id]+$initial_production_plan["next_month"][$id]+$initial_production_plan["next2_month"][$id]) - $balance - $deficit_curr - $deficit_next;
            if ($deficit_next2 < 0) $deficit_next2 = 0;

            // след. след. след. мес.
            $deficit_next3 = ($initial_production_plan["curr_month"][$id]+$initial_production_plan["next_month"][$id]+$initial_production_plan["next2_month"][$id]+$initial_production_plan["next3_month"][$id]) - $balance - $deficit_curr - $deficit_next - $deficit_next2;
            if ($deficit_next3 < 0) $deficit_next3 = 0;

            $remaining_production_plan_current_day["curr_month"][$id] = $deficit_curr;
            $remaining_production_plan_current_day["next_month"][$id] = $deficit_next;
            $remaining_production_plan_current_day["next2_month"][$id] = $deficit_next2;
            $remaining_production_plan_current_day["next3_month"][$id] = $deficit_next3;
            $weights["remaining_production_plan_current_day"]["curr_month"][$id]  = $ps_weight[$id]*$deficit_curr;
            $weights["remaining_production_plan_current_day"]["next_month"][$id]  = $ps_weight[$id]*$deficit_next;
            $weights["remaining_production_plan_current_day"]["next2_month"][$id] = $ps_weight[$id]*$deficit_next2;
            $weights["remaining_production_plan_current_day"]["next3_month"][$id] = $ps_weight[$id]*$deficit_next3;


            //------------------------------------------------------------------
            // ПАРТИЙНЫЙ ПЛАН ПРОИЗВОДСТВА
            //------------------------------------------------------------------
            // тек. мес.
            $deficit_curr = ($initial_production_plan_parties["curr_month"][$id]) - $balance;
            if ($deficit_curr < 0) $deficit_curr = 0;

            // след. мес.
            $deficit_next = ($initial_production_plan_parties["curr_month"][$id]+$initial_production_plan_parties["next_month"][$id]) - $balance - $deficit_curr;
            if ($deficit_next < 0) $deficit_next = 0;

            // след. след. мес.
            $deficit_next2 = ($initial_production_plan_parties["curr_month"][$id]+$initial_production_plan_parties["next_month"][$id]+$initial_production_plan_parties["next2_month"][$id]) - $balance - $deficit_curr - $deficit_next;
            if ($deficit_next2 < 0) $deficit_next2 = 0;

            // след. след. след. мес.
            $deficit_next3 = ($initial_production_plan_parties["curr_month"][$id]+$initial_production_plan_parties["next_month"][$id]+$initial_production_plan_parties["next2_month"][$id]+$initial_production_plan_parties["next3_month"][$id]) - $balance - $deficit_curr - $deficit_next - $deficit_next2;
            if ($deficit_next3 < 0) $deficit_next3 = 0;

            $remaining_production_plan_parties_current_day["curr_month"][$id] = $deficit_curr;
            $remaining_production_plan_parties_current_day["next_month"][$id] = $deficit_next;
            $remaining_production_plan_parties_current_day["next2_month"][$id] = $deficit_next2;
            $remaining_production_plan_parties_current_day["next3_month"][$id] = $deficit_next3;
            $weights["remaining_production_plan_parties_current_day"]["curr_month"][$id]  = $ps_weight[$id]*$deficit_curr;
            $weights["remaining_production_plan_parties_current_day"]["next_month"][$id]  = $ps_weight[$id]*$deficit_next;
            $weights["remaining_production_plan_parties_current_day"]["next2_month"][$id] = $ps_weight[$id]*$deficit_next2;
            $weights["remaining_production_plan_parties_current_day"]["next3_month"][$id] = $ps_weight[$id]*$deficit_next3;
        }
        $remaining_production_plan_current_day["curr_month"]["total"] = array_sum($remaining_production_plan_current_day ["curr_month"]);
        $remaining_production_plan_current_day["next_month"]["total"] = array_sum($remaining_production_plan_current_day ["next_month"]);
        $remaining_production_plan_current_day["next2_month"]["total"] = array_sum($remaining_production_plan_current_day ["next2_month"]);
        $remaining_production_plan_current_day["next3_month"]["total"] = array_sum($remaining_production_plan_current_day ["next3_month"]);
        $view->assign("remaining_production_plan_current_day", $remaining_production_plan_current_day );
        $data["remaining_production_plan_current_day"]    = $remaining_production_plan_current_day;

        $remaining_production_plan_parties_current_day["curr_month"]["total"] = array_sum($remaining_production_plan_parties_current_day ["curr_month"]);
        $remaining_production_plan_parties_current_day["next_month"]["total"] = array_sum($remaining_production_plan_parties_current_day ["next_month"]);
        $remaining_production_plan_parties_current_day["next2_month"]["total"] = array_sum($remaining_production_plan_parties_current_day ["next2_month"]);
        $remaining_production_plan_parties_current_day["next3_month"]["total"] = array_sum($remaining_production_plan_parties_current_day ["next3_month"]);
        $view->assign("remaining_production_plan_parties_current_day", $remaining_production_plan_parties_current_day);
        $data["remaining_production_plan_parties_current_day"]    = $remaining_production_plan_parties_current_day;

        $view->assign("weights", $weights);

        //**********************************************************************
        // РАСЧЕТЫ ДЛЯ АНАЛИЗА ПРОДАЖ
        //**********************************************************************
        $pump_series = array_shift(fn_uns__get_pump_series(array('only_active' => true, "view_in_plans"=>"Y",)));
        $begin  = strtotime($_REQUEST["year"] . "-" . $_REQUEST["month"] . "-" . "1" . " 00:00:00");
        $end    = strtotime(date("Y-m-d", fn_parse_date($_REQUEST["current_day"])) . " 23:59:59");
        $sales = fn_uns__get_sales_pump_series_by_period(array_keys($pump_series), $begin, $end); // По СГП
        $view->assign("sales", $sales);
        if ($_REQUEST["analisis_of_sales"] == "Y"){
            //SALE_PROGRESS
            $sales_tpl = null;
            foreach ($pump_series as $ps_id=>$ps){
                // расчет процентов
                $plan_curr_month = $plan["group_by_item"]["S"][$ps_id]["ukr_curr"]+$plan["group_by_item"]["S"][$ps_id]["exp_curr"];
                if ($plan_curr_month == 0){
                    $perc = $sales[$ps_id]*100;
                }elseif ($sales[$ps_id] == 0){
                    $perc = 0;
                }else{
                    $perc = 100*$sales[$ps_id]/$plan_curr_month;
                }

                // расчет положения прогрессбара
                $progress_bar = -150;
                if ($perc <= 200) $progress_bar = -100 + $perc/2;

                $sales_tpl[$ps_id]["perc"] = $perc;
                $sales_tpl[$ps_id]["bar"]  = $progress_bar;
            }
            $view->assign("sales_tpl", $sales_tpl);
        }

        //**********************************************************************
        // РАСЧЕТЫ ДЛЯ АНАЛИЗА ПЛАНА
        //**********************************************************************
        if ($_REQUEST["analisys_of_production_plan"] == "Y"){
            // ОБЩЕЕ КОЛ-ВО ДНЕЙ В ВЫБРАННЫЕ МЕСЯЦА
            $curr_d = date("j", fn_parse_date($_REQUEST["current_day"]));
            $curr_m = date("m", fn_parse_date($_REQUEST["current_day"]));
            $curr_y = date("Y", fn_parse_date($_REQUEST["current_day"]));
            $total_days = date("t", strtotime($curr_y . "-" . $curr_m . "-" . "1"))
                        + date("t", strtotime($curr_y . "-" . $curr_m . "-" . "1" . " +1 month"))
                        + date("t", strtotime($curr_y . "-" . $curr_m . "-" . "1" . " +2 month"));

            $analisys = null;
            $month_px = 51;
            foreach ($pump_series as $ps_id=>$ps){
                $max_day = 0;
                $t = $sgp_current_day[$ps_id]+$sales[$ps_id];
                $z = $zadel_current_day[$ps_id];

                $a = $requirement["curr_month"][$ps_id];
                $b = $requirement["next_month"][$ps_id];
                $c = $requirement["next2_month"][$ps_id];

                $_TOTAL = fn_uns_calc_progress ($a, $b, $c, $t);
                if ($t > 0 and $_TOTAL["OSTALOS"]["a"] == 0 and $_TOTAL["OSTALOS"]["b"] == 0 and $_TOTAL["OSTALOS"]["c"] == 0){
                    $analisys[$ps_id]["total"] = fn_fvalue(100*($month_px * 3)/(3*$month_px), 2);
                }else{
                    $analisys[$ps_id]["total"] = fn_fvalue(100*($month_px * ($_TOTAL["ZAPOLNENIE"]["a"]/$a + $_TOTAL["ZAPOLNENIE"]["b"]/$b + $_TOTAL["ZAPOLNENIE"]["c"]/$c)/(3*$month_px)), 2);
                    $_ZADEL = fn_uns_calc_progress ($_TOTAL["OSTALOS"]["a"], $_TOTAL["OSTALOS"]["b"], $_TOTAL["OSTALOS"]["c"], $z);
                    $analisys[$ps_id]["zadel"] = fn_fvalue(100*($month_px * ($_ZADEL["ZAPOLNENIE"]["a"]/$a + $_ZADEL["ZAPOLNENIE"]["b"]/$b + $_ZADEL["ZAPOLNENIE"]["c"]/$c)/(3*$month_px)), 2);

                    // Проверка если вообще нет насоса!
                    if (!is__more_0($analisys[$ps_id]["total"] + $analisys[$ps_id]["zadel"])){
                        $analisys[$ps_id]["zero"] = 1;
                    }
                    $analisys[$ps_id]["none"] = 100-($analisys[$ps_id]["total"]+$analisys[$ps_id]["zadel"]+$analisys[$ps_id]["zero"]);
                }

                // Для установки приоритета по кол-ву остатка насосов на СГП и ЗАДЕЛА
                $max_day = floor($total_days*($analisys[$ps_id]["total"]+$analisys[$ps_id]["zadel"])/100);
                $analisys[$ps_id]["priority"]["date"]   = date("d/m/Y", strtotime("$curr_y-$curr_m-1 +$max_day day"));
                $analisys[$ps_id]["priority"]["left"]   = $max_day-$curr_d;
                if ($max_day-$curr_d <= 7){
                    $analisys[$ps_id]["priority"]["status"] = "R";

                }elseif ($max_day-$curr_d > 7 and $max_day-$curr_d <= 14){
                    $analisys[$ps_id]["priority"]["status"] = "R2";

                }elseif ($max_day-$curr_d > 14 and $max_day-$curr_d <= 28){
                    $analisys[$ps_id]["priority"]["status"] = "Y";

                }else{
                    $analisys[$ps_id]["priority"]["status"] = "n";

                }
            }
            $data["analisys"] = $analisys;
//            fn_print_r($analisys);
            $view->assign("analisys", $analisys);
        }

        // СОХРАНЕНИЕ ДАННЫХ В СЕССИЮ
        $_SESSION["uns_plan_of_mech_dep"] = $data;
        unset($_SESSION["balance_of_details"]);
        unset($_SESSION["balance_of_casts"]);
    }
}

//==============================================================================
// ПОДЕТАЛЬНЫЙ АНАЛИЗ НАСОСОВ
//==============================================================================
if ($mode == "analysis_of_pumps"){

    // 0. ПОЛУЧЕНИЕ ДАННЫХ ИЗ СЕССИИ
    if (!is__array($_SESSION["uns_plan_of_mech_dep"])) return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    $data = $_SESSION["uns_plan_of_mech_dep"];
    $view->assign("data", $data);

    // 1. ПОЛУЧИТЬ СПИСОК СЕРИЙ НАСОСОВ ДЛЯ АНАЛИЗА
    list($pump_series) = fn_uns__get_pump_series(array("only_active" => true, "view_in_plans"=>"Y",));
    $view->assign("ppump_series", $pump_series);
    $ps_ids = null;
    switch ($action){
        case "pump":                // 1. Выполнить анализ одной серии насосов, отображая все входящие в него насосы
            if (is__more_0($_REQUEST["ps_id"])) $ps_ids[] = $_REQUEST["ps_id"];
            break;

        case "allowance":     // 2. Выполнить анализ РАЗРЕШЕННЫХ насосов
            foreach ($pump_series as $ps_id=>$ps){
                if ($data["prohibition"][$ps_id] != "Y"){
                    $ps_ids[] = $ps_id;
                }
            }
            break;

        case "prohibition":   // 3. Выполнить анализ ЗАПРЕЩЕННЫХ насосов
            $ps_ids = array_keys($data["prohibition"]);
            break;

        case "all":           // 4. Выполнить анализ ВСЕХ насосов
            $ps_ids = array_keys($pump_series);
            break;
    }

    if (is_null($ps_ids)) return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");

    // 2. БАЛАНС ПО ДЕТАЛЯМ ====================================================
    if (is__array($_SESSION["balance_of_details"])){
        $balance_of_details = $_SESSION["balance_of_details"];
        $accessory_of_details = $_SESSION["accessory_of_details"];
    }else{
        $p = array();
        $p["accessory_pumps"] = "Y";
        $p["time_from"] = $data["current_day"];
        $p["time_to"]   = $data["current_day"];
        list ($p['time_from'], $p['time_to']) = fn_create_periods($p);
        $p["check_dcat_id"]      = false;
        list($balances) = fn_uns__get_balance_mc_sk_su($p, true, true, false);
        if (is__array($balances)){
            foreach ($balances as $o_id=>$groups){
                foreach ($groups as $g){
                    foreach ($g["items"] as $item_id=>$i){
                        $balance_of_details[$item_id][$o_id]["processing_konech"]   = $i["processing_konech"];
                        $balance_of_details[$item_id][$o_id]["complete_konech"]     = $i["complete_konech"];
                        $balance_of_details[$item_id][$o_id]["konech"]              = $i["konech"];

                        switch ($i["accessory_view"]){
                            case "P": $accessory_of_details[$item_id] = $i["accessory_pumps"];      break;
                            case "S": $accessory_of_details[$item_id] = $i["accessory_pump_series"];break;
                            case "M": $accessory_of_details[$item_id] = $i["accessory_pump_manual"];break;
                        }
                    }
                }
            }
        }
        $_SESSION["balance_of_details"] = $balance_of_details;
        $_SESSION["accessory_of_details"] = $accessory_of_details;
    }
    $view->assign("balance_of_details", $balance_of_details);
    $view->assign("accessory_of_details", $accessory_of_details);


    // 3. БАЛАНС ПО ОТЛИВКАМ ===================================================
    if (is__array($_SESSION["balance_of_casts"])){
        $balance_of_casts = $_SESSION["balance_of_casts"];
        $accessory_of_casts = $_SESSION["accessory_of_casts"];
    }else{
        $p = array(
            "plain"         => false,
            "all"           => true,
            "o_id"          => array(8),  // Склад литья
            "item_type"     => "M",
            "add_item_info" => true,
            "view_all_position" => "Y",
            "mclass_id"     => 1,
            "accessory_pumps"=> true,
        );
        $p["time_from"] = $data["current_day"];
        $p["time_to"]   = $data["current_day"];
        list ($p['time_from'], $p['time_to']) = fn_create_periods($p);
        list($balances) = fn_uns__get_balance($p);
        foreach ($balances as $o_id=>$g){
            foreach ($g["items"] as $item_id=>$i){
                $balance_of_casts[$item_id] = fn_fvalue($i["konech"]);

                switch ($i["accessory_view"]){
                    case "P": $accessory_of_casts[$item_id] = $i["accessory_pumps"];      break;
                    case "S": $accessory_of_casts[$item_id] = $i["accessory_pump_series"];break;
                    case "M": $accessory_of_casts[$item_id] = $i["accessory_pump_manual"];break;
                }
            }
        }
        $_SESSION["balance_of_casts"] = $balance_of_casts;
        $_SESSION["accessory_of_casts"] = $accessory_of_casts;

    }
    $view->assign("balance_of_casts", $balance_of_casts);
    $view->assign("accessory_of_casts", $accessory_of_casts);


    // 4. ПОЛУЧИТЬ КОМПЛЕКТАЦИИ ПО СЕРИЯМ ======================================
    $ps_sets = null;
    foreach ($ps_ids as $ps_id){
        $pumps = array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id)));
        if ($action != "pump"){
            $pumps = array_slice($pumps, 0, 1, true);
        }

        foreach ($pumps as $pump_id=>$pump){
            $set = fn_uns__get_packing_list_by_pump($pump_id, "D", true);
            list($details) = fn_uns__get_details(array("detail_id"=>array_keys($set), "with_material_info" => true,));
//             Объединить данные
            foreach ($details as $k=>$v){
                $details[$k] = array_merge($details[$k], $set[$k]);
            }

            $ps_sets[$ps_id]["pumps"][$pump_id] = $pump;
            $ps_sets[$ps_id]["pumps"][$pump_id]["details"] = $details;
        }
    }
    $view->assign("ps_sets", $ps_sets);
}


//==============================================================================
// РАСЧЕТ ПЛАНА ПРОИЗВОДСТВА ДЛЯ ЛИТЕЙНОГО ЦЕХА
//==============================================================================
if ($mode == "planning" and $action == "LC"){ // План для литейного цеха
    if (!is__array($_SESSION["uns_plan_of_mech_dep"])) return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    $data = $_SESSION["uns_plan_of_mech_dep"];
    $view->assign("data", $data);

    //-----------------------------------------------------------------------------
    // -1. ПОЛУЧИТЬ ВСЕ ДЕТАЛИ которые заложены в план продаж
    //-----------------------------------------------------------------------------
    $sales_plan_of_details = null;
    foreach ($data["plan"]["group_by_item"]["D"] as $id=>$v){ // Отбор только по деталям
        // +0 мес. = тек. мес. - план продаж
        $sales_plan_of_details["curr_month"][$id]  = $v["ukr_curr"]+$v["exp_curr"];

        // +1 мес. = след. мес. - план продаж
        $sales_plan_of_details["next_month"][$id]  = $v["ukr_next"]+$v["exp_next"];

        // +2 мес. = след. след. мес. - среднее (тек. мес. и след. мес.)
        $sales_plan_of_details["next2_month"][$id] = 0;
//        $sales_plan_of_details["next2_month"][$id] = floor(($sales_plan_of_details["curr_month"][$id]+$sales_plan_of_details["next_month"][$id])/2);

        // +3 мес. = 50% от +2 мес.
        $sales_plan_of_details["next3_month"][$id] = 0;
//        $sales_plan_of_details["next3_month"][$id] = floor(0.5*$sales_plan_of_details["next2_month"][$id]);
    }


    //-----------------------------------------------------------------------------
    // 0. ПОЛУЧИТЬ ВСЕ ДЕТАЛИ, по которым необходимо отслеживать минимальный остаток - это значение указывается в настройках детали
    //-----------------------------------------------------------------------------
    $min_rest_of_details = db_get_hash_array(UNS_DB_PREFIX . "SELECT detail_id, min_rest_value FROM ?:details WHERE detail_status = 'A' and min_rest_state = 'Y' and min_rest_value > 0 ", "detail_id");

    //--------------------------------------------------------------------------
    // 1. ПЛАН ПОТРЕБНОСТИ В ДЕТАЛЯХ на тек., след. и след.след. месяца по плану производства
    //--------------------------------------------------------------------------
    $details_requirement = null;
    $pumps_requirement = $data["remaining_production_plan_parties"]; // Осталось план производства по партиям
    foreach ($pumps_requirement as $month=>$pump_series){
        foreach ($pump_series as $ps_id=>$pump_quantity){
            if (is__more_0($ps_id)){
                // Комплектация насоса
                $pump = array_shift(array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id))));
                $set = fn_uns__get_packing_list_by_pump($pump["p_id"], "D", true);
                foreach ($set as $k=>$v){
                    $details_requirement[$month][$k] += $v["quantity"]*$pump_quantity + $sales_plan_of_details[$month][$k];
                }
            }
        }
    }

    //--------------------------------------------------------------------------
    // 2. БАЛАНС МЕХ. ЦЕХА и СКМП на первое число тек. месяца
    //--------------------------------------------------------------------------
    $initial_balance_of_details = null;
    $p = array();
    $p["time_from"] = strtotime($data["year"] . "-" . $data["month"] . "-" . "1" . " 00:00:00")-2;  // 01/01/2014 - 2 сек = 31/12/2013 23:59:58
    $p["time_to"]   = strtotime($data["year"] . "-" . $data["month"] . "-" . "1" . " 00:00:00")-1;     // 01/01/2014 - 1 сек = 31/12/2013 23:59:59
    list ($p['time_from'], $p['time_to']) = fn_create_periods($p);
    $p["check_dcat_id"]      = false;
    list($balances) = fn_uns__get_balance_mc_sk_su($p, true, true);
    if (is__array($balances)){
        foreach ($balances as $o_id=>$groups){
            foreach ($groups as $g){
                foreach ($g["items"] as $item_id=>$i){
                    if ($o_id == 17){
                        $summ = $i["konech"];
                    }else{
                        $summ = $i["processing_konech"] + $i["complete_konech"];
                    }
                    $initial_balance_of_details[$item_id] += $summ;
                }
            }
        }
    }


    //--------------------------------------------------------------------------
    // 3. ПОМЕСЯЧНЫЙ ДЕФИЦИТ ДЕТАЛЕЙ (положительные числа - означают дефицит, отрицательные - избыток)
    //--------------------------------------------------------------------------
    $deficit_of_details = null;
    foreach ($initial_balance_of_details as $detail_id=>$balance){
        // тек. мес.
        $deficit_curr = $details_requirement["curr_month"][$detail_id]-$balance;
        $deficit_of_details["curr_month"][$detail_id] = $deficit_curr;
        if ($deficit_curr < 0) $deficit_curr = 0;

        // след. мес.
        $deficit_next = ($details_requirement["curr_month"][$detail_id]+$details_requirement["next_month"][$detail_id])-$balance - $deficit_curr;
        $deficit_of_details["next_month"][$detail_id] = $deficit_next;
        if ($deficit_next < 0) $deficit_next = 0;

        // след. след. мес.
        $deficit_next2 = ($details_requirement["curr_month"][$detail_id]+$details_requirement["next_month"][$detail_id]+$details_requirement["next2_month"][$detail_id]) - $balance - $deficit_curr - $deficit_next;
        $deficit_of_details["next2_month"][$detail_id] = $deficit_next2;
        if ($deficit_next2 < 0) $deficit_next2 = 0;

        // след. след. след. мес.
        $deficit_next3 = ($details_requirement["curr_month"][$detail_id]+$details_requirement["next_month"][$detail_id]+$details_requirement["next2_month"][$detail_id]+$details_requirement["next3_month"][$detail_id]) - $balance - $deficit_curr - $deficit_next - $deficit_next2;
        $deficit_of_details["next3_month"][$detail_id] = $deficit_next3;
        if ($deficit_next3 < 0) $deficit_next3 = 0;

//        $deficit_of_details["curr_month"][$detail_id] = $deficit_curr;
//        $deficit_of_details["next_month"][$detail_id] = $deficit_next;
//        $deficit_of_details["next2_month"][$detail_id] = $deficit_next2;
//        $deficit_of_details["next3_month"][$detail_id] = $deficit_next3;
    }

    //--------------------------------------------------------------------------
    // 4. Баланc по Складу литья
    //--------------------------------------------------------------------------
    $balance_of_casts = null;
    $p = array(
        "plain"             => true,
        "all"               => true,
        "o_id"              => array(8),  // Склад литья
        "item_type"         => "M",
        "add_item_info"     => true,
        "view_all_position" => "Y",
        "mclass_id"         => 1,
        "with_weight"       => true,
        "accessory_pumps"   => true,
    );

    $p['time_from'] = strtotime($data["year"] . "-" . $data["month"] . "-" . "1" . " 00:00:00");
    $p['time_to']   = strtotime(date("Y-m-d", fn_parse_date($data["current_day"])) . " 23:59:59");

    $balance_of_casts = array_shift(fn_uns__get_balance($p));
    $view->assign("balance_of_casts", $balance_of_casts);

    $balance_of_casts_simple = null;
    if (is__array($balance_of_casts)){
        foreach ($balance_of_casts as $group){
            if (is__array($group["items"])){
                foreach ($group["items"] as $m_id=>$m){
                    $balance_of_casts_simple[$m_id] = $m["konech"];
                }
            }
        }
    }

    //--------------------------------------------------------------------------
    // 5. ПОМЕСЯЧНАЯ ПЛАНОВАЯ ПОТРЕБНОСТЬ В ЗАГОТОВКАХ
    //--------------------------------------------------------------------------
    $prohibition_of_casts = null;       // Запрет на отливки
    $requirement_of_casts = null;       // Потребность в литье
    $requirement_of_casts_for_min_rest = null;
    if (is__array($deficit_of_details)){
        foreach ($deficit_of_details as $month=>$items){
            list($details) = fn_uns__get_details(array("detail_id"=>array_keys($items), "with_material_info" => true, "with_material_info" => true,));
            if (is__array($details)){
                foreach ($details as $detail_id=>$d){
                    if ($d["mclass_id"] == 1 and $d["material_u_id"] == 9) { // Отливка, в штуках
                        $deficit = $items[$detail_id];
                        if ($month == "curr_month" and is__more_0($min_rest_of_details[$detail_id]["min_rest_value"])){
                            $deficit = $items[$detail_id]+$min_rest_of_details[$detail_id]["min_rest_value"];
                            $requirement_of_casts_for_min_rest[$d["material_id"]] = "Y";
                        }
                        if (is__more_0($deficit)) {
                            $requirement_of_casts[$month][$d["material_id"]] += $d["material_quantity"]*$deficit;
                        }
//                        if ($items[$detail_id]>0) {
//                            $requirement_of_casts[$month][$d["material_id"]] += $d["material_quantity"]*$items[$detail_id];
//                            if ($month == "curr_month" and is__more_0($min_rest_of_details[$detail_id]["min_rest_value"])){
//                                $requirement_of_casts_for_min_rest[$d["material_id"]] = "Y";
//                            }
//                        }
                    }
                }
            }
        }
    }

    //--------------------------------------------------------------------------
    // 7. РАСЧЕТ "ОСТАЛОСЬ"
    // ОСТАЛОСЬ(тек) = ПЛАН(тек) - Нач.Ост. - Приход;
    // если ОСТАЛОСЬ(тек) >= 0: ОСТАЛОСЬ(след) = ПЛАН(след)
    // если ОСТАЛОСЬ(тек) < 0 : ОСТАЛОСЬ(след) = ОСТАЛОСЬ(тек) + ПЛАН(след)
    //--------------------------------------------------------------------------
    $remaining_of_casts = null;
    $movement_of_casts = null;
    if (is__array($balance_of_casts)){
        foreach ($balance_of_casts as $group){
            if (is__array($group["items"])){
                foreach ($group["items"] as $m_id=>$m){
                    if ($group["group_view_in_plans"] == "Y"){
                        $balance = $m["nach"] + $m["current__in"];
                        // тек.
                        $deficit_curr = -($balance - $requirement_of_casts["curr_month"][$m_id]);
                        if ($deficit_curr < 0) $deficit_curr = 0;

                        // след. мес.
                        $deficit_next = ($requirement_of_casts["curr_month"][$m_id]+$requirement_of_casts["next_month"][$m_id]) - $balance - $deficit_curr;
                        if ($deficit_next < 0) $deficit_next = 0;

                        // след. след. мес.
                        $deficit_next2 = ($requirement_of_casts["curr_month"][$m_id] + $requirement_of_casts["next_month"][$m_id] + $requirement_of_casts["next2_month"][$m_id]) - $balance - $deficit_curr - $deficit_next;
                        if ($deficit_next2 < 0) $deficit_next2 = 0;

                        // след. след. след. мес.
                        $deficit_next3 = ($requirement_of_casts["curr_month"][$m_id] + $requirement_of_casts["next_month"][$m_id] + $requirement_of_casts["next2_month"][$m_id] + $requirement_of_casts["next3_month"][$m_id]) - $balance - $deficit_curr - $deficit_next - $deficit_next2;
                        if ($deficit_next3 < 0) $deficit_next3 = 0;

                        //------------------------------------------------------
                        // Расчет на минимально необходимый остаток
//                        if (($m["konech"]+$deficit_curr) < $min_necessary_rest_of_materials[$m_id]["min_necessary_rest"]){
//                            $deficit_curr = $min_necessary_rest_of_materials[$m_id]["min_necessary_rest"]-($m["konech"]+$deficit_curr);
//                            $min_necessary_rest_of_materials[$m_id]["status"] = true;
//                        }
                        //------------------------------------------------------

                        $remaining_of_casts["curr_month"][$m_id]    = $deficit_curr;
                        $remaining_of_casts["next_month"][$m_id]    = $deficit_next;
                        $remaining_of_casts["next2_month"][$m_id]   = $deficit_next2;
                        $remaining_of_casts["next3_month"][$m_id]   = $deficit_next3;

                        // ЗАПРЕТ
                        if (    $remaining_of_casts["curr_month"][$m_id]  == 0
                            and $remaining_of_casts["next_month"][$m_id]  == 0
                            and $remaining_of_casts["next2_month"][$m_id] == 0
                            and $remaining_of_casts["next3_month"][$m_id] == 0
                            and $m["konech"] >= 0
                            and $group["group_id"] != 36 // Полумуфты
                            and $group["group_id"] != 67 // Болванки и втулки
                            and $group["group_id"] != 28 // Гайки
                            and $group["group_id"] != 76 // Детали ЦНС
                            and $group["group_id"] != 78 // На собственные нужды
                            and $group["group_id"] != 79 // На продажу
                            and $group["group_id"] != 80 // Старое литья
                        ){
                            $prohibition_of_casts[$m_id] = "Y";
                        }

                        // РАСЧЕТ ВЕСА по тем категориям, которые разрешены для отображения в планах
                        // Потребность
                        $requirement_of_casts["curr_month"]["total_weight"] += $m["weight"]*$requirement_of_casts["curr_month"][$m_id];
                        $requirement_of_casts["next_month"]["total_weight"] += $m["weight"]*$requirement_of_casts["next_month"][$m_id];
                        $requirement_of_casts["next2_month"]["total_weight"]+= $m["weight"]*$requirement_of_casts["next2_month"][$m_id];
                        $requirement_of_casts["next3_month"]["total_weight"]+= $m["weight"]*$requirement_of_casts["next3_month"][$m_id];

                        // Движение по складу
                        $movement_of_casts["nach"]                          += $m["weight"]*$m["nach"];
                        $movement_of_casts["in"]                            += $m["weight"]*$m["current__in"];
                        $movement_of_casts["out"]                           += $m["weight"]*$m["current__out"];
                        $movement_of_casts["konech"]                        += $m["weight"]*$m["konech"];

                        // Осталось
                        $remaining_of_casts["curr_month"]["total_weight"]   += $m["weight"]*$deficit_curr;
                        $remaining_of_casts["next_month"]["total_weight"]   += $m["weight"]*$deficit_next;
                        $remaining_of_casts["next2_month"]["total_weight"]  += $m["weight"]*$deficit_next2;
                        $remaining_of_casts["next3_month"]["total_weight"]  += $m["weight"]*$deficit_next3;
                    }
                }
            }
        }
    }

    $view->assign("movement_of_casts",      $movement_of_casts);
    $view->assign("remaining_of_casts",     $remaining_of_casts);
    $view->assign("requirement_of_casts",   $requirement_of_casts);
    $view->assign("prohibition_of_casts",   $prohibition_of_casts);

    $view->assign("min_rest_of_details",    $min_rest_of_details);
    $view->assign("requirement_of_casts_for_min_rest",    $requirement_of_casts_for_min_rest);

    //==========================================================================
    // 8. "ГОРЯЩИЕ" заготовки определенные остатками насосов
    //     только для задания цветовой маркировки
    //==========================================================================
    // Определить насосы, которых хватает менее чем на месяц, и получить список отливок, которые в них входят.
    $priority_materials     = null;
    $priority_materials_w   = null;  // Вес
    $priority_materials_q   = null;  // Кол-во
    $priority_ps_id         = null;  // Приоритеты по насосам, указанные в плане продаж
    $priority_details       = null;  // Приоритеты по деталям, указанные в плане продаж
    $priority_materials__ps         = null; // Приоритеты отливок на основании приоритетов насосов в плане продаж
    $priority_materials__details    = null; // Приоритеты отливок на основании приоритетов деталей в плане продаж
    // R - red      - отливки высокого уровня приоритета
    // Y - yellow   - отливки среднего уровня приоритета

    // 1. Список "красных" и "желтых" насосов
    if (is__array($data["analisys"])){
        foreach ($data["analisys"] as $id=>$v) {
            $t = $v["total"] + $v["zadel"];

            // RED уровень - по принудительному приоритету насоса указанному в плане продаж
            if($data["plan"]["group_by_item"]["S"][$id]["forced_status"] == "R"){
                $priority_ps_id["R"][$id] = $id;

            // RED уровень - по фактическому наличию насосов на СГП и в заделе
            }elseif($v["priority"]["status"] == "R"){                    // насосов хватит до 2-х недель
                $priority_ps_id["R"][$id] = $id;

            // RED уровень - по фактическому наличию насосов на СГП и в заделе
            }elseif($v["priority"]["status"] == "R2"){                    // насосов хватит до 2-х недель
                $priority_ps_id["R2"][$id] = $id;

            // YELLOW уровень - по принудительному приоритету указанному в плане продаж
            }elseif($data["plan"]["group_by_item"]["S"][$id]["forced_status"] == "Y"){
                    $priority_ps_id["Y"][$id] = $id;

            // YELLOW уровень - по фактическому наличию насосов на СГП и в заделе
            }elseif($v["priority"]["status"] == "Y"){     // насосов хватит от 2-х до 4-х недель
                $priority_ps_id["Y"][$id] = $id;
            }
        }
    }

    fn_print_r($priority_ps_id);

    //--------------------------------------------------------------------------
    // ПРИОРИТЕТНОСТЬ ПО НАСОСАМ в плане продаж
    //--------------------------------------------------------------------------
    // 2. получить список отливок, которые в них входят
    // КРАСНЫЙ УРОВЕНЬ
    if (is__array($priority_ps_id["R"])){
        foreach ($priority_ps_id["R"] as $ps_id){
            $pump = array_shift(array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id))));
            $pump_materials = fn_uns__get_packing_list_by_pump($pump["p_id"]);
            if (is__array($pump_materials)){
                foreach (array_keys($pump_materials) as $m_id){
                    if (    ($prohibition_of_casts[$m_id] != "Y")
                        and (fn_fvalue($remaining_of_casts["curr_month"][$m_id],0) > 0 or fn_fvalue($remaining_of_casts["next_month"][$m_id],0) > 0)
                        and ($priority_materials__ps["R"][$m_id]    != "Y")
                    ){
                            $priority_materials__ps["R"][$m_id] = "Y";
                    }
                }
            }
        }
    }

    if (is__array($priority_ps_id["R2"])){
        foreach ($priority_ps_id["R2"] as $ps_id){
            $pump = array_shift(array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id))));
            $pump_materials = fn_uns__get_packing_list_by_pump($pump["p_id"]);
            if (is__array($pump_materials)){
                foreach (array_keys($pump_materials) as $m_id){
                    if (    ($prohibition_of_casts[$m_id] != "Y")
                        and (fn_fvalue($remaining_of_casts["curr_month"][$m_id],0) > 0 or fn_fvalue($remaining_of_casts["next_month"][$m_id],0) > 0)
                        and ($priority_materials__ps["R"][$m_id]    != "Y")
                        and ($priority_materials__ps["R2"][$m_id]   != "Y")
                    ){
                            $priority_materials__ps["R2"][$m_id] = "Y";
                    }
                }
            }
        }
    }

    // ЖЕЛТЫЙ УРОВЕНЬ
    if (is__array($priority_ps_id["Y"])){
        foreach ($priority_ps_id["Y"] as $ps_id){
            $pump = array_shift(array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id))));
            $pump_materials = fn_uns__get_packing_list_by_pump($pump["p_id"]);
            if (is__array($pump_materials)){
                foreach (array_keys($pump_materials) as $m_id){
                    if (    ($prohibition_of_casts[$m_id] != "Y")
                        and (fn_fvalue($remaining_of_casts["curr_month"][$m_id],0) > 0 or fn_fvalue($remaining_of_casts["next_month"][$m_id],0) > 0)
                        and ($priority_materials__ps["R"][$m_id]    != "Y")
                        and ($priority_materials__ps["R2"][$m_id]   != "Y")
                        and ($priority_materials__ps["Y"][$m_id]    != "Y")
                    ){
                            $priority_materials__ps["Y"][$m_id] = "Y";
                    }
                }
            }
        }
    }

    //---------------------------------------------------------------------------------
    // ПРИОРИТЕТНОСТЬ ПО ДЕТАЛЯМ в плане продаж (детали необходимо перевести в отливки)
    //---------------------------------------------------------------------------------
    // 0. Список "красных" и "желтых" деталей по плану продаж
    if ($data["plan"]["group_by_item"]["D"]){
        foreach ($data["plan"]["group_by_item"]["D"] as $d){
            if (in_array($d["forced_status"], array("R", "Y"))){
                $m_id = db_get_field(UNS_DB_PREFIX . "SELECT material_id FROM ?:detail__and__items WHERE detail_id = ?i", $d["item_id"]);
                $priority_materials__details[$d["forced_status"]][$m_id] = "Y";
            }
        }
    }

    // Собрать все material_id с приоритетами
    $materials = array();
    if (is__array(array_keys($priority_materials__ps["R"])))        $materials = array_merge($materials, array_keys($priority_materials__ps["R"]));
    if (is__array(array_keys($priority_materials__ps["R2"])))       $materials = array_merge($materials, array_keys($priority_materials__ps["R2"]));
    if (is__array(array_keys($priority_materials__ps["Y"])))        $materials = array_merge($materials, array_keys($priority_materials__ps["Y"]));
    if (is__array(array_keys($priority_materials__details["R"])))   $materials = array_merge($materials, array_keys($priority_materials__details["R"]));
    if (is__array(array_keys($priority_materials__details["Y"])))   $materials = array_merge($materials, array_keys($priority_materials__details["Y"]));

    if (is__array($materials) and fn_is_not_empty($materials)){
        foreach ($materials as $m_id){
            if ($remaining_of_casts["curr_month"][$m_id] + $remaining_of_casts["next_month"][$m_id] > 0){
                $w = fn_uns__get_accounting_item_weights("M", $m_id);
                $w = $w[$m_id]["M"][0]['value'];
                if (//ps_RED + det_RED
                    ($priority_materials__ps["R"][$m_id] == "Y" and $priority_materials__details["R"][$m_id] == "Y")
                    or
                    //ps_RED + det_YEL
                    ($priority_materials__ps["R"][$m_id] == "Y" and $priority_materials__details["Y"][$m_id] == "Y")
                    or
                    //ps_RED + det_GRAY
                    ($priority_materials__ps["R"][$m_id] == "Y" and ($priority_materials__details["R"][$m_id] != "Y" and $priority_materials__details["Y"][$m_id] != "Y"))
                    or
                    //ps_YEL + det_RED
                    ($priority_materials__ps["Y"][$m_id] == "Y" and $priority_materials__details["R"][$m_id] == "Y")
                    or
                    //ps_GRAY + det_RED
                    (($priority_materials__ps["R"][$m_id] != "Y" and $priority_materials__ps["Y"][$m_id] != "Y") and $priority_materials__details["R"][$m_id] == "Y")
                ){
                    $priority_materials_w["R"] += $w*($remaining_of_casts["curr_month"][$m_id] + $remaining_of_casts["next_month"][$m_id]);
                    $priority_materials_q["R"] +=     $remaining_of_casts["curr_month"][$m_id] + $remaining_of_casts["next_month"][$m_id];
                    $priority_materials  ["R"][$m_id] = "Y";

                }elseif (//ps_RED + det_RED
                    ($priority_materials__ps["R2"][$m_id] == "Y" and $priority_materials__details["R"][$m_id] == "Y")
                    or
                    //ps_RED + det_YEL
                    ($priority_materials__ps["R2"][$m_id] == "Y" and $priority_materials__details["Y"][$m_id] == "Y")
                    or
                    //ps_RED + det_GRAY
                    ($priority_materials__ps["R2"][$m_id] == "Y" and ($priority_materials__details["R"][$m_id] != "Y" and $priority_materials__details["Y"][$m_id] != "Y"))
                    or
                    //ps_YEL + det_RED
                    ($priority_materials__ps["Y"][$m_id] == "Y" and $priority_materials__details["R"][$m_id] == "Y")
                    or
                    //ps_GRAY + det_RED
                    (($priority_materials__ps["R2"][$m_id] != "Y" and $priority_materials__ps["Y"][$m_id] != "Y") and $priority_materials__details["R"][$m_id] == "Y")
                ){
                    $priority_materials_w["R2"] += $w*($remaining_of_casts["curr_month"][$m_id] + $remaining_of_casts["next_month"][$m_id]);
                    $priority_materials_q["R2"] +=     $remaining_of_casts["curr_month"][$m_id] + $remaining_of_casts["next_month"][$m_id];
                    $priority_materials  ["R2"][$m_id] = "Y";

                }elseif (
                    //ps_YEL + det_YEL
                    ($priority_materials__ps["Y"][$m_id] == "Y" and $priority_materials__details["Y"][$m_id] == "Y")
                    or
                    //ps_YEL + det_YEL
                    ($priority_materials__ps["Y"][$m_id] == "Y" and ($priority_materials__details["R"][$m_id] != "Y" and $priority_materials__details["Y"][$m_id] != "Y"))
                    or
                    //ps_GRAY + det_YEL
                    (($priority_materials__ps["R"][$m_id] != "Y" and $priority_materials__ps["Y"][$m_id] != "Y") and $priority_materials__details["Y"][$m_id] == "Y")
                ){
                    $priority_materials_w["Y"] += $w*($remaining_of_casts["curr_month"][$m_id] + $remaining_of_casts["next_month"][$m_id]);
                    $priority_materials_q["Y"] +=     $remaining_of_casts["curr_month"][$m_id] + $remaining_of_casts["next_month"][$m_id];
                    $priority_materials  ["Y"][$m_id] = "Y";
                }
            }
        }
    }

    $view->assign("priority_materials__details",$priority_materials__details);
    $view->assign("priority_materials",         $priority_materials);
    $view->assign("priority_materials_w",       $priority_materials_w);
    $view->assign("priority_materials_q",       $priority_materials_q);
}


//=======================================================================================
// ПРОСМОТР ОСТАТКОВ ДЕТАЛЕЙ ПО ВЫБРАННОЙ ЗАГОТОВКЕ в "плане производства литейного цеха"
//=======================================================================================
if ($mode == "planning" and $action == "balance_of_details"){
    if (!is__more_0($_REQUEST["m_id"])) return array(CONTROLLER_STATUS_REDIRECT, $controller . "." . $suffix);
    // Получить список деталей, которые сделаны из material_id
    $details = db_get_fields(UNS_DB_PREFIX . "
                select ?:details.detail_id
                from ?:details
                    left join ?:detail__and__items on (?:details.detail_id = ?:detail__and__items.detail_id)
                where
                    ?:details.detail_status = 'A'
                    and ?:detail__and__items.material_id = " . $_REQUEST["m_id"] . " ");


    if (!is__array($details)) return array(CONTROLLER_STATUS_REDIRECT, $controller . "." . $suffix);
    $p_D['period'] = "M";
    $p_D['accessory_pumps'] = "Y";
    $p_D['item_id'] = $details;
    list ($p_D['time_from'], $p_D['time_to']) = fn_create_periods($p_D);
    list($balances_D, $search_D) = fn_uns__get_balance_mc_sk_su($p_D, true, true, true);
    $view->assign('balances_D',     $balances_D);
    $view->assign('search',       $search_D);
}


// Расчет заполнения полосы продаж по месяцам
function fn_uns_calc_progress ($a=0, $b=0, $c=0, $t=0, $offset_a = 0){
    $res = array(
        "ZAPOLNENIE"=>array("a"=>0, "b"=>0, "c"=>0),
        "OSTALOS"   =>array("a"=>0, "b"=>0, "c"=>0),
        "TOTAL"     =>array("a"=>0, "b"=>0, "c"=>0),
    );

    if ($a == 0 and $b == 0 and $c == 0 and $t == 0) return $res;

    if ($t == 0){
        $res = array(
            "ZAPOLNENIE"=>array("a"=>0,     "b"=>0,     "c"=>0),
            "OSTALOS"   =>array("a"=>$a,    "b"=>$b,    "c"=>$c),
            "TOTAL"     =>array("a"=>0,     "b"=>0,     "c"=>0),
        );
        return $res;
    }

    if ($t >= ($a + $b + $c)){
        $res = array(
            "ZAPOLNENIE"=>array("a"=>$a,    "b"=>$b,    "c"=>$c),
            "OSTALOS"   =>array("a"=>0,     "b"=>0,     "c"=>0),
            "TOTAL"     =>array("a"=>0,     "b"=>0,     "c"=>0),
        );
    }elseif ($t >= ($a + $b)){
        $res = array(
            "ZAPOLNENIE"=>array("a"=>$a,    "b"=>$b,    "c"=>$t-$a-$b),
            "OSTALOS"   =>array("a"=>0,     "b"=>0,     "c"=>$a+$b+$c-$t),
            "TOTAL"     =>array("a"=>0,     "b"=>0,     "c"=>0),
        );
    }elseif ($t >= $a){
        $res = array(
            "ZAPOLNENIE"=>array("a"=>$a,    "b"=>$t-$a,    "c"=>0),
            "OSTALOS"   =>array("a"=>0,     "b"=>$a+$b-$t, "c"=>$c),
            "TOTAL"     =>array("a"=>0,     "b"=>0,     "c"=>0),
        );
    }elseif ($t > 0){
        $res = array(
            "ZAPOLNENIE"=>array("a"=>$t,    "b"=>0,     "c"=>0),
            "OSTALOS"   =>array("a"=>$a-$t, "b"=>$b,    "c"=>$c),
            "TOTAL"     =>array("a"=>0,     "b"=>0,     "c"=>0),
        );
    }
    return $res;
}


function fn_uns_plan_of_mech_dep__search ($controller){
    $params = array(
        "month",
        "year",
        "months_supply",
        "current_day",
        "type_of_production_plan",
        "analisys_of_production_plan",
        "analisis_of_sales",
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}
