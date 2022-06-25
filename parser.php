<?php

index($argv);
function index($argv)
{
	$servername = "localhost"; // локалхост
	$username = "root"; // имя пользователя
	$password = ""; // пароль если существует
		
		// Создание соединения
	$conn = new mysqli($servername, $username, $password);
	$conn->set_charset("utf8"); // устанавливаем кодировку
	if ($conn->connect_error) {
		die("Ошибка подключения: " . $conn->connect_error);
	}
		// создаем базу данных auto
	$sql = "CREATE DATABASE IF NOT EXISTS auto CHARSET=utf8";
	if ($conn->query($sql) === TRUE) {
		$dbname = "auto";
		$conn = new mysqli($servername, $username, $password, $dbname);
		$conn->set_charset("utf8");
	} else {
		echo "Ошибка создания базы данных: " . $conn->error;
	}
		// создаем табличку catalog
	$sql = "CREATE TABLE IF NOT EXISTS catalog (
			id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			mark VARCHAR(50) NOT NULL,
			model VARCHAR(50) NOT NULL,
			generation VARCHAR(50),
			year INT(5) UNSIGNED,
			run INT(11) UNSIGNED,
			color VARCHAR(50) NOT NULL,
			body_type VARCHAR(50) NOT NULL,
			engine_type VARCHAR(50) NOT NULL,
			transmission VARCHAR(50) NOT NULL,
			gear_type VARCHAR(50) NOT NULL,
			generation_id INT(11) UNSIGNED NOT NULL
		)";

	if ($conn->query($sql) === TRUE) {} else {
		echo "Ошибка создания базы данных: " . $conn->error;
	}

	$sql = "SELECT * FROM catalog";
	$data = $conn->query($sql);
	if (count($argv) > 1) {
		$xmlstr = file_get_contents($argv[1]);
	} else {
		$xmlstr = file_get_contents('data_light.xml');
	}

	$auto_catalog = new SimpleXMLElement($xmlstr);

	$json = json_encode($auto_catalog);
	$array = json_decode($json, TRUE);
	$ids_from_xml = [];
	foreach ($array['offers']['offer'] as $offer) {
		$is_exists = $conn->query("SELECT * FROM catalog WHERE id = " . $offer['id'])->fetch_assoc();
		array_push($ids_from_xml, $offer['id']);
		$id = $offer['id'];
		$mark = $offer['mark'];
		$model = $offer['model'];
		$generation = $offer['generation'];
		$year = $offer['year'];
		$run = $offer['run'];
		$color = $offer['color'];
		$body_type = $offer['body-type'];
		$engine_type = $offer['engine-type'];
		$transmission = $offer['transmission'];
		$gear_type = $offer['gear-type'];
		$generation_id = $offer['generation_id'];
		if (gettype($mark) == 'array') {
			$mark = '';
		}
		if (gettype($model) == 'array') {
			$generation = '';
		}
		if (gettype($generation) == 'array') {
			$generation = '';
		}
		if (gettype($year) == 'array') {
			$year = '';
		}
		if (gettype($run) == 'array') {
			$run = '';
		}
		if (gettype($color) == 'array') {
			$color = '';
		}
		if (gettype($body_type) == 'array') {
			$body_type = '';
		}
		if (gettype($engine_type) == 'array') {
			$engine_type = '';
		}
		if (gettype($transmission) == 'array') {
			$transmission = '';
		}
		if (gettype($gear_type) == 'array') {
			$gear_type = '';
		}
		if (gettype($generation_id) == 'array') {
			$generation_id = '';
		}

		if ($is_exists) {
			$sql = "UPDATE catalog SET mark='$mark', model='$model', generation='$generation', year='$year', run='$run', color='$color', body_type='$body_type', engine_type='$engine_type', transmission='$transmission', gear_type='$gear_type', generation_id='$generation_id'  WHERE id = $id";
			if ($conn->query($sql) === FALSE) {
				echo "Ошибка обновления: " . $conn->error;
			}
		} else {
			$sql = "INSERT INTO catalog (id, mark, model, generation, year, run, color, body_type, engine_type, transmission, gear_type, generation_id) VALUES ('$id', '$mark', '$model', '$generation', '$year', '$run', '$color', '$body_type', '$engine_type', '$transmission', '$gear_type', '$generation_id')";
			if ($conn->query($sql) === FALSE) {
				echo "Ошибка вставки: " . $conn->error;
			}
		}
	}
	$sql = "SELECT * FROM catalog";
	$catalog = $conn->query($sql);
	foreach ($catalog as $cat) {
		$b = 0;
		for ($i = 0; $i < count($ids_from_xml); $i++) {
			if ($cat['id'] == $ids_from_xml[$i]) {
				$b = 1;
				break;
			}
		}
		if (!$b) {
			$sql = "DELETE FROM catalog WHERE id=" . $cat['id'];
			if ($conn->query($sql) === FALSE) {
				echo "Ошибка удаления: " . $conn->error;
			}
		}
	}
	$conn->close();
}
