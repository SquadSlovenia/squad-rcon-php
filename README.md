# squad-rcon-php
RCON client for Squad dedicated server

# USAGE

# Create instance
$server = new SquadServer(SQUAD_SERVER_HOST, SQUAD_SERVER_PORT, SQUAD_SERVER_PASSWORD);

# Get currently active players
$players = $server->currentPlayers();

# Get current and next map
$maps = $server->currentMaps();

# Broadcast message to all players on the server
$server->broadcastMessage('Hello from the other side')

# Change map (end current game)
$server->changeMap('Sumari AAS v1')

# Set next map
$server->nextMap('Gorodok')
