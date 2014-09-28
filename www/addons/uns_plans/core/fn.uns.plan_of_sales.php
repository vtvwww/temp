<?php

/**
 * Class plan_of_sales
 * Для расчета плана продаж
 */
class plan_of_sales {

    /**
     *
     */
    public function __construct(){
        require('lib/pChart2.1.4/class/pData.class.php');
        require('lib/pChart2.1.4/class/pDraw.class.php');
        require('lib/pChart2.1.4/class/pImage.class.php');
    }

    /**
     * Расчет плана производства
     * @param $params
     */
    public function calculation ($params){
        $ps_id              = $params["ps_id"];
        $ref_month          = $params["month"];
        $ref_year           = $params["year"];
        $week_supply        = $params["week_supply"];       // Запас по продажам
        $koef_plan_prodazh  = $params["koef_plan_prodazh"];

        // 0. Получить список клиентов
        $customers = array_shift(fn_uns__get_customers()) ;

        // 1. Получить список серий насосов
        $pump_series = array_shift(fn_uns__get_pump_series(array("ps_id"=> $ps_id, "view_in_plans"=>"Y",)));
//        fn_print_r($pump_series);

        // 2. Получить статистику продаж по указанным сериям за последние 2 года
        $sales = self::get_sales(array_keys($pump_series), $ref_month, $ref_year, $customers);
        $sales_ukr_exp = self::group_sales_ukr_exp($sales, $customers);
//        fn_print_r($sales);

        // 3. Произвести анализ по каждой серии насосов
        $analysis   = self::analysis_sales($sales_ukr_exp, $ref_month, $ref_year, $customers);
//        fn_print_r($analysis);

        // 5. Список имеющихся заказов по каждой серии
        list($order_ps, $ps_order) = self::get_orders(array_keys($pump_series), $ref_month, $ref_year);
//        fn_print_r($plan);

        // 5. Выполнить расчет плана на расчетный месяц
        $plan       = self::planning($pump_series, $sales, $sales_ukr_exp, $analysis, $order_ps, $ps_order, $ref_month, $ref_year, $week_supply, $koef_plan_prodazh);
//        fn_print_r($plan);

        // 6. Выполнить постороение графиков
        $graphs     = self::create_graphs($params["graphs_key"], $pump_series, $sales, $sales_ukr_exp, $analysis, $plan, $ref_month, $ref_year);
//        fn_print_r($graphs);

        return array($pump_series, $sales, $sales_ukr_exp, $analysis, $plan, $ps_order);
    }


