# teste-plataforma-verde
API RESTful desenvolvida em PHP/Lumen 5.6.4 e SQLite para teste na empresa Plataforma Verde. Tecnologias utilizadas e suas respectivas versões:

- Docker version 18.06.0-ce, build 0ffa825
- docker-compose version 1.8.0, build unknown
- Alpine Linux 3.8
- nginx version: nginx/1.14.0
- PHP 7.2.8 (cli) (built: Jul 28 2018 17:55:09) ( NTS )
- Composer version 1.7.1 2018-08-07 09:39:23
- Lumen 5.6.4
- Laravel Components 5.6.*
- SQLite
- Supervisor 3.3.4
- curl 7.61.0 (x86_64-alpine-linux-musl) libcurl/7.61.0 LibreSSL/2.6.5 zlib/1.2.11 libssh2/1.8.0
- PHPUnit 7.3.1 by Sebastian Bergmann and contributors.


# Descrição
API composta pelas ações de CRUD de um cadastro de produtos. São chamadas REST com comunicação via Json desenvolvidas em PHP 7.2.8 através do framework Lumen 5.6.4. Como é uma API que tem como fundamento a comunicação com a base de dados e o retorno Json, a mesma não possui views, apenas as chamadas HTTP com os verbos GET, PUT e DELETE. O verbo POST não foi implementado via CRUD, pois o processo de inclusão é realizado através da importação dos dados a partir de uma planilha. Esse processo é feito via Queue, adicionando a rotina de importação, processando periodicamente mediante a ordem da fila e mantendo um status do processamento. Foi utilizado o SQLite para armazenamento dos dados.

Além da aplicação, foram criados vários testes automatizados que são executados pelo PHPUnit.


# Instalação
Através do terminal, ir a até o local onde será copiado o conteúdo e fazer o clone do repositório:

git clone https://github.com/acgomes68/teste-plataforma-verde.git


Utilização de container Docker. Caso não tenha o Docker instalado, baixar e instalar a versão de acordo com seu sistema operacional em:

https://www.docker.com/community-edition#/download


Usar o Docker Compose que pode ser encontrado no link abaixo:

https://docs.docker.com/compose/install/


Voltando ao terminal na pasta de trabalho, executar:

cd teste-plataforma-verde

docker pull acgomes68/webserver

Isso irá baixar a imagem com a infra necessária que está hospedada no Docker Hub em:

https://hub.docker.com/r/acgomes68/webserver/

Executando no terminal:

docker images

Deve exibir algo parecido com:


REPOSITORY                        TAG		IMAGE ID 		CREATED			SIZE

acgomes68/webserver               latest	4f91bdd1ad0f   	2 hours ago     80MB

alpine                            latest	11cd0b38bc3c  	5 weeks ago     4.41MB


# Execução
Ainda no terminal, executar o shell script "run.sh":

chmod +x run.sh

sh run.sh OU ./run.sh

O script irá instalar as dependências do projeto e subirá o container. Após a execução, abrir o navegador e apontar para o endpoint abaixo:

http://localhost:8080/api/products

Será exibido o conteúdo referente ao verbo HTTP GET que lista todos os produtos cadastrados na base em formato JSON. A princípio, não retornará nenhum conteúdo.


Localização da planilha de importação:

src/storage/app


Para visualizar o status das importações:

http://localhost:8080/api/imports/


Para executar uma importação manual:

http://localhost:8080/api/imports/importall


Para limpar a base de dados para um novo teste de importação:

docker exec -it webserver php artisan migrate:refresh


Para adicionar o processo na fila:

http://localhost:8080/api/imports/execqueue


Para ativar o processamento da fila:

docker exec -it webserver php artisan queue:work


# Testes
Foram realizados testes manuais e automáticos. Os primeiros foram utilizados para dar apoio rápido ao desenvolvimento e os últimos foram criados para execução com o PHPUnit.

# Teste manual da API
Os testes manuais são básicos e serviram apenas para verificar o acesso ao endpoint, as validações, formatos e parâmetros de entrada e saída. Os testes manuais foram realizados através do Postman:

https://www.getpostman.com/apps

Os verbos HTTP testados foram os seguintes:

GET - Retorna todos os produtos

http://localhost:8080/api/products

GET - Lista um produto específico

http://localhost:8080/api/products/{id}

onde:

{id} - ID único do produto criado no momento da inclusão

Validação

- id: Id existente na base de dados


PUT - Atualiza dados de um produto específico

http://localhost:8080/api/products/{id}

onde: {id} - ID único do produto criado no momento da inclusão

Parâmetros de entrada em formato JSON. Exemplo:

{

	"name": "Furadeira X",
	"free_shipping": 0,
	"description": "Furadeira eficiente X",
	"price": 100.00,
	"category_id": 123123,

}

Validação
- id: Id existente na base de dados
- name: Obrigatório com máximo de 255 caracteres
- free_shipping: Opcional, mas se fornecido é verificado se corresponde a um formato booleano (0 ou 1)
- description: Opcional, mas se fornecido com tamanho máximo de 255 caracteres
- price: Obrigatório, com formato numérico decimal
- category_id: Opcional, mas se fornecido é verificado se corresponde a um formato numérico inteiro


DELETE - Exclui um produto específico

http://localhost:8080/api/products/{id}

onde: {id} - ID único do produto criado no momento da inclusão

Validação
- id: Id existente na base de dados


# Teste automático da API
Os testes foram criados segundo o padrão do PHPUnit visto que o mesmo já está presente no framework para essa função. Foi criado um arquivo de classe de teste específico para cada método do controller Product. Está em:

src/tests/ProductTest.php

para executar os testes basta ir no terminal na pasta src/ e executar:

vendor/bin/phpunit

Se não tiver PHP instalado localmente, pode executar diretamente no container (com o prefixo docker exec -it webserver):

docker exec -it webserver vendor/bin/phpunit (Essa regra serve para os demais comandos abaixo)


Isso fará com que todos os testes sejam executados.


Para executar o teste de uma classe ou arquivo:

vendor/bin/phpunit --filter <nome_classe> <path_arquivo>

Ex.:

vendor/bin/phpunit --filter ProductTest tests/ProductTest.php


Para executar o teste de um método de uma classe ou arquivo:

vendor/bin/phpunit --filter <nome_metodo> <nome_classe> <path_arquivo>

Ex.:

vendor/bin/phpunit --filter testDestroy ProductTest tests/ProductTest.php

