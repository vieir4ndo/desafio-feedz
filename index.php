<?php 

require '\src\vendor\autoload.php';

$app = new \Slim\App([
    'settings'=> [
        'displayErrorDetails' => true
    ]
]);

$container = $app->getContainer();

$container['view']= function ($container){
    $folder = __DIR__;
    $view = new \Slim\Views\Twig($folder.'/src/app/public/views', [
        'cache' => false
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};

$container['db'] = function($container){
    return new PDO('mysql:host=localhost;dbname=desafiofeedz', 'root', '');

};

$container['usuarioController']= function($container) use ($app){
    return new \modulosecontrollers\controllers\usuarioController($container);
};

$app->get('/', 'usuarioController:redirecionaLogUsuario')->setname('login');

$app->post('/', 'usuarioController:loginUsuario');

$app->get('/sair', 'usuarioController:sairUsuario')->setname('sair');

$app->get('/cadastro', 'usuarioController:redirecionaCadUsuario')->setname('cadastro');

$app->post('/cadastro', 'usuarioController:cadastroUsuario');

$app->get('/home', 'usuarioController:redirecionaHome')->setname('home');

$app->get('/alterarSenha', 'usuarioController:redirecionaAlterarSenha')->setname('alterarSenha');

$app->post('/alterarSenha', 'usuarioController:alterarSenhaUsuario');

$app->get('/deletarConta', 'usuarioController:redirecionaDeletarConta')->setname('deletarConta');

$app->post('/deletarConta', 'usuarioController:deletarUsuario');

$app->get('/perfil', 'usuarioController:redirecionaPerfilUsuario')->setname('perfil');

$app->post('/perfil', 'usuarioController:editaUsuario');

$app->get('/consulta', 'usuarioController:consultaUsuario')->setname('consulta');

$app->run();

?>