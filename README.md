Basic Client Auth + CC Emails MOD
=================================
Starting from WalterEgo's work, I wrote a MOD implementing full support 
for CC emails AND simple password authentication for users, therefore
avoiding security issues (see here
https://github.com/osTicket/osTicket-1.7/issues/506#issuecomment-14901859)

As for CC emails, it works more or less as described in this post
http://www.osticket.com/forums/forum/osticket-1-7-latest-release/suggestions-and-feedback-aa/9538-send-email-cc-to-alternative-email-adresses?p=44014#post44014
(cc opt out feature is still missing, I still have to work on that)

As for password auth for users, instead of modifying the whole auth system 
based on email and ticket ID (otherwise it would become more of a fork 
rather than a simple MOD), I implemented a little workaround so that user 
login page asks for a password (required) and a ticket ID (optional), and 
if no (valid) ticket ID is provided (but the password is correct), the 
program picks up the first ticket id available for that user and then logs
him/her in, redirecting him/her to ticket list page.
User passwords are automatically created by the program and sent via email
to users, and can be changed by users after loggin in (there is also a 
"remember me" flag so users do not need to insert email and password every
time).
It is not exactly the best auth system ever :) but I just needed a quick 
and dirty MOD to implement CC emails with a minimum of security.



osTicket
========
osTicket is a widely-used open source support ticket system. It seamlessly
integrates inquiries created via email, phone and web-based forms into a
simple easy-to-use multi-user web interface. Manage, organize and archive
all your support requests and responses in one place while providing your
customers with accountability and responsiveness they deserve.

How osTicket works for you
--------------------------
  1. Users create tickets via your website, email, or phone
  1. Incoming tickets are saved and assigned to agents
  1. Agents help your users resolve their issues

osTicket is an attractive alternative to higher-cost and complex customer
support systems; simple, lightweight, reliable, open source, web-based and
easy to setup and use. The best part is, it's completely free.

Installation
------------
osTicket now supports bleeding-edge installations. The easiest way to
install the software and track updates is to clone the public repository.
Create a folder on you web server (using whatever method makes sense for
you) and cd into it. Then clone the repository (the folder must be empty!):

    git clone https://github.com/osTicket/osTicket-1.7 .

osTicket uses the git flow development model, so you’ll need to switch to
the develop branch in order to see the bleeding-edge feature additions.

    git checkout develop

Follow the usual install instructions (beginning from Manual Installation
above), except, don't delete the setup/ folder. For this reason, such an
installation is not recommended for a public-facing support system.

Upgrading
---------
osTicket supports upgrading from 1.6-rc1 and later versions. As with any
upgrade, strongly consider a backup of your attachment files, database, and
osTicket codebase before embarking on an upgrade.

To trigger the update process, fetch the osTicket-1.7 tarball from either
the osTicket [github](http://github.com/osTicket/osTicket-1.7) page or from
the osTicket website. Extract the tarball into the folder of your osTicket
codebase. This can also be accomplished with the zip file, and a FTP client
can of course be used to upload the new source code to your server.

Any way you choose your adventure, when you have your codebase upgraded to
osTicket-1.7, visit the /scp page of you ticketing system. The upgrader will
be presented and will walk you through the rest of the process. (The couple
clicks needed to go through the process are pretty boring to describe).

**WARNING**: If you are upgrading from osTicket 1.6, please ensure that all
    your files in your upload folder are both readable and writable to your
    http server software. Unreadable files will not be migrated to the
    database during the upgrade and will be effectively lost.

View the UPGRADING.txt file for other todo items to complete your upgrade.

Help
----
Visit the [wiki](http://osticket.com/wiki/Home) or the
[forum](http://osticket.com/forums/). And if you'd like professional help
managing your osTicket installation,
[commercial support](http://osticket.com/support/) is available.

Contributing
------------
Create your own fork of the project and use
[git-flow](https://github.com/nvie/gitflow) to create a new feature. Once
the feature is published in your fork, send a pull request to begin the
conversation of integrating your new feature into osTicket.

License
-------
osTicket is released under the GPL2 license. See the included LICENSE.txt
file for the gory details of the General Public License.

osTicket is supported by several magical open source projects including:

  * [Font-Awesome](http://fortawesome.github.com/Font-Awesome/)
  * [FPDF](http://www.fpdf.org/)
  * [HTMLawed](http://www.bioinformatics.org/phplabware/internal_utilities/htmLawed)
  * [jQuery dropdown](http://labs.abeautifulsite.net/jquery-dropdown/)
  * [PasswordHash](http://www.openwall.com/phpass/)
  * [PEAR](http://pear.php.net/package/PEAR)
  * [PEAR/Auth_SASL](http://pear.php.net/package/Auth_SASL)
  * [PEAR/Mail](http://pear.php.net/package/mail)
  * [PEAR/Net_SMTP](http://pear.php.net/package/Net_SMTP)
  * [PEAR/Net_Socket](http://pear.php.net/package/Net_Socket)
  * [PEAR/Serivces_JSON](http://pear.php.net/package/Services_JSON)
  * [phplint](http://antirez.com/page/phplint.html)
  * [phpseclib](http://phpseclib.sourceforge.net/)
  * [Spyc](http://github.com/mustangostang/spyc)
