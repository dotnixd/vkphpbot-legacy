<?php

require_once("module.php");

class Command extends CommandBase {
    public function Setup() { $this->SetOptions("приветствие <сообщение> - установить приветствие", 1, true); }

    public function Run($msg) {
        $this->vk->SendMessage(":: Приветствие изменено", $this->peer_id);
    
        $ok = $this->db->GetConn()->prepare("UPDATE dialogs SET greeting=? WHERE peer_id=?");
        $ok->execute(array($msg, $this->peer_id)); 
    }
}

?>