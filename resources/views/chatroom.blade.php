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
                            <div id="message-content" data-spy="scroll" data-target="#navbar-example" data-offset="0" style="height:400px; overflow: auto; position: relative;"></div>
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
    var wsServer = 'ws://139.224.15.38/ws';
    var websocket = new WebSocket(wsServer);
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
    };

    websocket.onmessage = function (evt) {
        console.log('Retrieved data from server: ' + evt.data);
        var message = eval('(' + evt.data + ')');
        if (message['type'] == 0) {
            var className = "#message-proto .message";
            if (userId == message['userId']) {
                className = "#message-proto .self-message";
            }
            var messageBox = $(className).clone();
            $(messageBox).find(".user-div").text(message['username']);
            $(messageBox).find(".message-div").text(message['message']);
            $("#message-content").append($(messageBox));
        }
        else if (message['type'] == 2 || message['type'] == 3)
        {
            closeAlert();
            var alertBox = $("#alert-proto div").clone();
            $(alertBox).find(".alert-span").text(message['message']);
            $("tbody").prepend($(alertBox));
            setTimeout(closeAlert, 3000);
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

    var closeAlert = function(){
        var closeButton = $("tbody").find(".close");
        if ($(closeButton).length) {
            $(closeButton).alert('close');
        }
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
