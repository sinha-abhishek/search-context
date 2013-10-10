<?php
include "Person.class.php";
include "phar://lib/neo4jphp.phar";
use Everyman\Neo4j\Relationship;
use Everyman\Neo4j\PathFinder;
//Parse query

//Get logged in user email

//List nodes with search query. Currently assume exact match of entity

//Find relationship with max depth 3 for each node

//If relationship found sort by degree

//Send result by type with lowest

//If no relationship with any

//Find node with most incoming relations 

//Send data in json

class SearchHelper {
	public $id ; //ID of person making query
	public $query;
	public $nodeList = array();
	public $scorebyId = array();
	public $nodeListsPerEndNode = array();
	const THRESHOLD = 5;
	const MAX_DEPTH = 6; 

	function __construct($email, $query){
		//Get Id from email
		$this->id = Person::findByEmail($email);
		#echo "ID = ".$this->id. " for eamil=".$email;
		$this->query = $query;
		//break query into searchable terms
		//TODO
		
		
		$this->nodeList[] = $this->queryNodes();
		#echo print_r($this->nodeList, 1);
		 

			

		//if type is person return the one with most outgoing connectios. (Not very good method. We should use history of how many times user has been searched) 
	}


    public function getResults(){
    	$client = new Everyman\Neo4j\Client();
    	// Find path between person and nodes.
    	//if(count($this->nodeList) <= self::THRESHOLD) {
    		$person = $client->getNode($this->id);
    		//echo print_r($this->nodeList,1);
    		$maxScore = -10000;
    		$maxIndex = 0;
    		$nodesInMax = array();
    		$endNodesInMaxPath = array();
    		foreach ($this->nodeList[0] as $index => $endNode) {
    			# code...
    			#echo print_r($endNode,1);
    			//echo print_r($this->nodeList[$index],1);
    			$this->scorebyId[$endNode->getId()] = 0; 
    			$score = 0;
    			$nodesInPath = array();
    			$paths = $person->findPathsTo($endNode)
    					->setMaxDepth(self::MAX_DEPTH)
    					->getPaths();
    			echo "<BR>".print_r(count($paths),1)."<BR>";
    			if(count($paths) == 0) {
    				return null;
    			}
    			//Very simplistic scoring. 100 * total_paths - sum of (each_path_depth * 50)
    			$score += 100 * count($paths);
    			foreach ($paths as $key => $path) {
    				# code...
    				$score -= 50*$path->getLength();
    				$nodes = $path->getNodes();
    				$nodeIn = $nodes[count($nodes) - 2];
    				echo $nodeIn->getProperty("name");
    				if(!in_array($nodeIn, $nodesInPath) && $nodeIn->getId() != $person->getId()){//Noone would want to search himself
    					$nodesInPath[] = $nodeIn;  //Store the person nodes related directly
    				}
    				#echo print_r($nodes[count($nodes) - 2],1);
    			}
    			echo $score;
    			$this->scorebyId[$endNode->getId()] = $score;
    			if($score > $maxScore){
    				$maxIndex = $index;
    				$maxScore = $score;
    				$nodesInMax = $nodesInPath;
    			}

    		}

    		$result =array();
    		$result['query'] = $this->query;
    		$resultNode = $this->nodeList[0][$maxIndex];
    		#echo print_r($resultNode,1);
    		$result['type'] = $resultNode->getProperty('type');
    		$result['name'] = $resultNode->getProperty('name');
    		if($result['type'] == "person") { //if search was on email id and is found retun the person data
    			$result['searchResults'] = array();
    			$result['searchResults'][0] = $this->getDataForPerson($resultNode);
    		}
    		else {
    			$result['searchResults'] = array();
    			foreach ($nodesInMax as $index => $node) {
    				# code...
    				$result['searchResults'][$index] = $this->getDataForPerson($node);
    			}
    			$relType = $result['searchResults'][$index][0]['type'];
    			//$relationships = $node->getRelationships(array('WENT_TO',"SCHOOL","LIVES_IN", "BELONGS_TO", "WORKS_AT"),Relationships::DirectionIn);
    		}
    		return $result;

    		
    	//}
    }

    private function getDataForPerson($node) {
    	$relationships = $node->getRelationships(array('WENT_TO',"SCHOOL","LIVES_IN", "BELONGS_TO", "WORKS_AT","IS_NAMED"),Relationship::DirectionOut);
    	$result = array();
    	foreach ($relationships as $rel) {
    		# code...
    		$end = $rel->getEndNode();
    		$type = $rel->getType();
    		$result[$type] = $end->getProperty('name');
    	}
    	return $result;
    }

	private function queryNodes() {
		$client = new Everyman\Neo4j\Client();
		$index = new Everyman\Neo4j\Index\NodeIndex($client, "names");
		return $index->query("name:*".$this->query."*");
	}
}

?>