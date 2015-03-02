<?php

// TODO: OOP
//class Database{
//	
//	function __construct($database_name){
//		$connection = new MongoClient();
//		$dbname = $connection->selectDB($database_name);
//		$collection = $dbname->selectCollection('message');
//	}
//
//	static function data_input()
//}

function database($collection, $name, $id, $from_id, $from_name, $message, $created_time){
	$database_name = $name . "_" . rand();
    $connection = new MongoClient();
	$dbname = $connection->selectDB($database_name);
	$collection = $dbname->selectCollection($collection);
	$mesg = array(
        'mesg_id' => $id,
        'from_id' => $from_id,
        'from_name' => $from_name,
        'message' => $message,
        'created_time' => $created_time,
        'Date'  => new MongoDate() 
    );
    
    $collection->insert($mesg);
}