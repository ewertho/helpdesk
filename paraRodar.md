## Baixar php 7 ou 8

## Baixar composer

### rodar composer update

### rodar php artisan serve

-   não esquecer de criar um novo .env pode ser a partir do .env.example
-   definir filesystem_driver como public para os arquivos serem publicos e conseguir pegar pelo sistema
    e apos isso rodar php artisan storage:link para que seja criado um link entre a pasta storage e a pasta public
    para que os arquivos sejam vistos pelo sistema publico da aplicação

## adicionar um banco, de preferencia mysql

### tem um docker-compose no projeto, gerar essa imagem e pode utilizar de boas

na entrada do adminer com a imagem ja pronta, definir o host como mysqlsrv usuario como root e senha como helpdesk
