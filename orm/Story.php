<?php

class Story
{
	private $id;
	private $initialLocation;
	private $title;
	private $db;

	public static function create($initialLocation, $db) {
		$result = $db->query('SELECT MAX(SID) FROM Stories;');
		$id = $result->fetch_array();
		$id = $id['MAX(SID)'];
		if ($id == null){$id = 0;}
		$id = $id + 1;
		$result = $db->query('Insert into Stories values('. $id . ','. $initialLocation .',"untitled");');
		return new Story($initialLocation, $id, $db);
	}




	public static function getAllStories($db){
		$result = $db->query('SELECT SID,Title FROM Stories;');
		
		$encode = array();

		while($row = mysqli_fetch_assoc($result)) {
  			 $encode[] = $row;
		}

		return json_encode($encode); 
		




		
	}









	
	public function getJSON(){
		$arr = array('id' => $this->id, 'initialLocation' => $this->initialLocation, 'title' => $this->title);
		return  json_encode($arr);
	}

	public static function getByID($id, $db) {
		$result = $db->query("select * from Stories where SID = " . $id . ";");
		if ($result) {
			if ($result->num_rows == 0){
				return null;
			}
			$transaction_info = $result->fetch_array();
			$result = new Story($transaction_info['InitialLocation'],
					       $transaction_info['SID'], $db);
			$result->setTitle($transaction_info['Title']);
			return $result;
		}
		return null;
	}

	private function __construct($initialLocation, $id, $db) {
		$this->id = $id;
		$this->initialLocation = $initialLocation;
		$this->db = $db;
	}

		
	

	
	
	public function getID() {
		return $this->id;
	}

	
	public function delete(){
		$this->db->query("delete from Options WHERE CurrentPath IN (select LID from StoryLocation where SID = " . $this->id . ");");
		$this->db->query("delete from StoryLocation where SID = " . $this->id . ";");
		$this->db->query("delete from Stories where SID = " . $this->id . ";");
		unset($this);
	}
	
	public function getInitialLocation() {
		
		return $this->initialLocation;
		
	}

	public function setInitialLocation($initialLocation) {
		
		$this->initialLocation = $initialLocation;
		$this->db->query("update Stories set InitialLocation=" . $initialLocation . " where SID = " . $this->id . ";");
	}

	
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
		$this->db->query("update Stories set Title='" . $title . "' where SID = " . $this->id . ";");
	}
	

}
