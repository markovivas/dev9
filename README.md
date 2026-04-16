# Intranet Dashboard Base

Tema base de WordPress criado para servir como ponto de partida de uma intranet corporativa sem dependencias de terceiros.

## Estrutura inicial

- `style.css`: cabecalho do tema para reconhecimento no WordPress
- `functions.php`: suporte do tema, menus, sidebars e assets
- `front-page.php`: dashboard inicial inspirado nas referencias visuais
- `page.php`, `single.php`, `archive.php`, `index.php`: templates basicos
- `assets/css/main.css`: estilos principais
- `assets/js/main.js`: script base

## Como usar

1. Copie a pasta `intranet-dashboard-theme` para `wp-content/themes/`
2. Ative o tema no painel do WordPress
3. Salve novamente os links permanentes para registrar a rota `busca-interna`
4. Defina uma pagina inicial estatica se quiser usar o dashboard na home
5. Configure os menus `primary` e `utility`
6. Preencha as areas de widget para substituir os blocos de exemplo

## Proximos passos sugeridos

- criar taxonomias para setores e categorias internas
- adicionar documentos, politicas e ramais como modulos proprios
- integrar controles de permissao por perfil e area
- evoluir o dashboard com filtros por departamento e atalhos por usuario

## Modulos nativos ja incluidos

- `Comunicados`: gerenciados por CPT proprio com listagem automatica na home
- `Eventos`: CPT com campos de data/hora e local
- `Eventos`: calendario mensal no dashboard, agenda completa em `/eventos/`, tela cheia e estrutura nativa do tema
- `Links Uteis`: CPT para atalhos corporativos com destaque no dashboard
- `Documentos`: CPT com categorias, URL de arquivo/link alternativo e destaque no dashboard
- `Perfil da intranet`: campos de usuario para cargo, departamento, aniversario e ramal
- `Aniversariantes do mes`: modulo alimentado pelos perfis de usuario
- `Busca interna`: rota propria do tema em `/busca-interna/`, sem usar a busca nativa do WordPress

## Defaults do tema

- widgets nativos `Pesquisar`, `Posts recentes`, `Comentarios recentes`, `Arquivos` e `Categorias` sao removidos
- as areas `Home - Coluna Direita` e `Rodape - Coluna 1` ficam limpas na ativacao do tema
- a busca nativa `?s=` e redirecionada para a busca interna do tema
