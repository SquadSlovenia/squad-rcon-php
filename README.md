# squad-rcon-php
RCON client for Squad dedicated server

## USAGE

### Create instance
$server = new SquadServer('server.squad-slovenia.com', 21114, 'verySecretPassword');

### Get currently active players
$players = $server->currentPlayers();

### Get next and current map
$maps = $server->currentMaps();

### Broadcast message to all players on the server
$server->broadcastMessage('Hello from the other side');

### Change map (end current game)
$server->changeMap('Sumari AAS v1');

### Set next map
$server->nextMap('Gorodok');
