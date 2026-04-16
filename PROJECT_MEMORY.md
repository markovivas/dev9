# Project Memory

## Resumo

Projeto de tema WordPress customizado para intranet corporativa, sem dependencias de terceiros no tema final.

Objetivo principal:
- usar WordPress como base da intranet
- manter visual de dashboard corporativo
- implementar modulos nativos no tema
- evitar plugins/temas/addons de terceiros para a solucao final

## Direcao do projeto

Decisao tomada:
- o tema sera a base principal da intranet
- os modulos devem ser implementados de forma nativa
- o sistema de eventos sera usado do zero no tema
- a compatibilidade com o plugin antigo de eventos foi removida

Observacao:
- ainda existe uma pasta local `eventos/` no projeto com o plugin antigo apenas como referencia/historico
- o tema atual nao depende mais desse plugin

## Estrutura principal do tema

Arquivos mais importantes:
- [functions.php](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\functions.php)
- [front-page.php](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\front-page.php)
- [inc/intranet-modules.php](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\inc\intranet-modules.php)
- [assets/css/main.css](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\assets\css\main.css)
- [assets/js/main.js](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\assets\js\main.js)
- [archive-evento.php](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\archive-evento.php)
- [single-evento.php](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\single-evento.php)
- [search-intranet.php](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\search-intranet.php)

## Modulos ja implementados

### Comunicados

- CPT `comunicado`
- aparece automaticamente na HOME
- usado como modulo de noticias/comunicados internos

### Eventos

- CPT `evento`
- taxonomia `tipo_evento`
- metadados atuais:
  - `_event_start_date`
  - `_event_end_date`
  - `_event_location`
- calendario mensal no dashboard
- lista de proximos eventos na HOME
- agenda completa em `/eventos/`
- modo tela cheia no calendario
- template individual do evento

Importante:
- o sistema antigo baseado em `data_evento`, `hora_evento` e `local_evento` foi removido
- a partir daqui, eventos devem usar apenas os metadados atuais do tema

### Links Uteis

- CPT `link_util`
- metadados:
  - `_useful_link_url`
  - `_useful_link_description`
  - `_useful_link_featured`
- aparece na HOME

### Documentos

- CPT `documento`
- taxonomia `documento_categoria`
- metadados:
  - `_document_file_url`
  - `_document_external_url`
  - `_document_file_type`
  - `_document_featured`
- aparece na HOME

### Perfil da intranet

Campos de usuario:
- `job_title`
- `department`
- `birthday`
- `extension_number`

Uso atual:
- card de perfil
- aniversariantes do mes

## HOME atual

A HOME foi organizada como dashboard com modulos reais.

Blocos principais:
- perfil
- destaque central
- busca do tema
- aniversariantes
- documentos
- comunicados
- calendario de eventos
- proximos eventos
- links uteis

Importante:
- calendario e proximos eventos agora aparecem fixos na HOME
- eles nao dependem mais da sidebar `home-right`

## Busca

A busca nativa do WordPress foi desativada no front.

Padrao atual:
- rota customizada: `/busca-interna/`
- arquivo: [search-intranet.php](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\search-intranet.php)
- a busca do dashboard usa essa rota
- `?s=` redireciona para a busca interna

## Widgets e defaults do tema

Defaults definidos:
- widgets nativos `Pesquisar`, `Posts recentes`, `Comentarios recentes`, `Arquivos` e `Categorias` sao removidos
- areas `Home - Coluna Direita` e `Rodape - Coluna 1` sao limpas ao ativar o tema

Observacao:
- widgets continuam existindo como extensao do layout
- os modulos principais da HOME nao devem depender deles

## Taxonomias atuais

Taxonomias ja registradas:
- `tipo_evento`
- `documento_categoria`

Padrao atual das taxonomias:
- hierarquicas
- com `show_ui`
- com `show_admin_column`
- com `show_in_quick_edit`
- com `show_in_rest`
- rewrite hierarquico
- `with_front` desativado

## Decisoes visuais ja aplicadas

- visual base inspirado em dashboard corporativo
- navegacao superior horizontal
- cards modulares
- calendario proprio no tema
- fundo de `.em-dia-celula` removido

## Pontos de atencao

### Eventos na HOME

Se a HOME mostrar a mensagem:
`Cadastre eventos com data e local para exibir a agenda da intranet.`

Verificar:
- se `_event_start_date` foi salvo
- se a data/hora do evento esta no futuro
- se o formato salvo no metadado esta valido para `strtotime`

Hoje a listagem de proximos eventos considera:
- apenas eventos publicados
- apenas eventos com data/hora futura

## Pastas de referencia

Existe uma pasta:
- [eventos](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\eventos)

Essa pasta contem o plugin antigo usado apenas como referencia historica. Nao deve ser considerada parte obrigatoria da arquitetura final.

## Proximos passos recomendados

Melhorias naturais para continuar:
- criar filtros por `tipo_evento` na agenda completa
- melhorar a UX do cadastro de eventos
- permitir eventos sem horario, se isso fizer sentido para o negocio
- criar modulo de documentos com listagem completa e filtros
- adicionar controle de acesso por perfil/departamento
- criar pagina de configuracoes da intranet no admin
- revisar `README.md` para manter alinhado com a evolucao do projeto

## Continuidade

Se este projeto for retomado depois:
- abrir primeiro [PROJECT_MEMORY.md](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\PROJECT_MEMORY.md)
- depois revisar [README.md](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\README.md)
- em seguida validar [inc/intranet-modules.php](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\inc\intranet-modules.php) e [front-page.php](c:\Users\Marco\Desktop\new\intranet-dashboard-theme\front-page.php)

## Backup recomendado

Para nao perder o historico do projeto:
- manter esta pasta em backup
- versionar em Git
- guardar este arquivo junto com o projeto
