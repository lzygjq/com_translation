<?php
//no direct access
defined("_JEXEC") or die("restricted access");

//get the instance of the controller
$controller=JControllerLegacy::getInstance('Translation');

//get the task from the url
$input=JFactory::getApplication()->input->get('task','display');

//execute the task
$controller->execute($input);

//redirect the url
$controller->redirect();
