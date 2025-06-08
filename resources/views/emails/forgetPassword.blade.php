<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            color: #333;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #dc3545;
        }

        p {
            font-size: 1rem;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background-color: #4eb1ea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        .footer {
            font-size: 0.9em;
            color: #666;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Reset Request</h1>
        <p>You recently requested to reset your password. Click the button below to proceed:</p>

        <a class="btn" href="http://localhost:5173/reset_password/{{ $token }}">Reset Password</a>

        <p class="footer">
            If you did not request a password reset, you can safely ignore this email.
        </p>
    </div>
</body>
</html>
