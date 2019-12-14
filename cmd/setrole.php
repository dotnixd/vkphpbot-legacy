<?php

require_once("module.php");

class Command extends CommandBase {
    public function Setup() { $this->SetOptions("сетроль <пользователь> <роль> - изменить роль пользователю (а/админ/админстратор, м/модер/модератор, что угодно - обычный участник)", 2, false); }

    public function Run($f) {
        if(\count($f) < 3) {
            $this->vk->SendMessage(":: Команда требует аргументов. Пиши \"!хелп\"", $this->peer_id);
            return;
        }

        $dog = $this->vk->GetID($f[1]);
        if($dog == FALSE) {
            $this->vk->SendMessage(":: Такого пользователя нет", $this->peer_id);
            return;
        }

        $this->vk->SendMessage(":: Права выданы", $this->peer_id);
        $r = 0;

        switch($f[2]) {
            case "админ":
            case "администратор":
            case "а":
                $r = 2;
            break;
            case "модер":
            case "модератор":
            case "м":
                $r = 1;
            break;
        }

        $this->u->SetRole($this->peer_id, $dog, $r);
    }
}

?>