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

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The articles list by category module service provider.
 *
 * @since  __DEPLOY_VERSION__
 */
return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function register(Container $container)
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\Joomla\\Module\\ArticlesListByCategory'));
        $container->registerServiceProvider(new HelperFactory('\\Joomla\\Module\\Articleslistbycategory\\Site\\Helper'));

        $container->registerServiceProvider(new Module());
    }
};
