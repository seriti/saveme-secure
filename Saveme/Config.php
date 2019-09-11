<?php 
namespace App\Saveme;

use Psr\Container\ContainerInterface;
use Seriti\Tools\BASE_URL;
use Seriti\Tools\SITE_NAME;

class Config
{
    
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

    }

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        
        $module = $this->container->config->get('module','saveme');
        $menu = $this->container->menu;
        
        //define('STORAGE','amazon');

        define('TABLE_PREFIX',$module['table_prefix']);
        
        define('MODULE_ID','SAVEME');
        define('MODULE_LOGO','<img src="'.BASE_URL.'images/saveme32.png"> ');
        define('MODULE_PAGE',URL_CLEAN_LAST);      
        
        //S3 alternative bucket access credentials
        if(defined('AWS_S3_KEY_ALT')) {
            $replace = true;
            $this->container->config->set('s3','region',AWS_S3_REGION,$replace);
            $this->container->config->set('s3','key',AWS_S3_KEY,$replace);
            $this->container->config->set('s3','secret',AWS_S3_SECRET,$replace);
            $this->container->config->set('s3','bucket',AWS_S3_BUCKET,$replace);
        } 

        $submenu_html = $menu->buildNav($module['route_list'],MODULE_PAGE);
        $this->container->view->addAttribute('sub_menu',$submenu_html);

        //quick search form adjacent to title only for active sub_menu items
        if(array_key_exists(MODULE_PAGE,$module['route_list'])) {
            $search = '&nbsp;<form method="get" id="full_search" name="full_search" action="search" style="display:inline">'.
                      '<input type="hidden" name="mode" value="search">'.
                      '<input type="text" name="search_text" placeholder="search..." class="input-group-sm input-small"></form>';
            $this->container->view->addAttribute('title_xtra',$search);
        }
        
        $response = $next($request, $response);
        
        return $response;
    }
}