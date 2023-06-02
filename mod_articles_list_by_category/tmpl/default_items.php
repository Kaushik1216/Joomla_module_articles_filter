<?php

/**
 * @package     [package]
 * @subpackage  articles list by category
 *
 * @author     Kaushik Vishwakarma kaushik.vishwakarma2003@gmail.com
 * 
 * @copyright  [copyright]
 * @license    [license]
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
?>
<?php foreach ($items as $item) : ?>
<li>
    <?php if ($params->get('link_titles') == 1) : ?>
        <?php $attributes = ['class' => 'mod-articles-category-title ' . $item->active]; ?>
        <?php $link = htmlspecialchars($item->link, ENT_COMPAT, 'UTF-8', false); ?>
        <?php $title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false); ?>
        <?php echo HTMLHelper::_('link', $link, $title, $attributes); ?>
    <?php else : ?>
        <?php echo $item->title; ?>
    <?php endif; ?>

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
