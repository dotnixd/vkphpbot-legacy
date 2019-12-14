<?php

require_once("module.php");
require_once("incl/simple_html_dom.php");

class bash extends CommandBase {
    public function Setup() { $this->SetOptions("баш - цитата с bash.im", 0, false); }

    public function Run() {
        $html = file_get_html("https://bash.im/random");
		$div = $html->find("div[class=quote__body]")[0];
		$div = str_replace("<div class=\"quote__body\">", "", $div);
		$div = str_replace("</div>", "", $div);
		$this->vk->SendMessage(":: Ваша цитата:\n" . $div, $this->peer_id);
    }
}

?>