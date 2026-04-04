@extends('testbaseView') {{-- 成為繼承者 --}}
@section('section1') {{-- 子區塊1 --}}
    test section1 ( {{ $t01 . " " . $uri }} ) <br>
    @parent <br> {{-- 呼叫母 section1 --}}
@stop

@section('section2') {{-- 子區塊2 --}}
    test section2 <br>
    @if(true) {{-- 邏輯用 @ 包裹 --}}
        @for($i=1; $i<=3; $i++)
            {{ $i }}
        @endfor
    @endif
@stop