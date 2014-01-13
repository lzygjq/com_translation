<?php
//defined no direct access
defined("_JEXEC") or die("restricted access");
jimport('joomla.application.component.view');
//extends the JView class
class TranslationViewTranslation extends JViewLegacy {
	
	function display($tpl=null) {
		$this->msg=$this->get('Dir');
		$this->msg=$this->get('File');
		parent::display($tpl);
	}
}
