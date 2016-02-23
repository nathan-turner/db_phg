<script type="text/x-handlebars-template" id="public-invitation-grid-tmpl">
    <table id="public-invitation-grid">
        <caption>
            {{invitationOwnerName}} <a href="mailto:{{invitationOwnerEmail}}">{{invitationOwnerEmail}}</a>
            <?php echo Spc::translate('invites you'); ?>
        </caption>
        <tbody>
            <tr>
                <td colspan="2" class="response-row">
                    <strong><?php echo Spc::translate('Your response'); ?>: </strong>

                    <input id="public-invitation-grid-invitee-response-yes" name="public-invitation-grid-invitee-response" type="radio" value="yes" />
                    <label for="public-invitation-grid-invitee-response-yes"><?php echo Spc::translate('yes'); ?></label>

                    <input id="public-invitation-grid-invitee-response-no" name="public-invitation-grid-invitee-response" type="radio" value="no" />
                    <label for="public-invitation-grid-invitee-response-no"><?php echo Spc::translate('no'); ?></label>

                    <input id="public-invitation-grid-invitee-response-maybe" name="public-invitation-grid-invitee-response" type="radio" value="maybe" />
                    <label for="public-invitation-grid-invitee-response-maybe"><?php echo Spc::translate('maybe'); ?></label>

                    <input id="public-invitation-grid-invitee-response-pending" name="public-invitation-grid-invitee-response" type="radio" value="pending" />
                    <label for="public-invitation-grid-invitee-response-pending"><?php echo Spc::translate('pending'); ?></label>
                </td>
            </tr>
            <tr>
                <td><?php echo Spc::translate('title') ?></td>
                <td>{{title}}</td>
            </tr>
            <tr>
                <td><?php echo Spc::translate('date') ?></td>
                <td>{{date}}</td>
            </tr>
            <tr>
                <td><?php echo Spc::translate('description') ?></td>
                <td>{{description}}</td>
            </tr>
            <tr>
                <td><?php echo Spc::translate('location') ?></td>
                <td>{{location}}</td>
            </tr>
            <tr>
                <td colspan="2" class="guests-title">
                    <?php echo Spc::translate('guests') ?> ({{invitees.guestCount}})
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table id="public-invitation-responses-grid">
                        <thead>
                            <tr>
                                <th class="yes"><?php echo Spc::translate('yes'); ?> ({{yesCount}})</th>
                                <th class="no"><?php echo Spc::translate('no'); ?> ({{noCount}})</th>
                                <th class="maybe"><?php echo Spc::translate('maybe'); ?> ({{maybeCount}})</th>
                                <th class="pending"><?php echo Spc::translate('pending'); ?> ({{pendingCount}})</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <ul>
                                        {{#invitees.yes}}
                                            <li>
                                                <a href="mailto:{{email}}">{{email}}</a>
                                            </li>
                                        {{/invitees.yes}}
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        {{#invitees.no}}
                                            <li>
                                                <a href="mailto:{{email}}">{{email}}</a>
                                            </li>
                                        {{/invitees.no}}
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        {{#invitees.maybe}}
                                            <li>
                                                <a href="mailto:{{email}}">{{email}}</a>
                                            </li>
                                        {{/invitees.maybe}}
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        {{#invitees.pending}}
                                            <li>
                                                <a href="mailto:{{email}}">{{email}}</a>
                                            </li>
                                        {{/invitees.pending}}
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</script>

<script type="text/x-handlebars-template" id="timeline-body-tmpl">
    <div id='spc-timeline' class='{{mode}} ui-helper-clearfix'>
        <div id='spc-timeline-left'>
            <div id='spc-timeline-mode'></div>
            <div id='spc-timeline-resources'>
                <table id='spc-timeline-resources-list'></table>
            </div>
        </div>
        <div id='spc-timeline-right'>
            <div id='spc-timeline-header'>
                {{timelineHeader}}
            </div>
            <div id='spc-timeline-body'>
                <table id='spc-timeline-time-sep' class='{{mode}}'></table>
                <table id='spc-timeline-main-grid'></table>
                <div id='spc-timeline-range-selector'></div>
                <div id='spc-timeline-range-selector-title'></div>
            </div>
            <div id='spc-timeline-v-scroller'>
                <div></div>
            </div>
            <div id='spc-timeline-h-scroller'>
                <div></div>
            </div>
        </div>
    </div>
</script>

<script type="text/x-handlebars-template" id="spc-v-timeline-header-tmpl">
    <table id="spc-v-timeline-header">
        <tbody>
            <tr>
                {{#calendars}}
                    <td class="{{id}}" style="background-color: {{color}};" data-cal-id="{{id}}"><div class="{{id}}" style='overflow: hidden;'>{{name}}</div></td>
                {{/calendars}}
            </tr>
        </tbody>
    </table>
</script>

<script type="text/x-handlebars-template" id="spc-h-timeline-timebar-tmpl_">
    <div class='spc-timeline-x-day-hour-grid-wrapper'>
        <table class='spc-timeline-x-day-hour-grid'>
            <tbody>
                <tr>
                    {{#times}}
                        <td style='width: {{hourWidth}}%;'>
                            <div class='relative'>
                                <span class='time {{../userTimeFormat}}'>{{.}}</span>
                            </div>
                        </td>
                    {{/times}}
                    <td style='width: {{hourWidth}}%;'>&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </div>
</script>

<script type="text/x-handlebars-template" id="spc-h-timeline-timebar-tmpl">
    <div class='spc-h-timeline-timebar-wrapper' data-time-slot-length='{{timeSlotLength}}'>
        {{#times}}
            <div class='absolute time {{../userTimeFormat}}' style='left: {{top}}px;' data-top='{{top}}'>{{time}}</div>
        {{/times}}
    </div>
</script>

<script type="text/x-handlebars-template" id="spc-v-timeline-timebar-tmpl">
    <div class='spc-v-timeline-timebar-wrapper' data-time-slot-length='{{timeSlotLength}}'>
        {{#times}}
            <div class='time {{../userTimeFormat}}' style='top: {{top}}px;' data-top='{{top}}'>{{time}}</div>
        {{/times}}
    </div>
</script>

<script type="text/x-handlebars-template" id="event-popup-dialog-tmpl">
    <div class='title'>{{title}}</div>
    <div class='date relative'><div class='calendar' style='background-color: {{color}};'>{{calendar}}</div> {{date}}</div>
    <div class='description'>{{description}}</div>
    {{#if location}}
        <div class='location'><label><?php echo Spc::t('location'); ?></label> <span>{{location}}</span></div>
    {{/if}}
</script>