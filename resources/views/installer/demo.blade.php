@extends('layouts.installer.installer')

@section('license')
done
@stop

@section('environment')
done
@stop

@section('database')
done
@stop

@section('locale')
active
@stop

@section('content')

<!-- <body onbeforeunload="return myFunction()"> -->
 <div id="form-content">

<div ng-app="myApp">
        <h1 style="text-align: center;">Getting Started</h1>
        
          {!! Form::open(['url'=>route('final'), 'id' => 'postaccount']) !!}
         
        

        <!-- checking if the form submit fails -->
        @if($errors->first('admin_fname')||$errors->first('admin_lname')||$errors->first('admin_email')||$errors->first('admin_password')||$errors->first('confirm_password'))
             <div class="woocommerce-message woocommerce-tracker">
                <div class="fail">
                    @if($errors->first('admin_fname'))
                        <span id="fail">{!! $errors->first('admin_fname', ':message') !!}</span><br/><br/>
                    @endif
                    @if($errors->first('admin_lname'))
                        <span id="fail">{!! $errors->first('admin_lname', ':message') !!}</span><br/><br/>
                    @endif
                    @if($errors->first('admin_email'))
                        <span id="fail">{!! $errors->first('admin_email', ':message') !!}</span><br/><br/>
                    @endif
                   
                    @if($errors->first('admin_password'))
                        <span id="fail">{!! $errors->first('admin_password', ':message') !!}</span><br/><br/>
                    @endif
                  


                </div>
            </div>  
             @endif
               <!-- checking if the system fails -->
        @if(Session::has('fails'))
            <div class="woocommerce-message woocommerce-tracker">
                <div class="fail">
                    <span id="fail">{{Session::get('fails')}} </span><br/><br/>
                </div>
            </div>
        @endif

    <div ng-controller="MainController"> 
            <table>                
                <h1>Sign up as Admin</h1>
                <div>
                    <tr>
                        <td>
                            <label for="box1">First Name<span style="color
                                : red;font-size:12px;">*</span></label>
                        </td>
                        <td>
                            {!! Form::text('admin_fname',null,['style' =>'margin-left:250px', 'required' => true]) !!}
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Nametitle}}" data-content="@{{Namecontent}}" style="padding: 0px;border: 0px; border-radius: 5px;">
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="box2">Last Name<span style="color
                                : red;font-size:12px;">*</span></label>
                        </td>
                        <td>
                            {!! Form::text('admin_lname',null,['style' =>'margin-left:250px', 'required' => true]) !!}
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Lasttitle}}" data-content="@{{Lastcontent}}" style="padding: 0px;border: 0px; border-radius: 5px;">
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="box2">Email<span style="color
                                : red;font-size:12px;">*</span></label>
                        </td>
                        <td>
                            {!! Form::email('admin_email',null,['style' =>'margin-left:250px', 'required' => true]) !!}
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Emailtitle}}" data-content="@{{Emailcontent}}" style="padding: 0px;border: 0px; border-radius: 5px;">
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label for="box4">Password <span style="color
                                    : red;font-size:12px;">*</span>
                            </label>
                        </td>
                        <td>
                            <input type="password" name="admin_password" style="margin-left: 250px" required="true" >
                                <span style="color: red; font-size: 12px; margin-left: 20px">@error('admin_password') {{'Invalid Password'}}  @enderror </span>
                        </td>
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Passtitle}}" data-content="@{{Passcontent}}" style="padding: 0px;border: 0px; border-radius: 5px;">
                            </button>
                        </td>
                    </tr>
                    <tr>
                      
                        <td>
                            <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Confirmtitle}}" data-content="@{{Confirmcontent}}" style="padding: 0px;border: 0px; border-radius: 5px;"> </button>
                        </td>
                    </tr>
                </div>
            </table>
           
            <br><br>
            <p class="setup-actions step">


                <input type="submit" id="submitme" class="button-primary button button-large button-next" value="Continue">


            </p>
        </form>
    </div>
    </p>
    <script src="{{assetLink('js','jquery-3-latest')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.2/angular.min.js"></script>
    <script src="{{assetLink('js','angular2')}}" type="text/javascript"></script>
</script>


    </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $('.hiddenCredentials').hide();
            $('input.hiddenInput').prop('required',false);
            $('.hiddenCacheCredentials').hide();
            $('input.hiddenRedisInput').prop('required',false);
            var cacheSelect = $('#cacheSelect :selected').val();
            if (cacheSelect == 'redis') {
                $('.hiddenCacheCredentials').show();
                $("input[name='redis_host']").prop('required',true);
                $("input[name='redis_port']").prop('required',true);

            } else {
                $('.hiddenCacheCredentials').hide();
                $('input.hiddenRedisInput').prop('required',false);

            }
        })
        
        $('#driverSelect').change(function (){
            var value = $(this).val();
            if (value == 's3') {
                $('.hiddenCredentials').show();
                $('input.hiddenInput').prop('required',true);
		$('input.endpoint').prop('required', false);
            } else {
                $('.hiddenCredentials').hide();
                $('input.hiddenInput').prop('required',false);

            }
        })
        $('#cacheSelect').change(function (){
            var value = $(this).val();
            if (value == 'redis') {
                $('.hiddenCacheCredentials').show();
                $("input[name='redis_host']").prop('required',true);
                $("input[name='redis_port']").prop('required',true);

            } else {
                $('.hiddenCacheCredentials').hide();
                $('input.hiddenRedisInput').prop('required',false);

            }
        })
            @if($errors->has('admin_fname'))
                addErrorClass('admin_fname');
            @endif
            @if($errors->has('admin_lname'))
                addErrorClass('admin_lname');
            @endif
            @if($errors->has('admin_email'))
                addErrorClass('admin_email');
            @endif
           
            @if($errors->has('admin_password'))
                addErrorClass('admin_password');
            @endif
            @if($errors->has('confirmpassword'))
                addErrorClass('confirmpassword');
            @endif
         
        $('#postaccount').on('submit', function(e) {
            $empty_field = 0;
            $("#postaccount input").each(function() {
                if($(this).attr('name') == 'admin_fname' ||
                   $(this).attr('name') == 'admin_lname' ||
                   $(this).attr('name') == 'admin_email' ||
                   $(this).attr('name') == 'admin_password' ||
                   $(this).attr('timezone') == 'admin_password' ||
                   $(this).attr('name') == 'confirmpassword'){
                    if ($(this).val() == '') {
                        $(this).css('border-color','red')
                        $(this).css('border-width','1px');
                        $empty_field = 1;
                    } else {
                        $empty_field = 0;
                    }
                }
            });
            if ($empty_field !=0 ) {
                alert('Please fill all required values.');
                e.preventDefault();
                $('#submitme').attr('disabled', false);
                $('#submitme').val('Install');
            }
        });
        $('input').on('focus', function(){
            $(this).css('border-color','#A9A9A9')
            $(this).css('border-width','1px');
        })
        $('input').on('blur', function(){
            if($(this).val() == ''){
                addErrorClass($(this).attr('name'));
            }
        });
        function addErrorClass(name){
            var target = document.getElementsByName(name);
            $(target[0]).css('border-color','red');
            $(target[0]).css('border-width','1px');
        }
    </script>
@stop

