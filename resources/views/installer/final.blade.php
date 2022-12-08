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
done
@stop

@section('license-code')
done
@stop

@section('ready')
active
@stop

@section('content')

        <a class="twitter-share-button" target="_blank" href="https://twitter.com/intent/tweet?text=I just set up a new HELPDESK with @faveohelpdesk www.faveohelpdesk.com" @if(isWhiteLabelEnabled()) style="display:none;" @endif>
            <img src="https://encrypted-tbn2.gstatic.com/images?q=tbn:ANd9GcQ-uhinU3OzXKj9zlFO7dFxHaChqyHPcWWg5nWgMqYt6N5b3knK" style="width: 86px; float: right;">
        </a>

        <h1 style="text-align: center;">Your License Manager is Ready</h1>
<div class="woocommerce-message woocommerce-tracker">
               

            </div>


        <div class="setup-next-steps">
            <div class="setup-next-steps-first">
                <h2>Next Step</h2>
                <ul>

                    <li class="setup-product"><a class="button button-primary button-large" href="{!! url('login') !!}" style="float: none; text-align: center; font-size: 24px;    padding: 15px;     line-height: 1;">Login to License Manager</a>
                    </li>
                </ul>
            </div>
            <div class="setup-next-steps-last" @if(isWhiteLabelEnabled()) style="display:none;" @endif>
                <h2>Learn More</h2>
                <ul>
                    <li class="video-walkthrough"><a target="_blank" href="https://www.youtube.com/channel/UC-eqh-h241b1janp6sU7Iiw">Video walk thriugh</a></a>
                    </li>
                    <li class="sidekick"><a target="_blank" href="https://support.faveohelpdesk.com/knowledgebase">Knowledge base</a>
                    </li>

                    <li class="newsletter"><a href="mailto:support@ladybirdweb.com">Email Support</a>
                    </li>
                    <br>
                    <br>
                    <br>
                </ul>
            </div>
        </div>
  @stop