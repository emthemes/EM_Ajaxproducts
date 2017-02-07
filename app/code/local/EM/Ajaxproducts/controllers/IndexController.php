<?php
class EM_Ajaxproducts_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$params = $this->getRequest()->getParam('params');
        $params = base64_decode($params);
        $params = json_decode($params,true);
		$featuredBlock = $this->getLayout()->createBlock('ajaxproducts/list')->setData($params);
		$this->getResponse()->setBody($featuredBlock->toHtml());
    }
}