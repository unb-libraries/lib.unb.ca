(function ($, Drupal) {
    Drupal.behaviors.libChosen = {
        attach: function() {
            // Do not apply on Mobile: match chosen.jquery.js v.1.8.7 exceptions.
            if (/iP(od|hone)/i.test(window.navigator.userAgent) || /IEMobile/i.test(window.navigator.userAgent) ||
                /Windows Phone/i.test(window.navigator.userAgent) || /BlackBerry/i.test(window.navigator.userAgent) ||
                /BB10/i.test(window.navigator.userAgent) || /Android.*Mobile/i.test(window.navigator.userAgent)) {
                return false;
            }

            // Invoke chosen() on main content select tags with options.
            // See: https://harvesthq.github.io/chosen/options.html.
            $("select.custom-chosen-select").chosen({
                display_disabled_options: false,
                width: "100%",
            });
            // Apply bootstrap 4 form-control class to chosen div.
            $(".chosen-container-single").addClass("form-control");
            // Resolve original select missing display:none inline style issue.
            $("select.custom-chosen-select").addClass("d-none");
        }
    }
})(jQuery, Drupal);
