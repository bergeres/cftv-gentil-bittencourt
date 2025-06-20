# Sistema CFTV Gentil Bittencourt

O **Sistema CFTV ** é uma aplicação web desenvolvida para gerenciar solicitações de visualização de imagens de câmeras de segurança (CFTV) no Colégio Gentil Bittencourt. O sistema permite que usuários com diferentes níveis de acesso (Abertura, Autorização, Monitor e Admin) realizem tarefas específicas, como abrir solicitações, aprovar solicitações, marcar atendimentos e gerenciar usuários, com uma interface responsiva e amigável.

## Funcionalidades

- **Níveis de Acesso**:
  - **Abertura**: Usuários podem abrir solicitações de visualização de CFTV e acompanhar o status de suas solicitações no histórico.
  - **Autorização**: Usuários podem aprovar ou rejeitar solicitações.
  - **Monitor**: Usuários podem marcar solicitações aprovadas como atendidas.
  - **Admin**: Acesso completo, incluindo gerenciamento de usuários, aprovação/atendimento de solicitações e visualização de análises no dashboard.

- **Características**:
  - Interface responsiva, compatível com desktops e dispositivos móveis (usando Tailwind CSS).
  - Formulário de solicitação com preenchimento automático do nome do usuário para nível "Abertura".
  - Histórico de solicitações exclusivo para usuários "Abertura".
  - Dashboard com gráficos analíticos (Chart.js) para administradores.
  - Suporte a Progressive Web App (PWA) para uso offline (parcialmente implementado).
  - Alteração de senha disponível para todos os usuários.

## Estrutura do Projeto

O projeto é composto pelos seguintes arquivos principais:

- **PHP**:
  - `index.php`: Página inicial para usuários "Abertura", com opções para abrir solicitações ou visualizar o histórico.
  - `nova_solicitacao.php`: Formulário para criar novas solicitações de visualização de CFTV (nível "Abertura").
  - `historico.php`: Exibe o histórico de solicitações do usuário logado (nível "Abertura").
  - `solicitacoes.php`: Lista todas as solicitações com opções de aprovação/atendimento (níveis "Autorização", "Monitor", "Admin").
  - `painel.php`: Painel de administração para gerenciar usuários (nível "Admin").
  - `dashboard.php`: Dashboard com análises gráficas das solicitações (nível "Admin").
  - `change_password.php`: Página para alteração de senha (todos os usuários).
  - `auth.php`: Lógica de autenticação de usuários (login).
  - `submit.php`: Processa o envio de novas solicitações.
  - `update_status.php`: Atualiza o status das solicitações (Aprovado, Atendido, Não Atendido).
  - `edit_user.php`: Edita informações de usuários (nível "Admin").
  - `delete_user.php`: Exclui usuários (nível "Admin").
  - `logout.php`: Encerra a sessão do usuário.
  - `sw.js`: Service Worker para suporte a PWA (usado em `painel.php` e `dashboard.php`).

- **Outros**:
  - `manifest.json`: Configuração para Progressive Web App (PWA).
  - Banco de dados MySQL: Tabelas `usuarios` e `solicitacoes`.

## Pré-requisitos

- **Servidor Web**: Apache ou Nginx.
- **PHP**: Versão 7.4 ou superior.
- **MySQL**: Versão 5.7 ou superior.
- **Navegador**: Versão recente de Chrome, Firefox, Safari ou Edge.
- **Dependências Externas**:
  - Tailwind CSS (via CDN: `https://cdn.tailwindcss.com`).
  - Chart.js (via CDN: `https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js`, usado em `dashboard.php`).

## Instalação

Siga os passos abaixo para configurar o sistema localmente:

1. **Clone o Repositório**:
   ```bash
   git clone https://github.com/seu-usuario/cftv_banco.git
   cd cftv banco
   ```

2. **Configure o Servidor Web**:
   - Copie os arquivos do projeto para o diretório raiz do seu servidor web (ex.: `/var/www/html` no Apache).
   - Certifique-se de que o servidor web tem permissões de leitura/escrita nos arquivos.

