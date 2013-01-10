<?php

$json = array('answers' => array());

foreach ($answers as $a) {
	$data = array(
		'answer' => $a['Answer']['answer'],
		'lat'	 => $a['Answer']['lat'],
		'lng'	 => $a['Answer']['lng']
	);
	$json['answers'][] = $data;
}

echo json_encode($json);

