@section('message')
<div id="message-proto" class="row" style="display: none;">
    <div class="message" style="height: 65px;" messageId="">
        <div class="float-left">
            <div class="alert alert-light img-thumbnail user-div" style="display: inline-block;"></div>
            <div class="arrow" style="width: 0; height: 0; font-size: 0; border-width: 10px; border-style: solid; position: relative; top: 6px; border-color: transparent #c7eed8 transparent transparent; left: 4px; display: inline-block;"></div>
            <div class="alert alert-success message-div" style="display: inline-block;"></div>
        </div>
    </div>
    <div class="self-message" style="height: 65px;" messageId="">
        <div class="float-right">
            <div class="alert alert-success message-div" style="display: inline-block;"></div>
            <div class="arrow" style="width: 0; height: 0; font-size: 0; border-width: 10px; border-style: solid; position: relative; top: 6px; border-color: transparent transparent transparent #c7eed8; right: 4px; display: inline-block;"></div>
            <div class="alert alert-light img-thumbnail user-div" style="display: inline-block;"></div>
        </div>
    </div>
</div>
@show