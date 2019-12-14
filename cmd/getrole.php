<?php

require_once("module.php");

class getrole extends CommandBase {
    public function Setup() { $this->SetOptions("роль - получить роль", 0, false); }

    public function Run() {
        $role = $this->u->GetRole($this->peer_id, $this->user_id);
        $role_s = "";

        switch($role) {
            case 0:
                $role_s = "участник беседы";
            break;
            case 1:
                $role_s = "модератор";
            break;
            case 2:
                $role_s = "администратор";
            break;
            default:
                $role_s = "хз кто";
        }

        $this->vk->SendMessage(":: " . $this->GetMention($this->user_id) . ", ваша роль: ". $role_s, $this->peer_id);
    }
}

?>