var package = {};


jQuery(function() {

    var ajax_url = url.ajaxurl;

    jQuery.getJSON(ajax_url, { 'action' : 'get_token', 'post_id' : url.post_id })
        .success(function(data) {
            console.log('got token: '+ data.token);
            transactApi.setToken(data.token);
        })
        .fail(function(data) {
            console.log('Failed to get Transact token');
        });




});

function PurchasePopUpClosed(popup, event) {

    var ajax_url = url.ajaxurl;
    console.log('PurchasePopUpClosed');
    console.log(popup);

    if (event && event.data) {
        console.log(event.data);
        var validation_data = event.data;
        validation_data.action = 'get_purchased_content';
        validation_data.post_id = url.post_id;

        var jqxhr = jQuery.getJSON(ajax_url, validation_data)
            .done(function(resp_data) {
                console.log('Success Response data:', resp_data);
                //$('#paid_content').html(resp_data.content);
            })
            .fail(function(resp_data) {
                console.log('Error Response data:', resp_data);
                //$('#paid_content').html('purchase failed');
            })
            .always(function() {
                console.log( "finished" );
            });

    }
}



