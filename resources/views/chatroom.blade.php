@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <table class="table table-success table-bordered">
                <thead>
                    <tr>
                        <th>
                            <div>{{ $room->name }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <tr><td></td></tr> -->
                    <tr>
                        <td class="bg-light">
                            <div id="message-content" data-spy="scroll" data-target="#navbar-example" data-offset="0" style="height:400px; overflow: auto; position: relative;">
                                @foreach ($messageList as $message)
                                    @if ($message['user_id'] == $userId)
                                        <div class="self-message" style="height: 65px;" messageId="{{ $message['message_id'] }}">
                                            <div class="float-right">
                                                <div class="alert alert-success message-div" style="display: inline-block;">{{ $message['message'] }}</div>
                                                <div class="arrow" style="width: 0; height: 0; font-size: 0; border-width: 10px; border-style: solid; position: relative; top: 6px; border-color: transparent transparent transparent #c7eed8; right: 4px; display: inline-block;"></div>
                                                <div class="alert alert-light img-thumbnail user-div" style="display: inline-block;">{{ $message['username'] }}</div>
                                    @else
                                        <div class="message" style="height: 65px;" messageId="{{ $message['message_id'] }}">
                                            <div class="float-left">
                                                <div class="alert alert-light img-thumbnail user-div" style="display: inline-block;">{{ $message['username'] }}</div>
                                                <div class="arrow" style="width: 0; height: 0; font-size: 0; border-width: 10px; border-style: solid; position: relative; top: 6px; border-color: transparent #c7eed8 transparent transparent; left: 4px; display: inline-block;"></div>
                                                <div class="alert alert-success message-div" style="display: inline-block;">{{ $message['message'] }}</div>
                                    @endif
                                            </div>
                                        </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="" aria-label="message" aria-describedby="basic-addon2" id="message">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-success" type="button" id="submit">提交</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-lg-2">
            <div class="card" id="user-card">
                <div class="card-header">当前用户</div>
                <div style="height: 400px; overflow: auto; position: relative;">
                    <ul class="list-group list-group-flush">
                        @foreach ($userList as $user)
                            <li class="list-group-item" id="user-li-{{ $user }}">{{ $user }}</li>
                        @endforeach
                        <li class="list-group-item"></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @include('chat.message')
    @include('chat.alert')
</div>


<script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
<script type="text/javascript">
    var userId = "{{ $userId }}";
    var username = "{{ $username }}";
    var roomId = "{{ $room['room_id'] }}";
    var wsServer = 'ws://139.224.15.38/ws?id=' + roomId;
    var websocket = new WebSocket(wsServer);
    var noMoreHistory = false;
    var ajaxStatus = false;
    websocket.onopen = function (evt) {
        console.log("Connected to WebSocket server.");
        var enterMessage = {
            "type": "2",
            "message": username + "加入了聊天室",
            "userId": userId,
            "username": username
        }
        websocket.send(JSON.stringify(enterMessage));
    };

    websocket.onclose = function (evt) {
        console.log("Disconnected");
        clearInterval(timer);
    };

    websocket.onmessage = function (evt) {
        console.log('Retrieved data from server: ' + evt.data);
        var message = eval('(' + evt.data + ')');
        if (message['type'] == 0) {
            var height = $("#message-content").height();
            var scrollHeight = $("#message-content").prop("scrollHeight");
            var className = "#message-proto .message";
            if (userId == message['userId']) {
                className = "#message-proto .self-message";
            }
            var messageBox = $(className).clone();
            $(messageBox).attr("messageId", message['message_id']);
            $(messageBox).find(".user-div").text(message['username']);
            $(messageBox).find(".message-div").text(message['message']);
            $("#message-content").append($(messageBox));
            if (height == scrollHeight) {
                scrollToBottom();
            }
        }
        else if (message['type'] == 2 || message['type'] == 3)
        {
            showAlert(message['message']);
        }
        if (message['type'] == 2 && userId != message['userId']) {
            var lastLi = $("#user-card").find("li:last");
            var userLi = $(lastLi).clone();
            $(userLi).attr("id", "user-li-" + message['username']);
            $(userLi).text(message['username']);
            $(lastLi).before($(userLi));
        }
        if (message['type'] == 3) {
            $("#user-li-" + message['username']).remove();
        }
    };

    websocket.onerror = function (evt, e) {
        console.log('Error occured: ' + evt.data);
    };

    var heartBeatMessage = {
        "type": "1",
        "message": "heart beat",
        "userId": 0,
        "username": "心跳"
    }

    var heartBeat = function(){
        console.log("heartBeat");
        websocket.send(JSON.stringify(heartBeatMessage));
    }

    var timer = setInterval(heartBeat, 50000);

    var showAlert = function(message){
        closeAlert();
        var alertBox = $("#alert-proto div").clone();
        $(alertBox).find(".alert-span").text(message);
        $("tbody").prepend($(alertBox));
        setTimeout(closeAlert, 3000);
    }

    var closeAlert = function(){
        var closeButton = $("tbody").find(".close");
        if ($(closeButton).length) {
            $(closeButton).alert('close');
        }
    }

    $(document).ready(function(){
        scrollToBottom();
    });

    var scrollToBottom = function(){
        var height = $("#message-content").height();
        var scrollHeight = $("#message-content").prop("scrollHeight");
        if (scrollHeight > height) {
            $("#message-content").scrollTop(scrollHeight);
        }
    }

    $("#message-content").on('mousewheel DOMMouseScroll', function(e){
        var wheel = e.originalEvent.wheelDelta || -e.originalEvent.detail;
        var delta = Math.max(-1, Math.min(1, wheel) );
        if (delta > 0 && $(this).scrollTop() == 0 && noMoreHistory == true) {
            showAlert("没有更多历史消息");
            return;
        }
        if (delta > 0 && $(this).scrollTop() == 0 && noMoreHistory == false) {
            prependMessage();
            $("#message-content").off('mousewheel DOMMouseScroll');
        }
    });

    var prependMessage = function(){
        if (ajaxStatus) {
            return;
        }
        ajaxStatus = true;
        var oldestMessageId = $("#message-content").children().first().attr("messageId");
        var url = "{{ action('ChatroomController@messageList') }}";
        var data = {roomId: roomId, messageId: oldestMessageId, order: 0};
        $.ajax({
            url:url,
            data:data,
            type:"post",
            dataType:"json",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(message){
                ajaxStatus = false;
                if (message.length == 0) {
                    showAlert("没有更多历史消息");
                    noMoreHistory = true;
                    return;
                }
                for (var i = 0; i < message.length; i++) {
                    var className = "#message-proto .message";
                    if (userId == message[i]['user_id']) {
                        className = "#message-proto .self-message";
                    }
                    var messageBox = $(className).clone();
                    $(messageBox).attr("messageId", message[i]['message_id']);
                    $(messageBox).find(".user-div").text(message[i]['username']);
                    $(messageBox).find(".message-div").text(message[i]['message']);
                    $("#message-content").prepend($(messageBox));
                }
            }
        });
    }

    $("#submit").click(function () {
        var message = $("#message").val();
        if (message) {
            $("#message").val("");
            var data = {
                "type":0,
                "message":message,
                "roomId":roomId,
                "userId":userId,
                "username":username
            };
            websocket.send(JSON.stringify(data));
            clearInterval(timer);
            timer = setInterval(heartBeat, 50000);
        }
    });
</script>
@endsection
