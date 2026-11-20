 <template>
    <Head :title="`Reporting Balance Kixi Crédito Angola · ${tituloDireccao}`" />

    <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100/50">
        <div class="container mx-auto py-4 md:py-6 px-2 md:px-4 max-w-[1600px]">

            <!-- ═══════════════ HEADER MODERNO ═══════════════ -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0B2E4F] via-[#103E68] to-[#1E5A8E] shadow-xl mb-6">
                <div class="absolute inset-0 opacity-10"
                     style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px); background-size: 24px 24px;">
                </div>
                <div class="relative flex flex-col gap-4 p-5 md:p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="bg-white/10 backdrop-blur-sm p-3 rounded-2xl ring-1 ring-white/20">
                                <i class="fas fa-chart-pie text-2xl text-emerald-300"></i>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h1 class="text-xl md:text-2xl font-bold text-white tracking-tight">
                                        Reporting Balance · Kixi Crédito Angola
                                    </h1>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold uppercase tracking-wider bg-emerald-400/20 text-emerald-200 ring-1 ring-emerald-300/30">
                                        {{ tituloDireccao }}
                                    </span>
                                </div>
                                <p class="text-sm text-blue-100/80 mt-0.5 flex items-center gap-2">
                                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Relatório Diário Executivo · {{ ultimoMesNome }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <button @click="imprimirA4"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-medium transition-all backdrop-blur-sm">
                                <i class="fa fa-print text-blue-200"></i>
                                Imprimir Tabelas (A4)
                            </button>
                            <button @click="abrirDialogEmail"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white text-sm font-semibold transition-all shadow-lg shadow-emerald-500/30">
                                <i class="fa fa-paper-plane"></i>
                                Enviar por Email
                            </button>
                        </div>
                    </div>

                    <!-- ═══════════════ NAVEGAÇÃO POR DIRECÇÃO ═══════════════ -->
                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-white/15">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-blue-200/80 mr-1">
                            <i class="fas fa-layer-group mr-1"></i> Direcção:
                        </span>

                        <Link href="/rdcorp"
                              :class="direccaoActiva === 'GLOBAL'
                                  ? 'bg-white text-[#0B2E4F] shadow-md font-bold'
                                  : 'bg-white/10 text-white hover:bg-white/20 border border-white/15'"
                              class="px-3.5 py-1.5 rounded-xl text-xs transition-all flex items-center gap-1.5">
                            <i class="fas fa-globe-africa"></i>
                            Global (100%)
                        </Link>

                        <Link href="/reports/rd/dpp"
                              :class="direccaoActiva === 'DPP'
                                  ? 'bg-white text-[#0B2E4F] shadow-md font-bold'
                                  : 'bg-white/10 text-white hover:bg-white/20 border border-white/15'"
                              class="px-3.5 py-1.5 rounded-xl text-xs transition-all flex items-center gap-1.5">
                            <i class="fas fa-handshake"></i>
                            DPP (Particulares & Parcerias)
                        </Link>

                        <Link href="/reports/rd/dop"
                              :class="direccaoActiva === 'DOP'
                                  ? 'bg-white text-[#0B2E4F] shadow-md font-bold'
                                  : 'bg-white/10 text-white hover:bg-white/20 border border-white/15'"
                              class="px-3.5 py-1.5 rounded-xl text-xs transition-all flex items-center gap-1.5">
                            <i class="fas fa-sitemap"></i>
                            DOP (Operações)
                        </Link>

                        <Link href="/reports/rd/out"
                              :class="direccaoActiva === 'OUT'
                                  ? 'bg-white text-[#0B2E4F] shadow-md font-bold'
                                  : 'bg-white/10 text-white hover:bg-white/20 border border-white/15'"
                              class="px-3.5 py-1.5 rounded-xl text-xs transition-all flex items-center gap-1.5">
                            <i class="fas fa-folder-open"></i>
                            OUT (Outros)
                        </Link>
                    </div>
                </div>
            </div>

            <!-- ═══════════════ KPI CARDS ═══════════════ -->
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 md:gap-4 mb-6">
                <KpiCard
                    label="Carteira Total"
                    :value="formatarMoedaCompleta(ultimoRegistro?.BalancoValor)"
                    :delta="calcularDelta(ultimoRegistro?.BalancoValor, registroAnterior?.BalancoValor)"
                    icon="fa-wallet"
                    tone="blue"
                />
                <KpiCard
                    label="Desembolso"
                    :value="formatarMoedaCompleta(ultimoRegistro?.Desembolsos)"
                    :delta="calcularDelta(ultimoRegistro?.Desembolsos, registroAnterior?.Desembolsos)"
                    icon="fa-hand-holding-usd"
                    tone="emerald"
                />
                <KpiCard
                    label="Reembolso"
                    :value="formatarMoedaCompleta(ultimoRegistro?.Reembolso)"
                    :delta="calcularDelta(ultimoRegistro?.Reembolso, registroAnterior?.Reembolso)"
                    icon="fa-undo"
                    tone="rose"
                />
                <KpiCard
                    label="Créditos Novos"
                    :value="formatarInt(ultimoRegistro?.CreditosNovos)"
                    :delta="calcularDelta(ultimoRegistro?.CreditosNovos, registroAnterior?.CreditosNovos)"
                    icon="fa-file-invoice-dollar"
                    tone="amber"
                />
                <KpiCard
                    label="NPL"
                    :value="formatarPercentual(ultimoRegistro?.NPLPercentual)"
                    :delta="calcularDelta(ultimoRegistro?.NPLPercentual, registroAnterior?.NPLPercentual)"
                    icon="fa-exclamation-triangle"
                    tone="rose"
                    invert
                />
                <KpiCard
                    label="Taxa de Reembolso"
                    :value="formatarPercentual(ultimoRegistro?.TaxaReembolsoPercentual)"
                    :delta="calcularDelta(ultimoRegistro?.TaxaReembolsoPercentual, registroAnterior?.TaxaReembolsoPercentual)"
                    icon="fa-percentage"
                    tone="emerald"
                />
            </div>

            <!-- ═══════════════ GRÁFICOS DINÂMICOS ═══════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-chart-area text-blue-600"></i>
                            <h3 class="font-semibold text-slate-800">Evolução da Carteira</h3>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                            {{ historicoOrdenado.length }} Períodos (até {{ ultimoMesNome }})
                        </span>
                    </div>
                    <div class="h-56 relative">
                        <canvas ref="chartBalancoRef"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exchange-alt text-emerald-600"></i>
                            <h3 class="font-semibold text-slate-800">Desembolsos e Reembolsos</h3>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">12 meses</span>
                    </div>
                    <div class="h-56 relative">
                        <canvas ref="chartFluxoRef"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-shield-alt text-amber-600"></i>
                            <h3 class="font-semibold text-slate-800">Qualidade da Carteira (NPL · PAR 1 · PAR 30)</h3>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                            Até {{ ultimoMesNome }}
                        </span>
                    </div>
                    <div class="h-56 relative">
                        <canvas ref="chartQualidadeRef"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-building text-indigo-600"></i>
                            <h3 class="font-semibold text-slate-800">Distribuição da Carteira por Localidade Kixi Crédito</h3>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                            Top 8 · {{ ultimoMesNome }}
                        </span>
                    </div>
                    <div class="h-56 relative">
                        <canvas ref="chartAgenciasRef"></canvas>
                    </div>
                </div>
            </div>

            <!-- ═══════════════ ÁREA DE IMPRESSÃO (TABELAS) ═══════════════ -->
            <div ref="areaTabelas">
                <div class="hidden print:block text-center mb-4 pb-2 border-b border-slate-300">
                    <h1 class="text-base font-bold text-slate-800 uppercase tracking-wide">
                        RD Express {{ ultimoMesNome }} [KIXICREDITO ANGOLA · {{ tituloDireccao }}]
                    </h1>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-6 space-y-8">

                    <!-- ═══ TABELA 1: Reporting Global ═══ -->
                    <section>
                        <SectionTitle
                            icon="fa-globe-africa"
                            :title="`Reporting Consolidado — KIXICRÉDITO ANGOLA (${tituloDireccao})`"
                            subtitle="Histórico Mensal Consolidado até ao Último Período"
                            tone="blue"
                        />

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs border-collapse text-slate-800">
                                <thead class="sticky top-0 z-10">
                                    <tr class="text-center">
                                        <!-- Canto superior esquerdo invisível -->
                                        <th rowspan="2"
                                            class="w-36 bg-white border-0 px-3 py-1.5 align-bottom text-left text-[11px] uppercase tracking-wider">
                                        </th>
                                        <th :colspan="colunasMeses.length"
                                            class="bg-[#0B2E4F] text-white border-r border-b border-slate-300 py-1.5 text-[11px] uppercase tracking-wider rounded-tl-xl">
                                            Histórico Mensal
                                        </th>
                                        <th colspan="2" class="bg-[#0B2E4F] text-white border-r border-b border-slate-300 py-1.5 text-[11px] uppercase tracking-wider">
                                            Variação
                                        </th>
                                        <th rowspan="2" class="text-white border-b border-slate-300 py-1.5 px-3 text-[11px] uppercase tracking-wider bg-[#1E5A8E] rounded-tr-xl">
                                            {{ ultimoMesNome || 'Mês Atual' }}
                                        </th>
                                        <!-- Canto superior direito invisível -->
                                        <th rowspan="2" class="bg-white border-0 py-1.5 px-3 text-[11px] uppercase tracking-wider">
                                        </th>
                                    </tr>
                                    <tr class="bg-[#103E68] text-white text-center">
                                        <th v-for="mes in colunasMeses" :key="mes.key"
                                            class="border-r border-b border-slate-300 px-3 py-1 whitespace-nowrap text-[11px] font-semibold">
                                            {{ mes.label }}
                                        </th>
                                        <th class="border-r border-b border-slate-300 px-3 py-1 bg-emerald-500/20 text-emerald-200 text-[11px]">▲</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-1 bg-rose-500/20 text-rose-200 text-[11px]">▼</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 border-x border-b border-slate-200">
                                    <tr class="hover:bg-blue-50/40 transition-colors border-t border-slate-200">
                                        <td class="font-bold border-r border-slate-200 px-3 py-1.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-4 bg-blue-500 rounded-full"></span>
                                                1. Carteira
                                            </span>
                                        </td>
                                        <td v-for="item in historicoAteUltimoPeriodo" :key="item.Id || item.DataReferencia"
                                            class="text-right border-r border-slate-200 px-2 py-1 font-medium tabular-nums">
                                            {{ formatarNumero(item.BalancoValor) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.Desembolsos) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.Reembolso) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-amber-50 text-amber-800 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.BalancoValor) }}
                                        </td>
                                        <td class="text-right px-2 py-1 font-bold tabular-nums"
                                            :class="Number(ultimoRegistro?.VariacaoPercentual) >= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="Number(ultimoRegistro?.VariacaoPercentual) >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.VariacaoPercentual) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr class="hover:bg-blue-50/40 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-1.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-4 bg-indigo-500 rounded-full"></span>
                                                <span>2. Créditos | Novos<br>
                                                <span class="text-[9px] font-normal text-slate-500">Taxa Juros ≤ Kz 2.000</span></span>
                                            </span>
                                        </td>
                                        <td v-for="item in historicoAteUltimoPeriodo" :key="item.Id || item.DataReferencia"
                                            class="text-right border-r border-slate-200 px-2 py-1 font-medium tabular-nums leading-tight">
                                            <span class="text-slate-700">{{ formatarInt(item.CreditosQuantidade) }}</span>
                                            <span class="text-slate-400 mx-1">|</span>
                                            <span class="text-indigo-700 font-bold">{{ formatarInt(item.CreditosNovos) }}</span>
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarInt(ultimoRegistro?.CreditosNovos_Mais) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarInt(ultimoRegistro?.CreditosNovos_Menos) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 font-bold tabular-nums leading-tight">
                                            <span>{{ formatarInt(ultimoRegistro?.CreditosQuantidade) }}</span>
                                            <span class="text-slate-400 mx-1">|</span>
                                            <span class="text-indigo-700">{{ formatarInt(ultimoRegistro?.CreditosNovos) }}</span>
                                        </td>
                                        <td class="text-right px-2 py-1 font-bold tabular-nums"
                                            :class="Number(ultimoRegistro?.VariacaoCreditosNovos) >= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="Number(ultimoRegistro?.VariacaoCreditosNovos) >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.VariacaoCreditosNovos) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr class="hover:bg-blue-50/40 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-1.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-4 bg-cyan-500 rounded-full"></span>
                                                <span>3. Clientes | Novos<br>
                                                <span class="text-[9px] font-normal text-slate-500">≤ Kz 2.000</span></span>
                                            </span>
                                        </td>
                                        <td v-for="item in historicoAteUltimoPeriodo" :key="item.Id || item.DataReferencia"
                                            class="text-right border-r border-slate-200 px-2 py-1 font-medium tabular-nums leading-tight">
                                            <span>{{ formatarInt(item.ClientesQuantidade) }}</span>
                                            <span class="text-slate-400 mx-1">|</span>
                                            <span class="text-cyan-700 font-bold">{{ formatarInt(item.ClientesNovos) }}</span>
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarInt(ultimoRegistro?.ClientesNovos_Mais) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarInt(ultimoRegistro?.ClientesNovos_Menos) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 font-bold tabular-nums leading-tight">
                                            <span>{{ formatarInt(ultimoRegistro?.ClientesQuantidade) }}</span>
                                            <span class="text-slate-400 mx-1">|</span>
                                            <span class="text-cyan-700">{{ formatarInt(ultimoRegistro?.ClientesNovos) }}</span>
                                        </td>
                                        <td class="text-right px-2 py-1 font-bold tabular-nums"
                                            :class="Number(ultimoRegistro?.VariacaoClientesNovos) >= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="Number(ultimoRegistro?.VariacaoClientesNovos) >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.VariacaoClientesNovos) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr v-for="linha in linhasRisco" :key="linha.key" class="hover:bg-blue-50/40 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-1.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-4 rounded-full" :class="linha.cor"></span>
                                                {{ linha.numero }}. {{ linha.label }}
                                            </span>
                                        </td>
                                        <td v-for="item in historicoAteUltimoPeriodo" :key="item.Id || item.DataReferencia"
                                            class="text-right border-r border-slate-200 px-2 py-1 font-medium tabular-nums">
                                            <span class="font-bold" :class="linha.classePerc">{{ formatarPercentual(item[linha.percKey]) }}</span>
                                            <br>
                                            <span class="text-[9px] text-slate-500">{{ formatarK(item[linha.valKey]) }}</span>
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.[linha.maisKey]) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.[linha.menosKey]) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 font-bold tabular-nums">
                                            <span class="font-bold" :class="linha.classePerc">{{ formatarPercentual(ultimoRegistro?.[linha.percKey]) }}</span>
                                            <br>
                                            <span class="text-[9px] text-slate-500">{{ formatarK(ultimoRegistro?.[linha.valKey]) }}</span>
                                        </td>
                                        <td class="text-right px-2 py-1 font-bold tabular-nums"
                                            :class="Number(ultimoRegistro?.[linha.variacaoKey]) <= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="Number(ultimoRegistro?.[linha.variacaoKey]) <= 0 ? 'fas fa-arrow-down' : 'fas fa-arrow-up'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.[linha.variacaoKey]) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr class="bg-rose-50/40 hover:bg-rose-50/70 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-1.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-4 bg-rose-500 rounded-full"></span>
                                                7. Provisão
                                            </span>
                                        </td>
                                        <td v-for="item in historicoAteUltimoPeriodo" :key="item.Id || item.DataReferencia"
                                            class="text-right border-r border-slate-200 px-2 py-1 font-semibold text-rose-700 tabular-nums">
                                            {{ formatarNumero(item.ProvisaoValor) }}
                                            <br>
                                            <span class="text-[9px] text-slate-500">{{ formatarProvisaoCobertura(item.ProvisaoDiasAtraso, item.ProvisaoCoberturaPerc) }}</span>
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.Provisao_Mais) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.Provisao_Menos) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 font-bold text-rose-700 tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.ProvisaoValor) }}
                                            <br>
                                            <span class="text-[9px] text-slate-500">{{ formatarProvisaoCobertura(ultimoRegistro?.ProvisaoDiasAtraso, ultimoRegistro?.ProvisaoCoberturaPerc) }}</span>
                                        </td>
                                        <td class="text-right px-2 py-1 font-bold tabular-nums"
                                            :class="Number(ultimoRegistro?.VariacaoProvisao) <= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="Number(ultimoRegistro?.VariacaoProvisao) <= 0 ? 'fas fa-arrow-down' : 'fas fa-arrow-up'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.VariacaoProvisao) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr v-for="linha in linhasPercentuais" :key="linha.key" class="hover:bg-blue-50/40 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-1.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-4 rounded-full" :class="linha.cor"></span>
                                                {{ linha.numero }}. {{ linha.label }}
                                            </span>
                                        </td>
                                        <td v-for="item in historicoAteUltimoPeriodo" :key="item.Id || item.DataReferencia"
                                            class="text-right border-r border-slate-200 px-2 py-1 font-bold tabular-nums"
                                            :class="linha.classeFixa">
                                            {{ formatarPercentual(item[linha.key]) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.[linha.maisKey]) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.[linha.menosKey]) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-1 font-bold tabular-nums"
                                            :class="linha.classeFixa">
                                            {{ formatarPercentual(ultimoRegistro?.[linha.key]) }}
                                        </td>
                                        <td class="text-right px-2 py-1 font-bold tabular-nums"
                                            :class="linha.invertido
                                                ? (Number(ultimoRegistro?.[linha.variacaoKey]) <= 0 ? 'text-emerald-700' : 'text-rose-700')
                                                : (Number(ultimoRegistro?.[linha.variacaoKey]) >= 0 ? 'text-emerald-700' : 'text-rose-700')">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="(linha.invertido
                                                    ? Number(ultimoRegistro?.[linha.variacaoKey]) <= 0
                                                    : Number(ultimoRegistro?.[linha.variacaoKey]) >= 0)
                                                    ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.[linha.variacaoKey]) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ═══ TABELA 2: Agências ═══ -->
                    <section v-if="agenciasCapital.length > 0 || agenciasProvincia.length > 0">
                        <SectionTitle
                            icon="fa-wallet"
                            title="1. DISTRIBUIÇÃO DA CARTEIRA - KIXICRÉDITO ANGOLA"
                            subtitle="Performance detalhada por localidade"
                            tone="emerald"
                        />
                        <div class="space-y-4">
                            <AgenciasTable titulo="Capital (Luanda)" :agencias="agenciasCapital" />
                            <AgenciasTable titulo="Províncias" :agencias="agenciasProvincia" />
                        </div>
                    </section>

                    <!-- ═══ TABELA 3: Detalhamento por Produto ═══ -->
                    <section v-if="produtosAgrupados.length > 0">
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-[11px] border-collapse text-slate-800">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-[#0B2E4F] text-white">
                                        <th colspan="11" class="px-3 py-1.5 text-xs tracking-wider uppercase text-left">
                                            <i class="fas fa-user-tie mr-2 opacity-70"></i>
                                            Detalhamento por Produto
                                        </th>
                                    </tr>
                                    <tr class="bg-[#103E68] text-white text-center text-[10px]">
                                        <th class="border-r border-b border-slate-300 px-3 py-1 text-left w-56 uppercase tracking-wider">Produto</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider">OA/PA</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider">Carteira</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider">Créditos | Novos</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider">Clientes | Novos</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider">PAR 1</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider">PAR 30</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider">Desembolsos | TA</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider">Reembolsos</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider">Provisão</th>
                                        <th class="border-b border-slate-300 px-2 py-1 uppercase tracking-wider">Gestão</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template v-for="grupo in produtosAgrupados" :key="grupo.gestor">
                                        <tr class="bg-indigo-100/90 border-y border-indigo-300 font-bold text-indigo-950">
                                            <td class="border-r border-indigo-200 px-3 py-1.5 text-left">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-5 h-5 rounded-md bg-indigo-600 text-white flex items-center justify-center shadow-sm shrink-0">
                                                        <i class="fas fa-user-tie text-[9px]"></i>
                                                    </div>
                                                    <div class="leading-tight">
                                                        <div class="text-xs font-bold text-indigo-950 uppercase tracking-wide">
                                                            {{ grupo.gestor }}
                                                        </div>
                                                        <div class="text-[9px] text-indigo-700 font-medium">
                                                            {{ grupo.tipoDireccao }} · {{ grupo.itens.length }} {{ grupo.itens.length === 1 ? 'produto' : 'produtos' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-r border-indigo-200 px-2 py-1.5 text-center tabular-nums">
                                                {{ grupo.total.OA_Qtd }}/{{ grupo.total.PA_Qtd }}
                                            </td>
                                            <td class="border-r border-indigo-200 px-2 py-1.5 text-right tabular-nums">
                                                {{ formatarNumero(grupo.total.BalancoValor) }}
                                            </td>
                                            <td class="border-r border-indigo-200 px-2 py-1.5 text-right tabular-nums">
                                                <span>{{ formatarInt(grupo.total.CreditosTotal_Qtd) }}</span>
                                                <span class="text-indigo-400 mx-1">|</span>
                                                <span class="text-indigo-800">{{ formatarInt(grupo.total.CreditosNovos_Qtd) }}</span>
                                            </td>
                                            <td class="border-r border-indigo-200 px-2 py-1.5 text-right tabular-nums">
                                                <span>{{ formatarInt(grupo.total.ClientesTotal_Qtd) }}</span>
                                                <span class="text-indigo-400 mx-1">|</span>
                                                <span class="text-cyan-800">{{ formatarInt(grupo.total.ClientesNovos_Qtd) }}</span>
                                            </td>
                                            <td class="border-r border-indigo-200 px-2 py-1.5 text-right tabular-nums">
                                                {{ formatarK(grupo.total.PAR1_Valor) }}
                                                <span class="text-[9px] text-amber-800 font-semibold ml-0.5">({{ Math.round(grupo.total.PAR1_Percentual) }}%)</span>
                                            </td>
                                            <td class="border-r border-indigo-200 px-2 py-1.5 text-right tabular-nums">
                                                {{ formatarK(grupo.total.PAR30_Valor) }}
                                                <span class="text-[9px] text-rose-800 font-semibold ml-0.5">({{ Math.round(grupo.total.PAR30_Percentual) }}%)</span>
                                            </td>
                                            <td class="border-r border-indigo-200 px-2 py-1.5 text-right text-emerald-800 tabular-nums">
                                                {{ formatarNumero(grupo.total.DesembolsoValor) }}
                                                <span v-if="grupo.total.TempoAtendimentoMedia !== null" class="text-[9px] text-slate-600 ml-1">| {{ String(Math.round(grupo.total.TempoAtendimentoMedia)).padStart(2, '0') }}</span>
                                            </td>
                                            <td class="border-r border-indigo-200 px-2 py-1.5 text-right text-rose-800 tabular-nums">
                                                {{ formatarNumero(grupo.total.ReembolsoValor) }}
                                            </td>
                                            <td class="border-r border-indigo-200 px-2 py-1.5 text-right tabular-nums">
                                                {{ formatarNumero(grupo.total.ProvisaoValor) }}
                                            </td>
                                            <td class="px-2 py-1.5 text-center tabular-nums">
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-[10px]"
                                                      :class="obterCorGestao(grupo.total.GestaoMedia)">
                                                    {{ grupo.total.GestaoMedia !== null ? Math.round(grupo.total.GestaoMedia) + (grupo.total.GestaoSufixo || '') : '—' }}
                                                </span>
                                            </td>
                                        </tr>

                                        <tr v-for="prod in grupo.itens" :key="prod.Id" class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="border-r border-slate-200 px-3 py-1 text-left whitespace-nowrap pl-6 font-medium text-slate-700">
                                                <span class="inline-flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                                    {{ prod.NomeProduto }}
                                                </span>
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-1 text-center bg-slate-50/50 text-slate-600 tabular-nums">
                                                {{ prod.OA_Qtd ?? '—' }}/{{ prod.PA_Qtd ?? '—' }}
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-1 text-right font-semibold tabular-nums">{{ formatarNumero(prod.BalancoValor) }}</td>
                                            <td class="border-r border-slate-200 px-2 py-1 text-right tabular-nums">
                                                <span class="text-slate-700">{{ formatarInt(prod.CreditosTotal_Qtd) }}</span>
                                                <span class="text-slate-400 mx-1">|</span>
                                                <span class="text-indigo-700 font-bold">{{ formatarInt(prod.CreditosNovos_Qtd) }}</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-1 text-right tabular-nums">
                                                <span class="text-slate-700">{{ formatarInt(prod.ClientesTotal_Qtd) }}</span>
                                                <span class="text-slate-400 mx-1">|</span>
                                                <span class="text-cyan-700 font-bold">{{ formatarInt(prod.ClientesNovos_Qtd) }}</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-1 text-right tabular-nums">
                                                {{ formatarK(prod.PAR1_Valor) }}
                                                <span v-if="prod.PAR1_Percentual !== null" class="text-[9px] text-amber-700 font-semibold ml-0.5">({{ Math.round(prod.PAR1_Percentual) }}%)</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-1 text-right tabular-nums">
                                                {{ formatarK(prod.PAR30_Valor) }}
                                                <span v-if="prod.PAR30_Percentual !== null" class="text-[9px] text-rose-700 font-semibold ml-0.5">({{ Math.round(prod.PAR30_Percentual) }}%)</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-1 text-right bg-emerald-50/50 text-emerald-700 font-semibold tabular-nums">
                                                {{ formatarNumero(prod.DesembolsoValor) }}
                                                <span v-if="prod.TempoAtendimentodias !== null" class="text-[9px] text-slate-500 ml-1">| {{ String(prod.TempoAtendimentodias).padStart(2, '0') }}</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-1 text-right bg-rose-50/50 text-rose-700 tabular-nums">{{ formatarNumero(prod.ReembolsoValor) }}</td>
                                            <td class="border-r border-slate-200 px-2 py-1 text-right tabular-nums">
                                                {{ formatarNumero(prod.ProvisaoValor) }}
                                                <span v-if="prod.ProvisaoClasseRisco" class="font-bold text-slate-700 ml-1">({{ prod.ProvisaoClasseRisco }})</span>
                                            </td>
                                            <td class="px-2 py-1 text-center font-bold tabular-nums">
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-[10px]"
                                                      :class="obterCorGestao(prod.GestaoValor)">
                                                    {{ prod.GestaoValor !== null ? prod.GestaoValor + (prod.GestaoSufixo || '') : '—' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ═══ TABELA 4: MOVIMENTO DAS PRESTAÇÕES ═══ -->
                    <section v-if="movimentoPrestacaoRD.length > 0">
                        <SectionTitle
                            icon="fa-file-invoice-dollar"
                            title="2. MOVIMENTO DAS PRESTAÇÕES - KIXICRÉDITO ANGOLA"
                            subtitle="Detalhe das prestações Kixi Crédito"
                            tone="emerald"
                        />
                        <div class="overflow-x-auto rounded border border-slate-300">
                            <table class="w-full text-[10px] md:text-[11px] border-collapse text-slate-800">
                                <thead>
                                    <tr class="bg-[#103E68] text-white text-center font-bold">
                                        <th rowspan="2" class="border-r border-b border-slate-400 px-2 py-1 min-w-[85px] leading-tight">
                                            Periodo<br>Prestação
                                        </th>
                                        <th colspan="4" class="border-r border-b border-slate-400 py-1 uppercase tracking-wider bg-[#0E355A]">CAPITAL</th>
                                        <th colspan="4" class="border-r border-b border-slate-400 py-1 uppercase tracking-wider bg-[#0B2E4F]">JUROS + OUTROS</th>
                                        <th rowspan="2" class="border-r border-b border-slate-400 px-2 py-1 min-w-[95px] leading-tight">Total<br>Recuperado</th>
                                        <th rowspan="2" class="border-b border-slate-400 px-2 py-1 min-w-[70px] leading-tight">%Recup./<br>TxRecup.</th>
                                    </tr>
                                    <tr class="bg-[#103E68] text-white text-center font-semibold text-[9px] md:text-[10px]">
                                        <th class="border-r border-b border-slate-400 px-2 py-1">B.I.</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">Desembolsado</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">Reembolsado</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">B.F.</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">B.I.</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">Desembolsado</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">Reembolsado</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">B.F.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="mp in linhasMovimentoPrestacao" :key="mp.Id" :class="obterClasseLinhaMP(mp)">
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-center font-bold">{{ mp.PeriodoPrestacao }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(mp.CapIniValor) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(mp.CapDesValor) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(mp.CapReeValor) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(mp.CapFimValor) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(mp.JurIniValor) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(mp.JurDesValor) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(mp.JurReeValor) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(mp.JurFimValor) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums font-bold">{{ formatarNumero(mp.TotalRecuperado) }}</td>
                                        <td class="px-2 py-0.5 text-center tabular-nums font-bold">
                                            {{ Math.round(Number(mp.PctRecuperado ?? 0)) }}%/{{ Math.round(Number(mp.TxRecuperacao ?? 0)) }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ═══ TABELA 5: Desembolsos/Reembolsos 12 Meses (2 blocos de 6 meses + Coluna Total Acumulado) ═══ -->
                    <section v-if="desembolsosReembolsos12MesesRD.length > 0">
                        <SectionTitle
                            icon="fa-money-bill-wave"
                            title="3. DESEMBOLSOS E REEMBOLSOS (ÚLTIMOS 12 MESES)"
                            subtitle="Evolução mensal repartida em 2 blocos de 6 meses com destaque para pico máximo (verde), mínimo (vermelho) e total acumulado"
                            tone="emerald"
                        />

                        <div class="flex flex-col lg:flex-row rounded-xl border border-slate-200 overflow-hidden">
                            <!-- Esquerda: 2 blocos empilhados de 6 meses cada -->
                            <div class="flex-1">
                                <table class="w-full text-xs border-collapse text-slate-800">
                                    <tbody>
                                        <template v-for="(bloco, blocoIdx) in blocosFluxo6Meses" :key="blocoIdx">
                                            <tr class="bg-[#103E68] text-white text-center text-[11px]"
                                                :class="{ 'border-t-2 border-slate-400': blocoIdx > 0 }">
                                                <th class="border-r border-b border-slate-300 px-3 py-1.5 text-left w-36 uppercase tracking-wider">
                                                    Fluxo / Mês ({{ blocoIdx === 0 ? '1–6' : '7–12' }})
                                                </th>
                                                <th v-for="mes in bloco" :key="mes.Id"
                                                    class="border-r border-b border-slate-300 px-2 py-1.5 uppercase tracking-wider">
                                                    {{ mes.NomeDoMes }}
                                                </th>
                                                <th v-for="n in Math.max(0, 6 - bloco.length)" :key="'empty-h-' + n"
                                                    class="border-r border-b border-slate-300 px-2 py-1.5 bg-[#0B2E4F]/60">
                                                    —
                                                </th>
                                            </tr>

                                            <tr class="hover:bg-emerald-50/20 transition-colors">
                                                <td class="font-bold border-r border-b border-slate-200 px-3 py-1.5 text-left bg-emerald-50 text-emerald-800 whitespace-nowrap">
                                                    <i class="fas fa-arrow-down mr-2 text-emerald-600"></i>Desembolso (+)
                                                </td>
                                                <td v-for="mes in bloco" :key="mes.Id"
                                                    class="text-right border-r border-b border-slate-200 px-2 py-1.5 tabular-nums transition-all"
                                                    :class="obterClasseDestaqueDesembolso(mes)">
                                                    <div class="flex items-center justify-end gap-1">
                                                        <span v-if="Number(mes.DesembolsoValor) === maxMinFluxo.maxDesemb" class="text-[8px] bg-emerald-600 text-white px-1 rounded-sm uppercase tracking-tighter">▲ MAX</span>
                                                        <span v-else-if="Number(mes.DesembolsoValor) === maxMinFluxo.minDesemb" class="text-[8px] bg-rose-600 text-white px-1 rounded-sm uppercase tracking-tighter">▼ MIN</span>
                                                        <span>{{ formatarNumero(mes.DesembolsoValor) }}</span>
                                                    </div>
                                                </td>
                                                <td v-for="n in Math.max(0, 6 - bloco.length)" :key="'empty-d-' + n"
                                                    class="text-right border-r border-b border-slate-200 px-2 py-1.5 text-slate-400">
                                                    —
                                                </td>
                                            </tr>

                                            <tr class="hover:bg-rose-50/20 transition-colors">
                                                <td class="font-bold border-r border-slate-200 px-3 py-1.5 text-left bg-rose-50 text-rose-800 whitespace-nowrap"
                                                    :class="{ 'border-b': blocoIdx < blocosFluxo6Meses.length - 1 }">
                                                    <i class="fas fa-arrow-up mr-2 text-rose-600"></i>Reembolso (−)
                                                </td>
                                                <td v-for="mes in bloco" :key="mes.Id"
                                                    class="text-right border-r border-slate-200 px-2 py-1.5 tabular-nums transition-all"
                                                    :class="[obterClasseDestaqueReembolso(mes), { 'border-b': blocoIdx < blocosFluxo6Meses.length - 1 }]">
                                                    <div class="flex items-center justify-end gap-1">
                                                        <span v-if="Number(mes.ReembolsoValor) === maxMinFluxo.maxReemb" class="text-[8px] bg-emerald-600 text-white px-1 rounded-sm uppercase tracking-tighter">▲ MAX</span>
                                                        <span v-else-if="Number(mes.ReembolsoValor) === maxMinFluxo.minReemb" class="text-[8px] bg-rose-600 text-white px-1 rounded-sm uppercase tracking-tighter">▼ MIN</span>
                                                        <span>{{ formatarNumero(mes.ReembolsoValor) }}</span>
                                                    </div>
                                                </td>
                                                <td v-for="n in Math.max(0, 6 - bloco.length)" :key="'empty-r-' + n"
                                                    class="text-right border-r border-slate-200 px-2 py-1.5 text-slate-400"
                                                    :class="{ 'border-b': blocoIdx < blocosFluxo6Meses.length - 1 }">
                                                    —
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Direita: Coluna de Total Acumulado (12 Meses) alinhada -->
                            <div class="lg:w-48 shrink-0 flex flex-col border-t-2 lg:border-t-0 border-slate-300 bg-slate-50">
                                <div class="bg-[#0B2E4F] text-amber-200 px-3 py-1.5 text-center text-[11px] font-black uppercase tracking-wider border-b border-slate-300">
                                    Total Acumulado
                                    <span class="block text-[9px] font-normal text-blue-200">12 Meses</span>
                                </div>
                                <div class="flex-1 flex flex-col justify-center divide-y divide-slate-200">
                                    <div class="flex-1 flex flex-col justify-center px-3 py-2 bg-emerald-50/80 text-right">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                                            <i class="fas fa-arrow-down mr-1"></i>Total Desembolso
                                        </span>
                                        <span class="text-sm font-black text-emerald-950 tabular-nums mt-0.5">
                                            {{ formatarNumero(totaisAcumuladosFluxo.totalDesembolso) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-center px-3 py-2 bg-rose-50/80 text-right">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700">
                                            <i class="fas fa-arrow-up mr-1"></i>Total Reembolso
                                        </span>
                                        <span class="text-sm font-black text-rose-950 tabular-nums mt-0.5">
                                            {{ formatarNumero(totaisAcumuladosFluxo.totalReembolso) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ═══ TABELA 6: PROVISÕES ═══ -->
                    <section v-if="provisoesAgrupadas.length > 0">
                        <SectionTitle
                            icon="fa-chart-line"
                            title="4. PROVISÕES - KIXICRÉDITO ANGOLA"
                            subtitle="Evolução de provisões por agrupamento de produto"
                            tone="emerald"
                        />

                        <div class="overflow-x-auto rounded border border-slate-300">
                            <table class="w-full text-[10px] md:text-[11px] border-collapse text-slate-800">
                                <thead>
                                    <tr class="bg-[#103E68] text-white text-center font-bold">
                                        <th class="border-r border-b border-slate-400 px-2 py-1 text-left w-56">Agrupamento</th>
                                        <th class="border-r border-b border-slate-400 px-1 py-1 w-10">%</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">{{ labelFechoAA }}</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">{{ labelMesAnterior }}</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">{{ labelMesActual }}</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">Dif. Mensal</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1">Dif. Anual</th>
                                        <th class="border-b border-slate-400 px-1.5 py-1">Var. Ano</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-blue-50/70 font-bold border-b border-slate-300">
                                        <td class="border-r border-slate-300 px-2 py-1 text-[#0B2E4F] uppercase">KIXICRÉDITO ANGOLA</td>
                                        <td class="border-r border-slate-300 px-1 py-1 text-center text-blue-900 bg-blue-100/60">{{ totalGeralProvisoes.percDado }}%</td>
                                        <td class="border-r border-slate-300 px-2 py-1 text-right text-blue-900">{{ formatarNumero(totalGeralProvisoes.fechoAA) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-1 text-right text-blue-900">{{ formatarNumero(totalGeralProvisoes.mesAnterior) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-1 text-right text-blue-900 bg-blue-100/40">{{ formatarNumero(totalGeralProvisoes.mesActual) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-1 text-right" :class="totalGeralProvisoes.difMensal < 0 ? 'text-blue-600' : 'text-rose-600'">
                                            {{ formatarNumero(totalGeralProvisoes.difMensal) }}
                                        </td>
                                        <td class="border-r border-slate-300 px-2 py-1 text-right" :class="totalGeralProvisoes.difAnual < 0 ? 'text-blue-600' : 'text-rose-600'">
                                            {{ formatarNumero(totalGeralProvisoes.difAnual) }}
                                        </td>
                                        <td class="px-1.5 py-1 text-center" :class="totalGeralProvisoes.varAno < 0 ? 'text-blue-600' : 'text-rose-600'">
                                            {{ formatarPercentual(totalGeralProvisoes.varAno) }}
                                        </td>
                                    </tr>

                                    <template v-for="grupo in provisoesAgrupadas" :key="grupo.gestor">
                                        <tr class="bg-[#FEF9E7] border-y border-amber-200 font-bold text-slate-800">
                                            <td class="border-r border-slate-300 px-2 py-1 text-left">
                                                <div class="uppercase text-[10px] leading-tight text-slate-900 font-bold">{{ grupo.tipoDireccao }}</div>
                                                <div class="text-[9px] text-slate-700 font-semibold">{{ grupo.gestor }}</div>
                                            </td>
                                            <td class="border-r border-slate-300 px-1 py-1 text-center bg-amber-100/60 text-slate-900">{{ grupo.total.percDado }}%</td>
                                            <td class="border-r border-slate-300 px-2 py-1 text-right text-slate-900">{{ formatarNumero(grupo.total.fechoAA) }}</td>
                                            <td class="border-r border-slate-300 px-2 py-1 text-right text-slate-900">{{ formatarNumero(grupo.total.mesAnterior) }}</td>
                                            <td class="border-r border-slate-300 px-2 py-1 text-right text-slate-900 bg-amber-100/40">{{ formatarNumero(grupo.total.mesActual) }}</td>
                                            <td class="border-r border-slate-300 px-2 py-1 text-right" :class="grupo.total.difMensal < 0 ? 'text-blue-600' : 'text-rose-600'">
                                                {{ formatarNumero(grupo.total.difMensal) }}
                                            </td>
                                            <td class="border-r border-slate-300 px-2 py-1 text-right" :class="grupo.total.difAnual < 0 ? 'text-blue-600' : 'text-rose-600'">
                                                {{ formatarNumero(grupo.total.difAnual) }}
                                            </td>
                                            <td class="px-1.5 py-1 text-center font-bold" :class="grupo.total.varAno < 0 ? 'text-blue-600' : 'text-rose-600'">
                                                {{ formatarPercentual(grupo.total.varAno) }}
                                            </td>
                                        </tr>

                                        <tr v-for="item in grupo.itens" :key="item.Id" class="hover:bg-blue-50/40 border-b border-slate-100 text-slate-700">
                                            <td class="border-r border-slate-300 px-2 py-0.5 text-left whitespace-nowrap pl-4">{{ item.NomeProduto }}</td>
                                            <td class="border-r border-slate-300 px-1 py-0.5 text-center bg-blue-50/30 font-medium">
                                                {{ item.PercentagemDado !== null ? item.PercentagemDado + '%' : '0%' }}
                                            </td>
                                            <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(item.ProvisaoValorFechoAnoAnterior) }}</td>
                                            <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(item.ProvisaoValorMesAnterior) }}</td>
                                            <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums bg-blue-50/20 font-medium">{{ formatarNumero(item.ProvisaoValorMesActual) }}</td>
                                            <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums font-semibold"
                                                :class="Number(item.ProvisaoDiferencaMensal) < 0 ? 'text-blue-600' : 'text-rose-600'">
                                                {{ formatarNumero(item.ProvisaoDiferencaMensal) }}
                                            </td>
                                            <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums font-semibold"
                                                :class="Number(item.ProvisaoDiferencaAnual) < 0 ? 'text-blue-600' : 'text-rose-600'">
                                                {{ formatarNumero(item.ProvisaoDiferencaAnual) }}
                                            </td>
                                            <td class="px-1.5 py-0.5 text-center tabular-nums font-semibold"
                                                :class="Number(item.PercentVariaAnual) < 0 ? 'text-blue-600' : 'text-rose-600'">
                                                {{ formatarPercentual(item.PercentVariaAnual) }}
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ═══ TABELA 7: CARTEIRA WRITE-OFF ═══ -->
                    <section v-if="carteiraWriteOffRD.length > 0">
                        <SectionTitle
                            icon="fa-eraser"
                            title="5. CARTEIRA WRITE-OFF"
                            subtitle="Evolução e recuperação de ativos baixados"
                            tone="rose"
                        />
                        <div class="overflow-x-auto rounded border border-slate-300">
                            <table class="w-full text-[10px] md:text-[11px] border-collapse text-slate-800">
                                <thead>
                                    <tr class="bg-[#103E68] text-white text-center font-bold">
                                        <th class="border-r border-b border-slate-400 px-2 py-1 text-center w-28">Periodo WO</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1 text-right min-w-[95px]">B. Inicial</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1 text-right min-w-[95px]">&lt;= {{ cabecalhosWO.anterior }}</th>
                                        <th v-for="m in cabecalhosWO.meses" :key="m" class="border-r border-b border-slate-400 px-2 py-1 text-right min-w-[80px]">{{ m }}</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1 text-right min-w-[105px]">Recuperado</th>
                                        <th class="border-r border-b border-slate-400 px-2 py-1 text-right min-w-[105px] bg-[#0B2E4F]">B. Final</th>
                                        <th class="border-b border-slate-400 px-2 py-1 text-center min-w-[50px]">%KR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="wo in linhasWO" :key="wo.Id" :class="wo.IsTotal ? 'bg-[#103E68] text-white font-bold' : 'hover:bg-blue-50/40 border-b border-slate-200'">
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-center font-semibold">{{ wo.EtiquetaPeriodoWO }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(wo.BalancoInicial) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(wo.RecuperadoAcumuladoAnterior) }}</td>
                                        <td v-for="(v, idx) in wo.mesesValores" :key="idx" class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums">{{ formatarNumero(v) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums font-bold">{{ formatarNumero(wo.TotalRecuperado) }}</td>
                                        <td class="border-r border-slate-300 px-2 py-0.5 text-right tabular-nums font-bold bg-[#FFF9C4] text-slate-900">{{ formatarNumero(wo.BalancoFinal) }}</td>
                                        <td class="px-2 py-0.5 text-center tabular-nums font-bold">{{ formatarPercentual(wo.PctKR) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ═════════════════════════════════════════════════════════════════
                         6. TOP CLIENTES EM RISCO CRÍTICO (Coluna Oficial de Crédito oculta)
                         ═════════════════════════════════════════════════════════════════ -->
                    <section v-if="temTopClientes" class="pt-6 border-t-2 border-slate-200 space-y-4">
                        <SectionTitle
                            icon="fa-users-slash"
                            :title="`6. ${tituloSecaoTopClientes}`"
                            :subtitle="subtituloSecaoTopClientes"
                            tone="rose"
                        />

                        <!-- TABELA ÚNICA DE TOP 15 CLIENTES CONFORME VISÃO ATIVA -->
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-[11px] border-collapse text-slate-800">
                                <thead class="sticky top-0 text-white text-center"
                                       :class="direccaoActiva === 'GLOBAL' ? 'bg-gradient-to-r from-rose-900 to-rose-800' : (direccaoActiva === 'DOP' ? 'bg-[#0B2E4F]' : 'bg-indigo-900')">
                                    <tr>
                                        <th class="px-2 py-2 text-center w-10 border-r border-white/20">Pos.</th>
                                        <th class="px-2 py-2 text-center border-r border-white/20">LNR</th>
                                        <th class="px-3 py-2 text-left border-r border-white/20">Nome do Cliente</th>
                                        <th class="px-2 py-2 text-left border-r border-white/20">Produto</th>
                                        <th class="px-2 py-2 text-center border-r border-white/20">Direção</th>
                                        <th class="px-2 py-2 text-left border-r border-white/20">Centro de Custo</th>
                                        <th class="px-2 py-2 text-center border-r border-white/20">Dias Atraso</th>
                                        <th class="px-2 py-2 text-right border-r border-white/20">Saldo Capital (Kz)</th>
                                        <th class="px-2 py-2 text-right border-r border-white/20">Saldo Juros (Kz)</th>
                                        <th class="px-2 py-2 text-right border-r border-white/20 font-black bg-black/20 text-amber-200">Total em Risco (Kz)</th>
                                        <th class="px-2 py-2 text-right">Provisão Constituída</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="c in listaTopClientesAtiva" :key="c.ID || c.CodigoCliente"
                                        class="hover:bg-slate-50 transition-colors">
                                        <td class="px-2 py-1.5 text-center font-bold text-slate-500 border-r border-slate-200">
                                            {{ c.Posicao ?? '—' }}
                                        </td>
                                        <td class="px-2 py-1.5 text-center font-mono text-[10px] text-slate-600 border-r border-slate-200">
                                            {{ c.CodigoCliente }}
                                        </td>
                                        <td class="px-3 py-1.5 font-bold text-slate-900 whitespace-nowrap border-r border-slate-200">
                                            {{ c.NomeCliente }}
                                        </td>
                                        <td class="px-2 py-1.5 text-slate-700 whitespace-nowrap border-r border-slate-200">
                                            {{ c.Produto }}
                                        </td>
                                        <td class="px-2 py-1.5 text-center border-r border-slate-200">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold"
                                                  :class="c.ccDireccao === 'DOP' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                                                {{ c.ccDireccao || '—' }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-1.5 text-slate-600 whitespace-nowrap border-r border-slate-200">
                                            {{ c.CentroCusto }}
                                        </td>
                                        <td class="px-2 py-1.5 text-center border-r border-slate-200">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold"
                                                  :class="Number(c.DiasAtrasoACT) >= 1000 ? 'bg-rose-100 text-rose-800 ring-1 ring-rose-300' : 'bg-amber-100 text-amber-800'">
                                                {{ c.DiasAtrasoACT ?? 0 }} d
                                            </span>
                                        </td>
                                        <td class="px-2 py-1.5 text-right tabular-nums text-slate-700 border-r border-slate-200">
                                            {{ formatarNumero(c.SaldoCapitalACT) }}
                                        </td>
                                        <td class="px-2 py-1.5 text-right tabular-nums text-slate-500 border-r border-slate-200">
                                            {{ formatarNumero(c.SaldoJurosACT) }}
                                        </td>
                                        <td class="px-2 py-1.5 text-right tabular-nums font-black text-rose-700 bg-rose-50/70 border-r border-slate-200">
                                            {{ formatarNumero(c.SaldoTotalEmRisco) }}
                                        </td>
                                        <td class="px-2 py-1.5 text-right tabular-nums text-slate-600">
                                            {{ formatarNumero(c.ProvisaoACT) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>


                    <!-- ═════════════════════════════════════════════════════════════════
                         7. EXPOSIÇÃO AO RISCO POR CENTROS DE CUSTO
                         ═════════════════════════════════════════════════════════════════ -->
                    <section v-if="centrosCustoOrdenadosPorRisco.length > 0" class="pt-6 border-t-2 border-slate-200">
                        <SectionTitle
                            icon="fa-network-wired"
                            title="7. EXPOSIÇÃO AO RISCO POR CENTROS DE CUSTO"
                            subtitle="Pontos de Atendimento classificados em ordem de gravidade de risco (Maior risco na 1ª posição)"
                            tone="amber"
                        />

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-[11px] border-collapse text-slate-800">
                                <thead class="sticky top-0 bg-[#0B2E4F] text-white text-center">
                                    <tr>
                                        <th class="px-2 py-2 text-center w-12 border-r border-slate-700">Pos.</th>
                                        <th class="px-3 py-2 text-left border-r border-slate-700">Ponto de Atendimento (Centro de Custo)</th>
                                        <th class="px-3 py-2 text-left border-r border-slate-700">Província</th>
                                        <th class="px-2 py-2 text-center border-r border-slate-700">Clientes Ativos</th>
                                        <th class="px-2 py-2 text-center border-r border-slate-700 text-rose-300">Em Atraso</th>
                                        <th class="px-2 py-2 text-right border-r border-slate-700">Carteira Total Capital</th>
                                        <th class="px-2 py-2 text-right border-r border-slate-700 text-rose-300">Carteira Vencida</th>
                                        <th class="px-2 py-2 text-right border-r border-slate-700 bg-[#103E68] text-amber-300 font-bold">PAR %</th>
                                        <th class="px-2 py-2 text-right border-r border-slate-700">Provisão Total</th>
                                        <th class="px-2 py-2 text-center">Máx. Dias Atraso</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="item in centrosCustoOrdenadosPorRisco" :key="item.ID || item.CentroCusto"
                                        class="hover:bg-amber-50/40 transition-colors">
                                        <td class="px-2 py-1.5 text-center font-bold text-slate-500 border-r border-slate-200">
                                            {{ item.PosicaoRisco ?? '—' }}
                                        </td>
                                        <td class="px-3 py-1.5 font-bold text-slate-800 whitespace-nowrap border-r border-slate-200">
                                            <i class="fas fa-building mr-1.5 text-slate-400"></i>
                                            {{ item.CentroCusto }}
                                        </td>
                                        <td class="px-3 py-1.5 text-slate-600 border-r border-slate-200">
                                            {{ item.Provincia || '—' }}
                                        </td>
                                        <td class="px-2 py-1.5 text-center tabular-nums border-r border-slate-200">
                                            {{ formatarInt(item.TotalClientesAtivos) }}
                                        </td>
                                        <td class="px-2 py-1.5 text-center tabular-nums font-semibold text-rose-700 bg-rose-50/40 border-r border-slate-200">
                                            {{ formatarInt(item.ClientesEmAtraso) }}
                                        </td>
                                        <td class="px-2 py-1.5 text-right tabular-nums font-medium border-r border-slate-200">
                                            {{ formatarNumero(item.CarteiraTotalCapital) }}
                                        </td>
                                        <td class="px-2 py-1.5 text-right tabular-nums font-bold text-rose-700 border-r border-slate-200">
                                            {{ formatarNumero(item.CarteiraVencidaCapital) }}
                                        </td>
                                        <td class="px-2 py-1.5 text-right tabular-nums font-black text-amber-900 bg-amber-50 border-r border-slate-200">
                                            {{ formatarPercentual(item.PAR_Percentual) }}
                                        </td>
                                        <td class="px-2 py-1.5 text-right tabular-nums text-slate-700 border-r border-slate-200">
                                            {{ formatarNumero(item.ProvisaoTotal) }}
                                        </td>
                                        <td class="px-2 py-1.5 text-center">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold"
                                                  :class="Number(item.MaxDiasAtraso) > 365 ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700'">
                                                {{ item.MaxDiasAtraso ?? 0 }} dias
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                </div>
            </div>
        </div>

        <!-- ═══════════════ DIALOG: ENVIAR POR EMAIL ═══════════════ -->
        <transition name="fade">
            <div v-if="emailDialog.aberto"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                 @click.self="fecharDialogEmail"
                 @keydown.esc="fecharDialogEmail">
                <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden" role="dialog" aria-modal="true">
                    <div class="bg-gradient-to-r from-[#0B2E4F] to-[#1E5A8E] px-5 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3 text-white">
                            <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm">Enviar Relatório por Email</h3>
                                <p class="text-[11px] text-blue-100/80">RD Express · {{ ultimoMesNome }}</p>
                            </div>
                        </div>
                        <button @click="fecharDialogEmail" class="text-white/70 hover:text-white text-lg leading-none" aria-label="Fechar">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form @submit.prevent="enviarEmail" class="p-5 space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Remetente</label>
                            <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-100 text-slate-600 text-sm border border-slate-200">
                                <i class="fas fa-lock text-slate-400 text-xs"></i>
                                {{ EMAIL_REMETENTE }}
                            </div>
                        </div>

                        <div>
                            <label for="emailDestinatario" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                                Email do destinatário
                            </label>
                            <input id="emailDestinatario" ref="emailInputRef" v-model.trim="emailDialog.destinatario"
                                   type="email" required autocomplete="email" placeholder="nome@exemplo.com"
                                   :disabled="emailDialog.enviando"
                                   class="w-full px-3 py-2 rounded-lg border text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 disabled:bg-slate-50"
                                   :class="emailDialog.erro ? 'border-rose-400' : 'border-slate-300'" />
                            <p v-if="emailDialog.erro" class="mt-1 text-xs text-rose-600">{{ emailDialog.erro }}</p>
                            <p v-if="emailDialog.sucesso" class="mt-1 text-xs text-emerald-600">
                                <i class="fas fa-check-circle mr-1"></i>{{ emailDialog.sucesso }}
                            </p>
                        </div>

                        <div class="flex justify-end gap-2 pt-1">
                            <button type="button" @click="fecharDialogEmail" :disabled="emailDialog.enviando"
                                    class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition-all">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="emailDialog.enviando || !emailValido"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                <i :class="emailDialog.enviando ? 'fas fa-spinner fa-spin' : 'fas fa-paper-plane'"></i>
                                {{ emailDialog.enviando ? 'A enviar…' : 'Enviar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount, watch, nextTick, h } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Chart from 'chart.js/auto'

/* ══════════════════════════════════════════════════
   PROPS
   ══════════════════════════════════════════════════ */
const props = defineProps({
    direccaoActiva: { type: String, default: 'GLOBAL' },
    directorNome: { type: String, default: '' },
    historicoRD: { type: Array, default: () => [] },
    agenciasRD: { type: Array, default: () => [] },
    produtosRD: { type: Array, default: () => [] },
    provisoesProdutosRD: { type: Array, default: () => [] },
    desembolsosReembolsos12MesesRD: { type: Array, default: () => [] },
    dadosEstatisticosRD: { type: Array, default: () => [] },
    carteiraWriteOffRD: { type: Array, default: () => [] },
    movimentoPrestacaoRD: { type: Array, default: () => [] },
    riscoExposicaoCentrosCustoRD: { type: Array, default: () => [] },
    riscoTopClientesRD: { type: Array, default: () => [] },
    user: Object
})

const tituloDireccao = computed(() => {
    const map = {
        GLOBAL: '100% Instituição',
        DPP: 'Direcção DPP · Particulares & Parcerias',
        DOP: 'Direcção DOP · Operações',
        OUT: 'Direcção OUT · Outros',
    }
    const base = map[String(props.direccaoActiva || 'GLOBAL').toUpperCase()] || props.direccaoActiva
    return props.directorNome ? `${base} (${props.directorNome})` : base
})

/* ══════════════════════════════════════════════════
   COMPONENTES INTERNOS (KpiCard, SectionTitle, AgenciasTable)
   ══════════════════════════════════════════════════ */
const KpiCard = (p) => {
    const tones = {
        blue:    { bg: 'bg-blue-50',    text: 'text-blue-700',    ring: 'ring-blue-100',    grad: 'from-blue-500/10' },
        emerald: { bg: 'bg-emerald-50', text: 'text-emerald-700', ring: 'ring-emerald-100', grad: 'from-emerald-500/10' },
        rose:    { bg: 'bg-rose-50',    text: 'text-rose-700',    ring: 'ring-rose-100',    grad: 'from-rose-500/10' },
        amber:   { bg: 'bg-amber-50',   text: 'text-amber-700',   ring: 'ring-amber-100',   grad: 'from-amber-500/10' },
    }
    const t = tones[p.tone] || tones.blue
    const deltaNum = p.delta === null || p.delta === undefined ? null : Number(p.delta)
    const positivo = deltaNum === null ? null : (p.invert ? deltaNum <= 0 : deltaNum >= 0)
    const arrowClass = deltaNum === null ? '' : (deltaNum >= 0 ? 'fa-arrow-up' : 'fa-arrow-down')
    const deltaColor = deltaNum === null ? 'text-slate-400'
        : positivo ? 'text-emerald-600' : 'text-rose-600'

    return h('div', {
        class: `relative bg-white rounded-2xl shadow-sm border border-slate-200 p-3 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden`
    }, [
        h('div', { class: `absolute -top-8 -right-8 w-24 h-24 rounded-full bg-gradient-to-br ${t.grad} to-transparent opacity-60` }),
        h('div', { class: 'relative' }, [
            h('div', { class: 'flex items-center justify-between mb-1.5' }, [
                h('span', { class: 'text-[10px] font-bold uppercase tracking-wider text-slate-500' }, p.label),
                h('div', { class: `w-7 h-7 rounded-lg ${t.bg} flex items-center justify-center ring-1 ${t.ring}` }, [
                    h('i', { class: `fas ${p.icon} ${t.text} text-xs` })
                ])
            ]),
            h('div', { class: 'text-sm xl:text-base font-bold text-slate-900 tabular-nums tracking-tight leading-tight break-words' }, p.value),
            deltaNum !== null && h('div', { class: `flex items-center gap-1 mt-1 text-[11px] font-semibold ${deltaColor}` }, [
                h('i', { class: `fas ${arrowClass} text-[9px]` }),
                h('span', { class: 'tabular-nums' }, Math.abs(Math.round(deltaNum)) + '%'),
                h('span', { class: 'text-slate-400 font-normal ml-0.5' }, 'vs mês ant.')
            ])
        ])
    ])
}
KpiCard.props = ['label', 'value', 'delta', 'icon', 'tone', 'invert']

const SectionTitle = (p) => {
    const tones = {
        blue:    'border-blue-500 text-blue-700',
        emerald: 'border-emerald-500 text-emerald-700',
        amber:   'border-amber-500 text-amber-700',
        indigo:  'border-indigo-500 text-indigo-700',
        rose:    'border-rose-500 text-rose-700',
    }
    return h('div', { class: 'flex items-center gap-3 mb-2.5 border-l-4 pl-3 py-0.5 ' + (tones[p.tone] || tones.blue) }, [
        h('i', { class: `fas ${p.icon} text-lg` }),
        h('div', [
            h('h2', { class: 'text-sm md:text-base font-bold text-slate-900 leading-tight' }, p.title),
            p.subtitle && h('p', { class: 'text-[10px] md:text-[11px] text-slate-500 mt-0.5' }, p.subtitle)
        ])
    ])
}
SectionTitle.props = ['icon', 'title', 'subtitle', 'tone']

const AgenciasTable = (p) => {
    const linhasBase = (p.agencias || []).filter(a => !a.IsTotalizador)
    const itensParaSomar = linhasBase.length > 0 ? linhasBase : (p.agencias || [])

    const totais = itensParaSomar.reduce((acc, a) => {
        acc.OA_Qtd += Number(a.OA_Qtd) || 0
        acc.PA_Qtd += Number(a.PA_Qtd) || 0
        acc.BalancoValor += Number(a.BalancoValor) || 0
        acc.CreditosTotal_Qtd += Number(a.CreditosTotal_Qtd) || 0
        acc.CreditosNovos_Qtd += Number(a.CreditosNovos_Qtd) || 0
        acc.ClientesTotal_Qtd += Number(a.ClientesTotal_Qtd) || 0
        acc.ClientesNovos_Qtd += Number(a.ClientesNovos_Qtd) || 0
        acc.PAR1_Valor += Number(a.PAR1_Valor) || 0
        acc.PAR30_Valor += Number(a.PAR30_Valor) || 0
        acc.DesembolsoValor += Number(a.DesembolsoValor) || 0
        acc.ReembolsoValor += Number(a.ReembolsoValor) || 0
        acc.ProvisaoValor += Number(a.ProvisaoValor) || 0
        acc.PesoPercentual += Number(a.PesoPercentual) || 0

        if (a.TempoAtendimentodias !== null && a.TempoAtendimentodias !== undefined) {
            acc.taSoma += Number(a.TempoAtendimentodias) || 0
            acc.taCount += 1
        }
        if (a.GestaoValor !== null && a.GestaoValor !== undefined) {
            acc.gestaoSoma += Number(a.GestaoValor) || 0
            acc.gestaoCount += 1
            if (!acc.gestaoSufixo && a.GestaoSufixo) acc.gestaoSufixo = a.GestaoSufixo
        }
        return acc
    }, {
        OA_Qtd: 0, PA_Qtd: 0, BalancoValor: 0,
        CreditosTotal_Qtd: 0, CreditosNovos_Qtd: 0,
        ClientesTotal_Qtd: 0, ClientesNovos_Qtd: 0,
        PAR1_Valor: 0, PAR30_Valor: 0,
        DesembolsoValor: 0, ReembolsoValor: 0, ProvisaoValor: 0,
        PesoPercentual: 0, taSoma: 0, taCount: 0,
        gestaoSoma: 0, gestaoCount: 0, gestaoSufixo: ''
    })

    const par1Perc = totais.BalancoValor > 0 ? (totais.PAR1_Valor / totais.BalancoValor) * 100 : 0
    const par30Perc = totais.BalancoValor > 0 ? (totais.PAR30_Valor / totais.BalancoValor) * 100 : 0
    const taMedia = totais.taCount > 0 ? Math.round(totais.taSoma / totais.taCount) : null
    const gestaoMedia = totais.gestaoCount > 0 ? Math.round(totais.gestaoSoma / totais.gestaoCount) : null

    return h('div', { class: 'overflow-x-auto rounded-xl border border-slate-200' }, [
        h('table', { class: 'w-full text-xs border-collapse text-slate-800' }, [
            h('thead', [
                h('tr', { class: 'bg-[#0B2E4F] text-white' }, [
                    h('th', { colspan: 11, class: 'px-3 py-1.5 text-xs tracking-wider uppercase text-left' }, [
                        h('i', { class: 'fas fa-location-dot mr-2 opacity-70' }),
                        p.titulo
                    ])
                ]),
                h('tr', { class: 'bg-[#103E68] text-white text-center text-[10px]' }, [
                    h('th', { class: 'border-r border-b border-slate-300 px-3 py-1 text-left w-48 uppercase tracking-wider' }, 'Localidade'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'OA/PA'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'Carteira'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'Créditos | Novos'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'Clientes | Novos'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'PAR 1'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'PAR 30'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'Desembolsos | TA'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'Reembolsos'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'Provisão'),
                    h('th', { class: 'border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'Gestão')
                ])
            ]),
            h('tbody', { class: 'divide-y divide-slate-100' },
                p.agencias.map(a => h('tr', {
                    key: a.Id,
                    class: a.IsTotalizador
                        ? 'bg-gradient-to-r from-amber-50 to-amber-50/40 font-bold text-slate-900 border-t-2 border-b-2 border-amber-200'
                        : 'hover:bg-blue-50/40 transition-colors font-medium'
                }, [
                    h('td', { class: 'border-r border-slate-200 px-3 py-1 text-left whitespace-nowrap' }, [
                        !a.IsTotalizador && h('span', { class: 'text-slate-400 mr-1' }, '─'),
                        h('span', { class: a.IsTotalizador ? '' : 'pl-2' }, a.NomeLocal),
                        a.PesoPercentual && h('span', { class: 'text-slate-500 font-normal ml-1 text-[10px]' }, `(${Math.round(a.PesoPercentual)}%)`)
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-1 text-center bg-slate-50/60 text-slate-600 tabular-nums' },
                        `${a.OA_Qtd ?? '—'}/${a.PA_Qtd ?? '—'}`),
                    h('td', { class: 'border-r border-slate-200 px-2 py-1 text-right font-semibold tabular-nums' }, formatarNumero(a.BalancoValor)),
                    h('td', { class: 'border-r border-slate-200 px-2 py-1 text-right tabular-nums' }, [
                        h('span', { class: 'text-slate-700' }, formatarInt(a.CreditosTotal_Qtd)),
                        h('span', { class: 'text-slate-400 mx-1' }, '|'),
                        h('span', { class: 'text-indigo-700 font-bold' }, formatarInt(a.CreditosNovos_Qtd))
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-1 text-right tabular-nums' }, [
                        h('span', { class: 'text-slate-700' }, formatarInt(a.ClientesTotal_Qtd)),
                        h('span', { class: 'text-slate-400 mx-1' }, '|'),
                        h('span', { class: 'text-cyan-700 font-bold' }, formatarInt(a.ClientesNovos_Qtd))
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-1 text-right tabular-nums' }, [
                        formatarK(a.PAR1_Valor),
                        a.PAR1_Percentual !== null && h('span', { class: 'text-[10px] text-amber-700 font-semibold ml-1' }, `(${Math.round(a.PAR1_Percentual)}%)`)
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-1 text-right tabular-nums' }, [
                        formatarK(a.PAR30_Valor),
                        a.PAR30_Percentual !== null && h('span', { class: 'text-[10px] text-rose-700 font-semibold ml-1' }, `(${Math.round(a.PAR30_Percentual)}%)`)
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-1 text-right bg-emerald-50/50 text-emerald-700 font-semibold tabular-nums' }, [
                        formatarNumero(a.DesembolsoValor),
                        a.TempoAtendimentodias !== null && h('span', { class: 'text-[10px] text-slate-500 ml-1' }, `| ${String(a.TempoAtendimentodias).padStart(2, '0')}`)
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-1 text-right bg-rose-50/50 text-rose-700 tabular-nums' }, formatarNumero(a.ReembolsoValor)),
                    h('td', { class: 'border-r border-slate-200 px-2 py-1 text-right tabular-nums' }, [
                        formatarNumero(a.ProvisaoValor),
                        a.ProvisaoClasseRisco && h('span', { class: 'font-bold text-slate-700 ml-1' }, `(${a.ProvisaoClasseRisco})`)
                    ]),
                    h('td', { class: 'px-2 py-1 text-center font-bold tabular-nums' }, [
                        h('span', {
                            class: 'inline-flex items-center px-1.5 py-0.2 rounded-full text-[10px] ' + obterCorGestao(a.GestaoValor)
                        }, a.GestaoValor !== null ? a.GestaoValor + (a.GestaoSufixo || '') : '—')
                    ])
                ]))
            ),
            h('tfoot', [
                h('tr', { class: 'bg-[#0B2E4F] text-white font-bold border-t-2 border-slate-400' }, [
                    h('td', { class: 'border-r border-slate-600 px-3 py-1.5 text-left uppercase tracking-wider' }, [
                        `Total ${p.titulo}`,
                        totais.PesoPercentual > 0 && h('span', { class: 'text-blue-200 font-normal ml-1 text-[10px]' }, `(${Math.round(totais.PesoPercentual)}%)`)
                    ]),
                    h('td', { class: 'border-r border-slate-600 px-2 py-1.5 text-center tabular-nums' },
                        `${totais.OA_Qtd}/${totais.PA_Qtd}`),
                    h('td', { class: 'border-r border-slate-600 px-2 py-1.5 text-right tabular-nums' },
                        formatarNumero(totais.BalancoValor)),
                    h('td', { class: 'border-r border-slate-600 px-2 py-1.5 text-right tabular-nums' }, [
                        h('span', null, formatarInt(totais.CreditosTotal_Qtd)),
                        h('span', { class: 'text-slate-400 mx-1' }, '|'),
                        h('span', { class: 'text-indigo-200' }, formatarInt(totais.CreditosNovos_Qtd))
                    ]),
                    h('td', { class: 'border-r border-slate-600 px-2 py-1.5 text-right tabular-nums' }, [
                        h('span', null, formatarInt(totais.ClientesTotal_Qtd)),
                        h('span', { class: 'text-slate-400 mx-1' }, '|'),
                        h('span', { class: 'text-cyan-200' }, formatarInt(totais.ClientesNovos_Qtd))
                    ]),
                    h('td', { class: 'border-r border-slate-600 px-2 py-1.5 text-right tabular-nums' }, [
                        formatarK(totais.PAR1_Valor),
                        h('span', { class: 'text-[10px] text-amber-300 font-semibold ml-1' }, `(${Math.round(par1Perc)}%)`)
                    ]),
                    h('td', { class: 'border-r border-slate-600 px-2 py-1.5 text-right tabular-nums' }, [
                        formatarK(totais.PAR30_Valor),
                        h('span', { class: 'text-[10px] text-rose-300 font-semibold ml-1' }, `(${Math.round(par30Perc)}%)`)
                    ]),
                    h('td', { class: 'border-r border-slate-600 px-2 py-1.5 text-right text-emerald-300 tabular-nums' }, [
                        formatarNumero(totais.DesembolsoValor),
                        taMedia !== null && h('span', { class: 'text-[10px] text-slate-300 ml-1' }, `| ${String(taMedia).padStart(2, '0')}`)
                    ]),
                    h('td', { class: 'border-r border-slate-600 px-2 py-1.5 text-right text-rose-300 tabular-nums' },
                        formatarNumero(totais.ReembolsoValor)),
                    h('td', { class: 'border-r border-slate-600 px-2 py-1.5 text-right tabular-nums' },
                        formatarNumero(totais.ProvisaoValor)),
                    h('td', { class: 'px-2 py-1.5 text-center tabular-nums' }, [
                        h('span', {
                            class: 'inline-flex items-center px-1.5 py-0.2 rounded-full text-[10px] ' + obterCorGestao(gestaoMedia)
                        }, gestaoMedia !== null ? gestaoMedia + totais.gestaoSufixo : '—')
                    ])
                ])
            ])
        ])
    ])
}
AgenciasTable.props = ['titulo', 'agencias']

/* ══════════════════════════════════════════════════
   REFS & CHARTS
   ══════════════════════════════════════════════════ */
const areaTabelas = ref(null)
const chartBalancoRef = ref(null)
const chartFluxoRef = ref(null)
const chartQualidadeRef = ref(null)
const chartAgenciasRef = ref(null)
const charts = { balanco: null, fluxo: null, qualidade: null, agencias: null }

/* ══════════════════════════════════════════════════
   HELPER DE DATA SEGURA (SUPORTA ISO YYYY-MM-DD E DD/MM/YYYY)
   ══════════════════════════════════════════════════ */
const parseDataLocal = (dataStr) => {
    if (!dataStr) return null
    if (dataStr instanceof Date) return dataStr
    const str = String(dataStr).trim()

    const isoMatch = str.match(/^(\d{4})[-/](\d{1,2})[-/](\d{1,2})/)
    if (isoMatch) {
        return new Date(Number(isoMatch[1]), Number(isoMatch[2]) - 1, Number(isoMatch[3]))
    }

    const ptMatch = str.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/)
    if (ptMatch) {
        return new Date(Number(ptMatch[3]), Number(ptMatch[2]) - 1, Number(ptMatch[1]))
    }

    return new Date(str)
}

/* ══════════════════════════════════════════════════
   COMPUTED: HISTÓRICO DINÂMICO (ACTIVO = 1)
   ══════════════════════════════════════════════════ */
const historicoOrdenado = computed(() => {
    return [...(props.historicoRD || [])]
        .filter(item => Number(item.Activo ?? item.activo) === 1 || item.Activo === true || item.activo === true)
        .sort((a, b) => parseDataLocal(a.DataReferencia) - parseDataLocal(b.DataReferencia))
})

const ultimoRegistro = computed(() => {
    if (historicoOrdenado.value.length === 0) return null
    return historicoOrdenado.value[historicoOrdenado.value.length - 1]
})

const registroAnterior = computed(() => {
    if (historicoOrdenado.value.length < 2) return null
    return historicoOrdenado.value[historicoOrdenado.value.length - 2]
})

const historicoAteUltimoPeriodo = computed(() => {
    if (historicoOrdenado.value.length <= 1) return historicoOrdenado.value
    return historicoOrdenado.value.slice(0, -1)
})

const anoAtual = computed(() => {
    if (ultimoRegistro.value?.DataReferencia) {
        return parseDataLocal(ultimoRegistro.value.DataReferencia).getFullYear()
    }
    return new Date().getFullYear()
})

const formatarLabelMes = (dataReferencia) => {
    const data = parseDataLocal(dataReferencia)
    if (!data || isNaN(data.getTime())) return String(dataReferencia || '')
    const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
    const mesAbrev = meses[data.getMonth()]
    const ano = data.getFullYear()
    return (data.getMonth() === 11 && ano < Number(anoAtual.value))
        ? `DEZ${ano}`
        : `${mesAbrev}/${String(ano).substring(2)}`
}

const colunasMeses = computed(() => historicoAteUltimoPeriodo.value.map(item => ({
    key: item.Id || item.DataReferencia,
    label: formatarLabelMes(item.DataReferencia)
})))

const labelsGraficosAutomaticos = computed(() => {
    if (historicoOrdenado.value.length === 0) return []
    if (historicoOrdenado.value.length === 1) {
        return [formatarLabelMes(ultimoRegistro.value.DataReferencia)]
    }
    const labelsHistorico = colunasMeses.value.map(m => m.label)
    const labelMesEmAnalise = formatarLabelMes(ultimoRegistro.value.DataReferencia)
    return [...labelsHistorico, labelMesEmAnalise]
})

const ultimoMesNome = computed(() => {
    if (!ultimoRegistro.value) return ''
    const d = parseDataLocal(ultimoRegistro.value.DataReferencia)
    return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
})

const labelMesActual = computed(() => ultimoMesNome.value)

const labelMesAnterior = computed(() => {
    if (!registroAnterior.value?.DataReferencia) return ''
    const d = parseDataLocal(registroAnterior.value.DataReferencia)
    return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
})

const labelFechoAA = computed(() => `DEZ ${Number(anoAtual.value) - 1}`)

const agenciasCapital  = computed(() => (props.agenciasRD || []).filter(i => i.TipoAgrupamento === 'CAPITAL'))
const agenciasProvincia = computed(() => (props.agenciasRD || []).filter(i => i.TipoAgrupamento === 'PROVINCIA'))

/* ══════════════════════════════════════════════════
   COMPUTED: DESTAQUE MAX/MIN, BLOCOS DE 6 MESES & TOTAL ACUMULADO FLUXO (12 MESES)
   ══════════════════════════════════════════════════ */
const blocosFluxo6Meses = computed(() => {
    const list = props.desembolsosReembolsos12MesesRD || []
    if (list.length === 0) return []
    const blocos = []
    for (let i = 0; i < list.length; i += 6) {
        blocos.push(list.slice(i, i + 6))
    }
    return blocos
})

const totaisAcumuladosFluxo = computed(() => {
    const list = props.desembolsosReembolsos12MesesRD || []
    return list.reduce((acc, item) => {
        acc.totalDesembolso += Number(item.DesembolsoValor) || 0
        acc.totalReembolso += Number(item.ReembolsoValor) || 0
        return acc
    }, { totalDesembolso: 0, totalReembolso: 0 })
})

const maxMinFluxo = computed(() => {
    const list = props.desembolsosReembolsos12MesesRD || []
    if (list.length === 0) return { maxDesemb: 0, minDesemb: 0, maxReemb: 0, minReemb: 0 }

    const desValores = list.map(i => Number(i.DesembolsoValor) || 0)
    const reeValores = list.map(i => Number(i.ReembolsoValor) || 0)

    return {
        maxDesemb: Math.max(...desValores),
        minDesemb: Math.min(...desValores),
        maxReemb: Math.max(...reeValores),
        minReemb: Math.min(...reeValores)
    }
})

const obterClasseDestaqueDesembolso = (mes) => {
    const val = Number(mes.DesembolsoValor) || 0
    if (val === maxMinFluxo.value.maxDesemb) {
        return 'bg-emerald-200 text-emerald-950 font-black ring-1 ring-emerald-500/50'
    }
    if (val === maxMinFluxo.value.minDesemb) {
        return 'bg-rose-200 text-rose-950 font-black ring-1 ring-rose-500/50'
    }
    return 'font-semibold text-emerald-700'
}

const obterClasseDestaqueReembolso = (mes) => {
    const val = Number(mes.ReembolsoValor) || 0
    if (val === maxMinFluxo.value.maxReemb) {
        return 'bg-emerald-200 text-emerald-950 font-black ring-1 ring-emerald-500/50'
    }
    if (val === maxMinFluxo.value.minReemb) {
        return 'bg-rose-200 text-rose-950 font-black ring-1 ring-rose-500/50'
    }
    return 'font-semibold text-rose-700'
}

/* ══════════════════════════════════════════════════
   COMPUTED: PRODUTOS AGRUPADOS
   ══════════════════════════════════════════════════ */
const produtosAgrupados = computed(() => {
    if (!props.produtosRD || props.produtosRD.length === 0) return []
    const grupos = {}

    props.produtosRD.forEach(item => {
        const gestor = item.DirectorNome || 'SEM GESTOR ATRIBUÍDO'
        const tipoDireccao = item.TipoDireccao || 'GERAL'

        if (!grupos[gestor]) {
            grupos[gestor] = {
                gestor,
                tipoDireccao,
                itens: [],
                total: {
                    OA_Qtd: 0,
                    PA_Qtd: 0,
                    BalancoValor: 0,
                    CreditosTotal_Qtd: 0,
                    CreditosNovos_Qtd: 0,
                    ClientesTotal_Qtd: 0,
                    ClientesNovos_Qtd: 0,
                    PAR1_Valor: 0,
                    PAR1_Percentual: 0,
                    PAR30_Valor: 0,
                    PAR30_Percentual: 0,
                    DesembolsoValor: 0,
                    ReembolsoValor: 0,
                    ProvisaoValor: 0,
                    taSoma: 0,
                    taCount: 0,
                    TempoAtendimentoMedia: null,
                    gestaoSoma: 0,
                    gestaoCount: 0,
                    GestaoMedia: null,
                    GestaoSufixo: ''
                }
            }
        }

        grupos[gestor].itens.push(item)

        const t = grupos[gestor].total
        t.OA_Qtd += Number(item.OA_Qtd) || 0
        t.PA_Qtd += Number(item.PA_Qtd) || 0
        t.BalancoValor += Number(item.BalancoValor) || 0
        t.CreditosTotal_Qtd += Number(item.CreditosTotal_Qtd) || 0
        t.CreditosNovos_Qtd += Number(item.CreditosNovos_Qtd) || 0
        t.ClientesTotal_Qtd += Number(item.ClientesTotal_Qtd) || 0
        t.ClientesNovos_Qtd += Number(item.ClientesNovos_Qtd) || 0
        t.PAR1_Valor += Number(item.PAR1_Valor) || 0
        t.PAR30_Valor += Number(item.PAR30_Valor) || 0
        t.DesembolsoValor += Number(item.DesembolsoValor) || 0
        t.ReembolsoValor += Number(item.ReembolsoValor) || 0
        t.ProvisaoValor += Number(item.ProvisaoValor) || 0

        if (item.TempoAtendimentodias !== null && item.TempoAtendimentodias !== undefined) {
            t.taSoma += Number(item.TempoAtendimentodias) || 0
            t.taCount += 1
        }
        if (item.GestaoValor !== null && item.GestaoValor !== undefined) {
            t.gestaoSoma += Number(item.GestaoValor) || 0
            t.gestaoCount += 1
            if (!t.GestaoSufixo && item.GestaoSufixo) {
                t.GestaoSufixo = item.GestaoSufixo
            }
        }
    })

    const lista = Object.values(grupos)
    lista.forEach(g => {
        const t = g.total
        t.PAR1_Percentual = t.BalancoValor > 0 ? (t.PAR1_Valor / t.BalancoValor) * 100 : 0
        t.PAR30_Percentual = t.BalancoValor > 0 ? (t.PAR30_Valor / t.BalancoValor) * 100 : 0
        t.TempoAtendimentoMedia = t.taCount > 0 ? t.taSoma / t.taCount : null
        t.GestaoMedia = t.gestaoCount > 0 ? t.gestaoSoma / t.gestaoCount : null
    })

    return lista.sort((a, b) => a.gestor.localeCompare(b.gestor, 'pt-PT'))
})

/* ══════════════════════════════════════════════════
   COMPUTED: PROVISÕES AGRUPADAS
   ══════════════════════════════════════════════════ */
const provisoesAgrupadas = computed(() => {
    if (!props.provisoesProdutosRD || props.provisoesProdutosRD.length === 0) return []

    const mapaProdutos = {}
    ;(props.produtosRD || []).forEach(p => {
        if (p.NomeProduto) {
            mapaProdutos[p.NomeProduto.trim().toLowerCase()] = p
        }
    })

    const grupos = {}

    props.provisoesProdutosRD.forEach(item => {
        const nomeProd = (item.NomeProduto || '').trim().toLowerCase()
        let gestor = ''
        let tipoDireccao = ''

        if (nomeProd.includes('colaborador')) {
            gestor = 'Marta dos Santos'
            tipoDireccao = 'OUTROS'
        } else {
            const prodRef = mapaProdutos[nomeProd]
            gestor = item.DirectorNome || item.Gestor || prodRef?.DirectorNome || 'SEM GESTOR'
            tipoDireccao = item.TipoDireccao || item.TipoAgrupamento || prodRef?.TipoDireccao || 'DIRECÇÃO'
        }

        const gestorKey = gestor.trim().replace(/\s+/g, ' ').toUpperCase()

        if (!grupos[gestorKey]) {
            grupos[gestorKey] = {
                gestor: gestor.trim(),
                tipoDireccao: tipoDireccao.toUpperCase(),
                pesoPercentual: item.PesoDireccao || null,
                itens: [],
                total: {
                    fechoAA: 0,
                    mesAnterior: 0,
                    provMenorAno: 0,
                    provAnoAtual: 0,
                    mesActual: 0,
                    difMensal: 0,
                    difAnual: 0,
                    difMedMen: 0,
                    varAno: 0,
                    percDado: 0
                }
            }
        }

        grupos[gestorKey].itens.push(item)

        const t = grupos[gestorKey].total
        t.fechoAA += Number(item.ProvisaoValorFechoAnoAnterior || 0)
        t.mesAnterior += Number(item.ProvisaoValorMesAnterior || 0)
        t.provMenorAno += Number(item.ProvisaoMenorAno ?? item.ProvisaoValorFechoAnoAnterior ?? 0)
        t.provAnoAtual += Number(item.ProvisaoAnoActual ?? (Number(item.ProvisaoValorMesActual || 0) - Number(item.ProvisaoValorFechoAnoAnterior || 0)))
        t.mesActual += Number(item.ProvisaoValorMesActual || 0)
        t.difMensal += Number(item.ProvisaoDiferencaMensal || 0)
        t.difAnual += Number(item.ProvisaoDiferencaAnual || 0)
        t.difMedMen += Number(item.ProvisaoDiferencaMediaMensal || 0)
    })

    const lista = Object.values(grupos)
    lista.forEach(g => {
        g.total.varAno = g.total.fechoAA > 0 ? ((g.total.mesActual - g.total.fechoAA) / g.total.fechoAA) * 100 : 0
        const somaPerc = g.itens.reduce((acc, i) => acc + Number(i.PercentagemDado || 0), 0)
        g.total.percDado = g.itens.length ? Math.round(somaPerc / g.itens.length) : 0
    })

    return lista
})

const totalGeralProvisoes = computed(() => {
    const tot = {
        fechoAA: 0,
        mesAnterior: 0,
        provMenorAno: 0,
        provAnoAtual: 0,
        mesActual: 0,
        difMensal: 0,
        difAnual: 0,
        difMedMen: 0,
        varAno: 0,
        percDado: 6
    }
    provisoesAgrupadas.value.forEach(g => {
        tot.fechoAA += g.total.fechoAA
        tot.mesAnterior += g.total.mesAnterior
        tot.provMenorAno += g.total.provMenorAno
        tot.provAnoAtual += g.total.provAnoAtual
        tot.mesActual += g.total.mesActual
        tot.difMensal += g.total.difMensal
        tot.difAnual += g.total.difAnual
        tot.difMedMen += g.total.difMedMen
    })
    tot.varAno = tot.fechoAA > 0 ? ((tot.mesActual - tot.fechoAA) / tot.fechoAA) * 100 : 0
    return tot
})

/* ══════════════════════════════════════════════════
   COMPUTED: MOVIMENTO DAS PRESTAÇÕES & WRITE-OFF
   ══════════════════════════════════════════════════ */
const linhasMovimentoPrestacao = computed(() => {
    return (props.movimentoPrestacaoRD || []).map(item => {
        const p = String(item.PeriodoPrestacao || '').trim().toUpperCase()
        const isTotal = p.includes('JUL 2021') || p.includes('TOTAL') || item.IsTotalizador
        const isIntermediario = /^\d{6}\s*-\s*\d{6}$/.test(p) || p.includes('202107 - 202608')
        return {
            ...item,
            IsTotal: isTotal,
            IsIntermediario: isIntermediario
        }
    })
})

const obterClasseLinhaMP = (mp) => {
    if (mp.IsTotal || mp.IsIntermediario) {
        return 'bg-[#103E68] text-white font-bold border-t border-slate-500'
    }
    return 'hover:bg-blue-50/40 border-b border-slate-200 text-slate-800'
}

const cabecalhosWO = computed(() => {
    return {
        anterior: `MAR ${anoAtual.value}`,
        meses: [`ABR ${anoAtual.value}`, `MAI ${anoAtual.value}`, `JUN ${anoAtual.value}`, `JUL ${anoAtual.value}`, `AGO ${anoAtual.value}`]
    }
})

const linhasWO = computed(() => {
    return (props.carteiraWriteOffRD || []).map(item => {
        const isTot = String(item.EtiquetaPeriodoWO).includes('-202') || item.IsTotalizador
        return {
            ...item,
            IsTotal: isTot,
            mesesValores: [
                item.RecuperadoMes1 ?? 0,
                item.RecuperadoMes2 ?? 0,
                item.RecuperadoMes3 ?? 0,
                item.RecuperadoMes4 ?? 0,
                item.RecuperadoMes5 ?? 0
            ]
        }
    })
})

/* ══════════════════════════════════════════════════
   COMPUTED: 7. CENTROS DE CUSTO ORDENADOS POR GRAVIDADE DE RISCO
   ══════════════════════════════════════════════════ */
const centrosCustoOrdenadosPorRisco = computed(() => {
    return [...(props.riscoExposicaoCentrosCustoRD || [])]
        .sort((a, b) => {
            const posA = a.PosicaoRisco !== null && a.PosicaoRisco !== undefined ? Number(a.PosicaoRisco) : 999999
            const posB = b.PosicaoRisco !== null && b.PosicaoRisco !== undefined ? Number(b.PosicaoRisco) : 999999
            if (posA !== posB) return posA - posB
            return (Number(b.CarteiraVencidaCapital) || 0) - (Number(a.CarteiraVencidaCapital) || 0)
        })
})

/* ══════════════════════════════════════════════════
   COMPUTED: 6. TOP 15 CLIENTES (GERAL NA PÁGINA INICIAL | POR DIREÇÃO NAS RESTANTES)
   ══════════════════════════════════════════════════ */
const normalizarTipoRanking = (val) => String(val || '').trim().toUpperCase()

const tituloSecaoTopClientes = computed(() => {
    const dir = String(props.direccaoActiva || 'GLOBAL').toUpperCase()
    if (dir === 'GLOBAL') return 'TOP 15 CLIENTES EM RISCO CRÍTICO — VISÃO GERAL (INSTITUIÇÃO)'
    return `TOP 15 CLIENTES EM RISCO CRÍTICO — DIRECÇÃO ${dir}`
})

const subtituloSecaoTopClientes = computed(() => {
    const dir = String(props.direccaoActiva || 'GLOBAL').toUpperCase()
    if (dir === 'GLOBAL') return 'Ranking consolidado dos 15 clientes com maior exposição em risco na instituição'
    return `Ranking dos 15 clientes com maior exposição em risco exclusivos da direcção ${dir}`
})

const listaTopClientesAtiva = computed(() => {
    const dir = String(props.direccaoActiva || 'GLOBAL').toUpperCase()
    const lista = [...(props.riscoTopClientesRD || [])]

    if (dir === 'GLOBAL') {
        return lista
            .filter(c => normalizarTipoRanking(c.TipoRanking) === 'TOP15_GERAL')
            .sort((a, b) => (Number(a.Posicao) || 999) - (Number(b.Posicao) || 999))
            .slice(0, 15)
    }

    const rankingEsperado = `TOP15_${dir}`
    return lista
        .filter(c =>
            normalizarTipoRanking(c.TipoRanking) === rankingEsperado ||
            (!c.TipoRanking && normalizarTipoRanking(c.ccDireccao) === dir)
        )
        .sort((a, b) => (Number(a.Posicao) || 999) - (Number(b.Posicao) || 999))
        .slice(0, 15)
})

const temTopClientes = computed(() => {
    return listaTopClientesAtiva.value.length > 0
})

const linhasRisco = [
    { key: 'npl',   numero: 4, label: 'NPL',    cor: 'bg-rose-500',   percKey: 'NPLPercentual',   valKey: 'NPLValor',   maisKey: 'NPL_Mais',   menosKey: 'NPL_Menos',   variacaoKey: 'VariacaoNPL',   classePerc: 'text-rose-700' },
    { key: 'par1',  numero: 5, label: 'PAR 1',  cor: 'bg-orange-500', percKey: 'PAR1Percentual',  valKey: 'PAR1Valor',  maisKey: 'PAR1_Mais',  menosKey: 'PAR1_Menos',  variacaoKey: 'VariacaoPAR1',  classePerc: 'text-orange-700' },
    { key: 'par30', numero: 6, label: 'PAR 30', cor: 'bg-amber-500',  percKey: 'PAR30Percentual', valKey: 'PAR30Valor', maisKey: 'PAR30_Mais', menosKey: 'PAR30_Menos', variacaoKey: 'VariacaoPAR30', classePerc: 'text-amber-700' },
]

const linhasPercentuais = [
    { key: 'TaxaReembolsoPercentual',   numero: 8,  label: 'Taxa de Reembolso',   cor: 'bg-emerald-500', maisKey: 'TaxaReembolso_Mais',   menosKey: 'TaxaReembolso_Menos',   variacaoKey: 'VariacaoTaxaReembolso',   classeFixa: 'text-emerald-700', invertido: false },
    { key: 'TaxaCumprimentoPercentual', numero: 9,  label: 'Taxa de Cumprimento', cor: 'bg-blue-500',    maisKey: 'TaxaCumprimento_Mais', menosKey: 'TaxaCumprimento_Menos', variacaoKey: 'VariacaoTaxaCumprimento', classeFixa: 'text-blue-700',    invertido: false },
    { key: 'TaxaRecuperacaoPercentual', numero: 10, label: 'Taxa de Recuperação', cor: 'bg-indigo-500',  maisKey: 'TaxaRecuperacao_Mais', menosKey: 'TaxaRecuperacao_Menos', variacaoKey: 'VariacaoTaxaRecuperacao', classeFixa: 'text-indigo-700',  invertido: false },
]

/* ══════════════════════════════════════════════════
   HELPERS & FORMATADORES
   ══════════════════════════════════════════════════ */
const calcularDelta = (atual, anterior) => {
    const a = Number(atual), b = Number(anterior)
    if (!isFinite(a) || !isFinite(b) || b === 0) return null
    return ((a - b) / Math.abs(b)) * 100
}

const formatarProvisaoCobertura = (diasAtraso, percentual) => {
    if (percentual === null || percentual === undefined) return '—'
    return `(DA: ${diasAtraso ?? 0} :: ${formatarNumero(percentual)}%)`
}

const _toNum = (val) => {
    if (val === null || val === undefined || val === '') return NaN
    if (typeof val === 'string') {
        return Number(val.includes(',') ? val.replace(/\./g, '').replace(',', '.') : val.trim())
    }
    return Number(val)
}

const formatarNumero = (val) => {
    const n = _toNum(val)
    if (!isFinite(n)) return '0'
    return n.toLocaleString('pt-PT', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

const formatarInt = (val) => {
    const n = _toNum(val)
    if (!isFinite(n)) return '0'
    return n.toLocaleString('pt-PT', { maximumFractionDigits: 0 })
}

const formatarCompacto = (val) => {
    const n = _toNum(val)
    if (!isFinite(n)) return '—'
    const abs = Math.abs(n)
    if (abs >= 1e9) return (n / 1e9).toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + 'B'
    if (abs >= 1e6) return (n / 1e6).toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + 'M'
    if (abs >= 1e3) return (n / 1e3).toLocaleString('pt-PT', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + 'K'
    return n.toLocaleString('pt-PT', { maximumFractionDigits: 0 })
}

const formatarK = (val) => {
    const n = _toNum(val)
    if (!isFinite(n)) return '—'
    const abs = Math.abs(n)
    if (abs >= 1e6) return (n / 1e6).toLocaleString('pt-PT', { minimumFractionDigits: 1, maximumFractionDigits: 3 }) + 'M'
    if (abs >= 1e3) return (n / 1e3).toLocaleString('pt-PT', { minimumFractionDigits: 1, maximumFractionDigits: 3 }) + 'K'
    return n.toLocaleString('pt-PT', { maximumFractionDigits: 0 })
}

const formatarMoedaCompleta = (val) => {
    const n = _toNum(val)
    if (!isFinite(n)) return '—'
    return n.toLocaleString('pt-PT', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' Kz'
}

const formatarMoedaCompacta = (val) => {
    const s = formatarCompacto(val)
    return s === '—' ? s : s + ' Kz'
}

const formatarPercentual = (val) => {
    const n = _toNum(val)
    if (!isFinite(n)) return '0%'
    return n.toLocaleString('pt-PT', { minimumFractionDigits: 0, maximumFractionDigits: 1 }) + '%'
}

const obterCorGestao = (valor) => {
    const n = Number(valor)
    if (!isFinite(n)) return 'bg-slate-100 text-slate-500'
    if (n > 0) return 'bg-emerald-100 text-emerald-700'
    if (n < 0) return 'bg-rose-100 text-rose-700'
    return 'bg-amber-100 text-amber-800'
}

/* ══════════════════════════════════════════════════
   CHARTS AUTOMÁTICOS (HISTÓRICO DA TABELA PRINCIPAL + MÊS EM ANÁLISE)
   ══════════════════════════════════════════════════ */
const CHART_FONT = { family: 'ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif' }

const destroyCharts = () => {
    Object.keys(charts).forEach(k => { if (charts[k]) { charts[k].destroy(); charts[k] = null } })
}

const initCharts = () => {
    destroyCharts()

    // 1. Gráfico: Evolução da Carteira (Histórico da Tabela Principal + Mês em Análise)
    if (chartBalancoRef.value && historicoOrdenado.value.length > 0) {
        const labels = labelsGraficosAutomaticos.value
        const data = historicoOrdenado.value.map(i => Number(i.BalancoValor) || 0)

        const ctx = chartBalancoRef.value.getContext('2d')
        const grad = ctx.createLinearGradient(0, 0, 0, 220)
        grad.addColorStop(0, 'rgba(37, 99, 235, 0.35)')
        grad.addColorStop(1, 'rgba(37, 99, 235, 0.0)')

        charts.balanco = new Chart(chartBalancoRef.value, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Carteira Total',
                    data,
                    borderColor: '#2563EB',
                    backgroundColor: grad,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563EB',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0B2E4F', padding: 10,
                        titleFont: { ...CHART_FONT, size: 11 },
                        bodyFont: { ...CHART_FONT, size: 12, weight: 'bold' },
                        callbacks: { label: c => ' Carteira: ' + formatarNumero(c.parsed.y) + ' Kz' }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { ...CHART_FONT, size: 10 }, color: '#64748b' } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { ...CHART_FONT, size: 10 }, color: '#64748b', callback: v => formatarCompacto(v) } }
                }
            }
        })
    }

    // 2. Gráfico: Fluxo (Desembolsos e Reembolsos 12 Meses)
    if (chartFluxoRef.value && props.desembolsosReembolsos12MesesRD?.length) {
        const labels = props.desembolsosReembolsos12MesesRD.map(m => m.NomeDoMes)
        const desemb = props.desembolsosReembolsos12MesesRD.map(m => Number(m.DesembolsoValor) || 0)
        const reemb  = props.desembolsosReembolsos12MesesRD.map(m => Number(m.ReembolsoValor) || 0)

        charts.fluxo = new Chart(chartFluxoRef.value, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Desembolso', data: desemb, backgroundColor: '#10B981', borderRadius: 6, maxBarThickness: 20 },
                    { label: 'Reembolso',  data: reemb,  backgroundColor: '#F43F5E', borderRadius: 6, maxBarThickness: 20 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top', align: 'end',
                        labels: { font: { ...CHART_FONT, size: 10 }, boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle' }
                    },
                    tooltip: {
                        backgroundColor: '#0B2E4F', padding: 10,
                        titleFont: { ...CHART_FONT, size: 11 },
                        bodyFont: { ...CHART_FONT, size: 11 },
                        callbacks: { label: c => ' ' + c.dataset.label + ': ' + formatarNumero(c.parsed.y) + ' Kz' }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { ...CHART_FONT, size: 9 }, color: '#64748b' } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { ...CHART_FONT, size: 10 }, color: '#64748b', callback: v => formatarCompacto(v) } }
                }
            }
        })
    }

    // 3. Gráfico: Qualidade da Carteira (Histórico da Tabela Principal + Mês em Análise)
    if (chartQualidadeRef.value && historicoOrdenado.value.length > 0) {
        const labels = labelsGraficosAutomaticos.value
        const npl = historicoOrdenado.value.map(i => Number(i.NPLPercentual) || 0)
        const p1  = historicoOrdenado.value.map(i => Number(i.PAR1Percentual) || 0)
        const p30 = historicoOrdenado.value.map(i => Number(i.PAR30Percentual) || 0)

        charts.qualidade = new Chart(chartQualidadeRef.value, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'NPL',    data: npl, borderColor: '#E11D48', backgroundColor: 'transparent', borderWidth: 2.5, tension: 0.35, pointRadius: 3.5 },
                    { label: 'PAR 1',  data: p1,  borderColor: '#F97316', backgroundColor: 'transparent', borderWidth: 2, tension: 0.35, pointRadius: 3.5 },
                    { label: 'PAR 30', data: p30, borderColor: '#F59E0B', backgroundColor: 'transparent', borderWidth: 2, tension: 0.35, pointRadius: 3.5 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top', align: 'end',
                        labels: { font: { ...CHART_FONT, size: 10 }, boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle' }
                    },
                    tooltip: {
                        backgroundColor: '#0B2E4F', padding: 10,
                        titleFont: { ...CHART_FONT, size: 11 },
                        bodyFont: { ...CHART_FONT, size: 11 },
                        callbacks: { label: c => ' ' + c.dataset.label + ': ' + formatarPercentual(c.parsed.y) }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { ...CHART_FONT, size: 10 }, color: '#64748b' } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { ...CHART_FONT, size: 10 }, color: '#64748b', callback: v => v + '%' } }
                }
            }
        })
    }

    // 4. Gráfico: Distribuição por Localidade / Centros de Custo (Top 8 até ao último período)
    if (chartAgenciasRef.value) {
        let labels = []
        let dadosValores = []

        if (props.riscoExposicaoCentrosCustoRD && props.riscoExposicaoCentrosCustoRD.length > 0) {
            const ccItens = [...props.riscoExposicaoCentrosCustoRD]
                .filter(c => Number(c.CarteiraTotalCapital) > 0)
                .sort((a, b) => Number(b.CarteiraTotalCapital) - Number(a.CarteiraTotalCapital))
                .slice(0, 8)

            labels = ccItens.map(i => i.CentroCusto)
            dadosValores = ccItens.map(i => Number(i.CarteiraTotalCapital))
        } else if (props.agenciasRD && props.agenciasRD.length > 0) {
            const itens = [...props.agenciasRD]
                .filter(a => !a.IsTotalizador && Number(a.BalancoValor) > 0)
                .sort((a, b) => Number(b.BalancoValor) - Number(a.BalancoValor))
                .slice(0, 8)

            labels = itens.map(i => i.NomeLocal)
            dadosValores = itens.map(i => Number(i.BalancoValor))
        }

        if (labels.length > 0) {
            const palette = ['#2563EB', '#10B981', '#F59E0B', '#E11D48', '#8B5CF6', '#06B6D4', '#F97316', '#64748B']
            charts.agencias = new Chart(chartAgenciasRef.value, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: dadosValores,
                        backgroundColor: palette,
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: { ...CHART_FONT, size: 10 }, boxWidth: 10, boxHeight: 10,
                                usePointStyle: true, pointStyle: 'circle', padding: 8
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0B2E4F', padding: 10,
                            titleFont: { ...CHART_FONT, size: 11 },
                            bodyFont: { ...CHART_FONT, size: 11 },
                            callbacks: { label: c => ' ' + c.label + ': ' + formatarNumero(c.parsed) + ' Kz' }
                        }
                    }
                }
            })
        }
    }
}

onMounted(() => nextTick(initCharts))

watch(
    () => [props.historicoRD, props.desembolsosReembolsos12MesesRD, props.agenciasRD, props.riscoExposicaoCentrosCustoRD],
    () => nextTick(initCharts),
    { deep: true }
)

onBeforeUnmount(destroyCharts)

/* ══════════════════════════════════════════════════
   IMPRESSÃO A4 (HTML PROFISSIONAL REUTILIZADO PARA EMAIL)
   ══════════════════════════════════════════════════ */
const EMAIL_REMETENTE = 'desenvolvimento@kixicredito.ao'

const CSS_IMPRESSAO = `
    @page {
        size: A4 landscape;
        margin: 10mm 8mm 12mm 8mm;
        @bottom-left  { content: "Kixi Crédito Angola · Documento confidencial"; font: 7pt 'Segoe UI', Arial, sans-serif; color: #64748b; }
        @bottom-right { content: "Página " counter(page) " de " counter(pages); font: 7pt 'Segoe UI', Arial, sans-serif; color: #64748b; }
    }
    * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    html, body { margin: 0 !important; padding: 0 !important; background: #fff !important; color: #0f172a;
        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif; font-size: 10px; line-height: 1.35;
        -webkit-font-smoothing: antialiased; }

    .doc-header { display: flex; align-items: center; justify-content: space-between;
        border-bottom: 2.5px solid #0B2E4F; padding: 0 0 6px; margin-bottom: 10px; }
    .doc-header .titulo { font-size: 15px; font-weight: 800; color: #0B2E4F; letter-spacing: .02em; text-transform: uppercase; margin: 0; }
    .doc-header .sub { font-size: 9.5px; color: #475569; margin-top: 2px; }
    .doc-header .meta { text-align: right; font-size: 9px; color: #475569; }
    .doc-header .meta strong { color: #0B2E4F; font-size: 10.5px; }

    .print-root, .print-root > div { box-shadow: none !important; border: 0 !important; padding: 0 !important; border-radius: 0 !important; background: #fff !important; }
    .print-root .space-y-8 > * + * { margin-top: 14px !important; }
    .print-root .space-y-4 > * + * { margin-top: 8px !important; }
    .overflow-x-auto { overflow: visible !important; }
    .sticky { position: static !important; }
    .shadow-sm, .shadow-xl, .shadow-lg, .shadow-md { box-shadow: none !important; }
    .print\\:block, .hidden { display: none !important; }
    .rounded-2xl, .rounded-xl, .rounded, .rounded-tl-xl, .rounded-tr-xl { border-radius: 0 !important; }

    table { width: 100% !important; border-collapse: collapse !important; font-size: 11px !important; page-break-inside: auto; }
    th, td { padding: 3.5px 5px !important; vertical-align: middle; }
    thead { display: table-header-group; }
    tfoot { display: table-row-group; }
    tr { page-break-inside: avoid; break-inside: avoid; }
    th { font-weight: 700 !important; letter-spacing: .02em; }
    td { font-variant-numeric: tabular-nums; }
    .text-\\[8px\\], .text-\\[9px\\] { font-size: 9px !important; }
    .text-\\[10px\\] { font-size: 10px !important; }
    .text-\\[11px\\], .text-xs { font-size: 11px !important; }
    h2 { font-size: 14px !important; margin: 0 !important; }
    h2 + p, section p { font-size: 10px !important; }

    section { break-inside: auto; page-break-inside: auto; margin: 0 0 12px; }
    section > div:first-child { break-after: avoid; page-break-after: avoid; }
    .quebra-pagina { break-before: page; page-break-before: always; }
`

const gerarHtmlRelatorio = ({ paraEmail = false } = {}) => {
    const estilos = paraEmail
        ? ''
        : Array.from(document.querySelectorAll('link[rel="stylesheet"], style')).map(s => s.outerHTML).join('')
    const tituloDoc = `RD Express ${ultimoMesNome.value} [KIXICREDITO ANGOLA · ${tituloDireccao.value}]`
    const agora = new Date().toLocaleString('pt-PT', { dateStyle: 'short', timeStyle: 'short' })

    const clone = areaTabelas.value.cloneNode(true)
    clone.querySelectorAll('canvas, button').forEach(el => el.remove())
    clone.classList.add('print-root')

    return `<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <title>${tituloDoc}</title>
    ${estilos}
    <style>${CSS_IMPRESSAO}</style>
</head>
<body>
    <div class="doc-header">
        <div>
            <h1 class="titulo">Reporting Balance · Kixi Crédito Angola</h1>
            <div class="sub">Relatório Diário Executivo · ${tituloDireccao.value}</div>
        </div>
        <div class="meta">
            <div><strong>Data de referência: ${ultimoMesNome.value}</strong></div>
            <div>Emitido em ${agora}</div>
        </div>
    </div>
    ${clone.outerHTML}
</body>
</html>`
}

const imprimirA4 = () => {
    if (!areaTabelas.value) return

    const iframe = document.createElement('iframe')
    iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;'
    document.body.appendChild(iframe)
    const doc = iframe.contentWindow.document

    doc.open()
    doc.write(gerarHtmlRelatorio())
    doc.close()

    const limpar = () => { if (iframe.parentNode) document.body.removeChild(iframe) }

    setTimeout(() => {
        // Ajusta a escala para que a tabela mais larga caiba na largura útil do A4 paisagem (≈ 281mm ≈ 1062px)
        const larguraUtil = 1062
        const maisLarga = Math.max(...Array.from(doc.querySelectorAll('table')).map(t => t.scrollWidth), larguraUtil)
        if (maisLarga > larguraUtil) {
            doc.body.style.zoom = String(Math.max(0.6, larguraUtil / maisLarga))
        }
        iframe.contentWindow.focus()
        iframe.contentWindow.onafterprint = limpar
        iframe.contentWindow.print()
        setTimeout(limpar, 60000)
    }, 700)
}

/* ══════════════════════════════════════════════════
   HTML DO EMAIL (ESTRUTURA TABLE-BASED, ESTILOS INLINE)
   ══════════════════════════════════════════════════ */
const esc = (v) => String(v ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')

const EM_COR = { azul: '#0B2E4F', azul2: '#103E68', verde: '#047857', rosa: '#be123c', ambar: '#b45309', cinza: '#64748b' }
const EM_BASE = 'padding:6px 9px;border:1px solid #dbe3ec;font-size:12.5px;line-height:1.35;'

const emTh = (t, st = '') => `<th style="background:${EM_COR.azul2};color:#ffffff;font-weight:700;text-align:center;white-space:nowrap;${EM_BASE}${st}">${t}</th>`
const emTd = (t, st = '') => `<td style="text-align:right;color:#1e293b;${EM_BASE}${st}">${t}</td>`
const emTdL = (t, st = '') => emTd(t, `text-align:left;font-weight:700;${st}`)
const emTr = (cels, st = '') => `<tr style="${st}">${cels.join('')}</tr>`

const emTabela = (heads, linhas, barra = '') => `
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;width:100%;">
  <thead>
    ${barra ? `<tr><th colspan="${heads.length}" style="background:${EM_COR.azul};color:#ffffff;text-align:left;font-size:13px;letter-spacing:.04em;text-transform:uppercase;padding:8px 10px;border:1px solid ${EM_COR.azul};">${barra}</th></tr>` : ''}
    <tr>${heads.map(h => emTh(h)).join('')}</tr>
  </thead>
  <tbody>${linhas.join('')}</tbody>
</table>`

const emSecao = (titulo, sub, conteudo) => `
<tr><td style="padding:26px 28px 6px;">
  <div style="border-left:5px solid ${EM_COR.azul};padding:2px 0 2px 12px;">
    <div style="font-size:17px;font-weight:700;color:#0f172a;">${titulo}</div>
    ${sub ? `<div style="font-size:12.5px;color:${EM_COR.cinza};margin-top:2px;">${sub}</div>` : ''}
  </div>
</td></tr>
<tr><td style="padding:8px 28px 4px;"><div style="overflow-x:auto;">${conteudo}</div></td></tr>`

const emBadge = (valor, sufixo = '') => {
    const n = Number(valor)
    if (valor === null || valor === undefined || !isFinite(n)) return '<span style="color:#94a3b8;">—</span>'
    const [bg, fg] = n > 0 ? ['#d1fae5', EM_COR.verde] : n < 0 ? ['#ffe4e6', EM_COR.rosa] : ['#fef3c7', '#92400e']
    return `<span style="display:inline-block;background:${bg};color:${fg};font-weight:700;font-size:12px;padding:1px 9px;border-radius:10px;">${Math.round(n)}${esc(sufixo)}</span>`
}

const emMapItem = (a) => ({
    oa: a.OA_Qtd, pa: a.PA_Qtd, balanco: a.BalancoValor,
    credTot: a.CreditosTotal_Qtd, credNov: a.CreditosNovos_Qtd,
    cliTot: a.ClientesTotal_Qtd, cliNov: a.ClientesNovos_Qtd,
    par1: a.PAR1_Valor, par1p: a.PAR1_Percentual, par30: a.PAR30_Valor, par30p: a.PAR30_Percentual,
    des: a.DesembolsoValor, ta: a.TempoAtendimentodias, ree: a.ReembolsoValor,
    prov: a.ProvisaoValor, provClasse: a.ProvisaoClasseRisco,
    gestao: a.GestaoValor, gestaoSufixo: a.GestaoSufixo
})

const emCelsMetricas = (m, st = '') => [
    emTd(`${m.oa ?? '—'}/${m.pa ?? '—'}`, `text-align:center;${st}`),
    emTd(formatarNumero(m.balanco), `font-weight:700;${st}`),
    emTd(`${formatarInt(m.credTot)} | <b style="color:#4338ca;">${formatarInt(m.credNov)}</b>`, st),
    emTd(`${formatarInt(m.cliTot)} | <b style="color:#0e7490;">${formatarInt(m.cliNov)}</b>`, st),
    emTd(`${formatarK(m.par1)}${m.par1p != null ? ` <span style="color:${EM_COR.ambar};font-size:11px;font-weight:700;">(${Math.round(m.par1p)}%)</span>` : ''}`, st),
    emTd(`${formatarK(m.par30)}${m.par30p != null ? ` <span style="color:${EM_COR.rosa};font-size:11px;font-weight:700;">(${Math.round(m.par30p)}%)</span>` : ''}`, st),
    emTd(`${formatarNumero(m.des)}${m.ta != null ? ` <span style="color:${EM_COR.cinza};font-size:11px;">| ${String(Math.round(m.ta)).padStart(2, '0')}</span>` : ''}`, `color:${EM_COR.verde};font-weight:600;${st}`),
    emTd(formatarNumero(m.ree), `color:${EM_COR.rosa};${st}`),
    emTd(`${formatarNumero(m.prov)}${m.provClasse ? ` <b>(${esc(m.provClasse)})</b>` : ''}`, st),
    emTd(emBadge(m.gestao, m.gestaoSufixo), `text-align:center;${st}`)
]

const EM_HEADS_METRICAS = ['OA/PA', 'Carteira', 'Créditos | Novos', 'Clientes | Novos', 'PAR 1', 'PAR 30', 'Desembolsos | TA', 'Reembolsos', 'Provisão', 'Gestão']

const emTotalAgencias = (lista) => {
    const base = lista.filter(a => !a.IsTotalizador)
    const its = base.length ? base : lista
    const soma = (k) => its.reduce((s, a) => s + (Number(a[k]) || 0), 0)
    const balanco = soma('BalancoValor')
    const tas = its.filter(a => a.TempoAtendimentodias != null)
    const ges = its.filter(a => a.GestaoValor != null)
    return {
        oa: soma('OA_Qtd'), pa: soma('PA_Qtd'), balanco,
        credTot: soma('CreditosTotal_Qtd'), credNov: soma('CreditosNovos_Qtd'),
        cliTot: soma('ClientesTotal_Qtd'), cliNov: soma('ClientesNovos_Qtd'),
        par1: soma('PAR1_Valor'), par1p: balanco > 0 ? soma('PAR1_Valor') / balanco * 100 : 0,
        par30: soma('PAR30_Valor'), par30p: balanco > 0 ? soma('PAR30_Valor') / balanco * 100 : 0,
        des: soma('DesembolsoValor'), ree: soma('ReembolsoValor'), prov: soma('ProvisaoValor'),
        ta: tas.length ? tas.reduce((s, a) => s + Number(a.TempoAtendimentodias), 0) / tas.length : null,
        gestao: ges.length ? ges.reduce((s, a) => s + Number(a.GestaoValor), 0) / ges.length : null,
        gestaoSufixo: ges.find(a => a.GestaoSufixo)?.GestaoSufixo || ''
    }
}

const emConsolidado = () => {
    const u = ultimoRegistro.value || {}
    const meses = historicoAteUltimoPeriodo.value
    const heads = ['Indicador', ...colunasMeses.value.map(m => esc(m.label)), '▲', '▼', esc(ultimoMesNome.value || 'Atual'), 'Var.']
    const varCel = (v, invertido = false) => {
        const n = Number(v) || 0
        const bom = invertido ? n <= 0 : n >= 0
        const seta = invertido ? (n <= 0 ? '▼' : '▲') : (n >= 0 ? '▲' : '▼')
        return emTd(`${seta} ${formatarPercentual(Math.abs(n))}`, `font-weight:700;color:${bom ? EM_COR.verde : EM_COR.rosa};`)
    }
    const ups = (v) => emTd(formatarNumero(v), 'background:#ecfdf5;color:#047857;font-weight:700;')
    const dns = (v) => emTd(formatarNumero(v), 'background:#fff1f2;color:#be123c;font-weight:700;')
    const upsI = (v) => emTd(formatarInt(v), 'background:#ecfdf5;color:#047857;font-weight:700;')
    const dnsI = (v) => emTd(formatarInt(v), 'background:#fff1f2;color:#be123c;font-weight:700;')
    const corRisco = { npl: '#be123c', par1: '#c2410c', par30: '#b45309' }
    const corPerc = { TaxaReembolsoPercentual: '#047857', TaxaCumprimentoPercentual: '#1d4ed8', TaxaRecuperacaoPercentual: '#4338ca' }
    const rows = []

    rows.push(emTr([
        emTdL('1. Carteira'),
        ...meses.map(i => emTd(formatarNumero(i.BalancoValor))),
        ups(u.Desembolsos), dns(u.Reembolso),
        emTd(formatarNumero(u.BalancoValor), 'background:#fffbeb;color:#92400e;font-weight:700;'),
        varCel(u.VariacaoPercentual)
    ]))
    rows.push(emTr([
        emTdL('2. Créditos | Novos<br><span style="font-size:11px;font-weight:400;color:#64748b;">Taxa Juros ≤ Kz 2.000</span>'),
        ...meses.map(i => emTd(`${formatarInt(i.CreditosQuantidade)} | <b style="color:#4338ca;">${formatarInt(i.CreditosNovos)}</b>`)),
        upsI(u.CreditosNovos_Mais), dnsI(u.CreditosNovos_Menos),
        emTd(`${formatarInt(u.CreditosQuantidade)} | <b style="color:#4338ca;">${formatarInt(u.CreditosNovos)}</b>`, 'font-weight:700;'),
        varCel(u.VariacaoCreditosNovos)
    ]))
    rows.push(emTr([
        emTdL('3. Clientes | Novos<br><span style="font-size:11px;font-weight:400;color:#64748b;">≤ Kz 2.000</span>'),
        ...meses.map(i => emTd(`${formatarInt(i.ClientesQuantidade)} | <b style="color:#0e7490;">${formatarInt(i.ClientesNovos)}</b>`)),
        upsI(u.ClientesNovos_Mais), dnsI(u.ClientesNovos_Menos),
        emTd(`${formatarInt(u.ClientesQuantidade)} | <b style="color:#0e7490;">${formatarInt(u.ClientesNovos)}</b>`, 'font-weight:700;'),
        varCel(u.VariacaoClientesNovos)
    ]))
    linhasRisco.forEach(l => {
        const c = corRisco[l.key] || '#1e293b'
        const dupla = (reg) => `<b style="color:${c};">${formatarPercentual(reg?.[l.percKey])}</b><br><span style="font-size:11px;color:#64748b;">${formatarK(reg?.[l.valKey])}</span>`
        rows.push(emTr([
            emTdL(`${l.numero}. ${esc(l.label)}`),
            ...meses.map(i => emTd(dupla(i))),
            ups(u[l.maisKey]), dns(u[l.menosKey]),
            emTd(dupla(u), 'font-weight:700;'),
            varCel(u[l.variacaoKey], true)
        ]))
    })
    const provSub = (d, p) => `<br><span style="font-size:11px;font-weight:400;color:#64748b;">${esc(formatarProvisaoCobertura(d, p))}</span>`
    rows.push(emTr([
        emTdL('7. Provisão'),
        ...meses.map(i => emTd(`${formatarNumero(i.ProvisaoValor)}${provSub(i.ProvisaoDiasAtraso, i.ProvisaoCoberturaPerc)}`, `color:${EM_COR.rosa};font-weight:600;`)),
        ups(u.Provisao_Mais), dns(u.Provisao_Menos),
        emTd(`${formatarNumero(u.ProvisaoValor)}${provSub(u.ProvisaoDiasAtraso, u.ProvisaoCoberturaPerc)}`, `color:${EM_COR.rosa};font-weight:700;`),
        varCel(u.VariacaoProvisao, true)
    ], 'background:#fff7f8;'))
    linhasPercentuais.forEach(l => {
        const c = corPerc[l.key] || '#1e293b'
        rows.push(emTr([
            emTdL(`${l.numero}. ${esc(l.label)}`),
            ...meses.map(i => emTd(formatarPercentual(i[l.key]), `color:${c};font-weight:700;`)),
            ups(u[l.maisKey]), dns(u[l.menosKey]),
            emTd(formatarPercentual(u[l.key]), `color:${c};font-weight:700;`),
            varCel(u[l.variacaoKey], !!l.invertido)
        ]))
    })
    return emTabela(heads, rows)
}

const emAgencias = (titulo, lista) => {
    const linhas = lista.map(a => {
        const st = a.IsTotalizador ? 'background:#fffbeb;font-weight:700;' : ''
        const peso = a.PesoPercentual ? ` <span style="color:#64748b;font-weight:400;font-size:11px;">(${Math.round(a.PesoPercentual)}%)</span>` : ''
        return emTr([emTdL(`${esc(a.NomeLocal)}${peso}`, st), ...emCelsMetricas(emMapItem(a), st)])
    })
    const t = emTotalAgencias(lista)
    linhas.push(emTr([emTdL(`Total ${esc(titulo)}`, 'background:#0B2E4F;color:#ffffff;'), ...emCelsMetricas(t, 'background:#0B2E4F;color:#ffffff;')]))
    return emTabela(['Localidade', ...EM_HEADS_METRICAS], linhas, esc(titulo))
}

const emProdutos = () => {
    const linhas = []
    produtosAgrupados.value.forEach(g => {
        const st = 'background:#e0e7ff;font-weight:700;color:#1e1b4b;'
        const mt = { ...emMapItem(g.total), ta: g.total.TempoAtendimentoMedia, gestao: g.total.GestaoMedia, gestaoSufixo: g.total.GestaoSufixo }
        linhas.push(emTr([
            emTdL(`${esc(g.gestor)}<br><span style="font-size:11px;font-weight:500;color:#4338ca;">${esc(g.tipoDireccao)} · ${g.itens.length} ${g.itens.length === 1 ? 'produto' : 'produtos'}</span>`, st),
            ...emCelsMetricas(mt, st)
        ]))
        g.itens.forEach(p => linhas.push(emTr([emTdL(`&nbsp;&nbsp;• ${esc(p.NomeProduto)}`, 'font-weight:500;'), ...emCelsMetricas(emMapItem(p))])))
    })
    return emTabela(['Produto', ...EM_HEADS_METRICAS], linhas, 'Detalhamento por Produto')
}

const emMovimento = () => {
    const heads = ['Período Prestação', 'Cap. B.I.', 'Cap. Desembolsado', 'Cap. Reembolsado', 'Cap. B.F.', 'Juros B.I.', 'Juros Desembolsado', 'Juros Reembolsado', 'Juros B.F.', 'Total Recuperado', '%Recup. / TxRecup.']
    const linhas = linhasMovimentoPrestacao.value.map(mp => {
        const st = (mp.IsTotal || mp.IsIntermediario) ? 'background:#103E68;color:#ffffff;font-weight:700;' : ''
        return emTr([
            emTd(esc(mp.PeriodoPrestacao), `text-align:center;font-weight:700;${st}`),
            ...['CapIniValor', 'CapDesValor', 'CapReeValor', 'CapFimValor', 'JurIniValor', 'JurDesValor', 'JurReeValor', 'JurFimValor'].map(k => emTd(formatarNumero(mp[k]), st)),
            emTd(formatarNumero(mp.TotalRecuperado), `font-weight:700;${st}`),
            emTd(`${Math.round(Number(mp.PctRecuperado ?? 0))}% / ${Math.round(Number(mp.TxRecuperacao ?? 0))}%`, `text-align:center;font-weight:700;${st}`)
        ])
    })
    return emTabela(heads, linhas)
}

const emFluxo12Meses = () => {
    const mm = maxMinFluxo.value
    const cel = (val, max, min, corBase) => {
        const n = Number(val) || 0
        const [bg, tag] = n === max ? ['background:#a7f3d0;font-weight:800;', '<span style="background:#047857;color:#fff;font-size:10px;padding:0 4px;border-radius:2px;">▲ MAX</span> ']
            : n === min ? ['background:#fecdd3;font-weight:800;', '<span style="background:#be123c;color:#fff;font-size:10px;padding:0 4px;border-radius:2px;">▼ MIN</span> ']
            : ['font-weight:600;', '']
        return emTd(`${tag}${formatarNumero(val)}`, `color:${corBase};${bg}`)
    }
    const blocos = blocosFluxo6Meses.value.map((bloco, idx) => emTabela(
        [`Fluxo / Mês (${idx === 0 ? '1–6' : '7–12'})`, ...bloco.map(m => esc(m.NomeDoMes))],
        [
            emTr([emTdL('▼ Desembolso (+)', 'background:#ecfdf5;color:#047857;'), ...bloco.map(m => cel(m.DesembolsoValor, mm.maxDesemb, mm.minDesemb, EM_COR.verde))]),
            emTr([emTdL('▲ Reembolso (−)', 'background:#fff1f2;color:#be123c;'), ...bloco.map(m => cel(m.ReembolsoValor, mm.maxReemb, mm.minReemb, EM_COR.rosa))])
        ]
    )).join('<div style="height:10px;"></div>')
    const tot = totaisAcumuladosFluxo.value
    const totais = emTabela(['Total Acumulado (12 Meses)', 'Valor'], [
        emTr([emTdL('Total Desembolso', 'background:#ecfdf5;color:#047857;'), emTd(formatarNumero(tot.totalDesembolso), 'font-weight:800;color:#064e3b;')]),
        emTr([emTdL('Total Reembolso', 'background:#fff1f2;color:#be123c;'), emTd(formatarNumero(tot.totalReembolso), 'font-weight:800;color:#881337;')])
    ])
    return `${blocos}<div style="height:10px;"></div>${totais}`
}

const emProvisoes = () => {
    const cor = (n) => (Number(n) < 0 ? '#2563eb' : EM_COR.rosa)
    const celsDif = (t, st = '') => [
        emTd(formatarNumero(t.difMensal), `color:${cor(t.difMensal)};${st}`),
        emTd(formatarNumero(t.difAnual), `color:${cor(t.difAnual)};${st}`),
        emTd(formatarPercentual(t.varAno), `text-align:center;color:${cor(t.varAno)};font-weight:700;${st}`)
    ]
    const tg = totalGeralProvisoes.value
    const linhas = [
        emTr([
            emTdL('KIXICRÉDITO ANGOLA', 'background:#dbeafe;color:#0B2E4F;text-transform:uppercase;'),
            emTd(`${tg.percDado}%`, 'text-align:center;background:#dbeafe;font-weight:700;'),
            emTd(formatarNumero(tg.fechoAA), 'background:#dbeafe;font-weight:700;'),
            emTd(formatarNumero(tg.mesAnterior), 'background:#dbeafe;font-weight:700;'),
            emTd(formatarNumero(tg.mesActual), 'background:#dbeafe;font-weight:700;'),
            ...celsDif(tg, 'background:#dbeafe;font-weight:700;')
        ])
    ]
    provisoesAgrupadas.value.forEach(g => {
        const st = 'background:#fef9e7;font-weight:700;'
        linhas.push(emTr([
            emTdL(`${esc(g.tipoDireccao)}<br><span style="font-size:11px;font-weight:600;color:#475569;">${esc(g.gestor)}</span>`, st),
            emTd(`${g.total.percDado}%`, `text-align:center;${st}`),
            emTd(formatarNumero(g.total.fechoAA), st),
            emTd(formatarNumero(g.total.mesAnterior), st),
            emTd(formatarNumero(g.total.mesActual), st),
            ...celsDif(g.total, st)
        ]))
        g.itens.forEach(i => linhas.push(emTr([
            emTdL(`&nbsp;&nbsp;${esc(i.NomeProduto)}`, 'font-weight:500;'),
            emTd(`${i.PercentagemDado !== null && i.PercentagemDado !== undefined ? i.PercentagemDado : 0}%`, 'text-align:center;'),
            emTd(formatarNumero(i.ProvisaoValorFechoAnoAnterior)),
            emTd(formatarNumero(i.ProvisaoValorMesAnterior)),
            emTd(formatarNumero(i.ProvisaoValorMesActual), 'font-weight:600;'),
            emTd(formatarNumero(i.ProvisaoDiferencaMensal), `color:${cor(i.ProvisaoDiferencaMensal)};font-weight:600;`),
            emTd(formatarNumero(i.ProvisaoDiferencaAnual), `color:${cor(i.ProvisaoDiferencaAnual)};font-weight:600;`),
            emTd(formatarPercentual(i.PercentVariaAnual), `text-align:center;color:${cor(i.PercentVariaAnual)};font-weight:600;`)
        ])))
    })
    return emTabela(['Agrupamento', '%', esc(labelFechoAA.value), esc(labelMesAnterior.value), esc(labelMesActual.value), 'Dif. Mensal', 'Dif. Anual', 'Var. Ano'], linhas)
}

const emWriteOff = () => {
    const heads = ['Período WO', 'B. Inicial', `&lt;= ${esc(cabecalhosWO.value.anterior)}`, ...cabecalhosWO.value.meses.map(esc), 'Recuperado', 'B. Final', '%KR']
    const linhas = linhasWO.value.map(w => {
        const st = w.IsTotal ? 'background:#103E68;color:#ffffff;font-weight:700;' : ''
        return emTr([
            emTd(esc(w.EtiquetaPeriodoWO), `text-align:center;font-weight:600;${st}`),
            emTd(formatarNumero(w.BalancoInicial), st),
            emTd(formatarNumero(w.RecuperadoAcumuladoAnterior), st),
            ...w.mesesValores.map(v => emTd(formatarNumero(v), st)),
            emTd(formatarNumero(w.TotalRecuperado), `font-weight:700;${st}`),
            emTd(formatarNumero(w.BalancoFinal), w.IsTotal ? st : 'background:#fff9c4;color:#0f172a;font-weight:700;'),
            emTd(formatarPercentual(w.PctKR), `text-align:center;font-weight:700;${st}`)
        ])
    })
    return emTabela(heads, linhas)
}

const emTopClientes = () => {
    const heads = ['Pos.', 'LNR', 'Nome do Cliente', 'Produto', 'Direção', 'Centro de Custo', 'Dias Atraso', 'Saldo Capital (Kz)', 'Saldo Juros (Kz)', 'Total em Risco (Kz)', 'Provisão Constituída']
    const linhas = listaTopClientesAtiva.value.map(c => {
        const critico = Number(c.DiasAtrasoACT) >= 1000
        const dir = c.ccDireccao === 'DOP' ? ['#dbeafe', '#1e40af'] : ['#f3e8ff', '#6b21a8']
        return emTr([
            emTd(esc(c.Posicao ?? '—'), 'text-align:center;font-weight:700;color:#64748b;'),
            emTd(esc(c.CodigoCliente), 'text-align:center;font-family:Consolas,monospace;'),
            emTdL(esc(c.NomeCliente), 'white-space:nowrap;'),
            emTd(esc(c.Produto), 'text-align:left;'),
            emTd(`<span style="display:inline-block;background:${dir[0]};color:${dir[1]};font-weight:700;font-size:11px;padding:1px 7px;border-radius:4px;">${esc(c.ccDireccao || '—')}</span>`, 'text-align:center;'),
            emTd(esc(c.CentroCusto), 'text-align:left;'),
            emTd(`<span style="display:inline-block;background:${critico ? '#ffe4e6' : '#fef3c7'};color:${critico ? '#9f1239' : '#92400e'};font-weight:800;font-size:11.5px;padding:1px 8px;border-radius:4px;">${esc(c.DiasAtrasoACT ?? 0)} d</span>`, 'text-align:center;'),
            emTd(formatarNumero(c.SaldoCapitalACT)),
            emTd(formatarNumero(c.SaldoJurosACT), 'color:#64748b;'),
            emTd(formatarNumero(c.SaldoTotalEmRisco), 'background:#fff1f2;color:#be123c;font-weight:800;'),
            emTd(formatarNumero(c.ProvisaoACT))
        ])
    })
    return emTabela(heads, linhas)
}

const emCentrosCusto = () => {
    const heads = ['Pos.', 'Ponto de Atendimento (Centro de Custo)', 'Província', 'Clientes Ativos', 'Em Atraso', 'Carteira Total Capital', 'Carteira Vencida', 'PAR %', 'Provisão Total', 'Máx. Dias Atraso']
    const linhas = centrosCustoOrdenadosPorRisco.value.map(i => {
        const longo = Number(i.MaxDiasAtraso) > 365
        return emTr([
            emTd(esc(i.PosicaoRisco ?? '—'), 'text-align:center;font-weight:700;color:#64748b;'),
            emTdL(esc(i.CentroCusto), 'white-space:nowrap;'),
            emTd(esc(i.Provincia || '—'), 'text-align:left;'),
            emTd(formatarInt(i.TotalClientesAtivos), 'text-align:center;'),
            emTd(formatarInt(i.ClientesEmAtraso), 'text-align:center;background:#fff1f2;color:#be123c;font-weight:700;'),
            emTd(formatarNumero(i.CarteiraTotalCapital)),
            emTd(formatarNumero(i.CarteiraVencidaCapital), 'color:#be123c;font-weight:700;'),
            emTd(formatarPercentual(i.PAR_Percentual), 'background:#fffbeb;color:#78350f;font-weight:800;'),
            emTd(formatarNumero(i.ProvisaoTotal)),
            emTd(`<span style="display:inline-block;background:${longo ? '#ffe4e6' : '#f1f5f9'};color:${longo ? '#9f1239' : '#334155'};font-weight:700;font-size:11.5px;padding:1px 8px;border-radius:4px;">${esc(i.MaxDiasAtraso ?? 0)} dias</span>`, 'text-align:center;')
        ])
    })
    return emTabela(heads, linhas)
}

const gerarHtmlEmail = () => {
    const u = ultimoRegistro.value || {}
    const ant = registroAnterior.value || {}
    const assunto = `RD Express ${ultimoMesNome.value} [KIXICREDITO ANGOLA · ${tituloDireccao.value}]`
    const agora = new Date().toLocaleString('pt-PT', { dateStyle: 'long', timeStyle: 'short' })

    const kpis = [
        { l: 'Carteira Total', v: formatarMoedaCompleta(u.BalancoValor), d: calcularDelta(u.BalancoValor, ant.BalancoValor), cor: '#1d4ed8' },
        { l: 'Desembolso', v: formatarMoedaCompleta(u.Desembolsos), d: calcularDelta(u.Desembolsos, ant.Desembolsos), cor: '#047857' },
        { l: 'Reembolso', v: formatarMoedaCompleta(u.Reembolso), d: calcularDelta(u.Reembolso, ant.Reembolso), cor: '#be123c' },
        { l: 'Créditos Novos', v: formatarInt(u.CreditosNovos), d: calcularDelta(u.CreditosNovos, ant.CreditosNovos), cor: '#b45309' },
        { l: 'NPL', v: formatarPercentual(u.NPLPercentual), d: calcularDelta(u.NPLPercentual, ant.NPLPercentual), cor: '#be123c', invert: true },
        { l: 'Taxa de Reembolso', v: formatarPercentual(u.TaxaReembolsoPercentual), d: calcularDelta(u.TaxaReembolsoPercentual, ant.TaxaReembolsoPercentual), cor: '#047857' }
    ]
    const kpiHtml = kpis.map(k => {
        const tem = k.d !== null && k.d !== undefined
        const bom = tem ? (k.invert ? k.d <= 0 : k.d >= 0) : null
        return `<td width="16.66%" valign="top" style="padding:4px;">
            <div style="border:1px solid #e2e8f0;border-top:4px solid ${k.cor};border-radius:8px;padding:10px 12px;background:#ffffff;">
                <div style="font-size:10.5px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#64748b;">${k.l}</div>
                <div style="font-size:16px;font-weight:800;color:#0f172a;margin-top:4px;">${k.v}</div>
                <div style="font-size:11.5px;font-weight:700;margin-top:3px;color:${tem ? (bom ? EM_COR.verde : EM_COR.rosa) : '#94a3b8'};">${tem ? `${k.d >= 0 ? '▲' : '▼'} ${Math.abs(Math.round(k.d))}% <span style="font-weight:400;color:#94a3b8;">vs mês ant.</span>` : '—'}</div>
            </div></td>`
    }).join('')

    const secoes = []
    secoes.push(emSecao(`Reporting Consolidado — KIXICRÉDITO ANGOLA`, esc(tituloDireccao.value) + ' · Histórico mensal consolidado até ao último período', emConsolidado()))
    if (agenciasCapital.value.length || agenciasProvincia.value.length) {
        secoes.push(emSecao('1. Distribuição da Carteira', 'Performance detalhada por localidade',
            [agenciasCapital.value.length ? emAgencias('Capital (Luanda)', agenciasCapital.value) : '',
             agenciasProvincia.value.length ? emAgencias('Províncias', agenciasProvincia.value) : ''].join('<div style="height:12px;"></div>')))
    }
    if (produtosAgrupados.value.length) secoes.push(emSecao('Detalhamento por Produto', 'Resultados por gestor e produto', emProdutos()))
    if (props.movimentoPrestacaoRD.length) secoes.push(emSecao('2. Movimento das Prestações', 'Detalhe das prestações Kixi Crédito', emMovimento()))
    if (props.desembolsosReembolsos12MesesRD.length) secoes.push(emSecao('3. Desembolsos e Reembolsos (Últimos 12 Meses)', 'Pico máximo (verde), mínimo (vermelho) e total acumulado', emFluxo12Meses()))
    if (provisoesAgrupadas.value.length) secoes.push(emSecao('4. Provisões', 'Evolução de provisões por agrupamento de produto', emProvisoes()))
    if (props.carteiraWriteOffRD.length) secoes.push(emSecao('5. Carteira Write-Off', 'Evolução e recuperação de ativos baixados', emWriteOff()))
    if (temTopClientes.value) secoes.push(emSecao(`6. ${esc(tituloSecaoTopClientes.value)}`, esc(subtituloSecaoTopClientes.value), emTopClientes()))
    if (centrosCustoOrdenadosPorRisco.value.length) secoes.push(emSecao('7. Exposição ao Risco por Centros de Custo', 'Pontos de atendimento por ordem de gravidade de risco', emCentrosCusto()))

    return `<!DOCTYPE html>
<html lang="pt-PT">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>${esc(assunto)}</title>
</head>
<body style="margin:0;padding:0;background:#eef2f7;font-family:'Segoe UI',Arial,Helvetica,sans-serif;color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#eef2f7;">
<tr><td align="center" style="padding:24px 10px;">
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="1100" style="width:100%;max-width:1100px;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #dbe3ec;">
    <tr><td style="background:#0B2E4F;background-image:linear-gradient(135deg,#0B2E4F,#1E5A8E);padding:26px 28px;">
        <div style="font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#6ee7b7;">Relatório Diário Executivo</div>
        <div style="font-size:24px;font-weight:800;color:#ffffff;margin-top:4px;">Reporting Balance · Kixi Crédito Angola</div>
        <div style="margin-top:10px;">
            <span style="display:inline-block;background:rgba(110,231,183,.18);border:1px solid rgba(110,231,183,.45);color:#a7f3d0;font-size:12px;font-weight:700;padding:3px 12px;border-radius:12px;">${esc(tituloDireccao.value)}</span>
            <span style="display:inline-block;color:#cfe3f5;font-size:13px;margin-left:10px;">Data de referência: <b style="color:#ffffff;">${esc(ultimoMesNome.value)}</b></span>
        </div>
    </td></tr>
    <tr><td style="padding:20px 24px 0;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;"><tr>${kpiHtml}</tr></table>
    </td></tr>
    ${secoes.join('\n')}
    <tr><td style="padding:28px;">
        <div style="border-top:2px solid #e2e8f0;padding-top:14px;font-size:12px;color:#64748b;line-height:1.6;">
            Relatório gerado em ${esc(agora)} e enviado por <b>${EMAIL_REMETENTE}</b>.<br>
            Kixi Crédito Angola · Documento confidencial, destinado exclusivamente ao destinatário.
        </div>
    </td></tr>
  </table>
</td></tr>
</table>
</body>
</html>`
}

/* ══════════════════════════════════════════════════
   ENVIAR POR EMAIL
   ══════════════════════════════════════════════════ */
const emailInputRef = ref(null)
const emailDialog = reactive({ aberto: false, destinatario: '', enviando: false, erro: '', sucesso: '' })

const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/
const emailValido = computed(() => REGEX_EMAIL.test(emailDialog.destinatario))

const abrirDialogEmail = () => {
    Object.assign(emailDialog, { aberto: true, erro: '', sucesso: '', enviando: false })
    nextTick(() => emailInputRef.value?.focus())
}

const fecharDialogEmail = () => {
    if (emailDialog.enviando) return
    emailDialog.aberto = false
}

const obterCsrf = () => {
    const meta = document.querySelector('meta[name="csrf-token"]')?.content
    if (meta) return { 'X-CSRF-TOKEN': meta }
    const m = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    return m ? { 'X-XSRF-TOKEN': decodeURIComponent(m[1]) } : {}
}

const enviarEmail = async () => {
    if (!emailValido.value || emailDialog.enviando || !areaTabelas.value) return
    emailDialog.erro = ''
    emailDialog.sucesso = ''
    emailDialog.enviando = true

    try {
        const resp = await fetch('/reports/rd/enviar-email', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...obterCsrf()
            },
            body: JSON.stringify({
                destinatario: emailDialog.destinatario,
                remetente: EMAIL_REMETENTE,
                direccao: String(props.direccaoActiva || 'GLOBAL').toUpperCase(),
                assunto: `RD Express ${ultimoMesNome.value} [KIXICREDITO ANGOLA · ${tituloDireccao.value}]`,
                html: gerarHtmlEmail()
            })
        })

        const dados = await resp.json().catch(() => ({}))
        if (!resp.ok) {
            throw new Error(dados.message || dados.error || `Falha no envio (HTTP ${resp.status}).`)
        }

        emailDialog.sucesso = `Relatório enviado para ${emailDialog.destinatario}.`
        setTimeout(() => { emailDialog.enviando = false; emailDialog.aberto = false }, 1600)
    } catch (e) {
        emailDialog.erro = e.message || 'Não foi possível enviar o email. Tente novamente.'
        emailDialog.enviando = false
    }
}
</script>

<style scoped>
.tabular-nums {
    font-variant-numeric: tabular-nums;
}
.fade-enter-active, .fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
