\# Objetivo

Implementar o fluxo completo de \*\*orçamentos com calculadora 3D integrada\*\*, painel administrativo com permissões, \*\*upload de imagens\*\* estáveis e \*\*módulo de artigos\*\* (blog), mantendo o layout centralizado e responsivo.



\## Principais mudanças



\### 1) Estrutura e segurança

\- `config.php` na raiz, com `BASE\_URL` dinâmica, `UPLOAD\_DIR`/`UPLOAD\_URL` corretos.

\- Sessão segura + \*\*CSRF\*\*.

\- Helpers `require\_login()`, `is\_admin()` e `require\_admin()`.

\- Footer fixo e containers com respiro (30px).



\### 2) Calculadora 3D

\- Cliente informa \*\*peso\*\* e \*\*tempo\*\*; demais custos usam defaults.

\- Cliente vê \*\*só preço final\*\*; admin vê \*\*detalhamento\*\* e pode ajustar parâmetros \*\*por orçamento\*\*.

\- Resultado fica no `sessionStorage` com \*\*carimbo de tempo\*\* e é limpo após uso.

\- Modal “Criar orçamento?” quando o cálculo não está vinculado a um orçamento.



\### 3) Orçamentos

\- `novo-orcamento.php` aceita orçamento sem cálculo (marca `PENDENTE`).

\- Se vier da calculadora com `?usecalc=1` e cálculo recente, já preenche e marca `ESTIMADO`.

\- Upload de imagem (JPG/PNG/WebP, 20MB), salva em `storage/uploads/AAAA/MM`, com metadados em `budget\_files`.

\- `meus-orcamentos-view.php`: status, `calc\_state`, anexos e ações (“Quero calcular”, “Excluir orçamento”).



\### 4) Painel administrativo

\- `admins\_orcamentos.php` (apenas admin) com filtros por \*\*texto, status, cálculo e período\*\*.

\- Atualização inline de `status` e `calc\_state`.

\- Ação “Calcular” abre calculadora vinculada e retorna após salvar.

\- Exclusão do orçamento (com CSRF).



\### 5) Blog / Artigos

\- Tabela `posts` com `slug` único, `title`, `thumb\_url`, `excerpt`, `body`, `published`, `created\_at`.

\- Listagem/pesquisa e página de visualização.

\- Home exibe 3 tópicos fixos (com imagens), \*\*carrossel\*\* e \*\*artigos recentes\*\*.



\## Migrações SQL



> Ver `db/migrations/\*.sql` (idempotentes via INFORMATION\_SCHEMA).



\## Checklist de testes

\- \[ ] Criar orçamento \*\*sem\*\* cálculo → “Pendente de cálculo”.

\- \[ ] Calcular sem orçamento → modal “Criar orçamento?” → vínculo correto.

\- \[ ] Em orçamento pendente, “Quero calcular” → \*\*salva e retorna\*\* `ESTIMADO`.

\- \[ ] Admin atualiza `status`/`calc\_state` no painel.

\- \[ ] Exclusão (cliente/admin) remove também anexos.

\- \[ ] Uploads servidos por `UPLOAD\_URL`.

\- \[ ] Artigos aparecem na home e na listagem; busca funciona.

\- \[ ] Layout central + footer fixo.



\## Próximos passos

\- CRUD de posts com upload de capa e editor rico (admin).

\- Marcar cálculo `FINAL` com etapa de aprovação.

\- Notificações por e-mail em mudança de status.



