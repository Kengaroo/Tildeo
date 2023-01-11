<?php

$projectsToAdd = [];
//generate array of persons, who created a new project
for ($i = 0; $i < 10; $i++) {
    $projectsToAdd[] = mt_rand(0,3);
}

include 'project.php';
$project = new Project();

echo '<table>
        <tr><th>#</th><th>Created by</th><th>Assigned to</th><th>Date</th></tr>';
$i = 1;

foreach ($projectsToAdd as $createdBy) {
    if ($manager = $project->addProject($createdBy)) {
        echo '<tr style="text-align:center"><td>' . $i . '</td><td>' . $createdBy . '</td><td>' . $manager . '</td><td>' . date('Y-m-d H:i:s', strtotime("+" . $i . " second")) . '</td></tr>';
        $i++;

        //added just for test, otherwise each line in DB has the same time created_at
        sleep(1);

    } else {
        '<tr><td colspan=4>No available manager</td><tr>';
    }
}
echo '</table>';
