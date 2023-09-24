<!DOCTYPE html>
<html>
<head>
    <title>Forget Password Email</title>
</head>
<body>
    <h1>Forget Password Email</h1>
    <p>Your password reset token: {{ $token }}</p>
    <!-- 將參數token添加到route中-->
    <a href="{{route('reset_password',$token)}}">check</a> 
</body>
</html>
