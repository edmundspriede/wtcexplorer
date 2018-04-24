<?php

require('../config.inc');
require(CMS_LIBPATH."constants.inc");
require(CMS_LIBPATH.CMS_SYSPATH."s_init.inc");
require(CMS_EXTRAPATH.'XPM3/MAIL.php');
require(CMS_LIBPATH.'dInvoice.php');
require(CMS_LIBPATH.'d_client.inc');


dInvoice::sendNotifications();


?>
