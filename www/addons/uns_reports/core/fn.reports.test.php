<?php
// mb_convert_case($group['group'], MB_CASE_UPPER, "utf-8")
function fn_rpt__test($data){
    $pdf = new UNS_TCPDF();
    $params = array(
        'header_title'  => "ООО «ТОРГОВЫЙ ДОМ «УКРНАСОССЕРВИС»",
        'header_text'   => $pdf->uns__strtoupper("Комплектация насосов"),
        'FooterData_tc' => array(255,255,255),
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

    define("ROW_HEIGHT", 6);

    $w          = 5;
    $h          = ROW_HEIGHT;
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

    $col_sizes = array( 7,
                        39,
                        33,
                        9,
                        13,
                        7,
                        72,
    );



    if (is__array($ps = $data["ps"])){
        foreach ($ps as $k_ps=>$v_ps){
            if (is__array($p = $v_ps["pumps"])){
                $p_index = 0;
                $p_base = array(); // Комплектация базового насоса
                $pdf->ln(5);
                foreach ($p as $k_p=>$v_p){
                    // Переход на новую страницу
                    if ($pdf->GetY() >= 250){
                        $pdf->AddPage();
                        $pdf->ln(5);
                    }

                    if (!$p_index) $p_base = $v_p;
                    $pdf->uns_SetFont("B", 20);
                    if (!$p_index){
                        $pdf->Cell(117, 8, $v_p["p_name"], 0, 0, 'L', 0, '', 0, false, "T", "B");
                        $pdf->uns_SetFont("BI", 11);
                        $pdf->Cell(25, 8, " ОСНОВНАЯ КОМПЛЕКТАЦИЯ НАСОСА", 0, 0, 'L', 0, '', 0, false, "T", "B");
                    }else{
                        $pdf->Cell(79, 8, $v_p["p_name"], 0, 0, 'L', 0, '', 0, false, "T", "B");
                        $pdf->uns_SetFont("BI", 11);
                        $pdf->Cell(25, 8, " (полумуфты и отличия от основной комплектации)", 0, 0, 'L', 0, '', 0, false, "T", "B");
                    }
                    $pdf->ln(10);
                    $i = 1;
                    $h = ROW_HEIGHT;
                    if (is__array($details = $v_p["details"]) and count($details)>0){
                        $pdf->SetFillColor(220, 220, 220);
                        $pdf->uns_SetFont("B", 11);
                        $k = 0;
                        $pdf->MultiCell($col_sizes[$k++],  $h, "№", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                        $pdf->MultiCell($col_sizes[$k++],  $h, "Наименование", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                        $pdf->MultiCell($col_sizes[$k++],  $h, "Номер", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                        $pdf->MultiCell($col_sizes[$k++],  $h, "Кол", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                        $pdf->MultiCell($col_sizes[$k++],  $h, "КЛМ", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                        $pdf->MultiCell($col_sizes[$k++],  $h, "Тип", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                        $pdf->MultiCell($col_sizes[$k++],  $h, "Принадлежность (Примечание)", $border, "C", $fill,   1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                        $pdf->SetFillColor(255, 255, 255);

                        foreach ($details as $k_d=>$v_d){
                            if (!$p_index or ($p_index and $v_d["dcat_id"] == 38) or ($p_index and !is__array($p_base["details"][$k_d]))){
                                $k = 0;
                                $material = array_shift($v_d["accounting_data"]["materials"]);

                                $d_ind_ = $i++;
                                $d_name = $v_d["detail_name"];
                                $d_no__ = $v_d["detail_no"];
                                $d_q___ = fn_fvalue($v_d["quantity"]);

                                $d_comm = $d_acce = $d_c_ac = "";
                                if ($v_d["dcat_id"] != 38){ // Категория полумуфт
                                    $d_comm = trim($v_d["detail_comment"]);
                                    if ($v_d["accessory_view"] == "S") $d_acce = $v_d["accessory_pump_series"];
                                    if ($v_d["accessory_view"] == "P") $d_acce = $v_d["accessory_pumps"];
                                    if ($v_d["accessory_view"] == "M") $d_acce = $v_d["accessory_manual"];
                                    $d_c_ac = $d_acce . (strlen($d_comm)? " ({$d_comm})":"");
                                }

                                $m_no__ = "";
                                if (strlen($material["material_no"])){
                                    $m_no__ = "{$material["material_no"]}";
                                }

                                if ($material["mclass_id"] == 2) $m_type = "ПФ";
                                if ($material["mclass_id"] == 1) $m_type = "Л";




                                $pdf->uns_SetFont("B", 10);
                                $pdf->MultiCell($col_sizes[$k++],  $h, $d_ind_, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                                $pdf->MultiCell($col_sizes[$k++],  $h, $d_name, $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                                $pdf->uns_SetFont("R", 10);
                                $pdf->MultiCell($col_sizes[$k++],  $h, $d_no__, $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                                $pdf->uns_SetFont("B", 11);
                                $pdf->SetFillColor(220, 220, 220);
                                $pdf->MultiCell($col_sizes[$k++],  $h, $d_q___, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                                $pdf->SetFillColor(255, 255, 255);
                                $pdf->uns_SetFont("BI", 11);
                                $pdf->MultiCell($col_sizes[$k++],  $h, $m_no__, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                                $pdf->uns_SetFont("R", 10);
                                $pdf->MultiCell($col_sizes[$k++],  $h, $m_type, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                                $pdf->MultiCell($col_sizes[$k++],  $h, $d_c_ac, $border, "L", $fill,   1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                            }
                        }
                    }

                    $pdf->ln(5);
                    $pdf->uns_SetFont("B", 10);
                    $pdf->Cell(0, 0, "=============================================================================================", 0, 1, 'C', 0, '', 0);
                    $pdf->ln(3);
                    $p_index++;
                }
            }
            $pdf->uns_SetFont("I", 9);
            $pdf->Cell(0, 0, "Распечатано " . strftime("%Y/%m/%d", time()), 0, 1, 'R', 0, '', 0);
            $pdf->AddPage();
        }
    }




    $pdf->Output(str_replace(array('fn.reports.', '.php'), '', basename(__FILE__)) . "_" . strftime("%Y-%m-%d_%H-%M", time()) . ".pdf", 'I');

    return true;
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
        $pdf->MultiCell(0.5,  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell(10,  $h, "МЦ2/О",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell(10,  $h, "МЦ2/З",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell(0.5,  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($col_sizes[$k++],  $h, "Принадлежность",$border, $align, $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $k = 0;

        $m = 0;
        foreach ($group['items'] as $k_i=>$i){
            if (in_array($i['id'], $data['exclude_items'])) continue;
            $item_name = $i["no"];
            if (strlen($i["no"]) == 0) $item_name = "{$i["name"]}";

            // accessory_view
            $acc = "";
            if ($i["accessory_view"] == "P") $acc = $i["accessory_pumps"];
            if ($i["accessory_view"] == "S") $acc = $i["accessory_pump_series"];
            if ($i["accessory_view"] == "M") $acc = $i["accessory_pump_manual"];

            $pdf->SetTextColor(0, 0, 0);

            // Переход на новую страницу
            if ($pdf->GetY() >= 280)  $pdf->AddPage();

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

            $mc1_O  = $data["balance"][10][$group["group_id"]]["items"][$k_i]["processing_konech"];
            $mc1_Z  = $data["balance"][10][$group["group_id"]]["items"][$k_i]["complete_konech"];
            $mc2_O  = $data["balance"][14][$group["group_id"]]["items"][$k_i]["processing_konech"];
            $mc2_Z  = $data["balance"][14][$group["group_id"]]["items"][$k_i]["complete_konech"];
            $sk     = $data["balance"][17][$group["group_id"]]["items"][$k_i]["konech"];;

            $mc1_O  = ($mc1_O)?$mc1_O:"";
            $mc1_Z  = ($mc1_Z)?$mc1_Z:"";
            $mc2_O  = ($mc2_O)?$mc2_O:"";
            $mc2_Z  = ($mc2_Z)?$mc2_Z:"";
            $sk     = ($sk)?$sk:"";

            $pdf->uns_SetFont("B", $font_sizes['big']);
            $pdf->MultiCell($col_sizes[$k++],  $h, "",      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $mc1_O,  $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $mc1_Z,  $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "",      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $mc2_O,  $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $mc2_Z,  $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, "",      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($col_sizes[$k++],  $h, $sk,     $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            $pdf->MultiCell(0.5,  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell(10,   $h, "",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell(10,   $h, "",         $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell(0.5,  $h, "",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            $pdf->uns_SetFont("B", $font_sizes['small']);
            $pdf->MultiCell($col_sizes[$k++],  $h, $acc,    $border, $align, $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $k = 0;
        }
        $k = 0;
        $pdf->SetFillColor(80, 80, 80);
        $pdf->SetTextColor(260, 260, 260);
        $pdf->uns_SetFont("B", $font_sizes["medium"]);
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
        $pdf->MultiCell($col_sizes[$k++],  $h, "Принадлежность",$border, $align, $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        $pdf->ln(10);
    }

    $pdf->Output(str_replace(array('fn.reports.', '.php'), '', basename(__FILE__)) . "_" . strftime("%Y-%m-%d_%H-%M", time()) . ".pdf", 'I');
    return true;
}

