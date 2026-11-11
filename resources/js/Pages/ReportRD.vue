 <template>
    <Head title="Reporting Balance Kixi Crédito Angola" />

    <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100/50">
        <div class="container mx-auto py-4 md:py-6 px-2 md:px-4 max-w-[1600px]">

            <!-- ═══════════════ HEADER MODERNO ═══════════════ -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0B2E4F] via-[#103E68] to-[#1E5A8E] shadow-xl mb-6">
                <div class="absolute inset-0 opacity-10"
                     style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px); background-size: 24px 24px;">
                </div>
                <div class="relative flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 p-5 md:p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/10 backdrop-blur-sm p-3 rounded-2xl ring-1 ring-white/20">
                            <i class="fas fa-chart-pie text-2xl text-emerald-300"></i>
                        </div>
                        <div>
                            <h1 class="text-xl md:text-2xl font-bold text-white tracking-tight">
                                Reporting Balance · Kixi Crédito Angola
                            </h1>
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
                        <a :href="`/reports/rdreport`" target="_blank"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white text-sm font-semibold transition-all shadow-lg shadow-emerald-500/30">
                            <i class="fa fa-chart-line"></i>
                            Visualizar RD Express
                        </a>
                    </div>
                </div>
            </div>

            <!-- ═══════════════ KPI CARDS ═══════════════ -->
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 md:gap-4 mb-6">
                <KpiCard
                    label="Balanço Total"
                    :value="formatarMoedaCompacta(ultimoRegistro?.BalancoValor)"
                    :delta="calcularDelta(ultimoRegistro?.BalancoValor, registroAnterior?.BalancoValor)"
                    icon="fa-wallet"
                    tone="blue"
                />
                <KpiCard
                    label="Desembolso"
                    :value="formatarMoedaCompacta(ultimoRegistro?.Desembolsos)"
                    :delta="calcularDelta(ultimoRegistro?.Desembolsos, registroAnterior?.Desembolsos)"
                    icon="fa-hand-holding-usd"
                    tone="emerald"
                />
                <KpiCard
                    label="Reembolso"
                    :value="formatarMoedaCompacta(ultimoRegistro?.Reembolso)"
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
                            {{ historicoAteUltimoPeriodo.length }} Períodos (até {{ ultimoMesNome }})
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
                        RD Express {{ ultimoMesNome }} [KIXICREDITO ANGOLA]
                    </h1>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-6 space-y-8">

                    <!-- ═══ TABELA 1: Reporting Global ═══ -->
                    <section>
                        <SectionTitle
                            icon="fa-globe-africa"
                            title="Reporting Global — KIXICRÉDITO ANGOLA (100%)"
                            subtitle="Histórico Mensal Consolidado até ao Último Período"
                            tone="blue"
                        />

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs border-collapse text-slate-800">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-[#0B2E4F] text-white text-center">
                                        <th rowspan="2"
                                            class="w-36 bg-slate-50 text-slate-500 font-bold border-r border-b border-slate-300 px-3 py-1.5 align-bottom text-left text-[11px] uppercase tracking-wider">
                                            Indicadores
                                        </th>
                                        <th :colspan="colunasMeses.length"
                                            class="border-r border-b border-slate-300 py-1.5 text-[11px] uppercase tracking-wider">
                                            Histórico Mensal
                                        </th>
                                        <th colspan="2" class="border-r border-b border-slate-300 py-1.5 text-[11px] uppercase tracking-wider">
                                            Variação
                                        </th>
                                        <th class="border-r border-b border-slate-300 py-1.5 text-[11px] uppercase tracking-wider bg-[#1E5A8E]">
                                            {{ ultimoMesNome || 'Mês Atual' }}
                                        </th>
                                        <th class="border-b border-slate-300 py-1.5 text-[11px] uppercase tracking-wider">OM</th>
                                    </tr>
                                    <tr class="bg-[#103E68] text-white text-center">
                                        <th v-for="mes in colunasMeses" :key="mes.key"
                                            class="border-r border-b border-slate-300 px-3 py-1 whitespace-nowrap text-[11px] font-semibold">
                                            {{ mes.label }}
                                        </th>
                                        <th class="border-r border-b border-slate-300 px-3 py-1 bg-emerald-500/20 text-emerald-200 text-[11px]">▲</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-1 bg-rose-500/20 text-rose-200 text-[11px]">▼</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-1 bg-[#1E5A8E]"></th>
                                        <th class="border-b border-slate-300 px-3 py-1"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr class="hover:bg-blue-50/40 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-1.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-4 bg-blue-500 rounded-full"></span>
                                                1. Balanço
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
                                        <th class="border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider">Balanço</th>
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
                                        <tr class="bg-gradient-to-r from-indigo-100 via-indigo-50 to-transparent border-y border-indigo-200">
                                            <td colspan="11" class="px-3 py-1.5">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-6 h-6 rounded-md bg-indigo-600 text-white flex items-center justify-center shadow-sm">
                                                            <i class="fas fa-user-tie text-[10px]"></i>
                                                        </div>
                                                        <div>
                                                            <span class="text-xs font-bold text-indigo-900 uppercase tracking-wide">
                                                                {{ grupo.gestor }}
                                                            </span>
                                                            <span class="text-[10px] text-indigo-600/80 font-medium ml-2">
                                                                ({{ grupo.tipoDireccao }} · {{ grupo.itens.length }} {{ grupo.itens.length === 1 ? 'produto' : 'produtos' }})
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
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

                    <!-- ═══ TABELA 5: Desembolsos/Reembolsos 12 Meses ═══ -->
                    <section v-if="desembolsosReembolsos12MesesRD.length > 0">
                        <SectionTitle
                            icon="fa-money-bill-wave"
                            title="3. DESEMBOLSOS E REEMBOLSOS (ÚLTIMOS 12 MESES)"
                            subtitle="Evolução mensal com destaque para pico máximo (verde) e mínimo (vermelho)"
                            tone="emerald"
                        />

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs border-collapse text-slate-800">
                                <thead>
                                    <tr class="bg-[#103E68] text-white text-center text-[11px]">
                                        <th class="border-r border-b border-slate-300 px-3 py-1.5 text-left w-44 uppercase tracking-wider">Fluxo / Mês</th>
                                        <th v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                            class="border-r border-b border-slate-300 px-2 py-1.5 min-w-[85px] uppercase tracking-wider">
                                            {{ mes.NomeDoMes }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr class="hover:bg-emerald-50/20 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-1.5 text-left bg-emerald-50 text-emerald-800">
                                            <i class="fas fa-arrow-down mr-2 text-emerald-600"></i>Desembolso (+)
                                        </td>
                                        <td v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                            class="text-right border-r border-slate-200 px-2 py-1.5 tabular-nums transition-all"
                                            :class="obterClasseDestaqueDesembolso(mes)">
                                            <div class="flex items-center justify-end gap-1">
                                                <span v-if="Number(mes.DesembolsoValor) === maxMinFluxo.maxDesemb" class="text-[8px] bg-emerald-600 text-white px-1 rounded-sm uppercase tracking-tighter">▲ MAX</span>
                                                <span v-else-if="Number(mes.DesembolsoValor) === maxMinFluxo.minDesemb" class="text-[8px] bg-rose-600 text-white px-1 rounded-sm uppercase tracking-tighter">▼ MIN</span>
                                                <span>{{ formatarNumero(mes.DesembolsoValor) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-rose-50/20 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-1.5 text-left bg-rose-50 text-rose-800">
                                            <i class="fas fa-arrow-up mr-2 text-rose-600"></i>Reembolso (−)
                                        </td>
                                        <td v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                            class="text-right border-r border-slate-200 px-2 py-1.5 tabular-nums transition-all"
                                            :class="obterClasseDestaqueReembolso(mes)">
                                            <div class="flex items-center justify-end gap-1">
                                                <span v-if="Number(mes.ReembolsoValor) === maxMinFluxo.maxReemb" class="text-[8px] bg-emerald-600 text-white px-1 rounded-sm uppercase tracking-tighter">▲ MAX</span>
                                                <span v-else-if="Number(mes.ReembolsoValor) === maxMinFluxo.minReemb" class="text-[8px] bg-rose-600 text-white px-1 rounded-sm uppercase tracking-tighter">▼ MIN</span>
                                                <span>{{ formatarNumero(mes.ReembolsoValor) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                         6. TOP CLIENTES EM RISCO CRÍTICO
                         Model: tblRDRiscoTopClientes
                         ═════════════════════════════════════════════════════════════════ -->
                    <section v-if="temTopClientes" class="pt-6 border-t-2 border-slate-200 space-y-6">
                        <SectionTitle
                            icon="fa-users-slash"
                            title="6. TOP CLIENTES EM RISCO CRÍTICO"
                            subtitle="Classificação por Direção (DOP & DPP) e Risco Geral da Instituição"
                            tone="rose"
                        />

                        <!-- ABAS DE NAVEGAÇÃO DOS 3 RANKINGS -->
                        <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-2">
                            <button
                                type="button"
                                @click="tabRiscoAtiva = 'GERAL'"
                                :class="tabRiscoAtiva === 'GERAL'
                                    ? 'bg-rose-700 text-white shadow-sm'
                                    : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                Top 15 Risco Geral (Instituição)
                                <span class="text-[10px] bg-white/20 px-1.5 py-0.2 rounded-full">{{ top15ClientesGeral.length }}</span>
                            </button>

                            <button
                                type="button"
                                @click="tabRiscoAtiva = 'DOP'"
                                :class="tabRiscoAtiva === 'DOP'
                                    ? 'bg-[#0B2E4F] text-white shadow-sm'
                                    : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                                <i class="fas fa-sitemap"></i>
                                Top 15 DOP (Operações)
                                <span class="text-[10px] bg-white/20 px-1.5 py-0.2 rounded-full">{{ top15ClientesDOP.length }}</span>
                            </button>

                            <button
                                type="button"
                                @click="tabRiscoAtiva = 'DPP'"
                                :class="tabRiscoAtiva === 'DPP'
                                    ? 'bg-indigo-700 text-white shadow-sm'
                                    : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                                <i class="fas fa-handshake"></i>
                                Top 15 DPP (Particulares & Parcerias)
                                <span class="text-[10px] bg-white/20 px-1.5 py-0.2 rounded-full">{{ top15ClientesDPP.length }}</span>
                            </button>
                        </div>

                        <!-- TABELA DINÂMICA DE TOP CLIENTES -->
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-[11px] border-collapse text-slate-800">
                                <thead class="sticky top-0 text-white text-center"
                                       :class="tabRiscoAtiva === 'GERAL' ? 'bg-gradient-to-r from-rose-900 to-rose-800' : (tabRiscoAtiva === 'DOP' ? 'bg-[#0B2E4F]' : 'bg-indigo-900')">
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
                                        <th class="px-2 py-2 text-right border-r border-white/20">Provisão Constituída</th>
                                        <th class="px-2 py-2 text-left">Oficial de Crédito</th>
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
                                        <td class="px-2 py-1.5 text-right tabular-nums text-slate-600 border-r border-slate-200">
                                            {{ formatarNumero(c.ProvisaoACT) }}
                                        </td>
                                        <td class="px-2 py-1.5 text-slate-600 text-[10px] whitespace-nowrap">
                                            {{ c.NomeOficialCredito || c.UsSectorista || '—' }}
                                        </td>
                                    </tr>

                                    <tr v-if="listaTopClientesAtiva.length === 0">
                                        <td colspan="12" class="px-3 py-6 text-center text-slate-400">
                                            Nenhum cliente em risco encontrado para a classificação selecionada.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>


                    <!-- ═════════════════════════════════════════════════════════════════
                         7. EXPOSIÇÃO AO RISCO POR CENTROS DE CUSTO
                         Model: tblrdriscoexposicaocentroscusto
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
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick, h } from 'vue'
import { Head } from '@inertiajs/vue3'
import Chart from 'chart.js/auto'

/* ══════════════════════════════════════════════════
   PROPS
   ══════════════════════════════════════════════════ */
const props = defineProps({
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

/* ══════════════════════════════════════════════════
   ESTADO REATIVO DAS ABAS DE RISCO (SEÇÃO 7)
   ══════════════════════════════════════════════════ */
const tabRiscoAtiva = ref('GERAL') // 'GERAL' | 'DOP' | 'DPP'

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
            h('div', { class: 'text-lg font-bold text-slate-900 tabular-nums tracking-tight leading-tight' }, p.value),
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
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-1 uppercase tracking-wider' }, 'Balanço'),
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
            )
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
   COMPUTED: HISTÓRICO DINÂMICO ATÉ AO ÚLTIMO PERÍODO
   ══════════════════════════════════════════════════ */
const historicoOrdenado = computed(() => {
    return [...(props.historicoRD || [])].sort((a, b) => new Date(a.DataReferencia) - new Date(b.DataReferencia))
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
    const arr = historicoOrdenado.value
    if (arr.length <= 6) return arr
    return arr.slice(arr.length - 6)
})

const anoAtual = computed(() => {
    if (ultimoRegistro.value?.DataReferencia) {
        return new Date(ultimoRegistro.value.DataReferencia).getFullYear()
    }
    return 2026
})

const colunasMeses = computed(() => historicoAteUltimoPeriodo.value.map(item => {
    const data = new Date(item.DataReferencia)
    const mesExtenso = data.toLocaleDateString('pt-PT', { month: 'short' })
    const anoCurto = data.getFullYear().toString().substring(2)
    const label = mesExtenso.charAt(0).toUpperCase() + mesExtenso.slice(1, 3) + '/' + anoCurto
    return { key: item.Id || item.DataReferencia, label }
}))

const ultimoMesNome = computed(() => {
    if (!ultimoRegistro.value) return ''
    const d = new Date(ultimoRegistro.value.DataReferencia)
    return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
})

const labelMesActual = computed(() => {
    if (ultimoRegistro.value?.DataReferencia) {
        const d = new Date(ultimoRegistro.value.DataReferencia)
        return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
    }
    return '31/08/2026'
})

const labelMesAnterior = computed(() => {
    if (registroAnterior.value?.DataReferencia) {
        const d = new Date(registroAnterior.value.DataReferencia)
        return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
    }
    return '31/07/2026'
})

const labelFechoAA = computed(() => `DEZ ${Number(anoAtual.value) - 1}`)

const agenciasCapital  = computed(() => (props.agenciasRD || []).filter(i => i.TipoAgrupamento === 'CAPITAL'))
const agenciasProvincia = computed(() => (props.agenciasRD || []).filter(i => i.TipoAgrupamento === 'PROVINCIA'))

/* ══════════════════════════════════════════════════
   COMPUTED: DESTAQUE MAX/MIN FLUXO (12 MESES)
   ══════════════════════════════════════════════════ */
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
                total: { BalancoValor: 0, DesembolsoValor: 0 }
            }
        }
        grupos[gestor].itens.push(item)
        grupos[gestor].total.BalancoValor += Number(item.BalancoValor) || 0
        grupos[gestor].total.DesembolsoValor += Number(item.DesembolsoValor) || 0
    })
    return Object.values(grupos).sort((a, b) => a.gestor.localeCompare(b.gestor, 'pt-PT'))
})

