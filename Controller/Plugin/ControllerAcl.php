<?php

/**
 * Plugin for ACL
 *
 * @author marcelo.jacobus
 */
class App_Controller_Plugin_ControllerAcl extends Zend_Controller_Plugin_Abstract
{

    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        
        if (Admin_Model_Authentication::isLogged()) {
            $user = Admin_Model_Authentication::getIdentity();
            $role = $user->Role->name;
        } else {
            $role = 'guest';
        }


        $resources = array(
            "module-$module-controller-$controller-action-$action",
            "module-$module-controller-$controller",
            "module-$module",
        );


        $acl = App_Acl::getInstance();

        foreach ($resources as $resource) {
            if ($acl->has($resource)) {
                if (!$acl->isAllowed($role, $resource)) {
                    $this->handleDenied($request);
                }
                break;
            }
        }
    }

    /**
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function handleDenied(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()->setRedirect($this->getAbsoluteUrl(), 301);
    }

    /**
     *
     * @return string
     */
    public function getAbsoluteUrl()
    {
        $protocol = explode('/', $_SERVER['SERVER_PROTOCOL']);
        $protocol = strtolower($protocol[0]) . '://';

        $port = ':' . $_SERVER['SERVER_PORT'];

        if (($protocol == 'http://' && $port == ':80') || ($protocol == 'https://' && $port == ':443')) {
            $port = '';
        }

        $host = $_SERVER['HTTP_HOST'];
        $url = $protocol . $host . $port;

        return $url . Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/authentication';
    }

}
