<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

$email=Format::input($_POST['lemail']?$_POST['lemail']:$_GET['e']);
// Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD 
// use "id" instead of "t"
$ticketid=Format::input($_POST['lticket']?$_POST['lticket']:$_GET['id']);
// End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD
?>
<h1>Check Ticket Status</h1>
<p>To view the status of a ticket, provide us with the login details below.</p>
<form action="login.php" method="post" id="clientLogin">
    <?php csrf_token(); ?>
    <strong><?php echo Format::htmlchars($errors['login']); ?></strong>
    <br>
    <div>
        <label for="email">E-Mail Address:</label>
        <input id="email" type="text" name="lemail" size="30" value="<?php echo $email; ?>">
    </div>

<!-- Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD
     added password input and "remember me" flag 
     changed message at the end -->

    <div>
        <label for="pass"><?php echo /*__(*/'password'/*)*/;?>:</label>
        <input type="password" name="lpass" id="pass" size="16" autocorrect="off" autocapitalize="off">
    </div>
    <div>
        <label for="ticketno"><?php echo /*__(*/'Ticket ID'/*)*/./*__(*/' (optional)'/*)*/;?>:</label>
        <input id="ticketno" type="text" name="lticket" size="16" value="<?php echo $ticketid; ?>" autocomplete="off"></td>
    </div>
        <div>
        <label for="rememberme"><?php echo /*__(*/'Remember me'/*)*/;?></label>
        <input id="remeberme" type="checkbox" name="lremember" value="yes" checked></td>
    </div>


    <p>
        <input class="btn" type="submit" value="View Status">
    </p>
</form>
<br>
<p>
<?php echo /*__(*/"If this is your first time contacting us, please"/*)*/;?> <a href="open.php"><?php echo /*__(*/'open a new ticket'/*)*/;?></a>.    
</p>
<!-- End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD -->