    /**
     * Список имеющихся заказов на расчетный период
     * @param $pump_series
     * @param $ref_month
     * @param $ref_year
     */
    private function get_orders ($ps_id, $ref_month, $ref_year){
        $order_ps = array(); // Order_x => ps_1, ps_2, ps_3
        $ps_order = array(); // ps_x => order_1, order_2, order_3

        $begin      = $ref_year . "-" . $ref_month . "-" . "1" . " 00:00:00";
        $end        = $ref_year . "-" . $ref_month . "-" . date("t", strtotime($begin))  . " 23:59:59";
        $orders     = array_shift(fn_acc__get_orders(array("with_items"=>true, "only_active"=>true, "date_finished_begin"=> strtotime($begin), "date_finished_end"=>strtotime($end),)));
        if (is__array($orders)){
            $pumps      = array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id, "only_active"=>true,)));
            foreach ($orders as $o){
                foreach ($o["items"] as $i){
                    $__ps_id = $pumps[$i["p_id"]]["ps_id"];
                    if (in_array($i["item_type"], array("P", "PF", "PA")) and in_array($__ps_id, $ps_id)){
                        $order_ps[$o["order_id"]][$__ps_id] += $i["quantity"];
                        $ps_order[$__ps_id][$o["order_id"]] += $i["quantity"];
                    }
                }
            }
        }
        return array($order_ps, $ps_order);
    }


    /**
     * Выполнить расчет плана продаж
     * @param $pump_series
     * @param $sales
     * @param $analysis
     * @param $ref_month
     * @param $ref_year
     * @param $week_supply
     */
    private function planning($pump_series, $sales, $sales_ukr_exp, $analysis, $order_ps, $ps_order, $ref_month, $ref_year, $week_supply, $koef_plan_prodazh){
        $res = array();
        $customers = array_shift(fn_uns__get_customers()) ;
        if (is__array($pump_series) and is__array($sales) and is__array($sales_ukr_exp) and is__array($analysis)){
            $ps_ids = array_keys($pump_series);

            // 2. Определить ПЛАН ПРОДАЖ на расчетный месяц
            // Если расчетный ПЛАН ПРОДАЖ <= ИМЕЮЩИМСЯ ЗАКАЗАМ, тогда необходимо увеличить
            // ПЛАН ПРОДАЖ до величины ИМЕЮЩИХСЯ ЗАКАЗОВ + xx% "koef_plan_prodazh"
            $plan_prodazh   = self::_get_plan_prodazh ($ps_ids, $sales, $sales_ukr_exp, $ps_order, $ref_month, $ref_year, $koef_plan_prodazh);

            foreach (array("UKR", "EXP") as $zone_id){
                foreach ($pump_series as $ps_id=>$ps){
                    $v_total_orders = 0;
                    $orders = null;
                    // Отделить заказы по УКРАИНЕ (UKR) от заказов за Украину (EXP)
                    foreach ($ps_order[$ps_id] as $order_id=>$q){
                        $order = array_shift(array_shift(fn_acc__get_orders(array("order_id"=>$order_id))));
                        if ((($zone_id == "UKR") and ($customers[$order["customer_id"]]["to_export"] == "N"))
                            or
                            (($zone_id == "EXP") and ($customers[$order["customer_id"]]["to_export"] == "Y")))
                        {
                            $v_total_orders += $q;
                            $orders[$order_id] = $q;
                        }
                    }

                    $v_plan_prodazh_statistical = $plan_prodazh[$zone_id][$ps_id];          // Статистическое значение
                    $v_plan_prodazh_calc        = fn_fvalue($v_plan_prodazh_statistical, 0);// Расчетное значение
                    $v_plan_prodazh_recalc      = "N";

                    // Пересчет плана продаж если абсолютная разность между статистическим планом и имеющимися заказами составляет более $koef_plan_prodazh процентов
                    if (is__more_0($koef_plan_prodazh, $v_plan_prodazh_calc) and $v_plan_prodazh_calc <= $v_total_orders){
                        $v_plan_prodazh_calc    = fn_fvalue($v_total_orders+$koef_plan_prodazh*$v_total_orders/100,0);
                        $v_plan_prodazh_recalc  = "Y1";

                    }

                    $res[$zone_id][$ps_id] = array(
                        "orders"                    => $orders,
                        "total_orders"              => $v_total_orders,
                        "plan_prodazh_statistical"  => $v_plan_prodazh_statistical,
                        "plan_prodazh_calc"         => $v_plan_prodazh_calc,
                        "plan_prodazh_recalc"       => $v_plan_prodazh_recalc,
                    );
                }
            }
        }
        return $res;
    }


    /**
     * Определить ПЛАН ПРОДАЖ на расчетный месяц
     * @param $ps_ids
     * @param $sales
     * @param $zakaz
     * @param $ref_month
     * @param $ref_year
     * @param $koef_plan_prodazh
     * @param int $sample_range - выборка по месяцам
     */
    private function _get_plan_prodazh ($ps_ids, $sales, $sales_ukr_exp, $zakaz, $ref_month, $ref_year, $koef_plan_prodazh, $sample_range=3 ){
        $res = array();
        foreach ($sales_ukr_exp as $zone_id=>$s){
            foreach ($ps_ids as $id){
                $avr = self::__calc_average($s[$id], $ref_month, $ref_year, $sample_range);
                $res[$zone_id][$id] = $avr;
            }
        }
        return $res;
    }


    private function __calc_average ($sales, $ref_month, $ref_year, $sample_range){
        $res = array();
        $months = array();
        foreach ($sales as $s){
            $months = array_merge($months, $s);
        }

        $vars = array();
        $begin = 12+$ref_month-$sample_range-1;
        $end = 12+$ref_month-2;
        for ($j=$begin; $j<=$end; $j++){
            $vars[] = $months[$j];
        }


        if ($sample_range == 3){
//            $a = array(0.1, 0.2, 0.7);
//            $a = array(0.1, 0.3, 0.6);
            $a = array(0.2, 0.3, 0.5);
            $res = $a[0]*$vars[0]+$a[1]*$vars[1]+$a[2]*$vars[2];
        }

        return $res;
    }

    /**
     * Определить ИМЕЮЩИЕСЯ ЗАКАЗЫ по сериям
     * на период с 01/ref_month/ref_year 00:00:00 по 31/ref_month/ref_year 23:59:59
     * с открытым статусом
     *
     * @param $ps_id
     * @param $ref_month
     * @param $ref_year
     */
    private function _get_zakaz ($ps_id, $ref_month, $ref_year){
        $res = array();
        $begin      = $ref_year . "-" . $ref_month . "-" . "1" . " 00:00:00";
        $end        = $ref_year . "-" . $ref_month . "-" . date("t", strtotime($begin))  . " 23:59:59";
        $orders     = array_shift(fn_acc__get_orders(array("with_items"=>true, "only_active"=>true, "date_finished_begin"=> strtotime($begin), "date_finished_end"=>strtotime($end),)));
        if (is__array($orders)){
            $pumps      = array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_id, "only_active"=>true,)));
            foreach ($orders as $o){
                foreach ($o["items"] as $i){
                    $__ps_id = $pumps[$i["p_id"]]["ps_id"];
                    if (in_array($i["item_type"], array("P", "PF", "PA")) and in_array($__ps_id, $ps_id)){
                        $res[$__ps_id] += $i["quantity"];
                    }
                }
            }
        }
        return $res;
    }


    /**
     * Определить ИМЕЮЩИЙСЯ ЗАДЕЛ по сериям по открытым партиям насосов на 01/ref_month/ref_year 00:00:00
     * @param $ps_id
     * @param $ref_month
     * @param $ref_year
     */
    private function _get_zadel ($ps_id, $ref_month, $ref_year){

    }



    /**
     * Определить НАЧАЛЬНЫЙ ОСТАТОК ПРОДУКЦИИ на начало расчетного месяца ref_month/ref_year
     * @param $ps_ids
     * @param $ref_month
     * @param $ref_year
     * @return array
     */
    private function _get_nach_ostatok ($ps_ids, $ref_month, $ref_year){
        $res = array();
        $time_from = strtotime("$ref_year/$ref_month/01 00:00:00");
        $time_to   = strtotime("$ref_year/$ref_month/01 00:00:01");
        $pumps = array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_ids, "only_active"=>true,)));
        list($balances) = fn_uns__get_balance_sgp(array("time_from"=>$time_from, "time_to"=>$time_to), true, true, true);
        if (is__array($balances)){
            foreach ($balances as $type){
                foreach ($type as $groups){
                    foreach ($groups["items"] as $p){
                        $ps_id = $pumps[$p["id"]]["ps_id"];
                        if (in_array($ps_id, $ps_ids)){
                            $res[$ps_id] += $p["konech"];
                        }
                    }
                }
            }
        }
        return $res;
    }







    /**
     * Выполнить постороение графиков
     * @param $pump_series
     * @param $sales
     * @param $analysis
     * @param $ref_month
     */
    private function create_graphs ($graphs_key, $pump_series, $sales, $sales_ukr_exp, $analysis, $plan, $ref_month, $ref_year){
        if (is__array($pump_series) and is__array($sales) and is__array($sales_ukr_exp) and is__array($analysis)){
            foreach ($pump_series as $ps_id=>$ps){
                // UKR----------------------------------------------------------
                $data = array(
                    "zone_id"   => "UKR",
                    "series"    => $sales_ukr_exp["UKR"][$ps_id],
                    "analysis"  => $analysis["UKR"][$ps_id],
                    "plan"      => $plan["UKR"][$ps_id],
                    "ref_month" => $ref_month,
                    "ref_year"  => $ref_year,
                );
                if (!is_dir(DIR_ROOT . "/skins/basic/admin/images/uns_charts/")) mkdir(DIR_ROOT . "/skins/basic/admin/images/uns_charts/");
                self::_create_graph (DIR_ROOT . "/skins/basic/admin/images/uns_charts/".$graphs_key."_ps_{$ps_id}_ukr.png", $data);

                // EXP----------------------------------------------------------
                $data = array(
                    "zone_id"   => "EXP",
                    "series"    => $sales_ukr_exp["EXP"][$ps_id],
                    "analysis"  => $analysis["EXP"][$ps_id],
                    "plan"      => $plan["EXP"][$ps_id],
                    "ref_month" => $ref_month,
                    "ref_year"  => $ref_year,
                );
                if (!is_dir(DIR_ROOT . "/skins/basic/admin/images/uns_charts/")) mkdir(DIR_ROOT . "/skins/basic/admin/images/uns_charts/");
                self::_create_graph (DIR_ROOT . "/skins/basic/admin/images/uns_charts/".$graphs_key."_ps_{$ps_id}_exp.png", $data);

//                // TOTAL--------------------------------------------------------
//                $data = array(
//                    "zone_id"   => "TOTAL",
//                    "series"    => array("UKR"=>$sales_ukr_exp["UKR"][$ps_id],  "EXP"=>$sales_ukr_exp["EXP"][$ps_id]),
//                    "analysis"  => array("UKR"=>$analysis["UKR"][$ps_id],       "EXP"=>$analysis["EXP"][$ps_id]),
//                    "plan"      => array("UKR"=>$plan["UKR"][$ps_id],           "EXP"=>$plan["EXP"][$ps_id]),
//                    "ref_month" => $ref_month,
//                    "ref_year"  => $ref_year,
//                );
//                self::_create_graph (DIR_ROOT . "/skins/basic/admin/images/uns_charts/".$graphs_key."_ps_{$ps_id}_total.png", $data);
            }
        }
    }

    /**
     * Постороение графика
     * @param $file_name
     * @param $data
     */
    private function _create_graph ($file_name, $data){
//        fn_print_r($data);
        // Размеры графика
        $size = array("w" => 460, "h" => 300);
        $margin = 20;
        $month_arab = array(1=>"I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII");

        /* Create and populate the pData object */
        $MyData = new pData();

        // Ось абсцисс
        $MyData->addPoints(array("I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII", ""),"Labels");
        $MyData->setAbscissa("Labels");

        /* Create the pChart object */
        $myPicture = new pImage($size["w"],$size["h"],$MyData);

        $i = 0;
        foreach ($data["series"] as $k_s=>$s){
            $i ++;

            // ДАННЫЕ ==========================================================
            if ($i==1){         // за предыдущий год
                // По Украине
                $MyData->addPoints(array_merge($s,array(VOID)),$k_s);
                // Вне Украины
//                $MyData->addPoints($export, $k_s . "_export");
            }elseif ($i == 2){ //  за текущий год
                $p = array();
                $p_export = array();
                for ($m=1; $m<=12; $m++){
                    if ($m<$data["ref_month"]){
                        $p[] = $s[$m];
                        $p_export[] = $export[$m];
                    }else{
                        $p[] = $p_export[] = VOID;
                    }
                }
                $MyData->addPoints(array_merge($p,array(VOID)),$k_s);
//                $MyData->addPoints($p_export, $k_s . "_export");
            }

            // СТИЛИ СЕРИЙ
            $MyData->setPalette($k_s,               array("R"=>200,"G"=>200,"B"=>200));
//            $MyData->setPalette($k_s . "_export",   array("R"=>100,"G"=>100,"B"=>100));

            // РАСПОЛОЖЕНИЕ ГРАФИКОВ
            if ($i==1){
                $myPicture->setGraphArea(1.5*$margin, $margin,              $size["w"]-$margin, $size["h"]/2-$margin);
            }elseif ($i == 2){
                $myPicture->setGraphArea(1.5*$margin, $margin+$size["h"]/2, $size["w"]-$margin, $size["h"]-$margin);
            }
            $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/pf_arma_five.ttf","FontSize"=>12));

            // Определить минимальное и максимальное значения рядов
            list($min, $max) = self::_get_min_max($data["series"] + array(array(fn_fvalue($data["plan"]["plan_prodazh_calc"], 0)))/* + $export*/, true);

            /* Координатная плоскость */
            $AxisBoundaries = array(0=>array("Min"=>$min,"Max"=>$max));
            $ScaleSettings  = array(
                "Mode"=>SCALE_MODE_MANUAL, "ManualScale"=>$AxisBoundaries,
                "CycleBackground"=>false,
                "DrawSubTicks"=>TRUE,
                "DrawXLines"=>false,
                "GridR"=>0,"GridG"=>0,"GridB"=>0,
            );
            $myPicture->drawScale($ScaleSettings);

            /* Описание оси абсцисс */
            $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/pf_arma_five.ttf","FontSize"=>12));
            if ($i==1){
                $myPicture->drawText($size["w"]-40, $size["h"]/2-6, $k_s, TEXT_ALIGN_TOPRIGHT);
            }elseif ($i == 2){
                $myPicture->drawText($size["w"]-40, $size["h"]-6, $k_s, TEXT_ALIGN_TOPRIGHT);
            }

//            /* Тренд */
            $myPicture->drawBestFit();

            /* Среднее значение */
            if (is__more_0($avr = $data["analysis"][$k_s]["for_year"]["avr"])){
                $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/pf_arma_five.ttf","FontSize"=>12));
                $myPicture->drawThreshold($avr,array(
                        "WriteCaption"=>TRUE,"Caption"=>fn_fvalue($avr,1),
                        "CaptionAlpha" => 100,
                        "CaptionR"  => 255,
                        "CaptionG"  => 255,
                        "CaptionB"  => 255,
                        "CaptionOffset"=>-26,
                        "CaptionAlign"=>CAPTION_RIGHT_BOTTOM,
                        "BoxR"      => 0,
                        "BoxG"      => 0,
                        "BoxB"      => 0,
                        "BoxAlpha"  => 100,
                        "BorderOffset"=>3,

                        "R" => 0,
                        "G" => 0,
                        "B" => 0,
                        "Alpha" => 35,
                        "Ticks" => 10,
                        "Weight"=>0.5,
                        "Wide"  =>true,
                    ));
            }

            /* ПРОДАЖИ */
            $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/pf_arma_five.ttf","FontSize"=>12));
            $myPicture->drawStackedBarChart(array(
                    "DisplayValues"         =>TRUE,
                    "DisplayPos"            =>LABEL_POS_OUTSIDE,
                    "DisplayOrientation"    =>ORIENTATION_VERTICAL,
                    "FontFactor"            => 5,
                )
            );
            $MyData->removeSerie($k_s);
        }

        // Линия указывающая на текущий месяц
        $myPicture->drawXThreshold($month_arab[$data["ref_month"]],array("ValueIsLabel"=>TRUE,"Alpha"=>100,"Ticks"=>2, "Weight"=>0.4,));

        // Добавить на график расчетное значение плана продаж на расчетный месяц
        $d = array(VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID);
        $d[$data["ref_month"]-1] = fn_fvalue($data["plan"]["plan_prodazh_calc"], 0);
        $MyData->addPoints($d,"PLAN");
        $MyData->setSerieShape("PLAN",SERIE_SHAPE_FILLEDDIAMOND);
        $MyData->setPalette("PLAN",array("R"=>0,"G"=>0,"B"=>0));
        $myPicture->drawPlotChart(array(
                "BorderSize"=>0,
                "Surrounding"=>40,
                "BorderAlpha"=>100,
                "PlotSize"=>4,
                "PlotBorder"=>TRUE,
                "DisplayValues"=>TRUE,
                "DisplayColor"=>DISPLAY_MANUAL,
            ));




        $myPicture->Render($file_name);
    }

    /**
     * Определение MIN и MAX из массивов, а также задать им кратность
     * @param $data
     * @param bool $round
     * @param int $f
     * @return array
     */
    private function _get_min_max ($data, $round=false, $f=5){
        $min = $max = 0;
        foreach ($data as $d){
            $_min = min($d);
            $_max = round(max($d));

            if ($min>$_min) $min = $_min;
            if ($max<$_max) $max = $_max;
        }
        if ($round){
            $min = ceil($min/$f) * $f;
            $max = ceil($max/$f) * $f;
        }

        if (!$max) $max = $f;

        return array($min, $max);
    }


    /**
     * Выполнить анализ продаж
     * @param $sales
     * @param $ref_month
     * @param $ref_year
     * @return array|null
     */
    private function analysis_sales ($sales_ukr_exp, $ref_month, $ref_year, $customers){
        if (!is__array($sales_ukr_exp)) return null;
        $analysis = array();
        foreach ($sales_ukr_exp as $zone_id=>$sales){
            foreach ($sales as $ps_id => $ps){
                foreach ($ps as $k_y=>$year){
                   $for_year = array();
                   $for_months_ref_year = array();

                   // С начала года -------------------------------------------
                   $total_for_months_ref_year = 0;
                   for ($m=1; $m<$ref_month; $m++){
                       $total_for_months_ref_year += $year[$m];
                   }

                   $avr_for_months_ref_year = fn_fvalue(($ref_month==1)?0:$total_for_months_ref_year/($ref_month-1),1);
                   $for_months_ref_year = array(
                       "total" => $total_for_months_ref_year,
                       "avr"   => $avr_for_months_ref_year,
                       "amount_months" => $ref_month-1,
                   );

                   // За год --------------------------------------------------
                   $for_year = array(
                       "total" => fn_fvalue(($k_y==$ref_year)?$avr_for_months_ref_year*12:array_sum($year),0),
                       "avr"   => fn_fvalue(($k_y==$ref_year)?$avr_for_months_ref_year:array_sum($year)/count($year),1),
                   );

                   // Результаты --------------------------------------------------
                   $analysis[$zone_id][$ps_id][$k_y]["for_year"] = $for_year;
                   $analysis[$zone_id][$ps_id][$k_y]["for_months_ref_year"] = $for_months_ref_year;
                }
            }
        }
        return $analysis;
    }


    /**
     * Получить статистику продаж по указанным сериям за последние YEARS_FOR_ANALYSIS
     * @param $ps_id
     * @param $years_for_analysis
     * @param $ref_month
     * @param $ref_year
     */
    private function get_sales ($ps_id, $ref_month, $ref_year, $customers){
        $sales = false;
        // 1. Получить список месячных интервалов
        $months = self::_get_months(2, $ref_year);

        // 2. Cобрать помесячные продажи с разбивкой по клиентам
        foreach ($months as $k_y=>$year){
            foreach ($year as $k_m=>$month){
                $balance = self::_get_sales_pump_series_by_period($ps_id, $month["timestamp"]["begin"], $month["timestamp"]["end"]);
                foreach ($customers as $c){
                    foreach ($ps_id as $ps){
                        $sales[$ps][$c["customer_id"]][$k_y][$k_m] = $balance[$c["customer_id"]][$ps]?:0;
                    }
                }
            }
        }
        return $sales;
    }

    /**
     * СГРУППИРОВАТЬ ПРОДАЖИ UKR и EXP
     * @param $sales
     * @param $customers
     * @return bool
     */
    private function group_sales_ukr_exp ($sales, $customers){
        $s = false;
        foreach ($sales as $ps_id=>$customer_sales){
            foreach ($customer_sales as $customer_id=>$years){
                foreach ($years as $y=>$months){
                    foreach ($months as $month=>$q){
                        $k = ($customers[$customer_id]["to_export"]=="N")?"UKR":"EXP";
                        $s[$k][$ps_id][$y][$month] += $q;
                    }
                }
            }
        }
        return $s;
    }




    /**
     * Получить продажи по сериям за указанный период
     * @param $ps_id
     * @param $begin
     * @param $end
     * @return array|null
     */
    private function _get_sales_pump_series_by_period ($ps_id, $begin, $end){
        //todo необходимо оптимизировать функцию!!! 2014-04-23
        list($pumps) = fn_uns__get_pumps(array("only_active"=>true, ));
        $params = array(
            "time_from"     => $begin,
            "time_to"       => $end,
            "type"          => 7,           // Расходные ордера
            "o_id"          => 19,          // Склад готовой продукции
            "only_active"   => true,
            "item_type"     => array("P", "PF", "PA"),
            "with_items"    => true,
            "info_category" => false,
            "info_item"     => true,
            "info_unit"     => false,
        );
        list($docs) = fn_uns__get_documents($params);
        if(!is__array($docs)) return null;

        $res = array();
        foreach ($docs as $d){
            if (is__array($d["items"])){
                foreach ($d["items"] as $i){
                    if (in_array($pumps[$i["item_id"]]["ps_id"], $ps_id)){
                        $res[$d["customer_id"]][$pumps[$i["item_id"]]["ps_id"]] += $i["quantity"];
                    }
                }
            }
        }
        return $res;
    }


    /**
     * Получить список отчетных месяцев
     * @param $time
     * @return array
     */
    public function _get_months($years_for_analysis, $ref_year){
        $res = array();
        for ($year=$ref_year-$years_for_analysis+1; $year<=$ref_year; $year++){
            for ($month=1; $month<=12; $month++){
                $begin = $year . "-" . $month . "-" . "1" . " 00:00:00";
                $end = $year . "-" . $month . "-" . date("t", strtotime($begin))  . " 23:59:59";
                $res[$year][$month]["date"]["begin"] = $begin;
                $res[$year][$month]["date"]["end"]   = $end;
                $res[$year][$month]["timestamp"]["begin"] = strtotime($begin);
                $res[$year][$month]["timestamp"]["end"]   = strtotime($end);
            }
        }
        return $res;
    }



    public function test_graph (){
        /* Create and populate the pData object */
        $MyData = new pData();
        $MyData->addPoints(array(150,220,300,-250,-420,-200,300,200,100),"Server A");
        $MyData->addPoints(array(140,0,340,-300,-320,-300,200,100,50),"Server B");
        $MyData->setAxisName(0,"Hits");
        $MyData->addPoints(array("January","February","March","April","May","Juin","July","August","September"),"Months");
        $MyData->setSerieDescription("Months","Month");
        $MyData->setAbscissa("Months");

        /* Create the pChart object */
        $myPicture = new pImage(700,230,$MyData);

        /* Turn of Antialiasing */
        $myPicture->Antialias = FALSE;

        /* Add a border to the picture */
        $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
        $myPicture->drawGradientArea(0,0,700,230,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
        $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

        /* Set the default font */
        $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

        /* Define the chart area */
        $myPicture->setGraphArea(60,40,650,200);

        /* Draw the scale */
        $scaleSettings = array("GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
        $myPicture->drawScale($scaleSettings);

        /* Write the chart legend */
        $myPicture->drawLegend(580,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

        /* Turn on shadow computing */
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

        /* Draw the chart */
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
        $settings = array("Surrounding"=>-30,"InnerSurrounding"=>30);
        $myPicture->drawBarChart($settings);

        /* Render the picture (choose the best way) */
        //$myPicture->autoOutput("pictures/example.drawBarChart.simple.png");
                $myPicture->Render("si.png");

    }


}
