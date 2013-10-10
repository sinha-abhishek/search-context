<?php
#include "phar://lib/neo4jphp.phar";
use Everyman\Neo4j\Relationship;
class Person {
	 public $id; //database ID
	 public $email;
	 public $name;
	 function __construct( $name, $email ) {
	 	$this->name = $name;
	 	$this->email = $email;
	 	#echo "EMAIL = ".$this->email;
	 	$this->id = $this->find(true); 
	 	echo ("Got ".$this->id);
	 }


	 public static function exists($email) {
	 	$client = new Everyman\Neo4j\Client();
	 	$index = new Everyman\Neo4j\Index\NodeIndex($client, 'emails');
	 	$match = $index->findOne("email",$email);
	 	if($match == null) {
	 		return false;
	 	}
	 	return true;
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
	 		#echo "Eureka ".print_r($match,1);
	 		return $match->getId();
	 	}
	 }

	 private function checkRelationship($node, $entity, $relationship) {
	 	$outgoing = $node->getRelationships(array($relationship), Relationship::DirectionOut);
	 	foreach ($outgoing as $relation) {
	 		# code...
	 		$entityNode = $relation->getEndNode();
	 		if($entity->id == $entityNode->getId()) {
	 			echo "FOUND ".$relationship;
	 			return true;
	 		}
	 	}
	 	return false;
	 }

	 public function relate($entity, $relationship){
	 	//Create relationship between entity and person
	 	echo "<BR>RELATE ".$this->email." ".$entity->value."<BR>";
	 	$client = new Everyman\Neo4j\Client();
	 	//Fetch nodes with ID
	 	$personNode = $client->getNode($this->id);
	 	$entityNode = $client->getNode($entity->id);
	 	if($this->checkRelationship($personNode,$entity,$relationship)) {
	 		return;
	 	}
	 	$personNode->relateTo($entityNode,$relationship)->save();
	 	//Relate 
	 }

	 public function addFriend($person){
	 	//Find, if not found create and relate
	 	$client = new Everyman\Neo4j\Client();
	 	$friend1 = $client->getNode($this->id);
	 	$friend2 = $client->getNode($person->id);
	 	if($this->checkRelationship($friend1,$person, "KNOWS")) {
	 		return;
	 	}
	 	$friend1->relateTo($friend2,"KNOWS")->save();
	 	$friend2->relateTo($friend1,"KNOWS")->save();
	 }


}

?>