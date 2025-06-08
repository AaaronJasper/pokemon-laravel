<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f7f7f7;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            border-radius: 6px;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h1 {
            color: #007bff;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #1ee0a2;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        .footer {
            font-size: 0.9em;
            color: #777;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Pokémon Collection!</h1>
        <p>Thank you for registering. Please click the button below to verify your email address:</p>

        <a class="btn" href="http://localhost:5173/verify_email/{{ $token }}">Verify Email</a>

        <p class="footer">
            If you did not register for this account, please ignore this email.
        </p>
    </div>
</body>
</html>
