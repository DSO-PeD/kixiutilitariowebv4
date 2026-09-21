<template>

    <Head title="Reporting Balance Kixi Crédito Angola" />

    <div class="container mx-auto py-4 md:py-6 max-w-full">
        <!-- Cabeçalho Principal (Não sairá na impressão) -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-chart-pie text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Reporting Balance Kixi Crédito Angola</h1>
                    <p class="text-sm text-gray-600 mt-1">Relatório Diário</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <!-- Botão de Impressão A4 das Tabelas -->
                <button @click="imprimirA4" class="btn btn-outline-primary btn-sm flex items-center gap-2">
                    <i class="fa fa-print text-blue-600 text-lg"></i>
                    Imprimir Tabelas (A4)
                </button>

                <a :href="`/reports/rdreport`" class="btn btn-outline-success btn-sm flex items-center gap-1"
                    target="_blank">
                    <i class="fa fa-chart-line text-orange-600 text-xl"></i>
                    Visualizar RD Express
                </a>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <!-- ÁREA EXCLUSIVA DE IMPRESSÃO (capturada via ref) -->
        <div ref="areaTabelas">
            <!-- Título exclusivo para a Impressão (Aparece no topo da folha A4) -->
            <div class="hidden print:block text-center mb-4 pb-2 border-b border-slate-300">
                <h1 class="text-base font-bold text-slate-800 uppercase tracking-wide">
                    RD Express 2026-08-21 [KIXICREDITO ANGOLA]
                </h1>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 space-y-8">

                <!-- TABELA 1: Reporting Global (Histórico Mensal) -->
                <div class="overflow-x-auto bg-white shadow-sm rounded-lg border border-slate-300">
                    <table class="w-full text-xs font-sans border-collapse text-slate-800">
                        <thead>
                            <tr class="bg-[#103E68] text-white font-bold text-center text-sm border-b border-slate-300">
                                <th :colspan="colunasMeses.length + 5" class="py-2 tracking-wider">
                                    KIXICRÉDITO ANGOLA (100%)
                                </th>
                            </tr>
                            <tr
                                class="bg-[#246294] text-white font-semibold text-center text-xs border-b border-slate-300">
                                <th :colspan="colunasMeses.length + 5" class="py-1 tracking-wide">
                                    PCE: JOAQUIM CATINDA
                                </th>
                            </tr>
                            <tr class="bg-[#103E68] text-white text-center font-bold">
                                <th rowspan="2"
                                    class="w-32 bg-slate-100 text-slate-500 font-bold border-r border-b border-slate-300 px-2 py-1 align-bottom text-left">
                                    Indicadores
                                </th>
                                <th :colspan="colunasMeses.length" class="border-r border-b border-slate-300 py-1">
                                    Histórico Mensal</th>
                                <th colspan="2" class="border-r border-b border-slate-300 py-1">Variação</th>
                                <th class="border-r border-b border-slate-300 py-1">{{ ultimoMesNome }}</th>
                                <th class="border-b border-slate-300 py-1">OM</th>
                            </tr>
                            <tr class="bg-[#337AB7] text-white text-center font-semibold">
                                <th v-for="mes in colunasMeses" :key="mes.key"
                                    class="border-r border-b border-slate-300 px-3 py-1 whitespace-nowrap">
                                    {{ mes.label }}
                                </th>
                                <th class="border-r border-b border-slate-300 px-3 py-1 bg-[#DDEBF7] text-[#1F497D]">+
                                </th>
                                <th class="border-r border-b border-slate-300 px-3 py-1 bg-[#FCE4D6] text-[#C00000]">-
                                </th>
                                <th class="border-r border-b border-slate-300 px-3 py-1"></th>
                                <th class="border-b border-slate-300 px-3 py-1"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <!-- Linha 1: Balanço -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="font-bold border-r border-slate-300 px-2 py-1 text-left">1. Balanço</td>
                                <td v-for="item in historicoSeisMeses" :key="item.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1">
                                    {{ formatarNumero(item.BalancoValor) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#E2EFDA] text-[#375623] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.Desembolsos) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#FCE4D6] text-[#C00000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.Reembolso) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#FFF2CC] text-[#806000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.BalancoValor) }}
                                </td>
                                <td class="text-right border-slate-300 px-2 py-1 font-bold"
                                    :class="[Number(ultimoRegistro?.VariacaoPercentual) >= 0 ? 'bg-[#E2EFDA] text-[#375623]' : 'bg-[#FCE4D6] text-[#C00000]']">
                                    {{ formatarPercentual(ultimoRegistro?.VariacaoPercentual) }}
                                </td>
                            </tr>

                            <!-- Linha 2: Créditos Novos -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="font-bold border-r border-slate-300 px-2 py-1 text-left">
                                    2. Créditos | Novos <br /> Taxa Juros &lt;=kz2000
                                </td>
                                <td v-for="item in historicoSeisMeses" :key="item.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1">
                                    {{ formatarNumero(item.CreditosQuantidade) }} | {{
                                        formatarNumero(item.CreditosNovos) }}<br />
                                    {{ formatarNumero(item.TaxaJuros1) }}% | {{ formatarNumero(item.TaxaJuros2)
                                    }}%<br />
                                    {{ formatarNumero(item.CreditosAteKz2000_Qtd) }} | {{
                                        formatarNumero(item.CreditosAteKz2000_Valor) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#E2EFDA] text-[#375623] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.CreditosNovos_Mais) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#FCE4D6] text-[#C00000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.CreditosNovos_Menos) }}
                                </td>
                                <td class="text-right border-r border-slate-300 px-2 py-1 font-bold">
                                    {{ formatarNumero(ultimoRegistro?.CreditosQuantidade) }} | {{
                                        formatarNumero(ultimoRegistro?.CreditosNovos) }}<br />
                                    {{ formatarNumero(ultimoRegistro?.TaxaJuros1) }}% | {{
                                        formatarNumero(ultimoRegistro?.TaxaJuros2) }}%<br />
                                    {{ formatarNumero(ultimoRegistro?.CreditosAteKz2000_Qtd) }} | {{
                                        formatarNumero(ultimoRegistro?.CreditosAteKz2000_Valor) }}
                                </td>
                                <td class="text-right border-slate-300 px-2 py-1 font-bold"
                                    :class="[Number(ultimoRegistro?.VariacaoCreditosNovos) >= 0 ? 'bg-[#E2EFDA] text-[#375623]' : 'bg-[#FCE4D6] text-[#C00000]']">
                                    {{ formatarPercentual(ultimoRegistro?.VariacaoCreditosNovos) }}
                                </td>
                            </tr>

                            <!-- Linha 3: Clientes Novos -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="font-bold border-r border-slate-300 px-2 py-1 text-left">
                                    3. Clientes | Novos <br /> &lt;=kz2000
                                </td>
                                <td v-for="item in historicoSeisMeses" :key="item.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1">
                                    {{ formatarNumero(item.ClientesQuantidade) }} | {{
                                        formatarNumero(item.ClientesNovos) }}<br />
                                    {{ formatarNumero(item.ClientesAteKz2000) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#E2EFDA] text-[#375623] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.ClientesNovos_Mais) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#FCE4D6] text-[#C00000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.ClientesNovos_Menos) }}
                                </td>
                                <td class="text-right border-r border-slate-300 px-2 py-1 font-bold">
                                    {{ formatarNumero(ultimoRegistro?.ClientesQuantidade) }} | {{
                                        formatarNumero(ultimoRegistro?.ClientesNovos) }}<br />
                                    {{ formatarNumero(ultimoRegistro?.ClientesAteKz2000) }}
                                </td>
                                <td class="text-right border-slate-300 px-2 py-1 font-bold"
                                    :class="[Number(ultimoRegistro?.VariacaoClientesNovos) >= 0 ? 'bg-[#E2EFDA] text-[#375623]' : 'bg-[#FCE4D6] text-[#C00000]']">
                                    {{ formatarPercentual(ultimoRegistro?.VariacaoClientesNovos) }}
                                </td>
                            </tr>

                            <!-- Linha 4: NPL -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="font-bold border-r border-slate-300 px-2 py-1 text-left">4. NPL</td>
                                <td v-for="item in historicoSeisMeses" :key="item.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1">
                                    {{ formatarNumero(item.NPLPercentual) }}%<br /> {{ formatarNumero(item.NPLValor) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#E2EFDA] text-[#375623] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.NPL_Mais) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#FCE4D6] text-[#C00000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.NPL_Menos) }}
                                </td>
                                <td class="text-right border-r border-slate-300 px-2 py-1 font-bold">
                                    {{ formatarNumero(ultimoRegistro?.NPLPercentual) }}%<br /> {{
                                        formatarNumero(ultimoRegistro?.NPLValor) }}
                                </td>
                                <td class="text-right border-slate-300 px-2 py-1 font-bold"
                                    :class="[Number(ultimoRegistro?.VariacaoNPL) <= 0 ? 'bg-[#E2EFDA] text-[#375623]' : 'bg-[#FCE4D6] text-[#C00000]']">
                                    {{ formatarPercentual(ultimoRegistro?.VariacaoNPL) }}
                                </td>
                            </tr>

                            <!-- Linha 5: PAR 1 -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="font-bold border-r border-slate-300 px-2 py-1 text-left">5. PAR 1</td>
                                <td v-for="item in historicoSeisMeses" :key="item.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1">
                                    {{ formatarNumero(item.PAR1Percentual) }} <br /> {{ formatarNumero(item.PAR1Valor)
                                    }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#E2EFDA] text-[#375623] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.PAR1_Mais) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#FCE4D6] text-[#C00000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.PAR1_Menos) }}
                                </td>
                                <td class="text-right border-r border-slate-300 px-2 py-1 font-bold">
                                    {{ formatarNumero(ultimoRegistro?.PAR1Percentual) }} <br /> {{
                                        formatarNumero(ultimoRegistro?.PAR1Valor) }}
                                </td>
                                <td class="text-right border-slate-300 px-2 py-1 font-bold"
                                    :class="[Number(ultimoRegistro?.VariacaoPAR1) <= 0 ? 'bg-[#E2EFDA] text-[#375623]' : 'bg-[#FCE4D6] text-[#C00000]']">
                                    {{ formatarPercentual(ultimoRegistro?.VariacaoPAR1) }}
                                </td>
                            </tr>

                            <!-- Linha 6: PAR 30 -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="font-bold border-r border-slate-300 px-2 py-1 text-left">6. PAR 30</td>
                                <td v-for="item in historicoSeisMeses" :key="item.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1">
                                    {{ formatarNumero(item.PAR30Percentual) }} <br /> {{ formatarNumero(item.PAR30Valor)
                                    }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#E2EFDA] text-[#375623] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.PAR30_Mais) }}
                                </td>
                                <td class="text-right border-slate-300 px-2 py-1 bg-[#FCE4D6] text-[#C00000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.PAR30_Menos) }}
                                </td>
                                <td class="text-right border-r border-slate-300 px-2 py-1 font-bold">
                                    {{ formatarNumero(ultimoRegistro?.PAR30Percentual) }} <br /> {{
                                        formatarNumero(ultimoRegistro?.PAR30Valor) }}
                                </td>
                                <td class="text-right border-slate-300 px-2 py-1 font-bold"
                                    :class="[Number(ultimoRegistro?.VariacaoPAR30) <= 0 ? 'bg-[#E2EFDA] text-[#375623]' : 'bg-[#FCE4D6] text-[#C00000]']">
                                    {{ formatarPercentual(ultimoRegistro?.VariacaoPAR30) }}
                                </td>
                            </tr>

                            <!-- Linha 7: Provisão -->
                            <tr class="bg-[#FCE4D6]/50 hover:bg-[#FCE4D6]/70 font-medium text-[#C00000]">
                                <td class="font-bold border-r border-slate-300 px-2 py-1 text-left text-slate-800">
                                    7. Provisão
                                </td>

                                <!-- Histórico dos 6 Meses -->
                                <td v-for="item in historicoSeisMeses" :key="item.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1 font-semibold">
                                    {{ formatarNumero(item.ProvisaoValor) }} <br />
                                    {{ formatarProvisaoCobertura(item.ProvisaoDiasAtraso, item.ProvisaoCoberturaPerc) }}
                                    <br />
                                    (DA : {{ item.ProvisaoDiasAtraso ?? 0 }})
                                </td>

                                <!-- Variações (+) e (-) -->
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#E2EFDA] text-[#375623] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.Provisao_Mais) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#FCE4D6] text-[#C00000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.Provisao_Menos) }}
                                </td>

                                <!-- Último Mês / Mês Atual -->
                                <td class="text-right border-r border-slate-300 px-2 py-1 font-bold text-slate-800">
                                    {{ formatarNumero(ultimoRegistro?.ProvisaoValor) }} <br />
                                    {{ formatarProvisaoCobertura(ultimoRegistro?.ProvisaoDiasAtraso,
                                        ultimoRegistro?.ProvisaoCoberturaPerc) }} <br />
                                    (DA : {{ ultimoRegistro?.ProvisaoDiasAtraso ?? 0 }})
                                </td>

                                <!-- Variação por Indicador (OM) -->
                                <td class="text-right border-slate-300 px-2 py-1 font-bold"
                                    :class="[Number(ultimoRegistro?.VariacaoProvisao) <= 0 ? 'bg-[#E2EFDA] text-[#375623]' : 'bg-[#FCE4D6] text-[#C00000]']">
                                    {{ formatarPercentual(ultimoRegistro?.VariacaoProvisao) }}
                                </td>
                            </tr>

                            <!-- Linha 8: Taxa de Reembolso -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="font-bold border-r border-slate-300 px-2 py-1 text-left">8. Taxa de Reembolso
                                </td>
                                <td v-for="item in historicoSeisMeses" :key="item.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1">
                                    {{ formatarPercentual(item.TaxaReembolsoPercentual) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#E2EFDA] text-[#375623] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.TaxaReembolso_Mais) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#FCE4D6] text-[#C00000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.TaxaReembolso_Menos) }}
                                </td>
                                <td class="text-right border-r border-slate-300 px-2 py-1 font-bold">
                                    {{ formatarPercentual(ultimoRegistro?.TaxaReembolsoPercentual) }}
                                </td>
                                <td class="text-right border-slate-300 px-2 py-1 font-bold"
                                    :class="[Number(ultimoRegistro?.VariacaoTaxaReembolso) >= 0 ? 'bg-[#E2EFDA] text-[#375623]' : 'bg-[#FCE4D6] text-[#C00000]']">
                                    {{ formatarPercentual(ultimoRegistro?.VariacaoTaxaReembolso) }}
                                </td>
                            </tr>

                            <!-- Linha 9: Taxa de Cumprimento -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="font-bold border-r border-slate-300 px-2 py-1 text-left">9. Taxa de
                                    Cumprimento</td>
                                <td v-for="item in historicoSeisMeses" :key="item.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1">
                                    {{ formatarPercentual(item.TaxaCumprimentoPercentual) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#E2EFDA] text-[#375623] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.TaxaCumprimento_Mais) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#FCE4D6] text-[#C00000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.TaxaCumprimento_Menos) }}
                                </td>
                                <td class="text-right border-r border-slate-300 px-2 py-1 font-bold">
                                    {{ formatarPercentual(ultimoRegistro?.TaxaCumprimentoPercentual) }}
                                </td>
                                <td class="text-right border-slate-300 px-2 py-1 font-bold"
                                    :class="[Number(ultimoRegistro?.VariacaoTaxaCumprimento) >= 0 ? 'bg-[#E2EFDA] text-[#375623]' : 'bg-[#FCE4D6] text-[#C00000]']">
                                    {{ formatarPercentual(ultimoRegistro?.VariacaoTaxaCumprimento) }}
                                </td>
                            </tr>

                            <!-- Linha 10: Taxa de Recuperação -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="font-bold border-r border-slate-300 px-2 py-1 text-left">10. Taxa de
                                    Recuperação</td>
                                <td v-for="item in historicoSeisMeses" :key="item.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1">
                                    {{ formatarPercentual(item.TaxaRecuperacaoPercentual) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#E2EFDA] text-[#375623] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.TaxaRecuperacao_Mais) }}
                                </td>
                                <td
                                    class="text-right border-r border-slate-300 px-2 py-1 bg-[#FCE4D6] text-[#C00000] font-bold">
                                    {{ formatarNumero(ultimoRegistro?.TaxaRecuperacao_Menos) }}
                                </td>
                                <td class="text-right border-r border-slate-300 px-2 py-1 font-bold">
                                    {{ formatarPercentual(ultimoRegistro?.TaxaRecuperacaoPercentual) }}
                                </td>
                                <td class="text-right border-slate-300 px-2 py-1 font-bold"
                                    :class="[Number(ultimoRegistro?.VariacaoTaxaRecuperacao) >= 0 ? 'bg-[#E2EFDA] text-[#375623]' : 'bg-[#FCE4D6] text-[#C00000]']">
                                    {{ formatarPercentual(ultimoRegistro?.VariacaoTaxaRecuperacao) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center gap-3 border-l-4 border-emerald-500 pl-3 py-1">
                    <i class="fa fa-wallet text-emerald-600 text-lg"></i>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        Distribuição da Carteira
                    </h2>
                </div>

                <!-- TABELA 2: Detalhamento por Agências -->
                <div v-if="agenciasCapital.length > 0 || agenciasProvincia.length > 0" class="space-y-6">
                    <!-- Agrupamento CAPITAL -->
                    <div v-if="agenciasCapital.length > 0"
                        class="overflow-x-auto bg-white shadow-sm rounded-lg border border-slate-300">
                        <table class="w-full text-xs font-sans border-collapse text-slate-800">
                            <thead>
                                <tr class="bg-[#103E68] text-white font-bold text-left text-xs">
                                    <th colspan="11" class="px-3 py-2 text-sm tracking-wider uppercase">Capital (Luanda)
                                    </th>
                                </tr>
                                <tr class="bg-[#246294] text-white font-bold text-center text-xs">
                                    <th class="border-r border-b border-slate-300 px-2 py-2 text-left w-48">Localidade
                                    </th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">OA/PA</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Balanço</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Créditos | Novos</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Clientes | Novos</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">PAR 1</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">PAR 30</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Desembolso | TA</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Reembolso</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Provisão</th>
                                    <th class="border-b border-slate-300 px-2 py-2">Gestão</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr v-for="agencia in agenciasCapital" :key="agencia.Id"
                                    :class="[agencia.IsTotalizador ? 'bg-[#FFF2CC] font-bold text-slate-900 border-t-2 border-b-2 border-slate-400' : 'hover:bg-slate-50 font-medium']">
                                    <td class="border-r border-slate-300 px-2 py-1.5 text-left whitespace-nowrap">
                                        <span :class="{ 'pl-3': !agencia.IsTotalizador }">
                                            {{ agencia.IsTotalizador ? '' : '- ' }}{{ agencia.NomeLocal }}
                                            <span v-if="agencia.PesoPercentual" class="text-slate-500 font-normal">({{
                                                Math.round(agencia.PesoPercentual) }}%)</span>
                                        </span>
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-center bg-slate-50">
                                        {{ agencia.OA_Qtd !== null ? agencia.OA_Qtd : '-' }}/{{ agencia.PA_Qtd !== null
                                            ? agencia.PA_Qtd : '-' }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right font-semibold">{{
                                        formatarNumero(agencia.BalancoValor) }}</td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">{{
                                        formatarInt(agencia.CreditosTotal_Qtd) }} | {{
                                            formatarInt(agencia.CreditosNovos_Qtd) }}</td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">{{
                                        formatarInt(agencia.ClientesTotal_Qtd) }} | {{
                                            formatarInt(agencia.ClientesNovos_Qtd) }}</td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">{{
                                        formatarK(agencia.PAR1_Valor) }} <span
                                            v-if="agencia.PAR1_Percentual !== null">({{
                                                Math.round(agencia.PAR1_Percentual) }}%)</span></td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">{{
                                        formatarK(agencia.PAR30_Valor) }} <span
                                            v-if="agencia.PAR30_Percentual !== null">({{
                                                Math.round(agencia.PAR30_Percentual) }}%)</span></td>
                                    <td
                                        class="border-r border-slate-300 px-2 py-1 text-right bg-[#E2EFDA] text-[#375623]">
                                        {{ formatarNumero(agencia.DesembolsoValor) }} <span
                                            v-if="agencia.TempoAtendimentodias !== null">| {{
                                                String(agencia.TempoAtendimentodias).padStart(2, '0') }}</span></td>
                                    <td
                                        class="border-r border-slate-300 px-2 py-1 text-right bg-[#FCE4D6] text-[#C00000]">
                                        {{ formatarNumero(agencia.ReembolsoValor) }}</td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">{{
                                        formatarNumero(agencia.ProvisaoValor) }} <span
                                            v-if="agencia.ProvisaoClasseRisco" class="font-bold text-slate-700">({{
                                                agencia.ProvisaoClasseRisco }})</span></td>
                                    <td class="border-slate-300 px-2 py-1 text-center font-bold"
                                        :class="obterCorGestao(agencia.GestaoValor)">{{ agencia.GestaoValor !== null ?
                                            agencia.GestaoValor + (agencia.GestaoSufixo || '') : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Agrupamento PROVINCIA -->
                    <div v-if="agenciasProvincia.length > 0"
                        class="overflow-x-auto bg-white shadow-sm rounded-lg border border-slate-300">
                        <table class="w-full text-xs font-sans border-collapse text-slate-800">
                            <thead>
                                <tr class="bg-[#103E68] text-white font-bold text-left text-xs">
                                    <th colspan="11" class="px-3 py-2 text-sm tracking-wider uppercase">Províncias</th>
                                </tr>
                                <tr class="bg-[#246294] text-white font-bold text-center text-xs">
                                    <th class="border-r border-b border-slate-300 px-2 py-2 text-left w-48">Localidade
                                    </th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">OA/PA</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Balanço</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Créditos | Novos</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Clientes | Novos</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">PAR 1</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">PAR 30</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Desembolso | TA</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Reembolso</th>
                                    <th class="border-r border-b border-slate-300 px-2 py-2">Provisão</th>
                                    <th class="border-b border-slate-300 px-2 py-2">Gestão</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr v-for="agencia in agenciasProvincia" :key="agencia.Id"
                                    :class="[agencia.IsTotalizador ? 'bg-[#FFF2CC] font-bold text-slate-900 border-t-2 border-b-2 border-slate-400' : 'hover:bg-slate-50 font-medium']">
                                    <td class="border-r border-slate-300 px-2 py-1.5 text-left whitespace-nowrap">
                                        <span :class="{ 'pl-3': !agencia.IsTotalizador }">
                                            {{ agencia.IsTotalizador ? '' : '- ' }}{{ agencia.NomeLocal }}
                                            <span v-if="agencia.PesoPercentual" class="text-slate-500 font-normal">({{
                                                Math.round(agencia.PesoPercentual) }}%)</span>
                                        </span>
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-center bg-slate-50">
                                        {{ agencia.OA_Qtd !== null ? agencia.OA_Qtd : '-' }}/{{ agencia.PA_Qtd !== null
                                            ? agencia.PA_Qtd : '-' }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right font-semibold">{{
                                        formatarNumero(agencia.BalancoValor) }}</td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">{{
                                        formatarInt(agencia.CreditosTotal_Qtd) }} | {{
                                            formatarInt(agencia.CreditosNovos_Qtd) }}</td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">{{
                                        formatarInt(agencia.ClientesTotal_Qtd) }} | {{
                                            formatarInt(agencia.ClientesNovos_Qtd) }}</td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">{{
                                        formatarK(agencia.PAR1_Valor) }} <span
                                            v-if="agencia.PAR1_Percentual !== null">({{
                                                Math.round(agencia.PAR1_Percentual) }}%)</span></td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">{{
                                        formatarK(agencia.PAR30_Valor) }} <span
                                            v-if="agencia.PAR30_Percentual !== null">({{
                                                Math.round(agencia.PAR30_Percentual) }}%)</span></td>
                                    <td
                                        class="border-r border-slate-300 px-2 py-1 text-right bg-[#E2EFDA] text-[#375623]">
                                        {{ formatarNumero(agencia.DesembolsoValor) }} <span
                                            v-if="agencia.TempoAtendimentodias !== null">| {{
                                                String(agencia.TempoAtendimentodias).padStart(2, '0') }}</span></td>
                                    <td
                                        class="border-r border-slate-300 px-2 py-1 text-right bg-[#FCE4D6] text-[#C00000]">
                                        {{ formatarNumero(agencia.ReembolsoValor) }}</td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">{{
                                        formatarNumero(agencia.ProvisaoValor) }} <span
                                            v-if="agencia.ProvisaoClasseRisco" class="font-bold text-slate-700">({{
                                                agencia.ProvisaoClasseRisco }})</span></td>
                                    <td class="border-slate-300 px-2 py-1 text-center font-bold"
                                        :class="obterCorGestao(agencia.GestaoValor)">{{ agencia.GestaoValor !== null ?
                                            agencia.GestaoValor + (agencia.GestaoSufixo || '') : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABELA 3: Detalhamento por Produtos -->
                <div v-if="produtosAgrupados.length > 0"
                    class="overflow-x-auto bg-white shadow-sm rounded-lg border border-slate-300 mt-8">
                    <table class="w-full text-xs font-sans border-collapse text-slate-800">
                        <thead>
                            <tr class="bg-[#103E68] text-white font-bold text-left text-xs">
                                <th colspan="11" class="px-3 py-2 text-sm tracking-wider uppercase bg-[#103E68]">
                                    Detalhamento por Produtos
                                </th>
                            </tr>
                            <tr class="bg-[#246294] text-white font-bold text-center text-xs">
                                <th class="border-r border-b border-slate-300 px-2 py-2 text-left w-56">Tipo / Produto
                                </th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">OA/PA</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Balanço</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Créditos | Novos</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Clientes | Novos</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">PAR 1</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">PAR 30</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Desembolso | TA</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Reembolso</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Provisão</th>
                                <th class="border-b border-slate-300 px-2 py-2">Gestão</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <template v-for="grupo in produtosAgrupados" :key="grupo.tipo">
                                <tr class="bg-[#DDEBF7] text-[#1F497D] font-bold">
                                    <td colspan="11" class="px-3 py-1.5 text-xs uppercase border-b border-slate-300">
                                        Agrupamento: {{ grupo.tipo }}
                                    </td>
                                </tr>
                                <tr v-for="prod in grupo.itens" :key="prod.Id" class="hover:bg-slate-50 font-medium">
                                    <td class="border-r border-slate-300 px-2 py-1.5 text-left whitespace-nowrap pl-4">
                                        - {{ prod.NomeProduto }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-center bg-slate-50">
                                        {{ prod.OA_Qtd !== null ? prod.OA_Qtd : '-' }}/{{ prod.PA_Qtd !== null ?
                                            prod.PA_Qtd : '-' }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right font-semibold">
                                        {{ formatarNumero(prod.BalancoValor) }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">
                                        {{ formatarInt(prod.CreditosTotal_Qtd) }} | {{
                                            formatarInt(prod.CreditosNovos_Qtd) }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">
                                        {{ formatarInt(prod.ClientesTotal_Qtd) }} | {{
                                            formatarInt(prod.ClientesNovos_Qtd) }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">
                                        {{ formatarK(prod.PAR1_Valor) }} <span v-if="prod.PAR1_Percentual !== null">({{
                                            Math.round(prod.PAR1_Percentual) }}%)</span>
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">
                                        {{ formatarK(prod.PAR30_Valor) }} <span
                                            v-if="prod.PAR30_Percentual !== null">({{ Math.round(prod.PAR30_Percentual)
                                            }}%)</span>
                                    </td>
                                    <td
                                        class="border-r border-slate-300 px-2 py-1 text-right bg-[#E2EFDA] text-[#375623]">
                                        {{ formatarNumero(prod.DesembolsoValor) }} <span
                                            v-if="prod.TempoAtendimentodias !== null">| {{
                                                String(prod.TempoAtendimentodias).padStart(2, '0') }}</span>
                                    </td>
                                    <td
                                        class="border-r border-slate-300 px-2 py-1 text-right bg-[#FCE4D6] text-[#C00000]">
                                        {{ formatarNumero(prod.ReembolsoValor) }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">
                                        {{ formatarNumero(prod.ProvisaoValor) }}
                                        <span v-if="prod.ProvisaoClasseRisco" class="font-bold text-slate-700">
                                            ({{ prod.ProvisaoClasseRisco }})
                                        </span>
                                    </td>
                                    <td class="border-slate-300 px-2 py-1 text-center font-bold"
                                        :class="obterCorGestao(prod.GestaoValor)">
                                        {{ prod.GestaoValor !== null ? prod.GestaoValor + (prod.GestaoSufixo || '') :
                                            '-' }}
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center gap-3 border-l-4 border-emerald-500 pl-3 py-1">
                    <i class="fa fa-money-bill text-emerald-600 text-lg"></i>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        Desembolsos e Reembolsos (Últimos 12 Meses)
                    </h2>
                </div>

                <!-- TABELA 5: Desembolso e Reembolso dos Últimos 12 Meses -->
                <div v-if="desembolsosReembolsos12MesesRD.length > 0"
                    class="overflow-x-auto bg-white shadow-sm rounded-lg border border-slate-300 mt-8">
                    <table class="w-full text-xs font-sans border-collapse text-slate-800">
                        <thead>
                            <tr class="bg-[#103E68] text-white font-bold text-left text-xs">
                                <th :colspan="desembolsosReembolsos12MesesRD.length + 2"
                                    class="px-3 py-2 text-sm tracking-wider uppercase bg-[#103E68]">
                                    Evolução de Desembolsos e Reembolsos (Últimos 12 Meses)
                                </th>
                            </tr>
                            <tr class="bg-[#246294] text-white font-bold text-center text-xs">
                                <th class="border-r border-b border-slate-300 px-3 py-2 text-left w-40">Fluxo / Mês</th>
                                <th v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                    class="border-r border-b border-slate-300 px-2 py-2 min-w-[90px] uppercase">
                                    {{ mes.NomeDoMes }}
                                </th>
                                <th class="border-b border-slate-300 px-3 py-2 bg-[#103E68]">Total Acumulado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <!-- Linha Desembolsos (+) -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td
                                    class="font-bold border-r border-slate-300 px-3 py-2 text-left bg-[#E2EFDA] text-[#375623]">
                                    Desembolso (+)
                                </td>
                                <td v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1.5 font-semibold text-[#375623]">
                                    {{ formatarNumero(mes.DesembolsoValor) }}
                                </td>
                                <td class="text-right border-slate-300 px-3 py-2 font-bold bg-[#E2EFDA] text-[#375623]">
                                    {{ formatarNumero(totalDesembolso12Meses) }}
                                </td>
                            </tr>

                            <!-- Linha Reembolsos (-) -->
                            <tr class="hover:bg-slate-50 font-medium">
                                <td
                                    class="font-bold border-r border-slate-300 px-3 py-2 text-left bg-[#FCE4D6] text-[#C00000]">
                                    Reembolso (-)
                                </td>
                                <td v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1.5 font-semibold text-[#C00000]">
                                    {{ formatarNumero(mes.ReembolsoValor) }}
                                </td>
                                <td class="text-right border-slate-300 px-3 py-2 font-bold bg-[#FCE4D6] text-[#C00000]">
                                    {{ formatarNumero(totalReembolso12Meses) }}
                                </td>
                            </tr>

                            <!-- Linha Saldo Líquido do Mês -->
                            <tr class="bg-[#FFF2CC] font-bold text-slate-900 border-t-2 border-slate-400">
                                <td class="border-r border-slate-300 px-3 py-2 text-left">
                                    Saldo Líquido
                                </td>
                                <td v-for="mes in desembolsosReembolsos12MesesRD" :key="mes.Id"
                                    class="text-right border-r border-slate-300 px-2 py-1.5"
                                    :class="[Number(mes.ReembolsoValor) - Number(mes.DesembolsoValor) >= 0 ? 'text-[#375623]' : 'text-[#C00000]']">
                                    {{ formatarNumero(Number(mes.ReembolsoValor) - Number(mes.DesembolsoValor)) }}
                                </td>
                                <td class="text-right border-slate-300 px-3 py-2 font-bold"
                                    :class="[totalReembolso12Meses - totalDesembolso12Meses >= 0 ? 'text-[#375623]' : 'text-[#C00000]']">
                                    {{ formatarNumero(totalReembolso12Meses - totalDesembolso12Meses) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center gap-3 border-l-4 border-emerald-500 pl-3 py-1">
                    <i class="fa fa-chart-line text-emerald-600 text-lg"></i>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                       Provisões
                    </h2>
                </div>

                <!-- TABELA 4: Provisão por Produtos e Serviços -->
                <div v-if="provisoesAgrupadas.length > 0"
                    class="overflow-x-auto bg-white shadow-sm rounded-lg border border-slate-300 mt-8">
                    <table class="w-full text-xs font-sans border-collapse text-slate-800">
                        <thead>
                            <tr class="bg-[#103E68] text-white font-bold text-left text-xs">
                                <th colspan="10" class="px-3 py-2 text-sm tracking-wider uppercase bg-[#103E68]">
                                    Provisão por Produtos e Serviços
                                </th>
                            </tr>
                            <tr class="bg-[#246294] text-white font-bold text-center text-xs">
                                <th class="border-r border-b border-slate-300 px-2 py-2 text-left w-56">Tipo / Produto
                                </th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">% Dado</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Fecho Ano Anterior</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Mês Anterior</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Mês Actual</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Dif. Mensal</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Dif. Anual</th>
                                <th class="border-r border-b border-slate-300 px-2 py-2">Dif. Média Mensal</th>
                                <th class="border-b border-slate-300 px-2 py-2">% Var. Anual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <template v-for="grupo in provisoesAgrupadas" :key="grupo.tipo">
                                <tr class="bg-[#DDEBF7] text-[#1F497D] font-bold">
                                    <td colspan="10" class="px-3 py-1.5 text-xs uppercase border-b border-slate-300">
                                        Agrupamento Provisão: {{ grupo.tipo }}
                                    </td>
                                </tr>
                                <tr v-for="item in grupo.itens" :key="item.Id" class="hover:bg-slate-50 font-medium">
                                    <td class="border-r border-slate-300 px-2 py-1.5 text-left whitespace-nowrap pl-4">
                                        - {{ item.NomeProduto }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-center bg-slate-50">
                                        {{ item.PercentagemDado !== null ? item.PercentagemDado + '%' : '-' }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right font-semibold">
                                        {{ formatarNumero(item.ProvisaoValorFechoAnoAnterior) }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">
                                        {{ formatarNumero(item.ProvisaoValorMesAnterior) }}
                                    </td>
                                    <td
                                        class="border-r border-slate-300 px-2 py-1 text-right font-bold text-slate-900 bg-[#FFF2CC]">
                                        {{ formatarNumero(item.ProvisaoValorMesActual) }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right font-semibold"
                                        :class="obterCorDiferenca(item.ProvisaoDiferencaMensal)">
                                        {{ formatarNumero(item.ProvisaoDiferencaMensal) }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right font-semibold"
                                        :class="obterCorDiferenca(item.ProvisaoDiferencaAnual)">
                                        {{ formatarNumero(item.ProvisaoDiferencaAnual) }}
                                    </td>
                                    <td class="border-r border-slate-300 px-2 py-1 text-right">
                                        {{ formatarNumero(item.ProvisaoDiferencaMediaMensal) }}
                                    </td>
                                    <td class="border-slate-300 px-2 py-1 text-center font-bold"
                                        :class="obterCorDiferenca(item.PercentVariaAnual)">
                                        {{ formatarPercentual(item.PercentVariaAnual) }}
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    historicoRD: { type: Array, default: () => [] },
    agenciasRD: { type: Array, default: () => [] },
    produtosRD: { type: Array, default: () => [] },
    provisoesProdutosRD: { type: Array, default: () => [] },
    desembolsosReembolsos12MesesRD: { type: Array, default: () => [] },
    user: Object
})

const areaTabelas = ref(null)

/**
 * Imprime exclusivamente a área das tabelas capturada via Ref
 */
const imprimirA4 = () => {
    if (!areaTabelas.value) return;

    // Cria um iframe invisível temporário
    const iframe = document.createElement('iframe');
    iframe.style.position = 'absolute';
    iframe.style.width = '0px';
    iframe.style.height = '0px';
    iframe.style.border = 'none';
    document.body.appendChild(iframe);

    const doc = iframe.contentWindow.document;

    // Copia todas as folhas de estilo da página original
    const estilos = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
        .map(style => style.outerHTML)
        .join('');

    doc.open();
    doc.write(`
        <!DOCTYPE html>
        <html>
            <head>
                <title>RD Express 2026-08-21 [KIXICREDITO ANGOLA]</title>
                ${estilos}
                <style>
                    @page {
                        size: A4 landscape;
                        margin: 8mm;
                    }
                    body {
                        background-color: white !important;
                        padding: 0 !important;
                        margin: 0 !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                    table {
                        font-size: 10px !important;
                    }
                    /* Garante a visibilidade do título exclusivo de impressão */
                    .print\\:block {
                        display: block !important;
                    }
                </style>
            </head>
            <body>
                ${areaTabelas.value.outerHTML}
            </body>
        </html>
    `);
    doc.close();

    // Dispara a impressão e remove o iframe após o término
    setTimeout(() => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        document.body.removeChild(iframe);
    }, 500);
};

const obterNivelRisco = (diasAtraso) => {
    if (diasAtraso === null || diasAtraso === undefined) return 'A';
    const da = Number(diasAtraso);

    if (da <= 7) return 'A';
    if (da <= 15) return 'B';
    if (da <= 30) return 'C';
    if (da <= 45) return 'D';
    if (da <= 75) return 'E';
    if (da <= 90) return 'F';
    return 'G';
};

const formatarProvisaoCobertura = (diasAtraso, percentual) => {
    if (percentual === null || percentual === undefined) return '-';

    const nivel = obterNivelRisco(diasAtraso);
    const percFormatado = formatarNumero(percentual);

    return `(${nivel} :: ${percFormatado}%)`;
};

const provisoesAgrupadas = computed(() => {
    if (!props.provisoesProdutosRD || props.provisoesProdutosRD.length === 0) return [];
    const grupos = {};
    props.provisoesProdutosRD.forEach(item => {
        const tipo = item.TipoAgrupamento || 'OUTROS';
        if (!grupos[tipo]) {
            grupos[tipo] = [];
        }
        grupos[tipo].push(item);
    });
    return Object.keys(grupos).map(tipo => ({
        tipo,
        itens: grupos[tipo]
    }));
});

const produtosAgrupados = computed(() => {
    if (!props.produtosRD || props.produtosRD.length === 0) return [];
    const grupos = {};
    props.produtosRD.forEach(item => {
        const tipo = item.TipoAgrupamento || 'OUTROS';
        if (!grupos[tipo]) {
            grupos[tipo] = [];
        }
        grupos[tipo].push(item);
    });
    return Object.keys(grupos).map(tipo => ({
        tipo,
        itens: grupos[tipo]
    }));
});

const agenciasCapital = computed(() => {
    if (!props.agenciasRD) return [];
    return props.agenciasRD.filter(item => item.TipoAgrupamento === 'CAPITAL');
});

const agenciasProvincia = computed(() => {
    if (!props.agenciasRD) return [];
    return props.agenciasRD.filter(item => item.TipoAgrupamento === 'PROVINCIA');
});

const historicoSeisMeses = computed(() => {
    if (!props.historicoRD || props.historicoRD.length === 0) return [];
    return props.historicoRD.slice(0, 6);
});

const ultimoRegistro = computed(() => {
    if (!props.historicoRD || props.historicoRD.length === 0) return null;
    return props.historicoRD.length >= 7
        ? props.historicoRD[6]
        : props.historicoRD[props.historicoRD.length - 1];
});

const colunasMeses = computed(() => {
    return historicoSeisMeses.value.map(item => {
        const data = new Date(item.DataReferencia);
        const mesExtenso = data.toLocaleDateString('pt-PT', { month: 'short' });
        const anoCurto = data.getFullYear().toString().substring(2);
        const labelFormatted = mesExtenso.charAt(0).toUpperCase() + mesExtenso.slice(1, 3) + '/' + anoCurto;

        return {
            key: item.Id,
            label: labelFormatted
        };
    });
});

const ultimoMesNome = computed(() => {
    if (!ultimoRegistro.value) return '';
    const d = new Date(ultimoRegistro.value.DataReferencia);
    const dia = String(d.getDate()).padStart(2, '0');
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    return `${dia}/${mes}/${d.getFullYear()}`;
});

const totalDesembolso12Meses = computed(() => {
    if (!props.desembolsosReembolsos12MesesRD) return 0;
    return props.desembolsosReembolsos12MesesRD.reduce((acc, item) => acc + Number(item.DesembolsoValor || 0), 0);
});

const totalReembolso12Meses = computed(() => {
    if (!props.desembolsosReembolsos12MesesRD) return 0;
    return props.desembolsosReembolsos12MesesRD.reduce((acc, item) => acc + Number(item.ReembolsoValor || 0), 0);
});

const formatarNumero = (val) => {
    if (val === null || val === undefined || val === '') return '-';
    let num = val;
    if (typeof num === 'string') {
        if (num.includes(',')) {
            num = num.replace(/\./g, '').replace(',', '.');
        } else {
            num = num.trim();
        }
    }
    num = Number(num);
    return isNaN(num)
        ? '-'
        : num.toLocaleString('pt-PT', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
};

const formatarInt = (val) => {
    if (val === null || val === undefined) return '-';
    const num = Number(val);
    return isNaN(num) ? '-' : num.toLocaleString('pt-PT');
};

const formatarK = (val) => {
    if (val === null || val === undefined) return '-';
    const num = Number(val);
    if (isNaN(num)) return '-';
    if (Math.abs(num) >= 1000000) {
        return (num / 1000000).toLocaleString('pt-PT', { minimumFractionDigits: 1, maximumFractionDigits: 3 }) + 'M';
    } else if (Math.abs(num) >= 1000) {
        return (num / 1000).toLocaleString('pt-PT', { minimumFractionDigits: 1, maximumFractionDigits: 3 }) + 'K';
    }
    return num.toLocaleString('pt-PT');
};

const formatarPercentual = (val) => {
    if (val === null || val === undefined) return '-';
    const num = Number(val);
    return isNaN(num) ? '-' : Math.round(num) + '%';
};

const obterCorGestao = (valor) => {
    if (valor === null || valor === undefined) return '';
    const num = Number(valor);
    if (num > 0) return 'bg-[#E2EFDA] text-[#375623]';
    if (num < 0) return 'bg-[#FCE4D6] text-[#C00000]';
    return 'bg-[#FFF2CC] text-[#806000]';
};

const obterCorDiferenca = (val) => {
    if (val === null || val === undefined) return '';
    const num = Number(val);
    if (num < 0) return 'bg-[#E2EFDA] text-[#375623]';
    if (num > 0) return 'bg-[#FCE4D6] text-[#C00000]';
    return '';
};
</script>

<style scoped>
.btn {
    @apply px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center justify-center;
}

.btn-outline-success {
    @apply border border-green-500 text-green-500 hover:bg-green-50;
}

.btn-outline-primary {
    @apply border border-blue-500 text-blue-600 hover:bg-blue-50;
}

.btn-sm {
    @apply px-3 py-1 text-sm;
}
</style>
