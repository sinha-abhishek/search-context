<?php
#include "phar://lib/neo4jphp.phar";
class Person {
	 public $id; //database ID
	 public $email;
	 public $name;
	 function __construct( $name, $email ) {
	 	$this->name = $name;
	 	$this->email = $email;
	 	echo "EMAIL = ".$this->email;
	 	$this->id = $this->find(true); 
	 	echo ("Got ".$this->id);
	 }

	 public function find($createIfFailed = false ) {
	 	$client = new Everyman\Neo4j\Client();
	 	$index = new Everyman\Neo4j\Index\NodeIndex($client, 'emails');
	 	$match = $index->findOne("email",$this->email);
	 	if($match == null && $createIfFailed) {
	 		$node = $client->makeNode();
	 		$node->setProperty("email",$this->email)
	 		->setProperty("type","person")
	 		->setProperty("name",$this->name)
	 		->save();
	 	     
	 		$index->add($node,"email",$this->email);
	 		//$this->id = $client->getId();
	 		return $node->getId();
	 	}
	 	else {
	 		echo "Eureka ".print_r($match,1);
	 	}
	 }

	 public function relate($entity, $relationship){
	 	//Create relationship between entity and person

	 	//Fetch nodes with ID

	 	//Relate 
	 }

	 public function addFriend($person){
	 	//Find, if not found create and relate
	 }


}

?>