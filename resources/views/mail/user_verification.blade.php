<!DOCTYPE html>
<html>
<head>
    <title>{{ env('APP_NAME')}}</title>
</head>
<body>
    <h1>Hello {{ $data['verification_code'] }},</h1>
    <p>Your verification code is.</p>
    <br>
    <h1>{{ $data['verification_code'] }}</h1>
    <p>Please use this code to verify your profile.</p>
    <p>Thank you</p>
</body>
</html>
