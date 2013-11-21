<?php
/*********************************************************************
    login.php

    Client Login

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require_once('client.inc.php');
if(!defined('INCLUDE_DIR')) die('Fatal Error');
define('CLIENTINC_DIR',INCLUDE_DIR.'client/');
define('OSTCLIENTINC',TRUE); //make includes happy

require_once(INCLUDE_DIR.'class.client.php');
require_once(INCLUDE_DIR.'class.ticket.php');

if($_POST) {
//Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD
   $remember_me=($_POST['lremember']=='yes')?true:false;
   if(($user=Client::login(trim($_POST['lticket']), trim($_POST['lemail']), null, $errors, md5(trim($_POST['lpass'])), $remember_me))) {
	        unset($_POST['lpass']);
		if(trim($_POST['lticket'])){
		//XXX: Ticket owner is assumed.
		@header('Location: tickets.php?id='.$user->getTicketID());
		require_once('tickets.php'); //Just in case of 'header already sent' error.
		exit;
		}else{ // if no ticket no. is provided, go to the ticket list
		   @header('Location: tickets.php');
		   require_once('tickets.php'); //Just in case of 'header already sent' error.
		   exit;
		}
	} elseif(!$errors['err']) {
		$errors['err'] = /*__(*/'Authentication error - try again!'/*)*/;
	}
	unset($_POST['lpass']);
}
// End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD


$nav = new UserNav();
$nav->setActiveNav('status');
require(CLIENTINC_DIR.'header.inc.php');
require(CLIENTINC_DIR.'login.inc.php');
require(CLIENTINC_DIR.'footer.inc.php');
?>
