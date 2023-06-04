<?php

/**
 * @package     kaushik.Site
 * @subpackage  mod_articles_list_category
 *
 * @copyright   [copyright]
 * @license     [license]
 */

namespace Kaushik\Module\ArticlesListCategory\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

\defined('JPATH_PLATFORM') or die;

/**
 * Dispatcher class for mod_articles_list_category
 *
 * @since  __DEPLOY_VERSION__
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   __DEPLOY_VERSION__
     */
    protected function getLayoutData(): array
    {
        $data   = parent::getLayoutData();
        $params = $data['params'];

        $data['list'] = $this->getHelperFactory()->getHelper('ArticlesListCategoryHelper')->getArticlesList($params, $data['app']);

        return $data;
    }
}
