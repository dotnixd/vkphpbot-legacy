<?php

require_once("module.php");

class Command extends CommandBase {
    public function Setup() { $this->SetOptions("вероятность <чего-нибудь> - сгенерировать случайную вероятность", 0, true); }

    public function Run($f) {
        $this->vk->SendMessage(":: Вероятность того, что" . $msg . " составляет " . strval(\rand() % 100) . "%", $this->peer_id);
    }
}

?>