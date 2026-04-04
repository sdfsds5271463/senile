<x-mail::message>
# Introduction

{{ $msg }}

<x-mail::button url='{{ $url }}'>
網站訪問
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
