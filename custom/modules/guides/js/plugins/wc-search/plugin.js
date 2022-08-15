CKEDITOR.plugins.add('wc-search', {
    requires: 'widget',
    icons: 'wc-search',

    init: function(editor) {
        editor.ui.addButton('wc-search', {
            label: 'UNB WorldCat',
            title: 'Add UNB WorldCat search box',
            command: 'wc-search',
            toolbar: 'lists'
        });
        CKEDITOR.document.appendStyleText('.cke_button__wc-search_label {display: inline;}');

        editor.widgets.add('wc-search', {
            button: 'Add a UNB WorldCat search box',
            template: '<div class="wc-search"><img src="/modules/custom/guides/img/wc-search-placeholder.png" /></div>',
            allowedContent: 'div(!wc-search); img[!src]',
            requiredContent: 'div(wc-search)',
            upcast: function(element) {
                return element.name == 'div' && element.hasClass('wc-search');
            },
        } );
    }
} );
