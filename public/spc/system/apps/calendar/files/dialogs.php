<!--calendar-dialogs-->
<div id="calendar-dialogs" class="spc-app-dialogs-wrapper hidden">
    <!--START: add-event-dialog -->
    <div id="add-event-dialog" class="dialog">
        <table id="add-event-dialog-table" style="width: 650px;">
            <tbody>
                <tr>
                    <td><?php echo Spc::translate('calendar'); ?>: </td>
                    <td>
                        <select style="width: 150px;" id="add-event-dialog-calendar"></select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo Spc::translate('date'); ?>:
                    </td>
                    <td>
                        <span class="group">
                            <input type="text" class="date-input" id="add-event-dialog-start-date" />
                            <input type="text" id="add-event-dialog-start-time" class="time-input dialog-click-visible" />
                        </span>
                        &ndash;
                        <span class="group">
                            <input type="text" id="add-event-dialog-end-date" class="date-input" />
                            <input type="text" id="add-event-dialog-end-time" class="time-input dialog-click-visible" />
                        </span>
                        <span class="group">
                            <input type="checkbox" id="add-event-dialog-all-day" />
                            <label for="add-event-dialog-all-day">
                                <?php echo Spc::translate('All day'); ?>
                            </label>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo Spc::translate('title'); ?>:
                    </td>
                    <td>
                        <input type="text" id="add-event-dialog-title" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo Spc::translate('location'); ?>:
                    </td>
                    <td>
                        <input type="text" id="add-event-dialog-location" />
                    </td>
                </tr>
                <tr>
                    <td class="v-a-top">
                        <?php echo Spc::translate('description'); ?>:
                    </td>
                    <td>
                        <textarea id="add-event-dialog-description"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <table id="add-event-dialog-details-table">
            <tbody class="hidden">
                <tr>
                    <td style="width: 100px; padding-top: 10px;" class="v-a-top">
                        <?php echo Spc::translate('image'); ?>:
                    </td>
                    <td>
                        <span id="add-event-dialog-image-wrapper" class="hidden">
                            <img src="" id="add-event-dialog-image" class="f-left" />
                            <span id="add-event-dialog-image-remove" class="pointer ui-icon ui-icon-close f-left"></span>
                            <?php if (SPC::getUserPref('wysiwyg', 'calendar') == '1'): ?>
                                <span
                                    class="image-copy-to-rte pointer ui-icon ui-icon-copy f-left"
                                    title="<?php echo SPC::translate('Copy to RTE'); ?>">
                                </span>
                            <?php endif; ?>
                        </span>
                        <button id="add-event-dialog-select-image" class="jq-ui-button">
                            <?php echo Spc::translate('select'); ?>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo Spc::translate('availability'); ?>:
                    </td>
                    <td>
                        <div id="add-event-dialog-availability">
                            <input
                                type="radio"
                                id="add-event-dialog-availability-available"
                                name="add-event-dialog-availability" />
                            <label class="ui-corner-left" for="add-event-dialog-availability-available">
                                <?php echo Spc::translate('available'); ?>
                            </label>

                            <input
                                type="radio"
                                id="add-event-dialog-availability-busy"
                                name="add-event-dialog-availability"
                                checked="checked" />
                            <label for="add-event-dialog-availability-busy">
                                <?php echo Spc::translate('busy'); ?>
                            </label>
                        </div>

                        <div id="add-event-dialog-privacy">
                            <span class="event-dialog-privacy-title">
                                <?php echo Spc::translate('privacy'); ?>:
                            </span>
                            <input
                                type="radio"
                                id="add-event-dialog-privacy-private"
                                name="add-event-dialog-privacy" />
                            <label class="ui-corner-left" for="add-event-dialog-privacy-private">
                                <?php echo Spc::translate('private'); ?>
                            </label>

                            <input
                                type="radio"
                                id="add-event-dialog-privacy-public"
                                name="add-event-dialog-privacy"
                                checked="checked" />
                            <label for="add-event-dialog-privacy-public">
                                <?php echo Spc::translate('public'); ?>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="v-a-top"><?php echo Spc::translate('invitation'); ?>: </td>
                    <td>
                        <button class="add-event-guests jq-ui-button">
                            <?php echo Spc::translate('Add Guests'); ?>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="v-a-top"><?php echo Spc::translate('reminders'); ?>: </td>
                    <td>
                        <div id="add-event-dialog-reminders"></div>
                    </td>
                </tr>
            <tbody>
        </table>

    </div>
    <!--END: add-event-dialog -->

    <!--START: edit-event-dialog -->
    <div id="edit-event-dialog" class="dialog">
        <table id="edit-event-dialog-table" style="width: 650px;">
            <tbody>
                <tr>
                    <td style="width: 100px;"><?php echo Spc::translate('calendar'); ?>: </td>
                    <td>
                        <select style="width: 150px;" id="edit-event-dialog-calendar"></select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px;"><?php echo Spc::translate('date'); ?>: </td>
                    <td>
                        <span class="group">
                            <input type="text" id="edit-event-dialog-start-date" class="date-input"/>
                            <input type="text" id="edit-event-dialog-start-time" class="time-input dialog-click-visible" />
                        </span>
                        &ndash;
                        <span class="group">
                            <input type="text" id="edit-event-dialog-end-date" class="date-input" />
                            <input type="text" id="edit-event-dialog-end-time" class="time-input dialog-click-visible" />
                        </span>
                        <span class="group">
                            <input type="checkbox" id="edit-event-dialog-all-day" />
                            <label for="edit-event-dialog-all-day">
                                <?php echo Spc::translate('All day'); ?>
                            </label>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><?php echo Spc::translate('title'); ?>: </td>
                    <td><input type="text" id="edit-event-dialog-title" /></td>
                </tr>
                <tr>
                    <td><?php echo Spc::translate('location'); ?>: </td>
                    <td><input type="text" id="edit-event-dialog-location" /></td>
                </tr>
                <tr>
                    <td class="v-a-top">
                        <?php echo Spc::translate('description'); ?>:
                    </td>
                    <td>
                        <textarea style="height: 70px;" id="edit-event-dialog-description"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <table id="edit-event-dialog-details-table">
            <tbody class="hidden">
                <tr>
                    <td style="width: 100px; padding-top: 10px;" class="v-a-top">
                        <?php echo Spc::translate('image'); ?>:
                    </td>
                    <td>
                        <span id="edit-event-dialog-image-wrapper" class="hidden">
                            <img src="" id="edit-event-dialog-image" class="f-left" />
                            <span id="edit-event-dialog-image-remove" class="pointer ui-icon ui-icon-close f-left"></span>
                            <?php if (SPC::getUserPref('wysiwyg', 'calendar') == '1'): ?>
                                <span
                                    class="image-copy-to-rte pointer ui-icon ui-icon-copy f-left"
                                    title="<?php echo SPC::translate('Copy to RTE'); ?>">
                                </span>
                            <?php endif; ?>
                        </span>
                        <input
                            id="edit-event-dialog-select-image"
                            type="button"
                            class="jq-ui-button"
                            value="<?php echo Spc::translate('select'); ?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo Spc::translate('availability'); ?>:
                    </td>
                    <td>
                        <div id="edit-event-dialog-availability">
                            <input
                                type="radio"
                                id="edit-event-dialog-availability-available"
                                name="edit-event-dialog-availability" />

                            <label class="ui-corner-left" for="edit-event-dialog-availability-available">
                                <?php echo Spc::translate('available'); ?>
                            </label>

                            <input
                                type="radio"
                                id="edit-event-dialog-availability-busy"
                                name="edit-event-dialog-availability"
                                checked="checked" />

                            <label for="edit-event-dialog-availability-busy">
                                <?php echo Spc::translate('busy'); ?>
                            </label>
                        </div>

                        <div id="edit-event-dialog-privacy">
                            <span class="event-dialog-privacy-title">
                                <?php echo Spc::translate('privacy'); ?>:
                            </span>
                            <input type="radio" id="edit-event-dialog-privacy-private" name="edit-event-dialog-privacy" />
                            <label class="ui-corner-left" for="edit-event-dialog-privacy-private">
                                <?php echo Spc::translate('private'); ?>
                            </label>

                            <input
                                type="radio"
                                id="edit-event-dialog-privacy-public"
                                name="edit-event-dialog-privacy"
                                checked="checked" />
                            <label for="edit-event-dialog-privacy-public">
                                <?php echo Spc::translate('public'); ?>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="v-a-top">
                        <?php echo Spc::translate('invitation'); ?>:
                    </td>
                    <td>
                        <button class="add-event-guests jq-ui-button">
                            <?php echo Spc::translate('Add Guests'); ?>
                        </button>
                        <div id="edit-event-dialog-edit-guests-wrapper">
                            <span class="bold">
                                <?php echo Spc::translate('going'); ?>?
                            </span>
                            <div id="edit-event-dialog-edit-guests-radio" class="jq-ui-buttonset">
                                <input
                                    type="radio"
                                    name="edit-event-dialog-response-invitation"
                                    id="edit-event-dialog-response-invitation-yes"
                                    value="yes" />
                                <label for="edit-event-dialog-response-invitation-yes">
                                    <?php echo Spc::translate('yes'); ?>
                                </label>

                                <input
                                    type="radio"
                                    name="edit-event-dialog-response-invitation"
                                    id="edit-event-dialog-response-invitation-no"
                                    value="no" />
                                <label for="edit-event-dialog-response-invitation-no">
                                    <?php echo Spc::translate('no'); ?>
                                </label>

                                <input
                                    type="radio"
                                    name="edit-event-dialog-response-invitation"
                                    id="edit-event-dialog-response-invitation-maybe"
                                    value="maybe" />
                                <label for="edit-event-dialog-response-invitation-maybe">
                                    <?php echo Spc::translate('maybe'); ?>
                                </label>
                            </div>
                            <button id="edit-event-dialog-see-guests" class="jq-ui-button">
                                <?php echo Spc::translate('See Guests'); ?>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr id="edit-event-dialog-created-by-row" class="hidden">
                    <td>
                        <?php echo Spc::translate('Created by'); ?>:
                    </td>
                    <td>
                        <span id="edit-event-dialog-created-by"></span>
                    </td>
                </tr>
                <tr id="edit-event-dialog-modified-by-row" class="hidden">
                    <td>
                        <?php echo Spc::translate('Modified by'); ?>:
                    </td>
                    <td>
                        <span id="edit-event-dialog-modified-by"></span>
                    </td>
                </tr>
                <tr>
                    <td class="v-a-top"><?php echo Spc::translate('reminders'); ?>: </td>
                    <td>
                        <div id="edit-event-dialog-reminders"></div>
                    </td>
                </tr>
            <tbody>
        </table>
    </div>
    <!--END: add-event-dialog -->

    <!--repeat-event-dialog-->
    <div id="repeat-event-dialog" class="dialog">
        <table id="repeat-event-dialog-table">
            <tbody>
                <tr>
                    <td>
                        <?php echo Spc::translate('repeats'); ?>:
                    </td>
                    <td>
                        <select id="repeat-event-dialog-repeat-type">
                            <option value="none">
                                <?php echo Spc::translate('none'); ?>
                            </option>
                            <option value="daily">
                                <?php echo Spc::translate('daily'); ?>
                            </option>
                            <option value="weekly">
                                <?php echo Spc::translate('weekly'); ?>
                            </option>
                            <option value="monthly">
                                <?php echo Spc::translate('monthly'); ?>
                            </option>
                            <option value="yearly">
                                <?php echo Spc::translate('yearly'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo Spc::translate('Repeat every'); ?>:
                    </td>
                    <td>
                        <select id="repeat-event-dialog-interval">
                            <?php
                                $intervals = '';
                                foreach (range(1, 30) as $interval) {
                                    $intervals .= "<option value='$interval'>$interval</option>";
                                }
                                echo $intervals;
                            ?>
                        </select>
                        <span id="repeat-event-dialog-interval-time-unit">
                            <?php echo Spc::translate('days', false); ?>
                        </span>
                    </td>
                </tr>
                <tr id="repeat-event-dialog-weekly-repeat-days-row">
                    <td>
                        <?php echo Spc::translate('Repeats on'); ?>:
                    </td>
                    <td>
                        <div id="repeat-event-dialog-weekly-repeat-days" class="jq-ui-buttonset">
                            <?php
                                $weekDays = SpcCalendar::getWeekDays();
                                $options = '';
                                foreach ($weekDays as $dayIndex => $dayName) {
                                    $options .= "<input
                                                    type='checkbox'
                                                    id='repeat-event-dialog-weekly-repeat-day-$dayIndex'
                                                    value='$dayIndex' />
                                                <label for='repeat-event-dialog-weekly-repeat-day-$dayIndex'>"
                                                    . Spc::substr(Spc::translate($dayName), 0, 1) .
                                                '</label>';
                                }
                                echo $options;
                            ?>
                        </div>
                    </td>
                </tr>
                <tr id="repeat-event-dialog-monthly-repeat-options-row">
                    <td>

                    </td>
                    <td>
                        <div id="repeat-event-dialog-monthly-repeat-options">
                            <div class="group">
                                <input
                                    id="repeat-event-dialog-monthly-repeat-options-day-of-the-month-radio"
                                    name="repeat-event-dialog-monthly-repeat-options"
                                    type="radio"
                                    checked="checked" />
                                <label for="repeat-event-dialog-monthly-repeat-options-day-of-the-month-radio">
                                    <?php echo Spc::translate('day'); ?>
                                </label>
                                <input
                                    id="repeat-event-dialog-monthly-repeat-options-day-of-the-month"
                                    class="number-input-s"
                                    type="text" />
                            </div>

                            <div class="group">
                                <input
                                    id="repeat-event-dialog-monthly-repeat-options-day-of-the-week-radio"
                                    name="repeat-event-dialog-monthly-repeat-options"
                                    type="radio" />
                                <select id="repeat-event-dialog-monthly-repeat-options-day-of-the-week-weekindex">
                                    <option value="1"><?php echo Spc::translate('first'); ?></option>
                                    <option value="2"><?php echo Spc::translate('second'); ?></option>
                                    <option value="3"><?php echo Spc::translate('third'); ?></option>
                                    <option value="4"><?php echo Spc::translate('fourth'); ?></option>
                                    <option value="5"><?php echo Spc::translate('last'); ?></option>
                                </select>
                                <select id="repeat-event-dialog-monthly-repeat-options-day-of-the-week-dayindex">
                                    <?php
                                        $weekDays = SpcCalendar::getWeekDays();
                                        $options = '';
                                        foreach ($weekDays as $dayIndex => $dayName) {
                                            $options .= "<option value='$dayIndex'>" . Spc::translate($dayName) . "</option>";
                                        }
                                        echo $options;
                                    ?>
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo Spc::translate('Starts on'); ?>:
                    </td>
                    <td>
                        <input type="text" id="repeat-event-dialog-starts-on" class="date-input" disabled="disabled" />
                    </td>
                </tr>
                <tr id="repeat-event-dialog-end-date-row">
                    <td class="v-a-top">
                        <?php echo Spc::translate('ends'); ?>:
                    </td>
                    <td>
                        <div class="group">
                            <input
                                id="repeat-event-dialog-never-radio"
                                type="radio"
                                name="repeat-event-dialog-end"
                                checked="checked" />
                            <label for="repeat-event-dialog-never-radio">
                                <?php echo Spc::translate('never'); ?>
                            </label>
                        </div>
                        <div class="group">
                            <input
                                id="repeat-event-dialog-after-radio"
                                type="radio"
                                name="repeat-event-dialog-end" />
                            <label for="repeat-event-dialog-after-radio">
                                <?php echo Spc::translate('after'); ?>
                            </label>
                            <input
                                id="repeat-event-dialog-count"
                                class="number-input-s"
                                type="text"
                                disabled="disabled" />
                            <?php echo Spc::translate('occurences', false); ?>
                        </div>
                        <div class="group">
                            <input
                                id="repeat-event-dialog-end-date-radio"
                                type="radio"
                                name="repeat-event-dialog-end" />
                            <label for="repeat-event-dialog-end-date-radio">
                                <?php echo Spc::translate('on'); ?>
                            </label>
                            <input
                                id="repeat-event-dialog-end-date"
                                class="date-input"
                                type="text"
                                disabled="disabled" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!--/repeat-event-dialog-->

    <!--add-guests-dialog-->
    <div id="add-guests-dialog">
        <table id="add-guests-dialog-table">
            <thead>
                <tr>
                    <form id="add-guests-dialog-submit-invitee-form">
                        <td>
                            <input type="text" class="jq-ui-fix-hidden-input" />
                            <input
                                type="text"
                                id="add-guests-dialog-guest-username"
                                class="spc-border-radius-s"
                                value="<?php echo Spc::translate('email'); ?>" />
                        </td>
                        <td>
                            <input
                                id="add-guests-dialog-add-guest"
                                class="jq-ui-button"
                                type="submit"
                                value="<?php echo Spc::translate('add'); ?>" />
                        </td>
                    </form>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <!--/add-guests-dialog-->

    <!--START: search-dialog -->
    <div id="search-dialog" class="dialog">
        <div>
            <form id="search-form">
                <input type="text" id="search_box" style="width: 300px;" />
                <input type="button" id="do-search" class="jq-ui-button" value="<?php echo Spc::translate('search'); ?>" />
            </form>
        </div>
        <div id="search-result-box" style="padding: 3px;"></div>
    </div>
    <!--END: search-dialog -->

    <!--START: create-calendar-dialog -->
    <div id="create-calendar-dialog" class="dialog">
        <table>
            <tbody>
                <tr>
                    <td style="width: 20%; padding: 5px;">
                        <label for="create-calendar-dialog-name"><?php echo Spc::translate('Calendar Name'); ?></label>
                    </td>
                    <td>
                        <input id="create-calendar-dialog-name" type="text" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; padding: 5px;">
                        <label for="create-calendar-dialog-url">URL <span class="text-s">(iCal)</span></label>
                    </td>
                    <td>
                        <input id="create-calendar-dialog-url" type="text" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; vertical-align: top; padding: 5px;">
                        <label for="create-calendar-dialog-description"><?php echo Spc::translate('description'); ?></label>
                    </td>
                    <td>
                        <textarea id="create-calendar-dialog-description" style="width: 100%; height: 70px;"></textarea>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20%; padding: 5px;">
                        <label for="create-calendar-dialog-color"><?php echo Spc::translate('color'); ?></label>
                    </td>
                    <td>
                        <input id="create-calendar-dialog-color" class="spc-color" type="text" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="vertical-align: middle; padding: 5px;">
                        <?php echo SpcCalendar::getColors(true); ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="search_result_box" style="padding: 3px;"></div>
    </div>
    <!--END: create-calendar-dialog -->

    <!--START: edit-calendar-dialog -->
    <div id="edit-calendar-dialog" class="dialog">
        <div id="edit-calendar-dialog-tabs" style="height: 96%;">
            <ul>
                <li>
                    <a href="#edit-calendar-dialog-tabs-1" class="edit-calendar-dialog-tabs-1">
                        <?php echo Spc::translate('Calendar Details'); ?>
                    </a>
                </li>
                <li class="hidden">
                    <a href="#edit-calendar-dialog-tabs-2" class="edit-calendar-dialog-tabs-2">
                        <?php echo Spc::translate('share'); ?>
                    </a>
                </li>
                <li class="hidden">
                    <a href="#edit-calendar-dialog-tabs-3" class="edit-calendar-dialog-tabs-3">
                        <?php echo Spc::translate('reminders'); ?>
                    </a>
                </li>
                <li class="hidden">
                    <a href="#edit-calendar-dialog-tabs-4" class="edit-calendar-dialog-tabs-4">
                        <?php echo Spc::translate('Reminder Messages'); ?>
                    </a>
                </li>
            </ul>

            <div id="edit-calendar-dialog-tabs-1">
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 20%;">
                                <?php echo Spc::translate('Calendar Name'); ?>
                            </td>
                            <td>
                                <input id="edit-calendar-dialog-name" type="text" style="width: 100%;" />
                            </td>
                        </tr>
                        <tr id="edit-calendar-dialog-tabs-1-owner-row">
                            <td style="width: 20%;">
                                <?php echo Spc::translate('owner'); ?>
                            </td>
                            <td>
                                <span id="edit-calendar-dialog-owner"></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                URL <span class="refresh-subscribed-cal pointer ui-icon ui-icon-refresh absolute"></span>
                            </td>
                            <td>
                                <span id="edit-calendar-dialog-url"></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 20%;">
                                <?php echo Spc::translate('description'); ?>
                            </td>
                            <td>
                                <textarea id="edit-calendar-dialog-description" style="width: 100%; height: 70px;"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                <?php echo Spc::translate('color'); ?>
                            </td>
                            <td>
                                <input id="edit-calendar-dialog-color" class="spc-color" type="text" style="width:100%;" />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="vertical-align: middle; padding: 5px; width: 100%;">
                                <?php echo SpcCalendar::getColors(true); ?>
                            </td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td></td>
                            <td style="text-align: right; padding: 20px 0 0 0;">

                                <input
                                    id="edit-calendar-dialog-tabs-1-hide"
                                    style="width: 65px;"
                                    type="button"
                                    value="<?php echo Spc::translate('hide'); ?>"
                                    class="ui-state-default ui-corner-all" />

                                <input
                                    id="edit-calendar-dialog-tabs-1-delete"
                                    style="width: 65px;"
                                    type="button"
                                    value="<?php echo Spc::translate('delete'); ?>"
                                    class="ui-state-default ui-corner-all" />

                                <input
                                    id="edit-calendar-dialog-tabs-1-save"
                                    style="width: 65px;"
                                    type="button"
                                    value="<?php echo Spc::translate('save'); ?>"
                                    class="ui-state-default ui-corner-all" />

                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div id="edit-calendar-dialog-tabs-2">
                <table id="calendar-share-table">
                    <caption>
                        <?php echo Spc::translate('Share this calendar'); ?>
                        <span id="share-free-busy" class="spc-text-button text-s f-right">
                            <?php echo Spc::translate('Share My Free/Busy Information'); ?>
                        </span>
                    </caption>
                    <thead>
                        <tr>
                            <th>
                                <?php echo Spc::translate('username'); ?>
                            </th>
                            <th>
                                <?php echo Spc::translate('permission'); ?>
                            </th>
                            <th>
                                <?php echo Spc::translate('delete'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                    $userRole = $_SESSION['spcUserPrefs']['role'];
                                    $userId = $_SESSION['spcUserPrefs']['id'];

                                    $adminId = ($userRole == 'admin')
                                                ? $userId
                                                : $_SESSION['spcUserPrefs']['admin_id'];

                                    if (CAL_SHARE_SHOW_USERNAME == 'none') {
                                        echo '<input id="new-share-username" type="text" style="width: 100%;" />';
                                    }

                                    if (CAL_SHARE_SHOW_USERNAME == 'group') {
                                        echo '<select id="new-share-username" type="text" style="width: 100%;">';

                                        $where = "(admin_id = $adminId OR admin_id = 1) AND id != $userId";

                                        if ($userRole == 'super') {
                                            $where = "1";
                                        }

                                        if ($userRole == 'user') {
                                            $where = "id != $userId AND (admin_id = $adminId OR id = $adminId)";
                                        }

                                        $sql = "SELECT username FROM spc_users WHERE $where ORDER BY username";

                                        if (!$rs = mysql_query($sql)) {
                                            exit(mysql_error());
                                        }

                                        $options = '';
                                        while (list($username) = mysql_fetch_row($rs)) {
                                            $options .= '<option value="' . $username . '">' . $username . '</option>';
                                        }

                                        echo $options . '</select>';
                                    }

                                    if (CAL_SHARE_SHOW_USERNAME == 'all') {
                                        echo '<select id="new-share-username" type="text" style="width: 100%;">';
                                        $sql = "SELECT username FROM spc_users WHERE id != $userId";
                                        if (!$rs = mysql_query($sql)) {
                                            exit(mysql_error());
                                        }

                                        $options = '';
                                        while (list($username) = mysql_fetch_row($rs)) {
                                            $options .= '<option value="' . $username . '">' . $username . '</option>';
                                        }

                                        echo $options . '</select>';
                                    }
                                ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <select id="new-share-permission">
                                    <option value="see"><?php echo Spc::translate('See all events'); ?></option>
                                    <option value="change"><?php echo Spc::translate('Make changes to events'); ?></option>
                                </select>
                                <span
                                    id="new-share"
                                    style="border: 0; width: 50px; margin: 3px; padding: 7px;"
                                    class="ui-state-default ui-corner-all pointer" >
                                      <?php echo Spc::translate('share'); ?>
                                </span>
                            </td>
                            <td></td>
                        </tr>
                        <tr> <td colspan="3"> <hr /> </td> </tr>
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>

            <div id="edit-calendar-dialog-tabs-3">
                <strong>
                      <?php echo Spc::translate('Default Reminders'); ?>
                </strong>
                <hr class="title-bottom-hr" />
                <div id="edit-calendar-dialog-reminders"></div>
                <div style="margin: 15px 0">
                    <span class="edit-calendar-dialog-save-reminders ui-state-default ui-corner-all button">
                        <?php echo Spc::translate('save'); ?>
                    </span>
                    &nbsp;
                    <span class="edit-calendar-dialog-save-reminders-for-all ui-state-default ui-corner-all button">
                        <?php echo Spc::translate('Save for all calendars'); ?>
                    </span>
                </div>
            </div>

            <div id="edit-calendar-dialog-tabs-4">
                <strong>
                    <?php echo Spc::translate('Default Reminder Messages'); ?>
                </strong>

                <hr class="title-bottom-hr" />

                <strong>
                    <?php echo Spc::translate('email'); ?>
                </strong>
                <textarea id="edit-calendar-dialog-reminder-msg-email"></textarea>

                <br /><br />

                <strong>
                    <?php echo Spc::translate('popup'); ?>
                </strong>

                <textarea id="edit-calendar-dialog-reminder-msg-popup"></textarea>

                <div style="margin: 15px 0">
                    <span class="edit-calendar-dialog-save-reminder-message ui-state-default ui-corner-all button">
                        <?php echo Spc::translate('save'); ?>
                    </span>
                    &nbsp;
                    <span class="edit-calendar-dialog-save-reminder-message-for-all ui-state-default ui-corner-all button">
                        <?php echo Spc::translate('Save for all calendars'); ?>
                    </span>
                </div>
                <br />
                <strong>
                    <?php echo Spc::translate('placeholders'); ?>
                </strong>
                <hr />
                <ul>
                    <li>%calendar%</li>
                    <li>%start-date%</li>
                    <li>%end-date%</li>
                    <li>%start-time%</li>
                    <li>%end-time%</li>
                    <li>%title%</li>
                    <li>%location%</li>
                    <li>%description%</li>
                </ul>
            </div>
        </div>
    </div>
    <!--END: edit-calendar-dialog -->

    <!--START: calendars-settings-dialog -->
    <div id="calendars-settings-dialog" class="dialog">

        <div id="calendars-settings-dialog-tabs" style="">
            <ul>
                <li>
                    <a href="#calendars-settings-dialog-tabs-1" class="calendars-settings-dialog-tabs-1">
                        <?php echo Spc::translate('My Calendars'); ?>
                    </a>
                </li>
                <li>
                    <a href="#calendars-settings-dialog-tabs-2" class="calendars-settings-dialog-tabs-2">
                        <?php echo Spc::translate('Other Calendars'); ?>
                    </a>
                </li>
                <?php if (SPC_USER_ROLE != 'super'): ?>
                    <li>
                        <a href="#calendars-settings-dialog-tabs-3" class="calendars-settings-dialog-tabs-3">
                            <?php echo Spc::translate('Group Calendars'); ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div id="calendars-settings-dialog-tabs-1">
                <table id="my-calendars-settings-table">
                    <thead>
                        <tr>
                            <th><?php echo Spc::translate('calendar'); ?></th>
                            <th><?php echo Spc::translate('Show in list'); ?></th>
                            <th><?php echo Spc::translate('public'); ?></th>
                            <th><?php echo Spc::translate('export'); ?></th>
                            <th>RSS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="calendars-settings-dialog-tabs-2">
                <table id="other-calendars-settings-table">
                    <thead>
                        <tr>
                            <th><?php echo Spc::translate('calendar'); ?></th>
                            <th><?php echo Spc::translate('Show in list'); ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <?php if (SPC_USER_ROLE != 'super'): ?>
                <div id="calendars-settings-dialog-tabs-3">
                    <table id="group-calendars-settings-table">
                        <thead>
                            <tr>
                                <th><?php echo Spc::translate('calendar'); ?></th>
                                <th><?php echo Spc::translate('Show in list'); ?></th>
                                <th><?php echo Spc::translate('public'); ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!--END: calendars-settings-dialog -->
    <div id="calendar-import-export-dialog" class="dialog" style="overflow: hidden;">
        <div id="calendar-import-export-dialog-tabs" style="height: 95%;">
            <ul>
                <li>
                    <a href="#calendar-import-export-dialog-import-tab" class="calendars-settings-dialog-import-tab">
                        <?php echo Spc::translate('import'); ?>
                    </a>
                </li>
                 <li>
                    <a href="#calendar-import-export-dialog-export-tab" class="calendars-settings-dialog-export-tab">
                        <?php echo Spc::translate('export'); ?>
                    </a>
                </li>
            </ul>

            <div id="calendar-import-export-dialog-import-tab" >
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <?php echo Spc::translate('File'); ?>: <sub style="font-size: 10px; color: #444;">(.ics)</sub>
                            </td>
                            <td style="text-align: right;">
                                <form
                                    id="upload-form"
                                    action="upload.php"
                                    method="post"
                                    enctype="multipart/form-data"
                                    target="upload-target">
                                        <input name="spc-ical-file" type="file" autocomplete="off" class="file-input" />
                                        <input type="hidden" name="import-ical" value="true" />
                                        <input type="hidden" name="userId" value="<?php echo SPC_USERID; ?>" />
                                        <input id="calendars-settings-dialog-tabs-3-cal-id" type="hidden" name="calId" />
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo Spc::translate('calendar'); ?>:
                            </td>
                            <td style="text-align: right;">
                                <select id="calendars-settings-dialog-tabs-3-calendar" style="width: 220px;"></select>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;" colspan="2">
                                <input id="calendars-settings-dialog-tabs-3-import-calendar" type="button" value="Import" style="" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="calendar-import-export-dialog-export-tab">
                <br />
                <?php echo Spc::translate('calendar'); ?>:
                <select id="calendar-import-export-dialog-export-calendar" style="width: 200px;"></select>
                <a id="calendar-import-export-dialog-export-tab-export-link"
                   href="#" class="ui-state-default ui-corner-all button"
                   style="text-decoration: none;">
                    <?php echo Spc::translate('export'); ?>
                </a>
            </div>
        </div>
    </div>

    <div id="edit-account-dialog" class="dialog">
        <table>
            <tbody>
                <tr>
                    <td><?php echo Spc::translate('username'); ?></td>
                    <td>
                        <input
                            type="text"
                            id="edit-account-dialog-username"
                            disabled="disabled"
                            value="<?php echo SPC_USERNAME; ?>" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo Spc::translate('role'); ?></td>
                    <td>
                        <input
                            type="text"
                            id="edit-account-dialog-role"
                            disabled="disabled"
                            value="<?php echo SPC_USER_ROLE; ?>" />
                    </td>
                </tr>
                <tr>
                    <td>Full Name</td>
                    <td>
                        <input
                            type="text"
                            id="edit-account-dialog-full-name"
                            value="<?php echo Spc::getUserPref('full_name'); ?>" />
                    </td>
                </tr>
                <tr>
                    <td>Company or Organization</td>
                    <td>
                        <input
                            type="text"
                            id="edit-account-dialog-company"
                            value="<?php echo Spc::getUserPref('company'); ?>" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo Spc::translate('email'); ?></td>
                    <td>
                        <input
                            type="text"
                            id="edit-account-dialog-email"
                            value="<?php echo SPC_USER_EMAIL; ?>" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="change-password-dialog" class="dialog">
        <table>
            <tbody>
                <tr>
                    <td><?php echo Spc::translate('old password'); ?></td>
                    <td>
                        <input type="password" id="change-password-dialog-old-password" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo Spc::translate('new password'); ?></td>
                    <td>
                        <input type="password" id="change-password-dialog-new-password" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo Spc::translate('retype new password'); ?></td>
                    <td>
                        <input type="password" id="change-password-dialog-re-new-password" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- image-dialogs -->
    <div id="image-upload-dialog" class="dialog">
        <form
            id="upload-image-form"
            action="upload.php"
            method="post"
            enctype="multipart/form-data"
            target="upload-image-target">
                <input
                    type="file"
                    name="spc-image-file"
                    id="spc-image-file"
                    autocomplete="off" />
                <input type="hidden" name="image-upload" value="true" />
        </form>
    </div>

    <div id="image-select-dialog" class="dialog" style="overflow: auto !important;"></div>
    <!-- /image-dialogs -->

    <!-- year-view-event-summary-dialog -->
    <div id="spc-year-cal-event-list-dialog" class="dialog"></div>
    <!-- /year-view-event-summary-dialog -->

    <!-- calendar-settings-dialog -->
    <div id="system-settings-dialog" class="dialog">
        <?php Spc::requireFile('files/settings.php', 'core'); ?>
    </div>
    <!-- /calendar-settings-dialog -->

    <!-- manage-users-dialog -->
    <div id="manage-users-dialog" class="dialog"></div>
    <!-- /manage-users-dialog -->

    <!--user-schedules-dialog-->
    <div id="user-schedules-dialog">

        <div id="user-schedules-dialog-schedule-scrollers">
            <div id="user-schedules-dialog-horizontal-scroller">
                <div id="user-schedules-dialog-horizontal-scroller-left" class="ui-icon ui-icon ui-icon-carat-1-w"></div>
                <div id="user-schedules-dialog-horizontal-scroller-right" class="ui-icon ui-icon ui-icon-carat-1-e"></div>
            </div>

            <div id="user-schedules-dialog-vertical-scroller">
                <div id="user-schedules-dialog-vertical-scroller-up" class="ui-icon ui-icon ui-icon-carat-1-n"></div>
                <div id="user-schedules-dialog-vertical-scroller-down" class="ui-icon ui-icon ui-icon-carat-1-s"></div>
            </div>
        </div>

        <table id="user-schedules-dialog-pager">
            <tbody>
                <tr>
                    <td class="page prev">
                        <span class="ui-icon ui-icon ui-icon-carat-1-w"></span>
                    </td>
                    <td class="page next">
                        <span class="ui-icon ui-icon ui-icon-carat-1-e"></span>
                    </td>
                    <td class="page today">
                        <div><?php echo Spc::translate('today'); ?></div>
                    </td>
                    <td style="width: 200px; cursor: default;">
                        <div id="user-schedules-dialog-pager-date"></div>
                    </td>
                    <td>
                        <input type="text" class="jq-ui-fix-hidden-input" />
                        <input id="user-schedules-datepicker" class="spc-border-radius-s date-input" type="text" />
                    </td>
                </tr>
            </tbody>
        </table>

        <div id="user-schedules-dialog-grid-wrapper" class="ui-helper-clearfix">
            <div id="user-schedules-dialog-grid-wrapper-left">
                <div id="user-schedules-dialog-grid-username">
                    <?php echo Spc::translate('username'); ?>
                </div>
                <div id="user-schedules-dialog-grid-usernames-wrapper"></div>
            </div>
            <div id="user-schedules-dialog-grid-wrapper-right">
                <div id="user-schedules-dialog-grid-time"></div>
                <div id="user-schedules-dialog-grid-timeslots-wrapper">
                    <table id="user-schedules-dialog-grid-timeslots">
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--/user-schedules-dialog-->

    <!--share-free-busy-dialog-->
    <div id="share-free-busy-dialog">
        <span class="group">
            <?php echo Spc::translate('username'); ?>:
            <input id="share-free-busy-dialog-username" type="text" />
            <input
                type="button"
                id="share-free-busy-dialog-share"
                class="jq-ui-button"
                value="<?php echo Spc::translate('share'); ?>" />
        </span>
        <table id="share-free-busy-list">
            <thead>
                <tr>
                    <th>
                        <?php echo Spc::translate('username'); ?>
                    </th>
                    <th>
                        <?php echo Spc::translate('delete'); ?>
                    </th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <!--/share-free-busy-dialog-->

    <!-- user-permissions-dialog -->
    <div id="user-permissions-dialog" class="dialog">
        <table>
            <thead>
                <tr>
                    <td></td>
                    <td>
                        <div class="jq-ui-buttonset" id="user-permissions-dialog-global-check">
                            <input
                                type="radio"
                                checked="checked"
                                name="user-permissions-dialog-global-check"
                                id="user-permissions-dialog-check-all-permissions"
                                value="1" />
                            <label for="user-permissions-dialog-check-all-permissions">
                                <?php echo Spc::substr(Spc::translate('yes'), 0, 1); ?>
                            </label>

                            <input
                                type="radio"
                                name="user-permissions-dialog-global-check"
                                id="user-permissions-dialog-uncheck-all-permissions"
                                value="0" />
                            <label for="user-permissions-dialog-uncheck-all-permissions">
                                <?php echo Spc::substr(Spc::translate('no'), 0, 1); ?>
                            </label>
                        </div>
                    </td>
                </tr>
            </thead>

            <tbody>
                <?php if (SPC_USER_ROLE === 'super'): ?>
                <tr class="admin">
                    <td><?php echo Spc::translate('Create users') ?>:</td>
                    <td>
                        <div class="jq-ui-buttonset permission" data-permission="create-users">
                            <input
                                type="radio"
                                checked="checked"
                                name="user-permissions-dialog-create-users"
                                id="user-permissions-dialog-create-users-yes"
                                value="1" />
                            <label for="user-permissions-dialog-create-users-yes">
                                <?php echo Spc::substr(Spc::translate('yes'), 0, 1); ?>
                            </label>

                            <input
                                type="radio"
                                name="user-permissions-dialog-create-users"
                                id="user-permissions-dialog-create-users-no"
                                value="0" />
                            <label for="user-permissions-dialog-create-users-no">
                                <?php echo Spc::substr(Spc::translate('no'), 0, 1); ?>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr class="admin">
                    <td><?php echo Spc::translate('Delete users') ?>:</td>
                    <td>
                        <div class="jq-ui-buttonset permission" data-permission="delete-users">
                            <input
                                type="radio"
                                checked="checked"
                                name="user-permissions-dialog-delete-users"
                                id="user-permissions-dialog-delete-users-yes"
                                value="1" />
                            <label for="user-permissions-dialog-delete-users-yes">
                                <?php echo Spc::substr(Spc::translate('yes'), 0, 1); ?>
                            </label>

                            <input
                                type="radio"
                                name="user-permissions-dialog-delete-users"
                                id="user-permissions-dialog-delete-users-no"
                                value="0" />
                            <label for="user-permissions-dialog-delete-users-no">
                                <?php echo Spc::substr(Spc::translate('no'), 0, 1); ?>
                            </label>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>

                <tr>
                    <td><?php echo Spc::translate('Create calendars') ?>:</td>
                    <td>
                        <div class="jq-ui-buttonset permission" data-permission="create-calendars">
                            <input
                                type="radio"
                                checked="checked"
                                name="user-permissions-dialog-create-calendars"
                                id="user-permissions-dialog-create-calendars-yes"
                                value="1" />
                            <label for="user-permissions-dialog-create-calendars-yes">
                                <?php echo Spc::substr(Spc::translate('yes'), 0, 1); ?>
                            </label>

                            <input
                                type="radio"
                                name="user-permissions-dialog-create-calendars"
                                id="user-permissions-dialog-create-calendars-no"
                                value="0" />
                            <label for="user-permissions-dialog-create-calendars-no">
                                <?php echo Spc::substr(Spc::translate('no'), 0, 1); ?>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><?php echo Spc::translate('Edit calendars') ?>:</td>
                    <td>
                        <div class="jq-ui-buttonset permission" data-permission="edit-calendars">
                            <input
                                type="radio"
                                checked="checked"
                                name="user-permissions-dialog-edit-calendars"
                                id="user-permissions-dialog-edit-calendars-yes"
                                value="1" />
                            <label for="user-permissions-dialog-edit-calendars-yes">
                                <?php echo Spc::substr(Spc::translate('yes'), 0, 1); ?>
                            </label>

                            <input
                                type="radio"
                                name="user-permissions-dialog-edit-calendars"
                                id="user-permissions-dialog-edit-calendars-no"
                                value="0" />
                            <label for="user-permissions-dialog-edit-calendars-no">
                                <?php echo Spc::substr(Spc::translate('no'), 0, 1); ?>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><?php echo Spc::translate('Delete calendars') ?>:</td>
                    <td>
                        <div class="jq-ui-buttonset permission" data-permission="delete-calendars">
                            <input
                                type="radio"
                                checked="checked"
                                name="user-permissions-dialog-delete-calendars"
                                id="user-permissions-dialog-delete-calendars-yes"
                                value="1" />
                            <label for="user-permissions-dialog-delete-calendars-yes">
                                <?php echo Spc::substr(Spc::translate('yes'), 0, 1); ?>
                            </label>

                            <input
                                type="radio"
                                name="user-permissions-dialog-delete-calendars"
                                id="user-permissions-dialog-delete-calendars-no"
                                value="0" />
                            <label for="user-permissions-dialog-delete-calendars-no">
                                <?php echo Spc::substr(Spc::translate('no'), 0, 1); ?>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><?php echo Spc::translate('Share calendars') ?>:</td>
                    <td>
                        <div class="jq-ui-buttonset permission" data-permission="share-calendars">
                            <input
                                type="radio"
                                checked="checked"
                                name="user-permissions-dialog-share-calendars"
                                id="user-permissions-dialog-share-calendars-yes"
                                value="1" />
                            <label for="user-permissions-dialog-share-calendars-yes">
                                <?php echo Spc::substr(Spc::translate('yes'), 0, 1); ?>
                            </label>

                            <input
                                type="radio"
                                name="user-permissions-dialog-share-calendars"
                                id="user-permissions-dialog-share-calendars-no"
                                value="0" />
                            <label for="user-permissions-dialog-share-calendars-no">
                                <?php echo Spc::substr(Spc::translate('no'), 0, 1); ?>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><?php echo Spc::translate('Create events') ?>:</td>
                    <td>
                        <div class="jq-ui-buttonset permission" data-permission="create-events">
                            <input
                                type="radio"
                                checked="checked"
                                name="user-permissions-dialog-create-events"
                                id="user-permissions-dialog-create-events-yes"
                                value="1" />
                            <label for="user-permissions-dialog-create-events-yes">
                                <?php echo Spc::substr(Spc::translate('yes'), 0, 1); ?>
                            </label>

                            <input
                                type="radio"
                                name="user-permissions-dialog-create-events"
                                id="user-permissions-dialog-create-events-no"
                                value="0" />
                            <label for="user-permissions-dialog-create-events-no">
                                <?php echo Spc::substr(Spc::translate('no'), 0, 1); ?>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><?php echo Spc::translate('Edit events') ?>:</td>
                    <td>
                        <div class="jq-ui-buttonset permission" data-permission="edit-events">
                            <input
                                type="radio"
                                checked="checked"
                                name="user-permissions-dialog-edit-events"
                                id="user-permissions-dialog-edit-events-yes"
                                value="1" />
                            <label for="user-permissions-dialog-edit-events-yes">
                                <?php echo Spc::substr(Spc::translate('yes'), 0, 1); ?>
                            </label>

                            <input
                                type="radio"
                                name="user-permissions-dialog-edit-events"
                                id="user-permissions-dialog-edit-events-no"
                                value="0" />
                            <label for="user-permissions-dialog-edit-events-no">
                                <?php echo Spc::substr(Spc::translate('no'), 0, 1); ?>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><?php echo Spc::translate('Delete events') ?>:</td>
                    <td>
                        <div class="jq-ui-buttonset permission" data-permission="delete-events">
                            <input
                                type="radio"
                                checked="checked"
                                name="user-permissions-dialog-delete-events"
                                id="user-permissions-dialog-delete-events-yes"
                                value="1" />
                            <label for="user-permissions-dialog-delete-events-yes">
                                <?php echo Spc::substr(Spc::translate('yes'), 0, 1); ?>
                            </label>

                            <input
                                type="radio"
                                name="user-permissions-dialog-delete-events"
                                id="user-permissions-dialog-delete-events-no"
                                value="0" />
                            <label for="user-permissions-dialog-delete-events-no">
                                <?php echo Spc::substr(Spc::translate('no'), 0, 1); ?>
                            </label>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!--keyboard-shortcuts-dialog-->
    <div id="keyboard-shortcuts-dialog">
        <table id="keyboard-shortcuts-docs-table">
            <thead>
                <tr>
                    <th>Shortcut Key</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" class="part">Views</td>
                </tr>
                <tr>
                    <td>
                        <strong>0</strong>
                    </td>
                    <td>
                        <div>Opens 'Resources' view. <br />
                            (press <strong>v</strong> for vertical mode and <strong>h</strong> for horizontal mode)

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>1</strong> or <strong>d</strong>
                    </td>
                    <td>
                        <div>Opens 'Day' view

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>2</strong> or <strong>w</strong>
                    </td>
                    <td>
                        <div>Opens 'Week' view

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>3</strong> or <strong>m</strong>
                    </td>
                    <td>
                        <div>Opens 'Month' view

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>4</strong> or <strong>a</strong>
                    </td>
                    <td>
                        <div>
                            Opens 'Agenda' view

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>5</strong> or <strong>x</strong>
                    </td>
                    <td>
                        <div>Opens 'Custom' view

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>6</strong> or <strong>y</strong>
                    </td>
                    <td>
                        <div>
                            Opens 'Year' view

                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="part">Navigation</td>
                </tr>
                <tr>
                    <td>
                        <strong>Left arrow</strong>
                    </td>
                    <td>
                        <div>
                            Previous date range

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Right arrow</strong>
                    </td>
                    <td>
                        <div>
                            Next date range

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Up arrow</strong>
                    </td>
                    <td>
                        <div>
                            Jump to 'Today'

                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="part">Actions</td>
                </tr>
                <tr>
                    <td>
                        <strong>c</strong>
                    </td>
                    <td>
                        <div>
                            Create event
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>Ctrl+e</strong>
                    </td>
                    <td>
                        <div>Open/Close event details
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>Ctrl+x</strong>
                    </td>
                    <td>
                        <div>
                            Create a new calendar, Create a new contact group
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="part">Application</td>
                </tr>
                <tr>
                    <td>
                        <strong>Ctrl+s</strong> or <strong>Ctrl+return</strong>
                        <br />
                        <h2>(SmartSave)</h2>
                    </td>
                    <td>
                        <div>
                            These shortcuts are valid for dialogs with buttons like "ok", "save", "done", "create", "edit", etc.
                            <h3>Examples</h3>
                            <h3>When you press "Ctrl+s" or "Ctrl+return"</h3>
                            <ul class="circle">
                                <li>on "Create Event Dialog" this shortcut will create a new event.</li>
                                <li>on "Edit Event Dialog" this shortcut will save the current event.</li>
                                <li>on "Create User Dialog" this shortcut will create a new user.</li>
                                <li>on "Create Calendar Dialog" this shortcut will create a new calendar.</li>
                                <li>on "Edit Calendar Dialog" this shortcut will save the current calendar options.</li>
                                <li>on "Calendar Settings Dialog" this shortcut will save the calendar settings.</li>
                                <li>...</li>
                            </ul>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>Ctrl+delete</strong> or <strong>Ctrl+backspace</strong>
                        <br />
                        <h2>(SmartDelete)</h2>
                    </td>
                    <td>
                        <div>
                            Triggers the delete button on current dialog or current operation.
                            For example it deletes event if you press on Edit Event Dialog open or
                            deletes the current contact if you are on Contacts tab.
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>r</strong>
                    </td>
                    <td>
                        <div>
                            Refresh current application, Calendar, Contacts, etc.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>p</strong>
                    </td>
                    <td>
                        <div>
                            Print current application

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>s</strong>
                    </td>
                    <td>
                        <div>
                            Application settings

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>u</strong>
                    </td>
                    <td>
                        <div>Manage users

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Escape</strong>
                    </td>
                    <td>
                        <div>Close current dialog or trigger cancel button on current operation

                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Ctrl+q</strong>
                    </td>
                    <td>
                        <div>Logout

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!--/keyboard-shortcuts-dialog-->

    <!-- display-event-dialog -->
    <div id="display-event-dialog" class="dialog" style="overflow: auto !important;">
        <table id="display-event-dialog-table" style="width: 100%;">
            <tbody>
                <tr>
                    <td>
                        <span class="export-ical-event ui-icon ui-icon-calendar pointer f-right"></span>
                        <div id="display-event-dialog-date"></div>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="display-event-dialog-description"></div>
                    </td>
                </tr>
                <tr id="display-event-dialog-image-parent">
                    <td>
                        <img id="display-event-dialog-image" src="" style="width: 100%; height: 100%;"/>
                    </td>
                </tr>
                <tr id="display-event-dialog-location-repeat-parent">
                    <td>
                        <div id="display-event-dialog-repeat"></div>
                    </td>
                </tr>
                <tr id="display-event-dialog-location-parent">
                    <td>
                        <div id="display-event-dialog-location"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /display-event-dialog -->

    <div id="week-cal-settings-dialog" class="dialog">
        <table>
            <tbody>
                <tr>
                    <td>
                        <label for="week-cal-settings-dialog-start-time"><?php echo Spc::t('start time'); ?></label>
                    </td>
                    <td>
                        <select id="week-cal-settings-dialog-start-time"></select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="week-cal-settings-dialog-end-time"><?php echo Spc::t('end time'); ?></label>
                    </td>
                    <td>
                        <select id="week-cal-settings-dialog-end-time"></select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="repeat-event-delete-dialog"></div>
</div>
<!-- /calendar-dialogs -->