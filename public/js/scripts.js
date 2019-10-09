/**
 * Created by nourdine on 10/6/17.
 */
//Facebook Share
// this loads the Facebook API
(function (d, s, id) {
    let js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) { return; }
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

window.fbAsyncInit = function () {
    let appId = '800663563367596';
    FB.init({
        appId: appId,
        xfbml: true,
        version: 'v2.9'
    });
};

function validate(elements_id, model) {
    if (elements_id === '#pinsbookForm') {
        // var date_input = $('input[name="pins[' + model + '][date]"]');
        // console.log(date_input.val());
        //
        // if (date_input.val() === '') {
        //     alert('Please fill in th date time for pin #' + model);
        //     return false;
        // }
        $('#pinsbookForm').submit();
        return;
    }

    if (jQuery(elements_id + ' input[type=checkbox]:checked').length) {
        $(elements_id).submit();
        return;
    }
    alert('Please check at least one ' + model);
    return false;

}