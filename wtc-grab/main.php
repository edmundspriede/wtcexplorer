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
    array('timestamp', ''),
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

  $SQL_S_LAST = 'select coalesce(max(d_number), -1) as m from blocks';

  $SQL_I_BLOCK = 'insert into blocks (block_number, %s) values (?, %s)';

  $SQL_I_TX = 'insert into transactions (parent_block_number, number_in_block, %s) values (?, ?, %s)';

  //===============================================================================================

  FixCLInput(); //Fix params from command line

  function Write($str) {
    echo $str."<br />";
    //Add save to log file
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
    global $BLK_NAMES, $SQL_I_BLOCK;
    //-----------------------
    //echo 'BLK - '; print_r($block); //return;
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
    //Write($sql); print_r($data); die;
    myExeSql($sql, $data);
  }

  function SaveTx($n, $m, $tx) {
    global $TX_NAMES, $SQL_I_TX;
    //-----------------------
    //echo 'TX - '; print_r($tx); //return;
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
    //Write($sql); print_r($data); die;
    myExeSql($sql, $data);
  }


  //===============================================================================================

  function GetLastBlockDBNum() {
    global $SQL_S_LAST;
    //-----------------------
    //return intval(myGetValue($SQL_S_LAST, 'm', -1));
    return 0;
  }

  function GetLastBlockWLTNum() {
    global $WLT;
    //-----------------------
    return DecodeHex($WLT->eth_blockNumber());
  }

  //===============================================================================================

  function GrabBlock($n) {
    global $WLT;
    //-----------------------
    $num = Dec2Hex($n);
    //-----------------------
    try {
		  $block = $WLT->eth_getBlockByNumber($num);
      SaveBlock($n, (array)$block);
    } catch (Exception $e) {
      Write('Block '.$n.' error: ',  $e->getMessage());
    }
    //-----------------------
		$txc = intval(DecodeHex($WLT->eth_getBlockTransactionCountByNumber($num)));
    if ($txc > 0) {
		  for ($q = 0; $q < $txc; $q++) {
        try {
		      $tx = $WLT->eth_getTransactionByBlockNumberAndIndex($num, Dec2Hex($q));
          SaveTx($n, $q, (array)$tx);
        } catch (Exception $e) {
          Write('Transaction '.$n.'-'.$q.' error: ',  $e->getMessage());
        }
	    }
    }
    //-----------------------
		/*
		$uncleCountByHash = $WLT->eth_getUncleCountByBlockHash($block->hash);
		$uncleCountByNum = $WLT->eth_getUncleCountByBlockNumber($block->number);
		assertIsHex($uncleCountByHash);
		assertIsHex($uncleCountByNum);
		assertEqual($uncleCountByHash, $uncleCountByNum);
		assertEqual($uncleCountByHash, '0x'.dechex(count($block->uncles)));
    $unc = DecodeHex($uncleCountByNum);
    Write($unc);
    if ($unc > 0) {
		  $block_unc = array();
		  for ($q = 0; $q < $unc; $q++) {
		    $block_unc[] = $WLT->eth_getUncleByBlockNumberAndIndex($num, Dec2Hex($q));
	    }
	    print_r($block_unc);// die;
    }
    */
    //-----------------------
    Write('Block: '.$num.' - OK');
  }



  //===============================================================================================

  function Sync() {
    global $WLT;
    //-----------------------
    $latest_db = GetLastBlockDBNum();
    $latest = GetLastBlockWLTNum();
    //-----------------------
    if (($latest - $latest_db) > 0) {
      for ($q = $latest_db + 1; $q <= $latest; $q++) {
        Write('Working on block: '.strval($q));
        GrabBlock($q);
      }
    }
    //-----------------------
    Write('Finished');
  }

  //===============================================================================================

  
  Write('Starting sync...');
  myConnect();
  WltConn();

  //===============================================================================================

  Sync();

  //===============================================================================================

  WltClose();
  myClose();

?>