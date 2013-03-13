<?php
$socket = fsockopen(sprintf("udp://%s", '127.0.0.1'), 8125);
$postdata = json_decode(file_get_contents("php://input"), true);
foreach($postdata as $m) {
	foreach ($m['dsnames'] as $key => $name) {
		$statName = '';
		if (count($m['dsnames']) > 1) {
			$statName = '.'.$name;
		}
		$metric = sprintf(
			'collectd.%s.%s.%s%s:%s|%s',
			$m['host'],
			stat_name($m['plugin'], $m['plugin_instance']),
			stat_name($m['type'], $m['type_instance']),
			$statName,
			$m['values'][$key], get_type($m['dstypes'][$key])
		);
		send($metric);
	}
}

function stat_name($stat, $instance) {
	if ($instance === '') {
		return $stat;
	}
	return sprintf('%s-%s', $stat, $instance);
}

function get_type($type) {
	switch($type) {
		case 'gauge': return 'g';
		case 'counter': return 'c';
		case 'derive': return 'c'; //Fix me
		case 'absolute': return 'g'; // Fix me here too
		default:
			error("Type '$type' Unknown");
	}
}

function error($message) {
	file_put_contents(
		'error.log', 
		sprintf(
			'[%s] %s'.PHP_EOL,
			date('Y-m-d H:i:s'), $message
		)
	);
}

function send($message) {
	global $socket;
	if (0 != strlen($message) && $socket) {
		fwrite($socket, $message);
	}
}
