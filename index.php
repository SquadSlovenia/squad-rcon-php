<?php
require_once 'lib/squadserver.php';

// Configuration parameters
define('SQUAD_SERVER_HOST', '');
define('SQUAD_SERVER_PASSWORD', '');
define('SQUAD_SERVER_PORT', 21113);

$server = new SquadServer(SQUAD_SERVER_HOST, SQUAD_SERVER_PORT, SQUAD_SERVER_PASSWORD);

// Get currently active players
$players = $server->currentPlayers();
/*
RETURNS:
array(
	array(
		'id' => 0,
		'steam_id' => '772321313123',
		'name' => 'John Doe'
	),
	...
)
*/

// Get current and next map
$maps = $server->currentMaps();
/*
RETURNS:
array(
	'current' => 'Sumari AAS v2',
	'next' => 'Kohat AAS v1'
)
*/

// Broadcast message to all players on the server, returns BOOLEAN
if($server->broadcastMessage('Hello from the other side')) {...}

// Change map, returns BOOLEAN
if($server->changeMap('Sumari AAS v1')) {...}

// Set next map, returns BOOLEAN
if($server->nextMap('Gorodok')) {...}
