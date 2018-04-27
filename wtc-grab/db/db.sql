CREATE TABLE `blocks` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `block_number` INTEGER(11) DEFAULT NULL,
  `d_number` INTEGER(11) DEFAULT NULL,
  `d_extraData` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `d_timestamp` INTEGER(11) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_number` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_difficulty` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_extraData` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_gasLimit` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_gasUsed` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_hash` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_logsBloom` VARCHAR(1024) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_miner` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_mixHash` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_nonce` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_parentHash` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_receiptsRoot` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_sha3Uncles` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_size` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_stateRoot` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_timestamp` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_totalDifficulty` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `blk_transactionsRoot` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  PRIMARY KEY USING BTREE (`id`)
) ENGINE=InnoDB
AUTO_INCREMENT=4773 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_latvian_ci'
;


CREATE TABLE `transactions` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `parent_block_number` INTEGER(11) DEFAULT NULL,
  `d_blockNumber` INTEGER(11) DEFAULT NULL,
  `d_transactionIndex` INTEGER(11) DEFAULT NULL,
  `tx_blockHash` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_blockNumber` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_from` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_gas` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_gasPrice` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_hash` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_input` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_nonce` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_to` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_transactionIndex` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_value` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_v` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_r` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `tx_s` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
  `number_in_block` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY USING BTREE (`id`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_latvian_ci'
;