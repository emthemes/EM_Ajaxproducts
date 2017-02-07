<?php
class EM_Ajaxproducts_Block_List extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface
{
	protected $_pageSize = 22;
	protected $_numRow = 3;
	protected $_maxPage = null;
	protected function _construct()
    {
		if($this->getCacheLifeTime())
		{
			$this->addData(array(
				'cache_lifetime'    => $this->getCacheLifeTime(),
				'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG)
			));
		}
		else	
		{
			$this->addData(array(
				'cache_lifetime'    => 7200,
				'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG)
			));
		}
		parent::_construct();
		
    }   
	
	public function _prepareLayout()
	{
	
		return parent::_prepareLayout();
	}

	protected function _toHtml()
	{	
		if($this->getData('choose_template')	==	'custom_template')
		{
			if($this->getData('custom_theme'))
			$this->setTemplate($this->getData('custom_theme'));
			else
			$this->setTemplate('em_ajaxproducts/template_custom.phtml');
		}
		else
		{
			$this->setTemplate($this->getData('choose_template'));
		}
		return parent::_toHtml();
	}

	public function getCategories()
	{
		$strCategories=  $this->getData('new_category');
		$arrCategories = explode(",", $strCategories);
		return $arrCategories;
	}

	public function getColumnCount(){
		if($this->getData('column_count'))
			return $this->getData('column_count');
		return -1;
	}

	public function getCustomClass($temp){
		if ($this->getData('custom_class'))
		return $this->getData('custom_class');
		else
		return $temp;
	}

	public function getLimitCount(){
		if($this->getData('limit_count')==null)
		return 1;
		return $this->getData('limit_count');
	}

	public function getOrderBy(){
		return $this->getData('order_by');
	}

	public function getCacheLifeTime(){
		return $this->getData('cache_lifetime');
	}

	public function getThumbnailWidth($temp){
		$tempwidth = $this->getData('thumbnail_width');
		if (!(is_numeric($tempwidth)))
		$tempwidth = $temp;
		return $tempwidth;
	}

	public function getThumbnailHeight($temp){
		$tempheight = $this->getData('thumbnail_height');
		if (!(is_numeric($tempheight)))
		$tempheight = $temp;
		return $tempheight;
	}

	public function getItemWidth(){
        $tempwidth = $this->getData('item_width');
        if (!(is_numeric($tempwidth)))
            $tempwidth = null;
        return $tempwidth;
	}
    
    public function getItemHeight(){
        $tempheight = $this->getData('item_height');
       if (!(is_numeric($tempheight)))
            $tempheight = null;
        return $tempheight;
	}
	
	public function getItemSpacing(){
        $tempspacing= $this->getData('item_spacing');
       if (!(is_numeric($tempspacing)))
            $tempspacing = null;
        return $tempspacing;
	}	
	public function getItemClass($temp){
		if ($this->getData('item_class'))
		return $this->getData('item_class');
		else
		return $temp;
	}
	public function getFrontendTitle(){
		return $this->getData('frontend_title');
	}

	public function getFrontendDescription(){
		return $this->getData('frontend_description');
	}

	public function ShowThumb(){
		return $this->getData('show_thumbnail');
	}
    
    public function getAltImg(){
        return $this->getData('alt_img');
	}
    
    public function ShowProductName(){
        return $this->getData('show_product_name');
	}

	public function ShowDesc(){
		return $this->getData('show_description');
	}

	public function ShowPrice(){
		return $this->getData('show_price');
	}

	public function ShowReview(){
		return $this->getData('show_reviews');
	}

	public function ShowAddtoCart(){
		return $this->getData('show_addtocart');
	}

	public function ShowAddto(){
		return $this->getData('show_addto');
	}

	public function ShowLabel(){
		return $this->getData('show_label');
	}

	public function generateParamsWidget(){
		return base64_encode(json_encode($this->getData()));
	}
	
	public function getIdJsWidget(){
		if(!$idJs = $this->getData('id_js'))
			$this->setData('id_js','ajaxproducts-'.rand(0,100));
		return $this->getData('id_js');	
	}
	
	public function getMaxPage(){
		return $this->_maxPage;
	}
	
	protected function getProductCollection()
	{
		$config2 = $this->getData('order_by');
		if(isset($config2))
		{
			$orders = explode(' ',$config2);
		}
		$products= Mage::getModel('catalog/product')->getCollection()
		//->setStoreId($storeId) // check lai trong multi store
		//->addStoreFilter($store_id) //lay cac san pham trong store hien tai
		->addAttributeToFilter('status', array('neq' => Mage_Catalog_Model_Product_Status::STATUS_DISABLED))
		/*
		 ->joinField(
		 'qty',
		 'cataloginventory/stock_item',
		 'qty',
		 'product_id=entity_id',
		 '{{table}}.stock_id=1',
		 'left'
		 )
		 ->addAttributeToFilter('qty', array('gt' => 0))//*/
		->addAttributeToFilter('visibility',array("neq"=>1));
		//->addAttributeToFilter('em_featured', array('gt' => 0));
		//Sort
		if(count($orders))
		$products->addAttributeToSort($orders[0],$orders[1]);
		else
		$products->addAttributeToSort('name', 'asc');
		//Filter by categories
		$config1 = $this->getData('new_category');
		if($config1)
		{
			$result = array();
			$condition_cat = array();
			$alias = 'cat_index';
			$categoryCondition = $products->getConnection()->quoteInto(
			$alias.'.product_id=e.entity_id AND '.$alias.'.store_id=? AND ',
			$products->getStoreId()
			);
			$categoryCondition.= $alias.'.category_id IN ('.$config1.')';
			$products->getSelect()->joinInner(
			array($alias => $products->getTable('catalog/category_product_index')),
			$categoryCondition,
			array()
			);
			$products->_categoryIndexJoined = true;
			$products->distinct(true);
		}
		
		//Page size & CurPage
		$curPage = $this->getRequest()->getParam('p',1);
		$columnCount = $this->getColumnCount();
		if($columnCount != -1){
			$this->_pageSize = $this->_numRow*$columnCount;
		}
			
		$products->setPageSize($this->_pageSize);
		$products->setCurPage($curPage);
		$this->_maxPage = ceil(min($this->getLimitCount(),$products->getSize())/$this->_pageSize);
		$products->addAttributeToSelect('*');

		$this->_addProductAttributesAndPrices($products);
		return $products;

	}
}
