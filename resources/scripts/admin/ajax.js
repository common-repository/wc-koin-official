export class Ajax 
{
    do_ajax(url, data, redirect = false) {

        jQuery.ajax({
            type: "POST",
            url: url, 
            data: data, 

            beforeSend: () => {
                jQuery(".sync").addClass("sync-activate")
            },

            success: (response) => {

                jQuery(".sync").removeClass("sync-activate")

                if ( response.success !== true ) return;

                if (response.redirect || redirect) {
                    if (response.redirect) redirect = response.redirect;
                    alert( response.message );
                    window.location = window.location.origin + redirect;
                }
            },

        })
        .fail( (response) => {

            if ( response.responseJSON ) {
                response = response.responseJSON;
                alert( response.message );
                window.location = window.location.origin + redirect;
            }
        });
    }
}