/* ══════════════════════════════════════════════════
   COMPUTED: PROVISÕES AGRUPADAS (COM GESTORKEY CORRETO)
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

        if (nomeProd.includes('pame') || nomeProd.includes('colaborador')) {
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
   COMPUTED: 6. CENTROS DE CUSTO ORDENADOS POR GRAVIDADE DE RISCO
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
   COMPUTED: 7. TOP 15 CLIENTES POR TIPO DE RANKING
   ══════════════════════════════════════════════════ */
const normalizarTipoRanking = (val) => String(val || '').trim().toUpperCase()

const top15ClientesDOP = computed(() => {
    return [...(props.riscoTopClientesRD || [])]
        .filter(c => normalizarTipoRanking(c.TipoRanking) === 'TOP15_DOP' || (normalizarTipoRanking(c.ccDireccao) === 'DOP' && !c.TipoRanking))
        .sort((a, b) => (Number(a.Posicao) || 999) - (Number(b.Posicao) || 999))
        .slice(0, 15)
})

const top15ClientesDPP = computed(() => {
    return [...(props.riscoTopClientesRD || [])]
        .filter(c => normalizarTipoRanking(c.TipoRanking) === 'TOP15_DPP' || (normalizarTipoRanking(c.ccDireccao) === 'DPP' && !c.TipoRanking))
        .sort((a, b) => (Number(a.Posicao) || 999) - (Number(b.Posicao) || 999))
        .slice(0, 15)
})

