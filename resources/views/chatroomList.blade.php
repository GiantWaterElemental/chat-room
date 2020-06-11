@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @for ($i = 0; $i < $count; $i++)
            @if ($i > 0 && ($i - 1) % 3 == 0)
                <div class="row justify-content-center">
            @endif
            <div class="col-md-4 col-xs-1">
                <table class="table table-success table-bordered">
                    <thead class="">
                        <tr>
                            <th>
                                {{ $list[$i]->name }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="">
                                <div></div>
                                <img src="{{ $list[$i]->imgpath }}" class="img-fluid rounded">
                            </td>
                        </tr>
                        <tr>
                            <td class="">
                                当前有{{ $list[$i]->count }}人正在聊天
                                <a class="btn btn-success float-right" href="{{ route('chatroom', ['id' => $list[$i]->room_id]) }}">加入</a>
                            </td>
                        </tr>
                    </tbody>
                </div>
            </div>
            @if ($i > 0 && $i % 3 == 0)
                </div>
            @endif
        @endfor
    </div>
</div>
@endsection
