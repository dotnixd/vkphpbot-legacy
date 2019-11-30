<?php
class VKApi {
    private $token;
    private $vk;

    public function __construct($key) {
        $this->token = $key;
        $this->vk = "https://api.vk.com/method/";
    }

    public function Post($url, $params) {
        $get_params = http_build_query($params);

        $req = curl_init();
        curl_setopt_array($req, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $get_params
        ));

        $response = curl_exec($req);
        curl_close($req);

        return $response;
    }

    public function Upload($url, $as, $filename) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array($as => new CURLfile($filename)));
        $json = curl_exec($curl);
        curl_close($curl);
        return json_decode($json, true);
    }

    public function Request($method, $params) {
        $params["access_token"] = $this->token;
        $params["v"] = "5.92";

        return $this->Post($this->vk . $method, $params);
    } 

    public function SendMessage($msg, $peer_id, $attach = "") {
        $request_params = array (
            "message" => $msg,
            "peer_id" => $peer_id,
            "random_id" => 0,
            "disable_mentions" => "true"
        );

        if($attach != "") {
            $request_params["attachment"] = $attach;
        }

        $this->Request("messages.send", $request_params);
    }

    public function GetDialogMembers($peer_id) {
        $request_params = array (
            "peer_id" => $peer_id
        );

        return json_decode($this->Request("messages.getConversationMembers", $request_params));
    }

    public function GetFirstName($user_id) {
        $obj = json_decode( $this->Request("users.get", array("user_ids" => $user_id)) );

        return $obj->response[0]->first_name;
    }

    public function UploadImage($filename, $peer_id) {
        $ok = json_decode($this->Request("photos.getMessagesUploadServer", array(
            "peer_id" => $peer_id
        )));

        $ponvk = $this->Upload($ok->response->upload_url, "file", $filename);
        $pic = json_decode($this->Request("photos.saveMessagesPhoto", array(
            "server" => $ponvk["server"],
            "photo" => $ponvk["photo"],
            "hash" => $ponvk["hash"]
        )));

        return "photo" . $pic->response[0]->owner_id . "_" . $pic->response[0]->id;
    }
}
?>