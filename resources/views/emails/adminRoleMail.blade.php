<!DOCTYPE html>
<html>
<head>
<style>
    .bottom{
        margin-bottom: 0px;
         padding-bottom: 0px
    }
    .top{
        margin-top:0px;
         padding-top:0px  
    }
</style>
    <title>Admin Privileges Granted</title>
</head>
<body>
    <p>Hi {{$client_name}},</p>
    <p> Congratulations! You are now an Administrator with access to Agora License Manager's admin privileges.</p>
    <p>Log in to your account using the updated credentials:</p>
    <p class="bottom"> <strong>Email:</strong> {{$client_email}}</p>
    <p class="top"><strong>Password:</strong> <b>{{$password }}</b></p>
    <p class="bottom">Login Link:</p>
    <p class="top"><a href="{{$appUrl}}">{{$appUrl}}</a></p>
    <p >If you have any questions or need assistance, please contact our support <a href="mailto:support@faveohelpdesk.com">support@faveohelpdesk.com</a></p>
    <p class="bottom">Best Regards,</p>
    <p class="top">Agora Support Center</p>
  
    <hr style=" margin-top:3rem; border: none;
            border-top: 1px solid #ccc;">
   <p style=" color: #999;"> This email has been sent by <b>Agora Support Center</b> @powered by <a href="https://support.faveohelpdesk.com">Faveo</a></p>

</body>
</html>
