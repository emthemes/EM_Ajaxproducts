<?php
class EM_Ajaxproducts_Model_Config_Altimg
{
	public function toOptionArray()
    {
		return array(
			array('label' => "- None -", 'value' => ""),
			array('label' => "Base Image", 'value' => "image"),
			array('label' => "Small Image", 'value' => "small_image"),
			array('label' => "Thumbnail", 'value' => "thumbnail")
		);
    }
 
}
