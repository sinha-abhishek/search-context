<?php
	include "SearchHelper.class.php";
	$query = $_GET["query"];
	$email = $_GET["email"]; //Email of person making query. To simulate logged in user
	//Todo: Parse query to break it into entities and add a relevance score
	$helper = new SearchHelper($email, $query);
	echo print_r($helper->getResults(),1);


?>