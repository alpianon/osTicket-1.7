<!-- New File! CC_EMAILS+BASIC_CLIENT_AUTH MOD
form to change user password -->

<?php
if(!defined('OSTCLIENTINC')) die('Access Denied!');
?>
<h1><?php echo /*__(*/'Change Password for '/*)*/.$_SESSION['_client']['userID'];?></h1>
<p><?php echo /*__(*/'Please insert your old password and the new password you want to use'/*)*/;?></p>
<form id="ticketForm" method="post" action="password.php" enctype="multipart/form-data">
  <?php csrf_token(); ?>
  <input type="hidden" name="a" value="open">
  <table width="800" cellpadding="1" cellspacing="0" border="0">
    <tr>
        <th width="160"><?php echo /*__(*/'Old Password'/*)*/;?>:</th>
        <td>         
                <input type="password" name="old_password" id="opass" size="16" autocomplete="off" autocorrect="off" autocapitalize="off">
        </td>
    </tr>

    <tr>
        <th width="160"><?php echo /*__(*/'New Password'/*)*/;?>:</th>
        <td>         
                <input type="password" name="new_password" id="npass" size="16" autocomplete="off" autocorrect="off" autocapitalize="off">    
        </td>

    </tr>
       <tr>
        <th width="160"><?php echo /*__(*/'Retype New Password'/*)*/;?>:</th>
        <td>         
                <input type="password" name="retype_password" id="rpass" size="16" autocomplete="off" autocorrect="off" autocapitalize="off">  
        </td>
    </tr>
    
    <tr><td colspan=2>&nbsp;</td></tr>
  </table>
  <p style="padding-left:150px;">
        <input type="submit" value="<?php echo /*__(*/'Change Password'/*)*/;?>">
        <input type="reset" value="<?php echo /*__(*/'Reset'/*)*/;?>">
        <input type="button" value="<?php echo /*__(*/'Cancel'/*)*/;?>" onClick='window.location.href="index.php"'>
  </p>
</form>
