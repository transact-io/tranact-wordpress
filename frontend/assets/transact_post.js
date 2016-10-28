var package = {};


jQuery(function() {

    var ajax_url = url.ajaxurl;
    var input_data = {
        'action'  : 'get_token',
    };
    jQuery.get(ajax_url, input_data).done(function(data){

        console.log(data);
    });



    /**jQuery.getJSON(url.url, { 'action' : 'getToken' })
        .done(function(data) {
            console.log('got token: '+ data.token);
            transactApi.setToken(data.token);

        })
        .fail(function(resp_data) {
            console.log('Error Response data:', resp_data);
            return;
        });**/
});





function PurchasePopUpClosed(popup, event) {
    console.log('PurchasePopUpClosed');
    console.log(popup);

    if (event && event.data) {
        console.log(event.data);
        var validation_data = event.data;
        validation_data.action = 'getPurchasedContent';

        var jqxhr = $.getJSON("demo-api.php", validation_data)
            .done(function(resp_data) {
                console.log('Success Response data:', resp_data);
                $('#paid_content').html(resp_data.content);
            })
            .fail(function(resp_data) {
                console.log('Error Response data:', resp_data);
                $('#paid_content').html('purchase failed');
            })
            .always(function() {
                console.log( "finished" );
            });

    }
}