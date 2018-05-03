CREATE TABLE `wtc_blocks` (
  `blocks_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `block_number` INTEGER(11) DEFAULT NULL,
  `pay_out` TINYINT(1) NOT NULL DEFAULT 0,
  `d_number` INTEGER(11) DEFAULT NULL,
  `d_timestamp` INTEGER(11) DEFAULT NULL,
  `d_extraData` VARCHAR(256) COLLATE utf8_latvian_ci DEFAULT NULL,
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
  PRIMARY KEY USING BTREE (`blocks_id`),
  KEY `blocks_idx1` USING BTREE (`block_number`),
  KEY `blocks_idx2` USING BTREE (`blk_miner`),
  KEY `blocks_idx3` USING BTREE (`d_extraData`),
  KEY `blocks_idx4` USING BTREE (`pay_out`)
) ENGINE=InnoDB
AUTO_INCREMENT=11932 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_latvian_ci'
;

CREATE TABLE `wtc_transactions` (
  `transactions_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `block_number` INTEGER(11) DEFAULT NULL,
  `number_in_block` INTEGER(11) DEFAULT NULL,
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
  PRIMARY KEY USING BTREE (`transactions_id`),
  KEY `transactions_idx1` USING BTREE (`block_number`),
  KEY `transactions_idx2` USING BTREE (`number_in_block`)
) ENGINE=InnoDB
AUTO_INCREMENT=85 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_latvian_ci'
;

