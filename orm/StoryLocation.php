<?php

class StoryLocation
{
	private $id;
	private $description;
	private $sid;
	private $db;
	
	public static function create($description, $sid, $db) {
		$result = $db->query('SELECT MAX(LID) FROM StoryLocation;');
		$id = $result->fetch_array();
		$id = $id['MAX(LID)'];
		if ($id == null){$id = 0;}
		$id = $id + 1;
		$result = $db->query("insert into StoryLocation values (".$id.",'".$description . "'," . $sid . ");");
		return new StoryLocation($description, $sid, $id, $db);
	}

	public static function getByID($id,$db) {
		$result = $db->query("select * from StoryLocation where LID = " . $id . ";");
		if ($result) {
			if ($result->num_rows == 0){
				return null;
			}
			$transaction_info = $result->fetch_array();
			$result = new StoryLocation($transaction_info['Description'],
					       $transaction_info['SID'],$id,$db);
			return $result;
		}
		return null;
	}
	
	
	public static function getBySid($db,$sid){
		$result = $db->query('SELECT LID FROM StoryLocation WHERE SID = ' . $sid . ';');
		$array = array();
		while($row = $db->fetch()) { 
			array_push($array,$row['LID']);
		}
		return $array;
	}
	
	
	

	private function __construct($description, $sid, $id, $db) {
		$this->id = $id;
		$this->sid = $sid;
		$this->description = $description;
		$this->db = $db;
	}

		
	

	
	
	public function getID() {
		return $this->id;
	}

	//TODO
	public function delete(){
	$this->db->query("delete from Options where CurrentPath = " . $this->id . " or DestinationPath = " . $this->id . ";");
		$this->db->query("delete from StoryLocation where LID = " . $this->id . ";");
		
		unset($this);
	}
	
	public function getOptions() {
		
		$this->db->query("select OID from StoryLocation where StoryLocation.LID = " . $this->id . ";");
		
		$array = array();
		
		while($row = $result->fetch_array()) { 
			array_push($array,Option::create($row['CurrentPath'],$row['DestinationPath'],$row['Description']));
		}
		
		
		return $array;
		
	}
	
	
	public function getParents() {
		
		$this->db->query("select Options.CurrentPath,StoryLocation.Description from Options inner join StoryLocation
					on Options.DestinationPath = StoryLocation.LID and StoryLocation.LID = " . $this->id . ";");
		
		$array = array();
		
		while($row = $result->fetch_array()) { 
			array_push($array,StoryLocation::create($row['Description'],$row['CurrentPath']));
		}
		
		
		return $array;
		
	}
	
	
	public function getSid() {
		return $this->sid;
	}
	
	public function setSid($sid) {
		$this->sid = $sid;
		$this->db->query("update StoryLocation set SID = " . $sid . " where LID = " . $this->id . ";");
	}
	
	public function getDescription() {
		return $this->description;
	}
	

	public function setDescription($description) {
		
		$this->description = $description;
		$this->db->query("update StoryLocation set Description=\"" . $description . "\" where LID = " . $this->id . ";");
	}

	public function getJSON(){
		$arr = array('id' => $this->id, 'description' => $this->description, 'sid' => $this->sid);
		return  json_encode($arr);
	}
	
	

}