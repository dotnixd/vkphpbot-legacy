<?php

require_once("module.php");

class when extends CommandBase {
    public function Setup() { $this->SetOptions("когда <текст> - предугадать когда произойдет что-то", 0, true); }

    public function Run($msg) {
        $when = "Через " . rand() % 1000 . " ";
        $w = rand() % 5;
        switch($w) {
        case 0:
            $when .= "лет";
        break;
        case 1:
            $when .= "дней";
        break;
        case 2:
            $when .= "часов";
        break;
        case 3:
            $when .= "минут";
        break;
        default:
            $when .= "секунд";
        }

        $this->vk->SendMessage(":: " . $when . $msg, $this->peer_id);
    }
}

?>