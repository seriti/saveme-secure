<?php
namespace App\Saveme;

use Psr\Container\ContainerInterface;

use App\Saveme\Search;

class SearchController
{
    protected $container;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __invoke($request, $response, $args)
    {
        $search = new Search($this->container->mysql,$this->container);
        
        $html = $search->process();

        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'Saveme Search results ';
        //$template['javascript'] = $dashboard->getJavascript();

        return $this->container->view->render($response,'admin.php',$template);
    }
}