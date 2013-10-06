<?php
/*
The MIT License (MIT)
Copyright (c) 2013 Igor Sawczuk
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

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
