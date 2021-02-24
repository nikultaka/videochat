jQuery(document).ready(function () {
    let $ = jQuery;

    // Show selected table when user selects one in the style selector
    $('#tradeline-table-style').on('change', function () {
        this.value == "style1" ? $('[name="tradeline-thumb-style-1"]').show() : $('[name="tradeline-thumb-style-1"]').hide();
        this.value == "style2" ? $('[name="tradeline-thumb-style-2"]').show() : $('[name="tradeline-thumb-style-2"]').hide();
        this.value == "style3" ? $('[name="tradeline-thumb-style-3"]').show() : $('[name="tradeline-thumb-style-3"]').hide();
    });
});