<?php

require_once("module.php");

class Command extends CommandBase {
    public function Setup() { $this->SetOptions("кончил - кончить на пикчу (нужно приложить пикчу)", 0, false); }

    public function Run() {
        foreach($this->data["object"]["attachments"] as $a) {
            if($a["type"] == "photo") {
                $this->u->Download($a["photo"]["sizes"][\count($a["photo"]["sizes"]) - 1]["url"], "pics/vk.jpg");
            }
        }

        $cum = imagecreatefrompng("pics/cum.png");
        $meme = imagecreatefromjpeg("pics/vk.jpg");

        if(!$meme) {
            $this->vk->SendMessage(":: Нужно прикрепить пикчу", $peer_id);
            return;
        }

        $image = imagecreatetruecolor(imagesx($cum), imagesy($cum));
        imagecopyresized($image, $meme, 0, 0, 0, 0, imagesx($cum), imagesy($cum), imagesx($meme), imagesy($meme));
        imagecopyresized($image, $cum, 0, 0, 0, 0, imagesx($cum), imagesy($cum), imagesx($cum), imagesy($cum));
        imagepng($image, "pics/pic.png", 9);

        $attach = $this->vk->UploadImage("pics/pic.png", $this->peer_id);

        $this->vk->SendMessage(":: Ваша пикча", $this->peer_id, $attach);
        unlink("pics/vk.jpg");
        unlink("pics/pic.png");
    }
}

?>