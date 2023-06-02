<?php

/**
 * @package     kaushik.Site
 * @subpackage  mod_articles_list_category
 *
 * @copyright   [copyright]
 * @license     [license]
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

?>
<?php foreach ($items as $item) : ?>
<li>
    <?php ?>
        <?php $attributes = ['class' => 'mod-articles-category-title ' . $item->active]; ?>
        <?php $link = htmlspecialchars($item->link, ENT_COMPAT, 'UTF-8', false); ?>
        <?php $title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false); ?>
        <?php echo HTMLHelper::_('link', $link, $title, $attributes); ?>
    <?php ?>

    <?php if ($params->get('show_author')) : ?>
        <span class="mod-articles-category-writtenby">
            <?php echo $item->displayAuthorName; ?>
        </span>
    <?php endif; ?>

    <?php if ($item->displayDate) : ?>
        <span class="mod-articles-category-date"><?php echo $item->displayDate; ?></span>
    <?php endif; ?>

    <?php if ($params->get('show_introtext')) : ?>
        <p class="mod-articles-category-introtext">
            <?php echo $item->displayIntrotext; ?>
        </p>
    <?php endif; ?>

</li>
<?php endforeach; ?>
