/**
 * Created by kirill on 11.11.16.
 */
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