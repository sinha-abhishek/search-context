<?php
	$method = $_POST['method'];
	$data = $_POST;
	FormHelper::HandleInput($data, $method);
	class FormHelper {
		static function HandleInput($data, $method) {
			if($method == "addUser") {
				
			}

		}
	}

?>