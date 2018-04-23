SELECT

b.number ,

b.miner,

DATE_FORMAT(FROM_UNIXTIME(b.timestamp), '%e %b %Y %h:%m:%s') AS 'datetime' , 




UNHEX(SUBSTRING(b.data, 3)),

b.data

FROM `blocks` b

WHERE b.miner = '0x3be27a1781bf709b38f7764f9dfc6951dad3050c'

AND UNHEX(SUBSTRING(b.data, 3)) LIKE 'dainis%'

 ;
