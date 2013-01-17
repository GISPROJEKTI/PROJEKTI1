<?php

$json = array();
foreach ($paths as $id => $name) {
    $json[] = array(
        'id' => $id,
        'name' => $name
    );
}

echo json_encode($json);