<?php
/**
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
Use Joomla\CMS\Router\Route;

?>
<form action="index.php?option=com_oneloginsaml&view=attributes" method="post" id="adminForm" name="adminForm">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th width="1%"><?php echo Text::_('COM_ONELOGIN_NUM'); ?></th>
                <th width="2%">
                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                </th>
                <th width="40%"> 
                    <?php echo Text::_('COM_ONELOGIN_ATTR_LOCAL'); ?>
                </th>
                <th width="40%">
                    <?php echo Text::_('COM_ONELOGIN_ATTR_IDP'); ?>
                </th>
                <th width="10%">
                    <?php echo Text::_('COM_ONELOGIN_ATTR_MATCHER'); ?>
                </th>
                <th width="2%">
                    <?php echo Text::_('COM_ONELOGIN_ID'); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="5">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php if (!empty($this->items)) { ?>
                <?php
                foreach ($this->items as $i => $row) {
                    $link = Route::_('index.php?option=com_oneloginsaml&task=attributes.editButton&cid=' . $row->id);
                    ?>

                    <tr>
                        <td>
                            <?php echo $this->pagination->getRowOffset($i); ?>
                        </td>
                        <td>
                            <?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
                        </td>
                        <td>
                            <a href="<?php echo $link; ?>" >
                                <?php echo $row->local; ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo $link; ?>" >
                                <?php echo $row->idp; ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($row->match) { ?>
                                <span><?php Text::_('COM_ONELOGIN_ATTR_CURRENT_MATCHER'); ?></span>
                            <?php } else { ?>
                                <a href="<?php echo Route::_('index.php?option=com_oneloginsaml&task=attributes.setMatcher&id=' . $row->id); ?>"><?php echo Text::_('COM_ONELOGIN_ATTR_SET_MATCHER'); ?> </a>
                            <?php } ?>
                        </td>
                        <td align="center">
                            <?php echo $row->id; ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>