<?php

require_once("module.php");

class Command extends CommandBase {
    public function Setup() { $this->SetOptions("пуш <сообщение (опционально)> - позвать всех участников (модератор)", 1, false); }

    public function Run($f) {
        $msg = "";

        for($i = 1; $i < \count($f); $i++) {
            $msg .= " " . $f[$i];
        }

        $users = $this->vk->GetDialogMembers($this->peer_id);
        if($users->error->error_code) {
            $this->vk->SendMessage(":: Произошла ошибка: " . $users->error->error_msg, $this->peer_id);
            return;
        }

        $mention = "";
        foreach($users->response->profiles as $b) {
            $mention .= "[id" . $b->id . "|ᅠ]";
        }

        $this->vk->Request("messages.send", array(
            "peer_id" => $this->peer_id,
            "random_id" => 0,
            "message" => ":: [id" . $this->user_id . "|" . $this->vk->GetFirstName($this->user_id) . "] хочет сделать объявление:" . $msg . $mention
        ));
    }
}

?>