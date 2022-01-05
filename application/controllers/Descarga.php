<?php

class DescargaController extends Zend_Controller_Action
{

    

    public function indexAction()
	{
        
        $file=get
	   if ($file !== false) {
	$mtype = '';
	$file_path = _fullUploadPath().$file->image();
 
	// magic_mime module installed?
	if (function_exists('mime_content_type')) {
		$mtype = mime_content_type($file_path);
	}
	// fileinfo module installed?
	else if (function_exists('finfo_file')) {
		$finfo = finfo_open(FILEINFO_MIME); // return mime type
		$mtype = finfo_file($finfo, $file_path);
		finfo_close($finfo); 
	}
}
 
header($mtype);
header('Content-Disposition: attachment; filename="'.$file->image().'"');
readfile(_fullUploadPath().$file->image());
 
	    // disable layout and view
	    $this->view->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	}	
 
	public function errorAction()
	{}



}







