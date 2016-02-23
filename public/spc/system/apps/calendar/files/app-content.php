<div id="calendar" class="app">
    <span id="spc-cal-left-side-toggle" class="ui-icon ui-icon ui-icon-carat-1-e"></span>
    <div id="smartphpcalendar-wrapper">
        <div id="spc-app-calendar" class="spc-app">
            <table id="spc-calendar" class="ui-widget-content">
                <tbody>
                    <tr>
                        <!-- Smart PHP Calendar - Left Sidebar -->
                        <td id="app-left">
                            <div id="sidebar-calendar" class="spc-sidebar-widget ui-corner-all"></div>

                            <!-- User Calendars -->
                            <div id="calendars" class="ui-widget-content ui-corner-all spc-sidebar-widget">

                                <div id="calendars-header" class="ui-corner-all spc-sidebar-widget-header ui-helper-clearfix ui-widget-header">
                                    <?php echo Spc::translate('My Calendars'); ?>

                                    <span
                                        id="create-calendar"
                                        class="ui-icon ui-icon-plus"
                                        title="<?php echo Spc::translate('Create a New Calendar'); ?>">
                                    </span>
                                </div>

                                <!--user calendars list-->
                                <div id="calendars-list">
                                    <table id="calendars-table">
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /User Calendars-->

                            <!-- Other Calendar -->
                            <div
                                id="other-calendars"
                                class="hidden
                                    ui-widget-content
                                    ui-corner-all
                                    spc-sidebar-widget">

                                <div id="other-calendars-header" class="ui-corner-all spc-sidebar-widget-header ui-helper-clearfix ui-widget-header">
                                    <?php echo Spc::translate('Other Calendars'); ?>
                                    <span class="calendars-settings ui-icon ui-icon-wrench other"></span>
                                    <span class="create-cal-by-url ui-icon ui-icon-plus"></span>
                                </div>

                                <!--other calendars list-->
                                <div id="other-calendars-list" style="overflow: auto; padding: 2px;">
                                    <table id="other-calendars-table">
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /Other Calendars-->

                            <?php
                                /**
                                * superuser has no group calendars he can see
                                * admins group calendars in all-users-calendars dialog
                                */
                                if (SPC_USER_ROLE !== 'super'):
                            ?>
                            <!--Group Calendars-->
                            <div
                                id="group-calendars"
                                class="hidden
                                    ui-widget-content
                                       ui-corner-all
                                       spc-sidebar-widget">

                                <div id="group-calendars-header" class="ui-corner-all spc-sidebar-widget-header ui-helper-clearfix ui-widget-header">
                                    <?php echo Spc::translate('Group Calendars'); ?>
                                    <span class="calendars-settings ui-icon ui-icon-wrench group"></span>
                                    <?php if (SPC_USER_ROLE == 'admin'): ?>
                                        <span
                                            id="create-group-calendar"
                                            class="ui-icon ui-icon-plus"
                                            title="<?php echo Spc::translate('Create a New Group Calendar'); ?>">
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!--group calendars list-->
                                <div id="group-calendars-list" class="ui-corner-all spc-sidebar-widget">
                                    <table id="group-calendars-table" class="ui-corner-all spc-sidebar-widget-header ui-helper-clearfix">
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <!--/Group Calendars-->
                            <?php endif; ?>

                            <!-- image-upload-iframes -->
                            <iframe id="upload-target" name="upload-target" src="#" class="hidden"></iframe>
                            <iframe id="upload-image-target" name="upload-image-target" src="#" class="hidden"></iframe>
                            <!-- /image-upload-iframes -->
                        </td>

                        <!-- Smart PHP Calendar - Main Application -->
                        <td id="spc-main" class="spc-sidebar-widget">
                            <div id="spc-main-nav" class="ui-helper-clearfix ui-widget-header ui-corner-all">
                                <table id="spc-cal-pager">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div
                                                    id="cal-pager-prev"
                                                    class="spc-cal-page ui-state-default ui-corner-left"
                                                    data-direction="prev">
                                                        <span class="ui-icon ui-icon ui-icon-carat-1-w"></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    id="cal-pager-next"
                                                    class="spc-cal-page ui-state-default"
                                                    data-direction="next">
                                                        <span class="ui-icon ui-icon ui-icon-carat-1-e"></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    id="cal-pager-today"
                                                    class="spc-cal-page ui-state-default ui-corner-right"
                                                    data-direction="today">
                                                        <span>
                                                            <?php echo Spc::translate('today'); ?>
                                                        </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div id="spc-cal-nav-date" class="black-text-shadow"></div>

                                <div id="spc-cal-view-buttons" class="ui-buttonset">
                                    <input
                                        value="<?php echo Spc::translate('day'); ?>"
                                        id="spc-cal-day-view-btn"
                                        type="button"
                                        data-view-name="day"
                                        class="day ui-button ui-widget ui-state-default ui-corner-left" />
                                    <input
                                        value="<?php echo Spc::translate('agenda'); ?>"
                                        id="spc-cal-agenda-view-btn"
                                        type="button"
                                        data-view-name="agenda"
                                        class="agenda ui-button ui-widget ui-state-default" />
                                </div>

                                <div id="spc-cal-mode-timeline-checkbox-wrapper" class="f-right hidden">
                                    <div id="spc-timeline-x-days-zoom-wrapper" class="f-left">
                                        <span class="timeline-zoom timeline-zoom-in ui-icon ui-icon-plus"></span>
                                        <span class="timeline-zoom timeline-zoom-out ui-icon ui-icon-minus"></span>
                                        <span class="timeline-rotate-event-height ui-icon ui-icon-carat-2-n-s"></span>
                                        <span class="timeline-show-hours ui-icon ui-icon-clock"></span>
                                        <span class="timeline-change-mode ui-icon ui-icon-transferthick-e-w hidden-important"></span>
                                        <span class="week-cal-settings ui-icon ui-icon-gear hidden-important"></span>
                                    </div>
                                    <input
                                        type="checkbox"
                                        id="spc-cal-mode-timeline-checkbox"
                                        class="jq-ui-button"
                                        data-mode="timeline"
                                        name="spc-cal-mode-radio" />
                                    <label for="spc-cal-mode-timeline-checkbox" data-mode="timeline">
                                        <?php echo Spc::translate('timeline'); ?>
                                    </label>
                                </div>
                            </div>

                            <div id="spc-main-app"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

	<!--Smart PHP Calendar Dialogs-->
    <?php Spc::requireFile('files/dialogs.php', 'calendar'); ?>
    <?php Spc::requireFile('files/templates.php', 'calendar'); ?>
	<!--/Smart PHP Calendar Dialogs-->
    </div>
</div>