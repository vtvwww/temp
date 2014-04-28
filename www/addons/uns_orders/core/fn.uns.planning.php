<?php

/**
 * Class planning
 * Для расчета планирования
 */
class planning {

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
    public function calc ($params){
        $ps_id              = $params["ps_id"];
        $ref_month          = $params["month"];
        $ref_year           = $params["year"];
        $week_supply        = $params["week_supply"];       // Запас по продажам
        $years_for_analysis = $params["years_for_analysis"];
        $koef_plan_prodazh  = $params["koef_plan_prodazh"];

        // 1. Получить список серий насосов
        $pump_series = array_shift(fn_uns__get_pump_series(array("ps_id"=> $ps_id)));
//        fn_print_r($pump_series);

        // 2. Получить статистику продаж по указанным сериям за последние YEARS_FOR_ANALYSIS
        $sales = self::get_sales(array_keys($pump_series), $years_for_analysis, $ref_month, $ref_year);
//        fn_print_r($sales);

        // 3. Произвести анализ по каждой серии
        $analysis = self::analysis_sales($sales, $ref_month, $ref_year);
//        fn_print_r($analysis);

        // 4. Выполнить постороение графиков
        $graphs = self::create_graphs($pump_series, $sales, $analysis, $ref_month);
//        fn_print_r($graphs);

        // 5. Выполнить расчет плана
        $planning = self::planning($pump_series, $sales, $analysis, $ref_month, $ref_year, $week_supply, $koef_plan_prodazh);

        return array($pump_series, $sales, $analysis, $planning);
    }


    /**
     * @param $pump_series
     * @param $sales
     * @param $analysis
     * @param $ref_month
     * @param $ref_year
     * @param $week_supply
     */
    private function planning($pump_series, $sales, $analysis, $ref_month, $ref_year, $week_supply, $koef_plan_prodazh){
        $res = array();
        if (is__array($pump_series) and is__array($sales) and is__array($analysis)){
            $ps_ids = array_keys($pump_series);

            // 1. Определить НАЧАЛЬНЫЙ ОСТАТОК ПРОДУКЦИИ на начало расчетного месяца ref_month/ref_year
            $nach_ostatok   = self::_get_nach_ostatok($ps_ids, $ref_month, $ref_year);

            // 2. Определить ИМЕЮЩИЙСЯ ЗАДЕЛ по сериям по открытым партиям насосов на 01/ref_month/ref_year 00:00:00
            $zadel          = self::_get_zadel($ps_ids, $ref_month, $ref_year);

            // 3. Определить ИМЕЮЩИЕСЯ ЗАКАЗЫ по сериям на 01/ref_month/ref_year 00:00:00
            $zakaz          = self::_get_zakaz($ps_ids, $ref_month, $ref_year);

            // 4. Определить ПЛАН ПРОДАЖ на расчетный месяц
            // Если расчетный ПЛАН ПРОДАЖ <= ИМЕЮЩИМСЯ ЗАКАЗАМ, тогда необходимо увеличить
            // ПЛАН ПРОДАЖ до величины ИМЕЮЩИХСЯ ЗАКАЗОВ + xx% "koef_plan_prodazh"
            $plan_prodazh   = self::_get_plan_prodazh ($ps_ids, $zakaz, $ref_month, $ref_year, $koef_plan_prodazh);

            // 5. Определить КОНЕЧНЫЙ ОСТАТОК на конец расчетного месяца
            $konech_ostatok = self::_get_konech_ostatok($ps_ids, $plan_prodazh, $week_supply);

            // 6. Выполнить ПЛАН ПРОИЗВОДСТВА на расчетный месяц
            // Кон. Ост. = Нач. Ост. + Задел + План Производства - Продажи;
            // План Производства = Кон. Ост. - Нач. Ост. - Задел + Продажи;
            $production_plan= self::_get_production_plan ($ps_ids, $nach_ostatok, $zadel, $plan_prodazh, $konech_ostatok);

            foreach ($pump_series as $ps_id=>$ps){
                $res[$ps_id] = array(
                    "nach_ostatok"  => $nach_ostatok[$ps_id],
                    "zadel"         => $zadel[$ps_id],
                    "zakaz"         => $zakaz[$ps_id],
                    "plan_prodazh"  => $plan_prodazh[$ps_id],
                    "konech_ostatok"=> $konech_ostatok[$ps_id],
                    "production_plan"=> $production_plan
                );
            }
        }
        return $res;
    }

