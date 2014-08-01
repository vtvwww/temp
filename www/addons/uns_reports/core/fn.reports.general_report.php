<?php
function fn_rpt__general_report($data){
    $period = "";
    if (fn_date_format($_REQUEST["time_from"], "%d/%m/%Y") == fn_date_format($_REQUEST["time_to"], "%d/%m/%Y")){
        $period = "за " . fn_date_format($_REQUEST["time_from"], "%a %d/%m/%Y");
    }else{
        $period = "за период " . fn_date_format($_REQUEST["time_from"], "%a %d/%m/%Y") . " – " . fn_date_format($_REQUEST["time_to"], "%a %d/%m/%Y");
    }

    $pdf = new UNS_TCPDF();
    $params = array(
        'header_title'  => "ООО «ТОРГОВЫЙ ДОМ «УКРНАСОССЕРВИС»",
        'header_text'   => $pdf->uns__strtoupper("ОТЧЕТ ДЕЯТЕЛЬНОСТИ ПРЕДПРИЯТИЯ " . $period),
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

    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
    $pdf->SetTextColor(0); // BLACK = 0

    $pdf->uns_SetFont("BI", 24);
    $pdf->MultiCell(180,  8, "ОТЧЕТ ДЕЯТЕЛЬНОСТИ ПРЕДПРИЯТИЯ", 0, "C");
    $pdf->uns_SetFont("BI", 18);
    $pdf->MultiCell(180,  8, $period, 0, "C");

    // -------------------------------------------------------------------------
    // 1. ВЫПУСК ЛИТЕЙНОГО ЦЕХА ------------------------------------------------
    // -------------------------------------------------------------------------
    $h  = $maxh = 6;
    $border     = 1;
    $align      = 'C';
    $fill       = true;
    $ln         = 0;
    $x = $y     = '';
    $reseth     = true;
    $stretch    = 0;
    $ishtml     = false;
    $autopadding= false;
    $valign     = 'M';
    $fitcell    = true;
    $w = array(10, 50, 30, 30, 30, 30);
    //************************

    // TITLE
    $pdf->uns_SetFont("B", 18);
    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
    $pdf->SetTextColor(0); // BLACK = 0
    $pdf->ln(5);
    $pdf->MultiCell(170,  10, "1. ВЫПУСК ЛИТЕЙНОГО ЦЕХА", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(7);

    if (!is__array($data["report_VLC"])){
        $pdf->uns_SetFont("I", 12);
        $pdf->MultiCell(170,  10, "  Нет данных за указанный период!", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }else{
        // THEAD --------------------------------
        $pdf->uns_SetFont("B", 12);
        $pdf->SetFillColor(120); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(255); // BLACK = 0
        $h  = $maxh = 8;
        $k = 0;
        $pdf->MultiCell($w[$k++],  $h, "№", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Дата плавки", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Чугун, кг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Сталь, кг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Алюминий, кг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Чугун б., кг", $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        // TBODY --------------------------------
        $pdf->uns_SetFont("R", 13);
        $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        $h  = $maxh = 6;

        $i = 0;
        $weight_C = array();
        $weight_S = array();
        $weight_A = array();
        $weight_W = array();
        foreach ($data["report_VLC"] as $doc){
            if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}
            $i++;
            $weight_C[] =  ($doc['weight']['C'])?$doc['weight']['C']:0;
            $weight_S[] =  ($doc['weight']['S'])?$doc['weight']['S']:0;
            $weight_A[] =  ($doc['weight']['A'])?$doc['weight']['A']:0;
            $weight_W[] =  ($doc['weight']['W'])?$doc['weight']['W']:0;
            $k = 0;
            $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, fn_date_format($doc['date_cast'], "%a %d/%m/%Y"), $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($doc['weight']['C'])?number_format($doc['weight']['C'], 1, ".", " "):"", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($doc['weight']['S'])?number_format($doc['weight']['S'], 1, ".", " "):"", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($doc['weight']['A'])?number_format($doc['weight']['A'], 1, ".", " "):"", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($doc['weight']['W'])?number_format($doc['weight']['W'], 1, ".", " "):"", $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        }

        // TFOOT --------------------------------
        if (fn_date_format($_REQUEST["time_from"], "%d/%m/%Y") != fn_date_format($_REQUEST["time_to"], "%d/%m/%Y")){
            $pdf->uns_SetFont("BI", 13);
            $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255
            $pdf->SetTextColor(0); // BLACK = 0
            $k = 0;
            $pdf->MultiCell($w[$k++] + $w[$k++],  $h, "ИТОГО, кг:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, (!array_sum($weight_C))?"":number_format(array_sum($weight_C), 1, ".", " "), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, (!array_sum($weight_S))?"":number_format(array_sum($weight_S), 1, ".", " "), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, (!array_sum($weight_A))?"":number_format(array_sum($weight_A), 1, ".", " "), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, (!array_sum($weight_W))?"":number_format(array_sum($weight_W), 1, ".", " "), $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        }

        if ($_REQUEST["production_LC_from_the_beginning_of_the_month"] == "Y"){
            $pdf->uns_SetFont("BI", 13);
            $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255
            $pdf->SetTextColor(0); // BLACK = 0
            $k = 0;
            $pdf->MultiCell($w[$k++] + $w[$k++],  $h, "ИТОГО с " . fn_date_format($data['production_LC_date_from'], "%d/%m/%Y") . ", кг:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($data["total_weight_VLC"]["C"])?number_format($data["total_weight_VLC"]["C"], 1, ".", " "):"", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($data["total_weight_VLC"]["S"])?number_format($data["total_weight_VLC"]["S"], 1, ".", " "):"", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($data["total_weight_VLC"]["A"])?number_format($data["total_weight_VLC"]["A"], 1, ".", " "):"", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($data["total_weight_VLC"]["W"])?number_format($data["total_weight_VLC"]["W"], 1, ".", " "):"", $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        }
    }
    // -------------------------------------------------------------------------


    // -------------------------------------------------------------------------
    // 2. ПРОДАЖА ОТЛИВОК СО СКЛАДА ЛИТЬЯ --------------------------------------
    // -------------------------------------------------------------------------
    if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}
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
    $w = array(6, 25, 57, 14, 14, 16, 16, 16, 16);
    //************************

    // TITLE
    $pdf->uns_SetFont("B", 18);
    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
    $pdf->SetTextColor(0); // BLACK = 0
    $pdf->ln(10);
    $pdf->MultiCell(170,  10, "2. ОТПУСК ОТЛИВОК СО СКЛАДА ЛИТЬЯ", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(7);

    if (!is__array($data["sales_VLC"])){
        $pdf->uns_SetFont("I", 12);
        $pdf->MultiCell(170,  10, "  Нет данных за указанный период!", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }else{
        // THEAD --------------------------------
        $pdf->uns_SetFont("B", 12);
        $pdf->SetFillColor(120); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(255); // BLACK = 0
        $h = $maxh = 8;
        $k = 0;
        $pdf->MultiCell($w[$k++],  $h, "№", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Дата", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Наименование", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Вес,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Кол-во,\nшт", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Чугун,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Сталь,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Алюминий,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Чугун б.,\nкг", $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        // TBODY --------------------------------
        $pdf->uns_SetFont("R", 13);
        $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        $h = $maxh = 6;

        $i = 0;
        $weights = array("C" => array(), "S" =>  array(), "A" =>  array(), "W" =>  array());
        foreach ($data["sales_VLC"] as $doc){
            foreach ($doc["items"] as $item){
                if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}
                $i++;
                $k = 0;

                $name = $item["item_info"]["material_name"] . ((strlen($item["item_info"]["material_no"]))?" [".$item["item_info"]["material_no"]."]":"");
                $weight = array("C" => 0, "S" => 0, "A" => 0, "W" => 0, );
                $weight[$item["item_info"]["type_casting"]] = $item["weight"];
                $weights[$item["item_info"]["type_casting"]][] = $item["weight"]*$item["quantity"];

                $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_date_format($doc['date'], "%a %d/%m/%y"), $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, $name, $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($item["weight"], 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($item["quantity"]), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, (!$weight["C"])?"":fn_fvalue($item["quantity"]*$weight["C"], 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, (!$weight["S"])?"":fn_fvalue($item["quantity"]*$weight["S"], 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, (!$weight["A"])?"":fn_fvalue($item["quantity"]*$weight["A"], 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, (!$weight["W"])?"":fn_fvalue($item["quantity"]*$weight["W"], 1, false), $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
        }

        // TFOOT --------------------------------
        $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        $pdf->uns_SetFont("BI", 13);
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++] + $w[$k++] + $w[$k++] + $w[$k++],  $h, "ИТОГО, кг:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["C"]))?"":fn_fvalue(array_sum($weights["C"]), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["S"]))?"":fn_fvalue(array_sum($weights["S"]), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["A"]))?"":fn_fvalue(array_sum($weights["A"]), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["W"]))?"":fn_fvalue(array_sum($weights["W"]), 1, false), $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

    }


    // -------------------------------------------------------------------------
    // 3. ВЫПУСК НАСОСНОЙ ПРОДУКЦИИ --------------------------------------------
    // -------------------------------------------------------------------------
    if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}

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
    $w = array(10, 45, 15);
    //************************

    // TITLE
    $pdf->uns_SetFont("B", 18);
    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
    $pdf->SetTextColor(0); // BLACK = 0
    $pdf->ln(10);
    $pdf->MultiCell(170,  10, "3. ВЫПУСК НАСОСНОЙ ПРОДУКЦИИ (без учета роторов и корпусов в сборе)", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(7);

    if (!is__array($data["vn_SGP"])){
        $pdf->uns_SetFont("I", 12);
        $pdf->MultiCell(170,  10, "  Нет данных за указанный период!", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }else{
        // THEAD --------------------------------
        $pdf->uns_SetFont("B", 12);
        $pdf->SetFillColor(120); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(255); // BLACK = 0
        $h = $maxh = 8;
        $k = 0;
        $pdf->MultiCell($w[$k++],  $h, "№", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//        $pdf->MultiCell($w[$k++],  $h, "Дата", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Наименование", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Кол-во,\nшт", $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        // TBODY --------------------------------
        $pdf->uns_SetFont("R", 13);
        $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        $h = $maxh = 6;

        $i = 0;
        $total_q = 0;
        $total_w = 0;

        foreach ($data["pump_series"] as $ps_id=>$ps){
            if (is__more_0($q = $data["vn_SGP_groups"][$ps_id])){
                if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}                    $i++;
                $k = 0;
                $total_q += $q;
                $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, $ps["ps_name"], $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($q), $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
        }

        // TFOOT --------------------------------
        $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        $pdf->uns_SetFont("BI", 13);
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++] /*+ $w[$k++]*/,  $h, "ИТОГО, шт:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, $total_q, $border, "C", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        // Вес в т
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++] /*+ $w[$k++]*/,  $h, "ИТОГО, т:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, fn_fvalue(array_sum($data["vn_SGP_groups_weight"])/1000, 1, false), $border, "C", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        // Средний вес насоса, кг
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++] /*+ $w[$k++]*/,  $h, "Средний вес насоса, кг:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, fn_fvalue(array_sum($data["vn_SGP_groups_weight"])/array_sum($data["vn_SGP_groups"]), 0), $border, "C", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

    }



    // -------------------------------------------------------------------------
    // 4. ПРОДАЖА НАСОСНОЙ ПРОДУКЦИИ СО СКЛАДА ГОТОВОЙ ПРОДУКЦИИ ---------------
    // -------------------------------------------------------------------------
    if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}

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
    $w = array(10, 45, 15);
    //************************
    // Предварительный анализ
    // 1. Получить список клиентов по Украине и на экспорт
    $customers_of_UKR = array_keys($data["sales_SGP_groups"]["UKR"]);
    $customers_of_EXP = array_keys($data["sales_SGP_groups"]["EXP"]);
    $UKR = count($customers_of_UKR);
    $EXP = count($customers_of_EXP);

    // TITLE
    $pdf->uns_SetFont("B", 18);
    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
    $pdf->SetTextColor(0); // BLACK = 0
    $pdf->ln(5);
    $pdf->MultiCell(170,  10, "4. РЕАЛИЗАЦИЯ НАСОСНОЙ ПРОДУКЦИИ (без учета роторов и корпусов в сборе)", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(7);

    if (!is__array($data["sales_SGP_groups"])){
        $pdf->uns_SetFont("I", 12);
        $pdf->MultiCell(170,  10, "  Нет данных за указанный период!", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }else{
        // THEAD --------------------------------
        $pdf->uns_SetFont("B", 12);
        $pdf->SetFillColor(120); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(255); // BLACK = 0
        $h = $maxh = 8;
        $k = 0;

        if ($UKR > 0 and $EXP == 0){
            $pdf->MultiCell($w[$k++],  $h, "№", $border,           "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, "Наименование", $border, "C",  $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h,  "по\nУкраине", $border,          "C",   $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        }elseif ($UKR == 0 and $EXP > 0){
            $pdf->MultiCell($w[$k++],  $h,  "",     0,          "C",  false,  $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h,  "",     0,          "C",  false,  $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k]*$EXP,$h,  "ЭКСПОРТ", $border,          "C",  true,  1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $k=0;
            $pdf->MultiCell($w[$k++],  $h, "№", $border,           "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, "Наименование", $border, "C",  $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                $pdf->MultiCell($w[$k]  ,  $h,  $data["customers"][$customer_id]["name_short"], $border,          "C",   $fill, ($EXP > 1)?$ln:1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
            if ($EXP > 1){
                $pdf->MultiCell($w[$k],$h,  "Сумма", $border,          "C",  $fill,  1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }

        }elseif ($UKR > 0 and $EXP > 0){
            $pdf->MultiCell($w[$k++],  $h,  "",     0,          "C",  false,  $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h,  "",     0,          "C",  false,  $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k]  ,   $h,  "",    0,          "C",  false, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k]*$EXP,$h,  "ЭКСПОРТ", $border,          "C",  true,  1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            $k = 0;
            $pdf->MultiCell($w[$k++],  $h, "№", $border,           "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, "Наименование", $border, "C",  $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k]  ,  $h,  "по\nУкраине", $border,          "C",   $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                $pdf->MultiCell($w[$k]  ,  $h,  $data["customers"][$customer_id]["name_short"], $border,          "C",   $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
            $pdf->MultiCell($w[$k],$h,  "Сумма", $border,          "C",  $fill,  1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        }

        // TBODY --------------------------------
        $pdf->uns_SetFont("R", 13);
        $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        $h = $maxh = 6;

        $i = 1;
        $total_q = null;
        $total_w = null;
        foreach ($data["pump_series"] as $ps_id=>$ps){
            $q = null;
            if ($UKR > 0 and $EXP == 0){
                if (is__more_0($q[1] = $data["sales_SGP_groups"]["UKR"][1][$ps_id])){
                    if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(5);}
                    $k = 0;
                    $total_q[1] += $q[1];
                    $total_w[1] += $data["sales_SGP_groups_weight"]["UKR"][1][$ps_id];
                    $pdf->MultiCell($w[$k++],  $h, $i++, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    $pdf->MultiCell($w[$k++],  $h, $ps["ps_name"], $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    $pdf->MultiCell($w[$k++],  $h, fn_fvalue(array_sum($q)), $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                }
            }elseif ($UKR == 0 and $EXP > 0){
                // Добавить кол-во на экспорт
                foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                    $q[$customer_id] = $sales[$ps_id];
                }
                if (is__more_0(array_sum($q))){
                    if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}
                    $pdf->uns_SetFont("R", 13);
                    $k = 0;
                    $pdf->MultiCell($w[$k++],  $h, $i++, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    $pdf->MultiCell($w[$k++],  $h, $ps["ps_name"], $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    // Экспорт
                    foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                        $total_q[$customer_id] += $q[$customer_id];
                        $total_w[$customer_id] += $data["sales_SGP_groups_weight"]["EXP"][$customer_id][$ps_id];
                        $pdf->MultiCell($w[$k],  $h, ($q[$customer_id])?$q[$customer_id]:"", $border, "C", $fill, ($EXP > 1)?$ln:1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    }
                    if ($EXP > 1){
                        $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255
                        $pdf->SetTextColor(0); // BLACK = 0
                        // Сумма
                        $pdf->uns_SetFont("BI", 13);
                        $pdf->MultiCell($w[$k],     $h, fn_fvalue(array_sum($q)), $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                        $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
                        $pdf->SetTextColor(0); // BLACK = 0
                    }
                }
            }elseif ($UKR > 0 and $EXP > 0){
                // Добавить кол-во по Украине
                $q[1] = $data["sales_SGP_groups"]["UKR"][1][$ps_id];

                // Добавить кол-во на экспорт
                foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                    $q[$customer_id] = $sales[$ps_id];
                }
                if (is__more_0(array_sum($q))){
                    if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}
                    $pdf->uns_SetFont("R", 13);
                    $k = 0;
                    $pdf->MultiCell($w[$k++],  $h, $i++, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    $pdf->MultiCell($w[$k++],  $h, $ps["ps_name"], $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                    // Украина
                    $total_q[1] += $q[1];
                    $total_w[1] += $data["sales_SGP_groups_weight"]["UKR"][1][$ps_id];
                    $pdf->MultiCell($w[$k],  $h, ($q[1])?$q[1]:"", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                    // Экспорт
                    foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                        $total_q[$customer_id] += $q[$customer_id];
                        $total_w[$customer_id] += $data["sales_SGP_groups_weight"]["EXP"][$customer_id][$ps_id];
                        $pdf->MultiCell($w[$k],  $h, ($q[$customer_id])?$q[$customer_id]:"", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    }

                    $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255
                    $pdf->SetTextColor(0); // BLACK = 0
                    // Сумма
                    $pdf->uns_SetFont("BI", 13);
                    $pdf->MultiCell($w[$k],     $h, fn_fvalue(array_sum($q)), $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

                    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
                    $pdf->SetTextColor(0); // BLACK = 0
                }
            }
        }
        // TFOOT --------------------------------
        $pdf->uns_SetFont("B", 13);
        $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        if ($pdf->GetY() >= 270){$pdf->AddPage();$pdf->ln(5);}

        $k = 0;
        if ($UKR > 0 and $EXP == 0){
            //------------------------------------------------------------------
            // ИТОГО (в штуках) ************************************************
            $pdf->uns_SetFont("BI", 13);
            $pdf->MultiCell($w[$k++] + $w[$k++],  $h, "ИТОГО, шт:", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->uns_SetFont("B", 13);
            $pdf->MultiCell($w[$k++],  $h, ($total_q[1])?$total_q[1]:"", $border, "C", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            //------------------------------------------------------------------
            // ИТОГО (в тоннах) ************************************************
            $k = 0;
            $pdf->uns_SetFont("BI", 13);
            $pdf->MultiCell($w[$k++] + $w[$k++],  $h, "ИТОГО, т:", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->uns_SetFont("B", 13);
            $pdf->MultiCell($w[$k],  $h, ($total_w[1])?fn_fvalue($total_w[1]/1000,1,false):"", $border, "C", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            //------------------------------------------------------------------
            // Cредний вес насоса (в кг) ************************************************
            if (is__more_0($total_q[1]) and is__more_0($total_w[1])){
                $k = 0;
                $pdf->uns_SetFont("BI", 13);
                $pdf->MultiCell($w[$k++] + $w[$k++],  $h, "Cредний вес насоса, кг:", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->uns_SetFont("B", 13);
                $pdf->MultiCell($w[$k],  $h, fn_fvalue($total_w[1]/$total_q[1],0), $border, "C", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }

        }elseif ($UKR == 0 and $EXP > 0){
            //------------------------------------------------------------------
            // ИТОГО (в штуках) ************************************************
            $pdf->uns_SetFont("BI", 13);
            $pdf->MultiCell($w[$k++] + $w[$k++],  $h, "ИТОГО, шт:", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->uns_SetFont("B", 13);
            // Экспорт
            foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                $total_q[$customer_id] += $q[$customer_id];
                $pdf->MultiCell($w[$k],  $h, ($total_q[$customer_id])?$total_q[$customer_id]:"", $border, "C", $fill, ($EXP > 1)?$ln:1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }

            if ($EXP > 1){
                $pdf->uns_SetFont("BI", 13);
                $pdf->SetFillColor(180); // BLACK = 0 WHITE = 255
                $pdf->MultiCell($w[$k],  $h, (array_sum($total_q))?array_sum($total_q):"", $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255
            }


            //------------------------------------------------------------------
            // ИТОГО (в тоннах) ************************************************
            $k = 0;
            $pdf->uns_SetFont("BI", 13);
            $pdf->MultiCell($w[$k++]+$w[$k++],  $h, "ИТОГО, т:", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->uns_SetFont("B", 13);

            // Экспорт
            foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                $total_q[$customer_id] += $q[$customer_id];
                $pdf->MultiCell($w[$k],  $h, ($total_w[$customer_id])?fn_fvalue($total_w[$customer_id]/1000,1,false):"", $border, "C", $fill, ($EXP > 1)?$ln:1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
            if ($EXP > 1){
                $pdf->uns_SetFont("BI", 13);
                $pdf->SetFillColor(180); // BLACK = 0 WHITE = 255
                $pdf->MultiCell($w[$k],  $h, (array_sum($total_w))?fn_fvalue(array_sum($total_w)/1000,1,false):"", $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }

            //------------------------------------------------------------------
            // Cредний вес насоса (в кг) ************************************************
            $k = 0;
            $pdf->uns_SetFont("BI", 13);
            $pdf->MultiCell($w[$k++]+$w[$k++],  $h, "Cредний вес насоса, кг:", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->uns_SetFont("B", 13);

            // Экспорт
            foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                $total_q[$customer_id] += $q[$customer_id];
                $pdf->MultiCell($w[$k],  $h, fn_fvalue($total_w[$customer_id]/$total_q[$customer_id],0), $border, "C", $fill, ($EXP > 1)?$ln:1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
            if ($EXP > 1){
                $pdf->uns_SetFont("BI", 13);
                $pdf->SetFillColor(180); // BLACK = 0 WHITE = 255
                $pdf->MultiCell($w[$k],  $h, fn_fvalue(array_sum($total_w)/array_sum($total_q),0), $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
        }elseif ($UKR > 0 and $EXP > 0){
            //------------------------------------------------------------------
            // ИТОГО (в штуках) ************************************************
            $pdf->uns_SetFont("BI", 13);
            $pdf->MultiCell($w[$k++] + $w[$k++],  $h, "ИТОГО, шт:", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->uns_SetFont("B", 13);
            // Украина
            $pdf->MultiCell($w[$k],  $h, ($total_q[1])?$total_q[1]:"", $border, "C", $fill, $ln,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            // Экспорт
            foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                $total_q[$customer_id] += $q[$customer_id];
                $pdf->MultiCell($w[$k],  $h, ($total_q[$customer_id])?$total_q[$customer_id]:"", $border, "C", $fill, $ln,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
            $pdf->uns_SetFont("BI", 13);
            $pdf->SetFillColor(180); // BLACK = 0 WHITE = 255
            $pdf->MultiCell($w[$k],  $h, (array_sum($total_q))?array_sum($total_q):"", $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255


            //------------------------------------------------------------------
            // ИТОГО (в тоннах) ************************************************
            $k = 0;
            $pdf->uns_SetFont("BI", 13);
            $pdf->MultiCell($w[$k++]+$w[$k++],  $h, "ИТОГО, т:", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->uns_SetFont("B", 13);

            // Украина
            $pdf->MultiCell($w[$k],  $h, ($total_w[1])?fn_fvalue($total_w[1]/1000,1,false):"", $border, "C", $fill, $ln,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            // Экспорт
            foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                $total_q[$customer_id] += $q[$customer_id];
                $pdf->MultiCell($w[$k],  $h, ($total_w[$customer_id])?fn_fvalue($total_w[$customer_id]/1000,1,false):"", $border, "C", $fill, $ln,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
            $pdf->uns_SetFont("BI", 13);
            $pdf->SetFillColor(180); // BLACK = 0 WHITE = 255
            $pdf->MultiCell($w[$k],  $h, (array_sum($total_w))?fn_fvalue(array_sum($total_w)/1000,1,false):"", $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255


            //------------------------------------------------------------------
            // Cредний вес насоса (в кг) ************************************************
            $k = 0;
            $pdf->uns_SetFont("BI", 13);
            $pdf->MultiCell($w[$k++]+$w[$k++],  $h, "Cредний вес насоса, кг:", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->uns_SetFont("B", 13);

            // Украина
            $pdf->MultiCell($w[$k],  $h, fn_fvalue($total_w[1]/$total_q[1],0), $border, "C", $fill, $ln,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            // Экспорт
            foreach ($data["sales_SGP_groups"]["EXP"] as $customer_id=>$sales){
                $total_q[$customer_id] += $q[$customer_id];
                $pdf->MultiCell($w[$k],  $h, fn_fvalue($total_w[$customer_id]/$total_q[$customer_id],0), $border, "C", $fill, $ln,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
            $pdf->uns_SetFont("BI", 13);
            $pdf->SetFillColor(180); // BLACK = 0 WHITE = 255
            $pdf->MultiCell($w[$k],  $h, fn_fvalue(array_sum($total_w)/array_sum($total_q),0), $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255
        }
    }


    // -------------------------------------------------------------------------
    // 4. ПРОДАЖА ДЕТАЛЕЙ К НАСОСАМ --------------------------------------------
    // -------------------------------------------------------------------------
    if ($pdf->GetY() >= 270){$pdf->AddPage();$pdf->ln(2);}

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
//    $w = array(6, 25, 65, 70, 14);
    $w = array(10, 100, 15, 15, 20);
    //************************

    // TITLE
    $pdf->uns_SetFont("B", 18);
    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
    $pdf->SetTextColor(0); // BLACK = 0
    $pdf->ln(10);
    $pdf->MultiCell(170,  10, "5. РЕАЛИЗАЦИЯ ДЕТАЛЕЙ К НАСОСАМ", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(7);

    if (!is__array($data["sales_SGP_details"])){
        $pdf->uns_SetFont("I", 12);
        $pdf->MultiCell(170,  10, "  Нет данных за указанный период!", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }else{
        // THEAD --------------------------------
        $pdf->uns_SetFont("B", 12);
        $pdf->SetFillColor(120); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(255); // BLACK = 0
        $h = $maxh = 8;
        $k = 0;
        $pdf->MultiCell($w[$k++],  $h, "№", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Наименование", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//        $pdf->MultiCell($w[$k++],  $h, "Вес\n1 шт", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Кол-во,\nшт", $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//        $pdf->MultiCell($w[$k++],  $h, "Общий\nвес", $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        // TBODY --------------------------------
        $pdf->uns_SetFont("R", 13);
        $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        $h = $maxh = 6;

        $total_q = 0;
        $total_w = 0;
        $i = 0;
        foreach ($data["sales_SGP_details"] as $group=>$details){
            // Отобразить категорию
            if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}
            $pdf->uns_SetFont("BI", 12);
            $k = 0;
            $pdf->MultiCell($w[$k++] + $w[$k++] + $w[$k++],  $h, "  " . $group, $border, "L", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            $pdf->uns_SetFont("R", 12);
            foreach ($details as $d){
                if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}
                // Отобразить детали категории
                $i++;
                $k = 0;
                $total_q += $d["sold"];
                $total_w += $d["sold"]*$d["weight"];
                $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, $d["name"], $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($d["weight"], 1, false),               $border, "R", $fill, $ln,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($d["sold"]),                           $border, "C", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($d["sold"]*$d["weight"], 1, false),    $border, "R", $fill, 1,     $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }

//            if (is__more_0($q = $data["sales_SGP_groups"][$ps_id])){
//                    $i++;
//                    $k = 0;
//                    $total_q += $q;
//                    $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                    $pdf->MultiCell($w[$k++],  $h, $ps["ps_name"], $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                    $pdf->MultiCell($w[$k++],  $h, fn_fvalue($q), $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//            }
        }
        // TFOOT --------------------------------
        $pdf->SetFillColor(220); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        $pdf->uns_SetFont("BI", 13);
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++],  $h, "ИТОГО, шт:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, $total_q, $border, "C", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//        $pdf->MultiCell($w[$k++],  $h, fn_fvalue($total_w, 1, false), $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }











    $pdf->Output(str_replace(array('fn.reports.', '.php'), '', basename(__FILE__)) . "_" . strftime("%Y-%m-%d_%H-%M", time()) . ".pdf", 'I');
    return true;




}
