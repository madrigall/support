<?php
define('SUPPORT_SYSTEM', TRUE);


require_once "../../config.php";
require_once "../functions.php";



$isTickets = (string) $_POST["tickets"];
$weeks = (int) $_POST["weeks"];
$date = array();
$c = array();
$v = array();

if($isTickets && $weeks)
{
	$dt = new DateTime("-2 weeks");
	$time = $dt->format("Y-m-d");

	global $db;

	$sql = "SELECT time, ticket_id FROM support_user_tickets WHERE time  > :time ORDER BY time DESC";
	$stmt = $db->prepare($sql);

	$stmt->bindValue(':time', $time);
    $stmt->execute();

   	$weeks *= 7;

   	for($i = $weeks; $i >= 0; --$i)
   	{
   		$d = new DateTime("-" . $i . " days");
   		$t = $d->format("d.m");

   		array_push($date, $t);

   	}

   	while($row = $stmt->fetch())
   	{
   		$dt_row = new DateTime($row["time"]);
   		$time_row = $dt_row->format("d.m");

   		$c[$time_row]++;
   	}


   	for($i = 0; $i < count($date); ++$i)
   	{
   		if(!isset($c[$date[$i]]))
   			array_push($v, 0);
   		else
   			array_push($v, $c[$date[$i]]);
   	}

   	echo '{"time" : ' . json_encode($date) . ', "count" : ' . json_encode($v) . '}';
}
else
	echo '{"error" : "error"}';
?>