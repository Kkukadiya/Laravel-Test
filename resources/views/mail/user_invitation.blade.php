<!DOCTYPE html>
<html>
<head>
    <title>{{ env('APP_NAME')}}</title>
</head>
<body>
    <h1>Hello {{ $data['name'] }},</h1>
    <p>You are invited to use the portal, please use the following link to sigup on the platform.</p>
    <a href="{{ $data['verification_link'] }}">Click here</a>
    <br>
    <p>Thank you</p>
</body>
</html>
