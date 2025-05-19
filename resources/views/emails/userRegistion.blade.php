<!DOCTYPE html>
<html>
<head>
    <title>Registration Verification Email</title>
</head>
<body>
    <h1>Verify Password Email</h1>
    <p>Your password reset token: {{ $token }}</p>
    <!-- 將參數token添加到route中-->
    <a href="http://localhost:5173/verify_email/{{ $token }}">check</a> 
</body>
</html>