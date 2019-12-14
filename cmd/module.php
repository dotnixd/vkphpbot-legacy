<?php

class CommandBase {
    protected $vk;
    protected $db;
    protected $u;
    protected $data;
    protected $text;
    protected $peer_id;
    protected $user_id;
    protected $action;
    public $description;
    public $needRole;
    public $needArgsAsString;

    public function __construct($api, $sql, $bod, $txt, $peer, $user, $act) {
        $this->vk = $api;
        $this->db = $sql;
        $this->u = new Utils($this->db);
        $this->data = $bod;
        $this->text = $txt;
        $this->peer_id = $peer;
        $this->user_id = $user;
        $this->action = $act;
    }

    public function Setup() {}

    protected function SetOptions($descriptionn, $needRolee, $needArgsAsStringg) {
        $this->description = $descriptionn;
        $this->needRole = $needRolee;
        $this->needArgsAsString = $needArgsAsStringg;
    }

    public function GetMention($user_id) {
        return "[id" . $user_id . "|" . $this->vk->GetFirstName($user_id) . "]";
    }
}

?>