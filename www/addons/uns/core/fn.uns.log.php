<?php
//$l->p(__FILE__, __LINE__);

class uns_log {
    private $time = 0;
    private $delta = 0;
    private $show = true;
    private $mark = "";
    public function __construct($show=true, $mark=""){
        $this->show = $show;
        if (strlen($mark)) $this->mark = $mark;
        else $this->mark = substr(str_shuffle(str_repeat("0123456789", 5)), 0, 3);
        $this->upd_time();
    }

    private function get_mctime(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    private function upd_time(){
        $this->time = $this->get_mctime();
        $this->delta = $this->get_mctime();
    }

    private function g_interval(){
        $t = $this->get_mctime()-$this->time;
        $this->upd_time();
        return $t;
    }


    public function p ($file, $line, $label=""){
        $t = $this->get_mctime();
        $time = number_format($t-$this->time, 6, ".", " ");
        $d    = number_format($t-$this->delta, 6, ".", " ");
        $this->delta = $t;
        if ($this->show){
            print( $this->mark . " " . (strlen($label)?$label:"") . " :: " . basename($file).  "; Line: " . $line . "; time: " . $time . " (+" . $d . ")<br>");
        }
    }
}
