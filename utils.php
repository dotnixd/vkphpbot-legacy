<?php
class Utils {
    private $db;

    public function __construct($sql) {
        $this->db = $sql;
    }

    public function GetRole($peer_id, $user_id) {
        $query = $this->db->GetConn()->prepare("SELECT role FROM role_assign WHERE user_id=? AND peer_id=?");
        $query->execute(array($user_id, $peer_id));

        if($query->rowCount() == 0) {
            return 0;
        }

        return $query->fetch()["role"];
    }

    public function Download($url, $filename) {
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw = curl_exec($ch);
        curl_close ($ch);
    
        if(file_exists($filename)){
            unlink($filename);
        }
    
        $fp = fopen($filename, 'x');
        fwrite($fp, $raw);
        fclose($fp);
    }
}
?>