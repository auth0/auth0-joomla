<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        The MIT License (MIT), see LICENSE
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.application');
$app = JFactory::getApplication();
$document = JFactory::getDocument();
$user = JFactory::getUser();
$myparams = JComponentHelper::getParams('com_auth0');
$document->addStyleSheet('components/com_auth0/assets/auth0back.css');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

if (!$this->items) {
    if (strlen($this->lists['search']) > 1) {
        $app->enqueueMessage('No Results found containing "' . htmlspecialchars($this->lists['search']) . '"!', 'error');
    } else {
        $app->enqueueMessage('No Connected Users yet', 'message');
    }
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_auth0'); ?>" method="post" name="adminForm" id="adminForm">

    <table class="table table-striped" id="articleList">
        <thead>
        <tr>
            <th width="10">
                <?php echo JHtml::_('grid.sort', JText::_('ID'), 'id', $listDirn, $listOrder); ?> </th>
            <th>
                <?php echo JHtml::_('grid.sort', JText::_('Email'), 'email', $listDirn, $listOrder); ?> </th>
            <th>
                <?php echo JText::_('Username'); ?> </th>
            <th>
                <?php echo JText::_('Auth0 ID'); ?>
            </th>
            <th>
                <?php echo JHtml::_('grid.sort', JText::_('Registered Date'), 'registerDate', $listDirn, $listOrder); ?>            </th>
        </tr>
        </thead>
        <tbody><?php
        $k = 0;
        for ($i = 0, $n = count($this->items); $i < $n; $i++) {
            $row =& $this->items[$i];
            $checked = JHTML::_('grid.id', $i, $row->id);
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php echo $row->id; ?></td>
                <td>
                    <?php
                    if (version_compare(JVERSION, '1.6.0', 'ge')) {
                        ?>
                        <a href="index.php?option=com_users&task=user.edit&id=<?php echo $row->id; ?>&extension=COM_AUTH0"><?php echo $row->email; ?></a>
                    <?php } else { ?>
                        <a href="index.php?option=com_users&view=user&task=edit&cid[]=<?php echo $row->id; ?>&extension=COM_AUTH0"><?php echo $row->email; ?></a>
                    <?php } ?>
                </td>
                <td>
                    <?php echo $row->username; ?></td>
                <td><?php echo($row->auth0id); ?></td>
                <td>
                    <?php echo $row->joineddate; ?></td>
            </tr>



            <?php
            $k = 1 - $k;
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="9" align="center">
                <div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
            </td>
        </tr>
        </tfoot>
    </table>


    <input type="hidden" name="view" value="users"/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
