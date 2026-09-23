<!DOCTYPE html>
<html>
<head>
    <title>Welcome Email</title>
</head>

<body>
<h2>Thank you</h2>
<br/>
Thanks for subscribing to the Metro City email list.
To complete your subscription, you need to confirm you got this email.
To do so, please click the link below:
<br/>
<a href="{{url('newsLetters/verify', $newsLetter->verifyEmail->token)}}">{{url('newsLetters/verify', $newsLetter->verifyEmail->token)}}</a>
</body>

</html>
