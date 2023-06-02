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

namespace KaushikVishwakarma\Module\ArticlesListByCategory\Site\Helper;

use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper for mod_articles_list_by_category
 *
 * @since  1.6
 */

class ArticlesCategoryHelper implements DatabaseAwareInterface
{
   use DatabaseAwareTrait;
 
    /**
     * Retrieve a list of article
     *
     * @param   Registry         $params  The module parameters.
     * @param   SiteApplication  $app           The current application.
     *
     * @return  object[]
     *
     * @since   __DEPLOY_VERSION__
     */
    public function getArticles(Registry $params, SiteApplication $app)
    {
        $factory = $app->bootComponent('com_content')->getMVCFactory();
 
        // Get an instance of the generic articles model
        $articles = $factory->createModel('Articles', 'Site', ['ignore_request' => true]);
 
        // Set application parameters in model
        $input     = $app->getInput();
        $appParams = $app->getParams();
        $articles->setState('params', $appParams);
 
        $articles->setState('filter.published', ContentComponent::CONDITION_PUBLISHED);
 
        // Set the filters based on the module params
        $articles->setState('list.limit', (int) $params->get('count', 3));

        // Access filter
        $access     = !ComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = Access::getAuthorisedViewLevels($app->getIdentity()->get('id'));
        $articles->setState('filter.access', $access);

        $catids = $params->get('catid');
        $articles->setState('filter.category_id.include', (bool) $params->get('category_filtering_type', 1));

        // Category filter
        if ($catids) {
            $articles->setState('filter.category_id', $catids);
        }

        $items = $articles->getItems();

        // Display options
        $show_author      = $params->get('show_author', 0);
        $show_date        = $params->get('show_date', 0);
        $show_date_field  = $params->get('show_date_field', 'publish_up');
        $show_date_format = $params->get('show_date_format', 'Y-m-d H:i:s');
        $show_introtext   = $params->get('show_introtext', 0);
        $introtext_limit  = $params->get('introtext_limit', 100);

        // Find current Article ID if on an article page
        $option = $input->get('option');
        $view   = $input->get('view');

        if ($option === 'com_content' && $view === 'article') {
            $active_article_id = $input->getInt('id');
        } else {
            $active_article_id = 0;
        }

        // Prepare data for display using display options
        foreach ($items as &$item) {
            $item->slug = $item->id . ':' . $item->alias;

            if ($access || \in_array($item->access, $authorised)) {
                // We know that user has the privilege to view the article
                $item->link = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));
            } else {
                $menu      = $app->getMenu();
                $menuitems = $menu->getItems('link', 'index.php?option=com_users&view=login');

                if (isset($menuitems[0])) {
                    $Itemid = $menuitems[0]->id;
                } elseif ($input->getInt('Itemid') > 0) {
                    // Use Itemid from requesting page only if there is no existing menu
                    $Itemid = $input->getInt('Itemid');
                }

                $item->link = Route::_('index.php?option=com_users&view=login&Itemid=' . $Itemid);
            }

            // Used for styling the active article
            $item->active      = $item->id == $active_article_id ? 'active' : '';
            $item->displayDate = '';

            if ($show_date) {
                $item->displayDate = HTMLHelper::_('date', $item->$show_date_field, $show_date_format);
            }

            $item->displayAuthorName = $show_author ? $item->author : '';

            if ($show_introtext) {
                $item->introtext = HTMLHelper::_('content.prepare', $item->introtext, '', 'mod_articles_category.content');
                $item->introtext = self::_cleanIntrotext($item->introtext);
            }

            $item->displayIntrotext = $show_introtext ? self::truncate($item->introtext, $introtext_limit) : '';
            $item->displayReadmore  = $item->alternative_readmore;

        }
        
        return $items;
    }

    /**
     * Get a list of articles from a specific category
     *
     * @param   Registry  &$params  object holding the models parameters
     *
     * @return  array  The array of users
     *
     * @since   1.6
     *
     * @deprecated  __DEPLOY_VERSION__  will be removed in 6.0
     *              Use the non-static method getArticles
     *              Example: Factory::getApplication()->bootModule('mod_articles_category', 'site')
     *                           ->getHelper('ArticlesCategoryHelper')
     *                           ->getArticles($params, Factory::getApplication())
     */
    public static function getList(&$params)
    {
        /* @var SiteApplication $app */
        $app = Factory::getApplication();

        return (new self())->getArticles($params, $app);
    }
} 