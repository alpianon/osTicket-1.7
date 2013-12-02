<?php
/*********************************************************************
    class.client.php

    Handles everything about client

    XXX: Please note that osTicket uses email address and ticket ID to authenticate the user*!
          Client is modeled on the info of the ticket used to login .

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

class Client {

    var $id;
    var $fullname;
    var $username;
    var $email;

    var $ticket_id;
    var $ticketID;

    var $ht;


    function Client($id, $email=null) {
        $this->id =0;
        $this->load($id,$email);
    }

    function load($id=0, $email=null) {

        if(!$id && !($id=$this->getId()))
            return false;

        /* Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD (taken from WALTEREGO CC MULTIPLE EMAILS mod) */
        $sql='SELECT ticket.ticket_id '
            .' , ticket.ticketID '
            .' , IF((cc_emails.email IS NOT NULL AND '.db_input($email).' != ticket.email), '.db_input($email).', ticket.name) AS name '
            .' , ticket.email '
            .' , ticket.phone '
            .' , ticket.phone_ext '
            .' FROM '.TICKET_TABLE.' ticket '
            .' LEFT JOIN '.TICKET_CC_EMAILS_TABLE.' cc_emails ON ('
                .'ticket.ticket_id=cc_emails.ticket_id) '
            .' WHERE ticket.ticketID='.db_input($id);
        if($email) {
            $sql.=' AND ( ticket.email='.db_input($email)
                 .' OR cc_emails.email='.db_input($email).' ) ';
        }
        $sql.=' GROUP BY ticket.ticket_id';       
        /* End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD */

        if(!($res=db_query($sql)) || !db_num_rows($res))
            return NULL;

        $this->ht = db_fetch_array($res);
        $this->id         = $this->ht['ticketID']; //placeholder
        $this->ticket_id  = $this->ht['ticket_id'];
        $this->ticketID   = $this->ht['ticketID'];
         /* Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD (taken from WALTEREGO CC MULTIPLE EMAILS) */  
        //$this->fullname   = ucfirst($this->ht['name']);    
        //$this->username   = $this->ht['email'];
        //$this->email      = $this->ht['email']; 
        $this->fullname   = (Validator::is_email($this->ht['name']) ? $this->ht['name']:ucfirst($this->ht['name']));  
        $this->username   = $email;
        $this->email      = $email;
        /* End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD */

        $this->stats = array();
      
        return($this->id);
    }

    function reload() {
        return $this->load();
    }

    function isClient() {
        return TRUE;
    }

    function getId() {
        return $this->id;
    }

    function getEmail() {
        return $this->email;
    }

    function getUserName() {
        return $this->username;
    }

    function getName() {
        return $this->fullname;
    }

    function getPhone() {
        return $this->ht['phone'];
    }

    function getPhoneExt() {
        return $this->ht['phone_ext'];
    }
    
    function getTicketID() {
        return $this->ticketID;
    }

    function getTicketStats() {

        if(!$this->stats['tickets'])
            $this->stats['tickets'] = Ticket::getClientStats($this->getEmail());

        return $this->stats['tickets'];
    }

    function getNumTickets() {
        return ($stats=$this->getTicketStats())?($stats['open']+$stats['closed']):0;
    }

    function getNumOpenTickets() {
        return ($stats=$this->getTicketStats())?$stats['open']:0;
    }

    function getNumClosedTickets() {
        return ($stats=$this->getTicketStats())?$stats['closed']:0;
    }

    /* ------------- Static ---------------*/
    function getLastTicketIdByEmail($email) {
        $sql='SELECT ticketID FROM '.TICKET_TABLE
            .' WHERE email='.db_input($email)
            .' ORDER BY created '
            .' LIMIT 1';
        if(($res=db_query($sql)) && db_num_rows($res))
            list($tid) = db_fetch_row($res);

        return $tid;
    }

    function lookup($id, $email=null) {
        return ($id && is_numeric($id) && ($c=new Client($id,$email)) && $c->getId()==$id)?$c:null;
    }

    function lookupByEmail($email) {
        return (($id=self::getLastTicketIdByEmail($email)))?self::lookup($id, $email):null;
    }

// Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD
// added basic password authentication without changing the auth structure of osicket -see below
    /* static */ function login($ticketID, $email, $auth=null, &$errors=array(), $password, $remember_me) {
	    $password = trim($password);

        global $ost;
// End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD

        $cfg = $ost->getConfig();
        $auth = trim($auth);
        $email = trim($email);
        $ticketID = trim($ticketID);

        # Only consider auth token for GET requests, and for GET requests,
        # REQUIRE the auth token
        $auto_login = ($_SERVER['REQUEST_METHOD'] == 'GET');

        //Check time for last max failed login attempt strike.
        if($_SESSION['_client']['laststrike']) {
            if((time()-$_SESSION['_client']['laststrike'])<$cfg->getClientLoginTimeout()) {
                $errors['login'] = 'Excessive failed login attempts';
                $errors['err'] = 'You\'ve reached maximum failed login attempts allowed. Try again later or <a href="open.php">open a new ticket</a>';
                $_SESSION['_client']['laststrike'] = time(); //renew the strike.
            } else { //Timeout is over.
                //Reset the counter for next round of attempts after the timeout.
                $_SESSION['_client']['laststrike'] = null;
                $_SESSION['_client']['strikes'] = 0;
            }
        }
// Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD
        $sql='SELECT password FROM '.CLIENT_PASSWORDS_TABLE.' WHERE email='.db_input($email);
// End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD

        if($auto_login && !$auth)
            $errors['login'] = 'Invalid method';
// Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD
        elseif(!Validator::is_email($email))
            $errors['login'] = /*__(*/'Valid email required'/*)*/;

        elseif(!($res=db_query($sql)) || !db_num_rows($res))
            $errors['login'] = /*__(*/'this email has not been registered yet'/*)*/;
        $spwd=db_fetch_array($res);


        //Bail out on error.
        if($errors) {
                unset($spwd); // we do not need it any more	
        	return false;
        }
// End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD

/* Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD

check if ticketID exist and is valid, otherwise substitute it with first ticketID found in database for that user 
this is a workaround to make ticket ID optional to log in, whithout changing the whole authentication structure of OsTicket */

	if( !($ticket=Ticket::lookupByExtId($ticketID, $email)) || !($ticket->getId()) || !in_array($email, $ticket->getTicketEmailsArray()) ){
		$sql='SELECT ticketID FROM '.TICKET_TABLE.' AS ticket '
			  .'LEFT JOIN '.TICKET_CC_EMAILS_TABLE.' AS cc_emails ON (ticket.ticket_id=cc_emails.ticket_id) '
			  .'WHERE ticket.email='.db_input($email).' OR cc_emails.email='.db_input($email);
		if($res=db_query($sql)){
			$t_id=db_fetch_array($res);
			$ticket=Ticket::lookupByExtId($t_id['ticketID'], $email);			
		} // TODO: handle errors here?			
        }
        //See if we can fetch local ticket id associated with the ID given

        if(($ticket && $ticket->getId() && $password==$spwd['password'])) {
	    unset($spwd); // we do not need it any more	  
	      
// End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD

            //At this point we know the ticket ID is valid.
            //TODO: 1) Check how old the ticket is...3 months max?? 2) Must be the latest 5 tickets?? 
            //Check the email given.

            # Require auth token for automatic logins (GET METHOD).
             /* Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD (taken from WALTEREGO CC MULTIPLE EMAILS 
Probably this is not needed any more since we use only password auth now
In the meantime we can leave it here, it should do no harm
*/
            //if (!strcasecmp($ticket->getEmail(), $email) && (!$auto_login || $auth === $ticket->getAuthToken())) 
            if (in_array($email, $ticket->getTicketEmailsArray()) && (!$auto_login || $auth === $ticket->getAuthToken())) {
                /* End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD */
                    
                //valid match...create session goodies for the client.
                $user = new ClientSession($email,$ticket->getExtId());
                $_SESSION['_client'] = array(); //clear.
                /* Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD (taken from WALTEREGO CC MULTIPLE EMAILS MOD) 
use user's email instead of ticket's main email as User ID (maybe user has been cc'ed)*/
                $_SESSION['_client']['userID'] = $email; //Email
                /* End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD */

                $_SESSION['_client']['key'] = $ticket->getExtId(); 
                $_SESSION['_client']['token'] = $user->getSessionToken();
                $_SESSION['TZ_OFFSET'] = $cfg->getTZoffset();
                $_SESSION['TZ_DST'] = $cfg->observeDaylightSaving();
                $user->refreshSession(); //set the hash.
                //Log login info...
                $msg=sprintf('%s/%s logged in [%s]', $ticket->getEmail(), $ticket->getExtId(), $_SERVER['REMOTE_ADDR']);
                $ost->logDebug('User login', $msg);
        
                //Regenerate session ID.
                $sid=session_id(); //Current session id.
// Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD
// enable "remember me"
                if($remember_me) session_set_cookie_params('2592000'); //30 days  TODO: make it modifiable by user/staff?
// End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD
                session_regenerate_id(TRUE); //get new ID.
                if(($session=$ost->getSession()) && is_object($session) && $sid!=session_id())
                    $session->destroy($sid);

                return $user;

            } 
        }
// Start EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD
        unset($spwd); // we do not need it any more
// End EDIT for CC_EMAILS+BASIC_CLIENT_AUTH MOD

        //If we get to this point we know the login failed.
        $errors['login'] = 'Invalid login';
        $_SESSION['_client']['strikes']+=1;
        if(!$errors && $_SESSION['_client']['strikes']>$cfg->getClientMaxLogins()) {
            $errors['login'] = 'Access Denied';
            $errors['err'] = 'Forgot your login info? Please <a href="open.php">open a new ticket</a>.';
            $_SESSION['_client']['laststrike'] = time();
            $alert='Excessive login attempts by a user.'."\n".
                    'Email: '.$email."\n".'Ticket#: '.$ticketID."\n".
                    'IP: '.$_SERVER['REMOTE_ADDR']."\n".'Time:'.date('M j, Y, g:i a T')."\n\n".
                    'Attempts #'.$_SESSION['_client']['strikes'];
            $ost->logError('Excessive login attempts (user)', $alert, ($cfg->alertONLoginError()));
        } elseif($_SESSION['_client']['strikes']%2==0) { //Log every other failed login attempt as a warning.
            $alert='Email: '.$email."\n".'Ticket #: '.$ticketID."\n".'IP: '.$_SERVER['REMOTE_ADDR'].
                   "\n".'TIME: '.date('M j, Y, g:i a T')."\n\n".'Attempts #'.$_SESSION['_client']['strikes'];
            $ost->logWarning('Failed login attempt (user)', $alert);
        }

        return false;
    }
}
?>
