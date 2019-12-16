<?php

require_once("module.php");

class kick extends CommandBase {
    public function Setup() { $this->SetOptions("кик - кикнуть пользователя из беседы", 1, false); }

    public function Run($f) {
			if(\count($f) < 2) {
					$this->vk->SendMessage(":: Команда требует аргументов. Пиши \"!хелп\"", $this->peer_id);
					return;
			}

			$dog = $this->vk->GetID($f[1]);
			if(!$dog) {
				$this->vk->SendMessage(":: Такого пользователя нет", $this->peer_id);
				return;
		    }

			$resp = json_decode($this->vk->Request("messages.removeChatUser", array(
					"chat_id" => $this->peer_id - 2000000000,
					"user_id" => $dog,
					"member_id" => $dog
			)));

			if($resp->error->error_code) {
					$this->vk->SendMessage(":: Произошла ошибка: " . $resp->error->error_msg, $this->peer_id);
					return;
			}

			$this->vk->SendMessage(":: Успешно", $this->peer_id);
    }
}

?>
