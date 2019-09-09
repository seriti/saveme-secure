<?php  
/*
NB: This is not stand alone code and is intended to be used within "seriti/slim3-skeleton" framework
The code snippet below is for use within an existing src/routes.php file within this framework
copy the "/client" group into the existing "/admin" group within existing "src/routes.php" file 
*/

$app->group('/admin', function () {

    $this->group('/saveme', function () {
        $this->any('/dashboard', \App\Saveme\DashboardController::class);
        $this->any('/login', \App\Saveme\LoginController::class);
        $this->any('/import', \App\Saveme\ImportWizardController::class);
        $this->any('/text', \App\Saveme\TextController::class);
        $this->any('/file', \App\Saveme\FileController::class);
        $this->any('/search', \App\Saveme\SearchController::class);
        $this->get('/setup_data', \App\Saveme\SetupDataController::class);
        $this->any('/jscrypt', \App\Saveme\JscryptController::class);
    })->add(\App\Saveme\Config::class);

})->add(\App\ConfigAdmin::class);



