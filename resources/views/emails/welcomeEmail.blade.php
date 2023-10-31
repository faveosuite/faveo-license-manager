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
    <title>@lang('lang.login_credentials'): Your Account is Ready!</title>
</head>
<body>
    <p>Hi {{$client_name}},</p>
    <p>@lang('lang.login_credentials').</p>
    <p>@lang('lang.informclient')</p>
    <p> @lang('lang.login_credentials')</p>
    <p class="bottom"> <strong>Email:</strong> {{$client_email}}</p>
    <p class="top"><strong>Password:</strong> {{$password }}</p>
    <p class="bottom">Login Link:</p>
    <p class="top"><a href="{{$appUrl}}">{{$appUrl}}</a></p>
    <p >@lang('lang.support_contact')<a href="mailto:support@faveohelpdesk.com" style="text-decoration: none;">support@faveohelpdesk.com</a></p>
    <p class="bottom">Best Regards,</p>
    <p class="top">Agora Support Center</p>
  
    <hr style=" margin-top:3rem; border: none;
            border-top: 1px solid #ccc;">
   <p style=" color: #999;"> This email has been sent by <b>Agora Support Center.</b> powered by <a style="text-decoration: none;" href="https://faveohelpdesk.com/">Faveo</a></p>

</body>
</html>
