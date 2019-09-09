<?php
namespace App\Saveme;

use Psr\Container\ContainerInterface;
use App\Saveme\Login;

class LoginController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'logins'; 
        $table = new Login($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'Saveme Logins';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}