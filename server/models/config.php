<?php
require_once(dirname(__FILE__) . "/Path.php");
require_once(Path::models() . "class.Database.php");
require_once(Path::models() . "class.Location.php");
require_once(Path::models() . "class.InvolvementKind.php");
require_once(Path::models() . "class.ReportKind.php");
require_once(Path::models() . "class.Report.php");
require_once(Path::models() . "class.Person.php");
require_once(Path::models() . "class.Statuses.php");
require_once(Path::models() . "class.Comment.php");
require_once(Path::models() . "class.reportComment.php");
require_once(Path::models() . "class.Departments.php");
require_once(Path::models() . "class.Building.php");
require_once(Path::models() . "class.PersonKind.php");
require_once(Path::models() . "Funcs.php");

date_default_timezone_set('America/New_York');

GLOBAL $errors;
GLOBAL $successes;

$errors = array();
$successes = array();

?>