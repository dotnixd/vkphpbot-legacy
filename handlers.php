<?php

require_once("utils.php");
require_once("Field_calculate.php");
require_once("smeh.php");

class Handlers {
    private $vk;
    private $db;
    private $u;

    public function __construct($api, $sql) {
        $this->vk = $api;
        $this->db = $sql;
        $this->u = new Utils($this->db);
    }
    
    public function Run($data) {
        $text = $data->object->text;
        $peer_id = $data->object->peer_id;
        $user_id = $data->object->from_id;
        $action = $data->object->action;
        $f = \explode(" ", $text);

        if($data->object->id) {
            $this->vk->SendMessage(":: Бота нельзя использовать в личных сообщениях (пока что)", $peer_id);
            return;
        }

        switch(\substr($f[0], 1)) {
            case "жив?":
            case "пинг":
                $this->vk->SendMessage(":: Понг\nБот работает", $peer_id);
            break;
            case "хелп":
            case "помощь":
                $this->vk->SendMessage(":: !помощь (!хелп) - получить помощь
:: !пинг - проверить работоспособность бота
:: !кто <что-нибудь> - выберет случайного участика беседы
:: !выбери <что-то> или <что-то> - выбрать случайное значение
:: !вероятность <чего-нибудь> - сгенерировать случайную вероятность
:: !пуш <сообщение (опционально)> - позвать всех участников (модератор)
:: !перевод <сообщение> - перевести русский текст на английский язык, если язык сообщения другой, то сообщение будет переведено на русский язык
:: !онлайн - получить список участников которые в сети (модератор)
:: !оффлайн - получить список участников которые не в сети (модератор)
:: !погода <город> - получить текущую погоду
:: !тимкук - пикча с презентации эппл (нужно прикрепить пикчу) (в тестировании)
:: !приветствие <сообщение> - установить приветствие
:: !роль - получить роль
:: !кончил - кончить на пикчу (нужно приложить пикчу)
:: !документ <название> - поиск документов ВК
:: !огорчило - приклеить наклеку \"огорчило\" (нужно прикрепить пикчу)
:: !смех - генератор смеха (для справки напишите \"!смех -h\")
:: !калькулятор (пример) - вычислить ответ примера
:: !когда <текст> - предугадать когда произойдет что-то
:: !чтозааниме - найти аниме по пикче (нужно прикрепить пикчу)

Репозиторий на GitHub - https://github.com/OverPie/vkphpbot", $peer_id);
            break;
            case "кто":
                $users = $this->vk->GetDialogMembers($peer_id);
                if($users->error->error_code) {
                    $this->vk->SendMessage(":: Произошла ошибка: " . $users->error->error_msg, $peer_id);
                    return;
                }

                $who = $this->ArrayToString($f, $peer_id);

                $k = \rand() % \count($users->response->profiles);
                $name = $users->response->profiles[$k]->first_name . " " . $users->response->profiles[$k]->last_name;

                $this->vk->SendMessage(":: Кто" . $who . "? Возможно это [id" . $users->response->profiles[$k]->id . "|" . $name . "]", $peer_id);
            break;
            case "выбери":
                $str = "";
                $chooses = array();

                if(\count($f) == 1) {
                    $this->vk->SendMessage(":: Команда требует аргументов. Пиши \"!хелп\"", $peer_id);
                    return;
                }

                for($i = 1; $i < \count($f); $i++) {
                    if($f[$i] == "или") {
                        array_push($chooses, $str);
                        $str = "";
                    } else {
                        $str .=  " " . $f[$i];
                    }
                }

                $k = \rand() % \count($chooses);
                $this->vk->SendMessage(":: Выбрано:" . $chooses[$k], $peer_id);
            break;
            case "вероятность":
                $msg = $this->ArrayToString($f, $peer_id);
                $this->vk->SendMessage(":: Вероятность того, что" . $msg . " составляет " . strval(\rand() % 100) . "%", $peer_id);
            break;
            case "пуш":
                $msg = "";

                for($i = 1; $i < \count($f); $i++) {
                    $msg .= " " . $f[$i];
                }

                $users = $this->vk->GetDialogMembers($peer_id);
                if($users->error->error_code) {
                    $this->vk->SendMessage(":: Произошла ошибка: " . $users->error->error_msg, $peer_id);
                    return;
                }

                $mention = "";
                foreach($users->response->profiles as $b) {
                    $mention .= "[id" . $b->id . "|ᅠ]";
                }

                $this->vk->Request("messages.send", array(
                    "peer_id" => $peer_id,
                    "random_id" => 0,
                    "message" => ":: [id" . $user_id . "|" . $this->vk->GetFirstName($user_id) . "] хочет сделать объявление:" . $msg . $mention
                ));
            break;
            case "перевод":
                $msg = $this->ArrayToString($f);

                $lang = json_decode( $this->vk->Post("https://translate.yandex.net/api/v1.5/tr.json/detect", array(
                    "key" => "trnsl.1.1.20191031T124411Z.cd9c82c2cc463cb9.1b5043a3a12489d25f21ccca1441a6f263b6322f",
                    "text" => $msg
                )) );

                if($lang->lang == "ru") {
                    $l = "en";
                } else {
                    $l = "ru";
                }

                $trans = json_decode( $this->vk->Post("https://translate.yandex.net/api/v1.5/tr.json/translate", array(
                    "key" => "trnsl.1.1.20191031T124411Z.cd9c82c2cc463cb9.1b5043a3a12489d25f21ccca1441a6f263b6322f",
                    "text" => $msg,
                    "lang" => $l
                )) );

                $this->vk->SendMessage(":: Перевод\n" . $trans->text[0], $peer_id);
            break;
            case "онлайн":
                $users = $this->vk->GetDialogMembers($peer_id);
                if($users->error->error_code) {
                    $this->vk->SendMessage(":: Произошла ошибка: " . $users->error->error_msg, $peer_id);
                    return;
                }

                $online = "";

                foreach($users->response->profiles as $b) {
                    if($b->online) {
                        $online .= "\n" . "- [id" . $b->id . "|" . $b->first_name . " " . $b->last_name . "]";
                    }
                }

                $this->vk->SendMessage(":: Участники онлайн" . $online, $peer_id);
            break;
            case "оффлайн":
                $users = $this->vk->GetDialogMembers($peer_id);
                if($users->error->error_code) {
                    $this->vk->SendMessage(":: Произошла ошибка: " . $users->error->error_msg, $peer_id);
                    return;
                }

                $online = "";

                foreach($users->response->profiles as $b) {
                    if( !($b->online) ) {
                        $online .= "\n" . "- [id" . $b->id . "|" . $b->first_name . " " . $b->last_name . "]";
                    }
                }

                $this->vk->SendMessage(":: Участники оффлайн" . $online, $peer_id);
            break;
            case "погода":
                $msg = $this->ArrayToString($f, $peer_id);

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
                    $this->vk->SendMessage(":: Такого города/страны нет", $peer_id);
                    return;
                }

                if($err != 200) {
                    $this->vk->SendMessage(":: Произошла ошибка OpenWeatherMap: " . $err, $peer_id);
                    return;
                }

                $this->vk->SendMessage(":: Погода в " . $f[1] . ":\n" . 
                "- Описание: " . strval($desc) . 
                "\n- Температура: " . strval($temp) . 
                "°C\n- Влажность: " . strval($humidity) . 
                "%\n- Скорость ветра: " . strval($windspeed) . "м/с", $peer_id);
		    break;
			case "чтозааниме":
				$url = $data->object->attachments[0]->photo->sizes[\count($data->object->attachments[0]->photo->sizes) - 1]->url;

				if(!$url) {
				    $this->vk->SendMessage(":: Нужно прикрепить пикчу", $peer_id);
					return;
				}

				$request_params = array(
				    "url" => $url
				);

				$anime = $this->vk->Post("https://trace.moe/api/search", $request_params);

				$get_params = http_build_query($request_params);
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, "https://trace.moe/api/search?". $get_params);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$anime = json_decode(curl_exec($curl));
                curl_close($curl);
                
            //     name = encode["docs"][0]["title_english"]
            // episode = encode["docs"][0]["episode"]
            // chance = round(encode['docs'][0]["similarity"] * 100)
            // sec = round(encode["docs"][0]["from"])
            // time = timedelta(seconds = sec)
                $name = $anime->docs[0]->title_english;
                $episode = $anime->docs[0]->episode;
                $chance = round($anime->docs[0]->similarity * 100);
                $time = gmdate("H:i:s", $anime->docs[0]->from);

                $this->vk->SendMessage(":: " . $name . "\nСезон: ". $episode . "\nТочность: " . $chance . "\nВремя: " . $time, $peer_id);
		    break;
            case "тимкук":
                foreach($data->object->attachments as $a) {
                    if($a->type == "photo") {
                        $this->u->Download($a->photo->sizes[\count($a->photo->sizes) - 1]->url, "pics/vk.jpg");
                    }
                }
 
                $tim = imagecreatefrompng("pics/tim.png");
                $meme = imagecreatefromjpeg("pics/vk.jpg");

                if(!$meme) {
                    $this->vk->SendMessage(":: Нужно прикрепить пикчу", $peer_id);
                    return;
                }

                $image = imagecreatetruecolor(imagesx($tim), imagesy($tim));
                imagecopyresized($image, $meme, 0, 0, 0, 0, imagesx($tim), imagesy($tim) - 25, imagesx($meme), imagesy($meme));
                imagecopyresized($image, $tim, 0, 0, 0, 0, imagesx($tim), imagesy($tim), imagesx($tim), imagesy($tim));
                imagepng($image, "pics/pic.png", 9);

                $attach = $this->vk->UploadImage("pics/pic.png", $peer_id);

                $this->vk->SendMessage(":: Ваша пикча", $peer_id, $attach);

                unlink("pics/vk.jpg");
                unlink("pics/pic.png");
		    break;
            case "огорчило":
                foreach($data->object->attachments as $a) {
                    if($a->type == "photo") {
                        $this->u->Download($a->photo->sizes[\count($a->photo->sizes) - 1]->url, "pics/vk.jpg");
                    }
                }

                $cum = imagecreatefrompng("pics/oof.png");
                $meme = imagecreatefromjpeg("pics/vk.jpg");

                if(!$meme) {
                    $this->vk->SendMessage(":: Нужно прикрепить пикчу", $peer_id);
                    return;
                }

                $image = imagecreatetruecolor(imagesx($cum), imagesy($cum));
                imagecopyresized($image, $meme, 0, 0, 0, 0, imagesx($cum), imagesy($cum), imagesx($meme), imagesy($meme));
                imagecopyresized($image, $cum, 0, 0, 0, 0, imagesx($cum), imagesy($cum), imagesx($cum), imagesy($cum));
                imagepng($image, "pics/pic.png", 9);

                $attach = $this->vk->UploadImage("pics/pic.png", $peer_id);

                $this->vk->SendMessage(":: Ваша пикча", $peer_id, $attach);
                unlink("pics/vk.jpg");
                unlink("pics/pic.png");
            break;
            case "кончил":
                foreach($data->object->attachments as $a) {
                    if($a->type == "photo") {
                        $this->u->Download($a->photo->sizes[\count($a->photo->sizes) - 1]->url, "pics/vk.jpg");
                    }
                }

                $cum = imagecreatefrompng("pics/cum.png");
                $meme = imagecreatefromjpeg("pics/vk.jpg");

                if(!$meme) {
                    $this->vk->SendMessage(":: Нужно прикрепить пикчу", $peer_id);
                    return;
                }

                $image = imagecreatetruecolor(imagesx($cum), imagesy($cum));
                // imagecopyresized($image, $meme, 216, 111, 403, 372, imagesx($cum), imagesy($cum), imagesx($meme), imagesy($meme));
                imagecopyresized($image, $meme, 0, 0, 0, 0, imagesx($cum), imagesy($cum), imagesx($meme), imagesy($meme));
                imagecopyresized($image, $cum, 0, 0, 0, 0, imagesx($cum), imagesy($cum), imagesx($cum), imagesy($cum));
                imagepng($image, "pics/pic.png", 9);

                $attach = $this->vk->UploadImage("pics/pic.png", $peer_id);

                $this->vk->SendMessage(":: Ваша пикча", $peer_id, $attach);
                unlink("pics/vk.jpg");
                unlink("pics/pic.png");
            break;
            case "приветствие":
                $msg = $this->ArrayToString($f, $peer_id);
                
                $this->vk->SendMessage(":: Приветствие изменено", $peer_id);
    
                $ok = $this->db->GetConn()->prepare("UPDATE dialogs SET greeting=? WHERE peer_id=?");
                $ok->execute(array($msg, $peer_id)); 
		    break;
			case "когда":
				$msg = $this->ArrayToString($f, $peer_id);
				$when = "Через " . rand() % 1000 . " ";
				$w = rand() % 5;
				switch($w) {
				case 0:
				    $when .= "лет";
				break;
				case 1:
				    $when .= "дней";
				break;
				case 2:
				    $when .= "часов";
				break;
				case 3:
				    $when .= "минут";
				break;
				default:
					$when .= "секунд";
				}

				$this->vk->SendMessage(":: " . $when . $msg, $peer_id);
		    break;
            case "роль":
                $role = $this->u->GetRole($peer_id, $user_id);
                $role_s = "";

                switch($role) {
                    case 0:
                        $role_s = "участник беседы";
                    break;
                    case 1:
                        $role_s = "модератор";
                    break;
                    case 2:
                        $role_s = "администратор";
                    break;
                    default:
                        $role_s = "хз кто";
                }

                $this->vk->SendMessage(":: " . $this->GetMention($user_id) . ", ваша роль: ". $role_s, $peer_id);
            break;
            case "документ":
                $msg = $this->ArrayToString($f, $peer_id);
                $video = json_decode( $this->vk->Request("docs.search", array("q" => $msg, "count" => 1)) );

                if($video->response->count == 0) {
                    $this->vk->SendMessage(":: Документ не найден", $peer_id);
                    return;
                }

                $attach = "doc" . $video->response->items[0]->owner_id . "_" . $video->response->items[0]->id;

                $this->vk->SendMessage(":: Ваш документ", $peer_id, $attach);
            break;
            case "смех":
                $smeh = new Smeh($f);
                $smeh->Parse();
                if($smeh->GetCount() > 2000) {
                    $this->vk->SendMessage(":: Слишком большое число -c", $peer_id);
                    return;
                } 

                $this->vk->SendMessage($smeh->Generate(), $peer_id);
            break;
            case "калькулятор":
                $msg = $this->ArrayToString($f, $peer_id);
                if(\count($msg) > 2000) {
                    $this->vk->SendMessage(":: Слишком длинный пример", $peer_id);
                    return;
                }

                $calc = new Field_calculate();
                $this->vk->SendMessage(":: Результат:\n" . $calc->calculate($msg), $peer_id);
            break;
            case "whoa":
                $this->vk->SendMessage($this->vk->GetID($f[1]), $peer_id);
            break;
            case "сетроль":
                if(\count($f) < 3) {
                    $this->vk->SendMessage(":: Команда требует аргументов. Пиши \"!хелп\"", $peer_id);
                    return;
                }

                if($this->u->GetRole($peer_id, $user_id) < 2) {
                    $this->vk->SendMessage(":: У вас нет прав на использование этой команды", $peer_id);
                    return;
                }

                $dog = $this->vk->GetID($f[1]);
                if($dog == FALSE) {
                    $this->vk->SendMessage(":: Такого пользователя нет", $peer_id);
                    return;
                }

                $this->vk->SendMessage(":: Права выданы", $peer_id);
                $r = 0;

                switch($f[2]) {
                    case "админ":
                    case "администратор":
                    case "а":
                        $r = 2;
                    break;
                    case "модер":
                    case "модератор":
                    case "м":
                        $r = 1;
                    break;
                }

                $this->u->SetRole($peer_id, $user_id, $r);

            break;
        }

        if($action) {
            if($action->type == "chat_invite_user") {
                if($action->member_id == -189095114) {
                    $this->vk->SendMessage(":: /usr/bin/php приглашён в чат!", $peer_id);
                    $this->u->SetRole($peer_id, $user_id, 2);
                    if($this->db->GetConn()->query("SELECT peer_id FROM dialogs WHERE peer_id=\"" . $peer_id . "\"")->fetch()) {
                        return;
                    }

                    $this->db->GetConn()->prepare("INSERT INTO dialogs (peer_id) VALUES(?)")->execute(array($peer_id));
                    return;
                }

                $ok = $this->db->GetConn()->query("SELECT greeting FROM dialogs WHERE peer_id=\"" . $peer_id . "\"")->fetch();

                if(!$ok) {
                    $this->vk->SendMessage(":: " . $this->GetMention($action->member_id) . ", приветствуем в чате!", $peer_id);
                    return;
                }

                $this->vk->SendMessage(":: " . $this->GetMention($action->member_id) . "," . $ok["greeting"], $peer_id);

			} elseif ($action->type == "chat_kick_user") {
                if($action->member_id == -189095114) {
                    $this->db->GetConn()->prepare("DELETE FROM dialogs WHERE peer_id=?").execute(array($peer_id));

                    return;
                }

                $this->vk->SendMessage(":: " . $this->GetMention($action->member_id) . ", пока!", $peer_id);
            }
        }
    }

    public function ArrayToString($f, $peer_id) {
        if(\count($f) == 1) {
            $this->vk->SendMessage(":: Команда требует аргументов. Пиши \"!хелп\"", $peer_id);
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
