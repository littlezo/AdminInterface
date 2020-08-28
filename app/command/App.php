<?php

/**
 * +----------------------------------------------------------------------
 * | SolCo
 * +----------------------------------------------------------------------
 * |Copyright (c) 2017-{%year%} http://esoin.cc All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Author: @小小只 <soinco@qq.com>
 * +----------------------------------------------------------------------
 */

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class App extends Command
{
    /**
    * 应用基础目录
    * @author  @小小只 <soinco@qq.com>
    * @version 1.0.0
    * @date    2020-06-28 19:00:00
    * @desc    description
    * @var string
    */
    protected $basePath;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('maker:app')
            ->addArgument('app', Argument::OPTIONAL, 'app name .')
            ->setDescription('Builder App Dirs');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->basePath = $this->app->getBasePath();
        $this->rootPath = $this->app->getRootPath();
        $app            = $input->getArgument('app') ?: '';

        if (is_file($this->basePath . 'build.php')) {
            $list = include $this->basePath . 'build.php';
        } else {
            $list = [
                '__dir__' => ['controller', 'model', 'service'],
            ];
        }

        $this->buildApp($app, $list);
        $output->writeln("<info>Successed</info>");
    }

    /**
     * 创建应用
     * @access protected
     * @param  string $app  应用名
     * @param  array  $list 目录结构
     * @return void
     */
    protected function buildApp(string $app, array $list = []): void
    {
        if (!is_dir($this->basePath . $app)) {
            // 创建应用目录
            mkdir($this->basePath . $app);
        }

        $appPath   = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '');
        $namespace = 'app' . ($app ? '\\' . $app : '');
        // $basePath =$this->basePath;
        $rootPath =$this->rootPath;
        // root_path()
        $date =  date("Y/m/d  H:i:s");
        $year =  date("Y");
        // dump($year);
        // exit;

        // 创建模块的路由
        if ($app) {
            $this->buildRoute($app, $list);
        }
        // 创建配置文件和公共文件
        $this->buildCommon($app,$date,$year);

        foreach ($list as $path => $file) {
            if ('__dir__' == $path) {
                // 生成子目录
                foreach ($file as $dir) {
                    $this->checkDirBuild($appPath . $dir);
                }
            } elseif ('__file__' == $path) {
                // 生成（空白）文件
                foreach ($file as $name) {
                    if (!is_file($appPath . $name)) {
                        file_put_contents($appPath . $name, 'php' == pathinfo($name, PATHINFO_EXTENSION) ? '<?php' . PHP_EOL : '');
                    }
                }
            } else {
                // 生成相关MVC文件
                foreach ($file as $val) {
                    $val      = trim($val);
                    $filename = $appPath . $path . DIRECTORY_SEPARATOR . $val . '.php';
                    $space    = $namespace . '\\' . $path;
                    $spacePath    = (explode('/', $val));
                    $class   = array_pop($spacePath);
                    $layer    = (implode("\\", $spacePath));
                    // $class    = array_shift($spacePath);
                    $modulePath   = (explode('/', $val));
                    array_shift($modulePath);
                    $module   = (implode("/", $modulePath));
                    $module  = strtolower($module);
                    $method   = (implode(".", $modulePath));
                    $classPath     = $appPath . $path . DIRECTORY_SEPARATOR . (implode("/", (explode('/', $val, -1))));
                    // dump($app);
                    // exit;
                    if ($app == "") {
                        $routeFile = $rootPath . 'route/app.php';
                    } else {
                        $routeFile = $appPath . 'route/route.php';
                    }
                    if (!is_dir($classPath)) {
                        // 创建路由目录
                        mkdir($classPath, 0755, true);
                    }
                    switch ($path) {
                        case 'controller': // 控制器
                            // dump($space);
                            // exit;
                            if ($this->app->config->get('route.controller_suffix')) {
                                $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'controller.api.stub');
                                $content = str_replace(['{%name%}', '{%app%}', '{%layer%}', '{%suffix%}','{%date%}','{%year%}'], [$app, $space, $layer, $class, $date, $year. 'Controller'], $content);
                            }
                            $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'controller.api.stub');
                            $content = str_replace(['{%name%}', '{%app%}', '{%layer%}', '{%suffix%}','{%date%}','{%year%}'], [$app, $space, $layer, $class, $date, $year], $content);
                            // dump($routeFile);
                            // dump($module);
                            // exit;
                            // 定义路由规则
                            $route = PHP_EOL .
                                "// $module" .
                                PHP_EOL .
                                "Route::group('/<v>/$module', function () {
                                    Route::rule('', 'index', 'GET');
                                    Route::rule('', 'save', 'POST');
                                    Route::rule(':id', 'read', 'GET');
                                    Route::rule(':id', 'update', 'POST');
                                    Route::rule(':id', 'delete', 'DELETE');
                                })->completeMatch()->prefix('/:v.$method/')->pattern(['id' => '\d+']);" . PHP_EOL;
                            $temp = strpos(file_get_contents($routeFile), "// $module");
                            // dump($route);
                            // exit;
                            if (
                                is_file($routeFile)
                                && !strpos(file_get_contents($routeFile), "// $module")
                            ) {
                                file_put_contents($routeFile, $route, FILE_APPEND);
                            }
                            break;
                        case 'model': // 模型
                            $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'model.stub');
                            $content = str_replace(['{%app%}', '{%layer%}', '{%suffix%}','{%date%}','{%year%}'], [$space, $layer, $class, $date, $year], $content);
                            break;
                        case 'listener': // 监听
                            $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'listener.stub');
                            $content = str_replace(['{%app%}', '{%layer%}', '{%suffix%}','{%date%}','{%year%}'], [$space, $layer, $class, $date, $year], $content);
                            break;
                        case 'middleware': // 中间价
                            $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'middleware.stub');
                            $content = str_replace(['{%app%}', '{%layer%}', '{%suffix%}','{%date%}','{%year%}'], [$space, $layer, $class, $date, $year], $content);
                            break;
                        case 'service': // 服务

                            $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'service.stub');
                            $content = str_replace(['{%app%}', '{%layer%}', '{%suffix%}','{%date%}','{%year%}'], [$space, $layer, $class, $date, $year], $content);
                            break;
                        case 'validate': // 验证器
                            $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'validate.stub');
                            $content = str_replace(['{%app%}', '{%layer%}', '{%suffix%}','{%date%}','{%year%}'], [$space, $layer, $class, $date, $year], $content);
                            break;
                        default:
                            // 其他文件
                            $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'default.stub');
                            $content = str_replace(['{%app%}', '{%layer%}', '{%suffix%}','{%date%}','{%year%}'], [$space, $layer, $class, $date, $year], $content);
                    }
                    if (!is_file($filename)) {
                        file_put_contents($filename, $content);
                    }
                }
            }
        }
    }
    /**
     * 创建应用的路由文件
     * @access protected
     * @param  string $app 目录
     * @return void
     */
    public function buildRoute(string $app): void
    {
        $appPath = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR .  'route/' : '');

        // var_dump($appPath);
        // exit();
        if (!is_dir($appPath)) {
            // 创建路由目录
            mkdir($appPath);
        }
        $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'route.stub');
        if (!is_file($appPath . 'route.php')) {
            file_put_contents($appPath . 'route.php', $content);
        }
    }
    /**
     * 创建应用的公共文件
     * @access protected
     * @param  string $app 目录
     * @return void
     */
    protected function buildCommon(string $app,$date,$year): void
    {
        $appPath = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '');
        $namespace = 'app' . ($app ? '\\' . $app : '');
        $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'common.stub');
        $content = str_replace(['{%namespace%}', '{%class%}','{%date%}','{%year%}'], [$namespace,$date,$year, 'Base'], $content);
        if (!is_file($appPath . 'Base.php')) {
            file_put_contents($appPath . 'Base.php', $content);
        }

        foreach (['event', 'middleware', 'provider'] as $name) {
            if (!is_file($appPath . $name . '.php')) {
                file_put_contents($appPath . $name . '.php', "<?php" . PHP_EOL . "// 这是系统自动生成的{$name}定义文件" . PHP_EOL . "return [" . PHP_EOL . PHP_EOL . "];" . PHP_EOL);
            }
        }
    }

    /**
     * 创建目录
     * @access protected
     * @param  string $dirname 目录名称
     * @return void
     */
    protected function checkDirBuild(string $dirname): void
    {
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
    }
}
