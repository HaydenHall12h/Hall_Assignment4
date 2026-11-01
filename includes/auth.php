<?php

/*
 * This file is included by pages that need session/authentication behavior.
 *
 * IMPORTANT:
 * session_start() must happen BEFORE any HTML is sent to the browser.
 * PHP uses the session cookie to reconnect this request with server-side
 * session data from an earlier request.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/*
 * Return the currently authenticated user stored in the session.
 *
 * We store only the information the application needs. We DO NOT store
 * the user's password or password hash in the session.
 */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function loginUser(array $user): void
{
    /*
     * TODO 1 — Complete login session creation.
     *
     * 1. Regenerate the session ID after successful authentication.
     *    Why? The user's authentication state has just changed.
     *
     * 2. Store ONLY the fields the application needs in $_SESSION['user']:
     *      id, name, email, role
     *
     * Do not store password_hash in the session.
     */

    // Example structure AFTER you implement it:
    // $_SESSION['user'] = [
    //     'id' => ...,
    //     'name' => ...,
    //     'email' => ...,
    //     'role' => ...
    // ];

    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => $user['id'] ?? null,
        'name' => $user['name'] ?? null,
        'email' => $user['email'] ?? null,
        'role' => $user['role'] ?? 'agent' // I just default the role to agent
    ];
}

function requireLogin(): void
{
    /*
     * TODO 2 — Protect private pages.
     *
     * If the user is NOT logged in:
     * 1. Remember the page they originally requested using $_SERVER['REQUEST_URI'].
     * 2. Redirect to login.php.
     * 3. Stop the current script with exit.
     *
     * A-HA:
     * Hiding a navigation link is NOT access control.
     * The SERVER must reject/redirect unauthorized requests.
     */

    // I use the empty function over isset here because it covers more edge cases, such as a session key existing with no actual data.
    if (empty($_SESSION['user'])) {
        $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? 'index.php';

        header('Location: login.php');

        exit;
    }
}

function requireRole(string $requiredRole): void
{
    /*
     * TODO 3 — Authorization.
     *
     * First make sure the user is logged in.
     * Then compare currentUser()['role'] with $requiredRole.
     *
     * If the logged-in user does not have permission:
     * return HTTP 403 and stop the request.
     *
     * A-HA:
     * Authentication asks "Who are you?"
     * Authorization asks "Are you allowed to do this?"
     */
    requireLogin(); // Verify they're logged in

    $userRole = $_SESSION['user']['role'] ?? ''; // Extract the user's role from our array

    if ($userRole !== $requiredRole) { // Validate clearance level
        http_response_code(403); // Return our error code

        // Output a user-facing error message and stop execution
        echo "<h1> 403 Forbidden </h1>";
        echo "<p>You do not have permission to access this resource.</p>";
        exit;
    }
}

function setFlash(string $message): void
{
    /*
     * TODO 4 — Store a one-time message in the session.
     *
     * Flash messages are useful because POST handlers normally redirect.
     * The message must survive the redirect, but should appear only once.
     */
    $_SESSION['flash_message'] = $message;
}

function getFlash(): ?string
{
    /*
     * TODO 5 — Read AND remove the one-time message.
     *
     * Returning the message without removing it would make it appear
     * on every future page request.
     */
    
    if (isset($_SESSION['flash_message'])) { // Check if a message is set
        $message = $_SESSION['flash_message']; // Read message
        unset($_SESSION['flash_message']); // Remove message
        return $message; // Return the message
    }

    return null; // Else just return null
}

function logoutUser(): void
{
    /*
     * TODO 6 — End the authenticated session.
     *
     * Suggested sequence:
     * 1. Clear $_SESSION.
     * 2. If PHP is using a session cookie, expire that cookie.
     * 3. Destroy the server-side session.
     *
     * Do not merely redirect and leave the authenticated session alive.
     */

    $_SESSION = []; // Clear session

    setcookie(session_name(), '', time() - 3600, '/'); // Eat (expire) the cookie

    session_destroy(); // Destroy the session
}
