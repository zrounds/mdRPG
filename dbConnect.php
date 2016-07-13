<?php
require_once('dbObject.php');
$db = new MysqlAdapter(array('classroom.cs.unc.edu','stevengt','forzach','stevengtdb'));
$db->connect();
?>