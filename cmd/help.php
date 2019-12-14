<?php

require_once("module.php");
require_once("modules.php");

class help extends CommandBase {
    public function Setup() { $this->SetOptions("помощь (хелп) - получить помощь", 0, false); }

    public function Run() {
        $commands = ":: Использование - /(команда)\nНапример: /пуш я здесь!\nПрефикс (\"/\") можно использовать любой\nНапример: !пинг, \\пинг\n\nСписок команд:\n";
        foreach(GetModules::Get() as $command => $class) {
            if($class == "help") continue;
            require_once($class . ".php");
            $c = new $class(NULL, NULL, NULL, NULL, NULL, NULL, NULL);
            $c->Setup();
            $commands .= ":: " . $c->description . "\n";
        }

        $commands .= "\nРепозиторий на GitHub - https://github.com/OverPie/vkphpbot";

        $this->vk->SendMessage($commands, $this->peer_id);
    }
}

?>