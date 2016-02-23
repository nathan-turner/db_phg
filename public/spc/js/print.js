$(function() {

    //--------------------------------------------------------------------------
    // Calendar Events Print Handlers
    //--------------------------------------------------------------------------

    $("#print-date").html(SPC.Date.getUserLongDate(SPC.currentDate));

    $("#print-start-date, #print-end-date")
        .datepicker({
            dateFormat: SPC.Date.dateConverter.getDatePickerDateFormat()
        })
        .bind("change", function() {
            showPrintEvents();
        });

    function drawPrintEvents(events, rangeStartDate, rangeEndDate) {

        var eventList = "<h3>"
                            + SPC.Date.getUserLongDate(SPC.Date.dateConverter.userToCore($("#print-start-date").val()))
                            + " - "
                            + SPC.Date.getUserLongDate(SPC.Date.dateConverter.userToCore($("#print-end-date").val())) +
                        "</h3>"
                        + "<br />"
                        + "<ul id='print-event-list'>",
            currentEvent,
            title,
            eventDate,
            eventTime,
            eventOwner;

        for (var date in events) {
            if ((date < rangeStartDate) || (date > rangeEndDate)) {
                continue;
            }

            eventList += "<li>"
                      + "<h2 class='group-date'>" + SPC.Date.getUserLongDate(date) + "</h2>";
            eventList += "<ul>";

            for (var i = 0, c = events[date].length; i < c; i++) {
                currentEvent = events[date][i];

                title = currentEvent["title"] || SPC.translate("(No title)");

                eventDate = SPC.Date.dateConverter.coreToUser(currentEvent["start_date"]);

                if (currentEvent["type"] == "multi_day") {
                    eventDate = SPC.Date.dateConverter.coreToUser(currentEvent["start_date"])
                                + " - "
                                + SPC.Date.dateConverter.coreToUser(currentEvent["end_date"]);
                }

                eventTime = SPC.Date.convertTimeFormat(currentEvent["start_time"])
                            + " - "
                            + SPC.Date.convertTimeFormat(currentEvent["end_time"]);

                if (currentEvent["start_time"] == "00:00" && currentEvent["end_time"] == "00:00") {
                    eventTime = "all-day";
                }

                eventOwner = ""
                if (currentEvent["created_by"]) {
                    eventOwner = "<span class='bold'>" + SPC.translate("Created by") + ":</span> " + currentEvent["created_by"];
                }

                eventList += "<li class='ui-helper-clearfix'>"
                                //calendar
                                + "<h3 class='title'>" + title + "</h3>"
                                + "<div class='calendar'\
                                        style='\
                                            color: " + SPC.printCals[currentEvent["cal_id"]]["color"] + ";'>"
                                                + SPC.printCals[currentEvent["cal_id"]]["name"]
                                + "</div>"
                                //date
                                + "<div class='date'>" + eventDate + ", " + eventTime + "</div>"
                                + "<div class='description'>" + currentEvent["description"] + "</div>";

                if (currentEvent["location"]) {
                    eventList += "<div class='location'>\
                                      <strong class='location-label'>" + SPC.translate('location') + ":</strong> "
                                      + currentEvent["location"] +
                                  "</div>";
                }

                if (currentEvent["repeat_type"] != "none") {
                    eventList += "<div class='repeat'>\
                                      <strong class='repeat-label'>" + SPC.translate('repeat') + ":</strong> "
                                      + currentEvent["repeat_type"] +
                                  "</div>";
                }

                if (currentEvent["created_by"] != SPC.USERNAME) {
                    eventList += "<div class='created-by'>\
                                      <strong class='created-by-label'>" + SPC.translate('Created by') + ":</strong> "
                                      + currentEvent["created_by"] +
                                  "</div>";
                }

                eventList += "</li>";
            }

            eventList += "</ul>\
                        </li>";
        }

        eventList += "</ul>";

        $("#print-body").html(eventList);
    }

    function showPrintEvents(startDate, endDate) {
        startDate = startDate || $("#print-start-date").val();
        endDate = endDate || $("#print-end-date").val();

        startDate = SPC.Date.dateConverter.userToCore(startDate);
        endDate = SPC.Date.dateConverter.userToCore(endDate);

        SPC.Calendar.getEvents("print", [startDate, endDate], SPC.printCalIds, function(view, viewDates, events) {
            drawPrintEvents(events.all, startDate, endDate);
        });
    }

    $("#print-calendar").bind("click", function() {
        window.print();
    });

    //--------------------------------------------------------------------------
    // Contacts Print Handlers
    //--------------------------------------------------------------------------
    //localhost/spc-contacts/print.php?app=calendar&startDate=2012-06-16&endDate=2012-06-16&cals=136%2C147%2C167%2C148%2C168%2C152
    function printContact(contactId) {
        SPC.ajax("contacts/contact/getContact", [contactId], function(res) {
            var contact = $.extend({showText: true}, res.contact);
            if (contact["has_photo"] == 1) {
                contact["photoSrc"] = "system/apps/contacts/files/contact-photos/" + contact["email"] + ".png";
            }

            //get contact's groups
            var contactGroups = [SPC.t("All Contacts")];
            SPC.Array.foreach(contact.groups, function(i, v) {
                contactGroups.push(v["name"]);
            });

            contact.groupTags = contactGroups.join(", ");

            var tmpl = SPC.tmpl("#contact-form-tmpl", contact);
            $("#contact-form").html(tmpl);
        });
    }

    //init print
    $("#print-container div[data-app='" + SPC.printApp + "']").show();
    if (SPC.printApp == "calendar") {
        showPrintEvents();
    } else if (SPC.printApp == "contacts") {
        printContact(SPC.printAppParams["contactId"]);
    }
});