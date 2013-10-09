<?php
include "phar://lib/neo4jphp.phar";
$client = new Everyman\Neo4j\Client();
print_r($client->getServerInfo());

?>