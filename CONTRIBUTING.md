# 🤝 Guia de Contribuição - Origo

Este documento descreve diretrizes de contribuição **interna** para o projeto Origo.

---

## 📋 Índice

- [Código de Conduta](#código-de-conduta)
- [Como Posso Contribuir?](#como-posso-contribuir)
- [Configurando o Ambiente de Desenvolvimento](#configurando-o-ambiente-de-desenvolvimento)
- [Workflow de Desenvolvimento](#workflow-de-desenvolvimento)
- [Padrões de Código](#padrões-de-código)
- [Convenções de Commit](#convenções-de-commit)
- [Testes](#testes)
- [Pull Requests](#pull-requests)

---

## 📜 Código de Conduta

Este projeto adere a um código de conduta. Ao participar, você concorda em manter um ambiente respeitoso e inclusivo para todos.

---

## 🚀 Como Posso Contribuir?

### Reportando Bugs

Antes de criar um relatório de bug:
- Verifique se o bug já não foi reportado
- Verifique se você está usando a versão mais recente
- Colete informações sobre o bug

**Ao reportar um bug, inclua:**
- Descrição clara e concisa
- Passos para reproduzir
- Comportamento esperado vs. comportamento atual
- Screenshots (se aplicável)
- Ambiente (OS, PHP version, etc.)

### Sugerindo Melhorias

Sugestões de melhorias são bem-vindas! Ao sugerir:
- Use um título claro e descritivo
- Forneça uma descrição detalhada da melhoria
- Explique por que essa melhoria seria útil
- Liste exemplos de como funcionaria

### Contribuindo com Código

1. **Escolha uma issue** ou crie uma nova
2. **Comente na issue** que você vai trabalhar nela
3. **Siga o workflow** descrito abaixo
4. **Submeta um Pull Request**

---

## 🛠️ Configurando o Ambiente de Desenvolvimento

Siga as instruções no [README.md](README.md) para configurar o ambiente local.

### Ferramentas Recomendadas

- **IDE**: PhpStorm, VS Code com extensões PHP
- **Git GUI**: GitKraken, SourceTree (opcional)
- **Database Client**: TablePlus, DBeaver, pgAdmin
- **API Testing**: Postman, Insomnia

### Extensões VS Code Recomendadas

```json
{
  "recommendations": [
    "bmewburn.vscode-intelephense-client",
    "onecentlin.laravel-blade",
    "amiralizadeh9480.laravel-extra-intellisense",
    "ryannaddy.laravel-artisan",
    "mikestead.dotenv",
    "esbenp.prettier-vscode"
  ]
}
```

---

## 🔄 Workflow de Desenvolvimento

### 1. Clone

```bash
git clone <URL_DO_REPOSITORIO_PRIVADO>
cd origo
```

### 2. Crie uma Branch

```bash
# Atualize sua main
git checkout main
git pull origin main

# Crie uma branch para sua feature/fix
git checkout -b feature/nome-da-feature
# ou
git checkout -b fix/nome-do-bug
```

### 3. Desenvolva

- Faça suas alterações
- Siga os padrões de código
- Adicione/atualize testes
- Teste localmente

### 4. Commit

```bash
git add .
git commit -m "feat: adiciona funcionalidade X"
```

### 5. Push

```bash
git push origin feature/nome-da-feature
```

### 6. Pull Request

- Abra um PR no GitHub
- Preencha o template do PR
- Aguarde review

---

## 📝 Padrões de Código

### PHP

Seguimos o **PSR-12** e usamos **Laravel Pint** para formatação:

```bash
# Formatar código
./vendor/bin/pint

# Verificar sem modificar
./vendor/bin/pint --test
```

#### Convenções Laravel

**Controllers:**
```php
class CampaignController extends Controller
{
    public function index()
    {
        // Lógica simples no controller
        $campaigns = Campaign::published()->latest()->get();
        
        return view('campaigns.index', compact('campaigns'));
    }
}
```

**Actions:**
```php
namespace App\Actions;

class PublishCampaign
{
    public function execute(Campaign $campaign): void
    {
        // Lógica de negócio complexa em Actions
        $campaign->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
```

**Models:**
```php
class Campaign extends Model
{
    protected $fillable = ['title', 'description', 'goal'];
    
    protected $casts = [
        'goal' => 'decimal:2',
        'published_at' => 'datetime',
    ];
    
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
```

### Blade Templates

```blade
{{-- Use componentes para reutilização --}}
<x-card>
    <x-slot:title>
        {{ $campaign->title }}
    </x-slot>
    
    <p>{{ $campaign->description }}</p>
</x-card>

{{-- Evite lógica complexa nas views --}}
{{-- BOM --}}
@if($campaign->isPublished())
    <span class="badge bg-success">Publicada</span>
@endif

{{-- RUIM --}}
@if($campaign->status === 'published' && $campaign->published_at !== null)
    <span class="badge bg-success">Publicada</span>
@endif
```

### JavaScript

```javascript
// Use Alpine.js para interatividade simples
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Conteúdo</div>
</div>

// Para lógica complexa, use arquivos JS separados
// resources/js/components/campaign-form.js
```

### CSS

```css
/* Use classes do Bootstrap quando possível */
/* Para estilos customizados, use BEM */
.campaign-card {}
.campaign-card__title {}
.campaign-card__description {}
.campaign-card--featured {}
```

---

## 💬 Convenções de Commit

Seguimos o padrão [Conventional Commits](https://www.conventionalcommits.org/):

### Formato

```
<tipo>(<escopo>): <descrição>

[corpo opcional]

[rodapé opcional]
```

### Tipos

- `feat`: Nova funcionalidade
- `fix`: Correção de bug
- `docs`: Documentação
- `style`: Formatação (não afeta o código)
- `refactor`: Refatoração
- `test`: Testes
- `chore`: Tarefas de manutenção
- `perf`: Melhorias de performance

### Exemplos

```bash
feat(campaigns): adiciona filtro por categoria
fix(pledges): corrige cálculo de total arrecadado
docs(readme): atualiza instruções de instalação
refactor(dashboard): simplifica query de estatísticas
test(campaigns): adiciona testes para publicação
```

---

## 🧪 Testes

### Executando Testes

```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter CampaignTest

# Com coverage
php artisan test --coverage
```

### Escrevendo Testes

**Feature Test:**
```php
public function test_user_can_create_campaign()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post('/me/campaigns', [
        'title' => 'Nova Campanha',
        'description' => 'Descrição',
        'goal' => 10000,
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('campaigns', [
        'title' => 'Nova Campanha',
    ]);
}
```

**Unit Test:**
```php
public function test_campaign_calculates_progress_correctly()
{
    $campaign = Campaign::factory()->create(['goal' => 10000]);
    Pledge::factory()->create(['campaign_id' => $campaign->id, 'amount' => 2500]);
    
    $this->assertEquals(25, $campaign->progress());
}
```

### Cobertura de Testes

Mantenha a cobertura acima de 70%:
- Controllers: testes de feature
- Actions: testes de unit
- Models: testes de unit para métodos complexos

---

## 🔍 Pull Requests

### Antes de Submeter

- [ ] Código segue os padrões do projeto
- [ ] Testes foram adicionados/atualizados
- [ ] Todos os testes passam
- [ ] Documentação foi atualizada
- [ ] Commits seguem as convenções
- [ ] Branch está atualizada com main

### Template de PR

```markdown
## Descrição
Breve descrição das mudanças

## Tipo de Mudança
- [ ] Bug fix
- [ ] Nova feature
- [ ] Breaking change
- [ ] Documentação

## Como Testar
1. Passo 1
2. Passo 2
3. ...

## Checklist
- [ ] Testes passando
- [ ] Código formatado
- [ ] Documentação atualizada

## Screenshots (se aplicável)
```

### Processo de Review

1. **Automated checks** devem passar
2. **Code review** por pelo menos 1 mantenedor
3. **Testes** devem estar passando
4. **Conflitos** devem ser resolvidos
5. **Aprovação** e merge

### Respondendo a Reviews

- Seja receptivo a feedback
- Faça as alterações solicitadas
- Responda a comentários
- Marque conversas como resolvidas

---

## 🎯 Áreas para Contribuir

### Backend
- Integração com gateways de pagamento
- Sistema de notificações
- API REST
- Testes automatizados

### Frontend
- Melhorias de UX/UI
- Responsividade
- Acessibilidade
- Performance

### DevOps
- CI/CD
- Docker
- Deploy automation
- Monitoring

### Documentação
- Tutoriais
- Exemplos
- Traduções
- API docs

---

## 📚 Recursos

- [Laravel Docs](https://laravel.com/docs)
- [PSR-12](https://www.php-fig.org/psr/psr-12/)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [Git Flow](https://nvie.com/posts/a-successful-git-branching-model/)

---

## ❓ Dúvidas?

- Abra uma [issue](https://github.com/REPO/issues)
- Entre em contato com os mantenedores

---

**Obrigado por contribuir! 🚀**
