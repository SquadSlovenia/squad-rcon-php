<?php
require_once('rcon.php');

class SquadServer {

  const SQUAD_SOCKET_TIMEOUT_SECONDS = 0.5;

  private $_rcon;

  public function __construct($host, $port, $password, $timeout = SquadServer::SQUAD_SOCKET_TIMEOUT_SECONDS) {
    $this->_rcon = new RCon($host, $port, $password, $timeout);
  }

  public function currentPlayers($ignored = array()) {
    $res = $this->_rcon->execute("ListPlayers");
    $ra = explode("\n", $res);
    $players = array();
    for($i=1; $i<count($ra); $i++) {
      $l = trim($ra[$i]);
      if(
        $l == '----- Recently Disconnected Players [Max of 15] -----'
      ) {
        break;
      }
      if(
        empty($l)
      ) {
        continue;
      }
      $pla = explode(' | ', $l);
      $pli = substr($pla[0], 4);
      $pls = substr($pla[1], 9);
      $pln = substr($pla[2], 6);
      if(!in_array($pls, $ignored)) {
        $players[] = array(
          'id' => $pli,
          'steam_id' => $pls,
          'name' => $pln,
        );
      }
    }
    return $players;
  }

  public function currentMaps() {
    $res = $this->_sendCommand("ShowNextMap");
    $arr = explode(', Next map is ', $res);
    if(count($arr) > 1) {
      $next = trim($arr[1]);
      $curr = substr($arr[0], strlen('Current map is '));
      $maps = array(
        'current' => $curr,
        'next' => $next
      );  
    }
    else {
      $maps = false;
    }
    return $maps;
  }

  public function broadcastMessage($msg) {
    return $this->_consoleCommand('AdminBroadcast', $msg, 'Message broadcasted');
  }

  public function changeMap($map) {
    return $this->_consoleCommand('AdminChangeMap', $map, 'Changed map to');
  }

  public function nextMap($map) {
    return $this->_consoleCommand('AdminSetNextMap', $map, 'Set next map to');
  }

  private function _consoleCommand($cmd, $param, $rtn) {
    $ret = $this->_sendCommand($cmd.' '.$param);
    if(substr($ret, 0, strlen($rtn)) == $rtn)
      return true;
    return false; 
  }

  private function _sendCommand($cmd) {
    $res = $this->_rcon->execute($cmd);
    //error_log('Command "$cmd" response: '.$res);
    return $res;
  }

  public static function toJson($val) {
    $date = date('Y-m-d H:i:s');
    $out = array();
    $out['datetime'] = $date;
    $out = array_merge($out, $val);
    header('Content-Type: application/json');
    echo json_encode($out);
    exit;
  }
}
