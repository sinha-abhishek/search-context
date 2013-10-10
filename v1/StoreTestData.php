<?php
include "phar://lib/neo4jphp.phar";
include "Person.class.php";
include "Entity.class.php";
use Everyman\Neo4j\Relationship;
$client = new Everyman\Neo4j\Client();
//Read File
$data = file_get_contents("testdata");
$index = new Everyman\Neo4j\Index\NodeIndex($client, 'emails');
$index->save();
$perindex = new Everyman\Neo4j\Index\NodeIndex($client, 'personNames');
$perindex->save();
$insIndex = new Everyman\Neo4j\Index\NodeIndex($client, 'institutes');
echo $insIndex->save();
$nindex = new Everyman\Neo4j\Index\NodeIndex($client, 'names');
$nindex->save();
$pindex = new Everyman\Neo4j\Index\NodeIndex($client, 'places');
$pindex->save();
$cindex = new Everyman\Neo4j\Index\NodeIndex($client, 'companies');
$cindex->save();

//Convert JSON into oject
$obj = json_decode($data);
$arrObj = array($obj);
$test = array($arrObj[0]);
$tdata = array($test[0]);
$actualData = $tdata[0];

/*for($i = 10 ; $i <=5000; $i++){

	$earth = $client->getNode($i);
	if($earth != null) {
		$outgoing = $earth->getRelationships(array(), Relationship::DirectionOut);
		foreach ($outgoing as $relation) {
			echo print_r($relation,1);
			$relation->delete();
		}
		$incoming = $earth->getRelationships(array(), Relationship::DirectionIn);
		foreach ($incoming as $relation) {
			echo print_r($relation,1);
			$relation->delete();
		}
		$earth->delete();
	}
}*/
foreach ($actualData->testdata as $index => $person) {
	# code...
	$per = new Person($person->person->name, $person->person->email);
	$name = new Entity("personNames", $person->person->name);
	$per->relate($name,"IS_NAMED");
	$college = new Entity("institutes",$person->person->college);	
	$per->relate($college,"WENT_TO");
	$school = new Entity("institutes", $person->person->school);
	$per->relate($school,"SCHOOL");
	$city = new Entity("places", $person->person->city);
	$per->relate($city,"LIVES_IN");
	$hometown = new Entity("places",$person->person->hometown);
	$per->relate($hometown,"BELONGS_TO");
	$company = new Entity("companies", $person->person->company);
	$per->relate($company,"WORKS_AT");
	#echo print_r($person->person->friends,1);
	foreach ($person->person->friends as  $friend) {
		# code...
		echo "Search by ".$friend->email."<BR>";
		if(Person::exists($friend->email)) {
			
			$friendObj = new Person($friend->name, $friend->email);
			echo "Found ".$friendObj->id;
		}
		else {
			$friendObj = createFriend($friend);

		}
		$per->addFriend($friendObj);

	}

	
}

function createFriend($friend) {
	$per = new Person($friend->name, $friend->email);
	$name = new Entity("personNames", $friend->name);
	$per->relate($name,"IS_NAMED");
	$college = new Entity("institutes",$friend->college);	
	$per->relate($college,"WENT_TO");
	$school = new Entity("institutes", $friend->school);
	$per->relate($school,"SCHOOL");
	$city = new Entity("places", $friend->city);
	$per->relate($city,"LIVES_IN");
	$hometown = new Entity("places",$friend->hometown);
	$per->relate($hometown,"BELONGS_TO");
	$company = new Entity("companies", $friend->company);
	$per->relate($company,"WORKS_AT");
	return $per;
}
	//Call to create a node for person

	//Call to create nodes for each property like college, company etc.
	
	//relate each

	//For each friend
	//Create node recursively with relationships
	//add friendship relation in db

?>