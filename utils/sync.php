<?php



/* script to import blocks from walton RPC
 * 
 * 
 * 
 */


require '../config.inc';
require(CMS_LIBPATH.CMS_SYSPATH."s_init.inc");
require CMS_LIBPATH.'tables.php';
require CMS_VENDOR_PATH.'ethereum-php-master/ethereum.php';




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
    
   


