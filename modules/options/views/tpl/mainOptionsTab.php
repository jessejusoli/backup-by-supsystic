<section>
    <div class="supsystic-item supsystic-panel">
        <?php
            $not_piad = utilsBup::checkPRO() ? '' : 'bupNotPaid';
        ?>
        <form class="cspNiceStyle" id="bupAdminMainForm">
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

                        <?php echo htmlBup::submit('backupnow', array('value' => langBup::_('Where to Backup:'), 'attrs' => implode(' ', $attrs))); ?>
                        <div id="bupInfo">
                            <p style="font-size: 15px;">Available space: <br/>
                                <?php if (frameBup::_()->getModule('warehouse')->getWarehouseStatus()): ?>
                                    <?php echo frameBup::_()->humanSize(
                                        disk_free_space(frameBup::_()->getModule('warehouse')->getPath())
                                    );
                                    ?>
                                <?php else: ?>
                                    <span class="bupErrorMsg">
                            <?php langBup::_e('An errors has been occured while initialize warehouse module.'); ?>
                        </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="main-progress-bar" style="display:none;">
                            <div class="progress-bar devblue shine">
                                <span style="width: 0%;"><b id="bupCompletePercent"></b></span>
                            </div>
                            <span class="bupShowLog bupShowLogDlg">Show Log</span>
                        </div>
                        <div id="BUP_SHOW_LOG" style="display: none;">
                        <p id="inProcessMessage" class="bupErrorMsg" style="<?php echo $style; ?>">
                            Backup already in process.
                        </p>
                    </td>
                </tr>
            </table>


        </form>

        <div id="resBox"></div>

        <div align="left">
            <div id="BUP_MESS_INFO"></div>
        </div>

        <div class="bupDest">
            <form id="bupMainFormOptions" method="post">
                <div id="bupOptions">
                    <div class="bupMsgDest"></div>
                    <div class="bupMargDest">
                        <?php echo htmlBup::radiobutton('dest_opt', array('attrs'=>'class=""', 'value' => 'ftp',)); ?> FTP
                    </div>
                    <div class="bupMargDest">
                        <?php echo htmlBup::radiobutton('dest_opt', array(
                            'value'   => 'googledrive',
                        )); ?> <?php echo $this->backupPlaces['bupGdriveOptions']['title']?>
                    </div>
                    <div class="bupOptions bup-googledrive">
                        <?php echo $this->backupPlaces['bupGdriveOptions']['content']?>
                    </div>

