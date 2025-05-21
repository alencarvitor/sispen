<?php
// Controlador de autenticação
// controllers/AuthController.php

class AuthController extends Controller {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    // Método para exibir a página de login
    public function login() {
        // Se já estiver logado, redireciona para a página inicial
        if ($this->isLoggedIn()) {
            $this->redirect('/');
            return;
        }
        
        // Se o formulário foi enviado
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Validação básica
            if (empty($username) || empty($password)) {
                $this->view('auth/login', [
                    'title' => 'Login - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'login',
                    'error' => 'Por favor, preencha todos os campos.'
                ]);
                return;
            }
            
            // Tenta autenticar o usuário
            $user = $this->usuarioModel->authenticate($username, $password);
            
            if ($user) {
                // Cria a sessão do usuário
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user['username'];
                $_SESSION['user_type'] = $user['tipo'] ?? 'comprador';
                
                // Gera um token de sessão único
                $session_token = bin2hex(random_bytes(16));
                $_SESSION['session_token'] = $session_token;
                
                // Atualiza o token de sessão no banco
                $this->usuarioModel->updateSessionToken($user['id'], $session_token);
                
                // Redireciona com base no tipo de usuário
                switch ($_SESSION['user_type']) {
                    case 'admin':
                        $this->redirect('/admin');
                        break;
                    case 'leiloeiro':
                        $this->redirect('/leilao');
                        break;
                    case 'comprador':
                    default:
                        $this->redirect('/lance');
                        break;
                }
            } else {
                // Falha na autenticação
                $this->view('auth/login', [
                    'title' => 'Login - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'login',
                    'error' => 'Usuário ou senha incorretos.'
                ]);
            }
        } else {
            // Exibe o formulário de login
            $this->view('auth/login', [
                'title' => 'Login - Sistema de Leilão Beneficente São João Batista',
                'active_page' => 'login'
            ]);
        }
    }
    
    // Método para exibir a página de registro
    public function register() {
        // Se já estiver logado, redireciona para a página inicial
        if ($this->isLoggedIn()) {
            $this->redirect('/');
            return;
        }
        
        // Verifica se o usuário atual é admin (para cadastro de outros tipos)
        $is_admin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
        
        // Se o formulário foi enviado
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'comprador';
            
            // Se não for admin, só pode cadastrar comprador
            if (!$is_admin) {
                $tipo = 'comprador';
            }
            
            // Validação básica
            if (empty($username) || empty($password) || empty($confirm_password)) {
                $this->view('auth/register', [
                    'title' => 'Cadastro - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'register',
                    'error' => 'Por favor, preencha todos os campos.',
                    'is_admin' => $is_admin
                ]);
                return;
            }
            
            // Verifica se as senhas coincidem
            if ($password !== $confirm_password) {
                $this->view('auth/register', [
                    'title' => 'Cadastro - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'register',
                    'error' => 'As senhas não coincidem.',
                    'is_admin' => $is_admin
                ]);
                return;
            }
            
            // Verifica se o usuário já existe
            if ($this->usuarioModel->usernameExists($username)) {
                $this->view('auth/register', [
                    'title' => 'Cadastro - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'register',
                    'error' => 'Este nome de usuário já está em uso.',
                    'is_admin' => $is_admin
                ]);
                return;
            }
            
            // Cria o usuário
            $userData = [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'tipo' => $tipo
            ];
            
            $userId = $this->usuarioModel->create($userData);
            
            if ($userId) {
                $this->view('auth/register', [
                    'title' => 'Cadastro - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'register',
                    'success' => 'Usuário cadastrado com sucesso! Agora você pode fazer login.',
                    'is_admin' => $is_admin
                ]);
            } else {
                $this->view('auth/register', [
                    'title' => 'Cadastro - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'register',
                    'error' => 'Erro ao cadastrar usuário. Por favor, tente novamente.',
                    'is_admin' => $is_admin
                ]);
            }
        } else {
            // Exibe o formulário de registro
            $this->view('auth/register', [
                'title' => 'Cadastro - Sistema de Leilão Beneficente São João Batista',
                'active_page' => 'register',
                'is_admin' => $is_admin
            ]);
        }
    }
    
    // Método para fazer logout
    public function logout() {
        // Destrói a sessão
        session_destroy();
        
        // Redireciona para a página de login
        $this->redirect('/login');
    }
}
