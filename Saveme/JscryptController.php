<?php
namespace App\Saveme;

use Psr\Container\ContainerInterface;

class JscryptController
{
    protected $container;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __invoke($request, $response, $args)
    {
        $template = [];
        //completely standalone html and js code
        return $this->container->view->render($response,'saveme/jscrypt.php',$template);
    }
}