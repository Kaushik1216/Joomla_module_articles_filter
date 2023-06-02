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

namespace KaushikVishwakarma\Module\ArticlesListByCategory\Site\Dispatcher;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Helper\ModuleHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('JPATH_PLATFORM') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Dispatcher class for mod_articles_list_by_category
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

        $idBase = $params->get('catid');

        $cacheParams               = new \stdClass();
        $cacheParams->cachemode    = 'id';
        $cacheParams->class        = $this->getHelperFactory()->getHelper('ArticlesListByCategoryHelper');
        $cacheParams->method       = 'getArticles';
        $cacheParams->methodparams = [$params, $data['app']];
        $cacheParams->modeparams   = md5(serialize([$idBase, $this->module->module, $this->module->id]));

        $data['list'] = ModuleHelper::moduleCache($this->module, $params, $cacheParams);

        return $data;
    }
}