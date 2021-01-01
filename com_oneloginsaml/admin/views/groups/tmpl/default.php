<?php
/**
 * @package     Joomla-Saml
 * @subpackage  com_oneloginsaml
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
?>
<form action="index.php?option=com_oneloginsaml&view=groups" method="post" id="adminForm" name="adminForm">
    <table class="table table-striped table-hover">
	<thead>
	    <tr>
		<th width="1%"><?php echo Text::_('COM_ONELOGIN_NUM'); ?></th>
		<th width="2%">
		    <?php echo HTMLHelper::_('grid.checkall'); ?>
		</th>
		<th width="45%"> 
		    <?php echo Text::_('COM_ONELOGIN_GROUP_LOCAL'); ?>
		</th>
		<th width="45%">
		    <?php echo Text::_('COM_ONELOGIN_GROUP_IDP'); ?>
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
	    <?php if (!empty($this->items)) : ?>
		<?php
		foreach ($this->items as $i => $row) :
		    $link = Route::_('index.php?option=com_oneloginsaml&task=groups.editButton&cid=' . $row->id)
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
				<?php echo $row->localName; ?>
			    </a>
			</td>
			<td>
			    <a href="<?php echo $link; ?>" >
				<?php echo $row->idp; ?>
			    </a>
			</td>
			<td align="center">
			    <?php echo $row->id; ?>
			</td>
		    </tr>
		<?php endforeach; ?>
	    <?php endif; ?>
	</tbody>
    </table>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>