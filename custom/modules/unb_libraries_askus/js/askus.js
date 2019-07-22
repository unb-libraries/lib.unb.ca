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
            $("#lh3-online a").click(function(event) {
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
            setInterval(lh3CheckPresence, 50000);
        }
    };
})(jQuery, Drupal);

var lh3CheckPresence = function () {
    let url = "https://ca.libraryh3lp.com/presence/jid/askus/chat.ca.libraryh3lp.com/js";
    let script = document.createElement("script");
    script.src = url + "?cb=lh3UpdatePresence";
    document.getElementsByTagName("head")[0].appendChild(script);
};
var lh3UpdatePresence = function () {
    let resource = jabber_resources[0];

    if (resource.show === "available" || resource.show === "chat") {
        jQuery("#lh3-online").show();
        jQuery("#lh3-away").hide();
        jQuery("#lh3-busy").hide();
        jQuery("#lh3-offline").hide();
    } else if (resource.show === "away") {
        jQuery("#lh3-online").hide();
        jQuery("#lh3-away").show();
        jQuery("#lh3-busy").hide();
        jQuery("#lh3-offline").hide();
    } else if (resource.show === "dnd") {
        jQuery("#lh3-online").hide();
        jQuery("#lh3-away").hide();
        jQuery("#lh3-busy").show();
        jQuery("#lh3-offline").hide();
    } else {
        // resource.show is 'unavailable', 'xa' or unknown.
        jQuery("#lh3-online").hide();
        jQuery("#lh3-away").hide();
        jQuery("#lh3-busy").hide();
        jQuery("#lh3-offline div[data-ch-id='hil_help']").html(getOfflineNote());
        jQuery("#lh3-offline").show();
    }
    jQuery(".requires-js").slideDown(250);
};

var getOfflineNote = function () {
    let note = "Ask Us is currently <strong>offline</strong>";
    let currentlyOpen = '', reopensData = '';

    let askUs = Drupal.calendarHours.models["hil_help"];
    if (typeof askUs !== 'undefined') {
        currentlyOpen = askUs.isOpenNow();
        reopensData = askUs.getReopensAt();
    }

    if (!currentlyOpen && reopensData) {
        // Re-open details available.
        let tomorrow = moment().add(1, "days").format("Y-MM-DD");
        let opensAt = moment(reopensData);
        let opensAtDate = opensAt.format("Y-MM-DD");
        if (opensAtDate < tomorrow) {
            note += ". Reopens at " + opensAt.format("hh:mm");
        } else if (opensAtDate > tomorrow) {
            note += ". Reopens on " + opensAt.format("dddd, hh:mm");
        } else {
            note += ". Reopens tomorrow at " + opensAt.format("hh:mm");
        }
    }
    return note + ".";
};
