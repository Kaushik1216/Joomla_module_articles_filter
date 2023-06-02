<?php

/**
 * @package     kaushik.Site
 * @subpackage  mod_articles_list_category
 *
 * @copyright   [copyright]
 * @license     [license]
 */

namespace Kaushik\Module\ArticlesListCategory\Site\Helper;

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
 * Helper for mod_articles_list_category
 *
 * @since  1.6
 */
class ArticlesListCategoryHelper implements DatabaseAwareInterface
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

        $articles->setState('list.start', 0);
        $articles->setState('filter.published', ContentComponent::CONDITION_PUBLISHED);

        // Set the filters based on the module params
        $articles->setState('list.limit', (int) $params->get('count', 0));

        // Access filter
        $access     = !ComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = Access::getAuthorisedViewLevels($app->getIdentity()->get('id'));
        $articles->setState('filter.access', $access);

        $articles->setState('filter.category_id.include', (bool) $params->get('category_filtering_type', 1));

        $items = $articles->getItems();

        // Display options
        $show_date        = $params->get('show_date', 0);
        $show_date_field  = $params->get('show_date_field', 'created');
        $show_date_format = $params->get('show_date_format', 'Y-m-d H:i:s');
        $show_author      = $params->get('show_author', 0);
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

            $item->displayIntrotext = $show_introtext ? self::truncate($item->introtext, $introtext_limit) : '';
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
     *              Example: Factory::getApplication()->bootModule('mod_articles_list_category', 'site')
     *                           ->getHelper('ArticlesListCategoryHelper')
     *                           ->getArticles($params, Factory::getApplication())
     */
    public static function getList(&$params)
    {
        /* @var SiteApplication $app */
        $app = Factory::getApplication();

        return (new self())->getArticles($params, $app);
    }

    /**
     * Method to truncate introtext
     *
     * The goal is to get the proper length plain text string with as much of
     * the html intact as possible with all tags properly closed.
     *
     * @param   string  $html       The content of the introtext to be truncated
     * @param   int     $maxLength  The maximum number of characters to render
     *
     * @return  string  The truncated string
     *
     * @since   1.6
     */
    public static function truncate($html, $maxLength = 0)
    {
        $baseLength = \strlen($html);

        // First get the plain text string. This is the rendered text we want to end up with.
        $ptString = HTMLHelper::_('string.truncate', $html, $maxLength, true, false);

        for ($maxLength; $maxLength < $baseLength;) {
            // Now get the string if we allow html.
            $htmlString = HTMLHelper::_('string.truncate', $html, $maxLength, true, true);

            // Now get the plain text from the html string.
            $htmlStringToPtString = HTMLHelper::_('string.truncate', $htmlString, $maxLength, true, false);

            // If the new plain text string matches the original plain text string we are done.
            if ($ptString === $htmlStringToPtString) {
                return $htmlString;
            }

            // Get the number of html tag characters in the first $maxlength characters
            $diffLength = \strlen($ptString) - \strlen($htmlStringToPtString);

            // Set new $maxlength that adjusts for the html tags
            $maxLength += $diffLength;

            if ($baseLength <= $maxLength || $diffLength <= 0) {
                return $htmlString;
            }
        }

        return $ptString;
    }

}
