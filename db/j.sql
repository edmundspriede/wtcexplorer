SELECT

b.number ,

b.miner,

DATE_FORMAT(FROM_UNIXTIME(b.timestamp), '%e %b %Y %h:%m:%s') AS 'datetime' , 




UNHEX(SUBSTRING(b.data, 3)),

b.data

FROM `blocks` b

WHERE b.miner = '0xb2d1942aa9fcdd5f5d11152fa06dfe754416fba5'



 ;
