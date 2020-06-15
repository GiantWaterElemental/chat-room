@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <table class="table table-success table-bordered">
                <thead>
                    <tr>
                        <th>
                            {{ $room->name }}
                        </th>
                    </tr>
                </thead>
                <tbody>
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
            <div class="card">
                <div class="card-header">当前用户</div>
                <div style="height: 400px; overflow: auto; position: relative;">
                    <ul class="list-group list-group-flush">
                        @foreach ($userList as $user)
                            <li class="list-group-item">{{ $user }}</li>
                        @endforeach
                        <li class="list-group-item"></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @include('message')
</div>


<script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
<script type="text/javascript">
    var userId = "{{ $userId }}";
    var username = "{{ $username }}";
    var wsServer = 'ws://139.224.15.38/ws';
    var websocket = new WebSocket(wsServer);
    websocket.onopen = function (evt) {
        console.log("Connected to WebSocket server.");
        heartBeat.start();
    };

    websocket.onclose = function (evt) {
        console.log("Disconnected");
    };

    websocket.onmessage = function (evt) {
        console.log('Retrieved data from server: ' + evt.data);
        var message = eval('(' + evt.data + ')');
        var className = "#message-proto .message";
        if (userId == message['userId']) {
            className = "#message-proto .self-message";
        }
        var messageBox = $(className).clone();
        $(messageBox).find(".user-div").text(message['username']);
        $(messageBox).find(".message-div").text(message['message']);
        $("#message-content").append($(messageBox));
        heartBeat.reset();
    };

    websocket.onerror = function (evt, e) {
        console.log('Error occured: ' + evt.data);
    };

    var heartBeat = {
        timeout: 50000,
        timeoutObj: null,
        reset: function(){
            this.timeoutObj = null;
            this.start();
        },
        start: function(){
            this.timeoutObj = setTimeout(function(){
                var message = {
                    "type": "1",
                    "message":"heart beat at "+ new Date()
                };
                console.log("heart beat");
                websocket.send(JSON.stringify(message));
            },this.timeout)
        }
    };

    $( window ).on("unload", function() {
        websocket.close();
    });

    $("#submit").click(function () {
        var message = $("#message").val();
        if (message) {
            $("#message").val("");
            var data = {
                "message":message,
                "userId":userId,
                "username":username
            };
            websocket.send(JSON.stringify(data));
            heartBeat.reset();
        }
    });
</script>
@endsection
