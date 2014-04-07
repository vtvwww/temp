<?php

/*
include ("ini_file_class.php");
$ini = new ini_class(array("file_name"=>"45.ini"));
echo "<pre>" . print_r($ini->get() ,1) . "</pre>";

$ini->set("my_sec0", "my_var0", "my_value0");
echo "<pre>" . print_r($ini->get() ,1) . "</pre>";

$ini->set("my_sec0", array("s"=>0, "cs"=>1));
echo "<pre>" . print_r($ini->get() ,1) . "</pre>";

$ini->set(array("s"=>array("v1"=>1, "v2"=>2), "cs"=>array("v3"=>111)));
echo "<pre>" . print_r($ini->get() ,1) . "</pre>";
*/


class inifile
{
    public    $error            = false;
    protected $data             = null;
    protected $file_name        = null;
    protected $immediate_save   = true;

    public function __construct($params){
        if ($params["immediate_save"] === false){
            $this->immediate_save = false;
        }

        $this->file_name = $params["file_name"];

        if (file_exists($params["file_name"])){
            $this->read();
        }else{
            $this->error = "Файл не существует!";
        }
    }

    /**
     * Чтение ini-файла
     */
    protected function read (){
        $this->data = parse_ini_file($this->file_name, true);
    }


    /**
     * Установить новое значение
     * <pre>
     * $section = array("section_name_0" => array("var_0" => 0,
     *                                            "var_1" => 1,
     *                                            "var_2" => 2,),
     *                  "section_name_1" => array("var_0" => 0,
     *                                            "var_1" => 1,
     *                                            "var_2" => 2,),
     *                  "section_name_2" => array("var_0" => 0,
     *                                            "var_1" => 1,
     *                                            "var_2" => 2,),
     * );
     * </pre>
     * @param array|string $section
     * @param array|string|null $var
     * @param string $value
     */
    public function set ($section, $var=null, $value=""){
        if (is_string($section) and is_string($var)){
            $this->data[$section][$var] = $value;
            if ($this->immediate_save) $this->save();
        }elseif (is_string($section) and is_array($var)){
            $this->data[$section] = $var;
            if ($this->immediate_save) $this->save();
        }elseif (is_array($section)){
            $this->data = $section;
            if ($this->immediate_save) $this->save();
        }
    }

    /**
     * Получить SECTION and/or VAR
     * @param null $section
     * @param null $var
     * @return bool|null
     */
    public function get ($section=null, $var=null){
        if (is_null($section) and is_null($var)) return $this->data;
        elseif (!is_null($section) and is_null($var)) return $this->data[$section];
        elseif (!is_null($section) and !is_null($var)) return $this->data[$section][$var];
        else return false;
    }


    /**
     * Удалить SECTION and/or VAR
     * @param null $section
     * @param null $var
     */
    public function del ($section=null, $var=null){
        if (is_null($section) and is_null($var)){
            $this->data = null;
            if ($this->immediate_save) $this->save();
        }elseif (!is_null($section) and is_null($var)){
            unset($this->data[$section]);
            if ($this->immediate_save) $this->save();
        }elseif (!is_null($section) and !is_null($var)){
            unset($this->data[$section][$var]);
            if ($this->immediate_save) $this->save();
        }
    }

    /**
     * Запись в файл
     * @param null $new_file_name
     */
    private function save ($new_file_name=null){
        if (is_null($new_file_name)) $new_file_name = $this->file_name;
        // Сохранение в файл
        $content = null;
        foreach ($this->data as $section => $data) {
            $content .= '[' . $section . ']' . PHP_EOL;
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $v) {
                        $content .= $key . '[] = ' . (is_numeric($v) ? $v : '"' . $v . '"') . PHP_EOL;
                    }
                } elseif (empty($val)) {
                    $content .= $key . ' = ' . PHP_EOL;
                } else {
                    $content .= $key . ' = ' . (is_numeric($val) ? $val : '"' . $val . '"') . PHP_EOL;
                }
            }
            $content .= PHP_EOL;
        }
        return (($handle = fopen($new_file_name, 'w')) && fwrite($handle, trim($content)) && fclose($handle)) ? TRUE : FALSE;
    }
}