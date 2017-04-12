(function () {
    'use strict';
    /*jslint browser:true */

    if (location.hostname.match('facebook.com')) {
        // TODO
        return;
    }

    if (!window.jQuery) {
        console.log("MHHH: Can't find jQuery, exiting.");
        return;
    }

    $(document).ajaxSuccess(function (event, xhr, ajaxOptions) {
     //   /* Method        */ ajaxOptions.type
     //   /* URL           */ ajaxOptions.url
     //   /* Response body */ xhr.responseText
     //   /* Request body  */ ajaxOptions.data

        //window.console.log(JSON.parse(xhr.responseText));

        //if (ajaxOptions.url !== "https://mhhh.000webhostapp.com/") {
        if (ajaxOptions.url === "https://www.mousehuntgame.com/managers/ajax/turns/activeturn.php") {
            var response = JSON.parse(xhr.responseText);
            var message = {};
            var journal;

            //window.console.log(response);

            if (response.active_turn && response.success && response.journal_markup != null && response.journal_markup.length > 0) {

                for (var i=0; i < response.journal_markup.length; i++) {
                    if (response.journal_markup[i].render_data.css_class.match(/(catchfailure|catchsuccess|attractionfailure)/) &&
                        response.journal_markup[i].render_data.css_class.match(/active/)) {
                        journal = response.journal_markup[i];
                        break;
                    }
                }

                if (!journal) {
                    window.console.log("MHHH: Missing Info (trap check or friend hunt).");
                    return;
                }

                message = getMainHuntInfo(message, response, journal);

                // Title
                // message.title = {};
                // message.title.name = response.title_name;
                // message.title.id = response.title_id;

                // Send to database
                $.post("https://mhhh.000webhostapp.com/", message)
                    .done(function (data) {
                        // window.console.log("wohooo:");
                        if (data) {
                            window.console.log(data);
                        }
                    });
            }
            else {
                window.console.log("MHHH: Missing Info (trap check or friend hunt).");
            }
        }
    });

    function getMainHuntInfo(message, response, journal) {

        // Entry ID
        message.entry_id = journal.render_data.entry_id;

        // Entry Timestamp
        message.entry_timestamp = journal.render_data.entry_timestamp;

        // User ID
        message.user_id = response.user.user_id;

        // Location (alt: response.user.location)
        message.location = {};
        message.location.name = response.user.location;
        message.location.id = response.user.environment_id;

        // Trap
        message.trap = {};
        message.trap.name = response.user.weapon_name.replace(/\ trap/i, '');
        message.trap.id = response.user.weapon_item_id;

        // Base
        message.base = {};
        message.base.name = response.user.base_name.replace(/\ base/i, '');
        message.base.id = response.user.base_item_id;

        // Charm
        message.charm = {};
        if (response.user.trinket_name) {
            message.charm.name = response.user.trinket_name.replace(/\ charm/i, '');
            message.charm.id = response.user.trinket_item_id;
        }

        // Cheese
        message.cheese = {};
        if (response.user.bait_name) {
            message.cheese.name = response.user.bait_name.replace(/\ cheese/i, '');
            message.cheese.id = response.user.bait_item_id;
        }

        // Shield (true / false)
        message.shield = response.user.has_shield;

        // Caught / Attracted / Mouse
        var outcome = journal.publish_data.attachment.name;
        var action = journal.render_data.css_class;
        if (action.match(/catchsuccess/)) {
            message.caught = 1;
            message.attracted = 1;
            message.mouse = outcome.replace(/i\ caught\ an?\ /i, '');
            message.mouse = message.mouse.replace(/(\ mouse)?\!/i, '');
        } else if (action.match(/catchfailure/)) {
            message.caught = 0;
            message.attracted = 1;
            message.mouse = outcome.replace(/i\ failed\ to\ catch\ an?\ /i, '');
            message.mouse = message.mouse.replace(/(\ mouse)?\./i, '');
        } else if (action.match(/attractionfailure/)) {
            message.caught = 0;
            message.attracted = 0;
        }

        return message;
    }

    window.console.log("MH Hunt Helper loaded! Good luck!");

}());
