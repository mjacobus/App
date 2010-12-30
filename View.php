<?php

/**
 * @see Zend_View
 */
require_once 'Zend/View.php';

/**
 * @author marcelo.jacobus
 */
class App_View extends Zend_View
{

    /**
     * The icons
     * @var array
     */
    protected $_icons = array();

    /**
     * Get request
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        return Zend_Controller_Front::getInstance()->getRequest();
    }

    /**
     * Get crud base url for the current request
     * @param string $append
     */

    /**
     *
     * @param string $action
     * @param int $id
     * @return string 
     */
    public function getCrudUrl($action, $id = null)
    {
        $request = $this->getRequest();
        $params = array(
            'module' => $request->getModuleName(),
            'controller' => $request->getControllerName(),
            'action' => $action,
        );

        if ($id !== null) {
            $params['id'] = $id;
        }
        
        return $this->url($params, 'default', false) . $this->getExtraParams();
    }

    /**
     * Create url
     * @return string
     */
    public function c()
    {
        return $this->getCrudUrl('create');
    }

    /**
     * Read url
     * @param id $id
     * @return string
     */
    public function r($id)
    {
        return $this->getCrudUrl('read', $id);
    }

    /**
     * Update url
     * @param id $id
     * @return string
     */
    public function u($id)
    {
        return $this->getCrudUrl('update', $id);
    }

    /**
     * Delete url
     * @param id $id
     * @return string
     */
    public function d($id)
    {
        return $this->getCrudUrl('delete', $id);
    }

    /**
     * Create image
     * @return string
     */
    public function ci()
    {
        return $this->getIcon('create');
    }

    /**
     * Read image
     * @return string
     */
    public function ri()
    {
        return $this->getIcon('read');
    }

    /**
     * Update image
     * @return string
     */
    public function ui()
    {
        return $this->getIcon('update');
    }

    /**
     * Del image
     * @return string
     */
    public function di()
    {
        return $this->getIcon('delete');
    }

    /**
     *
     * @param string $name
     * @param string $icon
     * @return App_View
     */
    public function setIcon($name, $icon)
    {
        $this->_icons[$name] = $icon;
        return $this;
    }

    /**
     * Set icons
     * @param string $name
     * @return string
     * @throws App_Exception when no icon was set
     */
    public function getIcon($name)
    {
        if (isset($this->_icons[$name])) {
            $icon = $this->_icons[$name];
            if (!preg_match('/^[\w]{3,4}:\/\/.*/', $icon)) {
                $icon = $this->baseUrl($icon);
            }
            return $icon;
        }
        return sprintf('There is no "%s" icon', $name);
    }

    /**
     * Set the icons
     * @param array $icons
     * @return App_View
     */
    public function setIcons(array $icons)
    {
        foreach ($icons as $name => $icon) {
            $this->setIcon($name, $icon);
        }
        return $this;
    }

    /**
     * Get icons
     * @return array
     */
    public function getIcons()
    {
        return $this->_icons;
    }

    /**
     * Crud Check
     * @return Zend_Form_Element_Checkbox
     */
    public function cc($id)
    {
        $check = new Zend_Form_Element_MultiCheckbox('ids');
        $check->addMultiOption($id)
            ->setDecorators(array('viewHelper'))
            ->setAttrib('class', 'crud_id');
        return $check;
    }

    /**
     * Get the link to reordenate the list
     * @param string $order
     * @param string $direction
     */
    public function order($label, $order = null)
    {
        if ($order === null) {
            $order = strtolower($label);
        }

        $request = $this->getRequest();
        $params = $request->getParams();

        if ($request->getParam('order')) {
            $orders = explode(',', $request->getParam('order'));
        } else {
            $orders = array();
        }

        foreach ($orders as $i => $oldOrder) {
            $parts = explode('_', $oldOrder);

            if (strtolower($parts[0]) == strtolower($order)) {
                unset($orders[$i]);
            }
        }

        $newOrder = strtolower($order) . '_asc';
        array_unshift($orders, $newOrder);
        $params['order'] = implode(',', $orders);

        $url = $this->url($params, null, true, false);
        $html = "<a class=\"order\" href=\"$url\"><img src=\"" . $this->baseUrl("/img/order_asc.gif") . "\"/></a>";
        $html .= $label;

        $newOrder = strtolower($order) . '_desc';
        array_shift($orders);
        array_unshift($orders, $newOrder);
        $params['order'] = implode(',', $orders);

        $url = $this->url($params, null, true, false);
        $html .= "<a class=\"order\" href=\"$url\"><img src=\"" . $this->baseUrl("/img/order_desc.gif") . "\"/></a>";

        return $html;
    }

    /**
     * Get param
     * @param string $name
     * @param mixed $default
     * @return string
     */
    public function param($name, $default = '')
    {
        return $this->getRequest()->getParam($name, $default);
    }

    /**
     * Get params for search, order, page and per page.
     */
    public function getExtraParams()
    {
        $data = array();
        $extraParams = array('search', 'order', 'per-page', 'page');
        $request = $this->getRequest();

        foreach ($extraParams as $param) {
            if ($request->getParam($param)) {
                $data[$param] = $request->getParam($param);
            }
        }

        if (count($data)) {
            $get = str_replace('&amp;', '&', http_build_query($data));
            if (strlen(trim($get, '?'))) {
                return '?' . $get;
            }
        }

        return '';
    }

}