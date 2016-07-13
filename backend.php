<?php
include('dbConnect.php'); //Creates $db interface object to our DB
include('orm/Option.php');
include('orm/Story.php');
include('orm/StoryLocation.php');
if ($_SERVER["REQUEST_METHOD"] == "GET"){
	if (isset($_REQUEST['getAllStories'])){
		$json = Story::getAllStories($db);
		$a = json_decode($json);
		print("<ul>");
		for($i = 0; $i < count($a); $i++){
			print("<li><a href='http://wwwp.cs.unc.edu/Courses/comp426-f14/stevengt/demo/action.html?id=".$a[$i]->SID."'>". $a[$i]->Title ."</a></li>");
		}
		print("</ul>");
		exit();
	}
	if(isset($_REQUEST['getAllOptions'])){
		if(isset($_REQUEST['LID'])){
			if (is_numeric($_REQUEST['LID'])){
				$lid = $_REQUEST['LID'];
				$allOptionIDS = Option::getByLocationId($db, $lid);
				$allOptions = array();
				foreach($allOptionIDS as $oid){
					array_push($allOptions, Option::getByID($oid, $db));
				}	
				header("Content-type: application/json");
				foreach($allOptions as $Option){
					print($Option->getJSON());
				}
				exit();
			} else {
				header("HTTP/1.0 400 Bad Request");
				print("Non-numeric LID provided for getAllOptions.");
				exit();
			}
		}
		header("HTTP/1.0 400 Bad Request");
		print("LID missing for getAllOptions.");
		exit();
	}
	if(isset($_SERVER['PATH_INFO'])){
		$path_parsed = explode('/', $_SERVER['PATH_INFO']); //$path_parsed[1] is the first extra element, [0] is always empty and defined
		if((count($path_parsed >= 2))&&($path_parsed[1] != "")){
			$id = intval($path_parsed[1]);
			if(isset($_REQUEST['ObjectType'])){
				if ($_REQUEST['ObjectType'] == "Story"){
					if(isset($_REQUEST['getStoryInfo'])){
						$allLocations = StoryLocation::getBySid($db, $id);
						$storyInfo = array();
						foreach($allLocations as $l){
							$locationOptions = array();
							$temp = Option::getByLocationId($db,$l);
							if ($temp != null){
								foreach($temp as $t){
									$Option = Option::getByID($t, $db);
									$locationOptions[$t] = $Option->getJSON();
								}
								$storyInfo[$l] = $locationOptions;
							}
						}
						header("Content-type: application/json");
						print(json_encode($storyInfo));
						exit();
					}
					$Story = Story::getById($id, $db);
					if($Story == null){
						header("HTTP/1.0 404 Not Found");
						print("No Story with id '".$id."' found.");
						exit();
					}
					if (isset($_REQUEST['delete'])){
						$Story->delete();
						header("Content-type: application/json");
						print(json_encode(true));
						exit();
					} else {
						header("Content-type: application/json");
						print($Story->getJSON());
						exit();
					}
				} else if ($_REQUEST['ObjectType'] == "Option"){
					$Option = Option::getById($id, $db);
					if($Option == null){
						header("HTTP/1.0 404 Not Found");
						print("No Option with id '".$id."' found.");
						exit();
					}
					if (isset($_REQUEST['delete'])){
						$Option->delete();
						header("Content-type: application/json");
						print(json_encode(true));
						exit();
					} else {
						header("Content-type: application/json");
						print($Option->getJSON());
						exit();
					}
				} else if ($_REQUEST['ObjectType'] == "StoryLocation"){
					$StoryLocation = StoryLocation::getById($id, $db);
					if($StoryLocation == null){
						header("HTTP/1.0 404 Not Found");
						print("No Location with id '".$id."' found.");
						exit();
					}
					if (isset($_REQUEST['delete'])){
						$StoryLocation->delete();
						header("Content-type: application/json");
						print(json_encode(true));
						exit();
					} else {
						header("Content-type: application/json");
						print($StoryLocation->getJSON());
						exit();
					}
				}
			} else {
				header("HTTP/1.0 400 Bad Request");
				print("Improper / missing object type request.");
				exit();
			}
		}
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_REQUEST['ObjectType'])){
		if ($_REQUEST['ObjectType'] == "Story"){
			if(isset($_REQUEST['create'])){
				if((isset($_REQUEST['initialLocation'])) && (isset($_REQUEST['title']))){
					if (is_string($_REQUEST['title']) && is_numeric($_REQUEST['initialLocation'])){
						//print("Escaped String: ".$db->escapeValue($_REQUEST['title']));
						//exit();
						$Story = Story::create($_REQUEST['initialLocation'], $db);
						$Story->setTitle($db->escapeValue($_REQUEST['title']));
						header("Content-type: application/json");
						print($Story->getJSON());
						exit();
					} else {
						header("HTTP/1.0 400 Bad Request");
						print("Incorrect datatype of either initialLocation or title provided for Story.");
						exit();
					}
				} else {
					header("HTTP/1.0 400 Bad Request");
					print("Either no initial location or title provided for Story.");
					exit();
				}
			}
			if (isset($_REQUEST['id'])){
				if(is_numeric($_REQUEST['id'])) {
					$id = $_REQUEST['id'];
					$Story = Story::getById($id, $db);
					if($Story == null){
						header("HTTP/1.0 404 Not Found");
						print("No Story with id '".$id."' found.");
						exit();
					} else if(isset($_REQUEST['update'])){
						if (isset($_REQUEST['title'])){
							if(is_string($_REQUEST['title'])){
								$Story->setTitle($db->escapeValue($_REQUEST['title']));
							}
						}
						if(isset($_REQUEST['initialLocation']) && is_numeric($_REQUEST['initialLocation'])){
							$Story->setInitialLocation($_REQUEST['initialLocation']);
						}
						header("Content-type: application/json");
						print($Story->getJSON());
						exit();
					}
				}  else {
					header("HTTP/1.0 400 Bad Request");
					print("Poorly formed request for Story; improper datatype for ID.");
					exit();
				}
			} else {
				header("HTTP/1.0 400 Bad Request");
				print("Poorly formed request for Story; no proper action specified for expected POST actions or missing ID.");
				exit();
			}
		} else if ($_REQUEST['ObjectType'] == "Option"){
			if(isset($_REQUEST['create'])){
				if((isset($_REQUEST['startPoint'])) && (isset($_REQUEST['destination'])) && isset($_REQUEST['description'])){
					if (is_numeric($_REQUEST['startPoint']) && is_numeric($_REQUEST['destination']) && is_string($_REQUEST['description'])){
						$Option = Option::create($_REQUEST['startPoint'],$_REQUEST['destination'],$db->escapeValue($_REQUEST['description']), $db);
						header("Content-type: application/json");
						print($Option->getJSON());
						exit();
					} else {
						header("HTTP/1.0 400 Bad Request");
						print("Incorrect datatype of some parameter for new Option.");
						exit();
					}
				} else {
					header("HTTP/1.0 400 Bad Request");
					print("Some parameter missing for new Option.");
					exit();
				}
			}
			if(isset($_REQUEST['id'])){
				if (is_numeric($_REQUEST['id'])) {
					$id = $_REQUEST['id'];
					$Option = Option::getById($id, $db);
					if($Option == null){
						header("HTTP/1.0 404 Not Found");
						print("No Story with id '".$id."' found.");
						exit();
					} else if(isset($_REQUEST['update'])){
						if (isset($_REQUEST['startPoint'])){
							$Option->setStartPoint($_REQUEST['startPoint']);
						}
						if(isset($_REQUEST['destination'])){
							$Option->setDestination($_REQUEST['destination']);
						}
						if(isset($_REQUEST['description'])){
							$Option->setDescription($_REQUEST['description']);
						}
						header("Content-type: application/json");
						print($Option->getJSON());
						exit();
					} 
				} else {
					header("HTTP/1.0 400 Bad Request");
					print("Poorly formed request for Option; improper datatype for ID.");
					exit();
				}
			} else {
				header("HTTP/1.0 400 Bad Request");
				print("Poorly formed request for Option; no proper action specified for expected POST actions or missing ID.");
				exit();
			}
		} else if ($_REQUEST['ObjectType'] == "StoryLocation"){
			if(isset($_REQUEST['create'])){
				if((isset($_REQUEST['description'])) && (isset($_REQUEST['sid']))){
					if (is_string($_REQUEST['description']) && is_numeric($_REQUEST['sid'])){
						$StoryLocation = StoryLocation::create($db->escapeValue($_REQUEST['description']),$_REQUEST['sid'], $db);
						header("Content-type: application/json");
						print($StoryLocation->getJSON());
						exit();
					} else {
						header("HTTP/1.0 400 Bad Request");
						print("Improper datatype for some parameter for new StoryLocation.");
						exit();	
					}
				} else {
					header("HTTP/1.0 400 Bad Request");
					print("Some parameter missing for new StoryLocation.");
					exit();
				}
			}
			if (isset($_REQUEST['id'])){
				if (is_numeric($_REQUEST['id'])){
					$id = $_REQUEST['id'];
					$StoryLocation = StoryLocation::getById($id, $db);
					if($StoryLocation == null){
						header("HTTP/1.0 404 Not Found");
						print("No StoryLocation with id '".$id."' found.");
						exit();
					} else if(isset($_REQUEST['update'])){
						if (isset($_REQUEST['sid'])){
							if (is_numeric($_REQUEST['sid'])){
								$StoryLocation->setSid($_REQUEST['sid']);
							}
						}
						if(isset($_REQUEST['description'])){
							$StoryLocation->setDescription($db->escapeValue($_REQUEST['description']));
						}
						header("Content-type: application/json");
						print($StoryLocation->getJSON());
						exit();
					}
				} else {
					header("HTTP/1.0 400 Bad Request");
					print("Non-numeric id provided for StoryLocation.");
					exit();
				}
			} else {
				header("HTTP/1.0 400 Bad Request");
				print("Poorly formed request for StoryLocation; missing ID.");
				exit();
			}
		} else {
			header("HTTP/1.0 400 Bad Request");
			print("Improper object type request.");
			exit();
		}
	} else {
		header("HTTP/1.0 400 Bad Request");
		print("Missing object type request.");
		exit();
	}
}
include('dbClose.php'); //Closes connection in $db
?>