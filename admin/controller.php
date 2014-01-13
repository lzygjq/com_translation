<?php
// no dierect access
defined ( "_JEXEC" ) or die ( "restricted access" );

// import the controller class file
jimport ( "joomla.application.component.controller" );

/**
 * base controller for Translation
 * */
class TranslationController extends JControllerLegacy {
	/**
	 * method to display a view
	 * @param boolen	if true the view output will be cached
	 * @param array		An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 * @return JController		This object to support chaining.
	 * */
	public function display($cachable = "false", $urlparams = "false") {
		parent::display($cachable = "false", $urlparams = "false");
	}
}
