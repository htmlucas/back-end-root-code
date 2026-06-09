### Bem vindo !

## Como rodar o projeto:
  Bom este projeto é o back-end de cotações da root code, para rodar ele é necessário seguir alguns passos:
  - Clonar o repositório
  - Instalar as dependencias com 
  ```bash
  composer i
  ```
  - Criar o arquivo .env com a sua configuração de banco de dados que preferir
  - rodar o comando:
  ```bash
  php artisan key:generate
  ```
  - Pronto !

## Decisões e premissas
- Eu decidi usar o Laravel 12 por mais prática, já que tenho utilizado bastante em projetos pessoais. Ele permite instalar só o que a gente realmente vai usar. No caso da API, a partir do 11 (se não me engano) ele não vem mais com o arquivo pronto, então usando php artisan install:api ele já cria tudo configurado pra uso.
- Decidi utilizar PostgreSQL porque já venho usando há alguns anos e ele tem me atendido muito bem. Ele escala bem pra projetos maiores e com bastante regra de negócio.
- Ao me deparar com várias funções, comecei centralizando a lógica no controller pra entender melhor a regra de negócio e como tudo ia funcionar. Depois disso, fui separando melhor as responsabilidades, começando a usar services e repository. Deixei os cálculos nos services e as execuções de banco no repository. Também criei a tabela quote pra salvar as cotações assim que o cálculo é realizado. No geral, não tive muitos problemas na lógica, achei tudo bem tranquilo de entender.
- Quando cheguei na parte de salvar os dados no banco, percebi que poderia dar erro no meio do processo. Então decidi desacoplar esse salvamento do service, pra não ter uma única função fazendo duas coisas ao mesmo tempo. Assim, o service termina o cálculo e retorna os dados pro controller, e só depois uma outra função cuida do salvamento. Se der erro ao salvar, ele registra em um log. (Mais pra frente, poderia até virar uma tabela de logs de cálculo, pra retry e pra visualizar o que foi processado e quais erros aconteceram.)
- Utilizei colunas JSON tanto em avisos quanto em participantes pra ganhar agilidade no desenvolvimento, mas também daria pra criar uma tabela de participantes relacionada à cotação (ou à viagem, dependendo do modelo).