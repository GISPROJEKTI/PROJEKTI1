<?php

$json = array();
foreach ($markers as $id => $name) {
    $json[] = array(
        'id' => $id,
        'name' => $name
    );
}

echo json_encode($json);