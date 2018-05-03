<?php

  require_once (realpath(__DIR__).'/config.php');

  //===============================================================================================

  require_once (realpath(__DIR__).'/lib/utils.php');
  require_once (realpath(__DIR__).'/lib/my_utils.php');

  //===============================================================================================

  require_once (realpath(__DIR__).'/lib/ethereum.php');

  //===============================================================================================
  //Field name, blk_xxxxxx, decoded by type d_xxxxxxx

  $BLK_NAMES = array( //int -> to integer, str -> to filtered string
    array('number', 'int'),
    array('extraData', 'str'),
    array('difficulty', ''),
    array('gasLimit', ''),
    array('gasUsed', ''),
    array('hash', ''),
    array('logsBloom', ''),
    array('miner', ''),
    array('mixHash', ''),
    array('nonce', ''),
    array('parentHash', ''),
    array('receiptsRoot', ''),
    array('sha3Uncles', ''),
    array('size', ''),
    array('stateRoot', ''),
    array('timestamp', 'int'),
    array('totalDifficulty', ''),
    array('transactionsRoot', '')
  );

  //Field name, tx_xxxxxx, decoded by type d_xxxxxxx
  $TX_NAMES = array( //int -> to integer, str -> to filtered string
    array('blockHash', ''),
    array('blockNumber', 'int'),
    array('from', ''),
    array('gas', ''),
    array('gasPrice', ''),
    array('hash', ''),
    array('input', ''),
    array('nonce', ''),
    array('to', ''),
    array('transactionIndex', 'int'),
    array('value', ''),
    array('v', ''),
    array('r', ''),
    array('s', '')
  );

  $SQL_S_LAST = 'select coalesce(max(d_number), -1) as m from wtc_blocks';

  $SQL_I_BLOCK = 'insert into wtc_blocks (block_number, %s) values (?, %s)';

  $SQL_I_TX = 'insert into wtc_transactions (block_number, number_in_block, %s) values (?, ?, %s)';

  $SQL_D_BLOCK = 'delete from wtc_blocks where block_number = %d';

  $SQL_D_TX = 'delete from wtc_transactions where block_number = %d';

  //===============================================================================================

  $log_file = null;

  //===============================================================================================

  FixCLInput(); //Fix params from command line

  function Write($str = '') {
    global $OUTPUT_TO_LOG, $LOG_FILE_NAME, $log_file;
    //-----------------------
    if (!isset($str)) $str = '';
    if ($str != '') $timestamp = date('Y.m.d H:i:s').': '; else $timestamp = '';
    //-----------------------
    if ($OUTPUT_TO_LOG) {
      if ($log_file == null) $log_file = fopen($LOG_FILE_NAME, "a");
      fwrite($log_file, $timestamp.$str."\n");
    } else echo $timestamp.$str."\n";
  }

  function CloseWrite() {
    global $log_file;
    //-----------------------
    if ($log_file != null) fclose($log_file);
  }

  //===============================================================================================

  $WLT = null;

  //===============================================================================================

  function WltConn() {
    global $WLT, $WLT_HOST, $WLT_PORT;
    //-----------------------
    $WLT = new Ethereum($WLT_HOST, $WLT_PORT);
    if ($WLT == false) {
      Write('Error connecting to blockchain server...');
      die;
    }
  }

  function WltClose() {
    global $WLT;
    //-----------------------
    unset($WLT);
    $WLT = null;
  }

  //===============================================================================================
  /*
  function AssertEqual($a, $b) { if($a !== $b) { trigger_error("$a !== $b", E_USER_ERROR); } }
	function AssertNotEqual($a, $b)	{ if($a === $b) { trigger_error("$a === $b", E_USER_ERROR);	}	}
	function AssertIsA($a, $type)	{ if(!is_a($a, $type)) { trigger_error("Object is not $type", E_USER_ERROR); } }
	function AssertIsNumeric($a) { if(!is_numeric($a)) { trigger_error("$a is not numeric", E_USER_ERROR); } }
	function AssertMatch($a, $pattern) { if(!preg_match($pattern, $a)) { trigger_error("$a does not match pattern '$pattern'", E_USER_ERROR);	} }
	function AssertLength($a, $len) { if(strlen($a) !== $len) { trigger_error("$a is not $len characters long", E_USER_ERROR); } }
	function AssertIsHex($a) { if(!preg_match('/[0-9a-fx]+/', $a)) { trigger_error("$a is not hex", E_USER_ERROR); } }
	function AssertIsBoolean($a) {if(!is_bool($a)) { trigger_error("Object is not boolean", E_USER_ERROR); } }
	function AssertIsArray($a) { if(!is_array($a)) { trigger_error("Object is not an array", E_USER_ERROR); } }
	function AssertError($message) { trigger_error($message, E_USER_ERROR);	}
  */
  //===============================================================================================

  function Trim0x(&$str) {
    if (substr($str, 0, 2) == '0x') return substr($str, 2);
    return $str;
  }

  function DecodeHex($input, $binary = false)	{
		$input = Trim0x($input); //if(substr($input, 0, 2) == '0x') $input = substr($input, 2);
		if(preg_match('/[a-f0-9]+/', $input)) {
      if ($binary) return intval(hex2bin(json_decode($input))); else return intval(hexdec($input));
    }
	}

  function Hex2Str($hex) {
    $str = '';
    for($i = 0; $i < strlen($hex); $i += 2) $str .= chr(hexdec(substr($hex, $i, 2)));
    return $str;
  }

  function Dec2Hex($dec) {
    return '0x'.dechex($dec);
  }

  //===============================================================================================

  function SaveBlock($n, $block) {
    global $BLK_NAMES, $SQL_I_BLOCK, $LOG_BLK_DATA;
    //-----------------------
    if ($LOG_BLK_DATA) {
      Write('Saving block: '.$n);
      Write(print_r($block, true));
    }
    //-----------------------
    $fields = array();
    $params = array();
    $data = array();
    //-----------------------
    $data[] = intval($n); //Save requested block_number
    //-----------------------
    foreach ($BLK_NAMES as $n => &$tmp) {
      $name = $tmp[0];
      $type = $tmp[1];
      $value = $block[$name];
      //---------------------
      $fields[] = 'blk_'.$name;
      $params[] = '?';
      $data[] = Trim0x($value);
      //---------------------
      switch ($type) {
        case 'int': {
          $d_value = DecodeHex($value); //Decode to integer
          $fields[] = 'd_'.$name;
          $params[] = '?';
          $data[] = $d_value;
        } break;
        case 'str': {
          $d_value = ldTrimSL(Hex2Str($value)); //Decode to string and trim invalid stuff
          $fields[] = 'd_'.$name;
          $params[] = '?';
          $data[] = $d_value;
        } break;
        //default:
      }
    }
    //-----------------------
    $sql = sprintf($SQL_I_BLOCK, implode(', ', $fields), implode(', ', $params));
    myExeSql($sql, $data);
  }

  function SaveTx($n, $m, $tx) {
    global $TX_NAMES, $SQL_I_TX, $LOG_BLK_DATA;
    //-----------------------
    if ($LOG_BLK_DATA) {
      Write('Saving block: '.$n. ', transaction in block: '.$m);
      Write(print_r($tx, true));
    }
    //-----------------------
    $fields = array();
    $params = array();
    $data = array();
    //-----------------------
    $data[] = intval($n); //Save requested parent_block_number
    $data[] = intval($m); //Save requested number_in_block
    //-----------------------
    foreach ($TX_NAMES as $n => &$tmp) {
      $name = $tmp[0];
      $type = $tmp[1];
      $value = $tx[$name];
      //---------------------
      $fields[] = 'tx_'.$name;
      $params[] = '?';
      $data[] = Trim0x($value);
      //---------------------
      switch ($type) {
        case 'int': {
          $d_value = DecodeHex($value); //Decode to integer
          $fields[] = 'd_'.$name;
          $params[] = '?';
          $data[] = $d_value;
        } break;
        case 'str': {
          $d_value = ldTrimSL(Hex2Str($value)); //Decode to string and trim invalid stuff
          $fields[] = 'd_'.$name;
          $params[] = '?';
          $data[] = $d_value;
        } break;
        //default:
      }
    }
    //-----------------------
    $sql = sprintf($SQL_I_TX, implode(', ', $fields), implode(', ', $params));
    myExeSql($sql, $data);
  }

  //===============================================================================================

  function GrabBlock($n) {
    global $WLT, $SQL_D_BLOCK, $SQL_D_TX;
    //-----------------------
    $num = Dec2Hex($n);
    $err = false;
    //-----------------------
    //Delete old data if exists
    try {
      myExeSql(sprintf($SQL_D_BLOCK, intval($n)));
      myExeSql(sprintf($SQL_D_TX, intval($n)));
    } catch (Exception $e) {
      Write('Block '.$n.' error: ',  $e->getMessage());
      $err = true;
    }
    //-----------------------
    //Add block
    try {
		  $block = $WLT->eth_getBlockByNumber($num);
      SaveBlock($n, (array)$block);
    } catch (Exception $e) {
      Write('Block '.$n.' error: ',  $e->getMessage());
      $err = true;
    }
    //-----------------------
    //Add transactions
		$txc = intval(DecodeHex($WLT->eth_getBlockTransactionCountByNumber($num)));
    if ($txc > 0) {
		  for ($q = 0; $q < $txc; $q++) {
        try {
		      $tx = $WLT->eth_getTransactionByBlockNumberAndIndex($num, Dec2Hex($q));
          SaveTx($n, $q, (array)$tx);
        } catch (Exception $e) {
          Write('Transaction '.$n.'-'.$q.' error: ',  $e->getMessage());
          $err = true;
        }
	    }
    }
    //-----------------------
    if ($err) Write('Block: '.$n.' ('.$num.') - Error'); else Write('Block: '.$n.' ('.$num.') - OK');
  }

  //===============================================================================================

  function GetLastBlockDBNum() {
    global $SQL_S_LAST;
    //-----------------------
    return intval(myGetValue($SQL_S_LAST, 'm', -1));
  }

  function GetLastBlockWLTNum() {
    global $WLT;
    //-----------------------
    return DecodeHex($WLT->eth_blockNumber());
  }

  //===============================================================================================

  function Sync($start = -1, $end = -1) {
    global $WLT;
    //-----------------------
    $latest_db = GetLastBlockDBNum();
    $latest_wlt = GetLastBlockWLTNum();
    //-----------------------
    Write('Last DB block: '.$latest_db);
    Write('Last WLT block: '.$latest_wlt);
    //-----------------------
    if ($start > -1) $from = $start; else $from = $latest_db + 1;
    if ($end > -1) $to = $end; else $to = $latest_wlt;
    //-----------------------
    Write('Grabing total bloks: '.strval($to - $from + 1));
    //-----------------------
    if ($from <= $to) {
      Write('Grabing from block: '.$from);
      Write('Grabing to block: '.$to);
      for ($q = $from; $q <= $to; $q++) {
        Write('Working on block: '.strval($q));
        GrabBlock($q);
      }
    } else Write('No blocks to grab.');
    //-----------------------
    Write('Finished');
  }

  //===============================================================================================

  Write();
  Write('=======================================================');
  Write('================== WTC GRAB STARTING ==================');
  Write('=======================================================');

  //===============================================================================================

  FixCLInput();

  $start = ldGetParamInt('start', -1); //Starting block
  $end = ldGetParamInt('end', -1); //Ending block

  if ($start < -1) $start = -1;
  if ($end < -1) $end = -1;
  if ($start > $end) $start = $end;

  Write('Param start='.$start);
  Write('Param end='.$end);

  //===============================================================================================

  myConnect();
  Write('DB found');

  WltConn();
  Write('WTC found');

  //===============================================================================================

  Sync($start, $end);

  //===============================================================================================

  WltClose();
  myClose();

  CloseWrite();

?>