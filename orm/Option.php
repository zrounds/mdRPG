<?php

class Option
{
	private $id;
	private $startPoint;
	private $destination;
	private $description;
	private $db;
	
	public static function create($startPoint, $destination, $description, $db) {
		$result = $db->query('SELECT MAX(OID) FROM Options;');
		$id = $result->fetch_array();
		$id = $id['MAX(OID)'];
		if ($id == null){$id = 0;}
		$id = $id + 1;
		$result = $db->query("insert into Options values (" . 
		                  $startPoint . ", " . $destination . ", '" . $description . "', ".$id.");");
		return new Option($startPoint, $destination, $description, $id, $db);
	}
	
	public static function getByLocationId($db,$locationId){
		
		$result = $db->query('SELECT OID FROM Options WHERE CurrentPath = ' . $locationId . ';');
		
		if($result){
		
		$array = array();
		
		while($row = $db->fetch()) { 
			array_push($array,$row['OID']);
		}
		
		
		return $array;
		}
		return null;
		
	}
	
	
	public function getJSON(){
		$arr = array('id' => $this->id, 'startPoint' => $this->startPoint, 'destination' => $this->destination,
					'description' => $this->description);
		return  json_encode($arr);
	}

	public static function getByID($id,$db) {
		$result = $db->query("select * from Options where OID = " . $id . ";");
		if ($result) {
			if ($result->num_rows == 0){
				return null;
			}
			$transaction_info = $result->fetch_array();
			return new Option($transaction_info['CurrentPath'],
					       $transaction_info['DestinationPath'],
					       $transaction_info['Description'],
						   $transaction_info['OID'],$db);
		}
		return null;
	}

	private function __construct($startPoint, $destination, $description, $id, $db) {
		$this->id = $id;
		$this->startPoint = $startPoint;
		$this->destination = $destination;
		$this->description = $description;
		$this->db = $db;
	}

		
	

	
	
	public function getID() {
		return $this->id;
	}

	
	public function delete(){
		$this->db->query("delete from Options where OID = " . $this->id . ";");
		unset($this);
	
	}
	
	public function getStartPoint() {
		
		$this->db->query("select LID from StoryLocation where StoryLocation.LID = " . $this->startPoint . ";");
		
		$transaction_info = $result->fetch_array();
		
		return StoryLocation::getById($transaction_info['LID']);
		
	}

	public function setStartPoint($startPoint) {
		
		$this->startPoint = $startPoint;
		$this->db->query("update Options set CurrentPath=" . $startPoint . " where OID = " . $this->id . ";");
	}

	public function getDestination() {
		$this->db->query("select LID from StoryLocation where StoryLocation.LID = " . $this->destination . ";");
		
		$transaction_info = $result->fetch_array();
		
		return StoryLocation::getById($transaction_info['LID']);
	}
	
	
	public function setDestination($destination) {
		
		$this->destination = $destination;
		$this->db->query("update Options set DestinationPath=" . $destination . " where OID = " . $this->id . ";");
	}

	
	
	
	
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
		$this->db->query("update Options set Description=\"" . $description . "\" where OID = " . $this->id . ";");
	}
	

}