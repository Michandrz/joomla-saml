<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

?>
<form action="<?php echo Route::_('index.php?option=com_oneloginsaml&view=config'); ?>"
      method="post" name="adminForm" id="adminForm">
    <ul class="nav nav-tabs">
        <?php
        $first = true;
        foreach ($this->form->getFieldsets() as $fieldset) {
            ?>
            <li class="<?php if ($first) { ?>active<?php } ?>">
                <a href="#page-<?php echo $fieldset->name; ?>" data-toggle="tab"><?php echo Text::_( strtoupper($fieldset->name)); ?></a>
            </li>
            <?php
            $first = false;
        }
        ?>
    </ul>
    <div class="tab-content">
        <?php
        $first = true;
        foreach ($this->form->getFieldsets() as $fieldset) {
            ?>
            <div id="page-<?php echo $fieldset->name; ?>" class="tab-pane <?php if ($first) { ?>active<?php } ?>">
                <legend><?php echo Text::_('COM_ONELOGINSAML_CONFIG_PAGE_' . strtoupper($fieldset->name)); ?></legend>
                <div class="row-fluid"><p class="center"><?php echo $fieldset->description; ?><p></div>
                <fieldset name="<?php echo $fieldset->name; ?>" class="form-horizontal">
                    <?php
                    foreach ($this->form->getFieldset($fieldset->name) as $field) {
                        echo $field->renderField();
                    }
                    ?>
                </fieldset>
            </div>
            <?php
            $first = false;
        }
        ?>
    </div>
    <input type="hidden" name="task" value="attribute.submit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
