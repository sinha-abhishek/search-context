<?php
include "phar://lib/neo4jphp.phar";
include "Person.class.php";
$client = new Everyman\Neo4j\Client();
//Read File
$data = file_get_contents("testdata");
$index = new Everyman\Neo4j\Index\NodeIndex($client, 'emails');
$index->save();
//Convert JSON into oject
$obj = json_decode($data);
$arrObj = array($obj);
$test = array($arrObj[0]);
$tdata = array($test[0]);
$actualData = $tdata[0];
foreach ($actualData->testdata as $index => $person) {
	# code...
	$per = new Person($person->person->name, $person->person->email);
	

	//echo "\n##############";
}
	//Call to create a node for person

	//Call to create nodes for each property like college, company etc.
	
	//relate each

	//For each friend
	//Create node recursively with relationships
	//add friendship relation in db

?>