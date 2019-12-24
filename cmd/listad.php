<?php

require_once("module.php");

class listad extends CommandBase {
    public function Setup() { $this->SetOptions("админсостав - посмотреть администраторов и модераторов беседы", 0, false); }

	public function Run() {
	    $role_assign = $this->db->GetConn()->prepare("SELECT * FROM role_assign WHERE peer_id=?");
	    $role_assign->execute(array($this->peer_id));
	    $admins = "";
	    $moders = "";
	    while($b = $role_assign->fetch()) {
		if($b["role"] == 2)
		    $admins .= "- " . $this->GetMention($b["user_id"]) . "\n";
		elseif($b["role"] == 1)
		    $moders .= "- " . $this->GetMention($b["user_id"]) . "\n";
	    }


	    $msg = ":: Администраторы:\n" . $admins . "\n:: Модераторы:\n" . $moders;
	    $this->vk->SendMessage($msg, $this->peer_id);
    }
}

?>
