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
            lh3UpdatePresence();

            // Presence check - 60s polling.
            setInterval(lh3UpdatePresence, 60000);
        }
    };
})(jQuery, Drupal);

var lh3UpdatePresence = function () {
    jQuery.ajax ({
        crossDomain: true,
        dataType: "xml",
        type: 'GET',
        url: 'https://ca.libraryh3lp.com/presence/jid/askus/chat.ca.libraryh3lp.com/xml',
        success: function(data) {
            let resource = jQuery(data).find('resource:first');
            let resourceShow = resource.attr('show');
            if (resourceShow === "available" || resourceShow === "chat") {
                jQuery("#lh3-online").show();
                jQuery("#lh3-away").hide();
                jQuery("#lh3-busy").hide();
                jQuery("#lh3-offline").hide();
            } else if (resourceShow === "away") {
                jQuery("#lh3-online").hide();
                jQuery("#lh3-away").show();
                jQuery("#lh3-busy").hide();
                jQuery("#lh3-offline").hide();
            } else if (resourceShow === "dnd") {
                jQuery("#lh3-online").hide();
                jQuery("#lh3-away").hide();
                jQuery("#lh3-busy").show();
                jQuery("#lh3-offline").hide();
            } else {
                // resource show attribute is 'unavailable', 'xa' or unknown.
                jQuery("#lh3-online").hide();
                jQuery("#lh3-away").hide();
                jQuery("#lh3-busy").hide();
                jQuery("#lh3-offline .offline-msg").text(getOfflineNote());
                jQuery("#lh3-offline").show();
            }
            jQuery(".requires-js").slideDown(250);
        },
        error: function() {
            jQuery("#lh3-online").hide();
            jQuery("#lh3-away").hide();
            jQuery("#lh3-busy").hide();
            jQuery("#lh3-offline .offline-msg").text("Ask Us is experiencing technical difficulties.");
            jQuery("#lh3-offline").show();
            jQuery('.requires-js').slideDown(250);
        }
    });
};

var getOfflineNote = function () {
    let note = "Ask Us is currently offline";
    let currentlyOpen = '', reopensData = '';

    let calendarRHD = calendarHours.collection.get("hil_help");
    if (typeof calendarRHD != 'undefined') {
        currentlyOpen = calendarRHD.isOpenNow();
        reopensData = calendarRHD.getReopensAt();
    }

    if (!currentlyOpen && reopensData) {
        // Re-open details available.
        let tomorrow = moment().add(1, "days").format("Y-MM-DD");
        let opensAt = moment(reopensData);
        let opensAtDate = opensAt.format("Y-MM-DD");
        if (opensAtDate < tomorrow) {
            note += ". Chat service will re-open today at " + opensAt.format("h:mma");
        } else if (opensAtDate > tomorrow) {
            note += ". Chat service will re-open on " + opensAt.format("dddd, h:mma");
        } else {
            note += ". Chat service will re-open tomorrow at " + opensAt.format("h:mma");
        }
    }
    return note + ".";
};
