<?php

require_once("modules.php");
require_once("utils.php");
require_once("devconsole.php");

class Handlers {
    private $vk;
    private $db;
    private $u;
    private $admins;

    public function __construct($api, $sql, $ad) {
        $this->vk = $api;
        $this->db = $sql;
        $this->u = new Utils($this->db);
        $this->admins = $ad;
    }
    
    public function Run($data) {
        $text = $data["object"]["text"];
        $peer_id = $data["object"]["peer_id"];
        $user_id = $data["object"]["from_id"];
        $action = $data["object"]["action"];
        $f = \explode(" ", $text);

        $modules = GetModules::Get();

        foreach($modules as $cmd => $mod) {
            if(\substr($f[0], 1) == $cmd) {
                $file = $mod;
                break;
            }
        }
	if(isset($file)) {
            require_once("cmd/" . $file . ".php");
            $runner = new $file($this->vk, $this->db, $data, $text, $peer_id, $user_id, $action);
            $runner->Setup();
            $this->IsRoleOrDie($peer_id, $user_id, $runner->needRole);

            if($runner->needArgsAsString) {
                $args = $this->ArrayToString($f, $peer_id);
            } else {
                $args = $f;
            }
            $runner->Run($args);
        }

        if(\substr($f[0], 1) == "к") {
            $is = 0;
            foreach($this->admins as $a) {
                if($this->vk->GetID($a) == $user_id) $is = 1;
            }

            if($is == 0) {
                return;
            }
                
            $a = new DevConsole($peer_id, $user_id, $this->db, $this->vk);
            $a->Run($f);
        }


        if($action) {
            if($action["type"] == "chat_invite_user") {
                if($action["member_id"] == -189095114) {
                    $this->vk->SendMessage(":: /usr/bin/php приглашён в чат!", $peer_id);
                    $this->u->SetRole($peer_id, $user_id, 2);

					if($this->db->GetConn()->query("SELECT peer_id FROM dialogs WHERE peer_id=\"" . $peer_id . "\"")->fetch()) {
                        return;
                    }

					$this->db->GetConn()->prepare("INSERT INTO dialogs (peer_id) VALUES(?)")->execute(array($peer_id));
                    return;
                }

                $clown = $this->db->GetConn()->prepare("SELECT id FROM blacklist WHERE id=?");
                $clown->execute(array($action["member_id"]));
                if($clown->rowCount() > 0) {
                    $this->vk->SendMessage(":: " . $this->GetMention($action["member_id"]) . ", вы в ЧС бота\nПока пока)))))", $peer_id);
                    $this->vk->Request("messages.removeChatUser", array(
                        "member_id" => $action["member_id"], 
                        "user_id" => $action["member_id"], 
                        "chat_id" => $peer_id - 2000000000
                    ));

                    return;
                }

                $ok = $this->db->GetConn()->query("SELECT greeting FROM dialogs WHERE peer_id=\"" . $peer_id . "\"")->fetch();

                if(!$ok) {
                    $this->vk->SendMessage(":: " . $this->GetMention($action["member_id"]) . ", приветствуем в чате!", $peer_id);
                    return;
                }

                $this->vk->SendMessage(":: " . $this->GetMention($action["member_id"]) . "," . $ok["greeting"], $peer_id);

			} elseif ($action["type"] == "chat_kick_user") {
                if($action["member_id"] == -189095114) {
                    $this->db->GetConn()->prepare("DELETE FROM dialogs WHERE peer_id=?").execute(array($peer_id));

                    return;
                }

                $this->vk->SendMessage(":: " . $this->GetMention($action["member_id"]) . ", пока!", $peer_id);
            }
		}
    }

    public function IsRoleOrDie($peer_id, $user_id, $role) {
        if($this->u->GetRole($peer_id, $user_id) < $role) {
            $this->vk->SendMessage(":: Недостаточно прав для использования этой команды", $peer_id);
            echo "ok";
            exit();
        }
    }

    public function ArrayToString($f, $peer_id) {
        if(\count($f) == 1) {
            $this->vk->SendMessage(":: Команда требует аргументов. Пиши \"/хелп\"", $peer_id);
            echo "ok";
            exit();
        }

        $msg = "";

        for($i = 1; $i < \count($f); $i++) {
            $msg .= " " . $f[$i];
        }

        return $msg;
    }

    public function GetMention($user_id) {
        return "[id" . $user_id . "|" . $this->vk->GetFirstName($user_id) . "]";
    }
}

?>
