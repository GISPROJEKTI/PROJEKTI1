<?php

$json = array();
foreach ($overlays as $id => $name) {
    $json[] = array(
        'id' => $id,
        'name' => $name
    );
}

echo json_encode($json);