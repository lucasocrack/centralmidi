<?php
/**
 * Template Name: Ferramentas Admin Vue
 */
if (!is_user_logged_in() || !current_user_can('manage_options')) {
  wp_redirect(wp_login_url(get_permalink()));
  exit;
}
get_header();
$ajax_url = admin_url('admin-ajax.php');
$nonce = wp_create_nonce('pa_admin_nonce');
?>

<main id="main" class="admin-tools-page">
  <div id="app" v-cloak>
    <div class="shop-container">
      <!-- Toast Notification -->
      <transition name="toast">
        <div v-if="toast.show" :class="['admin-toast', toast.type]">
          {{ toast.message }}
        </div>
      </transition>

      <div class="admin-header">
        <!-- Navegação de Abas -->
        <div class="admin-tabs-nav">
          <button type="button" class="tab-nav-btn" :class="{ 'is-active': activeTab === 'produtos' }" @click="activeTab = 'produtos'">
            📦 Gestão de Produtos
          </button>
          <button type="button" class="tab-nav-btn" :class="{ 'is-active': activeTab === 'demos' }" @click="activeTab = 'demos'">
            🎧 Conciliação de Demos & Órfãos
            <span v-if="demoAudit && (demoAudit.summary.fixable_count > 0 || demoAudit.summary.orphans_count > 0)" class="tab-alert-badge">!</span>
          </button>
        </div>

        <div v-show="activeTab === 'produtos'">
          <div class="admin-header-top">
            <div class="stats-grid">
              <div class="stat-card">
                <div class="stat-number">{{ stats.total }}</div>
                <div class="stat-label">Total Produtos</div>
              </div>
              <div class="stat-card">
                <div class="stat-number">{{ stats.withDemo }}</div>
                <div class="stat-label">Com Demo</div>
              </div>
            </div>

            <div class="actions-row">
              <button @click="exportCSV" class="button" :disabled="isExporting">
                {{ isExporting ? '⏳ Exportando...' : '📥 Exportar CSV' }}
              </button>
              <button @click="checkDuplicates" class="button" :disabled="isCheckingDupes">
                {{ isCheckingDupes ? '⏳ Verificando...' : '🔍 Verificar Duplicidade' }}
              </button>
            </div>

            <div class="prod-filters-row">
              <select v-model="filters.filtro_artista" class="filtro-select">
                <option value="">Artista: Todos</option>
                <option v-for="t in taxonomies.product_cat" :key="t.id" :value="t.id">{{ t.name }}</option>
              </select>
              <select v-model="filters.filtro_genero" class="filtro-select">
                <option value="">Gênero: Todos</option>
                <option v-for="t in taxonomies.genero_musical" :key="t.id" :value="t.id">{{ t.name }}</option>
              </select>
              <select v-model="filters.filtro_mes" class="filtro-select">
                <option value="">Mês: Todos</option>
                <option v-for="t in taxonomies.mes_de_lancamento" :key="t.id" :value="t.id">{{ t.name }}</option>
              </select>
              <select v-model="filters.filtro_tipo" class="filtro-select">
                <option value="">Tipo: Todos</option>
                <option v-for="t in taxonomies.tipo" :key="t.id" :value="t.id">{{ t.name }}</option>
              </select>
              <select v-model="filters.filtro_demo" class="filtro-select">
                <option value="all">Demo: Todos</option>
                <option value="has">Demo: Com demo</option>
                <option value="none">Demo: Sem demo</option>
              </select>
              <button @click="clearFilters" class="button button-sm" title="Limpar Filtros">✕</button>
            </div>
          </div>

          <div class="prod-search-row">
            <div class="search-group" style="width: 25%;">
              <span class="search-icon">🔗</span>
              <input type="text" v-model="filters.filtro_demo_search" class="search-input" placeholder="Buscar URL demo..." @keyup.enter="loadProducts(1)">
            </div>
            
            <div class="search-group grow">
              <span class="search-icon">🎵</span>
              <input type="text" v-model="filters.search" class="search-input" placeholder="Música..." @keyup.enter="loadProducts(1)">
            </div>

            <div class="search-group" style="width: 160px;">
              <span class="search-icon">#</span>
              <input type="number" v-model="filters.search_id" class="search-input" placeholder="ID" @keyup.enter="loadProducts(1)">
            </div>

            <button @click="loadProducts(1)" class="button primary load-btn" :class="{loading: isLoading}">
              <span>📦 Carregar</span>
              <span class="total-badge">{{ totalProducts }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- CONTEÚDO DA ABA 1: PRODUTOS -->
      <div v-show="activeTab === 'produtos'">
      <div v-if="products.length" class="results-table-wrap" style="margin-top:8px; position: relative;">
        <!-- Overlay de Carregamento da Tabela -->
        <div v-if="isLoading" class="table-loading-overlay">
          <div class="spinner"></div>
          <span>Carregando dados...</span>
        </div>
        
        <table class="results-table" :class="{ 'is-loading': isLoading }">
          <thead>
            <tr>
              <th class="col-id">ID</th>
              <th class="col-artista">Artista</th>
              <th class="col-musica">Música</th>
              <th class="col-rlm">Classificação</th>
              <th class="col-tipo">Tipo</th>
              <th class="col-preco">Preço</th>
              <th class="col-genero">Gênero</th>
              <th class="col-mes">Mês</th>
              <th class="col-demo">Demo</th>
              <th class="col-actions">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in products" :key="p.id">
              <td class="col-id" style="font-size: 11px;">{{ p.id }}</td>
              <td>
                <div class="artista-picker">
                  <input type="text" class="prod-input" v-model="p.artista_busca"
                         @input="buscarArtista(p)" @keydown.escape="p.artista_opcoes = []"
                         placeholder="artista" :disabled="p.isSaving">
                  <ul v-if="p.artista_opcoes && p.artista_opcoes.length" class="artista-opcoes">
                    <li v-for="o in p.artista_opcoes" :key="o.id" @click="escolherArtista(p, o)">
                      {{ o.name }} <em v-if="o.count">{{ o.count }}</em>
                    </li>
                  </ul>
                  <button v-if="p.artista_id" class="artista-limpar" title="Remover artista"
                          @click="limparArtista(p)" :disabled="p.isSaving">&times;</button>
                </div>
              </td>
              <td class="col-musica">
                <a href="#" @click.prevent="openEditModal(p, 'post_title')" :class="{ 'is-disabled': p.isSaving }">{{ p.title }}</a>
                <span v-if="p.isSaving" class="save-spinner"></span>
              </td>
              <td>
                <select v-model="p.rlm" class="prod-select" @change="saveProduct(p, 'rlm')" :disabled="p.isSaving">
                  <option value="">— Nenhum —</option>
                  <option value="M">Somente Melodia</option>
                  <option value="L">Somente Letra sincronizada</option>
                  <option value="RLM">Melodia e Letra sincronizada</option>
                </select>
              </td>
              <td>
                <select v-model="p.tipo_ids[0]" class="prod-select" @change="saveProduct(p, 'tipo')" :disabled="p.isSaving">
                  <option value="">— Nenhum —</option>
                  <option v-for="t in taxonomies.tipo" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
              </td>
              <td>
                <input type="number" step="0.01" v-model="p.preco" class="prod-input" style="width: 80px;" @change="saveProduct(p, '_regular_price')" :disabled="p.isSaving">
              </td>
              <td>
                <select v-model="p.genero_ids[0]" class="prod-select" @change="saveProduct(p, 'genero_musical')" :disabled="p.isSaving">
                  <option value="">— Nenhum —</option>
                  <option v-for="t in taxonomies.genero_musical" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
              </td>
              <td>
                <select v-model="p.mes_ids[0]" class="prod-select" @change="saveProduct(p, 'mes_de_lancamento')" :disabled="p.isSaving">
                  <option value="">— Nenhum —</option>
                  <option v-for="t in taxonomies.mes_de_lancamento" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
              </td>
              <td>
                <a v-if="p.url_demo" href="#" class="demo-link" @click.prevent="openEditModal(p, 'url_demo')" :class="{ 'is-disabled': p.isSaving }">{{ p.url_demo }}</a>
                <span v-else style="color: #6b7280; cursor: pointer; font-size: 11px;" @click="openEditModal(p, 'url_demo')" :class="{ 'is-disabled': p.isSaving }">Sem demo</span>
              </td>
              <td>
                <div style="display: flex; gap: 6px; align-items: center;">
                  <button v-if="p.url_demo" class="btn-play" :class="{playing: currentPlayingId === p.id}" @click="togglePlay(p)">
                    {{ currentPlayingId === p.id ? '⏸' : '▶' }}
                  </button>
                  <a :href="p.edit_url" target="_blank" class="button button-sm" title="Editar no painel do WordPress">✏️ WP</a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="products.length" class="prod-pagination" style="margin-top:8px; display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <button @click="loadProducts(1)" class="button" :disabled="currentPage <= 1">««</button>
        <button @click="loadProducts(currentPage - 1)" class="button" :disabled="currentPage <= 1">« Anterior</button>
        <span>Página {{ currentPage }} de {{ totalPages }}</span>
        <button @click="loadProducts(currentPage + 1)" class="button" :disabled="currentPage >= totalPages">Próxima »</button>
        <button @click="loadProducts(totalPages)" class="button" :disabled="currentPage >= totalPages">»»</button>
        
        <label style="margin-left:auto">Por página:
          <select v-model="limit" @change="loadProducts(1)" id="prod-limit">
            <option v-for="n in [5, 10, 15, 20, 50, 100]" :key="n" :value="n">{{ n }}</option>
          </select>
        </label>
      </div>
      </div>

      <!-- CONTEÚDO DA ABA 2: CONCILIAÇÃO DE DEMOS & ÓRFÃOS -->
      <div v-show="activeTab === 'demos'" class="demo-audit-wrapper">
        <div class="audit-top-bar">
          <div class="audit-intro">
            <h3 style="margin: 0 0 6px 0; font-size: 18px; font-weight: 700;">🎧 Auditoria e Conciliação de Arquivos de Demonstração</h3>
            <p style="margin: 0; color: #6b7280; font-size: 13.5px;">Varre a pasta física <code>/demos/</code> no disco e cruza com os caminhos registrados na tabela <code>wp_centralmidi_midis</code>.</p>
          </div>
          <button type="button" @click="runDemoAudit" class="button primary" :disabled="isAuditingDemos" style="height: 38px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
            {{ isAuditingDemos ? '⏳ Escaneando Pasta /demos/... Aguarde' : '🔍 Iniciar Auditoria Completa' }}
          </button>
        </div>

        <!-- Cards de Métricas da Auditoria -->
        <div v-if="demoAudit" class="audit-summary-grid">
          <div class="audit-card">
            <div class="audit-card-val" style="color: #0284c7;">{{ demoAudit.summary.total_disk_files }}</div>
            <div class="audit-card-label">Arquivos no Disco</div>
          </div>
          <div class="audit-card">
            <div class="audit-card-val" style="color: #10b981;">{{ demoAudit.summary.matched_exact }}</div>
            <div class="audit-card-label">Vínculos 100% OK</div>
          </div>
          <div class="audit-card" :class="{ 'card-warn': demoAudit.summary.fixable_count > 0 }">
            <div class="audit-card-val" style="color: #f59e0b;">{{ demoAudit.summary.fixable_count }}</div>
            <div class="audit-card-label">Vínculos Corrigíveis</div>
          </div>
          <div class="audit-card" :class="{ 'card-danger': demoAudit.summary.missing_count > 0 }">
            <div class="audit-card-val" style="color: #ef4444;">{{ demoAudit.summary.missing_count }}</div>
            <div class="audit-card-label">Sem Arquivo no Disco</div>
          </div>
          <div class="audit-card" :class="{ 'card-purple': demoAudit.summary.orphans_count > 0 }">
            <div class="audit-card-val" style="color: #8b5cf6;">{{ demoAudit.summary.orphans_count }}</div>
            <div class="audit-card-label">Arquivos Órfãos no Disco</div>
          </div>
        </div>

        <!-- Seção 1: Vínculos Corrigíveis -->
        <div v-if="demoAudit && demoAudit.fixable && demoAudit.fixable.length" class="audit-section-box">
          <div class="section-box-header">
            <div>
              <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 700; color: #d97706;">⚠️ Vínculos com Divergência Identificada ({{ demoAudit.fixable.length }} itens)</h4>
              <p style="margin: 0; color: #6b7280; font-size: 13px;">O arquivo físico existe no disco mas o caminho no banco tem diferenças de URL-encode (<code>%5B</code>), HTML Entities (<code>&#039;</code>) ou subpastas.</p>
            </div>
            <button type="button" @click="applyDemoFixes" class="button primary" :disabled="isApplyingFixes" style="font-weight: 600;">
              {{ isApplyingFixes ? '⏳ Aplicando Correções...' : '⚡ Corrigir Todos os ' + demoAudit.fixable.length + ' Vínculos Automaticamente' }}
            </button>
          </div>

          <div class="audit-table-scroll">
            <table class="results-table">
              <thead>
                <tr>
                  <th style="width: 70px;">ID</th>
                  <th>Produto</th>
                  <th>Caminho Atual no Banco</th>
                  <th>Caminho Real no Disco (Sugerido)</th>
                  <th style="width: 90px;">Tipo</th>
                  <th style="width: 120px; text-align: center;">Ação</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(f, idx) in demoAudit.fixable.slice(0, 100)" :key="f.id">
                  <td style="font-size: 11px;">{{ f.id }}</td>
                  <td><strong>{{ f.title }}</strong></td>
                  <td><code class="code-danger">{{ f.current }}</code></td>
                  <td><code class="code-success">{{ f.suggested }}</code></td>
                  <td><span class="badge-pill" :class="f.match_type">{{ f.match_type === 'decode' ? 'URL/HTML' : 'Fuzzy' }}</span></td>
                  <td style="text-align: center;">
                    <button type="button" class="button primary button-sm" @click="applySingleFix(f, idx)" :disabled="f.isFixing">
                      {{ f.isFixing ? '⏳ Corrigindo...' : '✔️ Corrigir' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-if="demoAudit.fixable.length > 100" class="table-more-hint">
              Mostrando os primeiros 100 itens de {{ demoAudit.fixable.length }}. Você pode corrigir individualmente cada linha ou usar o botão superior para corrigir todos em lote.
            </p>
          </div>
        </div>

        <!-- Seção 2: Arquivos Órfãos no Disco (Sem Produto) -->
        <div v-if="demoAudit && demoAudit.orphans && demoAudit.orphans.length" class="audit-section-box">
          <div class="section-box-header">
            <div>
              <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 700; color: #7c3aed;">📁 Arquivos Órfãos na Pasta <code>/demos/</code> ({{ demoAudit.orphans.length }} arquivos)</h4>
              <p style="margin: 0; color: #6b7280; font-size: 13px;">Estes arquivos MP3 existem fisicamente no servidor mas nenhum produto aponta para eles. Você pode ouvi-los e criar o produto MIDI correspondente com 1 clique.</p>
            </div>
            <button type="button" @click="exportOrphansCSV" class="button">📥 Exportar Lista de Órfãos (CSV)</button>
          </div>

          <div class="audit-table-scroll">
            <table class="results-table">
              <thead>
                <tr>
                  <th style="width: 40px;">Áudio</th>
                  <th style="width: 180px;">Arquivo Físico</th>
                  <th style="width: 180px;">Artista</th>
                  <th style="min-width: 180px;">Música</th>
                  <th style="width: 140px;">Gênero</th>
                  <th style="width: 90px;">Classif.</th>
                  <th style="width: 120px;">Mês / Lançamento</th>
                  <th style="width: 75px;">Preço</th>
                  <th style="width: 80px; text-align: center;">Publicado</th>
                  <th style="width: 110px;">Ação</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(o, idx) in demoAudit.orphans.slice(0, 100)" :key="o.path">
                  <td>
                    <button type="button" @click="togglePlayPath(o.url)" class="btn-play" :class="{ playing: currentPlayingPath === o.url }">
                      {{ currentPlayingPath === o.url ? '⏸' : '▶' }}
                    </button>
                  </td>
                  <td>
                    <code style="font-size: 11px; word-break: break-all;">{{ o.path }}</code>
                  </td>
                  <td>
                    <select v-model="o.artist_id" class="prod-select" :disabled="o.isCreating" style="max-width: 190px;">
                      <option value="">— Selecione o Artista —</option>
                      <option v-for="a in taxonomies.product_cat" :key="a.id" :value="a.id">{{ a.name }}</option>
                    </select>
                  </td>
                  <td>
                    <input type="text" class="prod-input" v-model="o.deduced_title" placeholder="Título da Música" :disabled="o.isCreating">
                  </td>
                  <td>
                    <select v-model="o.genre_id" class="prod-select" :disabled="o.isCreating">
                      <option value="">— Selecione —</option>
                      <option v-for="g in taxonomies.genero_musical" :key="g.id" :value="g.id">{{ g.name }}</option>
                    </select>
                  </td>
                  <td>
                    <select v-model="o.rlm" class="prod-select" :disabled="o.isCreating">
                      <option value="M">M (Melodia)</option>
                      <option value="L">L (Letra)</option>
                      <option value="RLM">RLM (Ambos)</option>
                    </select>
                  </td>
                  <td>
                    <select v-model="o.mes_id" class="prod-select" :disabled="o.isCreating" style="max-width: 130px;">
                      <option value="">— Mês / Ano —</option>
                      <option v-for="m in allAvailableMonths" :key="m.value" :value="m.value">{{ m.label }}</option>
                    </select>
                  </td>
                  <td>
                    <input type="number" step="0.50" min="0" class="prod-input" v-model="o.price" style="width: 70px;" :disabled="o.isCreating">
                  </td>
                  <td style="text-align: center;">
                    <label style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; margin: 0;">
                      <input type="checkbox" v-model="o.publicado" :disabled="o.isCreating" style="margin: 0; width: 16px; height: 16px; cursor: pointer;">
                    </label>
                  </td>
                  <td>
                    <button type="button" class="button primary button-sm" @click="createProductFromOrphan(o, idx)" :disabled="o.isCreating">
                      {{ o.isCreating ? '⏳ Criando...' : '➕ Criar' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-if="demoAudit.orphans.length > 100" class="table-more-hint">
              Mostrando os primeiros 100 arquivos órfãos de {{ demoAudit.orphans.length }}.
            </p>
          </div>
        </div>

        <!-- Seção 3: Demos Faltantes no Disco -->
        <div v-if="demoAudit && demoAudit.missing && demoAudit.missing.length" class="audit-section-box">
          <div class="section-box-header">
            <div>
              <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 700; color: #dc2626;">❌ Produtos sem Arquivo Físico no Disco ({{ demoAudit.missing.length }} itens)</h4>
              <p style="margin: 0; color: #6b7280; font-size: 13px;">Estes produtos têm demo cadastrado no banco, mas o arquivo MP3 não foi encontrado na pasta <code>/demos/</code>.</p>
            </div>
            <button type="button" @click="exportMissingCSV" class="button">📥 Exportar Faltantes (CSV)</button>
          </div>

          <div class="audit-table-scroll">
            <table class="results-table">
              <thead>
                <tr>
                  <th style="width: 60px;">ID</th>
                  <th style="width: 70px;">Prod ID</th>
                  <th>Título do Produto</th>
                  <th>Caminho Registrado</th>
                  <th style="width: 140px;">Motivo</th>
                  <th style="width: 160px; text-align: center;">Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(m, idx) in demoAudit.missing.slice(0, 50)" :key="m.id">
                  <td>{{ m.id }}</td>
                  <td>
                    <a :href="getEditPostUrl(m.product_id)" target="_blank" title="Abrir produto no WordPress" style="color: #2563eb; font-weight: 600; text-decoration: underline;">
                      {{ m.product_id }} ↗
                    </a>
                  </td>
                  <td><strong>{{ m.title }}</strong></td>
                  <td>
                    <code style="word-break: break-all; font-size: 11px;">{{ m.demo_audio }}</code>
                  </td>
                  <td><span class="badge-pill danger">{{ m.reason }}</span></td>
                  <td style="text-align: center;">
                    <div style="display: inline-flex; gap: 4px; align-items: center; justify-content: center;">
                      <a :href="getEditPostUrl(m.product_id)" target="_blank" class="button button-sm" title="Editar produto no painel WordPress em nova guia">
                        ✏️ Editar ↗
                      </a>
                      <label class="button primary button-sm" :class="{ 'is-disabled': m.isUploading }" style="margin: 0; cursor: pointer;" title="Fazer upload do arquivo MP3 deste demo">
                        <span v-if="m.isUploading">⏳ Enviando...</span>
                        <span v-else>📤 Upload MP3</span>
                        <input type="file" accept=".mp3,.mid,.midi,audio/mpeg" @change="uploadMissingDemoFile($event, m, idx)" style="display: none;" :disabled="m.isUploading">
                      </label>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- Modal de Edição -->
      <div v-if="editingProduct" class="modal-overlay" @click.self="closeEditModal">
        <div class="modal-content">
          <div class="modal-header">
            <h3>{{ editModalTitle }}</h3>
            <button @click="closeEditModal" class="modal-close">&times;</button>
          </div>
          <div class="modal-body">
            <label>{{ editModalLabel }}</label>
            <textarea v-model="editValue" class="prod-input" rows="3" ref="modalInput" style="min-height: 100px; font-size: 14px;"></textarea>
          </div>
          <div class="modal-footer">
            <button @click="closeEditModal" class="button">Cancelar</button>
            <button @click="saveEditModal" class="button primary" :disabled="isSavingModal">
              {{ isSavingModal ? 'Salvando...' : 'Salvar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<style>
[v-cloak] { display: none; }
/* ... restante do style continua igual ... */
body { background-color: #f9fafb; color: #111827; }
.admin-tools-page { padding: 20px 10px; max-width: 98vw; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }

.admin-header { display: flex; flex-direction: column; gap: 4px; margin-bottom: 4px; }
.admin-header-top { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.admin-header .stats-grid { display: flex; gap: 4px; }
.admin-header .stat-card { min-width: 80px; padding: 4px 8px; border-radius: 4px; text-align: center; background: #f9fafb; border: 1px solid #e5e7eb; }
.admin-header .stat-number { font-size: 16px; font-weight: 700; color: #69A24C; line-height: 1.1; }
.admin-header .stat-label { font-size: 8px; color: #6b7280; text-transform: uppercase; margin-top: 1px; white-space: nowrap; }
.admin-header .actions-row { display: flex; gap: 4px; }
.admin-header .admin-controls-flex { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-top: 4px; }
.admin-header .prod-filters-row { display: flex; gap: 4px; align-items: center; flex-shrink: 0; }
.admin-header .prod-search-row { display: flex; gap: 8px; align-items: center; margin-top: 2px; }

.search-group { display: flex; align-items: center; background: #fff; border-bottom: 2px solid #e5e7eb; height: 32px; transition: all 0.2s; }
.search-group:focus-within { border-color: #69A24C; background: #fff; }
.search-group.grow { flex: 1; }
.search-group.small { width: 80px; }
.search-icon { padding: 0 8px; font-size: 14px; opacity: 0.6; display: flex; align-items: center; }
.search-input { border: none !important; background: transparent !important; padding: 0 !important; font-size: 13px !important; width: 100%; height: 100%; box-shadow: none !important; color: #111827 !important; line-height: 32px !important; display: block !important; margin-bottom: 0 !important; }
.search-input::placeholder { color: #9ca3af; }
.search-input:focus { outline: none !important; }

.load-btn { height: 28px; padding: 0 12px; gap: 8px; }
.total-badge { background: rgba(255,255,255,0.2); padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: 600; }

.admin-header .filtro-select { padding: 2px 6px; font-size: 11px; min-width: 0; width: auto; max-width: 120px; border: 1px solid #d1d5db; border-radius: 3px; background: #fff; color: #111827; height: 26px; }
.admin-header .filtro-select:focus { outline: none; border-color: #69A24C; box-shadow: 0 0 0 2px rgba(105, 162, 76, 0.2); }
.v-divider { width: 1px; height: 20px; background: #e5e7eb; margin: 0 4px; }

.button { display: inline-flex; align-items: center; justify-content: center; height: 26px; padding: 0 10px; font-size: 11px; font-weight: 500; border-radius: 4px; border: 1px solid #d1d5db; background: #fff; color: #374151; cursor: pointer; line-height: 1; transition: all 0.15s; }
.button:hover { background: #f3f4f6; }
.button.primary { background: #69A24C; color: #fff; border-color: #69A24C; }
.button.primary:hover { background: #5a8a41; }
.button-sm { height: 22px; padding: 2px 8px; font-size: 10px; }
.button:disabled { opacity: 0.5; pointer-events: none; }
.result-message { padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; display:none; font-weight: 500; font-size: 13px; }
.result-message.success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.result-message.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.result-message.info { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }

.results-table-wrap { overflow-x: auto; background: #ffffff; border-top: 1px solid #e5e7eb; }
.results-table { width: 100%; border-collapse: collapse; }
.results-table th, .results-table td { padding: 4px 4px; text-align: left; border-bottom: 1px solid #e5e7eb; font-size: 12px; white-space: nowrap; color: #374151; }
.results-table th { background: #f9fafb; color: #111827; position: sticky; top: 0; font-weight: 600; text-transform: uppercase; font-size: 11px; z-index: 10; border-bottom: 2px solid #e5e7eb; padding: 6px 4px; }
.results-table tr:hover { background-color: #f9fafb; }
.results-table a { color: #2563eb; text-decoration: none; font-weight: 500; }
.results-table a:hover { text-decoration: underline; }
.button.loading { position: relative; pointer-events: none; opacity: 0.7; }
.product-tools-bar { display: flex; gap: 8px; align-items: center; margin-bottom: 10px; flex-wrap: wrap; }

.prod-select { width: 100%; min-width: 90px; padding: 4px 6px; border: 1px solid #d1d5db; border-radius: 4px; background: #ffffff; color: #111827; font-size: 11px; }
.prod-input { width: 100%; min-width: 60px; padding: 2px 6px; border: 1px solid #d1d5db; border-radius: 4px; background: #ffffff; color: #111827; font-size: 11px; transition: all 0.2s; height: 24px; }
.prod-input:focus, .prod-select:focus { outline: none; border-color: #69A24C; box-shadow: 0 0 0 2px rgba(105, 162, 76, 0.2); }

.artista-picker { position: relative; }
.artista-picker .prod-input { padding-right: 20px; }
.artista-limpar { position: absolute; right: 3px; top: 4px; width: 16px; height: 16px; line-height: 1; border: 0; padding: 0; background: #e5e7eb; color: #6b7280; border-radius: 50%; cursor: pointer; font-size: 12px; }
.artista-limpar:hover { background: #d1d5db; color: #374151; }
.artista-opcoes { position: absolute; z-index: 60; left: 0; right: 0; top: 100%; margin: 0; padding: 0; list-style: none; background: #ffffff; border: 1px solid #d1d5db; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,.15); max-height: 220px; overflow-y: auto; }
.artista-opcoes li { padding: 4px 6px; font-size: 11px; cursor: pointer; white-space: nowrap; color: #111827; }
.artista-opcoes li:hover { background: #f3f4f6; }
.artista-opcoes em { color: #9ca3af; font-style: normal; }
#prod-search { padding: 2px 8px; border: 1px solid #d1d5db; border-radius: 4px; background: #ffffff; color: #111827; width: 220px; font-size: 11px; height: 26px; }
#prod-search-id { padding: 2px 8px; border: 1px solid #d1d5db; border-radius: 4px; background: #ffffff; color: #111827; width: 60px; font-size: 11px; height: 26px; }
#prod-search:focus, #prod-search-id:focus { outline: none; border-color: #69A24C; box-shadow: 0 0 0 2px rgba(105, 162, 76, 0.2); }
#prod-limit { padding: 2px 6px; border: 1px solid #d1d5db; border-radius: 4px; background: #ffffff; color: #111827; font-size: 11px; font-weight: 500; height: 26px; }

.col-id { width: 40px; }
.col-artista { width: 150px; }
.results-table td.col-musica, .results-table th.col-musica { max-width: 300px; width: 300px; white-space: normal; word-break: break-word; overflow-wrap: break-word; }
.col-rlm { width: 120px; }
.col-tipo { width: 110px; }
.col-preco { width: 90px; }
.col-genero { width: 120px; }
.col-mes { width: 50px; }
.col-demo { min-width: 220px; }
.col-actions { width: 120px; }
.demo-link { color: #2563eb; font-size: 12px; text-decoration: underline; word-break: break-all; white-space: normal; line-height: 1.4; display: block; }
.btn-play { background: #2563eb; color: #fff; border: none; border-radius: 4px; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 10px; cursor: pointer; transition: all 0.15s; }
.btn-play:hover { background: #1d4ed8; transform: scale(1.05); }
.btn-play.playing { background: #dc2626; }
.btn-play.playing:hover { background: #b91c1c; }
.btn-play.loading { opacity: 0.5; pointer-events: none; }
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; }
.modal-content { background: #ffffff; border-radius: 12px; padding: 32px; min-width: 900px; max-width: 95vw; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #e5e7eb; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px; }
.modal-header h3 { color: #111827; margin: 0; font-size: 18px; font-weight: 600; }
.modal-close { background: none; border: none; color: #9ca3af; font-size: 28px; cursor: pointer; padding: 0; line-height: 1; transition: color 0.2s; }
.modal-close:hover { color: #111827; }
.modal-body { margin-bottom: 24px; }
.modal-body label { display: block; color: #4b5563; margin-bottom: 8px; font-size: 14px; font-weight: 500; }
.modal-footer { display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid #e5e7eb; padding-top: 20px; }
#modal-field-input { width: 100%; padding: 12px 16px; font-size: 15px; box-sizing: border-box; resize: vertical; min-height: 80px; font-family: inherit; line-height: 1.5; border: 1px solid #d1d5db; border-radius: 8px; color: #111827; background: #f9fafb; transition: all 0.2s; }
#modal-field-input:focus { outline: none; border-color: #69A24C; background: #ffffff; box-shadow: 0 0 0 3px rgba(105, 162, 76, 0.15); }
#prod-info { color: #4b5563; margin-left: 12px; font-weight: 500; font-size: 14px; }
#prod-page-info { color: #4b5563; font-weight: 500; font-size: 14px; }
label { color: #4b5563; font-weight: 500; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; }

/* Toast Notification */
.admin-toast { position: fixed; top: 35px; right: 20px; padding: 12px 24px; border-radius: 8px; color: #fff; font-weight: 600; z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-size: 14px; }
.admin-toast.success { background: #10b981; }
.admin-toast.error { background: #ef4444; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter, .toast-leave-to { opacity: 0; transform: translateY(-20px); }

/* Table Loading Overlay */
.table-loading-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.7); z-index: 50; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; font-weight: 600; color: #374151; }
.spinner { width: 32px; height: 32px; border: 4px solid #f3f3f3; border-top: 4px solid #69A24C; border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

/* Save Spinner in Table */
.save-spinner { display: inline-block; width: 12px; height: 12px; border: 2px solid #e5e7eb; border-top: 2px solid #69A24C; border-radius: 50%; animation: spin 1s linear infinite; margin-left: 6px; vertical-align: middle; }
.is-disabled { opacity: 0.5; pointer-events: none; }
.results-table.is-loading { opacity: 0.5; pointer-events: none; }

/* Abas de Navegação */
.admin-tabs-nav { display: flex; gap: 8px; margin-bottom: 12px; border-bottom: 2px solid #e5e7eb; padding-bottom: 4px; }
.tab-nav-btn { background: #f3f4f6; border: 1px solid #d1d5db; border-bottom: none; border-radius: 6px 6px 0 0; padding: 8px 16px; font-size: 13px; font-weight: 600; color: #4b5563; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.tab-nav-btn:hover { background: #e5e7eb; color: #111827; }
.tab-nav-btn.is-active { background: #69A24C; color: #ffffff; border-color: #69A24C; }
.tab-alert-badge { background: #ef4444; color: #ffffff; font-size: 10px; font-weight: 800; border-radius: 50%; width: 16px; height: 16px; display: inline-flex; align-items: center; justify-content: center; }

/* Estilos da Aba de Conciliação de Demos */
.demo-audit-wrapper { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-top: 8px; }
.audit-top-bar { display: flex; justify-content: space-between; align-items: center; gap: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 20px; flex-wrap: wrap; }
.audit-summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 24px; }
.audit-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px 16px; text-align: center; }
.audit-card-val { font-size: 24px; font-weight: 800; line-height: 1.1; margin-bottom: 4px; }
.audit-card-label { font-size: 11.5px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em; }
.card-warn { background: #fffbeb; border-color: #fde68a; }
.card-danger { background: #fef2f2; border-color: #fecaca; }
.card-purple { background: #f5f3ff; border-color: #ddd6fe; }

.audit-section-box { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.section-box-header { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 14px; flex-wrap: wrap; }
.audit-table-scroll { max-height: 400px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 6px; }
.code-danger { color: #dc2626; background: #fee2e2; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
.code-success { color: #059669; background: #d1fae5; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
.badge-pill { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; }
.badge-pill.decode { background: #e0f2fe; color: #0369a1; }
.badge-pill.fuzzy { background: #fef3c7; color: #b45309; }
.badge-pill.danger { background: #fee2e2; color: #b91c1c; }
.table-more-hint { font-size: 12px; color: #6b7280; font-style: italic; margin: 8px 4px 4px 4px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script>
const ajaxUrl = '<?php echo $ajax_url; ?>';
const nonce = '<?php echo $nonce; ?>';
const adminPostUrl = '<?php echo admin_url("post.php"); ?>';

new Vue({
  el: '#app',
  data: {
    activeTab: 'produtos',
    demoAudit: null,
    isAuditingDemos: false,
    isApplyingFixes: false,
    currentPlayingPath: null,
    audioPath: null,
    stats: { total: 0, withDemo: 0 },
    products: [],
    toast: { show: false, message: '', type: 'success' },
    taxonomies: { product_cat: [], genero_musical: [], mes_de_lancamento: [], tipo: [] },
    filters: {
      filtro_artista: '',
      filtro_genero: '',
      filtro_mes: '',
      filtro_tipo: '',
      filtro_demo: 'all',
      filtro_demo_search: '',
      search: '',
      search_id: ''
    },
    limit: 10,
    currentPage: 1,
    totalPages: 1,
    totalProducts: 0,
    isLoading: false,
    isExporting: false,
    isCheckingDupes: false,
    editingProduct: null,
    editField: '',
    editValue: '',
    isSavingModal: false,
    currentPlayingId: null,
    audio: null
  },
  computed: {
    editModalTitle() {
      const labels = { 'post_title': 'Editar Música', 'url_demo': 'Editar URL Demo' };
      return labels[this.editField] || 'Editar';
    },
    editModalLabel() {
      const labels = { 'post_title': 'Música (título do produto)', 'url_demo': 'URL da demonstração' };
      return labels[this.editField] || this.editField;
    },
    allAvailableMonths() {
      const list = [];
      const seen = new Set();
      const siglas = { 1: 'JAN', 2: 'FEV', 3: 'MAR', 4: 'ABR', 5: 'MAI', 6: 'JUN', 7: 'JUL', 8: 'AGO', 9: 'SET', 10: 'OUT', 11: 'NOV', 12: 'DEZ' };

      // 1. Termos já existentes na taxonomia
      if (this.taxonomies.mes_de_lancamento && this.taxonomies.mes_de_lancamento.length) {
        this.taxonomies.mes_de_lancamento.forEach(m => {
          list.push({
            value: m.id,
            label: m.name,
            sortKey: m.name
          });
          seen.add(m.name.toUpperCase().trim());
        });
      }

      // 2. Garante anos presentes e futuros (2025 até 2028) para permitir lançamentos futuros
      const currentYear = new Date().getFullYear();
      for (let y = 2025; y <= currentYear + 2; y++) {
        for (let m = 1; m <= 12; m++) {
          const label = siglas[m] + ' ' + y;
          if (!seen.has(label)) {
            list.push({
              value: m + ':' + y,
              label: label + ' (novo)',
              sortKey: label
            });
            seen.add(label);
          }
        }
      }

      // 3. Ordenação cronológica reversa (meses futuros e recentes no topo)
      const parseSort = (lbl) => {
        const parts = lbl.replace('(novo)', '').trim().split(' ');
        if (parts.length >= 2) {
          const ano = parseInt(parts[1], 10) || 0;
          const sig = parts[0].toUpperCase();
          const mesMap = { JAN: 1, FEV: 2, MAR: 3, ABR: 4, MAI: 5, JUN: 6, JUL: 7, AGO: 8, SET: 9, OUT: 10, NOV: 11, DEZ: 12 };
          const mes = mesMap[sig] || 0;
          return (ano * 100) + mes;
        }
        return 0;
      };

      list.sort((a, b) => parseSort(b.label) - parseSort(a.label));
      return list;
    }
  },
  watch: {
    filters: {
      handler() { this.debouncedLoad(); },
      deep: true
    }
  },
  created() {
    this.debouncedLoad = this.debounce(() => this.loadProducts(1), 300);
    this.init();
  },
  methods: {
    async init() {
      await Promise.all([
        this.loadTaxonomy('product_cat'),
        this.loadTaxonomy('genero_musical'),
        this.loadTaxonomy('mes_de_lancamento'),
        this.loadTaxonomy('tipo'),
        this.loadStats()
      ]);
      this.loadProducts(1);
    },
    async callApi(action, extra = {}) {
      const formData = new FormData();
      formData.append('action', action);
      formData.append('_wpnonce', nonce);
      Object.keys(extra).forEach(k => formData.append(k, extra[k]));
      const response = await fetch(ajaxUrl, { method: 'POST', body: formData });
      return response.json();
    },
    async loadStats() {
      const data = await this.callApi('pa_admin_stats');
      if (data.success) this.stats = data.data.results.stats;
    },
    async loadTaxonomy(tax) {
      const data = await this.callApi('pa_admin_get_taxonomy_terms', { taxonomy: tax });
      if (data.success) this.taxonomies[tax] = data.data;
    },
    async loadProducts(page) {
      this.isLoading = true;
      this.currentPage = page;
      const params = {
        page: this.currentPage,
        limit: this.limit,
        ...this.filters
      };
      const data = await this.callApi('pa_admin_list_products', params);
      if (data.success) {
        const d = data.data;
        this.products = d.results.map(p => ({
          ...p,
          // ids vindos do servidor. Adivinhar pelo nome apagava o termo em
          // produtos com mais de um, como o 1146331.
          artista_id: (p.artista_ids && p.artista_ids.length) ? p.artista_ids[0] : '',
          artista_ids: p.artista_ids || [],
          artista_busca: (p.artista && p.artista.length) ? p.artista[0] : '',
          artista_opcoes: [],
          isSaving: false
        }));
        this.totalPages = d.total_pages;
        this.totalProducts = d.total;
      }
      this.isLoading = false;
    },
    async saveProduct(p, field) {
      p.isSaving = true;
      let value;
      // Escolher um valor preserva os termos que existem mas nao aparecem no
      // select; escolher "Nenhum" limpa de proposito. O escolhido vai para a
      // FRENTE porque o backend trata o primeiro termo como o principal
      // (meta _centralmidi_artista e coluna artista_id da tabela).
      const mesclar = (atuais, escolhido) => {
        if (escolhido === '' || escolhido === null || escolhido === undefined) return [];
        return [escolhido].concat((atuais || []).filter(id => String(id) !== String(escolhido)));
      };

      if (field === 'product_cat') value = mesclar(p.artista_ids, p.artista_id);
      else if (field === 'genero_musical') value = mesclar(p.genero_ids, p.genero_ids[0]);
      else if (field === 'mes_de_lancamento') value = mesclar(p.mes_ids, p.mes_ids[0]);
      else if (field === 'tipo') value = mesclar(p.tipo_ids, p.tipo_ids[0]);
      else value = p[field === '_regular_price' ? 'preco' : (field === 'post_title' ? 'title' : field)];

      try {
        const data = await this.callApi('pa_admin_save_product', {
          product_id: p.id,
          field: field,
          value: value
        });
        
        if (data && data.success) {
          await this.sincronizarLinha(p, field);
          this.showToast('Salvo com sucesso!');
          return true;
        } else {
          this.showToast('Erro ao salvar!', 'error');
          return false;
        }
      } catch (e) {
        this.showToast('Erro de conexão!', 'error');
        return false;
      } finally {
        p.isSaving = false;
      }
    },
    // Relê a linha gravada para a tela refletir exatamente o que o backend
    // gravou (ordem dos artistas, termo principal, ids das taxonomias).
    async sincronizarLinha(p, field) {
      let data;
      try {
        data = await this.callApi('pa_admin_list_products', { search_id: p.id, page: 1, limit: 1 });
      } catch (e) {
        return;
      }
      if (!data || !data.success) return;
      const row = (data.data && data.data.results && data.data.results[0]) || null;
      if (!row || parseInt(row.id, 10) !== parseInt(p.id, 10)) return;

      if (field === 'product_cat') {
        p.artista_ids = row.artista_ids || [];
        p.artista = row.artista || [];
        p.artista_id = p.artista_ids.length ? p.artista_ids[0] : '';
        p.artista_busca = (p.artista.length) ? p.artista[0] : '';
      } else if (field === 'genero_musical') {
        p.genero_ids = row.genero_ids || [];
      } else if (field === 'mes_de_lancamento') {
        p.mes_ids = row.mes_ids || [];
      } else if (field === 'tipo') {
        p.tipo_ids = row.tipo_ids || [];
      } else if (field === 'rlm') {
        p.rlm = row.rlm;
      }
    },
    buscarArtista(p) {
      clearTimeout(p._timerArtista);
      const termo = (p.artista_busca || '').trim();
      p.artista_opcoes = [];
      if (termo.length < 2) return;
      p._timerArtista = setTimeout(async () => {
        const data = await this.callApi('pa_admin_buscar_artistas', { termo });
        if (data && data.success) p.artista_opcoes = data.data;
      }, 300);
    },
    async escolherArtista(p, o) {
      p.artista_id = o.id;
      p.artista_busca = o.name;
      p.artista_opcoes = [];
      await this.saveProduct(p, 'product_cat');
    },
    async limparArtista(p) {
      p.artista_id = '';
      p.artista_busca = '';
      p.artista_opcoes = [];
      await this.saveProduct(p, 'product_cat');
    },
    showToast(message, type = 'success') {
      this.toast.message = message;
      this.toast.type = type;
      this.toast.show = true;
      setTimeout(() => { this.toast.show = false; }, 3000);
    },
    openEditModal(p, field) {
      this.editingProduct = p;
      this.editField = field;
      this.editValue = field === 'post_title' ? p.title : p.url_demo;
      this.$nextTick(() => {
        if (this.$refs.modalInput) {
          this.$refs.modalInput.focus();
          this.$refs.modalInput.select();
        }
      });
    },
    closeEditModal() {
      this.editingProduct = null;
    },
    async saveEditModal() {
      this.isSavingModal = true;
      const p = this.editingProduct;
      const originalValue = this.editField === 'post_title' ? p.title : p.url_demo;
      
      // Atualiza temporariamente o objeto para o saveProduct pegar o valor correto
      if (this.editField === 'post_title') p.title = this.editValue;
      else p.url_demo = this.editValue;

      const success = await this.saveProduct(p, this.editField === 'post_title' ? 'post_title' : 'url_demo');
      
      if (!success) {
        // Reverte se falhar
        if (this.editField === 'post_title') p.title = originalValue;
        else p.url_demo = originalValue;
        alert('Erro ao salvar. Verifique as permissões.');
      }

      this.isSavingModal = false;
      this.closeEditModal();
    },
    clearFilters() {
      Object.keys(this.filters).forEach(k => this.filters[k] = k === 'filtro_demo' ? 'all' : '');
      this.loadProducts(1);
    },
    async exportCSV() {
      this.isExporting = true;
      try {
        // Uma requisicao so: o PHP faz o streaming do CSV inteiro. Antes o
        // navegador batia 819 vezes para percorrer 81.852 produtos.
        const fd = new FormData();
        fd.append('action', 'pa_admin_export_csv');
        fd.append('_wpnonce', nonce);
        Object.keys(this.filters).forEach(k => {
          const v = this.filters[k];
          if (Array.isArray(v)) v.forEach(x => fd.append(k + '[]', x));
          else fd.append(k, v);
        });

        const resp = await fetch(ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const blob = await resp.blob();
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'centralmidi-produtos.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(a.href);
        this.showToast('CSV exportado!');
      } catch (e) {
        this.showToast('Erro ao exportar!', 'error');
      } finally {
        this.isExporting = false;
      }
    },
    async checkDuplicates() {
      this.isCheckingDupes = true;
      const data = await this.callApi('pa_admin_check_duplicates');
      if (data.success && data.data.length) {
        let csv = 'Tipo;Valor;ID;Produto;URL Demo\n';
        data.data.forEach(r => {
          csv += `${r.tipo};"${r.valor}";${r.id};"${r.nome}";"${r.url_demo}"\n`;
        });
        const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `duplicidades_${Date.now()}.csv`;
        a.click();
      } else {
        alert('Nenhuma duplicidade encontrada.');
      }
      this.isCheckingDupes = false;
    },
    togglePlay(p) {
      if (this.currentPlayingId === p.id) {
        this.audio.pause();
        this.currentPlayingId = null;
      } else {
        if (this.audio) this.audio.pause();
        this.audio = new Audio(p.url_demo);
        this.audio.play();
        this.currentPlayingId = p.id;
        this.audio.onended = () => { this.currentPlayingId = null; };
      }
    },
    togglePlayPath(url) {
      if (this.currentPlayingPath === url) {
        if (this.audioPath) this.audioPath.pause();
        this.currentPlayingPath = null;
      } else {
        if (this.audioPath) this.audioPath.pause();
        this.audioPath = new Audio(url);
        this.audioPath.play();
        this.currentPlayingPath = url;
        this.audioPath.onended = () => { this.currentPlayingPath = null; };
      }
    },
    buscarArtistaOrphan(orphan) {
      clearTimeout(orphan._timerArtista);
      const termo = (orphan.artist_busca || '').trim();
      orphan.artist_opcoes = [];
      if (termo.length < 2) return;
      orphan._timerArtista = setTimeout(async () => {
        const data = await this.callApi('pa_admin_buscar_artistas', { termo });
        if (data && data.success) {
          this.$set(orphan, 'artist_opcoes', data.data);
        }
      }, 300);
    },
    escolherArtistaOrphan(orphan, opt) {
      orphan.artist_id = opt.id;
      orphan.artist_busca = opt.name;
      orphan.artist_opcoes = [];
    },
    limparArtistaOrphan(orphan) {
      orphan.artist_id = '';
      orphan.artist_busca = '';
      orphan.artist_opcoes = [];
    },
    async runDemoAudit() {
      this.isAuditingDemos = true;
      try {
        const res = await this.callApi('cm_admin_audit_demos');
        if (res && res.success) {
          const audit = res.data;
          // Prepara campos reativos para cada órfão
          if (audit.orphans && audit.orphans.length) {
            audit.orphans.forEach(o => {
              o.artist_id = '';
              if (o.deduced_artist && this.taxonomies.product_cat) {
                const normDeduced = o.deduced_artist.toLowerCase().trim();
                const matched = this.taxonomies.product_cat.find(a => a.name.toLowerCase().trim() === normDeduced);
                if (matched) {
                  o.artist_id = matched.id;
                }
              }
              o.genre_id = '';

              // Dedução de RLM a partir do arquivo
              const fn = (o.filename || '').toUpperCase();
              if (fn.includes('RLM')) {
                o.rlm = 'RLM';
              } else if (fn.includes('_L.') || fn.endsWith('_L.MP3') || fn.includes('-L.') || fn.endsWith('-L.MP3')) {
                o.rlm = 'L';
              } else {
                o.rlm = 'M';
              }

              // Dedução do mês se estiver em subpasta tipo demos/052024/ ou demos/2024/
              o.mes_id = '';
              const matchMonth = o.path.match(/demos\/(\d{2})(\d{4})\//i);
              if (matchMonth) {
                const mesNum = parseInt(matchMonth[1], 10);
                const anoNum = parseInt(matchMonth[2], 10);
                // Procura na taxonomia existente primeiro
                let foundMes = null;
                if (this.taxonomies.mes_de_lancamento) {
                  foundMes = this.taxonomies.mes_de_lancamento.find(m => {
                    const mName = m.name.toLowerCase();
                    return mName.includes(anoNum.toString()) && (mName.startsWith(matchMonth[1]) || m.slug.includes(matchMonth[1] + anoNum));
                  });
                }
                if (foundMes) {
                  o.mes_id = foundMes.id;
                } else if (mesNum >= 1 && mesNum <= 12 && anoNum >= 2000) {
                  o.mes_id = mesNum + ':' + anoNum;
                }
              }

              o.publicado = true;
              o.price = '15.00';
              o.isCreating = false;
            });
          }
          this.demoAudit = audit;
          this.showToast('Auditoria de demos concluída com sucesso!');
        } else {
          this.showToast(res.data || 'Erro ao auditar demos', 'error');
        }
      } catch (e) {
        this.showToast('Erro de conexão ao auditar demos', 'error');
      } finally {
        this.isAuditingDemos = false;
      }
    },
    async applyDemoFixes() {
      if (!confirm(`Deseja aplicar automaticamente as correções nos ${this.demoAudit.fixable.length} vínculos identificados?`)) {
        return;
      }
      this.isApplyingFixes = true;
      try {
        const res = await this.callApi('cm_admin_apply_demo_fixes');
        if (res && res.success) {
          this.showToast(res.data.message || 'Correções aplicadas com sucesso!');
          // Re-executa auditoria para atualizar métricas
          await this.runDemoAudit();
          this.loadProducts(this.currentPage);
        } else {
          this.showToast(res.data || 'Erro ao aplicar correções', 'error');
        }
      } catch (e) {
        this.showToast('Erro de conexão ao aplicar correções', 'error');
      } finally {
        this.isApplyingFixes = false;
      }
    },
    async applySingleFix(item, index) {
      this.$set(item, 'isFixing', true);
      try {
        const res = await this.callApi('cm_admin_apply_demo_fixes', {
          id: item.id,
          suggested: item.suggested
        });
        if (res && res.success) {
          this.showToast('Vínculo corrigido!');
          // Remove da lista de corrigíveis e incrementa os OKs
          this.demoAudit.fixable.splice(index, 1);
          this.demoAudit.summary.fixable_count--;
          this.demoAudit.summary.matched_exact++;
        } else {
          this.showToast(res.data || 'Erro ao corrigir vínculo', 'error');
        }
      } catch (e) {
        this.showToast('Erro de conexão ao corrigir vínculo', 'error');
      } finally {
        this.$set(item, 'isFixing', false);
      }
    },
    async createProductFromOrphan(orphan, index) {
      if (!orphan.deduced_title || !orphan.deduced_title.trim()) {
        alert('Por favor, informe o título da música.');
        return;
      }
      this.$set(orphan, 'isCreating', true);
      try {
        const res = await this.callApi('cm_admin_create_product_from_orphan', {
          path: orphan.path,
          title: orphan.deduced_title,
          artist_id: orphan.artist_id || 0,
          genre_id: orphan.genre_id || 0,
          mes_id: orphan.mes_id || '',
          rlm: orphan.rlm || 'M',
          price: orphan.price || '15.00',
          publicado: orphan.publicado ? 1 : 0
        });
        if (res && res.success) {
          this.showToast(res.data.message || 'Produto criado!');
          // Atualiza lista de taxonomias de mês caso um novo tenha sido criado
          if (orphan.mes_id && typeof orphan.mes_id === 'string' && orphan.mes_id.includes(':')) {
            this.loadTaxonomy('mes_de_lancamento');
          }
          // Remove da lista de órfãos
          this.demoAudit.orphans.splice(index, 1);
          this.demoAudit.summary.orphans_count--;
          this.demoAudit.summary.matched_exact++;
          this.demoAudit.summary.total_midis++;
        } else {
          this.showToast(res.data || 'Erro ao criar produto', 'error');
        }
      } catch (e) {
        this.showToast('Erro de conexão ao criar produto', 'error');
      } finally {
        this.$set(orphan, 'isCreating', false);
      }
    },
    exportOrphansCSV() {
      if (!this.demoAudit || !this.demoAudit.orphans.length) return;
      let csv = 'Caminho;Arquivo;Artista Sugerido;Música Sugerida;URL\n';
      this.demoAudit.orphans.forEach(o => {
        csv += `"${o.path}";"${o.filename}";"${o.deduced_artist}";"${o.deduced_title}";"${o.url}"\n`;
      });
      const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = `demos_orfaos_${Date.now()}.csv`;
      a.click();
    },
    exportMissingCSV() {
      if (!this.demoAudit || !this.demoAudit.missing.length) return;
      let csv = 'ID MIDI;Product ID;Título;Caminho no Banco;Motivo\n';
      this.demoAudit.missing.forEach(m => {
        csv += `${m.id};${m.product_id};"${m.title}";"${m.demo_audio}";"${m.reason}"\n`;
      });
      const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = `demos_faltantes_no_disco_${Date.now()}.csv`;
      a.click();
    },
    getEditPostUrl(productId) {
      return `${adminPostUrl}?post=${productId}&action=edit`;
    },
    async uploadMissingDemoFile(event, item, index) {
      const file = event.target.files && event.target.files[0];
      if (!file) return;

      this.$set(item, 'isUploading', true);
      const formData = new FormData();
      formData.append('action', 'cm_admin_upload_missing_demo');
      formData.append('_wpnonce', nonce);
      formData.append('product_id', item.product_id);
      formData.append('target_path', item.demo_audio || '');
      formData.append('demo_file', file);

      try {
        const response = await fetch(ajaxUrl, {
          method: 'POST',
          body: formData
        });
        const res = await response.json();

        if (res && res.success) {
          this.showToast(res.data.message || 'Upload concluído com sucesso!');
          // Remove da lista de faltantes e incrementa os exatos
          this.demoAudit.missing.splice(index, 1);
          this.demoAudit.summary.missing_count--;
          this.demoAudit.summary.matched_exact++;
        } else {
          this.showToast(res.data || 'Erro ao enviar arquivo', 'error');
        }
      } catch (err) {
        this.showToast('Erro de conexão ao realizar upload', 'error');
      } finally {
        this.$set(item, 'isUploading', false);
        // Limpa o input file para permitir novo upload se necessário
        event.target.value = '';
      }
    },
    debounce(fn, ms) {
      let timer;
      return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), ms);
      };
    }
  }
});
</script>

<?php get_footer(); ?>
