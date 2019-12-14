<?php

require_once("module.php");

class weather extends CommandBase {
    public function Setup() { $this->SetOptions("погода <город> - получить текущую погоду", 0, true); }

    public function Run($msg) {
        $trans = json_decode( $this->vk->Post("https://translate.yandex.net/api/v1.5/tr.json/translate", array(
            "key" => "trnsl.1.1.20191031T124411Z.cd9c82c2cc463cb9.1b5043a3a12489d25f21ccca1441a6f263b6322f",
            "text" => $msg,
            "lang" => "en"
        )) );

        $request_params = array(
            "appid" => "22c7bf8e593c47b0cf88f390e8e5376a",
            "units" => "metric",
            "lang" => "ru",
            "q" => $trans->text[0]
        );
                    
        $get_params = http_build_query($request_params);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://api.openweathermap.org/data/2.5/weather?". $get_params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $weather = json_decode(curl_exec($curl));
        curl_close($curl);
                
        $temp = $weather->main->temp;
        $desc = $weather->weather[0]->description;
        $humidity = $weather->main->humidity;
        $windspeed = $weather->wind->speed;
        $err = $weather->cod;

        if($err == 404) {
            $this->vk->SendMessage(":: Такого города/страны нет", $this->peer_id);
            return;
        }

        if($err != 200) {
            $this->vk->SendMessage(":: Произошла ошибка OpenWeatherMap: " . $err, $this->peer_id);
            return;
        }

        $this->vk->SendMessage(":: Погода в" . $msg . ":\n" . 
        "- Описание: " . strval($desc) . 
        "\n- Температура: " . strval($temp) . 
        "°C\n- Влажность: " . strval($humidity) . 
        "%\n- Скорость ветра: " . strval($windspeed) . "м/с", $this->peer_id);
    }
}

?>