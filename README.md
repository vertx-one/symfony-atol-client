# symfony-atol-client
Documentation: https://online.atol.ru/files/API_atol_online_v4.pdf

Клиент для online.atol.ru API. 

Позволяет отправлять чеки в атол используя протокол v4

## Использование

Добавляем в .env
```ini
ATOL_IS_TEST_MODE=1
ATOL_LOGIN=""
ATOL_PASSWORD=""
ATOL_COMPANY_GROUP_NAME=""
ATOL_COMPANY_INN=""
```

Добавляем в config.yaml

```yaml
parameters:
  atol.is_test_mode: '%env(ATOL_IS_TEST_MODE)%'

services:
  VertxOne\Symfony\Atol\Client:
    arguments: [ '%env(ATOL_IS_TEST_MODE)%', '%env(string:ATOL_LOGIN)%', '%env(string:ATOL_PASSWORD)%', '%env(string:ATOL_COMPANY_GROUP_NAME)%']
```
