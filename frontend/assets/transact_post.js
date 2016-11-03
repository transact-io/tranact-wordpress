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


function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}


function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

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

                // Set or Update Cookie
                var cookie = getCookie('wp_transact_');
                if (cookie != '') {
                    cookie = JSON.parse(cookie);

                    var new_cookie = [
                            validation_data.post_id,
                            [
                                resp_data.decoded.uid
                            ]
                    ];
                    cookie.push(new_cookie);
                    setCookie('wp_transact_', JSON.stringify(cookie), 365);

                } else {
                    var new_cookie = {};
                    new_cookie['id']  = validation_data.post_id;
                    new_cookie['uid'] = resp_data.decoded.uid;
                    var cookies = [];
                    cookies.push(new_cookie);

                    setCookie('wp_transact_', JSON.stringify(cookies), 365);
                }
                // Reload
                location.reload();
            })
            .fail(function(resp_data) {
                console.log('Error Response data:', resp_data);
                jQuery('#button_purchase').html('purchase failed');
            })
            .always(function() {
                console.log( "finished" );
            });
    }
}
