<?php

require_once("module.php");

class choose extends CommandBase {
    public function Setup() { $this->SetOptions("выбери <что-то> или <что-то> - выбрать случайное значение", 0, false); }

    public function Run($f) {
        $str = "";
        $chooses = array();

        if(\count($f) == 1) {
            $this->vk->SendMessage(":: Команда требует аргументов. Пиши \"!хелп\"", $this->peer_id);
            return;
        }

        for($i = 1; $i < \count($f); $i++) {
            if($f[$i] == "или") {
                array_push($chooses, $str);
                $str = "";
            } else {
                $str .=  " " . $f[$i];
            }
        }

        $k = \rand() % \count($chooses);
        $this->vk->SendMessage(":: Выбрано:" . $chooses[$k], $this->peer_id);
    }
}

?>