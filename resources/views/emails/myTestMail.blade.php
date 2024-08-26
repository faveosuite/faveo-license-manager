<!DOCTYPE html>
<html>
<head>
    <title>Faveo License Manager Reset Password Link </title>
</head>
<body>
<div style="width: 100%!important; margin: 0; padding: 0;">
    <div style="padding: 15px; line-height: 18px; font-family: Lucida Grande,Verdana,Arial,sans-serif; font-size: 12px; color: #444444;">
        <p>Hello {{ $username }},
            <br />
            <br />We received a request to reset your password. To proceed, please click the link below and follow the instructions to create a new password:<br />
            <a href="{{ url('reset/'.$token) }}" target="_blank">{{ url('reset/'.$token) }}</a>
            <br /><br />If you did not request a password reset, no further action is needed. Your account remains secure.</p>Best Regards,<br />Agora Support Center</div>
    </div>
</div>
</body>
</html>
