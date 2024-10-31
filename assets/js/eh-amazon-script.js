jQuery(function($) {
    $(document).on('click', '#cancel_amazon_order', function () {
        document.cookie = "eh_amazon_access_token=; expires=Thu, 01 Jan 1970 00:00:00 GMT;";
    });
});