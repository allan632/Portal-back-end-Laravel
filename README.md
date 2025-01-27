# Documentação da API

## Visão Geral

## Como clonar e rodar o projeto

Siga os passos abaixo para configurar e executar o projeto localmente:

### Pré-requisitos

- **PHP** = 8.1
- **Composer** = 2.7.2
- **Banco de dados** (sqlExpress) 2019
- **Node.js** (para o frontend, se aplicável) 

### Passos

1. **Clonar o repositório**
   ```bash
   git clone <URL_DO_REPOSITORIO>
   cd <NOME_DO_REPOSITORIO>
   ```

2. **Instalar as dependências do PHP**
   ```bash
   composer install
   ```

3. **Copiar o arquivo `.env`**
   ```bash
   cp .env.example .env
   ```

4. **Configurar as variáveis de ambiente**
   - Atualize as informações do banco de dados no arquivo `.env`:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=seu_banco
     DB_USERNAME=seu_usuario
     DB_PASSWORD=sua_senha
     ```

5. **Gerar a chave da aplicação**
   ```bash
   php artisan key:generate
   ```

6. **Executar as migrações do banco de dados**
   ```bash
   php artisan migrate
   ```

7. **Instalar as dependências do frontend (se aplicável)**
   ```bash
   npm install
   npm run dev
   ```

8. **Iniciar o servidor**
   ```bash
   php artisan serve
   ```
   O projeto estará disponível em `http://localhost:8000`.

## Dependências do Projeto

### Dependências principais:
- **php**: ^8.1 - Linguagem de programação usada no projeto.
- **guzzlehttp/guzzle**: ^7.2 - Biblioteca HTTP para requisições client-side.
- **laravel/framework**: ^10.10 - Framework principal usado para desenvolvimento da API.
- **laravel/tinker**: ^2.8 - Ferramenta interativa para interagir com a aplicação.
- **tymon/jwt-auth**: ^2.1 - Biblioteca para autenticação usando JSON Web Tokens (JWT).

### Dependências de desenvolvimento:
- **fakerphp/faker**: ^1.9.1 - Biblioteca para gerar dados fictícios para testes.
- **laravel/pint**: ^1.0 - Ferramenta de formatação de código para padrões do Laravel.
- **laravel/sail**: ^1.18 - Ambiente de desenvolvimento em contêineres Docker.
- **mockery/mockery**: ^1.4.4 - Biblioteca para criação de mocks em testes unitários.
- **nunomaduro/collision**: ^7.0 - Melhor visualização de erros no terminal.
- **phpunit/phpunit**: ^10.1 - Framework para testes automatizados.
- **spatie/laravel-ignition**: ^2.0 - Ferramenta de depuração para aplicações Laravel.

## Rotas Principais

### Registro de Usuário
- **POST /register**: Registra um novo usuário.

### Login de Usuário
- **POST /login**: Realiza o login do usuário e retorna um JWT para autenticação.


## Autenticação

A autenticação na API é realizada utilizando JWT (JSON Web Token). Após o login, um token JWT é retornado e deve ser incluído no cabeçalho das requisições subsequentes para acesso às rotas protegidas.

- Exemplo de uso do token no cabeçalho:
  ```
  Authorization: Bearer JWT_TOKEN
  ```

## Conclusão

Essa API oferece um fluxo básico de autenticação usando JWT, permitindo registro e login de usuários. Para acessar as rotas protegidas, o token de autenticação deve ser incluído no cabeçalho da requisição.