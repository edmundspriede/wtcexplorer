SELECT

b.number ,

b.miner,

DATE_FORMAT(FROM_UNIXTIME(b.timestamp), '%e %b %Y %h:%m:%s') AS 'datetime' , 


UNHEX(SUBSTRING(b.data, 3)),

b.data,


p.`blockreward` , 

p.`minerreaward`,

p.`id`

FROM `blocks` b

INNER JOIN pools p ON b.`miner` = p.`miner` 

WHERE p.id = 2  AND UNHEX(SUBSTRING(b.data, 3)) NOT LIKE 'dainis%'  



 ;
