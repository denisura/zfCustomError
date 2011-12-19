<?php
/**
 *
 * @package zfCustomError
 * @subpackage Plugins
 */

/**
 * Handle exceptions that bubble up based on missing controllers, actions, 
 * application errors, or user defined errors and forward to an error handler.
 * 
 * To handle custom error:
 *  1. Define exception constant
 *  2. Define Error Handler Action for custom exception 
 *  <code>
 *    case 'Core_Exception_CustomOne':
 *         $error->type = self::EXCEPTION_CUSTOM_ONE;
 *         $this->setErrorHandlerAction('customone');
 *         break;
 *  </code>
 * 
 * @uses Zend_Controller_Plugin_ErrorHandler
 * @package zfCustomError
 * @subpackage Plugins
 */
class Core_Controller_Plugin_ErrorHandler extends Zend_Controller_Plugin_ErrorHandler
{

    /**
     * Const - Item not found exception; item does not exist
     */
    const EXCEPTION_ITEM_NOT_FOUND = 'EXCEPTION_ITEM_NOT_FOUND';

    /**
     * Const - No access exception; Actor has no access to the resource
     */
    const EXCEPTION_NO_ACCESS = 'EXCEPTION_NO_ACCESS';

    /**
     * Const - Maintenance mode exception; Site section in maintenance mode
     */
    const EXCEPTION_MAINTENANCE_MODE = 'EXCEPTION_MAINTENANCE_MODE';
    
    // @todo add more EXCEPTION constant if needed to define exception type
    // const EXCEPTION_NAME = 'EXCEPTION_NAME';
    
    /**
     * Handle errors and exceptions
     *
     * If the 'noErrorHandler' front controller flag has been set,
     * returns early.
     *
     * @param $request Zend_Controller_Request_Abstract           
     * @return void
     */
    protected function _handleError (Zend_Controller_Request_Abstract $request)
    {
        $frontController = Zend_Controller_Front::getInstance();
        $response = $this->getResponse();
        
        if ($this->_isInsideErrorHandlerLoop) {
            $exceptions = $response->getException();
            if (count($exceptions) > $this->_exceptionCountAtFirstEncounter) {
                // Exception thrown by error handler; tell the front controller
                // to throw it
                $frontController->throwExceptions(true);
                throw array_pop($exceptions);
            }
        }
        
        // check for an exception AND allow the error handler controller the
        // option to forward
        if (($response->isException()) && (! $this->_isInsideErrorHandlerLoop)) {
            $this->_isInsideErrorHandlerLoop = true;
            
            // Get exception information
            $error = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
            $exceptions = $response->getException();
            $exception = $exceptions[0];
            $exceptionType = get_class($exception);
            $error->exception = $exception;
            switch ($exceptionType) {
                case 'Zend_Controller_Router_Exception':
                    if (404 == $exception->getCode()) {
                        $error->type = self::EXCEPTION_NO_ROUTE;
                    } else {
                        $error->type = self::EXCEPTION_OTHER;
                    }
                    break;
                case 'Zend_Controller_Dispatcher_Exception':
                    $error->type = self::EXCEPTION_NO_CONTROLLER;
                    break;
                case 'Zend_Controller_Action_Exception':
                    if (404 == $exception->getCode()) {
                        $error->type = self::EXCEPTION_NO_ACTION;
                    } else {
                        $error->type = self::EXCEPTION_OTHER;
                    }
                    break;
                case 'Core_Exception_ItemNotFound':
                    $error->type = self::EXCEPTION_ITEM_NOT_FOUND;
                    $this->setErrorHandlerAction('item');
                    break;
                case 'Core_Exception_NoAccess':
                    $error->type = self::EXCEPTION_NO_ACCESS;
                    $this->setErrorHandlerAction('noaccess');
                    break;
                case 'Core_Exception_MaintenanceMode':
                    $error->type = self::EXCEPTION_MAINTENANCE_MODE;
                    $this->setErrorHandlerAction('maintenance');
                    break;
                // @todo add more cases for custom exceptions
                // case 'Core_Exception_ExceptionName' :
                // $error->type = self::EXCEPTION_NAME;
                // $this->setErrorHandlerAction('ExceptionName');
                // break;
                default:
                    $error->type = self::EXCEPTION_OTHER;
                    break;
            }
            
            // Keep a copy of the original request
            $error->request = clone $request;
            
            // get a count of the number of exceptions encountered
            $this->_exceptionCountAtFirstEncounter = count($exceptions);
            
            // Forward to the error handler
            $request->setParam('error_handler', $error)
                ->setModuleName($this->getErrorHandlerModule())
                ->setControllerName($this->getErrorHandlerController())
                ->setActionName($this->getErrorHandlerAction())
                ->setDispatched(false);
        }
    }
}