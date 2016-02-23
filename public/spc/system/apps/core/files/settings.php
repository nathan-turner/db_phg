<table id="settings_table" cellspacing="0">
    <tbody>
        <tr class="title">
            <td colspan="2"><?php echo Spc::t('application') ?></td>
        </tr>

        <tr>
            <td>
                <?php echo Spc::translate('language'); ?>:
            </td>
            <td>
                <select id="language">
                   <?php
                       foreach (Spc::getLanguages() as $langId => $langTitle) {
                           if ($langId == SPC_USER_LANG) {
                               echo '<option value="' . $langId . '" selected="selected">' . $langTitle . '</option>';
                           } else {
                               echo '<option value="' . $langId . '">' . $langTitle . '</option>';
                           }
                       }
                   ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>
               <?php echo Spc::translate('Time Zone'); ?>:
            </td>
            <td>
                <select id="timezone">
                    <?php
                        foreach (Spc::getTimezones() as $zone => $tz) {
                            if ($tz == SPC_USER_TIMEZONE) {
                                echo '<option value="' . $tz . '" selected="selected">' . $zone . '</option>';
                            } else {
                                echo '<option value="' . $tz . '">' . $zone . '</option>';
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                <?php echo Spc::translate('theme'); ?>:
            </td>
            <td>
                <select id="theme">
                    <?php
                        $themes = Spc::getAppThemes();
                        $userTheme = Spc::getUserPref('theme');
                        foreach ($themes as $theme) {
                            $selected = '';
                            if ($theme == $userTheme) {
                                $selected = 'selected="selected"';
                            }
                            echo "<option value='{$theme}' {$selected}>{$theme}</option>";
                        }
                    ?>
                </select>
            </td>
        </tr>

        <tr class="title">
            <td colspan="2"><?php echo Spc::t('calendar') ?></td>
        </tr>

        <tr>
            <td>
                <?php echo Spc::translate('Short Date Format'); ?>:
            </td>
            <td>
                <select id="shortdate_format">
                    <?php
                        $endOfYearTs = mktime(0, 0, 0, 12, date('t'));
                        $userShortdateFormat = Spc::getUserPref('shortdate_format', 'calendar');
                        foreach (SpcCalendar::getShortdateFormats() as $shortdateFormat) {
                            if ($userShortdateFormat == $shortdateFormat) {
                                echo '<option value="' . $shortdateFormat . '" selected="selected">'
                                        . date($shortdateFormat, $endOfYearTs) .
                                     '</option>';
                            } else {
                                echo '<option value="' . $shortdateFormat . '">'
                                        . date($shortdateFormat, $endOfYearTs) .
                                     '</option>';
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                <?php echo Spc::translate('Long Date Format'); ?>:
            </td>
            <td>
                <select id="longdate_format">
                    <?php
                        $userLongdateFormat = Spc::getUserPref('longdate_format', 'calendar');
                        foreach (SpcCalendar::getLongdateFormats() as $longDateformat => $userLongdateFormatDate) {
                            if ($longDateformat == $userLongdateFormat) {
                                echo "<option value='$longDateformat' selected='selected'>$userLongdateFormatDate</option>";
                            } else {
                                echo "<option value='$longDateformat'>$userLongdateFormatDate</option>";
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                <?php
                    $userTimeformat = Spc::getUserPref('timeformat', 'calendar');
                    echo Spc::translate('Time Format');
                ?>:
            </td>
            <td>
                <select id="timeformat">
                    <option value="standard" <?php if ($userTimeformat == 'standard') echo 'selected="selected"'; ?> >1:00pm</option>
                    <option value="core" <?php if ($userTimeformat == 'core') echo 'selected="selected"'; ?> >13:00</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                <?php echo Spc::translate('Start Day Of Week'); ?>:
            </td>
            <td>
                <?php $userStartDayOfWeek = Spc::getUserPref('start_day', 'calendar'); ?>
                <select id="start_day">
                    <option value="Saturday" <?php if ($userStartDayOfWeek == 'Saturday') echo 'selected="selected"'; ?> >
                        <?php echo Spc::translate('Saturday'); ?>
                    </option>
                    <option value="Sunday" <?php if ($userStartDayOfWeek == 'Sunday') echo 'selected="selected"'; ?> >
                        <?php echo Spc::translate('Sunday'); ?>
                    </option>
                    <option value="Monday" <?php if ($userStartDayOfWeek == 'Monday') echo 'selected="selected"'; ?> >
                        <?php echo Spc::translate('Monday'); ?>
                    </option>
                </select>
            </td>
        </tr>

        <tr class="hidden">
            <td>
                <?php echo Spc::translate('Custom View'); ?>:
            </td>
            <td>
                <select id="custom-view">
                    <?php
                        $customViewDateCount = Spc::getUserPref('custom_view', 'calendar');
                        $daysI18nText = Spc::translate('days');
                        for ($i = 2; $i < 15; $i++) {
                            if ($i == $customViewDateCount) {
                                echo "<option value='$i' selected='selected'>$i $daysI18nText</option>";
                            } else {
                                echo "<option value='$i'>$i $daysI18nText</option>";
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                <?php echo Spc::translate('Default View'); ?>:
            </td>
            <td>
                <select id="default-view">
                    <?php
                        $calViews = SpcCalendar::getCalendarViews();
                        $userDefaultView = Spc::getUserPref('default_view', 'calendar');
                        foreach ($calViews as $calView) {
                            $selected = '';
                            if ($calView == $userDefaultView) {
                                $selected = 'selected="selected"';
                            }
                            echo "<option value='$calView' $selected>" . Spc::translate($calView) . '</option>';
                        }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                <?php
                    $wysiwyg = Spc::getUserPref('wysiwyg', 'calendar');
                    echo '<acronym title="What You See Is What You Get">WYSIWYG</acronym> '
                         . Spc::translate('editor');
                ?>:
            </td>
            <td>
                <div id="calendar-settings-wysiwyg-radio" class="jq-ui-buttonset">
                    <input
                        type="radio"
                        id="calendar-settings-wysiwyg-on"
                        name="calendar-settings-wysiwyg"
                        value="1"
                        <?php if ($wysiwyg == '1') echo 'checked="checked"'; ?> />
                    <label for="calendar-settings-wysiwyg-on">
                        <?php echo Spc::translate('on'); ?>
                    </label>
                    <input
                        type="radio"
                        id="calendar-settings-wysiwyg-off"
                        name="calendar-settings-wysiwyg"
                        value="0"
                        <?php if ($wysiwyg == '0') echo 'checked="checked"'; ?> />
                    <label for="calendar-settings-wysiwyg-off">
                        <?php echo Spc::translate('off'); ?>
                    </label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <?php
                    $eventTooltip = Spc::getUserPref('event_tooltip', 'calendar');
                    echo Spc::t('Event tooltip');
                ?>:
            </td>
            <td>
                <div id="calendar-settings-event-tooltip-radio" class="jq-ui-buttonset">
                    <input
                        type="radio"
                        id="calendar-settings-event-tooltip-on"
                        name="calendar-settings-event-tooltip"
                        value="1"
                        <?php if ($eventTooltip == '1') echo 'checked="checked"'; ?> />
                    <label for="calendar-settings-event-tooltip-on">
                        <?php echo Spc::translate('on'); ?>
                    </label>
                    <input
                        type="radio"
                        id="calendar-settings-event-tooltip-off"
                        name="calendar-settings-event-tooltip"
                        value="0"
                        <?php if ($eventTooltip == '0') echo 'checked="checked"'; ?> />
                    <label for="calendar-settings-event-tooltip-off">
                        <?php echo Spc::translate('off'); ?>
                    </label>
                </div>
            </td>
        </tr>

        <tr class="hidden">
            <td>
                <?php
                    $kbdShortcuts = Spc::getUserPref('kbd_shortcuts');
                    echo Spc::t('Keyboard shortcuts');
                ?>:
            </td>
            <td>
                <div id="calendar-settings-kbd-shortcuts-radio" class="jq-ui-buttonset">
                    <input
                        type="radio"
                        id="calendar-settings-kbd-shortcuts-on"
                        name="calendar-settings-kbd-shortcuts"
                        value="1"
                        <?php if ($kbdShortcuts == '1') echo 'checked="checked"'; ?> />
                    <label for="calendar-settings-kbd-shortcuts-on">
                        <?php echo Spc::translate('on'); ?>
                    </label>
                    <input
                        type="radio"
                        id="calendar-settings-kbd-shortcuts-off"
                        name="calendar-settings-kbd-shortcuts"
                        value="0"
                        <?php if ($kbdShortcuts == '0') echo 'checked="checked"'; ?> />
                    <label for="calendar-settings-kbd-shortcuts-off">
                        <?php echo Spc::translate('off'); ?>
                    </label>
                </div>
            </td>
        </tr>
    </tbody>
</table>