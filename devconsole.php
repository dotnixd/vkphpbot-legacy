<?php

class DevConsole {
    private $peer_id;
    private $user_id;
    private $db;
    private $vk;

    public function __construct($peer, $user, $base, $api) {
        $this->peer_id = $peer;
        $this->user_id = $user;
        $this->db = $base;
        $this->vk = $api;
    }

    public function Run($f) {
        switch($f[1]) {
            case "вчс":
                $this->db->GetConn()->prepare("INSERT INTO blacklist (id) VALUES (?)")->execute(array(
                    $this->vk->GetID($f[2]),
                ));
                $this->vk->SendMessage($this->vk->GetID($f[2]) . " ok added", $this->peer_id);
            break;
            case "бд":
                $test = $this->db->GetConn()->prepare("SELECT * FROM " . $f[2]);
                $test->execute();
                while($b = $test->fetch()) $this->vk->SendMessage(var_export($b, true), $this->peer_id);
            break;
            case "изчс":
                $this->db->GetConn()->prepare("DELETE FROM blacklist WHERE id=?")->execute(array(
                    $this->vk->GetID($f[2]),
                ));
                $this->vk->SendMessage($this->vk->GetID($f[2]) . " ok deleted", $this->peer_id);
		    break;
			case "инфо":
				$this->vk->SendMessage("peer_id: " . $this->peer_id . "\nuser_id: " . $this->user_id, $this->peer_id);	
        }
    }
}

?>
