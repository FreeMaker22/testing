<?php

/**
* Класс обьявляется, если создан класс PeoplesDB, в случае отсутствия 
* последнего выводится ошибка;
* Конструктор ведет поиск id людей по всем полям БД (поддержка 
выражений больше, меньше, не равно); 
* Получение массива экземпляров класса 1 из массива с id людей 
полученного в конструкторе - метод getSearchCollection; 
* Удаление людей из БД с помощью экземпляров класса 1 в 
соответствии с массивом, полученным в конструкторе - метод deleteSearchCollection;
* Получение массива экземпляров класса 1 из массива с id людей 
* полученного в конструкторе с изменённым форматом (дата рождения -> полный возраст персоны, 
* пол персоны(boolean) -> пол в читаемой строке); 
**/

	if (!class_exists('PeoplesDB', false)) { 
		trigger_error("Unable to load class: PeoplesDB", E_USER_WARNING);	
	} else {

		class Collection
		{	
		
			public $peoples_ids = array();
				
			public function __construct($field, $simbol, $value)
			{	
				$person = new PeoplesDB();	
				$this->peoples_ids = $person->searchOnField($field, $simbol, $value);		
			}
				
			public function getSearchCollection() 
			{	
				foreach ($this->peoples_ids as $el => $values){	
					$collection[] = new PeoplesDB($values[0]);	
				}	
				return $collection;
			}
				
			public function getSearchCollectionFormatPerson ()
			{		
				foreach ($this->peoples_ids as $el => $values){	
					$person = new PeoplesDB($values[0]);
					$collection[] = $person->getViewPerson();
				}	
				return $collection;
			}
				
			public function deleteSearchCollection() 
			{	
				foreach ($this->peoples_ids as $el => $values){	
					$collection[] = PeoplesDB::deletePerson($values[0]);
				}		
			}
		
		}
	}
?>
