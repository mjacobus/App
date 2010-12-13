<?php

/**
 * @see Doctrine_Pager
 */
require_once 'Doctrine/Pager.php';

/**
 * Pager for array
 *
 * @author marcelo.jacobus
 */
class App_Pager_Array extends Doctrine_Pager
{

    /**
     * The elements
     * @var array
     */
    protected $_elements = array();

    /**
     * __construct
     *
     * @param array array of elements to paginate
     * @param int $page     Current page
     * @param int $maxPerPage     Maximum itens per page
     * @return void
     */
    public function __construct(array $elements, $page, $maxPerPage = 0)
    {
        $this->setElements($elements);
        $this->_setExecuted(true);
        $this->setPage($page);
        $this->setMaxPerPage($maxPerPage);
        $this->setUp();
    }

    /**
     * Set the page
     * @param int $page
     * @return Admin_Model_PagerArray
     */
    public function setPage($page)
    {
        $page = intval($page);
        $this->_page = ($page <= 0) ? 1 : $page;
        return $this;
    }

    /**
     * Set the elements
     * @param array $elements
     */
    public function setElements(array $elements)
    {
        $this->_elements = $elements;
    }

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_setNumResults(count($this->_elements));
        $this->_setExecuted(true); // _adjustOffset relies of _executed equals true = getNumResults()

        $this->_setLastPage(
            max(1, ceil($this->getNumResults() / $this->getMaxPerPage()))
        );
    }

    /**
     * Get the elements from a page
     * @param int $page if null, get all the elements
     * @return array
     */
    public function getElements($page = null)
    {
        if ($page !== null) {
            $index = ($page - 1) * $this->getMaxPerPage();
            return array_slice($this->_elements, $index, $this->getMaxPerPage());
        }
        return $this->_elements;
    }

}