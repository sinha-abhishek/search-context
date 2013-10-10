<?php
class Entity {
	public $id;
	public $type;
	public $value;
	function __construct($type,  $value) {
		//Construct by creating a node
		$this->type = $type;
		$this->value = $value;
		#echo "Value =".$value;
		$this->id = $this->find(true);
		#echo ("Got ".$this->id);
	}

	public function find($createIfFailed = false){
		$client = new Everyman\Neo4j\Client();
	 	$index = new Everyman\Neo4j\Index\NodeIndex($client, $this->type);
	 	$match = $index->findOne("name",$this->value);
	 	if($match == null && $createIfFailed) {
	 		$node = $client->makeNode();
	 		$node->setProperty("name",$this->value)
	 		->setProperty("type",$this->type)
	 		->save();
	 	     
	 		$index->add($node,"name",$this->value);
	 		//$this->id = $client->getId();
	 		return $node->getId();
	 	}
	 	else {
	 		#echo "Eureka ".print_r($match,1);
	 		return $match->getId();
	 	}
	}
}

?>
