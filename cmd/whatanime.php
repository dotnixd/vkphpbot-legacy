<?php

require_once("module.php");

class Command extends CommandBase {
    public function Setup() { $this->SetOptions("чтозааниме - найти аниме по пикче (нужно прикрепить пикчу)", 0, false); }

    public function Run() {
        $url = $this->data["object"]["attachments"][0]["photo"]["sizes"][\count($this->data["object"]["attachments"][0]["photo"]["sizes"]) - 1]["url"];

		if(!$url) {
		    $this->vk->SendMessage(":: Нужно прикрепить пикчу", $this->peer_id);
			return;
		}

		$request_params = array(
		    "url" => $url
		);

		$anime = $this->vk->Post("https://trace.moe/api/search", $request_params);

		$get_params = http_build_query($request_params);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://trace.moe/api/search?". $get_params);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$anime = json_decode(curl_exec($curl), true);
        curl_close($curl);
        $name = $anime["docs"][0]["title_english"];
        $episode = $anime["docs"][0]["episode"];
        $chance = round($anime["docs"][0]["similarity"] * 100);
        $time = gmdate("H:i:s", $anime["docs"][0]["from"]);

        $this->vk->SendMessage(":: " . $name . "\nСезон: ". $episode . "\nТочность: " . $chance . "%\nВремя: " . $time, $this->peer_id);
    }
}

?>