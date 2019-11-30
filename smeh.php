<?php

class Smeh {
	private $setup;
	private $c;
	private $s;
	private $or;

	public function __construct($args) {
		$this->setup = $args;
		$this->c = rand() % 50 + 10;
		$this->s = "ХЫЪ";
		$this->or = false;
	}

	public function Char() {
		return mb_substr($this->s, 0, rand() % mb_strlen($this->s) + 1, "utf-8");
	}

	public function Generate() {
		$res = "";
		for($i = 0; $i < $this->c; $i++) {
			if($this->or) {
				$res .= $this->s;
			} else {
				$res .= $this->Char();
			}
		}

		return $res;
	}

	public function String() {
		echo "Count: " . $this->c . "\nString: " . $this->s . "\nOR: " . $this->or . "\n";
	}

	public function GetCount() {
		return $this->c;
	}

	public function Parse() {
		foreach($this->setup as $a) {
			switch($a) {
			case "-c":
				$mode = 1;
			break;
			case "-s":
				$mode = 2;
			break;
			case "-s2":
				$mode = 3;
			break;
			case "-h":
				$this->s = ":: Генератор смеха
Можно передать параметры:
-c (значение) - указать количество символов
-s (строка) - использовать символы из строки
-s2 (строка) - использовать строку как символ";
				$this->c = 1;
				$this->or = true;
				return;
			default:
				switch($mode) {
				case 1:
					$this->c = $a;
				break;
				case 2:
					$this->s = $a;
					$this->or = false;
				break;
				case 3:
					$this->s = $a;
					$this->or = true;
				break;
				}

				$mode = 0;
			}
		}
	}
}

?>
