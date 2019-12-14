<?php

require_once("module.php");
require_once("incl/Field_calculate.php");

class calculator extends CommandBase {
    public function Setup() { $this->SetOptions("калькулятор (пример) - вычислить ответ примера", 0, true); }

    public function Run($msg) {
        if(\count($msg) > 2000) {
            $this->vk->SendMessage(":: Слишком длинный пример", $peer_id);
            return;
        }

        $calc = new Field_calculate();
        $this->vk->SendMessage(":: Результат:\n" . $calc->calculate($msg), $this->peer_id);
    }
}

?>