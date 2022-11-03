<?php
/**
 * Автор: Олег Харкевич
 *
 * Дата реализации: 01.11.2022 13:00
 *
 * Дата изменения: 02.11.2022 15:40
 *
 * Утилита для работы с базой данных
 **/	
	require_once('peoplesdb_class.php');
	require_once('collection_class.php');
	
	$servername = "localhost";
	$username = "root";
	$password = "";
	// Create connection
	$conn = new mysqli($servername, $username, $password);
	// Check connection
	if ($conn->connect_error) {	
		die("Connection failed: " . $conn->connect_error);
	}
	// Create database
	$sql = "CREATE DATABASE `testDB`";
	if ($conn->query($sql) === TRUE) {
		echo 'Database created successfully';
	} else {
		echo "Error creating database: " . $conn->error;
	}
	//Choose DB
	$conn->select_db("testDB");
	//Create table
	$sql = "CREATE TABLE IF NOT EXISTS `peoples`(
						`id` int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
						`firstname` varchar(50) NOT NULL,
						`lastname` varchar(50) NOT NULL,
						`birthday` date NOT NULL,
						`gender` enum('0','1') NOT NULL,
						`place_birth` varchar(60) NOT NULL
						) ENGINE=MyISAM DEFAULT CHARSET=utf8";					
	if ($conn->query($sql)) {	
		echo 'Таблица peoples успешно создана';
	} else {	
		echo 'Ошибка: ' . $conn->error;
	}
	//Add unique keys
	$sql = "ALTER TABLE `peoples` ADD UNIQUE KEY (`firstname`,`lastname`,`birthday`) USING BTREE";	
	if ($conn->query($sql)) {
		echo "Ключи добавлены";
	} else {
		echo "Ошибка: " . $conn->error;
	}
	// Upload data
	$sql = "INSERT INTO `peoples` (`firstname`, `lastname`, `birthday`, `gender`, `place_birth`) VALUES
									('John', 'Deliver', '1995-08-10', '1', 'Paris'),
									('Molly', 'Farmer', '1996-12-30', '0', 'Gavre'),
									('Ken', 'Laslo', '2001-07-12', '1', 'Roma'),
									('Mike', 'Jordan', '1996-06-22', '1', 'Washington'),
									('Kate', 'Bush', '1986-07-14', '0', 'London'),
									('Garry', 'Potter', '1996-05-09', '1', 'Boston'),
									('Nataly', 'Pushkash', '1998-11-13', '0', 'Buffallo'),
									('Grisha', 'Rublevkin', '1989-04-19', '1', 'Madrid'),
									('Tichon', 'Tarelkin', '1988-01-29', '1', 'Madrid'),
									('Linda', 'Karlsson', '2002-09-16', '0', 'Aberdeen'),
									('Barry', 'Horsten', '2005-02-10', '1', 'Boston'),
									('Lilly', 'Massier', '2000-04-20', '0', 'Paris'),
									('Michael', 'Parry', '2003-11-22', '1', 'Vancuver');";
	if ($conn->query($sql)) {
		echo "Данные загружены";
	} else {
		echo "Ошибка: " . $conn->error;
	}
	$conn->close();
	/**
	*	Переменная в аргументе конструктора может быть массивом 
	*	с данными для создания экземпляра класса
	**/
	$data_person = array('firstname' => 'Kris', 
						'lastname' => 'Rainolds',
						'birthday' => '1998-11-11',
						'gender' => 1,
						'place_birth' => 'Roma'
						);
	/**
	*	или цифровым значением для поиска id в базе данных
	**/
	//$data_person = 1;					
	$person = new PeoplesDB($data_person);
	
	/**	
	*	удаление $person::deletePerson(13);
	**/
	
	//Коллекция на основе поиска
	$coll = new Collection('gender', '>', '0');

	echo "<pre>";
	var_dump($person);
	var_dump($coll);
	var_dump($coll->getSearchCollection());
	var_dump($coll->getSearchCollectionFormatPerson ());
	echo "</pre>";
?>
