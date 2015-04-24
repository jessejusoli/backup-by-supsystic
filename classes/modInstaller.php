<?php
class modInstallerBup {
    static private $_current = array();
    /**
     * Install new moduleBup into plugin
     * @param string $module new moduleBup data (@see classes/tables/modules.php)
     * @param string $path path to the main plugin file from what module is installed
     * @return bool true - if install success, else - false
     */
    static public function install($module, $path) {
        $exPlugDest = explode('plugins', $path);
        if(!empty($exPlugDest[1])) {
            $module['ex_plug_dir'] = str_replace(DS, '', $exPlugDest[1]);
        }
        $path = $path. DS. $module['code'];
        if(!empty($module) && !empty($path) && is_dir($path)) {
            if(self::isModule($path)) {
                $filesMoved = false;
                if(empty($module['ex_plug_dir']))
                    $filesMoved = self::moveFiles($module['code'], $path);
                else
                    $filesMoved = true;     //Those modules doesn't need to move their files
                if($filesMoved) {
                    if(frameBup::_()->getTable('modules')->exists($module['code'], 'code')) {
                        frameBup::_()->getTable('modules')->delete(array('code' => $module['code']));
                    }
					if($module['code'] != 'license')
						$module['active'] = 0;
                    frameBup::_()->getTable('modules')->insert($module);
                    self::_runModuleInstall($module);
                    self::_installTables($module);
                    return true;
                } else {
                    errorsBup::push(sprintf(__('Move files for %s failed'), $module['code'], BUP_LANG_CODE), errorsBup::MOD_INSTALL);
                }
            } else
                errorsBup::push(sprintf(__('%s is not plugin module', BUP_LANG_CODE), $module['code']), errorsBup::MOD_INSTALL);
        }
        return false;
    }
    static protected function _runModuleInstall($module, $action = 'install') {
        $moduleLocationDir = BUP_MODULES_DIR;
        if(!empty($module['ex_plug_dir']))
            $moduleLocationDir = utilsBup::getPluginDir( $module['ex_plug_dir'] );
        if(is_dir($moduleLocationDir. $module['code'])) {
			if(!class_exists($module['code']. strFirstUp(BUP_CODE))) {
				importClassBup($module['code'], $moduleLocationDir. $module['code']. DS. 'mod.php');
			}
            $moduleClass = toeGetClassNameBup($module['code']);
            $moduleObj = new $moduleClass($module);
            if($moduleObj) {
                $moduleObj->$action();
            }
        }
    }
    /**
     * Check whether is or no module in given path
     * @param string $path path to the module
     * @return bool true if it is module, else - false
     */
    static public function isModule($path) {
        return true;
    }
    /**
     * Move files to plugin modules directory
     * @param string $code code for module
     * @param string $path path from what module will be moved
     * @return bool is success - true, else - false
     */
    static public function moveFiles($code, $path) {
        if(!is_dir(BUP_MODULES_DIR. $code)) {
            if(mkdir(BUP_MODULES_DIR. $code)) {
                utilsBup::copyDirectories($path, BUP_MODULES_DIR. $code);
                return true;
            } else 
                errorsBup::push(__('Can not create module directory. Try to set permission to '. BUP_MODULES_DIR. ' directory 755 or 777', BUP_LANG_CODE), errorsBup::MOD_INSTALL);
        } else
            return true;
        return false;
    }
    static private function _getPluginLocations() {
        $locations = array();
        $plug = reqBup::getVar('plugin');
        if(empty($plug)) {
            $plug = reqBup::getVar('checked');
            $plug = $plug[0];
        }
        $locations['plugPath'] = plugin_basename( trim( $plug ) );
        $locations['plugDir'] = dirname(WP_PLUGIN_DIR. DS. $locations['plugPath']);
		$locations['plugMainFile'] = WP_PLUGIN_DIR. DS. $locations['plugPath'];
        $locations['xmlPath'] = $locations['plugDir']. DS. 'install.xml';
        return $locations;
    }
    static private function _getModulesFromXml($xmlPath) {
        if($xml = utilsBup::getXml($xmlPath)) {
            if(isset($xml->modules) && isset($xml->modules->mod)) {
                $modules = array();
                $xmlMods = $xml->modules->children();
                foreach($xmlMods->mod as $mod) {
                    $modules[] = $mod;
                }
                if(empty($modules))
                    errorsBup::push(__('No modules were found in XML file', BUP_LANG_CODE), errorsBup::MOD_INSTALL);
                else
                    return $modules;
            } else
                errorsBup::push(__('Invalid XML file', BUP_LANG_CODE), errorsBup::MOD_INSTALL);
        } else
            errorsBup::push(__('No XML file were found', BUP_LANG_CODE), errorsBup::MOD_INSTALL);
        return false;
    }
    /**
     * Check whether modules is installed or not, if not and must be activated - install it
     * @param array $codes array with modules data to store in database
     * @param string $path path to plugin file where modules is stored (__FILE__ for example)
     * @return bool true if check ok, else - false
     */
    static public function check($extPlugName = '') {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsBup::xmlNodeAttrsToArr($m);
                if(!empty($modDataArr)) {
                    if(frameBup::_()->moduleExists($modDataArr['code'])) { //If module Exists - just activate it
                        self::activate($modDataArr);
                    } else {                                           //  if not - install it
                        if(!self::install($modDataArr, $locations['plugDir'])) {
                            errorsBup::push(sprintf(__('Install %s failed', BUP_LANG_CODE), $modDataArr['code']), errorsBup::MOD_INSTALL);
                        }
                    }
                }
            }
        } else
            errorsBup::push(__('Error Activate module', BUP_LANG_CODE), errorsBup::MOD_INSTALL);
        if(errorsBup::haveErrors(errorsBup::MOD_INSTALL)) {
            self::displayErrors();
            return false;
        }
		update_option(BUP_CODE. '_full_installed', 1);
        return true;
    }
    /**
	 * Public alias for _getCheckRegPlugs()
	 */
	/**
	 * We will run this each time plugin start to check modules activation messages
	 */
	static public function checkActivationMessages() {

	}
    /**
     * Deactivate module after deactivating external plugin
     */
    static public function deactivate() {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsBup::xmlNodeAttrsToArr($m);
                if(frameBup::_()->moduleActive($modDataArr['code'])) { //If module is active - then deacivate it
                    if(frameBup::_()->getModule('options')->getModel('modules')->put(array(
                        'id' => frameBup::_()->getModule($modDataArr['code'])->getID(),
                        'active' => 0,
                    ))->error) {
                        errorsBup::push(__('Error Deactivation module', BUP_LANG_CODE), errorsBup::MOD_INSTALL);
                    }
                }
            }
        }
        if(errorsBup::haveErrors(errorsBup::MOD_INSTALL)) {
            self::displayErrors(false);
            return false;
        }
        return true;
    }
    static public function activate($modDataArr) {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsBup::xmlNodeAttrsToArr($m);
                if(!frameBup::_()->moduleActive($modDataArr['code'])) { //If module is not active - then acivate it
                    if(frameBup::_()->getModule('options')->getModel('modules')->put(array(
                        'code' => $modDataArr['code'],
                        'active' => 1,
                    ))->error) {
                        errorsBup::push(__('Error Activating module', BUP_LANG_CODE), errorsBup::MOD_INSTALL);
                    } else {
						$dbModData = frameBup::_()->getModule('options')->getModel('modules')->get(array('code' => $modDataArr['code']));
						if(!empty($dbModData) && !empty($dbModData[0])) {
							$modDataArr['ex_plug_dir'] = $dbModData[0]['ex_plug_dir'];
						}
						self::_runModuleInstall($modDataArr, 'activate');
					}
                }
            }
        }
    } 
    /**
     * Display all errors for module installer, must be used ONLY if You realy need it
     */
    static public function displayErrors($exit = true) {
        $errors = errorsBup::get(errorsBup::MOD_INSTALL);
        foreach($errors as $e) {
            echo '<b style="color: red;">'. $e. '</b><br />';
        }
        if($exit) exit();
    }
    static public function uninstall() {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsBup::xmlNodeAttrsToArr($m);
                self::_uninstallTables($modDataArr);
                frameBup::_()->getModule('options')->getModel('modules')->delete(array('code' => $modDataArr['code']));
                utilsBup::deleteDir(BUP_MODULES_DIR. $modDataArr['code']);
            }
        }
    }
    static protected  function _uninstallTables($module) {
        if(is_dir(BUP_MODULES_DIR. $module['code']. DS. 'tables')) {
            $tableFiles = utilsBup::getFilesList(BUP_MODULES_DIR. $module['code']. DS. 'tables');
            if(!empty($tableNames)) {
                foreach($tableFiles as $file) {
                    $tableName = str_replace('.php', '', $file);
                    if(frameBup::_()->getTable($tableName))
                        frameBup::_()->getTable($tableName)->uninstall();
                }
            }
        }
    }
    static public function _installTables($module, $action = 'install') {
		$modDir = empty($module['ex_plug_dir']) ? 
            BUP_MODULES_DIR. $module['code']. DS : 
            utilsBup::getPluginDir($module['ex_plug_dir']). $module['code']. DS; 
        if(is_dir($modDir. 'tables')) {
            $tableFiles = utilsBup::getFilesList($modDir. 'tables');
            if(!empty($tableFiles)) {
                frameBup::_()->extractTables($modDir. 'tables'. DS);
                foreach($tableFiles as $file) {
                    $tableName = str_replace('.php', '', $file);
                    if(frameBup::_()->getTable($tableName))
                        frameBup::_()->getTable($tableName)->$action();
                }
            }
        }
    }
}