<?php

require_once("module.php");

class probability extends CommandBase {
    public function Setup() { $this->SetOptions("вероятность <чего-нибудь> - сгенерировать случайную вероятность", 0, true); }

    public function Run($f) {
        $this->vk->SendMessage(":: Вероятность того, что" . $f . " составляет " . strval(\rand() % 100) . "%", $this->peer_id);
    }
}

?>
