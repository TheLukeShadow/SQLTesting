<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//read body we provided via unreal (json with Unique net id and Score)
$json = file_get_contents('php://input');
$data = json_decode($json);
echo $data;
$mysqli = new mysqli("sql206.infinityfree.com","if0_35569527","2Ms4F5oYsHB","if0_35569527_HighscoreTest");

if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

if($data->Action == "SetAchievement")
{
	$PlayerId = $data->PlayerId;
	$AchievementId = $data->AchievementId;
	//$result = $mysqli->query("SELECT * FROM `Achievements` WHERE (`PlayerId` = ".$PlayerId." AND `AchievementId` = ".$AchievementId);
	$stmt = $mysqli->prepare("SELECT COUNT(*) FROM `Achievements` WHERE (`PlayerId` = ? AND `AchievementId` = ?");
	$stmt->bind_param("ss", $PlayerId, $AchievementId);
	$result = $stmt->execute();
	if($result)
	{
		$stmt->bind_result($cnt);
    	$stmt->fetch();
    	if ($cnt == 0) {
			$stmt2 = $mysqli->prepare("INSERT INTO Achievements (PlayerId, AchievementId) VALUES (?, ?)");
			$stmt2->bind_param("ss", $PlayerId, $AchievementId);
			$stmt2->execute();
			$stmt2->close();
		}
		echo '{ "Success": true }';
	}
	$stmt->close();
	$mysqli->close();	
}
elseif($data->Action == "ReadAchievements")
{
	$PlayerId = $data->PlayerId;
	$stmt = $mysqli->prepare("SELECT AchievementId FROM `Achievements` WHERE (`PlayerId` = ?");
	$stmt->bind_param("s", $PlayerId);
	$result = $stmt->execute();
	$myArray = array();
	while($row = $result->fetch_assoc()) {
		$myArray[] = $row;
	}
	echo '{ "OwnedAchievements": '.json_encode($myArray),' }';
	$mysqli->close();
	$stmt->close();
}
elseif($data->Action == "DeleteAchievements")
{
	$PlayerId = $data->PlayerId;
	$stmt = $mysqli->prepare("DELETE FROM `Achievements` WHERE (`PlayerId` = ? AND `AchievementId` = ?");
	$stmt->bind_param("ss", $PlayerId, $AchievementId);
	$result = $stmt->execute();
	$myArray = array();
	while($row = $result->fetch_assoc()) {
		$myArray[] = $row;
	}
	echo '{ "OwnedAchievements": '.json_encode($myArray),' }';
	$mysqli->close();
	$stmt->close();
}

// 
?>