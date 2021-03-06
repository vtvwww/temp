<?php
// mb_convert_case($group['group'], MB_CASE_UPPER, "utf-8")
function fn_rpt__accounting($data){
    $pdf = new UNS_TCPDF();
    $period = fn_get_period_name($data['period'], $data['time_from'], $data['time_to']);
    $params = array(
        'header_title' =>   "ООО «ТОРГОВЫЙ ДОМ «УКРНАСОССЕРВИС»",
        'header_text' =>    $pdf->uns__strtoupper("УЧЕТ ДВИЖЕНИЯ ОТЛИВОК НА СКЛАДЕ ЛИТЬЯ " . fn_strtolower($period)),
    );
    $pdf->uns_set_config($params);
    $pdf->AddPage();

    $pdf->uns_SetFont("BI", 19);
    $pdf->Cell(0, 0, $pdf->uns__strtoupper('Учет движения отливок на складе литья'), 0, 1, 'C', 0, '', 0);
    $pdf->uns_SetFont("B", 16);
    $pdf->Cell(0, 0, $period, 0, 1, 'C', 0, '', 0);

    //--------------------------------------------------------------------------
    // 1. СПИСОК ЛИТЬЯ
    //--------------------------------------------------------------------------
    $col_sizes = array( 14,
                        62,
                        10,
                        17,
                        18,
                        18,
                        11,
                        20);

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
    $total_weights = array();
    if (is__array($data['balance'])){
        foreach ($data['balance'] as $group){
            $weights = 0;

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
            $pdf->uns_SetFont("B", 13);
            $pdf->MultiCell($col_sizes[$k++],  $h, "№ клм",    $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $pdf->uns__strtoupper($group['group']),  $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Вес,кг",    $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Нач.ост.",  $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Приход",    $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Расход",    $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Брак",      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Кон.ост.",  $border, $align, $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $k = 0;

            $m = 0;
            foreach ($group['items'] as $i){
                if ($i["show_in_report_as_name"] == "Y"){
                    $item_name = $i["name"];
                }else{
                    if ($i["accessory_view"] == "P") $item_name = $i["accessory_pumps"];
                    if ($i["accessory_view"] == "S") $item_name = $i["accessory_pump_series"];
                    if ($i["accessory_view"] == "M") $item_name = $i["accessory_pump_manual"];
                }

                if (strlen($i["material_comment_1"])){
                    $item_name = trim($i["material_comment_1"]);
                }

                $pdf->SetTextColor(0, 0, 0);

                // Переход на новую страницу
                if ($pdf->GetY() >= 280)  $pdf->AddPage();

                // ЗАЛИВКА СТРОВКИ
                if (++$m % 2) $pdf->SetFillColor(255);    // нечет 0 - черный
                else $pdf->SetFillColor(240);           // чет

                // ВЫСОТА СТРОКИ
                list($h, $maxh) = fn_report_calc_height_row(strlen(iconv("utf-8", "windows-1251", $item_name)), 16);

                $k = 0;
                $pdf->uns_SetFont("B", $font_sizes['normal']);
                $pdf->MultiCell($col_sizes[$k++],  $h, $i['no'],                $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($col_sizes[$k++],  $h, $item_name,              $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                $pdf->uns_SetFont("R", $font_sizes['small_1']);
                $pdf->MultiCell($col_sizes[$k++],  $h, fn_fvalue($i['weight']), $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                $pdf->uns_SetFont("B", $font_sizes['big']);

                if ($i['nach'] == 0){$pdf->SetTextColor(255, 255, 255);}
                $pdf->MultiCell($col_sizes[$k++],  $h, $i['nach'],              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                if ($i['nach'] == 0){$pdf->SetTextColor();}
                $pdf->MultiCell($col_sizes[$k++],  $h, "",                      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($col_sizes[$k++],  $h, "",                      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($col_sizes[$k++],  $h, "",                      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($col_sizes[$k++],  $h, "",                      $border, $align, $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $k = 0;
                if ($i['nach']>0){
                    $weights += $i['nach']*$i['weight'];
                }
            }
            // -------------------------------------------------------------
            if ($pdf->GetY() >= 260)  $pdf->AddPage();
            $k = 0;
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
            $pdf->SetFillColor(80, 80, 80);
            $pdf->SetTextColor(260, 260, 260);
            $pdf->uns_SetFont("B", 13);
            $pdf->MultiCell($col_sizes[$k++],  $h, "№ клм",                                 $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $pdf->uns__strtoupper($group['group']),   $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, fn_fvalue($weights),                                 $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Нач.ост.",                               $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Приход",                                 $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Расход",                                 $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Брак",                                   $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "Кон.ост.",                               $border, $align, $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            // -------------------------------------------------------------
            if (strlen($group['group_comment'])){
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->uns_SetFont("BI", 9);
                $pdf->MultiCell(170,  4, "({$group['group_comment']})",  0, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }

            $pdf->ln(6);

            $total_weights[$group['group']] = $weights;
        }
    }

    // Пустые строки
    for ($i=0;$i<5;$i++){
        if ($pdf->GetY() >= 260)  $pdf->AddPage();
        $pdf->Cell($col_sizes[0], 0, '', 1, 0, 'C', 0, '', 1);
        $pdf->Cell($col_sizes[1], 0, '', 1, 0, 'L', 0, '', 1);
        $pdf->Cell($col_sizes[2], 0, '', 1, 0, 'C', 0, '', 1);
        $pdf->Cell($col_sizes[3], 0, '', 1, 0, 'C', 0, '', 1);
        $pdf->Cell($col_sizes[4], 0, '', 1, 0, 'C', 0, '', 1);
        $pdf->Cell($col_sizes[5], 0, '', 1, 0, 'C', 0, '', 1);
        $pdf->Cell($col_sizes[6], 0, '', 1, 0, 'C', 0, '', 1);
        $pdf->Cell($col_sizes[7], 0, '', 1, 1, 'C', 0, '', 1);
    }

    $pdf->ln(10);

    // Сводка веса
    $max_weight = array_sum($total_weights);
    $perc_width = 20;


    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255);
    $pdf->uns_SetFont("B", 11);

    $pdf->Cell(100, 0, "Группа заготовок", 1, 0, 'C', 0, '', 1);
    $pdf->Cell(35, 0, "Общий вес, кг", 1, 0, 'C', 0, '', 1);
    $pdf->Cell($perc_width, 0, "%", 1, 1, 'C', 0, '', 1);

    foreach ($total_weights as $k=>$v){
        if ($pdf->GetY() >= 275)  $pdf->AddPage();
        $pdf->uns_SetFont("R", 12);
        $pdf->Cell(100, 6, $k, 1, 0, 'L', 0, '', 1);
        $pdf->Cell(35, 6, fn_fvalue($v, 1, false), 1, 0, 'R', 0, '', 1);

        $pdf->uns_SetFont("B", 8);
        $perc = ($max_weight!=0)?fn_fvalue(100*$v/$max_weight, 1, false):0;
        if ($perc == 100){
            $pdf->SetFillColor(160);
            $pdf->Cell($perc_width*$perc/100,           6, $perc."%",   1, 1, 'L', 1, '', 0);
        }elseif ($perc == 0){
            $pdf->SetFillColor(255);
            $pdf->Cell($perc_width,                     6, $perc."%",   1, 1, 'R', 1, '', 0);
        }else{
            $pdf->SetFillColor(160);
            $pdf->Cell($perc_width*$perc/100,           6, "",          1, 0, 'L', 1, '', 0);
            $pdf->SetFillColor(255);
            $pdf->Cell($perc_width*(100-$perc)/100,     6, $perc."%",   1, 1, 'R', 0, '', 0);
        }
    }
    $pdf->uns_SetFont("B", 13);
    $pdf->Cell(100, 0, "ИТОГО", 1, 0, 'R', 0, '', 1);
    $pdf->Cell(35, 0, fn_fvalue(array_sum($total_weights), 1, false), 1, 1, 'R', 0, '', 1);
    $pdf->ln(10);

    $pdf->Output(str_replace(array('fn.reports.', '.php'), '', basename(__FILE__)) . "_" . strftime("%Y-%m-%d_%H-%M", time()) . ".pdf", 'I');
    return true;
}


// ПОЛУЧИТЬ ОБОЗНАЧЕНИЕ
function fn_report_get_name ($item, $rules){
    $str = trim($item['accessory_pump_series']);

    foreach (array('mcat_id', 'material_id') as $t){
        if (isset($rules[$t][$item[$t]])){
            switch ($rules[$t][$item[$t]]){
                case "N":
                    $str = trim($item['name']);
                    break;
                case "NA":
                    $str = trim($item['name_accounting']);
                    break;
                case "P":
                    $str = trim($item['accessory_pumps']);
                    break;
                case "PS":
                    $str = trim($item['accessory_pump_series']);
                    break;
            }
        }
    }
    return $str;
}


// РАСЧЕТ ВЫСОТЫ СТРОКИ ДЛЯ ЕЕ ЛУЧШЕГО ЗАПОЛНЕНИЯ
function fn_report_calc_height_row ($length, $msize = 25){
    $h          = 6;
    if (($length >= (1*0)) and ($length <= (2*$msize))){
        $h          = 7;
    }elseif (($length > (2*$msize)) and ($length <= (3*$msize))){
        $h          = 12;
    }elseif (($length > (3*$msize)) and ($length <= (4*$msize))){
        $h          = 15;
    }elseif (($length > (4*$msize)) and ($length <= (5*$msize))){
        $h          = 18;
    }elseif (($length > (5*$msize)) and ($length <= (6*$msize))){
        $h          = 21;
    }else{
        $h          = 24;
    }
    $maxh       = $h;
    return array($h, $maxh);
}
