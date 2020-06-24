<?php
    namespace modulosecontrollers\controllers;
    use modulosecontrollers\controllers\Controller;

    class usuarioController extends Controller{
    
        public function redirecionaCadUsuario($request, $response, $args){
  
            return $this->view->render($response, 'cadastro.twig', [

            ]);
        }

        public function redirecionaLogUsuario($request, $response, $args){
  
            return $this->view->render($response, 'login.twig', [

            ]);
        }

        public function redirecionaPerfilUsuario($request, $response, $args){
            session_start();
            return $this->view->render($response, 'perfil.twig', [
                'usuarios' => $_SESSION['user']
            ]);
        }

        public function redirecionaHome($request, $response, $args){
            session_start();
            return $this->view->render($response, 'home.twig', [
                'usuarios' => $_SESSION['user']
            ]);
        }

        public function redirecionaAlterarSenha($request, $response, $args){
            session_start();
            return $this->view->render($response, 'alterarSenha.twig', [
                'usuarios' => $_SESSION['user']
            ]);
        }

        public function redirecionaDeletarConta($request, $response, $args){
            session_start();
            return $this->view->render($response, 'deletarConta.twig', [
                'usuarios' => $_SESSION['user']
            ]);
        }

        public function sairUsuario($request, $response, $args){   
            session_start();
            session_destroy();
            return $this->view->render($response,'login.twig', [

                ]);
        }
        
        public function cadastroUsuario($request, $response, $args){
            $posts = $request->getParsedBody();
                if (isset($posts['nome']) && isset($posts['email']) && isset($posts['senha']) && isset($posts['confirmaSenha'])){
                    if ($posts['senha']==$posts['confirmaSenha']){
                    $posts['senha'] = md5($posts['senha']); 
                    unset($posts['confirmaSenha']);
                    $inserir = "INSERT INTO usuarios SET nome = '".$posts['nome']."' , email = '".$posts['email']."' , senha = '".$posts['senha']."'";
                    $insert = $this->db->prepare($inserir);
                    
                    if($insert->execute()){
                        return $this->view->render($response, 'login.twig', [
                            'emailError' => false,
                            'passwordError' => false
                            ]);

                    }else{
                        return $this->view->render($response, 'cadastro.twig', [
                            'emailError' => true,
                            'passwordError' => false
                            ]);
                    }
                    } else {
                        return $this->view->render($response, 'cadastro.twig', [
                            'emailError' => false,
                            'passwordError' => true
                            ]);
                    }
                }         
        }
        
        public function loginUsuario($request, $response, $args){
            $posts = $request->getParsedBody();
            $posts['senha'] = md5($posts['senha']);
            $inserir = "SELECT id, nome, email, senha FROM usuarios where email='".$posts['email']."' and senha = '".$posts['senha']."' limit 1";
            $insert = $this->db->prepare($inserir);  
            $insert->execute();     
            $insert = $insert->fetchAll();     
            if (empty($insert)) {
                return $this->view->render($response,'login.twig', [
                    'emailErrorLogin' => true, 
                    'delete' =>false
                ]);
            } else {
                session_start();
                $_SESSION['user'] = $insert;
                $responde = $response->withStatus(200);
                return $this->view->render($response,'home.twig', [
                    'usuarios' => $_SESSION['user']
                ]);
            }
    
        }

        public function consultaUsuario($request, $response, $args){
            $usuarios = $this->db->prepare("SELECT id, nome, email FROM usuarios");
            $usuarios->execute();
            $usuarios= $usuarios->fetchAll();

            $responde = $response->withStatus(200);
            return $this->view->render($response,'consulta.twig', [
                'usuarios' => $usuarios
            ]);
        }

        public function editaUsuario($request, $response, $args){
            session_start();
            $id = array_values($_SESSION['user']);  
            //print_r($_SESSION['user']);
            $posts = $request->getParsedBody();
            $inserir = "UPDATE usuarios SET nome ='".$posts['nome']."', email = '".$posts['email']."' where id = '".$id[0][0]."'";
            $insert = $this->db->prepare($inserir);
            if ($insert->execute()){
                $inserir = "SELECT id, nome, email, senha FROM usuarios where id = '".$id[0][0]."' limit 1";
                $responde = $response->withStatus(200);
                $insert = $this->db->prepare($inserir);
                $insert->execute();
                $_SESSION['user']= $insert->fetchAll();
                return $this->view->render($response,'perfil.twig', [
                    'error' => false,
                    'success' => true,
                    'usuarios' => $_SESSION['user']
            ]);
            }else {
                return $this->view->render($response,'perfil.twig', [
                    'error' => true,
                    'success' => false,
                    'usuarios' => $_SESSION['user']
            ]);
            }
        }
        
        public function alterarSenhaUsuario($request, $response, $args){
            session_start();
            $id = array_values($_SESSION['user']);  
            //print_r($_SESSION['user']);
            $posts = $request->getParsedBody();
            //print_r($id[0]['senha']);
            $posts['senhaAtual'] = md5($posts['senhaAtual']);
            if ($posts['senhaAtual']==$id[0]['senha']){
            if ($posts['novaSenha'] == $posts['confirmaNovaSenha']){
            $posts['novaSenha']= md5($posts['novaSenha']);
            $inserir = "UPDATE usuarios SET senha = '".$posts['novaSenha']."' where id = '".$id[0][0]."'";
            $insert = $this->db->prepare($inserir);
            if ($insert->execute()){
                $inserir = "SELECT id, nome, email, senha FROM usuarios where id = '".$id[0][0]."' limit 1";
                $responde = $response->withStatus(200);
                $insert = $this->db->prepare($inserir);
                $insert->execute();
                $_SESSION['user']= $insert->fetchAll();
                return $this->view->render($response,'alterarSenha.twig', [
                    'error' => false,
                    'success' => true,
                    'errorSenhaAtual' => false,
                    'passwordError' => false,
                    'usuarios' => $_SESSION['user']
            ]);
            }else {
                return $this->view->render($response,'alterarSenha.twig', [
                    'error' => true,
                    'success' => false,
                    'errorSenhaAtual' => false,
                    'passwordError' => false,
                    'usuarios' => $_SESSION['user']
            ]);
            }
            } else {
                return $this->view->render($response,'alterarSenha.twig', [
                    'error' => false,
                    'success' => false,
                    'errorSenhaAtual' => false,
                    'passwordError' => true,
                    'usuarios' => $_SESSION['user']
            ]);
            }
            } else{
            
                return $this->view->render($response,'alterarSenha.twig', [
                    'error' => false,
                    'success' => false,
                    'errorSenhaAtual' => true,
                    'passwordError' => false,
                    'usuarios' => $_SESSION['user']
                ]);
            }
        }

        public function deletarUsuario($request, $response, $args){
            session_start();
            $id = array_values($_SESSION['user']);  
            $posts = $request->getParsedBody();
            $posts['senha'] = md5($posts['senha']);
            $posts['confirmaSenha'] = md5($posts['confirmaSenha']);
            if (($posts['confirmaSenha']==$posts['senha']) && $posts['confirmaSenha']== $id[0]['senha']){

            $inserir = "DELETE FROM usuarios where id = '".$id[0][0]."'";
            $insert = $this->db->prepare($inserir);
            if ($insert->execute()){
                session_destroy();
                return $this->view->render($response,'login.twig', [
                    'delete' => true
            ]);
                } else {
                    echo 'não deu enviar banco';
                }
            } else {
                echo 'senhas não batem';
            }
        }

        
    }


?>