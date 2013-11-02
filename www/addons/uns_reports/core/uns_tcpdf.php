<?php

require_once(DIR_ADDONS . 'uns_reports' . "/lib/tcpdf/tcpdf.php");

/**
 * Класс для export данных в pdf
 * Class UNS_TCPDF
 */
class UNS_TCPDF extends TCPDF{

    /**
     * Стандартный шрифт 'consola'
     * @var string
     */
    public $font_name = 'consola';

    public function uns_set_config($params){
        $default_params = array(
            'header_title'  => '',
            'header_text'   => '',

        );
        $params = array_merge($default_params, $params);

        // set default header data
        $this->SetHeaderData("","",$params['header_title'],$params['header_text'],array(0, 0, 0),array(0, 0, 0));
        $this->setFooterData(array(0, 0, 0), array(0, 0, 0));

        // set header and footer fonts
        $this->setHeaderFont(Array($this->font_name, '', 10));
        $this->setFooterFont(Array($this->font_name, '', 9));
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(10);

        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $this->SetMargins(20, 16, 20);

        // set auto page breaks
        $this->SetAutoPageBreak(true, 10);

        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);


        // set default font subsetting mode
        $this->setFontSubsetting(true);
        $this->uns_SetFont('R',12);
    }

    /**
     * Установка типа шрифта
     * @param $family
     */
    public  function uns_SetFamily($family) {
        $this->font_name = $family;
    }


    /**
     * Упрощенная установка шрифта
     * @param $type
     * @param $size
     */
    public  function uns_SetFont($type='R', $size) {
        switch ($type){
            case "R":   $this->SetFont($this->font_name,        '', $size, '', true);break;
            case "RU":  $this->SetFont($this->font_name,        'U', $size, '', true);break;
            case "RD":  $this->SetFont($this->font_name,        'D', $size, '', true);break;
            case "RO":  $this->SetFont($this->font_name,        'O', $size, '', true);break;

            case "I":   $this->SetFont($this->font_name,        'I', $size, '', true);break;
            case "IU":  $this->SetFont($this->font_name.'i',    'U', $size, '', true);break;
            case "ID":  $this->SetFont($this->font_name.'i',    'D', $size, '', true);break;
            case "IO":  $this->SetFont($this->font_name.'i',    'O', $size, '', true);break;

            case "B":   $this->SetFont($this->font_name,        'B', $size, '', true);break;
            case "BU":  $this->SetFont($this->font_name.'b',    'U', $size, '', true);break;
            case "BD":  $this->SetFont($this->font_name.'b',    'D', $size, '', true);break;
            case "BO":  $this->SetFont($this->font_name.'b',    'O', $size, '', true);break;
            case "BI":  $this->SetFont($this->font_name.'z',    'I', $size, '', true);break;
            case "BIU": $this->SetFont($this->font_name.'z',    'U', $size, '', true);break;
            case "BID": $this->SetFont($this->font_name.'z',    'D', $size, '', true);break;
            case "BIO": $this->SetFont($this->font_name.'z',    'O', $size, '', true);break;
            default:$this->SetFont($this->font_name, '', $size, '', true);break;
        }
    }

    public function uns__strtoupper ($str){
        return mb_convert_case($str, MB_CASE_UPPER, "utf-8");
    }

    public function uns__strtolower ($str){
        return mb_convert_case($str, MB_CASE_LOWER, "utf-8");
    }
}