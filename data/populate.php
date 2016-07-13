





<html>
  <head>
    <title>test</title>
  </head>
  <body>
    <?php
	
	
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

/*
class StoryLocation
{

	private $db;

	private function __construct($db) {
		$this->$db = $db;
	}
	public addStory($title,$initialLocation){
		$this->db->query("insert into Stories values (". $initialLocation . "," . $title . ");");
	}
	
	public addOption($startPoint, $destination, $description){
		$this->db->query("insert into Options values (" . 
			                  $startPoint . ", " . $destination . ", " . $description . ");");
	}
	
	public function addStoryLocation($description, $sid) {
		$this->db->query("insert into StoryLocation values (\"". $description . "\"," . $sid . ");");
	}

}
*/
$json_string = file_get_contents('https://writer.inklestudios.com/stories/musgraveritual.json');
$data = json_decode($json_string, true);

$db = new mysqli('classroom.cs.unc.edu','stevengt','forzach','stevengtdb');

echo $json_string;

?>
  </body>
 </html>