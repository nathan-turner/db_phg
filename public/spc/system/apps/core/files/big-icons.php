<div class="big-icon">
    <div
        id="account-settings"
        class="btn username <?= Spc::getGlobalAppIconsDirName(); ?>"
        title="<?php echo Spc::translate('Account Settings'); ?>">
            <?php echo SPC_USERNAME; ?>
    </div>

    <img
        src="img/<?php echo Spc::getGlobalAppIconsDirName(); ?>/refresh.png"
        id="spc-refresh"
        class="btn"
        alt="Refresh"
        title="Refresh" />

    <a
        id="print-calendar"
        href="#"
        class="btn"
        target="_blank"
        title="<?php echo Spc::translate('Print Calendar'); ?>">
        <img
            src="img/<?php echo Spc::getGlobalAppIconsDirName(); ?>/print.png"
            alt="<?php echo Spc::translate('Print Calendar'); ?>" />
    </a>

    <?php if ((SPC_USER_ROLE == 'admin') || (SPC_USER_ROLE == 'super')): ?>
        <img
            src="img/<?php echo Spc::getGlobalAppIconsDirName(); ?>/users.png"
            id="system-users"
            class="btn"
            alt="<?php echo Spc::translate('Manage Users'); ?>"
            title="<?php echo Spc::translate('Manage Users'); ?>" />
    <?php endif; ?>

    <img
        src="img/<?php echo Spc::getGlobalAppIconsDirName(); ?>/keyboard.png"
        id="keyboard-shortcuts"
        class="btn"
        title="<?php echo Spc::translate('Keyboard shortcuts'); ?>"
        alt="<?php echo Spc::translate('Keyboard shortcuts'); ?>" />

    <img
        src="img/<?php echo Spc::getGlobalAppIconsDirName(); ?>/gear.png"
        id="system-settings"
        class="btn"
        title="<?php echo Spc::translate('Calendar Settings'); ?>"
        alt="<?php echo Spc::translate('Calendar Settings'); ?>" />

     <a
        id="help"
        href="http://smartphpcalendar.com/support"
        class="btn"
        target="_blank"
        title="<?php echo Spc::translate('help'); ?>">
        <img
            src="img/<?php echo Spc::getGlobalAppIconsDirName(); ?>/help.png"
            alt="<?php echo Spc::translate('help'); ?>" />
    </a>

    <img
        src="img/<?php echo Spc::getGlobalAppIconsDirName(); ?>/power.png"
        id="logout"
        class="btn"
        title="<?php echo Spc::translate('logout'); ?>"
        alt="<?php echo Spc::translate('logout'); ?>" />
</div>