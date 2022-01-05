<?php

class Zend_View_Helper_SomeElement extends Zend_View_Helper_Abstract
{
    public function someElement()
    {
        $html = '';
        if($this->view->acl->isAllowed('guest', null, 'view')){
           $html .= "<div>Top secret content</div>\n";
        }
        return $html;
    }
}
?>
