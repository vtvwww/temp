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
    $pdf->ln(10);
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
        $pdf->MultiCell($w[$k++],  $h, "Чугун,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Сталь,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Алюминий,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Чугун б.,\nкг", $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

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
            if ($pdf->GetY() >= 270){
                $pdf->AddPage();
                $pdf->ln(5);
            }
            $i++;
            $weight_C[] =  ($doc['weight']['C'])?$doc['weight']['C']:0;
            $weight_S[] =  ($doc['weight']['S'])?$doc['weight']['S']:0;
            $weight_A[] =  ($doc['weight']['A'])?$doc['weight']['A']:0;
            $weight_W[] =  ($doc['weight']['W'])?$doc['weight']['W']:0;
            $k = 0;
            $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, fn_date_format($doc['date_cast'], "%a %d/%m/%Y"), $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($doc['weight']['C'])?$doc['weight']['C']:"", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($doc['weight']['S'])?$doc['weight']['S']:"", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($doc['weight']['A'])?$doc['weight']['A']:"", $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($w[$k++],  $h, ($doc['weight']['W'])?$doc['weight']['W']:"", $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        }

        // TFOOT --------------------------------
        $pdf->uns_SetFont("BI", 13);
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++],  $h, "ИТОГО:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weight_C))?"":fn_fvalue(array_sum($weight_C), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weight_S))?"":fn_fvalue(array_sum($weight_S), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weight_A))?"":fn_fvalue(array_sum($weight_A), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weight_W))?"":fn_fvalue(array_sum($weight_W), 1, false), $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

    }
    // -------------------------------------------------------------------------


    // -------------------------------------------------------------------------
    // 2. ПРОДАЖА ОТЛИВОК СО СКЛАДА ЛИТЬЯ --------------------------------------
    // -------------------------------------------------------------------------
    if ($pdf->GetY() >= 270){
        $pdf->AddPage();
        $pdf->ln(5);
    }
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
                if ($pdf->GetY() >= 270){
                    $pdf->AddPage();
                    $pdf->ln(5);
                }
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
        $pdf->uns_SetFont("BI", 13);
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++] + $w[$k++] + $w[$k++] + $w[$k++],  $h, "ИТОГО:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["C"]))?"":fn_fvalue(array_sum($weights["C"]), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["S"]))?"":fn_fvalue(array_sum($weights["S"]), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["A"]))?"":fn_fvalue(array_sum($weights["A"]), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["W"]))?"":fn_fvalue(array_sum($weights["W"]), 1, false), $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

    }


    // -------------------------------------------------------------------------
    // 3. ВЫПУСК НАСОСНОЙ ПРОДУКЦИИ --------------------------------------------
    // -------------------------------------------------------------------------
    if ($pdf->GetY() >= 270){
        $pdf->AddPage();
        $pdf->ln(5);
    }

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
    $w = array(10, 80, 15);
    //************************

    // TITLE
    $pdf->uns_SetFont("B", 18);
    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
    $pdf->SetTextColor(0); // BLACK = 0
    $pdf->ln(10);
    $pdf->MultiCell(170,  10, "3. ВЫПУСК НАСОСНОЙ ПРОДУКЦИИ", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
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
//        foreach ($data["vn_SGP"] as $doc){
//            foreach ($doc["items"] as $item){
//                if (in_array($item["item_type"], array("P", "PF", "PA")) and is__more_0($item["quantity"])){
//                    if ($pdf->GetY() >= 270){
//                        $pdf->AddPage();
//                        $pdf->ln(5);
//                    }
//                    $i++;
//                    $k = 0;
//                    $total_q += $item["quantity"];
//                    $name = $item["item_info"]["p_name"] . (($item["item_type"]=="PF")?" на раме":"");
//                    $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                    $pdf->MultiCell($w[$k++],  $h, fn_date_format($doc['date'], "%a %d/%m/%y"), $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                    $pdf->MultiCell($w[$k++],  $h, $name, $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                    $pdf->MultiCell($w[$k++],  $h, fn_fvalue($item["quantity"]), $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                }
//            }
//        }

        foreach ($data["pump_series"] as $ps_id=>$ps){
            if (is__more_0($q = $data["vn_SGP_groups"][$ps_id])){
                    if ($pdf->GetY() >= 270){
                        $pdf->AddPage();
                        $pdf->ln(5);
                    }
                    $i++;
                    $k = 0;
                    $total_q += $q;
                    $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    $pdf->MultiCell($w[$k++],  $h, $ps["ps_name"], $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    $pdf->MultiCell($w[$k++],  $h, fn_fvalue($q), $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
        }

        // TFOOT --------------------------------
        $pdf->uns_SetFont("BI", 13);
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++] /*+ $w[$k++]*/,  $h, "ИТОГО:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, $total_q, $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

    }



    // -------------------------------------------------------------------------
    // 4. ПРОДАЖА НАСОСНОЙ ПРОДУКЦИИ СО СКЛАДА ГОТОВОЙ ПРОДУКЦИИ ---------------
    // -------------------------------------------------------------------------
    if ($pdf->GetY() >= 270){
        $pdf->AddPage();
        $pdf->ln(5);
    }

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
    $w = array(10, 80, 15);
    //************************

    // TITLE
    $pdf->uns_SetFont("B", 18);
    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
    $pdf->SetTextColor(0); // BLACK = 0
    $pdf->ln(10);
    $pdf->MultiCell(170,  10, "4. РЕАЛИЗАЦИЯ НАСОСНОЙ ПРОДУКЦИИ (без учета роторов и корпусов в сборе)", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    $pdf->ln(7);

    if (!is__array($data["sales_SGP"])){
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
//        $pdf->MultiCell($w[$k++],  $h, "Клиент/Регион", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Наименование", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Кол-во,\nшт", $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        // TBODY --------------------------------
        $pdf->uns_SetFont("R", 13);
        $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        $h = $maxh = 6;

        $i = 0;
        $total_q = 0;
//        foreach ($data["sales_SGP"] as $doc){
//            foreach ($doc["items"] as $item){
//                if (in_array($item["item_type"], array("P", "PF", "PA")) and is__more_0($item["quantity"])){
//                    if ($pdf->GetY() >= 270){
//                        $pdf->AddPage();
//                        $pdf->ln(5);
//                    }
//                    $i++;
//                    $k = 0;
//                    $total_q += $item["quantity"];
//                    $name = $item["item_info"]["p_name"] . (($item["item_type"]=="PF")?" на раме":"")  . (($item["item_type"]=="PA")?" агрегат":"");
//                    $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                    $pdf->MultiCell($w[$k++],  $h, fn_date_format($doc['date'], "%a %d/%m/%y"), $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                    $pdf->MultiCell($w[$k++],  $h, $data["customers"][$doc["customer_id"]]["name"], $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                    $pdf->MultiCell($w[$k++],  $h, $name, $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                    $pdf->MultiCell($w[$k++],  $h, fn_fvalue($item["quantity"]), $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                }
//            }
//        }
        foreach ($data["pump_series"] as $ps_id=>$ps){
            if (is__more_0($q = $data["sales_SGP_groups"][$ps_id])){
                    if ($pdf->GetY() >= 270){
                        $pdf->AddPage();
                        $pdf->ln(5);
                    }
                    $i++;
                    $k = 0;
                    $total_q += $q;
                    $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    $pdf->MultiCell($w[$k++],  $h, $ps["ps_name"], $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                    $pdf->MultiCell($w[$k++],  $h, fn_fvalue($q), $border, "R", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
        }
        // TFOOT --------------------------------
        $pdf->uns_SetFont("BI", 13);
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++]/* + $w[$k++] + $w[$k++]*/,  $h, "ИТОГО:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, $total_q, $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

    }


    if ($_REQUEST["with_details"] == "Y"){
    // -------------------------------------------------------------------------
    // 4. ПРОДАЖА ДЕТАЛЕЙ К НАСОСАМ --------------------------------------------
    // -------------------------------------------------------------------------
    if ($pdf->GetY() >= 270){
        $pdf->AddPage();
        $pdf->ln(5);
    }

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
        $pdf->MultiCell($w[$k++],  $h, "Вес\n1 шт", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Кол-во,\nшт", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Общий\nвес", $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        // TBODY --------------------------------
        $pdf->uns_SetFont("R", 13);
        $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
        $pdf->SetTextColor(0); // BLACK = 0
        $h = $maxh = 6;

        $total_q = 0;
        $total_w = 0;
        foreach ($data["sales_SGP_details"] as $group=>$details){
            // Отобразить категорию
            if ($pdf->GetY() >= 270){
                $pdf->AddPage();$pdf->ln(5);
            }
            $pdf->uns_SetFont("BI", 12);
            $pdf->MultiCell(array_sum($w),  $h, "  " . $group, $border, "L", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            $pdf->uns_SetFont("R", 12);
            $i = 0;
            foreach ($details as $d){
                // Отобразить детали категории
                $i++;
                $k = 0;
                $total_q += $d["sold"];
                $total_w += $d["sold"]*$d["weight"];
                $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, $d["name"], $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($d["weight"], 1, false),               $border, "R", $fill, $ln,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($d["sold"]),                           $border, "R", $fill, $ln,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($d["sold"]*$d["weight"], 1, false),    $border, "R", $fill, 1,     $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
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
        $pdf->uns_SetFont("BI", 13);
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++] + $w[$k++],  $h, "ИТОГО:", 1, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, $total_q, $border, "R", $fill, $ln,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, fn_fvalue($total_w, 1, false), $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

    }

    }











    $pdf->Output(str_replace(array('fn.reports.', '.php'), '', basename(__FILE__)) . "_" . strftime("%Y-%m-%d_%H-%M", time()) . ".pdf", 'I');
    return true;




}
