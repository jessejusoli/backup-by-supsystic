<section>
    <div class="supsystic-item supsystic-panel">
        <?php
        $not_piad = utilsBup::checkPRO() ? '' : 'bupNotPaid';
        ?>
        <form class="cspNiceStyle" id="bupAdminMainForm">
            <div class="description" style="border-bottom: 1px dashed #e3e3e3; padding-bottom: 10px; margin-bottom: 10px">
                <?php _e('To restore website backup, be sure that all files and folders in the core directory have writing permissions. Backup restoration can rewrite some of them.', BUP_LANG_CODE) ?>
            </div>
            <div id="BUP_MESS_MAIN"></div>
            <table width="100%">
                <tr class="cspAdminOptionRow cspTblRow">
                    <td style="padding-left: 0">
                        <?php echo htmlBup::hidden('reqType', array('value' => 'ajax'))?>
                        <?php echo htmlBup::hidden('page', array('value' => 'backup'))?>
                        <?php echo htmlBup::hidden('action', array('value' => 'createAction'))?>
                        <?php $attrs = array('class="button button-primary button-large" style="margin-right: 10px;"'); $style = ''; ?>
                        <?php if (defined('BUP_LOCK_FIELD') && get_option(BUP_LOCK_FIELD) == 1): ?>
                            <?php $attrs[] = 'style="display:none;"'; ?>
                        <?php else: ?>
                            <?php $style = 'display:none;'; ?>
                        <?php endif; ?>

                        <?php echo htmlBup::submit('backupnow', array('value' => __('Where to Backup:', BUP_LANG_CODE), 'attrs' => implode(' ', $attrs))); ?>
                        <div id="bupInfo">
                            <p style="font-size: 15px;"><?php _e('Available space:', BUP_LANG_CODE) ?> <br/>
                                <?php if (frameBup::_()->getModule('warehouse')->getWarehouseStatus()): ?>
                                    <?php echo frameBup::_()->humanSize(
                                        disk_free_space(frameBup::_()->getModule('warehouse')->getPath())
                                    );
                                    ?>
                                <?php else: ?>
                                    <span class="bupErrorMsg">
                                    <?php _e('An errors has been occured while initialize warehouse module.', BUP_LANG_CODE); ?>
                                </span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="bupPluginReviewBlock" style="display: <?php echo $this->reviewBlockDisplay?>">
                            <p>
                                <?php _e('Like Backup By Supsystic and can spare one minute?', BUP_LANG_CODE); ?><br/>
                                <a href="https://wordpress.org/support/view/plugin-reviews/backup-by-supsystic?rate=5#postform" class="bupDoNotShowReviewBlock" target="_blank">
                                    <?php _e('Please, help by giving a positive review at wordpress.org', BUP_LANG_CODE); ?>
                                </a>
                            </p>
                        </div>
                        <div class="main-progress-bar" style="display:none;">
                            <div class="progress-bar devblue shine">
                                <span style="width: 0%;"><b id="bupCompletePercent"></b></span>
                            </div>
                            <span class="bupShowLog bupShowLogDlg"><?php _e('Show Log', BUP_LANG_CODE) ?></span>
                        </div>
                        <div id="BUP_SHOW_LOG" style="display: none;">
                            <p id="inProcessMessage" class="bupErrorMsg" style="<?php echo $style; ?>">
                                <?php _e('Backup already in process.', BUP_LANG_CODE) ?>
                            </p>
                        </div>

                    </td>
                </tr>
            </table>


        </form>

        <div id="resBox"></div>

        <div align="left">
            <div id="BUP_MESS_INFO"></div>
        </div>

        <?php echo $this->backupOptions ?>

        <!-- Log modal window start  -->
        <div id="bupShowLogDlg" title="Backup Log:">
            <div id="bupLogText"></div>
        </div>
        <!-- Log modal window end  -->

    </div>
</section>
