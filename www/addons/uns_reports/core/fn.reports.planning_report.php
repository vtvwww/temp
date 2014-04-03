<?php
function fn_rpt__planning_report($data){
//    $period = "";
//    if (fn_date_format($_REQUEST["time_from"], "%d/%m/%Y") == fn_date_format($_REQUEST["time_to"], "%d/%m/%Y")){
//        $period = "за " . fn_date_format($_REQUEST["time_from"], "%a %d/%m/%Y");
//    }else{
//        $period = "за период " . fn_date_format($_REQUEST["time_from"], "%a %d/%m/%Y") . " – " . fn_date_format($_REQUEST["time_to"], "%a %d/%m/%Y");
//    }

    $months = array(
        1  => array("full"=>"Январь",   "short"=>"Янв", "arab"=>"I"),
        2  => array("full"=>"Февраль",  "short"=>"Фев", "arab"=>"II"),
        3  => array("full"=>"Март",     "short"=>"Мар", "arab"=>"III"),
        4  => array("full"=>"Апрель",   "short"=>"Апр", "arab"=>"IV"),
        5  => array("full"=>"Май",      "short"=>"Май", "arab"=>"V"),
        6  => array("full"=>"Июнь",     "short"=>"Июн", "arab"=>"VI"),
        7  => array("full"=>"Июль",     "short"=>"Июл", "arab"=>"VII"),
        8  => array("full"=>"Август",   "short"=>"Авг", "arab"=>"VIII"),
        9  => array("full"=>"Сентябрь", "short"=>"Сен", "arab"=>"IX"),
        10 => array("full"=>"Октябрь",  "short"=>"Окт", "arab"=>"X"),
        11 => array("full"=>"Ноябрь",   "short"=>"Ноя", "arab"=>"XI"),
        12 => array("full"=>"Декабрь",  "short"=>"Дек", "arab"=>"XII"),
    );

    $pdf = new UNS_TCPDF();
    $params = array(
        'header_title'  => "ООО «ТОРГОВЫЙ ДОМ «УКРНАСОССЕРВИС»",
        'header_text'   => $pdf->uns__strtoupper("ПРОИЗВОДСТВЕННОЕ ПЛАНИРОВАНИЕ НА " . $months[$_REQUEST["planning"]["month"]]["full"] . " " . $_REQUEST["planning"]["year"] . " ГОДА") . " (по состоянию на " . fn_date_format($_REQUEST["time_from"], "%a %d/%m/%Y") . ")",
        'FooterData_tc' => array(0),
        'HeaderData_lw' => 1.5,
        'Margins_Left'  => 15,
        'Margins_Top'   => 15,
        'Margins_Right' => 15,

    );
    $pdf->uns_set_config($params);
    $pdf->AddPage();


    // init
    $pdf->SetTextColor(0, 0, 0);
    $pdf->uns_SetFont("B", 10);

    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
    $pdf->SetTextColor(0); // BLACK = 0

    $pdf->uns_SetFont("BI", 18);
    $pdf->MultiCell(180,  8, "ИСХОДНЫЕ ДАННЫЕ ДЛЯ ПРОИЗВОДСТВЕННОГО ПЛАНИРОВАНИЯ\nна " . $months[$_REQUEST["planning"]["month"]]["full"] . " " . $_REQUEST["planning"]["year"] . " года", 0, "C");
    $pdf->uns_SetFont("BI", 14);
    $pdf->MultiCell(180,  8, "(по состоянию на " . fn_date_format($_REQUEST["time_from"], "%a %d/%m/%Y") . ")", 0, "C");
    $pdf->uns_SetFont("B", 18);
    $pdf->MultiCell(180,  8, $period, 0, "C");


    // СБОР ДАННЫХ: 1. Серии насосов
    $ps_ids = array(
//        29, //К8/18
//        37, //К20/30
//        65, //К160/30
//        88, //2СМ80-50-200
//        94, //СД50/56
    );
    list($pump_series) = fn_uns__get_pump_series(array("ps_id"=>$ps_ids));

    // СБОР ДАННЫХ: 2. Склад готовой продукции
    $balance_sgp = array_shift(fn_uns__get_balance_sgp($_REQUEST, true, true, true, false));

    // СБОР ДАННЫХ: 3. Партии ожидающие сборку
    $kits_opened = array_shift(fn_acc__get_kits(array("only_opened" => true)));

    // СБОР ДАННЫХ: 4. Заказы
    $orders = array_shift(fn_acc__get_orders(array("with_items"=>true, "only_active"=>true)));
    $regions = array_shift(fn_uns__get_regions());

    // СБОР ДАННЫХ: 5. План
    $plan = fn_planning_report__get_plan($_REQUEST["planning"]["month"], $_REQUEST["planning"]["year"]);

    // СБОР ДАННЫХ: 6. Статистика
    $stat_sales = fn_planning_report__get_stat_sales($_REQUEST["planning"]["year"]);

    $i = 0;
    foreach ($pump_series as $ps){
        // СЕРИЯ НАСОСА ========================================================
        $pdf->uns_SetFont("B", 18);
        $pdf->MultiCell(180,  7, ++$i . ". Насос " . $ps["ps_name"], array("B"=>array('width' => 0.8,'color' => array(0, 0, 0))), "L");
        $pdf->ln(2);
        // =====================================================================

        // СПИСОК НАСОСОВ ЭТОЙ СЕРИИ ===========================================
        $pumps = array_shift(fn_uns__get_pumps(array("ps_id"=>$ps["ps_id"])));
        $pumps_ids = array_keys($pumps);
        // =====================================================================

        // ТЕКУЩИЕ ОСТАТКИ =====================================================
        // 1. Текущий остаток по складу готовой продукции
        $data_sgp = array("P" => 0, "PF" => 0, "PA" => 0);
        if (is__array($balance_sgp)){
            foreach ($balance_sgp as $b_sgp__k_type=>$b_sgp__type){
                foreach ($b_sgp__type as $b_sgp__groups){
                    foreach ($b_sgp__groups["items"] as $b_sgp__pumps){
                        if (in_array($b_sgp__pumps["id"], $pumps_ids)){
                            $data_sgp[$b_sgp__k_type] += $b_sgp__pumps["konech"];
                        }
                    }
                }
            }
        }

        // 2. Комплекты ожидающие сборку
        $data_kits = 0;
        if (is__array($kits_opened)){
            foreach ($kits_opened as $kit){
                if (in_array($kit["p_id"], $pumps_ids)){
                    $data_kits += $kit["p_quantity"];
                }
            }
        }

        // 3. Заказы
        $data_orders = array();
        if (is__array($orders)){
            foreach ($orders as $o){
                if (is__array($o["items"])){
                    foreach ($o["items"] as $o_i){
                        if (in_array($o_i["p_id"], $pumps_ids)){
                            $data_orders[$o["order_id"]]["region_id"] = $o["region_id"];
                            $data_orders[$o["order_id"]]["quantity"] += $o_i["quantity"];
                        }
                    }
                }
            }
        }

        // 4. План
        $data_plan = (is__more_0($plan[$ps["ps_id"]]["quantity"]))?$plan[$ps["ps_id"]]["quantity"]:0;

        // 5. Чугунная комплектация насоса =====================================
        list($data_packing_list, $data_packing_list_balance_sl, $data_packing_list_balance_mc_sk) = fn_planning_report__get_packing_list($pumps_ids[0]);
//        fn_print_r($data_packing_list);

        // *********************************************************************
        // ТАБЛИЦА 1 - Остатки, задел, заказы и план производства по насосу
        // *********************************************************************
        $align = "C";
        $border = 1;
        $h = 10;
        $pdf->uns_SetFont("B", 9);
        $font_grey = 240;

        // thead
        if ($pdf->GetY() >= 270)  {$pdf->AddPage(); $pdf->ln(2);}
        $pdf->MultiCell(5,   5, "", 0, "L", false, 0);
        $pdf->MultiCell(175,   5, "ТАБЛИЦА 1. " . "Остатки, задел, заказы и план производства по насосу " . $ps["ps_name"], 0, "L", false, 1);

        $pdf->uns_SetFont("R", 9);
        $pdf->MultiCell(5,   5, "", 0, "L", false, 0);
        $pdf->MultiCell(4*15,  8, "Текущий остаток на\nСКЛАДЕ ГОТОВОЙ ПРОДУКЦИИ", array('LTRB' => array('width' => 0.3)), $align, false, 0);
        $pdf->MultiCell(  20,  12.5, "Комплекты ожидающие сборку", $border, $align, false, 0);
        if (is__array($orders)){
            if (count($orders) > 1){
                $pdf->MultiCell(10*(1+count($orders)),  8, "Заказы", $border, $align, false, 0);
            }else{
                $pdf->MultiCell(10,  8, "Заказы", $border, $align, false, 0);
            }
        }
        $pdf->MultiCell(25,  8, "План\nпроизводства", $border, $align, false, 1);
        //****
        $pdf->MultiCell(5,   4, "", 0, "L", false, 0);
        $pdf->MultiCell(15,  4, "Насос",    $border, $align, false, 0);
        $pdf->MultiCell(15,  4, "На раме",  $border, $align, false, 0);
        $pdf->MultiCell(15,  4, "Агрегат",  $border, $align, false, 0);
        $pdf->MultiCell(15,  4, "==",       $border, $align, false, 0);
        $pdf->MultiCell(20,  4, " ",         0, $align, false, 0);
        if (is__array($orders)){
            foreach ($orders as $o){
                $pdf->MultiCell(10,  4, $regions[$o["region_id"]]["name_short"], $border, $align, false, 0);
            }
            if (count($orders) > 1){
                $pdf->MultiCell(10,  4, "==",       $border, $align, false, 0);
            }
        }
        $pdf->MultiCell(25,  4, $pdf->uns__strtolower($months[$_REQUEST["planning"]["month"]]["full"]) . " " . $_REQUEST["planning"]["year"], $border, $align, false, 1);

        // tbody
        $pdf->uns_SetFont("R", 10);
        $pdf->MultiCell(5,   4, "", 0, "L", false, 0);

        // сгп
        if (is__more_0($data_sgp["P"])) $pdf->SetTextColor(0);
        else $pdf->SetTextColor($font_grey);
        $pdf->MultiCell(15,  4, $data_sgp["P"],    $border, $align, false, 0);

        if (is__more_0($data_sgp["PF"])) $pdf->SetTextColor(0);
        else $pdf->SetTextColor($font_grey);
        $pdf->MultiCell(15,  4, $data_sgp["PF"],  $border, $align, false, 0);

        if (is__more_0($data_sgp["PA"])) $pdf->SetTextColor(0);
        else $pdf->SetTextColor($font_grey);
        $pdf->MultiCell(15,  4, $data_sgp["PA"],  $border, $align, false, 0);

        if (is__more_0(array_sum($data_sgp))) $pdf->SetTextColor(0);
        else $pdf->SetTextColor($font_grey);
        $pdf->uns_SetFont("B", 10);
        $pdf->MultiCell(15,  4, array_sum($data_sgp),       $border, $align, false, 0);
        $pdf->uns_SetFont("R", 10);

        // комплекты
        if (is__more_0($data_kits)) $pdf->SetTextColor(0);
        else $pdf->SetTextColor($font_grey);
        $pdf->uns_SetFont("B", 10);
        $pdf->MultiCell(20,  4, $data_kits,         $border, $align, false, 0);
        $pdf->uns_SetFont("R", 10);
        $pdf->SetTextColor(0);

        // заказы
        if (is__array($orders)){
            $order_total_quantity = 0;
            foreach ($orders as $o){
                $order_quantity = (is__more_0($data_orders[$o["order_id"]]["quantity"]))?$data_orders[$o["order_id"]]["quantity"]:0;
                $order_total_quantity += $order_quantity;
                if (is__more_0($order_quantity)) $pdf->SetTextColor(0);
                else $pdf->SetTextColor($font_grey);
                if (count($orders) == 1) $pdf->uns_SetFont("B", 10);
                $pdf->MultiCell(10,  4, $order_quantity, $border, $align, false, 0);
                $pdf->uns_SetFont("R", 10);
            }
            if (count($orders) > 1){
                if (is__more_0($order_total_quantity)) $pdf->SetTextColor(0);
                else $pdf->SetTextColor($font_grey);
                $pdf->uns_SetFont("B", 10);
                $pdf->MultiCell(10,  4, $order_total_quantity,       $border, $align, false, 0);
                $pdf->uns_SetFont("R", 10);
            }
        }

        if (is__more_0($data_plan)) $pdf->SetTextColor(0);
        else $pdf->SetTextColor($font_grey);
        $pdf->uns_SetFont("B", 10);
        $pdf->MultiCell(25,  4, $data_plan, $border, $align, false, 1);
        $pdf->uns_SetFont("R", 10);

        $pdf->SetTextColor(0);
        $pdf->ln(5);
        // =====================================================================


        // *********************************************************************
        // ТАБЛИЦА 2 - Помесячная статистика продаж по насосу
        // *********************************************************************
        $pdf->uns_SetFont("B", 9);
        $curr_year = $_REQUEST["planning"]["year"];
        $prev_year = $curr_year-1;

        if ($pdf->GetY() >= 270)  {$pdf->AddPage(); $pdf->ln(2);}
        $pdf->MultiCell(5,   5, "", 0, "L", false, 0);
        $pdf->MultiCell(175,   5, "ТАБЛИЦА 2. " . "Помесячная статистика продаж за предыдущий и текущий года по насосу " . $ps["ps_name"], 0, "L", false, 1);

        // PREV_YEAR
        $pdf->uns_SetFont("R", 9);
        $total_q = 0;
        $pdf->MultiCell(5,   5, "", 0, "L", false, 0);
        for ($j=1;$j<=12;$j++){
            $pdf->MultiCell(13,  5, $months[$j]["short"] . "/" . substr($prev_year, -2), $border, $align, false, 0);
        }
        $pdf->uns_SetFont("B", 10);
        $pdf->MultiCell(0.5, 5, "", $border, $align, false, 0);
        $pdf->MultiCell(13,  5, $prev_year, $border, $align, false, 1);

        $pdf->uns_SetFont("B", 10);
        $pdf->MultiCell(5,   5, "", 0, "L", false, 0);
        for ($j=1;$j<=12;$j++){
            $q = (is__more_0($stat_sales[$prev_year][$ps["ps_id"]]["m".$j]))?$stat_sales[$prev_year][$ps["ps_id"]]["m".$j]:0;
            if (is__more_0($q)) $pdf->SetTextColor(0);
            else $pdf->SetTextColor($font_grey);
            $pdf->MultiCell(13,  5, $q, $border, $align, false, 0);
            $total_q += $q;
        }
        if (is__more_0($total_q)) $pdf->SetTextColor(0);
        else $pdf->SetTextColor($font_grey);
        $pdf->MultiCell(0.5, 5, "", $border, $align, false, 0);
        $pdf->MultiCell(13,  5, $total_q, $border, $align, false, 1);
        $pdf->ln(2);

        // CURR_YEAR
        $total_q = 0;
        $pdf->SetTextColor(0);
        $pdf->uns_SetFont("R", 9);

        $pdf->MultiCell(5,   5, "", 0, "L", false, 0);
        for ($j=1;$j<=12;$j++){
            $pdf->MultiCell(13,  5, $months[$j]["short"] . "/" . substr($curr_year, -2), $border, $align, false, 0);
        }
        $pdf->uns_SetFont("B", 10);
        $pdf->MultiCell(0.5, 5, "", $border, $align, false, 0);
        $pdf->MultiCell(13,  5, $curr_year, $border, $align, false, 1);

        $pdf->uns_SetFont("B", 10);
        $pdf->MultiCell(5,   5, "", 0, "L", false, 0);
        for ($j=1;$j<=12;$j++){
            $q = (is__more_0($stat_sales[$curr_year][$ps["ps_id"]]["m".$j]))?$stat_sales[$curr_year][$ps["ps_id"]]["m".$j]:0;
            if (is__more_0($q)) $pdf->SetTextColor(0);
            else $pdf->SetTextColor($font_grey);
            if (date('n')<=$j) $q = '';
            $pdf->MultiCell(13,  5, $q, $border, $align, false, 0);
            $total_q += $q;
        }
        if (is__more_0($total_q)) $pdf->SetTextColor(0);
        else $pdf->SetTextColor($font_grey);
        $pdf->MultiCell(0.5, 5, "", $border, $align, false, 0);
        $pdf->MultiCell(13,  5, $total_q, $border, $align, false, 1);
        $pdf->SetTextColor(0);
        $pdf->ln(5);

        // *********************************************************************
        // ТАБЛИЦА 3 - Остатки чугунной комплектации насоса по Складу литья, мех. цехам и складу комплектующих
        // *********************************************************************
        $pdf->uns_SetFont("B", 9);
        $curr_year = $_REQUEST["planning"]["year"];
        $prev_year = $curr_year-1;

        if ($pdf->GetY() >= 270)  {$pdf->AddPage(); $pdf->ln(2);}
        $pdf->MultiCell(5,   5, "", 0, "L", false, 0);
        $pdf->MultiCell(175,   5, "ТАБЛИЦА 3. " . "Остатки чугунной комплектации насоса по Складу литья, мех. цехам и складу комплектующих", 0, "L", false, 1);

        $h          = 6;
        $border     = 1;
        $align      = 'C';
        $fill       = true;
        $ln         = 0;
        $x = $y     = '';
        $reseth     = true;
        $stretch    = 0;
        $ishtml     = false;
        $autopadding= false;
        $maxh       = $h;
        $valign     = 'M';
        $fitcell    = true;

        $pdf->uns_SetFont("B", 9);

        // thead

        $pdf->uns_SetFont("R", 9);
        $pdf->MultiCell(5,   5, "", 0, "L", false, 0);
        $pdf->MultiCell(6,   9, "№", 1,                    $align, false, 0);
        $pdf->MultiCell(72,  9, "Наименование детали", 1,   $align, false, 0);
        $pdf->MultiCell(9,  9, "Кол-во", 1,                $align, false, 0);
        $pdf->MultiCell(0.5,   9, "", 1,                   $align, false, 0);
        $pdf->MultiCell(9,  9, "МЦ1\nОбр.", 1,             $align, false, 0);
        $pdf->MultiCell(9,  9, "МЦ1\nЗав.", 1,             $align, false, 0);
        $pdf->MultiCell(9,  9, "МЦ2\nОбр.", 1,             $align, false, 0);
        $pdf->MultiCell(9,  9, "МЦ2\nЗав.", 1,             $align, false, 0);
        $pdf->MultiCell(9,  9, "Скл.\nКМП", 1,            $align, false, 0);
        $pdf->MultiCell(9,  9, "==", 1,            $align, false, 0);
        $pdf->MultiCell(0.5,   9, "", 1,                   $align, false, 0);
        $pdf->MultiCell(11,  9, "Номер\nлитья", 1, $align, false, 0);
        $pdf->MultiCell(11,  9, "Скл.\nЛИТЬЯ",  1, $align, false, 0);
        $pdf->MultiCell(11,  9, "Вес\nлитья",   1, $align, false, 1);

        if (is__array($data_packing_list)){
            $j = 0;
            foreach ($data_packing_list as $d){
                if ($pdf->GetY() >= 284)  {$pdf->AddPage(); $pdf->ln(2);}
                $m1_p = $data_packing_list_balance_mc_sk[10][$d["detail_id"]]["processing_konech"];
                $m1_c = $data_packing_list_balance_mc_sk[10][$d["detail_id"]]["complete_konech"];
                $m2_p = $data_packing_list_balance_mc_sk[14][$d["detail_id"]]["processing_konech"];
                $m2_c = $data_packing_list_balance_mc_sk[14][$d["detail_id"]]["complete_konech"];
                $sk   = $data_packing_list_balance_mc_sk[17][$d["detail_id"]]["konech"];
                $v = array(
                    0=>++$j,
                    1=>$d["detail_name"] . ((strlen($d["detail_no"])>1)?" [{$d["detail_no"]}]":""),
                    2=>fn_fvalue($d["quantity"]),
                    3=>(strpos($d["detail_name"], "П/М")===false)?fn_fvalue($m1_p):"",
                    4=>(strpos($d["detail_name"], "П/М")===false)?fn_fvalue($m1_c):"",
                    5=>(strpos($d["detail_name"], "П/М")===false)?fn_fvalue($m2_p):"",
                    6=>(strpos($d["detail_name"], "П/М")===false)?fn_fvalue($m2_c):"",
                    7=>(strpos($d["detail_name"], "П/М")===false)?fn_fvalue($sk):"",
                    8=>(strpos($d["detail_name"], "П/М")===false)?fn_fvalue($m1_p+$m1_c+$m2_p+$m2_c+$sk):"",
                    9=>$d["material_no"],
                    10=>fn_fvalue($data_packing_list_balance_sl[$d["material_id"]]["ko"]),
                    11=>fn_fvalue($d["material_weight"]),
                );
                $pdf->MultiCell(5,      5,   "",     0, "L", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                $pdf->SetTextColor(0);
                $pdf->MultiCell(6,      5,   $v[0],  1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                $pdf->SetTextColor(0);
                $pdf->MultiCell(72,     5,   $v[1],  1, "L", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                $pdf->SetTextColor(0);
                $pdf->MultiCell(9,      5,   $v[2],  1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell(0.5,    5,   "",     1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                if (is__more_0($v[3])) $pdf->SetTextColor(0);
                else $pdf->SetTextColor($font_grey);
                $pdf->MultiCell(9,      5,   $v[3],  1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                if (is__more_0($v[4])) $pdf->SetTextColor(0);
                else $pdf->SetTextColor($font_grey);
                $pdf->MultiCell(9,      5,   $v[4],  1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                if (is__more_0($v[5])) $pdf->SetTextColor(0);
                else $pdf->SetTextColor($font_grey);
                $pdf->MultiCell(9,      5,   $v[5],  1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                if (is__more_0($v[6])) $pdf->SetTextColor(0);
                else $pdf->SetTextColor($font_grey);
                $pdf->MultiCell(9,      5,   $v[6],  1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                if (is__more_0($v[7])) $pdf->SetTextColor(0);
                else $pdf->SetTextColor($font_grey);
                $pdf->MultiCell(9,      5,   $v[7],  1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                $pdf->uns_SetFont("B", 9);
                if (is__more_0($v[8])) $pdf->SetTextColor(0);
                else $pdf->SetTextColor($font_grey);
                $pdf->MultiCell(9,      5,   $v[8],    1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->uns_SetFont("R", 9);

                $pdf->SetTextColor(0);
                $pdf->MultiCell(0.5,    5,   "",     1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                $pdf->SetTextColor(0);
                $pdf->MultiCell(11,     5,   $v[9],  1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                $pdf->uns_SetFont("B", 9);
                if (is__more_0($v[10])) $pdf->SetTextColor(0);
                else $pdf->SetTextColor($font_grey);
                $pdf->MultiCell(11,     5,   $v[10],  1, "C", false, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->uns_SetFont("R", 9);

                $pdf->SetTextColor(0);
                $pdf->MultiCell(11,     5,   $v[11], 1, "C", false, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
        }

        $pdf->ln(13);
    }



    $pdf->Output(str_replace(array('fn.reports.', '.php'), '', basename(__FILE__)) . "_" . strftime("%Y-%m-%d_%H-%M", time()) . ".pdf", 'I');
    return true;
    //**************************************************************************
}

// Получить план производства
function fn_planning_report__get_plan($month=0, $year=0){
    $res = array();
    if (!is__more_0($month) or !is__more_0($year)) return false;
    $sql = db_quote(UNS_DB_PREFIX . "SELECT ps_id, quantity FROM ?:_plans WHERE month = ?i AND year = ?i", $month, $year);
    $res = db_get_hash_array($sql, "ps_id");
    return $res;
}

// получить статистику продаж текущего и предыдущего года
function fn_planning_report__get_stat_sales ($curr_year){
    $res = array();
    $prev_year = $curr_year - 1;
    $res[$prev_year] = db_get_hash_array(UNS_DB_PREFIX . "SELECT * FROM ?:_stat_sales WHERE year = $prev_year", "ps_id");
    $res[$curr_year] = db_get_hash_array(UNS_DB_PREFIX . "SELECT * FROM ?:_stat_sales WHERE year = $curr_year", "ps_id");
    return $res;
}

// Получить информацию о комплекности насоса
function fn_planning_report__get_packing_list($pump_id){
    $packing_list = fn_uns__get_packing_list_by_pump($pump_id, "D");
    list($packing_list_details) = fn_uns__get_details(array("detail_id"=>array_keys($packing_list), "with_material_info" => true));
    $material_ids = array();
    foreach ($packing_list_details as $k=>$v){
        // информация о детали
        $packing_list_details[$k] = array_merge($packing_list_details[$k], $packing_list[$k]);

        // вес о материале
        $w = fn_uns__get_accounting_item_weights("M", $v['material_id']);
        $packing_list_details[$k]['material_weight'] = $w[$v['material_id']]["M"][0]['value'];

        // список материалов
        $material_ids[] = $v['material_id'];
    }

    // Получить баланс по чугунным отливкам
    $balance_sl = array();
    if (is__array($material_ids)){
        $p = array();
        list ($p['time_from'], $p['time_to']) = fn_create_periods($_REQUEST);
        $p['item_id']   = $p['material_id'] = $material_ids;
        $p['item_type'] = "M";
        $p['o_id'] = array(8);
        list($balance_sl) = fn_uns__get_balance($p);
    }

    // Получить баланс по деталям
    $balance_mc_sk = array();
    $p = array();
    list ($p['time_from'], $p['time_to']) = fn_create_periods($_REQUEST);
    $p['item_id'] = array_keys($packing_list);
    list($temp_balance_mc_sk) = fn_uns__get_balance_mc_sk_su($p, true, true);
    // упростить для вывода
    if (is__array($temp_balance_mc_sk)){
        foreach ($temp_balance_mc_sk as $k_o=>$v_o){
            foreach ($v_o as $group){
                foreach ($group['items'] as $k_d=>$v_d){
                    $balance_mc_sk[$k_o][$k_d] = $v_d;
                }
            }
        }
    }
    return array($packing_list_details, $balance_sl, $balance_mc_sk);


}
