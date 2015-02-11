<?php

/**
 * Microsoft OneDrive Module for Backup By Supsystic.
 * @package BackupBySupsystic\Modules\OneDrive
 * @version 1.0
 */
class onedriveBup extends moduleBup
{
    /**
     * @var array
     */
    protected $tab;

    /**
     * @var array
     */
    protected $storage;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     */
    public function __construct($d, $params = array())
    {
        parent::__construct($d, $params);

        $this->tab = array(
            'action' => 'indexAction',
            'key'    => 'bupOneDriveOptions',
            'title'  => 'OneDrive',
        );

        $this->storage = array(
            'action'   => 'uploadAction',
            'label'    => 'OneDrive',
            'provider' => 'onedrive',
        );

        $this->options = array(
            'onedrive_client_id'     => '000000004413CF67',
            'onedrive_client_secret' => 'jZ9BU1k812PYIGxTCQqnXHauXrwOuYB9',
        );
    }

    public function init()
    {
        parent::init();

        include rtrim($this->getModDir(), '/') . '/classes/skydriveBup.php';

        dispatcherBup::addFilter(
            'adminCloudServices',
            array($this, 'registerMenuItem')
        );

        dispatcherBup::addFilter(
            'adminSendToLinks',
            array($this, 'registerStorage')
        );

        dispatcherBup::addfilter(
            'adminBackupUpload',
            array($this, 'registerUploadMethod')
        );
    }

    public function registerMenuItem($tabs)
    {
        if (is_admin() && frameBup::_()->isPluginAdminPage()) {
            frameBup::_()
                ->addScript(
                    'adminOneDriveOptions',
                    $this->getModPath(). 'js/onedrive.admin.js'
                );
        }

        $tabs[$this->tab['key']] = array(
            'content' => $this->run($this->tab['action']),
            'title'   => $this->tab['title'],
            'faIcon' => ' fa-cloud-upload',
        );

        return $tabs;
    }

    public function registerStorage($storages)
    {
        array_push($storages, $this->storage);

        return $storages;
    }

    public function registerUploadMethod($methods)
    {
        $model = $this->getController()->getModel();
        $methods['onedrive'] = array($model, 'upload');

        return $methods;
    }

    public function run($action)
    {
        $controller = $this->getController();

        if (method_exists($controller, $action)) {
            return $controller->{$action}();
        }
    }
}
