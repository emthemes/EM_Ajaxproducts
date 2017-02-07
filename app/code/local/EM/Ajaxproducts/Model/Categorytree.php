<?php
class EM_Ajaxproducts_Model_Categorytree extends Mage_Core_Model_Abstract
{
 	//protected  $category_tree= array();
 	protected  $category_tree= '';
	public function toOptionArray2()
	{
	}

	function getCategoriesCustom($parent,$result){
		$result[]=array('value' => count($parent->getChildrenCategories()),'label' =>  $this->getCatNameCustom($parent));
		$childrens =  $parent->getChildrenCategories();
		if(count($childrens)){
			foreach($childrens as $child){
				$result[] = $this->getCategoriesCustom($child,$result);
			}
		}
		return $result;
	}

	function func1(){
		$cat_mod = Mage::getModel('catalog/category');
		$_main_categories=$this->getStoreCategories();
		if ($_main_categories):
		foreach ($_main_categories as $_main_category):
		if($_main_category->getIsActive()):
		$cid = $_main_category->getId();
		$cur_category = $cat_mod->load($cid);
		$category_name = $cur_category->getName();
		echo '-'.$category_name.'<br/>';
		res($cur_category,'-');
		endif;
		endforeach;
		endif;
	}

	function res($cur_category,$s)
	{
		$children_categories = $cur_category->getChildrenCategories();
		if(!empty($children_categories))
		{
			$s .= $s;
			foreach($children_categories as $k => $v)
			{
				$all_data = $v->getData();
				$nm = $all_data['name'];
				echo $s.$nm.'<br/>';
				res($v,$s);
			}
		}
	}

	function getCatNameCustom($category)
	{
		$level = $category->getLevel();
		$html = $level.'';
		for($i = 0;$i < $level;$i++){ $html .= '___'; }
		return	$html.' '.$category->getName();
	}

	function nodeToArray(Varien_Data_Tree_Node $node)
	{
		$result = array();
		$result['category_id'] = $node->getId();
		$result['parent_id'] = $node->getParentId();
		$result['name'] = $node->getName();
		$result['is_active'] = $node->getIsActive();
		$result['position'] = $node->getPosition();
		$result['level'] = $node->getLevel();
		$result['children'] = array();

		foreach ($node->getChildren() as $child) {
			$result['children'][] = $this->nodeToArray($child);
		}

		return $result;
	}

	function load_tree() {
		$store = 1;
		$parentId = 1;
		$tree = Mage::getResourceSingleton('catalog/category_tree')
		->load();
		$root = $tree->getNodeById($parentId);

		if($root && $root->getId() == 1) {
			$root->setName(Mage::helper('catalog')->__('Root'));
		}

		$collection = Mage::getModel('catalog/category')->getCollection()
		->setStoreId($store)
		->addAttributeToSelect('name')
		->addAttributeToSelect('is_active');

		$tree->addCollectionData($collection, true);

		return $this->nodeToArray($root);

	}

	function print_tree($tree,$level) {
		$level ++;
		foreach($tree as $item) {
			$this->category_tree[]=array('value' => $item['category_id'],'label' => str_repeat("__", $level).$item['name']);
			$this->print_tree($item['children'],$level);
		}
	}
	public function toOptionArray()
	{
		$tree = $this->load_tree();
		$this->print_tree($tree['children'],0);
		
		return $this->category_tree;

	}

}
?>
