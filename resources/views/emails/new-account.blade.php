<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>You have a new Account</h2>

        <div>
            @if ($socialMedia)
            <p>In addition to your social media login, we've provided you with a login for your email address.</p>
            @else
            <p>Your new Account for Gondolyn is:</p>
            @endif
            <p>Email: {{ $email }}</p>
            <p>Password: {{ $newPassword }}</p>
            <br>
            <p>In order to change your password, and we recommend you do, simply login, go to your settings and change your password.</p>
        </div>
    </body>
</html>