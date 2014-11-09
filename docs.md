# Site documentation

This is some documentation about the site's internals.

## Database

The users.gender field can be 0 for undefined, 1 for male, 2 for female or 3 for other.

The users.lookingfor field specifies which gender someone is looking for. This is 0 for undefined, 1 for male, 2 for female or 3 for both.

## Session

The session contains the following keys:

boolean loggedin (true when someone is logged in, you can assume the other fields to be filled then)

int userid

string name (the display name)

string username (login name, show this if empty(name))

string csrf (the csrf token, use echoCSRFToken() to spawn a form field and checkCSRF() to check for it)

boolean isadmin

