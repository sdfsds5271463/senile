<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>test</title>
    </head>
    <body>
        @section('section1')  {{-- 引入子 section1 --}}
            testbase section1
        @show
        @yield('section2')  {{-- 呼叫子 section2 --}}

         {{-- @include('testbase') 這個可以引入任何 view --}} 
    </body>
</html>
