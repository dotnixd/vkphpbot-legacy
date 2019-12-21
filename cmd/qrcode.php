<?php

require_once("module.php");

class qrcode extends CommandBase {
    public function Setup() { $this->SetOptions("кр <текст> - сгенерировать QR-код из текста", 0, true); }

    public function Run($msg) {
			$api = "http://api.foxtools.ru/v2/QR";
			$response = json_decode($this->vk->Post($api, array(
					"cp" => "UTF-8",
					"lang" => "Auto",
					"mode" => "Auto",
					"text" => $msg,
					"formatting" => 1
			)), true);

			$url = $response["response"]["value"];
			$this->u->Download($url, "pics/qr.png");
			$image = $this->vk->UploadImage("pics/qr.png", $this->peer_id);
			$this->vk->SendMessage(":: Ваш QR-код", $this->peer_id, $image);
    }
}

?>
