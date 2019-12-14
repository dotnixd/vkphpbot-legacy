<?php

require_once("module.php");

class Command extends CommandBase {
    public function Setup() { $this->SetOptions("перевод <сообщение> - перевести русский текст на английский язык, если язык сообщения другой, то сообщение будет переведено на русский язык", 0, true); }

    public function Run($msg) {
        $lang = json_decode( $this->vk->Post("https://translate.yandex.net/api/v1.5/tr.json/detect", array(
            "key" => "trnsl.1.1.20191031T124411Z.cd9c82c2cc463cb9.1b5043a3a12489d25f21ccca1441a6f263b6322f",
            "text" => $msg
        )) );

        if($lang->lang == "ru") {
            $l = "en";
        } else {
            $l = "ru";
        }

        $trans = json_decode( $this->vk->Post("https://translate.yandex.net/api/v1.5/tr.json/translate", array(
            "key" => "trnsl.1.1.20191031T124411Z.cd9c82c2cc463cb9.1b5043a3a12489d25f21ccca1441a6f263b6322f",
            "text" => $msg,
            "lang" => $l
        )) );

        $this->vk->SendMessage(":: Перевод\n" . $trans->text[0], $this->peer_id);
    }
}

?>