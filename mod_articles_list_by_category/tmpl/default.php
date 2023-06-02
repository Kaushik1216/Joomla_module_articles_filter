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

use Joomla\CMS\Helper\ModuleHelper;

if (!$list) {
    return;
}

?>
<ul class="mod-articlescategory category-module mod-list">
   
    <?php $items = $list; ?>
    <?php require ModuleHelper::getLayoutPath('mod_articles_list_by_category', $params->get('layout', 'default') . '_items'); ?>

</ul>
