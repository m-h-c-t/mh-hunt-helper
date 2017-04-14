/*jslint browser:true */

(function () {
    'use strict';

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
            var journal = {};

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
                message = getStage(message, response, journal);

                if (!message) {
                    window.console.log("MHHH: Missing Info (will try better next hunt).");
                    return;
                }

                // Send to database
                $.post("https://mhhh.000webhostapp.com/", message)
                    .done(function (data) {
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

    function getStage(message, response, journal) {
        switch (response.user.location) {
            case "Labyrinth":
                message = getLabyrinthStage(message, response, journal);
                break;
            case "Fiery Warpath":
                message = getFieryWarpathStage(message, response, journal);
                break;
            case "Balack's Cove":
                message = getBalacksCoveStage(message, response, journal);
                break;
            // case "Seasonal Garden":
                // message = getSeasonalGardenStage(message, response, journal);
                // break;
            case "Living Garden":
                message = getLivingGardenStage(message, response, journal);
                break;
            case "Sand Dunes":
                message = getSandDunesStage(message, response, journal);
                break;
            case "Lost City":
                message = getLostCityStage(message, response, journal);
                break;
            case "Iceberg":
                message = getIcebergStage(message, response, journal);
                break;
        }

        return message;
    }

    function getLabyrinthStage(message, response, journal) {
        if (response.user.quests.QuestLabyrinth.status === "hallway") {
            message.stage = response.user.quests.QuestLabyrinth.hallway_name;
            // Remove first word (like Short)
            message.stage = message.stage.substr(message.stage.indexOf(" ") + 1);
        } else {
            // Not recording last hunt of a hallway and intersections at this time
            return;
        }
        return message;
    }

    function getFieryWarpathStage(message, response, journal) {
        if (response.user.viewing_atts.desert_warpath.wave) {
            message.stage = "Wave " + response.user.viewing_atts.desert_warpath.wave;
        }
        return message;
    }

    function getBalacksCoveStage(message, response, journal) {
        if (response.user.viewing_atts.tide) {
            var tide = response.user.viewing_atts.tide;
            message.stage = tide.substr(0, 1).toUpperCase() + tide.substr(1) + " Tide";
        }
        return message;
    }

    function getSeasonalGardenStage(message, response, journal) {
        switch (response.user.viewing_atts.season) {
            case "sr":
                message.stage = "Summer";
                break;
            case "":
                message.stage = "Fall";
                break;
            case "":
                message.stage = "Winter";
                break;
            case "":
                message.stage = "Spring";
                break;
        }
        return message;
    }
    
    function getLivingGardenStage(message, response, journal) {
        if (user.quests.QuestLivingGarden.minigame.bucket_state) {
            var bucket = user.quests.QuestLivingGarden.minigame.bucket_state;
            if (bucket === "filling") {
                message.stage = "Not pouring";
            } else {
                message.stage = "Pouring";
            }
        }
        return message;
    }
    
    function getSandDunesStage(message, response, journal) {
        if (user.quests.QuestSandDunes.minigame.has_stampede) {
            message.stage = "Stampede";
        } else {
            message.stage = "No Stampede";
        }
        return message;
    }
    
    function getLostCityStage(message, response, journal) {
        if (user.quests.QuestLostCity.minigame.is_cursed) {
            message.stage = "Cursed";
        } else {
            message.stage = "Not Cursed";
        }
        return message;
    }
    
    function getIcebergStage(message, response, journal) {
        if (user.quests.QuestIceberg.current_phase) {
            message.stage = user.quests.QuestIceberg.current_phase;
        }
        // switch (user.quests.QuestIceberg.current_phase) {
            // case "Treacherous Tunnels";
                // message.stage = "0-300ft";
                // break;
            // case "Brutal Bulwark";
                // message.stage = "301-600ft";
                // break;
            // case "Bombing Run";
                // message.stage = "601-1600ft";
                // break;
            // case "The Mad Depths";
                // message.stage = "1601-1800ft";
                // break;
            // case "Icewing's Lair";
                // message.stage = "1801-2000ft";
                // break;
            // default:
                // message.stage = "2000ft";
                // break;
        // }
        return message;
    }

    window.console.log("MH Hunt Helper v1.4 loaded! Good luck!");

}());
