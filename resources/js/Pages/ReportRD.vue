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

            <!-- ═══════════════ GRÁFICOS ═══════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                <!-- Evolução do Balanço -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-chart-area text-blue-600"></i>
                            <h3 class="font-semibold text-slate-800">Evolução do Balanço</h3>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">6 meses</span>
                    </div>
                    <div class="h-56 relative">
                        <canvas ref="chartBalancoRef"></canvas>
                    </div>
                </div>

                <!-- Desembolso vs Reembolso -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exchange-alt text-emerald-600"></i>
                            <h3 class="font-semibold text-slate-800">Fluxo de Caixa</h3>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">12 meses</span>
                    </div>
                    <div class="h-56 relative">
                        <canvas ref="chartFluxoRef"></canvas>
                    </div>
                </div>

                <!-- Qualidade da Carteira -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-shield-alt text-amber-600"></i>
                            <h3 class="font-semibold text-slate-800">Qualidade da Carteira (NPL · PAR 1 · PAR 30)</h3>
                        </div>
                    </div>
                    <div class="h-56 relative">
                        <canvas ref="chartQualidadeRef"></canvas>
                    </div>
                </div>

                <!-- Distribuição por Agências -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-building text-indigo-600"></i>
                            <h3 class="font-semibold text-slate-800">Distribuição por Agências</h3>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">Top 8</span>
                    </div>
                    <div class="h-56 relative">
                        <canvas ref="chartAgenciasRef"></canvas>
                    </div>
                </div>
            </div>

            <!-- ═══════════════ ÁREA DE IMPRESSÃO (TABELAS) ═══════════════ -->
            <div ref="areaTabelas">
                <!-- Título exclusivo da impressão -->
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
                            subtitle="PCE: JOAQUIM CATINDA · Histórico Mensal Consolidado"
                            tone="blue"
                        />

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs border-collapse text-slate-800">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-[#0B2E4F] text-white text-center">
                                        <th rowspan="2"
                                            class="w-36 bg-slate-50 text-slate-500 font-bold border-r border-b border-slate-300 px-3 py-2 align-bottom text-left text-[11px] uppercase tracking-wider">
                                            Indicadores
                                        </th>
                                        <th :colspan="colunasMeses.length"
                                            class="border-r border-b border-slate-300 py-2 text-[11px] uppercase tracking-wider">
                                            Histórico Mensal
                                        </th>
                                        <th colspan="2" class="border-r border-b border-slate-300 py-2 text-[11px] uppercase tracking-wider">
                                            Variação
                                        </th>
                                        <th class="border-r border-b border-slate-300 py-2 text-[11px] uppercase tracking-wider bg-[#1E5A8E]">
                                            Mês Atual
                                        </th>
                                        <th class="border-b border-slate-300 py-2 text-[11px] uppercase tracking-wider">OM</th>
                                    </tr>
                                    <tr class="bg-[#103E68] text-white text-center">
                                        <th v-for="mes in colunasMeses" :key="mes.key"
                                            class="border-r border-b border-slate-300 px-3 py-1.5 whitespace-nowrap text-[11px] font-semibold">
                                            {{ mes.label }}
                                        </th>
                                        <th class="border-r border-b border-slate-300 px-3 py-1.5 bg-emerald-500/20 text-emerald-200 text-[11px]">▲</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-1.5 bg-rose-500/20 text-rose-200 text-[11px]">▼</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-1.5 bg-[#1E5A8E]"></th>
                                        <th class="border-b border-slate-300 px-3 py-1.5"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <!-- 1. Balanço -->
                                    <tr class="hover:bg-blue-50/40 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-2.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                                                1. Balanço
                                            </span>
                                        </td>
                                        <td v-for="item in historicoSeisMeses" :key="item.Id"
                                            class="text-right border-r border-slate-200 px-2 py-2 font-medium tabular-nums">
                                            {{ formatarNumero(item.BalancoValor) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.Desembolsos) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.Reembolso) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-amber-50 text-amber-800 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.BalancoValor) }}
                                        </td>
                                        <td class="text-right px-2 py-2 font-bold tabular-nums"
                                            :class="Number(ultimoRegistro?.VariacaoPercentual) >= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="Number(ultimoRegistro?.VariacaoPercentual) >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.VariacaoPercentual) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- 2. Créditos Novos -->
                                    <tr class="hover:bg-blue-50/40 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-2.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-5 bg-indigo-500 rounded-full"></span>
                                                <span>2. Créditos | Novos<br>
                                                <span class="text-[10px] font-normal text-slate-500">Taxa Juros ≤ Kz 2.000</span></span>
                                            </span>
                                        </td>
                                        <td v-for="item in historicoSeisMeses" :key="item.Id"
                                            class="text-right border-r border-slate-200 px-2 py-2 font-medium tabular-nums leading-relaxed">
                                            <span class="text-slate-700">{{ formatarInt(item.CreditosQuantidade) }}</span>
                                            <span class="text-slate-400 mx-1">|</span>
                                            <span class="text-indigo-700 font-bold">{{ formatarInt(item.CreditosNovos) }}</span>
                                            <br>
                                            <span class="text-[10px] text-slate-500">{{ formatarNumero(item.TaxaJuros1) }}% | {{ formatarNumero(item.TaxaJuros2) }}%</span>
                                            <br>
                                            <span class="text-[10px] text-slate-500">{{ formatarInt(item.CreditosAteKz2000_Qtd) }} | {{ formatarNumero(item.CreditosAteKz2000_Valor) }}</span>
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarInt(ultimoRegistro?.CreditosNovos_Mais) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarInt(ultimoRegistro?.CreditosNovos_Menos) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 font-bold tabular-nums leading-relaxed">
                                            <span>{{ formatarInt(ultimoRegistro?.CreditosQuantidade) }}</span>
                                            <span class="text-slate-400 mx-1">|</span>
                                            <span class="text-indigo-700">{{ formatarInt(ultimoRegistro?.CreditosNovos) }}</span>
                                            <br>
                                            <span class="text-[10px] text-slate-500">{{ formatarNumero(ultimoRegistro?.TaxaJuros1) }}% | {{ formatarNumero(ultimoRegistro?.TaxaJuros2) }}%</span>
                                            <br>
                                            <span class="text-[10px] text-slate-500">{{ formatarInt(ultimoRegistro?.CreditosAteKz2000_Qtd) }} | {{ formatarNumero(ultimoRegistro?.CreditosAteKz2000_Valor) }}</span>
                                        </td>
                                        <td class="text-right px-2 py-2 font-bold tabular-nums"
                                            :class="Number(ultimoRegistro?.VariacaoCreditosNovos) >= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="Number(ultimoRegistro?.VariacaoCreditosNovos) >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.VariacaoCreditosNovos) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- 3. Clientes Novos -->
                                    <tr class="hover:bg-blue-50/40 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-2.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-5 bg-cyan-500 rounded-full"></span>
                                                <span>3. Clientes | Novos<br>
                                                <span class="text-[10px] font-normal text-slate-500">≤ Kz 2.000</span></span>
                                            </span>
                                        </td>
                                        <td v-for="item in historicoSeisMeses" :key="item.Id"
                                            class="text-right border-r border-slate-200 px-2 py-2 font-medium tabular-nums leading-relaxed">
                                            <span>{{ formatarInt(item.ClientesQuantidade) }}</span>
                                            <span class="text-slate-400 mx-1">|</span>
                                            <span class="text-cyan-700 font-bold">{{ formatarInt(item.ClientesNovos) }}</span>
                                            <br>
                                            <span class="text-[10px] text-slate-500">≤2k: {{ formatarInt(item.ClientesAteKz2000) }}</span>
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarInt(ultimoRegistro?.ClientesNovos_Mais) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarInt(ultimoRegistro?.ClientesNovos_Menos) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 font-bold tabular-nums leading-relaxed">
                                            <span>{{ formatarInt(ultimoRegistro?.ClientesQuantidade) }}</span>
                                            <span class="text-slate-400 mx-1">|</span>
                                            <span class="text-cyan-700">{{ formatarInt(ultimoRegistro?.ClientesNovos) }}</span>
                                            <br>
                                            <span class="text-[10px] text-slate-500">≤2k: {{ formatarInt(ultimoRegistro?.ClientesAteKz2000) }}</span>
                                        </td>
                                        <td class="text-right px-2 py-2 font-bold tabular-nums"
                                            :class="Number(ultimoRegistro?.VariacaoClientesNovos) >= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="Number(ultimoRegistro?.VariacaoClientesNovos) >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.VariacaoClientesNovos) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Linhas 4-6 (NPL, PAR1, PAR30) -->
                                    <tr v-for="linha in linhasRisco" :key="linha.key" class="hover:bg-blue-50/40 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-2.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-5 rounded-full" :class="linha.cor"></span>
                                                {{ linha.numero }}. {{ linha.label }}
                                            </span>
                                        </td>
                                        <td v-for="item in historicoSeisMeses" :key="item.Id"
                                            class="text-right border-r border-slate-200 px-2 py-2 font-medium tabular-nums">
                                            <span class="font-bold" :class="linha.classePerc">{{ formatarPercentual(item[linha.percKey]) }}</span>
                                            <br>
                                            <span class="text-[10px] text-slate-500">{{ formatarK(item[linha.valKey]) }}</span>
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.[linha.maisKey]) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.[linha.menosKey]) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 font-bold tabular-nums">
                                            <span class="font-bold" :class="linha.classePerc">{{ formatarPercentual(ultimoRegistro?.[linha.percKey]) }}</span>
                                            <br>
                                            <span class="text-[10px] text-slate-500">{{ formatarK(ultimoRegistro?.[linha.valKey]) }}</span>
                                        </td>
                                        <td class="text-right px-2 py-2 font-bold tabular-nums"
                                            :class="Number(ultimoRegistro?.[linha.variacaoKey]) <= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="Number(ultimoRegistro?.[linha.variacaoKey]) <= 0 ? 'fas fa-arrow-down' : 'fas fa-arrow-up'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.[linha.variacaoKey]) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- 7. Provisão -->
                                    <tr class="bg-rose-50/40 hover:bg-rose-50/70 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-2.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-5 bg-rose-500 rounded-full"></span>
                                                7. Provisão
                                            </span>
                                        </td>
                                        <td v-for="item in historicoSeisMeses" :key="item.Id"
                                            class="text-right border-r border-slate-200 px-2 py-2 font-semibold text-rose-700 tabular-nums">
                                            {{ formatarNumero(item.ProvisaoValor) }}
                                            <br>
                                            <span class="text-[10px] text-slate-500">{{ formatarProvisaoCobertura(item.ProvisaoDiasAtraso, item.ProvisaoCoberturaPerc) }}</span>
                                            <br>
                                            <span class="text-[10px] text-slate-400">DA: {{ item.ProvisaoDiasAtraso ?? 0 }}</span>
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.Provisao_Mais) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.Provisao_Menos) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 font-bold text-rose-700 tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.ProvisaoValor) }}
                                            <br>
                                            <span class="text-[10px] text-slate-500">{{ formatarProvisaoCobertura(ultimoRegistro?.ProvisaoDiasAtraso, ultimoRegistro?.ProvisaoCoberturaPerc) }}</span>
                                            <br>
                                            <span class="text-[10px] text-slate-400">DA: {{ ultimoRegistro?.ProvisaoDiasAtraso ?? 0 }}</span>
                                        </td>
                                        <td class="text-right px-2 py-2 font-bold tabular-nums"
                                            :class="Number(ultimoRegistro?.VariacaoProvisao) <= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            <span class="inline-flex items-center gap-1">
                                                <i :class="Number(ultimoRegistro?.VariacaoProvisao) <= 0 ? 'fas fa-arrow-down' : 'fas fa-arrow-up'" class="text-[9px]"></i>
                                                {{ formatarPercentual(Math.abs(Number(ultimoRegistro?.VariacaoProvisao) || 0)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Linhas 8, 9, 10 -->
                                    <tr v-for="linha in linhasPercentuais" :key="linha.key" class="hover:bg-blue-50/40 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-2.5 text-left text-slate-700">
                                            <span class="inline-flex items-center gap-2">
                                                <span class="w-1 h-5 rounded-full" :class="linha.cor"></span>
                                                {{ linha.numero }}. {{ linha.label }}
                                            </span>
                                        </td>
                                        <td v-for="item in historicoSeisMeses" :key="item.Id"
                                            class="text-right border-r border-slate-200 px-2 py-2 font-bold tabular-nums"
                                            :class="linha.classeFixa">
                                            {{ formatarPercentual(item[linha.key]) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-emerald-50 text-emerald-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.[linha.maisKey]) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 bg-rose-50 text-rose-700 font-bold tabular-nums">
                                            {{ formatarNumero(ultimoRegistro?.[linha.menosKey]) }}
                                        </td>
                                        <td class="text-right border-r border-slate-200 px-2 py-2 font-bold tabular-nums"
                                            :class="linha.classeFixa">
                                            {{ formatarPercentual(ultimoRegistro?.[linha.key]) }}
                                        </td>
                                        <td class="text-right px-2 py-2 font-bold tabular-nums"
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
                            title="Distribuição da Carteira por Agências"
                            subtitle="Performance detalhada por localidade"
                            tone="emerald"
                        />

                        <div class="space-y-6">
                            <AgenciasTable titulo="Capital (Luanda)" :agencias="agenciasCapital" />
                            <AgenciasTable titulo="Províncias" :agencias="agenciasProvincia" />
                        </div>
                    </section>

                    <!-- ═══ TABELA 3: Produtos agrupados por Direção / Gestor ═══ -->
                    <section v-if="produtosAgrupados.length > 0">
                        <SectionTitle
                            icon="fa-sitemap"
                            title="Detalhamento por Direção / Gestor"
                            subtitle="Consolidado de produtos agrupados por responsável"
                            tone="indigo"
                        />

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs border-collapse text-slate-800">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-[#0B2E4F] text-white">
                                        <th colspan="12" class="px-3 py-2.5 text-sm tracking-wider uppercase text-left">
                                            <i class="fas fa-user-tie mr-2 opacity-70"></i>
                                            Detalhamento por Direção / Gestor
                                        </th>
                                    </tr>
                                    <tr class="bg-[#103E68] text-white text-center text-[11px]">
                                        <th class="border-r border-b border-slate-300 px-3 py-2 text-left w-56 uppercase tracking-wider">Tipo / Produto</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-2 uppercase tracking-wider">Direção / Gestor</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-2 uppercase tracking-wider">OA/PA</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-2 uppercase tracking-wider">Balanço</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-2 uppercase tracking-wider">Créditos | Novos</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-2 uppercase tracking-wider">Clientes | Novos</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-2 uppercase tracking-wider">PAR 1</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-2 uppercase tracking-wider">PAR 30</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-2 uppercase tracking-wider">Desemb. | TA</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-2 uppercase tracking-wider">Reembolso</th>
                                        <th class="border-r border-b border-slate-300 px-3 py-2 uppercase tracking-wider">Provisão</th>
                                        <th class="border-b border-slate-300 px-3 py-2 uppercase tracking-wider">Gestão</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template v-for="grupo in produtosAgrupados" :key="grupo.gestor">
                                        <!-- Cabeçalho do grupo Diretor -->
                                        <tr class="bg-gradient-to-r from-indigo-100 via-indigo-50 to-transparent border-y-2 border-indigo-200">
                                            <td colspan="12" class="px-3 py-2.5">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-sm">
                                                            <i class="fas fa-user-tie text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-bold text-indigo-900 leading-tight uppercase tracking-wide">
                                                                {{ grupo.gestor }}
                                                            </div>
                                                            <div class="text-[10px] text-indigo-600/80 font-medium mt-0.5">
                                                                <i class="fas fa-sitemap mr-1"></i>{{ grupo.tipoDireccao }}
                                                                <span class="mx-2 text-indigo-300">•</span>
                                                                <i class="fas fa-cube mr-1"></i>{{ grupo.itens.length }}
                                                                {{ grupo.itens.length === 1 ? 'produto' : 'produtos' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="hidden md:flex items-center gap-4 text-[11px]">
                                                        <div class="text-right">
                                                            <div class="text-[9px] uppercase tracking-wider text-indigo-500 font-bold">Balanço</div>
                                                            <div class="font-bold text-indigo-900 tabular-nums">{{ formatarNumero(grupo.total.BalancoValor) }}</div>
                                                        </div>
                                                        <div class="text-right border-l border-indigo-200 pl-4">
                                                            <div class="text-[9px] uppercase tracking-wider text-indigo-500 font-bold">Desemb.</div>
                                                            <div class="font-bold text-emerald-700 tabular-nums">{{ formatarNumero(grupo.total.DesembolsoValor) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Linhas de produtos do grupo -->
                                        <tr v-for="prod in grupo.itens" :key="prod.Id"
                                            class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="border-r border-slate-200 px-3 py-2 text-left whitespace-nowrap pl-6 font-medium text-slate-700">
                                                <span class="inline-flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                                    {{ prod.NomeProduto }}
                                                </span>
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-left bg-slate-50/50 text-[11px]">
                                                <span class="font-semibold text-slate-700">{{ prod.DirectorNome || '—' }}</span>
                                                <span v-if="prod.TipoDireccao" class="text-slate-500 block text-[10px]">{{ prod.TipoDireccao }}</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-center bg-slate-50/50 text-slate-600 tabular-nums">
                                                {{ prod.OA_Qtd ?? '—' }}/{{ prod.PA_Qtd ?? '—' }}
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right font-semibold tabular-nums">{{ formatarNumero(prod.BalancoValor) }}</td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right tabular-nums">
                                                <span class="text-slate-700">{{ formatarInt(prod.CreditosTotal_Qtd) }}</span>
                                                <span class="text-slate-400 mx-1">|</span>
                                                <span class="text-indigo-700 font-bold">{{ formatarInt(prod.CreditosNovos_Qtd) }}</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right tabular-nums">
                                                <span class="text-slate-700">{{ formatarInt(prod.ClientesTotal_Qtd) }}</span>
                                                <span class="text-slate-400 mx-1">|</span>
                                                <span class="text-cyan-700 font-bold">{{ formatarInt(prod.ClientesNovos_Qtd) }}</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right tabular-nums">
                                                {{ formatarK(prod.PAR1_Valor) }}
                                                <span v-if="prod.PAR1_Percentual !== null" class="text-[10px] text-amber-700 font-semibold ml-1">({{ Math.round(prod.PAR1_Percentual) }}%)</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right tabular-nums">
                                                {{ formatarK(prod.PAR30_Valor) }}
                                                <span v-if="prod.PAR30_Percentual !== null" class="text-[10px] text-rose-700 font-semibold ml-1">({{ Math.round(prod.PAR30_Percentual) }}%)</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right bg-emerald-50/50 text-emerald-700 font-semibold tabular-nums">
                                                {{ formatarNumero(prod.DesembolsoValor) }}
                                                <span v-if="prod.TempoAtendimentodias !== null" class="text-[10px] text-slate-500 ml-1">| {{ String(prod.TempoAtendimentodias).padStart(2, '0') }}</span>
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right bg-rose-50/50 text-rose-700 tabular-nums">{{ formatarNumero(prod.ReembolsoValor) }}</td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right tabular-nums">
                                                {{ formatarNumero(prod.ProvisaoValor) }}
                                                <span v-if="prod.ProvisaoClasseRisco" class="font-bold text-slate-700 ml-1">({{ prod.ProvisaoClasseRisco }})</span>
                                            </td>
                                            <td class="px-3 py-2 text-center font-bold tabular-nums">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]"
                                                      :class="obterCorGestao(prod.GestaoValor)">
                                                    {{ prod.GestaoValor !== null ? prod.GestaoValor + (prod.GestaoSufixo || '') : '—' }}
                                                </span>
                                            </td>
                                        </tr>

                                        <!-- Subtotalizador do grupo -->
                                        <tr class="bg-indigo-50/70 font-bold border-t-2 border-b-2 border-indigo-200">
                                            <td colspan="2" class="border-r border-slate-200 px-3 py-2.5 text-left text-indigo-900 uppercase tracking-wider text-[10px]">
                                                <i class="fas fa-calculator mr-2 text-indigo-500"></i>
                                                Subtotal — {{ grupo.gestor }}
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-center text-indigo-900 tabular-nums">
                                                {{ grupo.total.OA_Qtd || '—' }}/{{ grupo.total.PA_Qtd || '—' }}
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right text-indigo-900 tabular-nums">{{ formatarNumero(grupo.total.BalancoValor) }}</td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right text-indigo-900 tabular-nums">
                                                {{ formatarInt(grupo.total.CreditosTotal_Qtd) }}
                                                <span class="text-indigo-400 mx-1">|</span>
                                                {{ formatarInt(grupo.total.CreditosNovos_Qtd) }}
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right text-indigo-900 tabular-nums">
                                                {{ formatarInt(grupo.total.ClientesTotal_Qtd) }}
                                                <span class="text-indigo-400 mx-1">|</span>
                                                {{ formatarInt(grupo.total.ClientesNovos_Qtd) }}
                                            </td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right text-indigo-900 tabular-nums">{{ formatarK(grupo.total.PAR1_Valor) }}</td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right text-indigo-900 tabular-nums">{{ formatarK(grupo.total.PAR30_Valor) }}</td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right bg-emerald-100 text-emerald-800 tabular-nums">{{ formatarNumero(grupo.total.DesembolsoValor) }}</td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right bg-rose-100 text-rose-800 tabular-nums">{{ formatarNumero(grupo.total.ReembolsoValor) }}</td>
                                            <td class="border-r border-slate-200 px-3 py-2 text-right text-indigo-900 tabular-nums">{{ formatarNumero(grupo.total.ProvisaoValor) }}</td>
                                            <td class="px-3 py-2 text-center text-indigo-400">—</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ═══ TABELA 4: Desembolsos/Reembolsos 12 Meses ═══ -->
                    <section v-if="desembolsosReembolsos12MesesRD.length > 0">
                        <SectionTitle
                            icon="fa-money-bill-wave"
                            title="Desembolsos e Reembolsos"
                            subtitle="Evolução dos últimos 12 meses"
                            tone="emerald"
                        />

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs border-collapse text-slate-800">
                                <thead>
                                    <tr class="bg-[#103E68] text-white text-center text-[11px]">
                                        <th class="border-r border-b border-slate-300 px-3 py-2 text-left w-44 uppercase tracking-wider">Fluxo / Mês</th>
                                        <th v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                            class="border-r border-b border-slate-300 px-2 py-2 min-w-[90px] uppercase tracking-wider">
                                            {{ mes.NomeDoMes }}
                                        </th>
                                        <th class="border-b border-slate-300 px-3 py-2 bg-[#0B2E4F] uppercase tracking-wider">Total Acumulado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr class="hover:bg-emerald-50/30 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-2.5 text-left bg-emerald-50 text-emerald-800">
                                            <i class="fas fa-arrow-down mr-2 text-emerald-600"></i>Desembolso (+)
                                        </td>
                                        <td v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                            class="text-right border-r border-slate-200 px-2 py-2 font-semibold text-emerald-700 tabular-nums">
                                            {{ formatarNumero(mes.DesembolsoValor) }}
                                        </td>
                                        <td class="text-right px-3 py-2 font-bold bg-emerald-100 text-emerald-800 tabular-nums">
                                            {{ formatarNumero(totalDesembolso12Meses) }}
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-rose-50/30 transition-colors">
                                        <td class="font-bold border-r border-slate-200 px-3 py-2.5 text-left bg-rose-50 text-rose-800">
                                            <i class="fas fa-arrow-up mr-2 text-rose-600"></i>Reembolso (−)
                                        </td>
                                        <td v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                            class="text-right border-r border-slate-200 px-2 py-2 font-semibold text-rose-700 tabular-nums">
                                            {{ formatarNumero(mes.ReembolsoValor) }}
                                        </td>
                                        <td class="text-right px-3 py-2 font-bold bg-rose-100 text-rose-800 tabular-nums">
                                            {{ formatarNumero(totalReembolso12Meses) }}
                                        </td>
                                    </tr>
                                    <tr class="bg-amber-50/70 font-bold border-t-2 border-amber-200">
                                        <td class="border-r border-slate-200 px-3 py-2.5 text-left text-amber-900">
                                            <i class="fas fa-balance-scale mr-2 text-amber-600"></i>Saldo Líquido
                                        </td>
                                        <td v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                            class="text-right border-r border-slate-200 px-2 py-2 tabular-nums"
                                            :class="Number(mes.ReembolsoValor) - Number(mes.DesembolsoValor) >= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            {{ formatarDelta(Number(mes.ReembolsoValor) - Number(mes.DesembolsoValor)) }}
                                        </td>
                                        <td class="text-right px-3 py-2 font-bold tabular-nums"
                                            :class="totalReembolso12Meses - totalDesembolso12Meses >= 0 ? 'text-emerald-700' : 'text-rose-700'">
                                            {{ formatarDelta(totalReembolso12Meses - totalDesembolso12Meses) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ═══ TABELA 5: Provisões por Produtos ═══ -->
                    <section v-if="provisoesAgrupadas.length > 0">
                        <SectionTitle
                            icon="fa-shield-virus"
                            title="Provisões por Produtos"
                            subtitle="Comparativo anual e mensal por tipo"
                            tone="amber"
                        />

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs border-collapse text-slate-800">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-[#103E68] text-white text-center text-[11px]">
                                        <th class="border-r border-b border-slate-300 px-3 py-2 text-left w-56 uppercase tracking-wider">Tipo / Produto</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">% Dado</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Fecho AA</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Mês Anterior</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider bg-[#0B2E4F]">Mês Actual</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Dif. Mensal</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Dif. Anual</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Dif. Média Mensal</th>
                                        <th class="border-b border-slate-300 px-2 py-2 uppercase tracking-wider">% Var. Anual</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template v-for="grupo in provisoesAgrupadas" :key="grupo.tipo">
                                        <tr class="bg-gradient-to-r from-blue-50 to-transparent">
                                            <td colspan="9" class="px-3 py-2 text-[11px] uppercase tracking-wider font-bold text-blue-800 border-y border-blue-100">
                                                <i class="fas fa-folder-open mr-2 text-blue-500"></i>{{ grupo.tipo }}
                                            </td>
                                        </tr>
                                        <tr v-for="item in grupo.itens" :key="item.Id" class="hover:bg-blue-50/40 transition-colors">
                                            <td class="border-r border-slate-200 px-3 py-2 text-left whitespace-nowrap pl-6 font-medium text-slate-700">
                                                <span class="inline-flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                    {{ item.NomeProduto }}
                                                </span>
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-2 text-center bg-slate-50/50 font-semibold text-slate-700 tabular-nums">
                                                {{ item.PercentagemDado !== null ? item.PercentagemDado + '%' : '—' }}
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-2 text-right font-semibold tabular-nums">
                                                {{ formatarNumero(item.ProvisaoValorFechoAnoAnterior) }}
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-2 text-right tabular-nums">
                                                {{ formatarNumero(item.ProvisaoValorMesAnterior) }}
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-2 text-right font-bold text-amber-900 bg-amber-50 tabular-nums">
                                                {{ formatarNumero(item.ProvisaoValorMesActual) }}
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-2 text-right font-semibold tabular-nums"
                                                :class="obterCorDiferenca(item.ProvisaoDiferencaMensal)">
                                                {{ formatarDelta(item.ProvisaoDiferencaMensal) }}
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-2 text-right font-semibold tabular-nums"
                                                :class="obterCorDiferenca(item.ProvisaoDiferencaAnual)">
                                                {{ formatarDelta(item.ProvisaoDiferencaAnual) }}
                                            </td>
                                            <td class="border-r border-slate-200 px-2 py-2 text-right tabular-nums text-slate-600">
                                                {{ formatarNumero(item.ProvisaoDiferencaMediaMensal) }}
                                            </td>
                                            <td class="px-2 py-2 text-center font-bold tabular-nums">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]"
                                                      :class="obterCorDiferenca(item.PercentVariaAnual)">
                                                    {{ formatarPercentual(item.PercentVariaAnual) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ═══ TABELA 6: Dados Estatísticos ═══ -->
                    <section v-if="dadosEstatisticosRD.length > 0">
                        <SectionTitle
                            icon="fa-users"
                            title="Dados Estatísticos Operacionais & Demográficos"
                            subtitle="Demografia, tempos de atendimento e taxas médias"
                            tone="indigo"
                        />

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs border-collapse text-slate-800">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-[#103E68] text-white text-center text-[11px]">
                                        <th class="border-r border-b border-slate-300 px-3 py-2 text-left uppercase tracking-wider">Escopo</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">♂ Homens</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">♀ Mulheres</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Solic→Aprov</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Aprov→Desemb</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider bg-[#0B2E4F]">TA Total</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Média Desemb.</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Média Créd.</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Média Prest.</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">% Reemb.</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">% Cumpr.</th>
                                        <th class="border-b border-slate-300 px-2 py-2 uppercase tracking-wider">% Recup.</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="est in dadosEstatisticosRD" :key="est.Id" class="hover:bg-blue-50/40 transition-colors">
                                        <td class="border-r border-slate-200 px-3 py-2 font-bold text-slate-900 bg-slate-50">
                                            {{ est.TipoAgrupamento }}
                                            <span class="text-slate-500 font-normal text-[10px] block">{{ est.NivelEscopo }}</span>
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-center text-blue-700 font-semibold tabular-nums">
                                            {{ formatarInt(est.Homens_Qtd) }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-center text-pink-600 font-semibold tabular-nums">
                                            {{ formatarInt(est.Mulheres_Qtd) }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-center text-emerald-700 font-semibold tabular-nums">
                                            {{ est.TempoSolicitacaoAprovacaoDias ?? '—' }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-center text-emerald-700 font-semibold tabular-nums">
                                            {{ est.TempoAprovacaoDesembolsoDias ?? '—' }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-center font-bold bg-emerald-50 text-emerald-800 tabular-nums">
                                            {{ est.TempoAtendimentoTotalDias ?? '—' }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right font-semibold tabular-nums">{{ formatarNumero(est.MediaDesembolsoCredito) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-center tabular-nums">{{ formatarInt(est.MediaCreditosOficial) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right tabular-nums">{{ formatarNumero(est.MediaPrestacao) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-center font-bold tabular-nums">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">
                                                {{ formatarPercentual(est.TaxaReembolsoPct) }}
                                            </span>
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-center font-bold tabular-nums">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">
                                                {{ formatarPercentual(est.TaxaCumprimentoPct) }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2 text-center font-bold tabular-nums">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">
                                                {{ formatarPercentual(est.TaxaRecuperacaoPct) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ═══ TABELA 7: Write-Off ═══ -->
                    <section v-if="carteiraWriteOffRD.length > 0">
                        <SectionTitle
                            icon="fa-shield-alt"
                            title="Análise de Recuperação da Carteira Write-Off"
                            subtitle="Períodos de abate & recuperação acumulada"
                            tone="amber"
                        />

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs border-collapse text-slate-800">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-[#103E68] text-white text-center text-[11px]">
                                        <th class="border-r border-b border-slate-300 px-3 py-2 text-left uppercase tracking-wider">Período</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Bal. Inicial</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Bal. Final</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Rec. Ant.</th>
                                        <th v-for="n in 5" :key="n" class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Mês {{ n }}</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider bg-[#0B2E4F]">Total Rec.</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">% Rec. BI</th>
                                        <th class="border-b border-slate-300 px-2 py-2 uppercase tracking-wider">% KR</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="wo in carteiraWriteOffRD" :key="wo.Id" class="hover:bg-amber-50/40 transition-colors">
                                        <td class="border-r border-slate-200 px-3 py-2 font-bold text-slate-900 bg-slate-50">
                                            {{ wo.EtiquetaPeriodoWO }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right font-semibold tabular-nums">{{ formatarNumero(wo.BalancoInicial) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right tabular-nums">{{ formatarNumero(wo.BalancoFinal) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right text-slate-600 tabular-nums">{{ formatarNumero(wo.RecuperadoAcumuladoAnterior) }}</td>
                                        <td v-for="m in ['RecuperadoMes1','RecuperadoMes2','RecuperadoMes3','RecuperadoMes4','RecuperadoMes5']" :key="m"
                                            class="border-r border-slate-200 px-2 py-2 text-right bg-emerald-50/40 text-emerald-700 font-semibold tabular-nums">
                                            {{ formatarNumero(wo[m]) }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right font-bold bg-emerald-100 text-emerald-800 tabular-nums">
                                            {{ formatarNumero(wo.TotalRecuperado) }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-center font-bold bg-amber-50 text-amber-800 tabular-nums">
                                            {{ formatarPercentual(wo.PctRecuperadoBalancoInicial) }}
                                        </td>
                                        <td class="px-2 py-2 text-center font-bold tabular-nums">
                                            {{ formatarPercentual(wo.PctKR) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- ═══ TABELA 8: Movimento de Prestações ═══ -->
                    <section v-if="movimentoPrestacaoRD.length > 0">
                        <SectionTitle
                            icon="fa-calculator"
                            title="Movimento de Prestações (Capitais e Juros)"
                            subtitle="Evolução do fluxo financeiro"
                            tone="blue"
                        />

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-xs border-collapse text-slate-800">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-[#103E68] text-white text-center text-[11px]">
                                        <th class="border-r border-b border-slate-300 px-3 py-2 text-left uppercase tracking-wider">Período</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Cap. Inicial</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Cap. Desemb.</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Cap. Reemb.</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Cap. Final</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Jur. Inicial</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Jur. Desemb.</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Jur. Reemb.</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Jur. Final</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider bg-[#0B2E4F]">Total Rec.</th>
                                        <th class="border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider">% Rec.</th>
                                        <th class="border-b border-slate-300 px-2 py-2 uppercase tracking-wider">Tx Rec.</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="mp in movimentoPrestacaoRD" :key="mp.Id" class="hover:bg-blue-50/40 transition-colors">
                                        <td class="border-r border-slate-200 px-3 py-2 font-bold text-slate-900 bg-slate-50">
                                            {{ mp.PeriodoPrestacao }}
                                        </td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right tabular-nums">{{ formatarNumero(mp.CapIniValor) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right text-emerald-700 font-semibold tabular-nums">{{ formatarNumero(mp.CapDesValor) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right text-rose-700 font-semibold tabular-nums">{{ formatarNumero(mp.CapReeValor) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right font-bold tabular-nums">{{ formatarNumero(mp.CapFimValor) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right tabular-nums">{{ formatarNumero(mp.JurIniValor) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right text-emerald-700 tabular-nums">{{ formatarNumero(mp.JurDesValor) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right text-rose-700 tabular-nums">{{ formatarNumero(mp.JurReeValor) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right font-bold tabular-nums">{{ formatarNumero(mp.JurFimValor) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-right font-bold bg-emerald-100 text-emerald-800 tabular-nums">{{ formatarNumero(mp.TotalRecuperado) }}</td>
                                        <td class="border-r border-slate-200 px-2 py-2 text-center font-bold bg-amber-50 text-amber-800 tabular-nums">{{ formatarPercentual(mp.PctRecuperado) }}</td>
                                        <td class="px-2 py-2 text-center font-bold tabular-nums">{{ formatarPercentual(mp.TxRecuperacao) }}</td>
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
    user: Object
})

/* ══════════════════════════════════════════════════
   COMPONENTES INTERNOS
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
        class: `relative bg-white rounded-2xl shadow-sm border border-slate-200 p-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden`
    }, [
        h('div', { class: `absolute -top-8 -right-8 w-24 h-24 rounded-full bg-gradient-to-br ${t.grad} to-transparent opacity-60` }),
        h('div', { class: 'relative' }, [
            h('div', { class: 'flex items-center justify-between mb-2' }, [
                h('span', { class: 'text-[10px] font-bold uppercase tracking-wider text-slate-500' }, p.label),
                h('div', { class: `w-8 h-8 rounded-lg ${t.bg} flex items-center justify-center ring-1 ${t.ring}` }, [
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
    }
    return h('div', { class: 'flex items-center gap-3 mb-4 border-l-4 pl-3 py-1 ' + (tones[p.tone] || tones.blue) }, [
        h('i', { class: `fas ${p.icon} text-lg` }),
        h('div', [
            h('h2', { class: 'text-base font-bold text-slate-900 leading-tight' }, p.title),
            p.subtitle && h('p', { class: 'text-[11px] text-slate-500 mt-0.5' }, p.subtitle)
        ])
    ])
}
SectionTitle.props = ['icon', 'title', 'subtitle', 'tone']

const AgenciasTable = (p) => {
    return h('div', { class: 'overflow-x-auto rounded-xl border border-slate-200' }, [
        h('table', { class: 'w-full text-xs border-collapse text-slate-800' }, [
            h('thead', [
                h('tr', { class: 'bg-[#0B2E4F] text-white' }, [
                    h('th', { colspan: 11, class: 'px-3 py-2 text-sm tracking-wider uppercase text-left' }, [
                        h('i', { class: 'fas fa-location-dot mr-2 opacity-70' }),
                        p.titulo
                    ])
                ]),
                h('tr', { class: 'bg-[#103E68] text-white text-center text-[11px]' }, [
                    h('th', { class: 'border-r border-b border-slate-300 px-3 py-2 text-left w-48 uppercase tracking-wider' }, 'Localidade'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider' }, 'OA/PA'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider' }, 'Balanço'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider' }, 'Créditos | Novos'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider' }, 'Clientes | Novos'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider' }, 'PAR 1'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider' }, 'PAR 30'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider' }, 'Desemb. | TA'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider' }, 'Reembolso'),
                    h('th', { class: 'border-r border-b border-slate-300 px-2 py-2 uppercase tracking-wider' }, 'Provisão'),
                    h('th', { class: 'border-b border-slate-300 px-2 py-2 uppercase tracking-wider' }, 'Gestão')
                ])
            ]),
            h('tbody', { class: 'divide-y divide-slate-100' },
                p.agencias.map(a => h('tr', {
                    key: a.Id,
                    class: a.IsTotalizador
                        ? 'bg-gradient-to-r from-amber-50 to-amber-50/40 font-bold text-slate-900 border-t-2 border-b-2 border-amber-200'
                        : 'hover:bg-blue-50/40 transition-colors font-medium'
                }, [
                    h('td', { class: 'border-r border-slate-200 px-3 py-2 text-left whitespace-nowrap' }, [
                        !a.IsTotalizador && h('span', { class: 'text-slate-400 mr-1' }, '─'),
                        h('span', { class: a.IsTotalizador ? '' : 'pl-2' }, a.NomeLocal),
                        a.PesoPercentual && h('span', { class: 'text-slate-500 font-normal ml-1 text-[10px]' }, `(${Math.round(a.PesoPercentual)}%)`)
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-2 text-center bg-slate-50/60 text-slate-600 tabular-nums' },
                        `${a.OA_Qtd ?? '—'}/${a.PA_Qtd ?? '—'}`),
                    h('td', { class: 'border-r border-slate-200 px-2 py-2 text-right font-semibold tabular-nums' }, formatarNumero(a.BalancoValor)),
                    h('td', { class: 'border-r border-slate-200 px-2 py-2 text-right tabular-nums' }, [
                        h('span', { class: 'text-slate-700' }, formatarInt(a.CreditosTotal_Qtd)),
                        h('span', { class: 'text-slate-400 mx-1' }, '|'),
                        h('span', { class: 'text-indigo-700 font-bold' }, formatarInt(a.CreditosNovos_Qtd))
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-2 text-right tabular-nums' }, [
                        h('span', { class: 'text-slate-700' }, formatarInt(a.ClientesTotal_Qtd)),
                        h('span', { class: 'text-slate-400 mx-1' }, '|'),
                        h('span', { class: 'text-cyan-700 font-bold' }, formatarInt(a.ClientesNovos_Qtd))
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-2 text-right tabular-nums' }, [
                        formatarK(a.PAR1_Valor),
                        a.PAR1_Percentual !== null && h('span', { class: 'text-[10px] text-amber-700 font-semibold ml-1' }, `(${Math.round(a.PAR1_Percentual)}%)`)
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-2 text-right tabular-nums' }, [
                        formatarK(a.PAR30_Valor),
                        a.PAR30_Percentual !== null && h('span', { class: 'text-[10px] text-rose-700 font-semibold ml-1' }, `(${Math.round(a.PAR30_Percentual)}%)`)
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-2 text-right bg-emerald-50/50 text-emerald-700 font-semibold tabular-nums' }, [
                        formatarNumero(a.DesembolsoValor),
                        a.TempoAtendimentodias !== null && h('span', { class: 'text-[10px] text-slate-500 ml-1' }, `| ${String(a.TempoAtendimentodias).padStart(2, '0')}`)
                    ]),
                    h('td', { class: 'border-r border-slate-200 px-2 py-2 text-right bg-rose-50/50 text-rose-700 tabular-nums' }, formatarNumero(a.ReembolsoValor)),
                    h('td', { class: 'border-r border-slate-200 px-2 py-2 text-right tabular-nums' }, [
                        formatarNumero(a.ProvisaoValor),
                        a.ProvisaoClasseRisco && h('span', { class: 'font-bold text-slate-700 ml-1' }, `(${a.ProvisaoClasseRisco})`)
                    ]),
                    h('td', { class: 'px-2 py-2 text-center font-bold tabular-nums' }, [
                        h('span', {
                            class: 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] ' + obterCorGestao(a.GestaoValor)
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
   COMPUTED
   ══════════════════════════════════════════════════ */
const historicoSeisMeses = computed(() => (props.historicoRD || []).slice(0, 6))

const ultimoRegistro = computed(() => {
    if (!props.historicoRD || props.historicoRD.length === 0) return null
    return props.historicoRD.length >= 7
        ? props.historicoRD[6]
        : props.historicoRD[props.historicoRD.length - 1]
})

const registroAnterior = computed(() => {
    if (!props.historicoRD || props.historicoRD.length < 2) return null
    const idx = props.historicoRD.length >= 7 ? 6 : props.historicoRD.length - 1
    return props.historicoRD[idx - 1] || null
})

const colunasMeses = computed(() => historicoSeisMeses.value.map(item => {
    const data = new Date(item.DataReferencia)
    const mesExtenso = data.toLocaleDateString('pt-PT', { month: 'short' })
    const anoCurto = data.getFullYear().toString().substring(2)
    const label = mesExtenso.charAt(0).toUpperCase() + mesExtenso.slice(1, 3) + '/' + anoCurto
    return { key: item.Id, label }
}))

const ultimoMesNome = computed(() => {
    if (!ultimoRegistro.value) return ''
    const d = new Date(ultimoRegistro.value.DataReferencia)
    return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`
})

/* ── Agrupamento de Provisões (por tipo) ── */
const provisoesAgrupadas = computed(() => agrupar(props.provisoesProdutosRD, 'TipoAgrupamento'))

/* ── Agrupamento de Produtos POR DIREÇÃO / GESTOR ── */
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
                    BalancoValor: 0,
                    CreditosTotal_Qtd: 0,
                    CreditosNovos_Qtd: 0,
                    ClientesTotal_Qtd: 0,
                    ClientesNovos_Qtd: 0,
                    PAR1_Valor: 0,
                    PAR30_Valor: 0,
                    DesembolsoValor: 0,
                    ReembolsoValor: 0,
                    ProvisaoValor: 0,
                    OA_Qtd: 0,
                    PA_Qtd: 0,
                }
            }
        }

        grupos[gestor].itens.push(item)

        const t = grupos[gestor].total
        t.BalancoValor      += Number(item.BalancoValor)      || 0
        t.CreditosTotal_Qtd += Number(item.CreditosTotal_Qtd) || 0
        t.CreditosNovos_Qtd += Number(item.CreditosNovos_Qtd) || 0
        t.ClientesTotal_Qtd += Number(item.ClientesTotal_Qtd) || 0
        t.ClientesNovos_Qtd += Number(item.ClientesNovos_Qtd) || 0
        t.PAR1_Valor        += Number(item.PAR1_Valor)        || 0
        t.PAR30_Valor       += Number(item.PAR30_Valor)       || 0
        t.DesembolsoValor   += Number(item.DesembolsoValor)   || 0
        t.ReembolsoValor    += Number(item.ReembolsoValor)    || 0
        t.ProvisaoValor     += Number(item.ProvisaoValor)     || 0
        t.OA_Qtd            += Number(item.OA_Qtd)            || 0
        t.PA_Qtd            += Number(item.PA_Qtd)            || 0
    })

    return Object.values(grupos).sort((a, b) =>
        a.gestor.localeCompare(b.gestor, 'pt-PT', { sensitivity: 'base' })
    )
})

const agenciasCapital  = computed(() => (props.agenciasRD || []).filter(i => i.TipoAgrupamento === 'CAPITAL'))
const agenciasProvincia = computed(() => (props.agenciasRD || []).filter(i => i.TipoAgrupamento === 'PROVINCIA'))

const totalDesembolso12Meses = computed(() =>
    (props.desembolsosReembolsos12MesesRD || []).reduce((a, i) => a + Number(i.DesembolsoValor || 0), 0))
const totalReembolso12Meses = computed(() =>
    (props.desembolsosReembolsos12MesesRD || []).reduce((a, i) => a + Number(i.ReembolsoValor || 0), 0))

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
   HELPERS
   ══════════════════════════════════════════════════ */
function agrupar(lista, chave) {
    if (!lista || lista.length === 0) return []
    const g = {}
    lista.forEach(i => {
        const t = i[chave] || 'OUTROS'
        ;(g[t] ||= []).push(i)
    })
    return Object.keys(g).map(t => ({ tipo: t, itens: g[t] }))
}

const calcularDelta = (atual, anterior) => {
    const a = Number(atual), b = Number(anterior)
    if (!isFinite(a) || !isFinite(b) || b === 0) return null
    return ((a - b) / Math.abs(b)) * 100
}

const obterNivelRisco = (diasAtraso) => {
    if (diasAtraso === null || diasAtraso === undefined) return 'A'
    const da = Number(diasAtraso)
    if (da <= 7) return 'A'
    if (da <= 15) return 'B'
    if (da <= 30) return 'C'
    if (da <= 45) return 'D'
    if (da <= 75) return 'E'
    if (da <= 90) return 'F'
    return 'G'
}

const formatarProvisaoCobertura = (diasAtraso, percentual) => {
    if (percentual === null || percentual === undefined) return '—'
    return `(${obterNivelRisco(diasAtraso)} :: ${formatarNumero(percentual)}%)`
}

/* ══════════════════════════════════════════════════
   FORMATADORES NUMÉRICOS (pt-PT)
   ══════════════════════════════════════════════════ */
const _toNum = (val) => {
    if (val === null || val === undefined || val === '') return NaN
    if (typeof val === 'string') {
        return Number(val.includes(',') ? val.replace(/\./g, '').replace(',', '.') : val.trim())
    }
    return Number(val)
}

const formatarNumero = (val) => {
    const n = _toNum(val)
    if (!isFinite(n)) return '—'
    return n.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const formatarInt = (val) => {
    const n = _toNum(val)
    if (!isFinite(n)) return '—'
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
    if (!isFinite(n)) return '—'
    return n.toLocaleString('pt-PT', { minimumFractionDigits: 0, maximumFractionDigits: 1 }) + '%'
}

const formatarDelta = (val) => {
    const n = _toNum(val)
    if (!isFinite(n)) return '—'
    const prefixo = n > 0 ? '+' : ''
    return prefixo + n.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

/* ══════════════════════════════════════════════════
   CORES AUXILIARES
   ══════════════════════════════════════════════════ */
const obterCorGestao = (valor) => {
    const n = Number(valor)
    if (!isFinite(n)) return 'bg-slate-100 text-slate-500'
    if (n > 0) return 'bg-emerald-100 text-emerald-700'
    if (n < 0) return 'bg-rose-100 text-rose-700'
    return 'bg-amber-100 text-amber-800'
}

const obterCorDiferenca = (val) => {
    const n = Number(val)
    if (!isFinite(n)) return 'text-slate-500'
    if (n < 0) return 'text-emerald-700'
    if (n > 0) return 'text-rose-700'
    return 'text-slate-600'
}

/* ══════════════════════════════════════════════════
   CHARTS
   ══════════════════════════════════════════════════ */
const CHART_FONT = { family: 'ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif' }

const destroyCharts = () => {
    Object.keys(charts).forEach(k => { if (charts[k]) { charts[k].destroy(); charts[k] = null } })
}

const initCharts = () => {
    destroyCharts()

    /* ── 1. Evolução do Balanço ── */
    if (chartBalancoRef.value && historicoSeisMeses.value.length > 0) {
        const labels = colunasMeses.value.map(m => m.label)
        const data = historicoSeisMeses.value.map(i => Number(i.BalancoValor) || 0)

        const ctx = chartBalancoRef.value.getContext('2d')
        const grad = ctx.createLinearGradient(0, 0, 0, 220)
        grad.addColorStop(0, 'rgba(59, 130, 246, 0.35)')
        grad.addColorStop(1, 'rgba(59, 130, 246, 0.0)')

        charts.balanco = new Chart(chartBalancoRef.value, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Balanço',
                    data,
                    borderColor: '#2563EB',
                    backgroundColor: grad,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
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
                        backgroundColor: '#0B2E4F',
                        padding: 10,
                        titleFont: { ...CHART_FONT, size: 11 },
                        bodyFont: { ...CHART_FONT, size: 12, weight: 'bold' },
                        callbacks: { label: c => ' ' + formatarNumero(c.parsed.y) }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { ...CHART_FONT, size: 10 }, color: '#64748b' } },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { ...CHART_FONT, size: 10 }, color: '#64748b',
                            callback: v => formatarCompacto(v)
                        }
                    }
                }
            }
        })
    }

    /* ── 2. Desembolso vs Reembolso ── */
    if (chartFluxoRef.value && props.desembolsosReembolsos12MesesRD?.length) {
        const labels = props.desembolsosReembolsos12MesesRD.map(m => m.NomeDoMes)
        const desemb = props.desembolsosReembolsos12MesesRD.map(m => Number(m.DesembolsoValor) || 0)
        const reemb  = props.desembolsosReembolsos12MesesRD.map(m => Number(m.ReembolsoValor) || 0)

        charts.fluxo = new Chart(chartFluxoRef.value, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Desembolso', data: desemb, backgroundColor: '#10B981', borderRadius: 6, maxBarThickness: 22 },
                    { label: 'Reembolso',  data: reemb,  backgroundColor: '#F43F5E', borderRadius: 6, maxBarThickness: 22 }
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
                        backgroundColor: '#0B2E4F',
                        padding: 10,
                        titleFont: { ...CHART_FONT, size: 11 },
                        bodyFont: { ...CHART_FONT, size: 11 },
                        callbacks: { label: c => ' ' + c.dataset.label + ': ' + formatarNumero(c.parsed.y) }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { ...CHART_FONT, size: 9 }, color: '#64748b' } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { ...CHART_FONT, size: 10 }, color: '#64748b', callback: v => formatarCompacto(v) } }
                }
            }
        })
    }

    /* ── 3. Qualidade da Carteira ── */
    if (chartQualidadeRef.value && historicoSeisMeses.value.length > 0) {
        const labels = colunasMeses.value.map(m => m.label)
        const npl = historicoSeisMeses.value.map(i => Number(i.NPLPercentual) || 0)
        const p1  = historicoSeisMeses.value.map(i => Number(i.PAR1Percentual) || 0)
        const p30 = historicoSeisMeses.value.map(i => Number(i.PAR30Percentual) || 0)

        charts.qualidade = new Chart(chartQualidadeRef.value, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'NPL',    data: npl, borderColor: '#E11D48', backgroundColor: 'transparent', borderWidth: 2, tension: 0.4, pointRadius: 3, pointHoverRadius: 5 },
                    { label: 'PAR 1',  data: p1,  borderColor: '#F97316', backgroundColor: 'transparent', borderWidth: 2, tension: 0.4, pointRadius: 3, pointHoverRadius: 5 },
                    { label: 'PAR 30', data: p30, borderColor: '#F59E0B', backgroundColor: 'transparent', borderWidth: 2, tension: 0.4, pointRadius: 3, pointHoverRadius: 5 }
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

    /* ── 4. Distribuição por Agências ── */
    if (chartAgenciasRef.value && props.agenciasRD?.length) {
        const itens = [...props.agenciasRD]
            .filter(a => !a.IsTotalizador && Number(a.BalancoValor) > 0)
            .sort((a, b) => Number(b.BalancoValor) - Number(a.BalancoValor))
            .slice(0, 8)

        if (itens.length) {
            const palette = ['#2563EB', '#10B981', '#F59E0B', '#E11D48', '#8B5CF6', '#06B6D4', '#F97316', '#64748B']
            charts.agencias = new Chart(chartAgenciasRef.value, {
                type: 'doughnut',
                data: {
                    labels: itens.map(i => i.NomeLocal),
                    datasets: [{
                        data: itens.map(i => Number(i.BalancoValor)),
                        backgroundColor: palette,
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '62%',
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
                            callbacks: { label: c => ' ' + c.label + ': ' + formatarNumero(c.parsed) }
                        }
                    }
                }
            })
        }
    }
}

onMounted(() => nextTick(initCharts))

watch(
    () => [props.historicoRD, props.desembolsosReembolsos12MesesRD, props.agenciasRD],
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
                    @page { size: A4 landscape; margin: 8mm; }
                    body { background-color: #fff !important; padding: 0 !important; margin: 0 !important;
                           -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;
                           font-family: ui-sans-serif, system-ui, sans-serif; }
                    table { font-size: 8.5px !important; page-break-inside: auto; }
                    tr { page-break-inside: avoid; }
                    thead { display: table-header-group; }
                    .shadow-sm, .shadow-xl, .shadow-lg { box-shadow: none !important; }
                    .rounded-2xl, .rounded-xl { border-radius: 6px !important; }
                    section { page-break-inside: avoid; margin-bottom: 12px; }
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
