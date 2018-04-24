<?php

  
    


/* script to import blocks from walton RPC
 * 
 * 
 * 
 */

define ('CMS_PATH_SEP','\\');
define ('CMS_BASEPATH',		dirname(__FILE__) . CMS_PATH_SEP);
define ("CMS_VENDOR_PATH",	CMS_BASEPATH.'vendor'.CMS_PATH_SEP);


require './lib/tables.php';
require CMS_VENDOR_PATH.'ethereum-php-master/ethereum.php';
require CMS_BASEPATH."lib/d_data.php"; 
require CMS_BASEPATH."lib/common_functions.php"; 

set_time_limit ( 600 );
error_reporting (E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 0);

if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {
          define('IS_CMD_LINE',true);
     } else {
          define('IS_CMD_LINE',false);
     }


define ('CMS_DBSERVER', '127.0.0.1');
define ('CMS_DBASE', 'wtc');
define ('CMS_DBUSER', 'root');
define ('CMS_DBPASS', '');


mysql_connect(CMS_DBSERVER,CMS_DBUSER,CMS_DBPASS) or halt_error(mysql_error());
mysql_select_db(CMS_DBASE) or halt_error(mysql_error());
mysql_query("SET NAMES utf8");



sync();

function sync() {
    
    
    $latest_db =  latest_db_block($aRes);
    
  
    $walton = new Ethereum('192.168.10.212', 8545);
    $latest = $walton->eth_blockNumber();
    $latest = decode_hex_($latest);
    
    if (($latest - $latest_db) > 0  ) {
        
        $b = $latest_db + 1;

        for ($b  ; $b <= $latest ; $b++) {

            $block = $walton->eth_getBlockByNumber('0x'.dechex($b));

            $params['hash'] = $block->hash;
            $params['number'] = decode_hex_($block->number);
            $params['timestamp'] = decode_hex_($block->timestamp);
            $params['miner'] = $block->miner;
            $params['data'] = $block->extraData;
            $params['data_readable'] = hex2str($block->extraData, true);
            
            
            $coinbase = $walton->eth_getTransactionByBlockHashAndIndex($block->hash, '0x0');

            blocks('I' , $params , $aRes );

            echo "block : ". $b . 'OK'.'<br />';
        
    }   
        
    }
    
}  


function decode_hex_($input, $binary = false)
	{
		if(substr($input, 0, 2) == '0x')
			$input = substr($input, 2);
		
		if(preg_match('/[a-f0-9]+/', $input)) {
                    
                    if ($binary) {
                     
                        
                        
                        
                        return hex2bin(json_decode($input));
                        
                    }    
                    
                       else return hexdec($input);
                    
                }
			
			
	
	}  
        
        
function hex2str($hex) {
    $str = '';
    for($i=0;$i<strlen($hex);$i+=2) $str .= chr(hexdec(substr($hex,$i,2)));
    return $str;
}        
    
   


