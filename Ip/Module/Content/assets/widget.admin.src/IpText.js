/**
 * @package ImpressPages
 *
 *
 */

function IpWidget_IpText(widgetObject) {
    this.widgetObject = widgetObject;

    this.manageInit = manageInit;
    this.prepareData = prepareData;


    function manageInit() {
        var instanceData = this.widgetObject.data('ipWidget');
        this.widgetObject.find('textarea').tinymce(ipTinyMceConfigMin);
//        this.widgetObject.find('textarea').tinymce({
//            script_url : ip.baseUrl + 'Ip/Module/Assets/assets/js/tiny_mce/tinymce.min.js',
//            theme: "modern",
//            add_unload_trigger: false,
//            schema: "html5",
//            inline: true,
//            toolbar: "undo redo",
//            statusbar: false
//        });

    }

    function prepareData() {

        var data = Object();

        data.text = this.widgetObject.find('textarea').html();
        $(this.widgetObject).trigger('preparedWidgetData.ipWidget', [ data ]);
    }

    

};


