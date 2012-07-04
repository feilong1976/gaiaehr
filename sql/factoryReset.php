<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ernesto
 * Date: 7/4/12
 * Time: 2:23 PM
 * To change this template use File | Settings | File Templates.
 */
if(!isset($_SESSION)){
    session_name ("GaiaEHR");
    session_start();
    session_cache_limiter('private');
}

include_once($_SESSION['site']['root'].'/classes/dbHelper.php');

$db = new dbHelper();
$tables = array(
	'calendar_events'
);

function getDirectoryList ($directory)
{
  $results = array();
  $handler = opendir($directory);
  while ($file = readdir($handler)) {
    if ($file != "." && $file != "..") {
      $results[] = $file;
    }
  }
  closedir($handler);
  return $results;
}

function RemoveReclusiveDir($dir) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (filetype($dir."/".$object) == "dir") RemoveReclusiveDir($dir."/".$object); else unlink($dir."/".$object);
      }
    }
    reset($objects);
    rmdir($dir);
  }

	return true;
}


print '<h1>SQL Clean Up!</h1>';

foreach($tables as $table){
	$db->setSQL("TRUNCATE TABLE $table");
	$err = $db->execOnly();
	$msg = (isset($err[1])) ? 'FAIL!' : 'PASS!';
	print 'Empty  `' . $table . '`  ' . $msg . '<br>';
}

print '<h1>Patient Directories Clean Up!</h1>';
$path = '../sites/'.$_SESSION['site']['facility'].'/patients/';
$patientsDir = getDirectoryList($path);

foreach($patientsDir as $dir){
	print 'Removing directory `' . $path . $dir . ' ' . (RemoveReclusiveDir($path . $dir) ? '` PASS!' : 'FAIL!') . '<br>';
}
