<?php

/**
 * Class planning
 * Для расчета планирования
 */
class planning {

    /**
     * Расчет плана производства
     * @param $params
     */
    public function calc ($params){
        $years_for_analysis = $params["years_for_analysis"];
        $ref_month          = $params["month"];
        $ref_year           = $params["year"];
        $week_supply        = $params["week_supply"];
        $ps_id              = $params["ps_id"];

        // 1. Получить список серий насосов
        $pump_series = array_shift(fn_uns__get_pump_series(array("ps_id"=> $ps_id)));
//        fn_print_r($pump_series);

        // 2. Получить статистику продаж по указанным сериям за последние YEARS_FOR_ANALYSIS
        $sales = self::get_sales(array_keys($pump_series), $years_for_analysis, $ref_month, $ref_year);
        fn_print_r($sales);

        // 3. Произвести анализ по каждой серии
        $analysis = self::analysis_sales($sales, $ref_month, $ref_year);
        fn_print_r($analysis);

        // 4. Выполнить постороение графиков
        $graphs = self::create_graphs($sales, $analysis);

    }


    /**
     * Выполнить постороение графиков
     * @param $sales
     * @param $analysis
     */
    private function create_graphs ($sales, $analysis){
        // 1. Выполнить подключение библиотеки

    }

    /**
     * Выполнить анализ продаж
     * @param $sales
     * @param $ref_month
     * @param $ref_year
     * @return array|null
     */
    private function analysis_sales ($sales, $ref_month, $ref_year){
        if (!is__array($sales)) return null;

        $analysis = array();
        foreach ($sales as $ps_id => $ps){
            foreach ($ps as $k_y=>$year){
                $for_year = array();
                $for_months_ref_year = array();

                // С начала года -------------------------------------------
                $total_for_months_ref_year = 0;
                for ($m=1; $m<$ref_month; $m++){
                    $total_for_months_ref_year += $year[$m];
                }

                $avr_for_months_ref_year = ($ref_month==1)?0:$total_for_months_ref_year/($ref_month-1);
                $for_months_ref_year = array(
                    "total" => $total_for_months_ref_year,
                    "avr"   => $avr_for_months_ref_year,
                    "amount_months" => $ref_month-1,
                );

                // За год --------------------------------------------------
                $for_year = array(
                    "total" => ($k_y==$ref_year)?$avr_for_months_ref_year*12:array_sum($year),
                    "avr"   => ($k_y==$ref_year)?$avr_for_months_ref_year:array_sum($year)/count($year),
                );

                // Результаты --------------------------------------------------
                $analysis[$ps_id][$k_y]["for_year"] = $for_year;
                $analysis[$ps_id][$k_y]["for_months_ref_year"] = $for_months_ref_year;
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
    private function get_sales ($ps_id, $years_for_analysis, $ref_month, $ref_year){
        $sales = false;
        // 1. Получить список месячных интервалов
        $months = self::get_months($years_for_analysis, $ref_year);
//        fn_print_r($months);

        // 2. Cобрать помесячные продажи
        foreach ($months as $k_y=>$year){
            foreach ($year as $k_m=>$month){
                $balance = self::get_sales_pump_series_by_period($ps_id, $month["timestamp"]["begin"], $month["timestamp"]["end"]);
                foreach ($ps_id as $ps){
                    $sales[$ps][$k_y][$k_m] = $balance[$ps]?:0;
                }
            }
        }
        return $sales;
    }

    private function get_sales_pump_series_by_period ($ps_id, $begin, $end){
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
                        $res[$pumps[$i["item_id"]]["ps_id"]] += $i["quantity"];
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
    public function get_months($years_for_analysis, $ref_year){
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













    //==========================================================================
    // OLD FUNCTIONS
    //==========================================================================

    /**
     * Получить список отчетных месяцев
     * @param $time
     * @return array
     */
    public function get_last_months_old ($time){
        $curr_year  = date("Y", $time);
        $curr_month = date("Y", $time);


        $res = array();
        foreach (array($curr_year-1, $curr_year) as $year){
            foreach (array(1,2,3,4,5,6,7,8,9,10,11,12) as $month){
                $begin = $year . "-" . $month . "-" . "1";
                $res[$year][$month]["date"]["begin"] = $begin;
                $res[$year][$month]["date"]["end"]          = $year . "-" . $month . "-" . date("t", strtotime($begin));
            }
        }
        return $res;
    }


    public function get_sales_old ($periods){
        foreach ($periods as $k_y=>$y){
            foreach ($y as $k_m=>$m){
                $balance = self::get_sales_by_month_old($m["date"]);
                $periods[$k_y][$k_m]["balance"] = $balance;
                $periods[$k_y][$k_m]["total_balance"] = array_sum($balance);
            }
        }
        return $periods;
    }

    private function get_sales_by_month_old ($m){
        list($pumps) = fn_uns__get_pumps(array("only_active"=>true, ));
        $params = array(
            "time_from"     => strtotime($m["begin"]),
            "time_to"       => strtotime($m["end"]),
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
                    $res[$pumps[$i["item_id"]]["ps_name"]] += $i["quantity"];
                }
            }
        }

        return $res;
    }

}