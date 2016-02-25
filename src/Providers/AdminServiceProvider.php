<?php

namespace SleepingOwl\Admin\Providers;

use SleepingOwl\Admin\Form;
use SleepingOwl\Admin\Admin;
use SleepingOwl\Admin\Display;
use SleepingOwl\Admin\TableColumn;
use KodiCMS\Navigation\Navigation;
use SleepingOwl\Admin\FormElement;
use SleepingOwl\Admin\DisplayFilter;
use Symfony\Component\Finder\Finder;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use SleepingOwl\Admin\TableColumnFilter;
use SleepingOwl\Admin\Facades\AdminSection;
use SleepingOwl\Admin\Facades\AdminTemplate;
use SleepingOwl\Admin\Facades\AdminNavigation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminServiceProvider extends ServiceProvider
{
    protected $directory;

    public function register()
    {
        $this->app->singleton('sleeping_owl', function () {
            return new Admin();
        });

        $this->app->singleton('sleeping_owl.navigation', function () {
            $items = [];
            if (file_exists($navigation = $this->getBootstrapPath('navigation.php'))) {
                $items = include $navigation;
            }

            return new Navigation($items);
        });

        $this->registerAliases();
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected function getConfig($key)
    {
        return $this->app['config']->get('sleeping_owl.'.$key);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getBootstrapPath($path = null)
    {
        if (! is_null($path)) {
            $path = DIRECTORY_SEPARATOR.$path;
        }

        return $this->getConfig('bootstrapDirectory').$path;
    }

    public function boot()
    {
        $this->app->singleton('sleeping_owl.template', function () {
            return $this->app['sleeping_owl']->template();
        });

        $this->registerCustomRoutes();
        $this->registerBootstrap();
        $this->registerDefaultRoutes();
    }

    /**
     * @return array
     */
    protected function registerBootstrap()
    {
        $directory = $this->getBootstrapPath();

        if (! is_dir($directory)) {
            return;
        }

        $files = $files = Finder::create()
            ->files()
            ->name('/^.+\.php$/')
            ->notName('routes.php')
            ->notName('navigation.php')
            ->in($directory)->sort(function ($a) {
                return $a->getFilename() != 'bootstrap.php';
            });


        foreach ($files as $file) {
            require $file;
        }
    }

    protected function registerAliases()
    {
        AliasLoader::getInstance([
            'AdminSection'      => AdminSection::class,
            'AdminTemplate'     => AdminTemplate::class,
            'AdminNavigation'   => AdminNavigation::class,
            'AdminColumn'       => TableColumn::class,
            'AdminColumnFilter' => TableColumnFilter::class,
            'AdminFilter'       => DisplayFilter::class,
            'AdminForm'         => Form::class,
            'AdminFormElement'  => FormElement::class,
            'AdminDisplay'      => Display::class
        ]);
    }

    protected function registerCustomRoutes()
    {
        if (file_exists($file = $this->getBootstrapPath('routes.php'))) {
            $this->registerRoutes(function() use($file) {
                require $file;
            });
        }
    }

    protected function registerDefaultRoutes()
    {
        $this->registerRoutes(function() {
            $this->app['router']->pattern('adminModelId', '[0-9]+');

            $aliases = $this->app['sleeping_owl']->modelAliases();

            if (count($aliases) > 0) {
                $this->app['router']->pattern('adminModel', implode('|', $aliases));

                $this->app['router']->bind('adminModel', function ($model) use ($aliases) {
                    $class = array_search($model, $aliases);

                    if ($class === false) {
                        throw new ModelNotFoundException;
                    }

                    return $this->app['sleeping_owl']->getModel($class);
                });
            }

            if (file_exists($routesFile = __DIR__.'/../Http/routes.php')) {
                require $routesFile;
            }
        });
    }

    /**
     * @param \Closure $callback
     */
    protected function registerRoutes(\Closure $callback)
    {
        $this->app['router']->group(['prefix' => $this->getConfig('prefix'), 'namespace' => 'SleepingOwl\Admin\Http\Controllers'], function () use($callback) {
            $this->app['router']->group(['middleware' => $this->getConfig('middleware')], function () use($callback) {
                $callback();
            });
        });
    }
}