<?php

/**
 * Pagination
 *
 * @author marcelo.jacobus
 */
class App_View_Helper_Pagination extends Zend_View_Helper_Url
{

    /**
     * @var Doctrine_Pager
     */
    protected $_pager;
    /**
     * Caches html
     * @var string
     */
    private $_buffer;
    /**
     * Range of pages do display
     * @var int
     */
    private $_range = 10;
    /**
     * Stores module, controller and action
     * @var array
     */
    private $_urlParts = array();

    /**
     * Get pagination heler
     * @return Zend_View_Helper_Pagination
     */
    public function pagination()
    {
        return $this;
    }

    /**
     * Return
     * @return Zend_View_Helper_Pagination
     */
    private function _setUp()
    {
        $c = Zend_Controller_Front::getInstance()->getRequest();

        $urlParts = array(
            'module' => $c->getModuleName(),
            'controller' => $c->getControllerName(),
            'action' => null,
        );

        $this->_urlParts = $urlParts;
        return $this;
    }

    /**
     * Gets the url for a given page
     * @param int $page
     * @return string the url for given page
     */
    public function getUrlForPage($page)
    {
        Zend_Controller_Front::getInstance()->getRequest()->setParam('page', $page);
        return $this->url($this->_urlParts, null, true);
    }

    /**
     * Return the rendered html for pagination
     * @return string
     */
    public function render($range = null)
    {
        if ($range == null) {
            $range = $this->_range;
        }

        $this->_setUp();
        $pager = $this->getPager();
        $pagerRange = $pager->getRange(
                'Sliding',
                array(
                    'chunk' => (int) $range
                )
        );
        $pages = $pagerRange->rangeAroundPage();
        $html = '';

        if ($pager->haveToPaginate()) {
            $html = '<div class="pagination"><ul>';

            if (!in_array(1, $pages)) {
                $html .= '<li class="first">' . $this->getLink(1, '&lt;&lt;') . '</li>';
            }

            foreach ($pages as $page) {
                $class = ($pager->getPage() == $page) ? ' class="active"' : '';
                $html .='<li' . $class . '>' . $this->getLink($page) . '</li>';
            }

            $next = $pager->getNextPage();

            if (!in_array($pager->getLastPage(), $pages)) {
                $html .= '<li class="last">' . $this->getLink($pager->getLastPage(), '&gt;&gt;') . '</li>';
            }

            $html .= '</ul></div>';
        }

        $this->_buffer = $html;
        return $html;
    }

    /**
     * get link for page
     * @param int $page
     * @param label $label
     * @return string
     */
    public function getLink($page, $label = null)
    {
        if ($label === null) {
            $label = $page;
        }
        return '<a class="ajax-load" title="' . $page . '" href="' . $this->getUrlForPage($page) . '">' . $label . '</a>';
    }

    /**
     * @param Doctrine_Pager $pager
     * @return Zend_View_Helper_Pagination
     */
    public function setPager(Doctrine_Pager $pager)
    {
        $this->_pager = $pager;
        return $this;
    }

    /**
     * Get Pager
     * @return Doctrine_Pager
     */
    public function getPager()
    {
        return $this->_pager;
    }

    /**
     * Generates an url given the name of a route.
     *
     * @access public
     *
     * @param  array $urlOptions Options passed to the assemble method of the Route object.
     * @param  mixed $name The name of a Route to use. If null it will use the current Route
     * @param  bool $reset Whether or not to reset the route defaults with those provided
     * @return string Url for the link href attribute.
     */
    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $url = parent::url($urlOptions, $name, $reset, $encode);

        $data = $c = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        unset($data['module']);
        unset($data['action']);
        unset($data['controller']);
        $get = str_replace('&amp;', '&', http_build_query($data));

        if (strlen(trim($get, '?'))) {
            $url .= '?' . $get;
        }
        return $url;
    }

    /**
     *
     * @return String
     */
    public function __toString()
    {
        return $this->render();
    }

}