<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        http://www.gnu.org/licenses/gpl-2.0-standalone.html
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
$document 			= JFactory::getDocument();
$document->addStyleSheet('components/com_auth0/assets/auth0back.css');

?>
<div class="span6">
    <div class="well well-small">
        <div class="module-title nav-header"><?php echo JText::_('COM_AUTH0'); ?>
        (v <?php
       	$xml = JFactory::getXML(JPATH_SITE .'/administrator/components/com_auth0/com_auth0.xml');
		$version = @(string)$xml->version;

		if($version){
			echo $version;
		}else{
			echo '';
		}
		?>) - <?php echo JText::_('COM_AUTH0_INSTRUCTIONS'); ?></div>
        <div class="row-striped">
          <div class="row-fluid">1. <?php echo JText::sprintf(JText::_('COM_AUTH0_CREATE_ACCOUNT'),'<a href="https://auth0.com/" target="_blank">here</a>' ); ?></div>
          <div class="row-fluid">2. <?php echo JText::sprintf(JText::_('COM_AUTH0_CREATE_APP'),'<a href="https://manage.auth0.com/#/applications" target="_blank">here</a>' ); ?></div>
          <div class="row-fluid">3. <?php echo JText::_('COM_AUTH0_COPY_APP_DATA'); ?></div>
          <div class="row-fluid">4. <?php echo JText::_('COM_AUTH0_CONFIGURE_WIDGET'); ?></u></div>
          <div class="row-fluid">5. <?php echo JText::_('COM_AUTH0_CONFIGURE_REGISTRATION'); ?></div>
        </div>
    </div>
</div>
<div class="span4">



</div>
