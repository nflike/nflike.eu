The source code behind https://nflike.eu

## Setup

1. Setup a local webserver with Apache2.2/2.4, PHP 5.4+ and MySQL (on Windows,
   you can use wamp/xampp).
2. Clone the repository (`git clone <url>`) and copy the files to your
   documentroot. This is typically /var/www in Linux and C:\wamp\www or
   C:\xampp\htdocs in Windows).
3. Create a database on your MySQL server.
4. Rename the `config.php.template` file to `config.php` and replace DBHOST,
   DBUSER, etc. with the correct values. I think a typical xampp/wamp install
   will use localhost, root, empty password, and the database name which you
   just created.
5. Execute database.sql in your MySQL database. A handy tool is phpmyadmin
   (often available at <http://localhost/phpmyadmin>) or mysqlworkbench or
   something.

The site now works, though you will need to create a user account for yourself.
In the database, insert a new row in the users table. Pick a username, set
isadmin to 1, and use the password
`0551e5f3768bdff43bad75167733ab978de25850027e3bea927017b26a00070fb`. You can
now log in with your username and the password `tw1024`.

If you installed the website in a subdirectory, like /nf-like/, then you must
set the path to '/nf-like/' in config.php (include the leading and trailing
slash!).

## Getting started

The structure of the site is like this:

- The .htaccess file creates redirections for each page (pretty URLs);
- The index.php file handles every incoming request. Every. It calls the
  required pages.
- The file which is called by index.php takes over from there and handles the
  rest of the request. You can assume $db (the database connection) to be
  always available. See existing files for more info.

## To-do list

1. Design.
2. Profile pictures.
3. Find a better way to display custom texts.
4. HTTPS certificate, HSTS and secure cookie.
5. Other gender field.
6. Better sign-up process.
7. Perhaps a messaging option? Should we require e-mail addresses for notifs?

