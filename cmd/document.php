<?php

require_once("module.php");

class Command extends CommandBase {
    public function Setup() { $this->SetOptions("документ <название> - поиск документов ВК", 0, true); }

    public function Run($msg) {
        $video = json_decode( $this->vk->Request("docs.search", array("q" => $msg, "count" => 1)) );

        if($video->response->count == 0) {
            $this->vk->SendMessage(":: Документ не найден", $this->peer_id);
            return;
        }

        $attach = "doc" . $video->response->items[0]->owner_id . "_" . $video->response->items[0]->id;

        $this->vk->SendMessage(":: Ваш документ", $this->peer_id, $attach);
    }
}

?>