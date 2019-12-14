<?php

require_once("module.php");
require_once("incl/smeh.php");

class laugh extends CommandBase {
    public function Setup() { $this->SetOptions("смех - генератор смеха (для справки напишите \"!смех -h\")", 0, false); }

    public function Run($f) {
        $smeh = new Smeh($f);
        $smeh->Parse();
        if($smeh->GetCount() > 2000) {
            $this->vk->SendMessage(":: Слишком большое число -c", $this->peer_id);
            return;
        } 

        $this->vk->SendMessage($smeh->Generate(), $this->peer_id);
    }
}

?>