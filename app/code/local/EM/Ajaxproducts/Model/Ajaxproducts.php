<?php
class EM_Ajaxproducts_Model_Ajaxproducts extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ajaxproducts/ajaxproducts');
    }
}