<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <title>Ace Editor Demo</title>

</head>
<body>

<div id="editor"></div>
<input type="button" value="Отправить код" onclick="sendCode()">


<script src="{{ URL::asset('js/aui.js') }}" type="text/javascript" charset="utf-8"></script>
<script src="{{URL::asset('js/codeEditor/init.js') }}" type="text/javascript" charset="utf-8"></script>
<script src="{{URL::asset('js/codeEditor/sendCode.js') }}" type="text/javascript" charset="utf-8"></script>
<script src="{{ URL::asset('js/jquery-3.1.1.js')}}"></script>





</body>
</html>