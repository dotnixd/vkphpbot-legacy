<?php

require_once("module.php");

class timcook extends CommandBase {
    public function Setup() { $this->SetOptions("тимкук - пикча с презентации эппл (нужно прикрепить пикчу)", 0, false); }

    public function Run() {
        foreach($this->data["object"]["attachments"] as $a) {
            if($a["type"] == "photo") {
                $this->u->Download($a["photo"]["sizes"][\count($a["photo"]["sizes"]) - 1]["url"], "pics/vk.jpg");
                break;
            }
        }

        $tim = imagecreatefrompng("pics/tim.png");
        $meme = imagecreatefromjpeg("pics/vk.jpg");

        if(!$meme) {
            $this->vk->SendMessage(":: Нужно прикрепить пикчу", $peer_id);
            return;
        }

        $image = imagecreatetruecolor(imagesx($tim), imagesy($tim));
        imagecopyresized($image, $meme, 0, 0, 0, 0, imagesx($tim), imagesy($tim) - 25, imagesx($meme), imagesy($meme));
        imagecopyresized($image, $tim, 0, 0, 0, 0, imagesx($tim), imagesy($tim), imagesx($tim), imagesy($tim));
        imagepng($image, "pics/pic.png", 9);

        $attach = $this->vk->UploadImage("pics/pic.png", $this->peer_id);

        $this->vk->SendMessage(":: Ваша пикча", $this->peer_id, $attach);

        unlink("pics/vk.jpg");
        unlink("pics/pic.png");
    }
}

?>