<?php

 /**
* База данных содержит следующие поля id, имя(только буквы), фамилия(только буквы),   
* дата рождения, пол(0,1), город рождения 
* Класс содержит такие же приватные поля + статическое поле table + 
* статический массив знаков математических операторов + константу допустимой длины строки
* Конструктор принимает аргумент(массив - для добавления данных по новой персоне в БД или 
* цифровое значение id по которому идёт поиск и выбираеются данные из БД  
* При получении аргумента массива поля класса заполняются значениями из него метод - createPerson, 
* обрабатываются и вставляются в базу данных - метод savePerson
* при получении цифрового значения id  в конструкторе - извлекаются данные из БД метод getPerson 
* и инициализируются в методе createPerson 
* Предусмотрена проверка на валидность цифровых, строковых, булеан данных и формата даты 
* методы - validateID, validateBoolean, validateText, validateDate;
* Преобразование значения булеан в смысловкю строку - метод getGenderString
* Преобразование значения даты в полный возраст персоны - метод getFullAge 
* соответствии с массивом, полученным в конструкторе - метод deleteSearchCollection;
* Публичный метод searchOnField - поможет вести поиск по БД в классе Collection 
**/

	class PeoplesDB
	{
		private $id;
		private $firstname;
		private $lastname;
		private $birthday;
		private $gender;
		private $place_birth;
		
		protected static $table = 'peoples';
		protected static $symbols = array('=', '!=', '>', '<');
		
		const MAX_LEN = 60;
		
		public function __construct($input = false)
		{
			if (is_array($input)) {
				$this->savePerson($input);
			} else {	
				if($this->validateID($input)) {
					$this->getPerson($input);	
				}
			}
		}
		
		private function createPerson($input)
		{
			foreach ($input as $property => $value){	
				$this->$property = $value;	
			}
			return $this;
		}
		
		private static function setConnectDB()
		{
			$connect = new mysqli ('localhost', 'root', '', 'testDB');
			if ($connect->connect_errno){ exit( "Ошибка соединения с базой данных!" ); }
			$connect->set_charset("utf8");
			return $connect;
		}
		
		private function savePerson($input)
		{
			$connect = self::setConnectDB();
			$this->createPerson($input);
			if (self::validateText($this->firstname) 
				&& self::validateText($this->lastname) 
				&& self::validateDate($this->birthday) 
				&& self::validateBoolean($this->gender)  
				&& self::validateText($this->place_birth)) {														
				$values = "('{$connect->real_escape_string($this->firstname)}', 
							'{$connect->real_escape_string($this->lastname)}', 
							'{$connect->real_escape_string($this->birthday)}',
							'{$connect->real_escape_string($this->gender)}', 
							'{$connect->real_escape_string($this->place_birth)}');";							
				$query = ("INSERT INTO `peoples` (`firstname`, 
													`lastname`, 
													`birthday`, 
													`gender`,
													`place_birth`) VALUES $values");
			$result = $connect->query($query);
			} else {	
				echo 'Некорректные данные для сохранения в базу';
			}
			if ($result) { 
				return true;
			} else {
				echo 'Error !</h2>'. $result->error_list;
			}
		}
		
		private function getPerson($id)
		{
			$connect = self::setConnectDB();
			$query = ("SELECT `id`, `firstname`, `lastname`, 
								`birthday`, `gender`, `place_birth` 
									FROM `" . self::$table . "` WHERE `id` = $id");
			$result = $connect->query($query);
			$row = $result->fetch_array(MYSQLI_ASSOC);
		
			if (!is_null ($row)){
				$this->createPerson($row);
			} else {
				return false;
			}
		}
		
		public static function deletePerson($id)
		{
			$connect = self::setConnectDB();
			$query = ("DELETE FROM `peoples` WHERE `id` = $id");
			$result = $connect->query($query);
			if ($result) return true;
		}
		
		public function getViewPerson()
		{
			$data_person = new stdClass();
			foreach ($this as $property => $value){
				if ($property == 'birthday'){	
					$data_person->$property = self::getFullAge($value);	
				} elseif ($property == 'gender'){	
					$data_person->$property = self::getGenderString($value);	
				} else {
					$data_person->$property = $value;
				}
			}
			return $data_person;
		}
		
		private static function getFullAge($birthday)
		{
			if (!is_null($birthday)){
				$dates = explode("-", $birthday);
				$y = $dates[0];
				$m = $dates[1];
				$d = $dates[2];
				if ($m > date('m') || $m == date('m') && $d > date('d')){
					return (date('Y') - $y - 1);
				} else {	
					return (date('Y') - $y);
				}
			}
		}
		
		private static function getGenderString($gender)
		{
			if(!is_null($gender)){
				$gender = (int) $gender;
				if(self::validateBoolean($gender)) {
					if ($gender){	
						return 'Male';	
					} else {	
						return 'Female';
					}
				}
			}
		}
		
		protected static function validateID($id) 
		{
			$id = (int) $id;
			if (!is_null($id) && ((!is_int($id)) || ($id < 1))){ 
				return false;
			} else {
				return true;
			}
		}
		
		protected static function validateBoolean($gender) 
		{
			if (($gender != 0) && ($gender != 1)){ 
				return false;
			} else {
				return true;
			}
		}
		
		protected static function validateText($firstname)
		{
			if (mb_strlen($firstname) == 0){ 
				return false;
			} elseif (mb_strlen($firstname) > self::MAX_LEN){
				return false;
			} else {
				return true;
			}
		}
		
		protected static function validateSymbol($symbol)
		{
			if (!in_array($symbol, self::$symbols)){ 
				return false;
			} else {
				return true;
			}
		}
		
		protected static function validateDate($date, $format = 'Y-m-d')
		{	
			$d = DateTime::createFromFormat($format, $date);
			return $d && $d->format($format) == $date;
		}
		
		public function searchOnField($field, $simbol, $value)
		{
			$connect = self::setConnectDB();
			$query = ("SELECT `id` FROM `" . self::$table . "` WHERE `{$field}` {$simbol} '{$value}'"); 
			$result = $connect->query($query);
			$rows = $result->fetch_all();
			return $rows;
		}
		
	}
?>
