<?php
/**
 *
 * @package zfCustomError
 * @author Denis Uraganov
 *
 */

/**
 * Index Controller
 * @package zfCustomError
 * @author Denis Uraganov
 *
 */
class IndexController extends Zend_Controller_Action
{

    public function init ()
    {
        /*
         * Initialize action controller here
         */
    }

    public function indexAction ()
    {
        // action body
    }

    public function noaccessAction ()
    {
        // action body
        throw new Core_Exception_NoAccess();
    }

    public function noaccessCustomAction ()
    {
        // action body
        throw new Core_Exception_NoAccess('No access custom msg');
    }

    public function notfoundAction ()
    {
        // action body
        throw new Core_Exception_ItemNotFound();
    }

    public function notfoundCustomAction ()
    {
        // action body
        throw new Core_Exception_ItemNotFound('Not found custom msg');
    }

    public function maintenanceAction ()
    {
        // action body
        throw new Core_Exception_MaintenanceMode();
    }

    public function maintenanceCustomAction ()
    {
        // action body
        throw new Core_Exception_MaintenanceMode('Maintenance custom msg');
    }
}