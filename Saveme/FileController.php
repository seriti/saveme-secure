<?php
namespace App\Saveme;

use Psr\Container\ContainerInterface;
use App\Saveme\File;

class FileController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'files'; 
        $upload = new File($this->container->mysql,$this->container,$table_name);

        $upload->setup();
        $html = $upload->processUpload();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'Saveme Files';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}