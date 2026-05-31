# Intranet Dashboard Base

Tema base de WordPress para intranet corporativa sem dependencias de terceiros.

## Estrutura

- `functions.php`: carrega os modulos do tema
- `inc/setup.php` — suporte do tema, assets, widgets, rewrite rules
- `inc/helpers.php` — funcoes utilitarias (tempo de leitura, URLs, sanitize)
- `inc/intranet-modules.php` — CPTs, taxonomias, metaboxes, queries
- `inc/weather.php` — previsao do tempo via Open-Meteo
- `inc/login.php` — personalizacao da tela de login
- `inc/profile.php` — edicao de perfil no front-end
- `assets/css/main.css` — estilos principais
- `assets/css/login.css` — estilos da tela de login
- `assets/js/main.js` — calendario, preview de foto, validacao de senha

## Como usar

1. Copie a pasta para `wp-content/themes/`
2. Ative o tema no painel do WordPress
3. Acesse **Configuracoes > Links Permanentes** e clique em "Salvar alteracoes"
4. Defina uma pagina inicial estatica se quiser o dashboard na home
5. Configure os menus `primary` e `utility`
6. Preencha as areas de widget para substituir os blocos de exemplo

> Ao ativar o tema, as rotas `/meu-perfil/`, `/busca-interna/` e `/noticias/` sao registradas automaticamente. Se alguma rota retornar 404, repita o passo 3.

## Modulos nativos

- **Comunicados** — CPT com listagem automatica na home
- **Eventos** — CPT com data/hora, local e tipo; calendario mensal via AJAX; agenda em `/eventos/`
- **Links Uteis** — CPT com URL e descricao; destaque no dashboard
- **Documentos** — CPT com categorias, upload de arquivo e controle de acesso por departamento
- **Perfil** — campos de cargo, departamento, aniversario e ramal; edicao via `/meu-perfil/`
- **Aniversariantes** — listagem dos aniversarios do mes na home
- **Busca interna** — rota `/busca-interna/` com busca em paginas, posts, comunicados, eventos e documentos
- **Previsao do tempo** — integracao com Open-Meteo; latitude/longitude configuradas no Customizer

## Personalizacao

- **Tela de login**: Customizer > Tela de Login (logo, scroll, seletor de idioma)
- **Previsao do tempo**: Customizer > Previsao do Tempo (latitude, longitude, rotulo)
- **Documentos por departamento**: ao editar uma categoria de documento, selecione os departamentos permitidos
