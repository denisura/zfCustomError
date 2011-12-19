<?php

/**
 *
 * @package zfCustomError
 * @author Denis Uraganov
 *
 */

/**
 * Error Controller
 * @package zfCustomError
 * @author Denis Uraganov
 *        
 */
class ErrorController extends Zend_Controller_Action
{

    /**
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        $this->_errors = $this->_getParam('error_handler');
    }

    /**
     * Pass to view exception and request details
     *
     * @see Zend_Controller_Action::preDispatch()
     */
    public function preDispatch ()
    {
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $this->_errors->exception);
        }
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $this->_errors->exception;
        }
        
        $this->view->request = $this->_errors->request;
    }

    /**
     * Default Error handler action
     */
    public function errorAction ()
    {
        
        switch ($this->_errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
    }

    /**
     * Custom Error handler action for Core_Exception_ItemNotFound exception
     */
    public function itemAction ()
    {
        $this->getResponse()->setHttpResponseCode(404);
        $this->view->message = $this->_errors->exception->getMessage();
    }

    /**
     * Custom Error handler action for Core_Exception_NoAccess exception
     */
    public function noaccessAction ()
    {
        $this->getResponse()->setHttpResponseCode(401);
        $this->view->message = $this->_errors->exception->getMessage();
    }

    /**
     * Custom Error handler action for Core_Exception_MaintenanceMode exception
     */
    public function maintenanceAction ()
    {
        $this->view->message = $this->_errors->exception->getMessage();
    }

    /**
     *
     * @return Ambigous <NULL, mixed>|boolean
     */
    public function getLog ()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if ($bootstrap instanceof Zend_Application_Bootstrap_Bootstrap &&
                 $bootstrap->hasPluginResource('Log')) {
                    return $bootstrap->getResource('Log');
                }
                return false;
            }
        }