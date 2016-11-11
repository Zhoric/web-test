<!DOCTYPE html>
<html lang="en">
<head>
    <title>Ace Editor Demo</title>

</head>
<body>

<div id="editor"></div>



<script src="{{ URL::asset('js/aui.js') }}" type="text/javascript" charset="utf-8"></script>


<script>

    YUI().use(
            'aui-ace-editor',
            function(Y) {
                new Y.AceEditor(
                        {
                            boundingBox: '#editor',
                            mode: 'javascript'

                        }
                ).render();
            }
    );


</script>





</body>
</html>