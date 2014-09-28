<?php
// mb_convert_case($group['group'], MB_CASE_UPPER, "utf-8")
function fn_rpt__mc($data){
    $pdf = new UNS_TCPDF();
    $period = fn_get_period_name($data['period'], $data['time_from'], $data['time_to']);
    $params = array(
        'header_title' =>   "ООО «ТОРГОВЫЙ ДОМ «УКРНАСОССЕРВИС»",
        'header_text' =>    $pdf->uns__strtoupper("Баланс деталей мех. цеха и склада комплектующих " . fn_strtolower($period)),
    );
    $pdf->uns_set_config($params);
    $pdf->AddPage();

    $pdf->uns_SetFont("BI", 19);
    $pdf->Cell(0, 0, $pdf->uns__strtoupper('Баланс деталей мех. цеха и склада комплектующих'), 0, 1, 'C', 0, '', 0);
    $pdf->uns_SetFont("B", 16);
    $pdf->Cell(0, 0, $period, 0, 1, 'C', 0, '', 0);

    //--------------------------------------------------------------------------
    // 1. СПИСОК ЛИТЬЯ
    //--------------------------------------------------------------------------
    $col_sizes = array( 8,
                        40,
                        10,
                        0.5,
                        10,
                        10,
                        0.5,
                        10,
                        10,
                        0.5,
                        10,
                        62,
    );

//    $pdf->uns_SetFont("B", 10);
//    $pdf->Cell($col_sizes[0], 0, '№', 1, 'L', 0, '', 1);
//    $pdf->Cell($col_sizes[1], 0, 'Наименование', 1, 0, 'L', 0, '', 0);
//    $pdf->Cell($col_sizes[2], 0, 'Вес, кг', 1, 0, 'C', 0, '', 1);
//    $pdf->Cell($col_sizes[3], 0, 'Нач.ост.', 1, 0, 'C', 0, '', 1);
//    $pdf->Cell($col_sizes[4], 0, 'Приход', 1, 0, 'C', 0, '', 1);
//    $pdf->Cell($col_sizes[5], 0, 'Расход', 1, 0, 'C', 0, '', 1);
//    $pdf->Cell($col_sizes[6], 0, 'Брак', 1, 0, 'C', 0, '', 1);
//    $pdf->Cell($col_sizes[7], 0, 'Кон.ост.', 1, 1, 'C', 0, '', 1);

    $i = 0;
    $accounting = array(
        'mc1' =>array(
            'P' => array(
                'total_quantity' => 0,      // $accounting["mc1"]["P"]["total_quantity"]
                'total_weight' => 0,        // $accounting["mc1"]["P"]["total_weight"]
            ),
            'C' => array(
                'total_quantity' => 0,      // $accounting["mc1"]["C"]["total_quantity"]
                'total_weight' => 0,        // $accounting["mc1"]["C"]["total_weight"]
            ),
        ),
        'mc2' =>array(
            'P' => array(
                'total_quantity' => 0,      // $accounting["mc2"]["P"]["total_quantity"]
                'total_weight' => 0,        // $accounting["mc2"]["P"]["total_weight"]
            ),
            'C' => array(
                'total_quantity' => 0,      // $accounting["mc2"]["C"]["total_quantity"]
                'total_weight' => 0,        // $accounting["mc2"]["C"]["total_weight"]
            ),
        ),
        'sk' =>array(
            'P' => array(
                'total_quantity' => 0,      // $accounting["sk"]["P"]["total_quantity"]
                'total_weight' => 0,        // $accounting["sk"]["P"]["total_weight"]
            ),
            'C' => array(
                'total_quantity' => 0,      // $accounting["sk"]["C"]["total_quantity"]
                'total_weight' => 0,        // $accounting["sk"]["C"]["total_weight"]
            ),
        ),
    );
    $gait = $data["balance"][10];
    foreach ($gait as $group){
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

        $font_sizes = array(
            'big'    => 12,
            'normal' => 11,
            'medium' => 10,
            'small'  => 9,
            'small_1'  => 8,
        );

        // Переход на новую страницу
        if ($pdf->GetY() >= 260)  $pdf->AddPage();
        if (strlen($group['group_comment'])){
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->uns_SetFont("BI", 9);
            $pdf->MultiCell(170,  4, "({$group['group_comment']})",  0, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        }
        $k = 0;
        $pdf->SetFillColor(80, 80, 80);
        $pdf->SetTextColor(260, 260, 260);
        $pdf->uns_SetFont("B", $font_sizes["normal"]);
        $g_name = $pdf->uns__strtoupper($group['group']);
        $pdf->MultiCell($col_sizes[$k++],  $h, "№",            $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, $g_name,         $border, "L",    $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "Клм",           $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "МЦ1/О",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "МЦ1/З",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "МЦ2/О",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "МЦ2/З",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "Скл. КМП",      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//        $pdf->MultiCell(0.5,  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//        $pdf->MultiCell(10,  $h, "МЦ2/О",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//        $pdf->MultiCell(10,  $h, "МЦ2/З",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//        $pdf->MultiCell(0.5,  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "Применяемость",$border, $align, $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $k = 0;

        $m = 0;
        foreach ($group['items'] as $k_i=>$i){
            if (in_array($i['id'], $data['exclude_items'])) continue;
            $item_name = $i["no"];
            if (!strlen($i["no"]) or $i["no"] == "-") $item_name = "{$i["name"]}";

            // accessory_view
            $acc = "";
            if ($i["accessory_view"] == "P") $acc = $i["accessory_pumps"];
            if ($i["accessory_view"] == "S") $acc = $i["accessory_pump_series"];
            if ($i["accessory_view"] == "M") $acc = $i["accessory_pump_manual"];

            $pdf->SetTextColor(0, 0, 0);

            // Переход на новую страницу
            if ($pdf->GetY() >= 270)  $pdf->AddPage();

            // ЗАЛИВКА СТРОВКИ
            if (++$m % 2) $pdf->SetFillColor(255, 255, 255);
            else $pdf->SetFillColor(245, 245, 245);

            // ВЫСОТА СТРОКИ
            list($h, $maxh) = fn_report_calc_height_row(strlen(iconv("utf-8", "windows-1251", $acc)), 30);

            $k = 0;
            $pdf->uns_SetFont("R", $font_sizes['small']);
            $pdf->MultiCell($col_sizes[$k++],  $h, $m,                      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $item_name,              $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            $pdf->uns_SetFont("R", $font_sizes['small']);
            $pdf->MultiCell($col_sizes[$k++],  $h, $i['material_no'],       $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            $mc1_P  = $data["balance"][10][$group["group_id"]]["items"][$k_i]["processing_konech"];
            $mc1_C  = $data["balance"][10][$group["group_id"]]["items"][$k_i]["complete_konech"];
            $mc2_P  = $data["balance"][14][$group["group_id"]]["items"][$k_i]["processing_konech"];
            $mc2_C  = $data["balance"][14][$group["group_id"]]["items"][$k_i]["complete_konech"];
            $sk     = $data["balance"][17][$group["group_id"]]["items"][$k_i]["konech"];;

            // ВЕС детали и заготовки
            $w = fn_uns__get_accounting_item_weights("D", $i['detail_id']);
            $weight_detail = $w[$i['detail_id']]["M"][0]['value'];
            $w = fn_uns__get_accounting_item_weights("M", $i['material_id']);
            $weight_material = $w[$i['material_id']]["M"][0]['value'];
            if ($mc1_P){
                $accounting["mc1"]["P"]["total_quantity"]   += $mc1_P;
                $accounting["mc1"]["P"]["total_weight"]     += $mc1_P*$weight_material;
            }

            if ($mc1_C){
                $accounting["mc1"]["C"]["total_quantity"]   += $mc1_C;
                $accounting["mc1"]["C"]["total_weight"]     += $mc1_C*$weight_detail;
            }

            if ($mc2_P){
                $accounting["mc2"]["P"]["total_quantity"]   += $mc2_P;
                $accounting["mc2"]["P"]["total_weight"]     += $mc2_P*$weight_material;
            }

            if ($mc2_C){
                $accounting["mc2"]["C"]["total_quantity"]   += $mc2_C;
                $accounting["mc2"]["C"]["total_weight"]     += $mc2_C*$weight_detail;
            }

            if ($sk){
                $accounting["sk"]["C"]["total_quantity"]    += $sk;
                $accounting["sk"]["C"]["total_weight"]      += $sk*$weight_detail;
            }


            $mc1_P  = ($mc1_P)?$mc1_P:"";
            $mc1_C  = ($mc1_C)?$mc1_C:"";
            $mc2_P  = ($mc2_P)?$mc2_P:"";
            $mc2_C  = ($mc2_C)?$mc2_C:"";
            $sk     = ($sk)?$sk:"";

            if ($data['as_blank']){
                $mc1_P  = "";
                $mc1_C  = "";
                $mc2_P  = "";
                $mc2_C  = "";
                $sk     = "";
            }

            $pdf->uns_SetFont("B", $font_sizes['big']);
            $pdf->MultiCell($col_sizes[$k++],  $h, "",      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $mc1_P,  $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $mc1_C,  $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "",      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $mc2_P,  $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $mc2_C,  $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "",      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $sk,     $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

//            $pdf->MultiCell(0.5,  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//            $pdf->MultiCell(10,   $h, "",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//            $pdf->MultiCell(10,   $h, "",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//            $pdf->MultiCell(0.5,  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            $pdf->uns_SetFont("B", $font_sizes['small']);
            $pdf->MultiCell($col_sizes[$k++],  $h, $acc,    $border, "L", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $k = 0;
        }
        $k = 0;
        $pdf->SetFillColor(80, 80, 80);
        $pdf->SetTextColor(260, 260, 260);
        $pdf->uns_SetFont("B", $font_sizes["medium"]);
        $g_name = $pdf->uns__strtoupper($group['group']);
        $h = $maxh = 6;
        $pdf->MultiCell($col_sizes[$k++],  $h, "№",            $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, $g_name,         $border, "L",    $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "Клм",           $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "МЦ1/О",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "МЦ1/З",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "МЦ2/О",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "МЦ2/З",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "Скл. КМП",      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "Применяемость",$border, $align, $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        if (strlen($group['group_comment'])){
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->uns_SetFont("BI", 9);
            $pdf->MultiCell(170,  4, "({$group['group_comment']})",  0, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        }
        $pdf->ln(10);
    }

    $pdf->AddPage();
    // Итог:
    $pdf->SetFillColor(255);
    $pdf->SetTextColor(0);
    // МЕХ. ЦЕХ №1
    $pdf->uns_SetFont("B", 14);
    $pdf->MultiCell(170,  0, $pdf->uns__strtoupper("Остатки на Мех. цех №1:"), 0, "L", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(1);
    $pdf->uns_SetFont("B", 12);
    $pdf->MultiCell(40,  0, "", 0, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Общее кол-во, шт", 1, "C", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Общий вес, кг",    1, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->uns_SetFont("R", 12);
    $pdf->MultiCell(40,  0, "Детали в обработке", 1, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc1"]["P"]["total_quantity"]), 1, "R", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc1"]["P"]["total_weight"], 1, false), 1, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Готовые детали", 1, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc1"]["C"]["total_quantity"]), 1, "R", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc1"]["C"]["total_weight"], 1, false), 1, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(10);

    // МЕХ. ЦЕХ №2
    $pdf->uns_SetFont("B", 14);
    $pdf->MultiCell(170,  0, $pdf->uns__strtoupper("Остатки на Мех. цех №2:"), 0, "L", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(1);
    $pdf->uns_SetFont("B", 12);
    $pdf->MultiCell(40,  0, "", 0, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Общее кол-во, шт", 1, "C", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Общий вес, кг",    1, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->uns_SetFont("R", 12);
    $pdf->MultiCell(40,  0, "Детали в обработке", 1, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc2"]["P"]["total_quantity"]), 1, "R", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc2"]["P"]["total_weight"], 1, false), 1, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Готовые детали", 1, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc2"]["C"]["total_quantity"]), 1, "R", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc2"]["C"]["total_weight"], 1, false), 1, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(10);

    // СКЛАД КМП
    $pdf->uns_SetFont("B", 14);
    $pdf->MultiCell(170,  0, $pdf->uns__strtoupper("Остатки на Складе КОМПЛЕКТУЮЩИХ:"), 0, "L", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(1);
    $pdf->uns_SetFont("B", 12);
    $pdf->MultiCell(40,  0, "", 0, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Общее кол-во, шт", 1, "C", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Общий вес, кг",    1, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->uns_SetFont("R", 12);
    $pdf->MultiCell(40,  0, "Готовые детали", 1, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["sk"]["C"]["total_quantity"]), 1, "R", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["sk"]["C"]["total_weight"], 1, false), 1, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(10);

    // ИТОГО
    $pdf->uns_SetFont("B", 14);
    $pdf->MultiCell(170,  0, $pdf->uns__strtoupper("ИТОГО ДЕТАЛЕЙ ПО ПРЕДПРИЯТИЮ:"), 0, "L", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(1);
    $pdf->uns_SetFont("B", 12);
    $pdf->MultiCell(40,  0, "", 0, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Общее кол-во, шт", 1, "C", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Общий вес, кг",    1, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->uns_SetFont("R", 12);
    $pdf->MultiCell(40,  0, "Детали в обработке", 1, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc1"]["P"]["total_quantity"]+$accounting["mc2"]["P"]["total_quantity"]), 1, "R", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc1"]["P"]["total_weight"]+$accounting["mc2"]["P"]["total_weight"], 1, false), 1, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, "Готовые детали", 1, "L", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc1"]["C"]["total_quantity"]+$accounting["mc2"]["C"]["total_quantity"]+$accounting["sk"]["C"]["total_quantity"]), 1, "R", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc1"]["C"]["total_weight"]+$accounting["mc2"]["C"]["total_weight"]+$accounting["sk"]["C"]["total_weight"], 1, false), 1, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->uns_SetFont("B", 13);
    $pdf->MultiCell(40,  0, "Всего:", 0, "R", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc1"]["P"]["total_quantity"]+$accounting["mc2"]["P"]["total_quantity"]+$accounting["mc1"]["C"]["total_quantity"]+$accounting["mc2"]["C"]["total_quantity"]+$accounting["sk"]["C"]["total_quantity"]), 1, "R", $fill, 0, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->MultiCell(40,  0, fn_fvalue($accounting["mc1"]["P"]["total_weight"]+$accounting["mc2"]["P"]["total_weight"]+$accounting["mc1"]["C"]["total_weight"]+$accounting["mc2"]["C"]["total_weight"]+$accounting["sk"]["C"]["total_weight"], 1, false), 1, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(10);










    $pdf->Output(str_replace(array('fn.reports.', '.php'), '', basename(__FILE__)) . "_" . strftime("%Y-%m-%d_%H-%M", time()) . ".pdf", 'I');
    return true;
}

