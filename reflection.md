What information connects a browser request to a PHP session?
It'd be the session ID (PHPSESSID). It works by creating a data file server-side and sending a corresponding cookie to the browser.
Whenever a user visits that page, if they have a valid cookie on their device, the server will read that cookie, pull the matching data server-side,
and send a webpage containing that session's info back to the browser.
This allows users to effectively resume their previous session, even in a stateless language like HTTP.

Why does PHP use password_verify() instead of decrypting stored passwords?
It does this for security purposes. Passwords should never be unencrypted or stored in plain-text, only as encrypted hashes.
password_verify() works by taking the entered password, hashing it, and then checking if that newly created hash matches the stored hash.
This matters for security because it means a stolen database or json file of user info only contains useless hashes, keeping the plain-text passwords safe.

Why is hiding the status-update button insufficient authorization?
It really only obfuscates access, not action. It doesn't do anything to protect the endpoint script that receives the instruction.
If you don't protect the endpoint, people can manually send requests to it and bypass authorization checks, allowing them to make unauthorized changes.
The solution to this problem is additional serverside validation, such as making sure the request came from a valid start point or requiring a specific role be logged in before performing actions.

Why should the authenticated user's identity come from the session rather than a form field?
This boils down to control. Session data resides server-side, outside of the user's area of influence. 
Taking information from a form field can enable a user to edit their information client-side, which can result in escalated privileges.
i.e. manually setting your user id to that of an admin.

What did the session counter or flash-message exercise reveal about requests that was not obvious before?
It kinda revealed the illusion of a continuous connection. Web applications have no memory across page requests.
Because of this, we have to explicitly build a bridge for them. 
The flash message only appears because we literally store it server-side so it can persist for one page refresh/change/etc.
These continuous sessions only work because the server and browser pass a cookie around with every click. Without that cookie,
the server doesn't retain anything, shattering that illusion of continuity.