CREATE TABLE `wtc_pools` (
  `pools_id` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `name` VARCHAR(100) COLLATE utf8_latvian_ci DEFAULT NULL,
  `miner` VARCHAR(50) COLLATE utf8_latvian_ci DEFAULT NULL,
  `expression` VARCHAR(100) COLLATE utf8_latvian_ci DEFAULT NULL,
  `color` VARCHAR(10) COLLATE utf8_latvian_ci DEFAULT NULL,
  `comment` TEXT COLLATE utf8_latvian_ci,
  PRIMARY KEY USING BTREE (`pools_id`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_latvian_ci'
;


CREATE ALGORITHM=UNDEFINED DEFINER='wtc'@'%' SQL SECURITY DEFINER VIEW `blocks_total_v`
AS
select
  cast(`b`.`created` as date) AS `grab_date`,
  count(0) AS `block_count`
from
  `wtc_blocks` `b`
group by
  cast(`b`.`created` as date)
order by
  cast(`b`.`created` as date);


CREATE ALGORITHM=UNDEFINED DEFINER='wtc'@'%' SQL SECURITY DEFINER VIEW `pools_blk_v`
AS
select
  `p`.`pools_id` AS `pools_id`,
  `p`.`timestamp` AS `timestamp`,
  `p`.`name` AS `name`,
  `p`.`miner` AS `miner`,
  `p`.`expression` AS `expression`,
  `p`.`color` AS `color`,
  `p`.`comment` AS `comment`,
  `b`.`blocks_id` AS `blocks_id`,
  `b`.`created` AS `created`,
  `b`.`block_number` AS `block_number`,
  `b`.`pay_out` AS `pay_out`,
  `b`.`d_number` AS `d_number`,
  `b`.`d_extraData` AS `d_extraData`,
  `b`.`blk_number` AS `blk_number`,
  `b`.`blk_difficulty` AS `blk_difficulty`,
  `b`.`blk_extraData` AS `blk_extraData`,
  `b`.`blk_gasLimit` AS `blk_gasLimit`,
  `b`.`blk_gasUsed` AS `blk_gasUsed`,
  `b`.`blk_hash` AS `blk_hash`,
  `b`.`blk_logsBloom` AS `blk_logsBloom`,
  `b`.`blk_miner` AS `blk_miner`,
  `b`.`blk_mixHash` AS `blk_mixHash`,
  `b`.`blk_nonce` AS `blk_nonce`,
  `b`.`blk_parentHash` AS `blk_parentHash`,
  `b`.`blk_receiptsRoot` AS `blk_receiptsRoot`,
  `b`.`blk_sha3Uncles` AS `blk_sha3Uncles`,
  `b`.`blk_size` AS `blk_size`,
  `b`.`blk_stateRoot` AS `blk_stateRoot`,
  `b`.`blk_timestamp` AS `blk_timestamp`,
  `b`.`blk_totalDifficulty` AS `blk_totalDifficulty`,
  `b`.`blk_transactionsRoot` AS `blk_transactionsRoot`
from
  (`wtc_pools` `p`
  left join `wtc_blocks` `b` on (((cast(`p`.`miner` as char charset binary) = cast(`b`.`blk_miner` as char charset binary)) and (cast(`b`.`d_extraData` as
    char charset binary) like cast(`p`.`expression` as char charset binary)))))
order by
  `p`.`name`,
  `b`.`block_number`;


CREATE ALGORITHM=UNDEFINED DEFINER='wtc'@'%' SQL SECURITY DEFINER VIEW `pools_pay`
AS
select
  `p`.`pools_id` AS `pools_id`,
  `p`.`timestamp` AS `timestamp`,
  `p`.`name` AS `name`,
  `p`.`miner` AS `miner`,
  `p`.`expression` AS `expression`,
  `p`.`color` AS `color`,
  `p`.`comment` AS `comment`,
  `b`.`pay_out` AS `pay_out`,
  cast(`b`.`created` as date) AS `pay_day`,
  count(`b`.`blocks_id`) AS `blocks_count`
from
  (`wtc_pools` `p`
  left join `wtc_blocks` `b` on (((cast(`p`.`miner` as char charset binary) = cast(`b`.`blk_miner` as char charset binary)) and (cast(`b`.`d_extraData` as
    char charset binary) like cast(`p`.`expression` as char charset binary)))))
group by
  `p`.`pools_id`,
  `p`.`timestamp`,
  `p`.`name`,
  `p`.`miner`,
  `p`.`expression`,
  `p`.`color`,
  `p`.`comment`,
  `b`.`pay_out`,
  cast(`b`.`created` as date)
order by
  `p`.`name`;

CREATE ALGORITHM=UNDEFINED DEFINER='wtc'@'%' SQL SECURITY DEFINER VIEW `pools_pay_total_v`
AS
select
  `p`.`pools_id` AS `pools_id`,
  `p`.`timestamp` AS `timestamp`,
  `p`.`name` AS `name`,
  `p`.`miner` AS `miner`,
  `p`.`expression` AS `expression`,
  `p`.`color` AS `color`,
  `p`.`comment` AS `comment`,
  `b`.`pay_out` AS `pay_out`,
  count(`b`.`blocks_id`) AS `blocks_count`
from
  (`wtc_pools` `p`
  left join `wtc_blocks` `b` on (((cast(`p`.`miner` as char charset binary) = cast(`b`.`blk_miner` as char charset binary)) and (cast(`b`.`d_extraData` as
    char charset binary) like cast(`p`.`expression` as char charset binary)))))
group by
  `p`.`pools_id`,
  `p`.`timestamp`,
  `p`.`name`,
  `p`.`miner`,
  `p`.`expression`,
  `p`.`color`,
  `p`.`comment`,
  `b`.`pay_out`
order by
  `p`.`name`;


CREATE ALGORITHM=UNDEFINED DEFINER='wtc'@'%' SQL SECURITY DEFINER VIEW `pools_tx_v`
AS
select
  `p`.`pools_id` AS `pools_id`,
  `p`.`timestamp` AS `timestamp`,
  `p`.`name` AS `name`,
  `p`.`miner` AS `miner`,
  `p`.`expression` AS `expression`,
  `p`.`color` AS `color`,
  `p`.`comment` AS `comment`,
  `b`.`block_number` AS `block_number`,
  `b`.`pay_out` AS `pay_out`,
  `b`.`d_extraData` AS `d_extraData`,
  `b`.`blk_miner` AS `blk_miner`,
  `t`.`transactions_id` AS `transactions_id`,
  `t`.`created` AS `created`,
  `t`.`number_in_block` AS `number_in_block`,
  `t`.`d_blockNumber` AS `d_blockNumber`,
  `t`.`d_transactionIndex` AS `d_transactionIndex`,
  `t`.`tx_blockHash` AS `tx_blockHash`,
  `t`.`tx_blockNumber` AS `tx_blockNumber`,
  `t`.`tx_from` AS `tx_from`,
  `t`.`tx_gas` AS `tx_gas`,
  `t`.`tx_gasPrice` AS `tx_gasPrice`,
  `t`.`tx_hash` AS `tx_hash`,
  `t`.`tx_input` AS `tx_input`,
  `t`.`tx_nonce` AS `tx_nonce`,
  `t`.`tx_to` AS `tx_to`,
  `t`.`tx_transactionIndex` AS `tx_transactionIndex`,
  `t`.`tx_value` AS `tx_value`,
  `t`.`tx_v` AS `tx_v`,
  `t`.`tx_r` AS `tx_r`,
  `t`.`tx_s` AS `tx_s`
from
  ((`wtc_pools` `p`
  left join `wtc_blocks` `b` on (((cast(`p`.`miner` as char charset binary) = cast(`b`.`blk_miner` as char charset binary)) and (cast(`b`.`d_extraData` as
    char charset binary) like cast(`p`.`expression` as char charset binary)))))
  left join `wtc_transactions` `t` on ((`b`.`block_number` = `t`.`block_number`)))
order by
  `p`.`name`,
  `b`.`block_number`,
  `t`.`number_in_block`;


/* Data for the 'wtc_pools' table  (Records 1 - 4) */

INSERT INTO `wtc_pools` (`pools_id`, `timestamp`, `name`, `miner`, `expression`, `color`, `comment`) VALUES

  (1, '2018-04-29 12:12:24', 'Zavis', 'bfc745f6284de58afee2c434fddaf5127a8c2202', '%zavis%', NULL, NULL),
  (2, '2018-04-29 12:13:01', 'Stan', '6b8b9f1d44f5a55fb47876f950abfce4c6ea5967', '%Stan1A%', NULL, NULL),
  (3, '2018-04-29 12:13:43', 'Hamann', '2a7ce79a6532580c88032b9ec74c084bd647646d', '%Hamann%', NULL, NULL),
  (4, '2018-04-29 12:14:41', 'Dainis', '3be27a1781bf709b38f7764f9dfc6951dad3050c', '%dainis%', NULL, NULL);