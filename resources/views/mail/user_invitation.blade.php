<!DOCTYPE html>
<html>
<head>
    <title>{{ env('APP_NAME')}}</title>
</head>
<body>
    <h1>Hello,</h1>
    <p>You are invited to use the portal, please use the following link to submit the API form (API method POST).</p>
   <a href="{{ $data['verification_link'] }}">Click here</a>

    <br>
    <p>Thank you</p>
</body>
</html>
