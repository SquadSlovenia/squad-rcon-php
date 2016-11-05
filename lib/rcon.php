<?php
class RCon {

  const SERVERDATA_EXECCOMMAND = 2;
  const SERVERDATA_AUTH = 3;
  const SOCKET_TIMEOUT_SECONDS = 2.5;
    
  private $_password;
  private $_host;
  private $_port = 27015;
  private $_socket = null;
  private $_id = 0;
  private $_authd = false;

  public function __construct($host, $port, $password, $timeout = null) {
    $this->_password = $password;
    $this->_host = $host;
    $this->_port = $port;
    $this->_socket = @fsockopen($this->_host, $this->_port, $errno, $errstr, 30) or
          die("Unable to open socket: $errstr ($errno)\n");
    if(!$timeout) {
      $timeout = RCon::SOCKET_TIMEOUT_SECONDS;
    }
    $secs = intval($timeout);
    $milis = is_float($timeout) ? ($timeout-$secs)*1000000 : 0;
    stream_set_timeout($this->_socket, $secs, $milis);
  }

  private function _authenticate() {
    if(!$this->_authd) {
      $aid = $this->_write(RCon::SERVERDATA_AUTH, $this->_password);
      $ret = $this->_packetRead();
      if (isset($ret[1]['id']) && $ret[1]['id'] == -1) {
        die("Authentication Failure\n");
      }
      $this->_authd = true;
    }
  }

  private function _write($cmd, $s1='', $s2='') {
    $id = ++$this->_id;
    $data = pack("VV", $id, $cmd).$s1.chr(0).$s2.chr(0);
    $data = pack("V", strlen($data)).$data;
    fwrite($this->_socket, $data, strlen($data));
    return $id;
  }

  private function _packetRead() {
    $retarray = array();
    while ($size = @fread($this->_socket, 4)) {
      $size = unpack('V1Size', $size);
      if ($size["Size"] > 4096) {
        $packet = "\x00\x00\x00\x00\x00\x00\x00\x00".fread($this->_socket, 4096);
      } else {
        $packet = fread($this->_socket, $size["Size"]);
      }
      array_push($retarray, unpack("V1ID/V1Response/a*S1/a*S2", $packet));
    }
    return $retarray;
  }

  private function _read() {
    $packets = $this->_packetRead();
    foreach($packets as $pack) {
      if (isset($ret[$pack['ID']])) {
        $ret[$pack['ID']]['S1'] .= $pack['S1'];
        $ret[$pack['ID']]['S2'] .= $pack['S1'];
      } else {
        $ret[$pack['ID']] = array(
          'Response' => $pack['Response'],
          'S1' => $pack['S1'],
          'S2' => $pack['S2'],
        );
      }
    }
    if(isset($ret))
      return $ret;
  }

  private function _sendCommand($command, $sanitize = false) {
    $this->_authenticate();
    if($sanitize)
      $command = '"'.trim(str_replace(' ','" "', $command)).'"';
    $this->_write(RCon::SERVERDATA_EXECCOMMAND, $command, '');
  }

  public function execute($command) {
    $this->_sendCommand($command);
    $ret = $this->_read();
    return $ret[$this->_id]['S1'];
  }
}
