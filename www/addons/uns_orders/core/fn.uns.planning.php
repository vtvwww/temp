<?php

/**
 * Class planning
 * Для расчета планирования
 */
class planning {

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
//        fn_print_r($sales);

        // 3. Произвести анализ по каждой серии
        $analysis = self::analysis_sales($sales, $ref_month, $ref_year);
//        fn_print_r($analysis);

        // 4. Выполнить постороение графиков
//        $graphs = self::create_graphs_test();
        $graphs = self::create_graphs($pump_series, $sales, $analysis, $ref_month);

    }


    private function create_graphs ($pump_series, $sales, $analysis, $ref_month){
        if (is__array($pump_series) and is__array($sales) and is__array($analysis)){
            foreach ($pump_series as $ps_id=>$ps){
                $data = array(
                    "series"    => $sales[$ps_id],
                    "analysis"  => $analysis[$ps_id],
                    "ref_month" => $ref_month,
                );
                self::create_graph (DIR_ROOT_CACHE."ps_{$ps_id}.png", $data);
            }
        }
    }

    private function create_graph ($file_name, $data){
//        fn_print_r($data);
        // Размеры графика
        $size = array("w" => 460, "h" => 300);
        $margin = 20;


        /* Create and populate the pData object */
        $MyData = new pData();

        // Ось абсцисс
        $MyData->addPoints(array("", "I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII", ""),"Labels");
        $MyData->setAbscissa("Labels");

        /* Create the pChart object */
        $myPicture = new pImage($size["w"],$size["h"],$MyData);

        // Определить максимальное значения рядов

        list($min, $max) = self::_get_min_max($data["series"], true);

        $i = 0;
        foreach ($data["series"] as $k_s=>$s){
            $i ++;

            if ($i==1){
                $MyData->addPoints(array_merge(array(VOID),$s,array(VOID)),$k_s);
            }elseif ($i == 2){ // график за текущий год
                $p = array();
                for ($m=1; $m<=12; $m++){
                    if ($m<$data["ref_month"]){
                        $p[] = $s[$m];
                    }else{
                        $p[] = VOID;
                    }
                }
                $MyData->addPoints(array_merge(array(VOID),$p,array(VOID)),$k_s);
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
                $myPicture->drawText($size["w"]-40, $size["h"]/2-5, $k_s, TEXT_ALIGN_TOPRIGHT);
            }elseif ($i == 2){
                $myPicture->drawText($size["w"]-40, $size["h"]-5, $k_s, TEXT_ALIGN_TOPRIGHT);
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
//                $myPicture->drawThreshold($avr,array(
//                        "WriteCaption"=>TRUE,"Caption"=>"avr",
//                        "CaptionAlpha" => 100,
//                        "CaptionR"  => 255,
//                        "CaptionG"  => 255,
//                        "CaptionB"  => 255,
//                        "CaptionOffset"=>-16,
//                        "CaptionAlign"=>CAPTION_LEFT_TOP,
//                        "BoxR"      => 0,
//                        "BoxG"      => 0,
//                        "BoxB"      => 0,
//                        "BoxAlpha"  => 100,
//                        "BorderOffset"=>3,
//
//                        "R" => 0,
//                        "G" => 0,
//                        "B" => 0,
//                        "Alpha" => 35,
//                        "Ticks" => 10,
//                        "Weight"=>0.5,
//                        "Wide"  =>true,
//
//                    ));
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
     * Выполнить постороение графиков
     * @param $sales
     * @param $analysis
     */
    private function create_graphs_test ($sales, $analysis){
        /* Create and populate the pData object */
        $MyData = new pData();
        $size = array("w" => 600, "h" => 300);

        // Ось абсцисс
        $MyData->addPoints(array("", "Янв","Фев","Мар","Апр","Май","Июн","Июл","Авг","Сен","Окт","Ноя","Дек", ""),"Labels");
        $MyData->setAbscissa("Labels");

        // Данные
        $MyData->addPoints(array(VOID, 38,10,24,25,25,0,23,22,20,12,10,2, VOID),"Temperature");
//        $MyData->removeSerie("Temperature");
//        $MyData->addPoints(array(2,4,6,4,5,3,6,4,5,8,6,1),"Pressure");

//        $MyData->setSerieDrawable("Temperature",TRUE);
//        $MyData->setSerieDrawable("Pressure",FALSE);








        /* Create the pChart object */
        $myPicture = new pImage($size["w"],$size["h"],$MyData);

        /* Draw the background */
//        $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
//        $myPicture->drawFilledRectangle(0,0,700,390,$Settings);

        /* Overlay with a gradient */
//        $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
//        $myPicture->drawGradientArea(0,0,700,390,DIRECTION_VERTICAL,$Settings);

        /* Add a border to the picture */
//        $myPicture->drawRectangle(0,0,$size["w"]-1,$size["h"]-1,array("R"=>0,"G"=>0,"B"=>0));


        /* Write the chart title */
//        $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/courbd.ttf","FontSize"=>11));
//        $t = "Продажи К20/30 за последние 2 года";
//        $myPicture->drawText($size["w"]/2,8,$t,array("FontSize"=>15,"Align"=>TEXT_ALIGN_TOPMIDDLE));

        $margin = 20;




        /* Define the 1st chart area */
        $myPicture->setGraphArea(1.5*$margin,$margin,$size["w"]-$margin,$size["h"]/2-$margin);
        $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/PFOnlineTwoPro-Single.ttf","FontSize"=>11));

        /* Координатная плоскость */
        $AxisBoundaries = array(0=>array("Min"=>0,"Max"=>50));
        $ScaleSettings  = array(
            "Mode"=>SCALE_MODE_MANUAL, "ManualScale"=>$AxisBoundaries,
            "CycleBackground"=>TRUE,
            "DrawSubTicks"=>TRUE,
            "DrawXLines"=>false,
            "GridR"=>0,"GridG"=>0,"GridB"=>0,
        );
        $myPicture->drawScale($ScaleSettings);

        /* Описание оси абсцисс */
        $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/PFOnlineTwoPro-Double.ttf","FontSize"=>11));
        $myPicture->drawText($size["w"]-54, $size["h"]/2-5, "2013", TEXT_ALIGN_TOPRIGHT);

        /* Прямоугольники */
        $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/PFOnlineTwoPro-Double.ttf","FontSize"=>11));
        $myPicture->drawBarChart(array(
                "DisplayPos"=>LABEL_POS_OUTSIDE,
                "DisplayOrientation"=>ORIENTATION_VERTICAL,
                "DisplayValues"=>TRUE,
                "Surrounding"=>-20,"InnerSurrounding"=>20,
            )
        );

        /* Среднее значение */
        $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/PFOnlineTwoPro-Double.ttf","FontSize"=>10));
        $myPicture->drawThreshold(17,array(
                "WriteCaption"=>TRUE,"Caption"=>"Ср.зн.",
                "CaptionAlpha" => 100,
                "CaptionR"  => 255,
                "CaptionG"  => 255,
                "CaptionB"  => 255,
                "CaptionOffset"=>-16,
                "CaptionAlign"=>CAPTION_LEFT_TOP,
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
        $myPicture->drawThreshold(17,array(
                "WriteCaption"=>TRUE,"Caption"=>"19",
                "CaptionAlpha" => 100,
                "CaptionR"  => 255,
                "CaptionG"  => 255,
                "CaptionB"  => 255,
                "CaptionOffset"=>-16,
                "CaptionAlign"=>CAPTION_RIGHT_BOTTOM,
                "BoxR"      => 0,
                "BoxG"      => 0,
                "BoxB"      => 0,
                "BoxAlpha"  => 100,
                "BorderOffset"=>3,

                "R" => 0,
                "G" => 0,
                "B" => 0,
                "Alpha" => 0,
                "Ticks" => 0,
                "Weight"=>0,
//                "Wide"  =>true,

            ));


        /* Define the 2nd chart area */
        $myPicture->setGraphArea(1.5*$margin,$margin+$size["h"]/2,$size["w"]-$margin,$size["h"]-$margin);
        $myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/PFOnlineTwoPro-Single.ttf","FontSize"=>8));

        /* Draw the scale */
        $scaleSettings = array("DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,);
        $MyData->setSerieDrawable("Temperature",FALSE);
        $MyData->setSerieDrawable("Pressure",TRUE);
//        $MyData->setAxisName(0,"Pressure");
        $myPicture->drawScale($scaleSettings);
        $myPicture->drawBarChart(array("Surrounding"=>-30,"InnerSurrounding"=>30));

        /* Render the picture (choose the best way) */
        $myPicture->Render(DIR_ROOT_CACHE."simple.png");
//        $myPicture->Stroke();
//        $myPicture->autoOutput("pictures/example.drawBarChart.simple.png");
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