    /**
     * Выполнить ПЛАН ПРОИЗВОДСТВА на расчетный месяц
     * Кон. Ост. = Нач. Ост. + Задел + План Производства - Продажи;
     * План Производства = Кон. Ост. - Нач. Ост. - Задел + Продажи;
     */
    private function _get_production_plan (){

    }


    /**
     * Определить КОНЕЧНЫЙ ОСТАТОК на конец расчетного месяца
     *
     */
    private function _get_konech_ostatok (){

    }


    /**
     * Определить ПЛАН ПРОДАЖ на расчетный месяц
     */
    private function _get_plan_prodazh (){

    }

    /**
     * Определить ИМЕЮЩИЕСЯ ЗАКАЗЫ по сериям на 01/ref_month/ref_year 00:00:00
     * @param $ps_id
     * @param $ref_month
     * @param $ref_year
     */
    private function _get_zakaz ($ps_id, $ref_month, $ref_year){
        $res = array();
        $time_from  = strtotime("$ref_year/$ref_month/01 00:00:00");
        $time_to    = strtotime("$ref_year/$ref_month/01 00:00:01");
        $pumps      = array_shift(fn_uns__get_pumps(array("ps_id"=>$ps_ids, "only_active"=>true,)));
        $orders     = array_shift(fn_acc__get_orders());
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
    private function create_graphs ($pump_series, $sales, $analysis, $ref_month){
        if (is__array($pump_series) and is__array($sales) and is__array($analysis)){
            foreach ($pump_series as $ps_id=>$ps){
                $data = array(
                    "series"    => $sales[$ps_id],
                    "analysis"  => $analysis[$ps_id],
                    "ref_month" => $ref_month,
                );
                self::_create_graph (DIR_ROOT . "/skins/basic/admin/images/uns_charts/"."ps_{$ps_id}.png", $data);
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


        /* Create and populate the pData object */
        $MyData = new pData();

        // Ось абсцисс
        $MyData->addPoints(array("I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII", ""),"Labels");
        $MyData->setAbscissa("Labels");

        /* Create the pChart object */
        $myPicture = new pImage($size["w"],$size["h"],$MyData);

        // Определить максимальное значения рядов

        list($min, $max) = self::_get_min_max($data["series"], true);

        $i = 0;
        foreach ($data["series"] as $k_s=>$s){
            $i ++;

            if ($i==1){
                $MyData->addPoints(array_merge($s,array(VOID)),$k_s);
            }elseif ($i == 2){ // график за текущий год
                $p = array();
                for ($m=1; $m<=12; $m++){
                    if ($m<$data["ref_month"]){
                        $p[] = $s[$m];
                    }else{
                        $p[] = VOID;
                    }
                }
                $MyData->addPoints(array_merge($p,array(VOID)),$k_s);
            }

            if ($i==1){
                $myPicture->setGraphArea(1.5*$margin, $margin,              $size["w"]-$margin, $size["h"]/2-$margin);
            }elseif ($i == 2){
                $myPicture->setGraphArea(1.5*$margin, $margin+$size["h"]/2, $size["w"]-$margin, $size["h"]-$margin);
            }
            $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/pf_arma_five.ttf","FontSize"=>12));

            /* Координатная плоскость */
            $AxisBoundaries = array(0=>array("Min"=>$min,"Max"=>$max));
            $ScaleSettings  = array(
                "Mode"=>SCALE_MODE_MANUAL, "ManualScale"=>$AxisBoundaries,
                "CycleBackground"=>TRUE,
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

            /* Прямоугольники */
            $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/pf_arma_five.ttf","FontSize"=>12));
            $myPicture->drawBarChart(array(
                    "DisplayPos"=>LABEL_POS_OUTSIDE,
                    "DisplayOrientation"=>ORIENTATION_VERTICAL,
                    "DisplayValues"=>TRUE,
                    "Surrounding"=>-20,"InnerSurrounding"=>20,
                )
            );

            /* Среднее значение */
            $avr = $data["analysis"][$k_s]["for_year"]["avr"];

            if ($avr){
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
            $MyData->removeSerie($k_s);
        }
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
        $months = self::_get_months($years_for_analysis, $ref_year);

        // 2. Cобрать помесячные продажи
        foreach ($months as $k_y=>$year){
            foreach ($year as $k_m=>$month){
                $balance = self::_get_sales_pump_series_by_period($ps_id, $month["timestamp"]["begin"], $month["timestamp"]["end"]);
                foreach ($ps_id as $ps){
                    $sales[$ps][$k_y][$k_m] = $balance[$ps]?:0;
                }
            }
        }
        return $sales;
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
}