<?php

require_once("module.php");

class who extends CommandBase {
    public function Setup() { $this->SetOptions("кто <что-нибудь> - выберет случайного участика беседы", 0, true); }

    public function Run($who) {
        $users = $this->vk->GetDialogMembers($this->peer_id);
        if($users->error->error_code) {
            $this->vk->SendMessage(":: Произошла ошибка: " . $users->error->error_msg, $peer_id);
            return;
        }

        $k = \rand() % \count($users->response->profiles);
        $name = $users->response->profiles[$k]->first_name . " " . $users->response->profiles[$k]->last_name;

        $this->vk->SendMessage(":: Кто" . $who . "? Возможно это [id" . $users->response->profiles[$k]->id . "|" . $name . "]", $this->peer_id);

    }
}

?>