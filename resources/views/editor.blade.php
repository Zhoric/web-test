<!DOCTYPE html>
<html lang="en">
<head>
    <title>Ace Editor Demo</title>
    <style type="text/css">
        #editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
    </style>
</head>
<body>
<div id="editor"></div>
<script src="{{ URL::asset('js/ace.js') }}" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("editor"); // теперь обращаться к редактору будем через editor
    editor.getSession().setMode("ace/mode/javascript");
    editor.setTheme("ace/theme/monokai");
    // Далее весь экшон будет проходить тут!
</script>
</body>
</html>