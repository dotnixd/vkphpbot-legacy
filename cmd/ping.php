<?php

require_once("module.php");

class ping extends CommandBase {
    public function Setup() { $this->SetOptions("пинг - проверить работоспособность бота", 0, false); }

    public function Run() {
        $this->vk->SendMessage(":: Понг\nБот работает", $this->peer_id);
    }
}

?>