# 🐳 Guia de Execução com Docker

Este guia explica como rodar o projeto utilizando os scripts `docker.sh` e `database.sh` fornecidos.

## ⚠️ Aviso Importante

O script `docker.sh` realiza uma **limpeza completa** do ambiente. Ele irá:
- Deletar a pasta `vendor` e `node_modules`.
- Deletar o arquivo `composer.lock`.
- **DELETAR O BANCO DE DADOS** (pasta `.docker/postgresql/data`).

Use este script apenas para a **configuração inicial** ou quando quiser **resetar completamente** o projeto.

---

## 🚀 Passo a Passo

### 1. Iniciar o Ambiente (Host)

No terminal da sua máquina (fora do container), execute o script `docker.sh`. Este script irá preparar as permissões, instalar dependências, subir os containers e, ao final, entrará automaticamente no terminal do container `app`.

```bash
./docker.sh
```

> **Nota:** O script pode demorar alguns minutos na primeira execução pois fará o build das imagens e instalará todas as dependências.

### 2. Configurar o Banco de Dados (Dentro do Container)

Após o passo anterior, você estará dentro do terminal do container (o prompt deve mudar para algo como `root@...:/var/www#`). Agora, execute o script `database.sh` para rodar as migrações e configurar o Passport.

```bash
sh database.sh
```

Este script irá:
1. Rodar as migrações do banco de dados (`php artisan migrate`).
2. Criar as chaves do Laravel Passport (`php artisan passport:client --personal`).

### 3. Acessar a Aplicação

Após concluir os passos acima, o projeto estará rodando:

- **Aplicação Web:** [http://localhost:9873](http://localhost:9873)
- **PgAdmin (Gerenciador do Banco):** [http://localhost:9081](http://localhost:9081)
  - **Email:** ti@hostaraguaia.com.br
  - **Senha:** hostaraguaia

---

## 🛠️ Comandos Úteis (Dia a Dia)

Se você já rodou o setup inicial e quer apenas parar ou iniciar o projeto **sem apagar o banco de dados**, use os comandos do Docker Compose diretamente:

**Parar os containers:**
```bash
docker compose down
```

**Iniciar os containers (sem resetar):**
```bash
docker compose up -d
```

**Entrar no container da aplicação:**
```bash
docker compose exec app bash
```
