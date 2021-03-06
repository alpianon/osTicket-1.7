<?php
/*********************************************************************
    password.php

    Password change handle.  
    CC_EMAILS+BASIC_CLIENT_AUTH MOD

    Alberto Pianon <alberto@pianon.eu>
    Copyright (c)  2013
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('secure.inc.php');
if(!is_object($thisclient) || !$thisclient->isValid()) die('Access denied');

$inc='password.inc.php';    //default include.

$errors=array();

if($_POST) {
	$vars = $_POST;
	$sql='SELECT password FROM '.CLIENT_PASSWORDS_TABLE.' WHERE email='.db_input($_SESSION['_client']['userID']);
	if($res=db_query($sql)){
		$spwd=db_fetch_array($res);
		if(strlen($vars['new_password'])<8)
			$errors['err']=/*__(*/'password should be at least 8 characters long'/*)*/;
		if($vars['new_password']!=$vars['retype_password'])
			$errors['err']=($errors['err'])?$errors['err']./*__(*/' and new passwords do not match'/*)*/:/*__(*/'New passwords do not match'/*)*/;
		if($spwd['password']!=md5($vars['old_password']))
			$errors['err']=($errors['err'])?$errors['err']./*__(*/' and old password is not correct'/*)*/:/*__(*/'Old password is not correct'/*)*/;	
		if(!$errors['err']){
			$sql='UPDATE '.CLIENT_PASSWORDS_TABLE.' SET password='.db_input(md5($vars['new_password'])).' WHERE email='.db_input($_SESSION['_client']['userID']);
			if($res=db_query($sql)){
				$msg=/*__(*/'Password successfully changed!'/*)*/;
			}else{		
				$errors['err']=/*__(*/'cannot update password in database, please contact admin'/*)*/;	
			}
		}		
	}else{
		$errors['err']=/*__(*/'cannot find your old password in database, this is very strange, please contact admin'/*)*/;	
	}
	unset($vars);
	unset($_POST);	
}

//page
$nav->setActiveNav('change_password');
require(CLIENTINC_DIR.'header.inc.php');
require(CLIENTINC_DIR.$inc);
require(CLIENTINC_DIR.'footer.inc.php');
?>