const top15ClientesGeral = computed(() => {
    const filtrados = [...(props.riscoTopClientesRD || [])]
        .filter(c => normalizarTipoRanking(c.TipoRanking) === 'TOP15_GERAL')
        .sort((a, b) => (Number(a.Posicao) || 999) - (Number(b.Posicao) || 999))
        .slice(0, 15)

    if (filtrados.length === 0 && (props.riscoTopClientesRD || []).length > 0) {
        return [...props.riscoTopClientesRD]
            .sort((a, b) => (Number(b.SaldoTotalEmRisco) || 0) - (Number(a.SaldoTotalEmRisco) || 0))
            .slice(0, 15)
            .map((item, idx) => ({ ...item, Posicao: idx + 1 }))
    }

    return filtrados
})

const listaTopClientesAtiva = computed(() => {
    if (tabRiscoAtiva.value === 'DOP') return top15ClientesDOP.value
    if (tabRiscoAtiva.value === 'DPP') return top15ClientesDPP.value
    return top15ClientesGeral.value
})

const temTopClientes = computed(() => {
    return (props.riscoTopClientesRD || []).length > 0
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
   CHARTS AUTOMÁTICOS ATÉ AO ÚLTIMO PERÍODO
   ══════════════════════════════════════════════════ */
const CHART_FONT = { family: 'ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif' }

const destroyCharts = () => {
    Object.keys(charts).forEach(k => { if (charts[k]) { charts[k].destroy(); charts[k] = null } })
}

const initCharts = () => {
    destroyCharts()

    // 1. Gráfico: Evolução da Carteira (Até ao último período disponível)
    if (chartBalancoRef.value && historicoAteUltimoPeriodo.value.length > 0) {
        const labels = colunasMeses.value.map(m => m.label)
        const data = historicoAteUltimoPeriodo.value.map(i => Number(i.BalancoValor) || 0)

        const ctx = chartBalancoRef.value.getContext('2d')
        const grad = ctx.createLinearGradient(0, 0, 0, 220)
        grad.addColorStop(0, 'rgba(37, 99, 235, 0.35)')
        grad.addColorStop(1, 'rgba(37, 99, 235, 0.0)')

        charts.balanco = new Chart(chartBalancoRef.value, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Balanço Total',
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
                        callbacks: { label: c => ' Balanço: ' + formatarNumero(c.parsed.y) + ' Kz' }
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

    // 3. Gráfico: Qualidade da Carteira (NPL · PAR 1 · PAR 30 até ao último período)
    if (chartQualidadeRef.value && historicoAteUltimoPeriodo.value.length > 0) {
        const labels = colunasMeses.value.map(m => m.label)
        const npl = historicoAteUltimoPeriodo.value.map(i => Number(i.NPLPercentual) || 0)
        const p1  = historicoAteUltimoPeriodo.value.map(i => Number(i.PAR1Percentual) || 0)
        const p30 = historicoAteUltimoPeriodo.value.map(i => Number(i.PAR30Percentual) || 0)

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
   IMPRESSÃO A4
   ══════════════════════════════════════════════════ */
const imprimirA4 = () => {
    if (!areaTabelas.value) return
    const iframe = document.createElement('iframe')
    iframe.style.cssText = 'position:absolute;width:0;height:0;border:none;'
    document.body.appendChild(iframe)
    const doc = iframe.contentWindow.document

    const estilos = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
        .map(s => s.outerHTML).join('')

    doc.open()
    doc.write(`
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <title>RD Express ${ultimoMesNome.value} [KIXICREDITO ANGOLA]</title>
                ${estilos}
                <style>
                    @page { size: A4 landscape; margin: 5mm; }
                    body { background-color: #fff !important; padding: 0 !important; margin: 0 !important;
                           -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;
                           font-family: ui-sans-serif, system-ui, sans-serif; }
                    table { font-size: 7.5px !important; page-break-inside: auto; }
                    tr { page-break-inside: avoid; }
                    thead { display: table-header-group; }
                    .shadow-sm, .shadow-xl, .shadow-lg { box-shadow: none !important; }
                    .rounded-2xl, .rounded-xl, .rounded { border-radius: 2px !important; }
                    section { page-break-inside: avoid; margin-bottom: 6px; }
                </style>
            </head>
            <body>${areaTabelas.value.outerHTML}</body>
        </html>
    `)
    doc.close()

    setTimeout(() => {
        iframe.contentWindow.focus()
        iframe.contentWindow.print()
        document.body.removeChild(iframe)
    }, 600)
}
</script>

<style scoped>
.tabular-nums {
    font-variant-numeric: tabular-nums;
}
</style>