<!--                    <div class="bupMargDest">-->
<!--                        --><?php //echo htmlBup::radiobutton('dest_opt', array(
//                            'value'   => 'dropbox',
//                        )); ?><!-- --><?php //echo $this->backupPlaces['bupDropboxOptions']['title']?>
<!--                    </div>-->
<!--                    <div class="bupOptions bup-dropbox">-->
<!--                        --><?php //echo $this->backupPlaces['bupDropboxOptions']['content']?>
<!--                    </div>-->
<!--                    <div class="bupMargDest">-->
<!--                        --><?php //echo htmlBup::radiobutton('dest_opt', array(
//                                'value' => 'amazon',
//                            )); ?><!-- --><?php //echo $this->backupPlaces['bupAmazonS3Options']['title']?>
<!--                    </div>-->
<!--                    <div class="bupOptions bup-amazon">-->
<!--                        --><?php //echo $this->backupPlaces['bupAmazonS3Options']['content']?>
<!--                    </div>-->
                    <div class="bupMargDest">
                        <?php echo htmlBup::radiobutton('dest_opt', array(
                                'value' => 'onedrive',
                            )); ?> <?php echo $this->backupPlaces['bupOneDriveOptions']['title']?>
                     </div>
                    <div class="bupOptions bup-onedrive">
                        <?php echo $this->backupPlaces['bupOneDriveOptions']['content']?>
                    </div>


                    <div id="bupMainOption" style="display: none;">
                        <hr/>
                        <h3>Backup Presets:</h3>
                        <table class="form-table">
                            <tr>
                                <th class="col-w-30perc">Full backup</th>
                                <td class="col-w-1perc">
                                    <i class="fa fa-question supsystic-tooltip" title="Full backup"></i>
                                </td class="col-w-1perc">
                                <td class="col-w-1perc">
                                    <?php echo htmlBup::checkbox('opt_values[full]', array('attrs'=>'class="bupCheckbox bupFull" id="bupFullBackup"', 'value' => 1, 'checked' => frameBup::_()->getModule('options')->get('full') ? 'checked' : '' )); ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="col-w-30perc">Wordpress Core</th>
                                <td class="col-w-1perc">
                                    <i class="fa fa-question supsystic-tooltip" title="Wordpress Core"></i>
                                </td>
                                <td class="col-w-1perc"><?php echo htmlBup::checkbox('opt_values[wp_core]', array('attrs'=>'class="bupCheckbox bupFull"', 'value' => 1, 'checked' => frameBup::_()->getModule('options')->get('wp_core') ? 'checked' : '' )); ?></td>
                            </tr>
                            <tr>
                                <th class="col-w-30perc">Plugins folder</th>
                                <td class="col-w-1perc">
                                    <i class="fa fa-question supsystic-tooltip" title="Plugins folder"></i>
                                </td>
                                <td class="col-w-1perc"><?php echo htmlBup::checkbox('opt_values[plugins]', array('attrs'=>'class="bupCheckbox bupFull"', 'value' => 1, 'checked' => frameBup::_()->getModule('options')->get('plugins') ? 'checked' : '')); ?></td>
                            </tr>
                            <tr>
                                <th class="col-w-30perc">Themes folder</th>
                                <td class="col-w-1perc">
                                    <i class="fa fa-question supsystic-tooltip" title="Themes folder"></i>
                                </td>
                                <td class="col-w-1perc"><?php echo htmlBup::checkbox('opt_values[themes]', array('attrs'=>'class="bupCheckbox bupFull"', 'value' => 1, 'checked' => frameBup::_()->getModule('options')->get('themes') ? 'checked' : '')); ?></td>
                            </tr>
                            <tr>
                                <th class="col-w-30perc">Uploads folder</th>
                                <td class="col-w-1perc">
                                    <i class="fa fa-question supsystic-tooltip" title="Uploads folder"></i>
                                </td>
                                <td class="col-w-1perc"><?php echo htmlBup::checkbox('opt_values[uploads]', array('attrs'=>'class="bupCheckbox bupFull"', 'value' => 1, 'checked' => frameBup::_()->getModule('options')->get('uploads') ? 'checked' : '')); ?></td>
                            </tr>
                            <tr>
                                <th class="col-w-30perc">Any folder inside wp-content</th>
                                <td class="col-w-1perc">
                                    <i class="fa fa-question supsystic-tooltip" title="Any folder inside wp-content"></i>
                                </td>
                                <td class="col-w-1perc"><?php echo htmlBup::checkbox('opt_values[any_directories]', array('attrs'=>'class="bupCheckbox bupFull"', 'value' => 1, 'checked' => frameBup::_()->getModule('options')->get('any_directories') ? 'checked' : '')); ?></td>
                            </tr>
                            <tr>
                                <th class="col-w-30perc"><?php langBup::_e('Safe Update'); ?></th>
                                <td class="col-w-1perc">
                                    <i class="fa fa-question supsystic-tooltip" title="Safe Update"></i>
                                </td>
                                <td class="col-w-1perc"><?php echo htmlBup::checkbox('opt_values[safe_update]', array(
                                        'attrs'   => 'class="bupCheckbox"',
                                        'value'   => '1',
                                        'checked' => frameBup::_()->getModule('options')->get('safe_update') == 1 ? 'checked' : '',
                                    )); ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="col-w-30perc"><?php langBup::_e('Force Update'); ?></th>
                                <td class="col-w-1perc">
                                    <i class="fa fa-question supsystic-tooltip" title="Force Update"></i>
                                </td>
                                <td class="col-w-1perc"><?php echo htmlBup::checkbox('opt_values[force_update]', array(
                                        'attrs'   => 'class="bupCheckbox"',
                                        'value'   => '1',
                                        'checked' => frameBup::_()->getModule('options')->get('force_update') == 1 ? 'checked' : '',
                                    )); ?>
                                </td>
                            </tr>
                        </table>

                        <hr/>

                        <table style="width: 100%">
                            <tr>
                                <td width="200"><i class="fa fa-question supsystic-tooltip" title="Database backup"></i>Database backup</td>
                                <td><?php echo htmlBup::checkbox('opt_values[database]', array('attrs'=>'class="bupCheckbox bupFull"', 'value' => 1, 'checked' => frameBup::_()->getModule('options')->get('database') ? 'checked' : '')); ?></td>
                            </tr>
                        </table>

                        <hr/>

                        <!-- <div class="excludeOpt"> -->
                        <table style="width: 100%">
                            <tr>
                                <td width="200"><i class="fa fa-question supsystic-tooltip" title="Введите в поле название папок и файлов, которые следует исключить из бекапа."></i><?php echo langBup::_('Exclude:'); ?></td>
                                <td><?php echo htmlBup::text( 'opt_values[exclude]', array('attrs'=>'class="excludeInput" title="If entering multiple files/directories, then separate them with commas."', 'value' => frameBup::_()->getModule('options')->get('exclude')) ); ?></td>
                            </tr>
                        </table>
                        <!-- </div> -->

                        <hr/>

                        <!-- <div class="emailOpt"> -->
                        <table style="min-height: 45px;">
                            <tr>
                                <td width="200"><i class="fa fa-question supsystic-tooltip" title="Email notification"></i><?php echo langBup::_('Email notification:'); ?></td>
                                <td>
                                    <?php echo htmlBup::checkbox('__toggleEmailCheckbox', array('attrs'=>'class="bupCheckboxNotUnCheck emailCh"', 'checked' => frameBup::_()->getModule('options')->get('email_ch') == 1 ? 'checked' : '')); ?> <span  class="emailAddress" <?php echo frameBup::_()->getModule('options')->get('email_ch') ? '' : 'style="display:none"' ?>><?php echo htmlBup::text( 'opt_values[email]', array('attrs'=>'class="excludeInput" placeholder="example@mail.com" title=""', 'value' => frameBup::_()->getModule('options')->get('email')) );  ?></span>
                                </td>
                                <input type="hidden" value="<?php echo frameBup::_()->getModule('options')->get('email_ch'); ?>" name="opt_values[email_ch]">
                            </tr>
                        </table>

                        <hr/>

                        <table style="width:100%;">
                            <tr>
                                <td  width="200">
                                    <i class="fa fa-question supsystic-tooltip" title="Warehouse"></i><?php langBup::_e('Warehouse:'); ?>
                                </td>
                                <td>
                                    <?php
                                    echo htmlBup::text(
                                        'opt_values[warehouse]', array(
                                            'attrs' => 'class="input-regular" id="warehouseInput"',
                                            'value' => str_replace(
                                                '\\',
                                                '/',
                                                frameBup::_()->getModule('options')->get('warehouse')
                                            ),
                                        )
                                    );
                                    ?>
                                </td>
                            </tr>
                        </table>

                        <hr/>

                        <table style="width:100%;">
                            <tr>
                                <td  width="200">
                                    <i class="fa fa-question supsystic-tooltip" title="Use relative path"></i><?php langBup::_e('Use relative path:'); ?>
                            </td>
                            <td>
                                <?php
                                echo htmlBup::checkbox(
                                    '__toggleWarehouseAbs',
                                    array(
                                        'attrs'   => 'class="bupCheckbox wareabs" id="warehouseToggle"',
                                        'checked' => frameBup::_()->getModule('options')->get('warehouse_abs') == 1 ? 'checked' : '',
                                    )
                                );
                                ?>  <span id="abspath"><?php echo ABSPATH; ?></span><span id="realpath"></span>
                            </td>
                            <input type="hidden" value="<?php echo frameBup::_()->getModule('options')->get('warehouse_abs'); ?>" name="opt_values[warehouse_abs]">
                            </tr>
                        </table>

                        <hr/>
                    </div>

                    <?php echo htmlBup::hidden('reqType', array('value' => 'ajax'))?>
                    <?php echo htmlBup::hidden('page', array('value' => 'options'))?>
                    <?php echo htmlBup::hidden('action', array('value' => 'saveMainFromDestGroup'))?>
                    <?php echo htmlBup::hidden('backupDest', array('value' => $this->backupDest))?>
                    <div id="bupMainFormOptionsMsg"></div>
                </div>
            </form>
        </div>
        <!-- Log modal window start  -->
        <div id="bupShowLogDlg" title="Backup Log:">
            <div id="bupLogText"></div>
        </div>
        <!-- Log modal window end  -->

    </div>
</section>