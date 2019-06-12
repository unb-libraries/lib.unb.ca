/**
 * @file
 * Drupal behavior to attach AskUs script.
 * Source: https://docs.libraryh3lp.com/html-code-samples.
 */

(function ($, Drupal) {
    Drupal.behaviors.AskUs = {
        attach: function (context, settings) {
            // Show iff javascript is enabled.
            $("#lh3-noscript").hide();

            // Pop-up chat window
            $("#lh3-chat a").click(function(event) {
                var url = $(this).attr("href");
                var windowName = "Ask Us Chat";
                var windowAttributes = "height=560,width=400,menubar=no," +
                    "resizable=yes,scrollbars=yes,status=yes,toolbar=no";
                window.open(url, windowName, windowAttributes);
                event.preventDefault();
                event.stopPropagation();
                return false;
            });
            // Presence check with 30s refresh.
            lh3CheckPresence();
            setInterval(lh3CheckPresence, 30000);
        }
    };
})(jQuery, Drupal);

var lh3CheckPresence = function () {
    var url = "https://ca.libraryh3lp.com/presence/jid/askus/" +
        "chat.ca.libraryh3lp.com/js";
    var script = document.createElement("script");
    script.src = url + "?cb=lh3UpdatePresence";
    document.getElementsByTagName("head")[0].appendChild(script);
};
var lh3UpdatePresence = function () {
    var resource = jabber_resources[0];
    if (resource.show === "available" || resource.show === "chat") {
        jQuery("#lh3-away").hide();
        jQuery("#lh3-busy").hide();
        jQuery("#lh3-offline").hide();
    } else if (resource.show === "unavailable" || resource.show==="xa") {
        jQuery("#lh3-away").hide();
        jQuery("#lh3-busy").hide();
        jQuery("#lh3-offline").show();
    } else if (resource.show === "away") {
        jQuery("#lh3-away").show();
        jQuery("#lh3-busy").hide();
        jQuery("#lh3-offline").hide();
    } else if (resource.show === "dnd") {
        jQuery("#lh3-away").hide();
        jQuery("#lh3-busy").show();
        jQuery("#lh3-offline").hide();
    }
    jQuery(".requires-js").slideDown(250);
};
