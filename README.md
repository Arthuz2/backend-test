# Investments API

API REST desenvolvida em Symfony para gerenciamento de investimentos.

O sistema permite:

- Criar um investimento
- Consultar investimento por ID
- Realizar saque de investimento
- Listar investimentos por proprietário

---

## Tecnologias Utilizadas

- PHP 8+
- Symfony
- Doctrine ORM
- MySQL
- Docker
- PHPUnit
- KNP Paginator

---

## Instalação

Clone o repositório:

```sh
git clone <repo-url>
cd backend-test
```

Instale as dependências:

Configure o arquivo `.env` com suas credenciais de banco de dados.

```sh
composer install
```

Suba o container:
```sh
docker-compose up -d
```

Execute as migrations:

```sh
php bin/console doctrine:migrations:migrate
```

Inicie o servidor:

```sh
symfony server:start
```

ou

```sh
php -S localhost:8000 -t public
```

A API estará disponível em:

http://localhost:8000

---

## Endpoints

### Criar investimento

POST /investments

Body:

```json
{
    "ownerEmail": "user@email.com",
    "investedValue": 1000,
    "creationDate": "2024-01-01"
}
```

---

### Buscar investimento por ID

GET /investments/{id}

---

### Realizar saque

POST /investments/{id}/withdraw

Body:

```json
{
    "withdrawDate": "2024-12-01"
}
```

---

### Listar investimentos por proprietário

GET /investments?ownerEmail=user@email.com&page=1&limit=10

Query Params:

- ownerEmail (obrigatório)
- page (opcional, padrão 1)
- limit (opcional, padrão 10, máximo 50)

---

## Postman Collection

O arquivo `postman_collection.json` está disponível na raiz do projeto.

Para utilizar:

1. Abra o Postman
2. Clique em "Import"
3. Selecione o arquivo `postman_collection.json`

A collection contém todos os endpoints com exemplos prontos.

---

## Testes

Testes unitários foram implementados para a camada de regra de negócio:

- InvestmentCalculatorService::calculateMonths
- InvestmentCalculatorService::calculateBalance
- InvestmentCalculatorService::calculateTax

Para executar os testes:

```sh
php bin/phpunit
```

---

## Decisões Técnicas

- Uso de DTO para entrada de dados
- Validação centralizada via DTOValidatorService
- UUID como identificador de investimento
- Paginação implementada com KNP Paginator
- Regra de cálculo isolada em InvestmentCalculatorService para facilitar testes

---

### Desenvolvido por Arthur Porcino