3. **Configure o Banco de Dados**:
   - Crie um banco de dados MySQL chamado `cftv_banco`:
     ```sql
     CREATE DATABASE cftv_banco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
   - Crie as tabelas `usuarios` e `solicitacoes`:
     ```sql
     USE cftv_banco;

     CREATE TABLE usuarios (
         id INT AUTO_INCREMENT PRIMARY KEY,
         nome VARCHAR(255) NOT NULL,
         username VARCHAR(50) NOT NULL UNIQUE,
         password VARCHAR(255) NOT NULL,
         nivel_acesso ENUM('Abertura', 'Autorização', 'Monitor', 'Admin') NOT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );

     CREATE TABLE solicitacoes (
         id INT AUTO_INCREMENT PRIMARY KEY,
         nome VARCHAR(255) NOT NULL,
         funcao VARCHAR(100) NOT NULL,
         setor VARCHAR(100) NOT NULL,
         data_solicitacao DATETIME NOT NULL,
         data_ocorrido DATE NOT NULL,
         local_fato VARCHAR(255) NOT NULL,
         descricao TEXT NOT NULL,
         status ENUM('Não Atendido', 'Aprovado', 'Atendido') DEFAULT 'Não Atendido',
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );
     ```
   - Insira um usuário administrador inicial:
     ```sql
     INSERT INTO usuarios (nome, username, password, nivel_acesso) 
     VALUES ('Administrador', 'admin', '$2y$10$exemplo_hash_senha', 'Admin');
     ```
     *Nota*: Substitua `$2y$10$exemplo_hash_senha` pelo hash da senha gerado com `password_hash('sua_senha', PASSWORD_DEFAULT)` em PHP.

4. **Configure as Credenciais do Banco de Dados**:
   - Edite os arquivos PHP (`auth.php`, `submit.php`, `solicitacoes.php`, `painel.php`, `dashboard.php`, `historico.php`, `edit_user.php`, `delete_user.php`) para configurar as credenciais do banco de dados:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "senha";
     $dbname = "cftv_banco";
     ```
   - Substitua os valores conforme seu ambiente.

5. **Inicie o Servidor Web**:
   - Certifique-se de que o Apache/Nginx e o MySQL estão em execução.
   - Acesse o site em `http://localhost/sistema_cftv.

## Uso

1. **Acesse o Sistema**:
   - Abra o navegador e acesse `ttp://localhost/sistema_cftv..
   - Faça login com as credenciais de um usuário registrado.

2. **Funcionalidades por Nível de Acesso**:
   - **Abertura**:
     - **Página Inicial** (`index.php`): Escolha entre "Abrir Solicitação" ou "Histórico de Atendimento".
     - **Nova Solicitação** (`nova_solicitacao.php`): Preencha o formulário (o campo "Nome" é preenchido automaticamente e é somente leitura).
     - **Histórico** (`historico.php`): Visualize suas solicitações com status (Não Atendido, Aprovado, Atendido).
   - **Autorização**:
     - Acesse `solicitacoes.php` para aprovar ou rejeitar solicitações.
   - **Monitor**:
     - Acesse `solicitacoes.php` para marcar solicitações aprovadas como atendidas.
   - **Admin**:
     - Acesse `painel.php` para gerenciar usuários (criar, editar, excluir).
     - Acesse `dashboard.php` para visualizar análises gráficas.
     - Acesse `solicitacoes.php` para aprovar ou marcar solicitações como atendidas.
   - **Todos os Usuários**:
     - Altere a senha em `change_password.php`.

3. **Logout**:
   - Clique em "Sair" no cabeçalho para encerrar a sessão.

## Estrutura do Banco de Dados

- **Tabela `usuarios`**:
  - `id`: Chave primária, auto-incremento.
  - `nome`: Nome completo do usuário (usado em formulários e histórico).
  - `username`: Nome de usuário único para login.
  - `password`: Senha criptografada (usando `password_hash`).
  - `nivel_acesso`: Enum ('Abertura', 'Autorização', 'Monitor', 'Admin').
  - `created_at`: Data de criação do usuário.

- **Tabela `solicitacoes`**:
  - `id`: Chave primária, auto-incremento.
  - `nome`: Nome do solicitante (vinculado ao `nome` do usuário).
  - `funcao`: Função do solicitante.
  - `setor`: Setor do solicitante.
  - `data_solicitacao`: Data e hora da solicitação.
  - `data_ocorrido`: Data do evento relatado.
  - `local_fato`: Local do evento.
  - `descricao`: Descrição detalhada do evento.
  - `status`: Enum ('Não Atendido', 'Aprovado', 'Atendido').
  - `created_at`: Data de criação da solicitação.

## Contribuição

1. **Fork o Repositório**:
   - Clique em "Fork" no GitHub para criar uma cópia do repositório.

2. **Clone o Fork**:
   ```bash
   git clone https://github.com/seu-usuario/cftv-gentil-bittencourt.git
   ```

3. **Crie uma Branch**:
   ```bash
   git checkout -b minha-nova-funcionalidade
   ```

4. **Faça Alterações**:
   - Implemente suas alterações nos arquivos PHP, HTML, ou CSS.
   - Teste localmente para garantir que o sistema funciona.

5. **Commit e Push**:
   ```bash
   git add .
   git commit -m "Descrição das alterações"
   git push origin minha-nova-funcionalidade
   ```

6. **Crie um Pull Request**:
   - No GitHub, crie um Pull Request para o repositório original, descrevendo suas alterações.

## Licença

Este projeto está licenciado sob a [Licença MIT](LICENSE). Veja o arquivo `LICENSE` para mais detalhes.

## Contato

Para dúvidas ou sugestões, entre em contato pelo GitHub Issues ou diretamente com o mantenedor do projeto.

---

**© 2025 Colégio Gentil Bittencourt. Desenvolvido com ❤️ para gerenciar solicitações de CFTV.**
