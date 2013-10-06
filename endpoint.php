<?php
require 'config.php';
require 'habitrpg_lib.php';

$hrpg = new HabitRPG($hUserId,$hToken);

$tasks = $hrpg->userTasks('habit');
$tasks = $tasks['habitRPGData'];

foreach ($tasks as $task) {
	if($task['text'] === $activityName) {
		$fitbitTask = $task;
		break;
	}
}

if(!isset($fitbitTask)) {
	$newTask['type'] = "habit";
	$newTask['title'] = $activityName;
	$newTask['text'] = $activityName;
	$newTask['note'] = $activityName;

	print_r($hrpg->newTask($newTask));

	//ugly code - copypasta
	$tasks = $hrpg->userTasks('habit');
	$tasks = $tasks['habitRPGData'];

	foreach ($tasks as $task) {
		if($task['text'] === $activityName) {
			$fitbitTask = $task;
			break;
		}
	}
	//end of copypasta (it's 2am, so..)
}


	
	$scoringParams['taskId'] = $fitbitTask['id'];
	$scoringParams['direction'] = "up";
	
	print_r($hrpg->taskScoring($scoringParams));
