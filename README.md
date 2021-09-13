[![SymfonyInsight](https://insight.symfony.com/projects/31dd9a60-114b-4826-9817-1d8c75f2f87b/big.svg)](https://insight.symfony.com/projects/31dd9a60-114b-4826-9817-1d8c75f2f87b)

Lien du projet GITHUB: https://github.com/dinasty-serv/p6_openclassrooms


# p6_openclassrooms
Installation dev: 
1) git@github.com:dinasty-serv/p6_openclassrooms.git ./snowtricks
2) cd snowtricks
3) Configure DATABASE_URL and MAILER_URL into .env.dev
4) composer install
5) composer prepare-dev
6) php bin/console server:start 0.0.0.0:8000

Installation test:
1) git@github.com:dinasty-serv/p6_openclassrooms.git ./snowtricks
2) cd snowtricks
3) Configure DATABASE_URL and MAILER_URL into .env
4) composer install
5) composer prepare-test
6) php bin/console server:start 0.0.0.0:8000
