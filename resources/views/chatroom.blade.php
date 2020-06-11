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
                            <div data-spy="scroll" data-target="#navbar-example" data-offset="0" style="height:400px; overflow: auto; position: relative;"></div>
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
</div>

<script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
<script type="text/javascript">
    var wsServer = 'ws://127.0.0.1:9501';
    var websocket = new WebSocket(wsServer);
    websocket.onopen = function (evt) {
        console.log("Connected to WebSocket server.");
    };

    websocket.onclose = function (evt) {
        console.log("Disconnected");
    };

    websocket.onmessage = function (evt) {
        console.log('Retrieved data from server: ' + evt.data);
    };

    websocket.onerror = function (evt, e) {
        console.log('Error occured: ' + evt.data);
    };

    $("#submit").click(function () {
        var message = $("#message").val();
        if (message) {
            websocket.send(message);
        }
    });
</script>
@endsection
