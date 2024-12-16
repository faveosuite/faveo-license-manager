@extends('layouts.installer.installer')

@section('license')
done
@stop

@section('environment')
done
@stop

@section('database')
active
@stop

@section('content')
<div ng-app="myApp">
    <h1 style="text-align: center;">Database Setup</h1>
    <p class="wc-setup-content">Enter your database connection details.</p>

    @if(Cache::has('fails'))
    <div class="wc-setup-content">
        <div class="woocommerce-message woocommerce-tracker">
            <div class="fail">
                <span id="fail">{!! Lang::get('lang.fails') !!}! {{Cache::get('fails')}}</span><br/><br/>
            </div>
        </div>
    </div>
    <?php Cache::forget('fails')?>
    @endif


    {!! html()->form('POST', url('/config'))->id('databaseform')->open() !!}
    <table ng-controller="MainController">
        <tr>
            <td>
                <label for="box1">Host<span style="color: red;font-size:12px;">*</span></label>
            </td>
            <td>
                {!! html()->text('host', 'localhost')->required() !!}
            </td>
            <td>
                <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Hosttitle}}" data-content="@{{Hostcontent}}" style="padding: 0px;border: 0px; border-radius: 5px;"><i class="fa-solid fa-circle-question" style="padding: 0px;"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td>
                <label for="box2">MySQL port number</label>
            </td>
            <td>
                {!! html()->text('port')->attribute('onkeydown', 'return CheckPortForInput(event)') !!}
            </td>
            <td>
                <button type="button" data-toggle="popover" tabIndex="-1" data-placement="right" data-arrowcolor="#eeeeee" data-bordercolor="#bbbbbb" data-title-backcolor="#cccccc" data-title-bordercolor="#bbbbbb" data-title-textcolor="#444444" data-content-backcolor="#eeeeee" data-content-textcolor="#888888" title="@{{Porttitle}}" data-content="@{{Portcontent}}" style="padding: 0px;border: 0px; border-radius: 5px;"><i class="fa-solid fa-circle-question" style="padding: 0px;"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td>
                <label for="box3">Database Name<span style="color: red;font-size:12px;">*</span></label>
            </td>
            <td>
                {!! html()->text('databasename')->required() !!}
            </td>

        </tr>
        <tr>
            <td>
                <label for="box4">Username<span style="color: red; font-size: 12px;">*</span></label>
            </td>
            <td>
                {!! html()->text('username')->required() !!}
            </td>

        </tr>
        <tr>
            <td>
                <label for="box5">Password</label>
            </td>
            <td>
                <input type="password" name="password">
            </td>

        </tr>
        </table>


    <br>
    <p class="setup-actions step">
        <input type="submit" id="submitme" class="button-primary button button-large button-next" value="Continue">
        <a href="probe.php" class="button button-large button-next" style="float: left">Previous</a>
    </p>

    <br>
</form>
</div>
<script src="{{assetLink('js','jquery-3-latest')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.2/angular.min.js"></script>
<script src='themes/default/js/angular2.js' type="text/javascript"></script>
<script type="text/javascript">
    function CheckPortForInput(e) {
        var code = e.which || e.keyCode;
        if (e.ctrlKey != true){
            if((code >=48 && code<= 57) || (code >=96 && code<= 105) || code == 8 || code == 46 || e.keyCode == 9 || e.keyCode == 13) {
                return true;
            }
        } else {
            if((code == 65 || code == 97) || (code == 88 || code == 120) || (code == 86 || code == 118)) {
                return true;
            }
        }
        return false;
    }
</script>
<script type="text/javascript">


    $('#databaseform').on('submit', function(e){
        var empty_field = 0;
        $("#databaseform input[type=text]").each(function(){
            if($(this).attr('name') == 'host' || $(this).attr('name') == 'databasename' || $(this).attr('name') == 'username'){
                if ($(this).val() == '') {
                    $(this).css('border-color','red')
                    $(this).css('border-width','1px');
                    empty_field = 1;
                } else {
                    empty_field = 0;
                }
            }
        });
        if (empty_field != 0) {
            e.preventDefault();
            alert('Please fill all required values.');
        }
    });

    $('input[type=text]').on('blur', function(){
        if($(this).attr('name') == 'host' || $(this).attr('name') == 'databasename' || $(this).attr('name') == 'username'){
            if ($(this).val() == '') {
                addErrorClass($(this).attr('name'));
            }
        }
    })

    function addErrorClass(name){
        var target = document.getElementsByName(name);
        $(target[0]).css('border-color','red');
        $(target[0]).css('border-width','1px');
    }

    $('input').on('focus', function(){
        $(this).css('border-color','#A9A9A9')
        $(this).css('border-width','1px');
    })

    function toggleSSLSettings()
    {
        var x = document.getElementById('additional_ssl_config');
        x.style.display = x.style.display === "none" ? "block" : "none";
    }
</script>
</div>
@stop
