### Bem vindo !

## Como rodar o projeto:
  Bom este projeto é o back-end de cotações da root code, para rodar ele é necessário seguir alguns passos:
  - Clonar o repositório
  - Instalar as dependencias com 
  ```bash
  composer i
  ```
  - Criar o arquivo .env com a sua configuração de banco de dados que preferir
  - Pronto !

## Decisões e premissas
  - Eu decidi usar o laravel 12 por mais pratica, tenho utilizado bastante em projetos pessoais, e podemos instalar apenas oque vamos usar e utilizar poucas coisas, no caso da api, apartir do 11 (se nao me engano) ele nao vem com o arquivo api, então se voce utilizar "php artisan install:api" ele criará o arquivo pronto para uso.
  - Decidi utilizar o postgresql porque tenho utilizado ele ha alguns anos e tem me satisfazido muito bem, ele escala bem para projetos grandes e com bastante regra de negocio.
  - Ao me deparar com tantas funções decidi começar a logica centralizando tudo no controller para entender a regra de negócio, ver como tudo ia funcionar para depois começar a utilizar services e repository, decidi jogar calculos para os services e execuções de banco de dados no repository, criei a tabela quote, para salvar as cotações assim que é realizada o calculo, nao tive muitos problemas na lógica achei tudo bem explicado.
  - Ao me deparar com o salvamento dos valores no banco de dados, podendo dar erro ao salvar, decidi desacoplar o salvamento de dentro do service para que nao tenhamos uma mesma função fazendo 2 coisas ao mesmo tempo e uma delas acabar gerando erro desnecessário, e então ao terminar o calculo e devolver ao controller, eu chamo a outra funcao para apenas salvar os dados no banco, e se caso der erro ao salvar, ele irá informar em um log o erro (posteriormente poderia ter um tabela tambem de logs de calculos para retry e tambem visualização dos dados que foram, e qual foi o erro)