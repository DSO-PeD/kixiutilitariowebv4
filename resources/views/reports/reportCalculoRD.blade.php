<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Relatório Kixicrédito Angola</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
/* IMPORTANTE: Configurações específicas para DOMPDF */
body {
    font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
    background: #FFFFFF;
    margin: 0;
    padding: 0;
    font-size: 8px;
    line-height: 1.1;
    width: 100%;
}

/* Margens MUITO pequenas para DOMPDF */
@page {
    margin: 0.2cm 0.15cm; /* Margens mínimas: 2mm top/bottom, 1.5mm left/right */
    size: A4 portrait;
}

/* Container que ocupa toda a largura disponível */
.page-container {
    padding: 0.1cm;
    width: 98%; /* Um pouco menos que 100% */
    max-width: 20.5cm; /* Um pouco menor */
    margin: 0 auto;
    box-sizing: border-box;
    position: relative;
    left: 0;
    right: 0;
}

/* ===== TÍTULOS ===== */
.title {
    text-align: center;
    background: #0B3F6B;
    color: #FFFFFF;
    padding: 4px 2px;
    font-size: 11px;
    font-weight: bold;
    margin: 0 0 2px 0;
    border-radius: 1px;
}

.subtitle {
    text-align: center;
    background: #1F5D8F;
    color: #FFFFFF;
    padding: 2px;
    font-size: 9px;
    margin: 0 0 5px 0;
    border-radius: 1px;
}

/* ===== TABELAS PRINCIPAIS ===== */
.table-container {
    background: #FFFFFF;
    border: 1px solid #C9D1D9;
    margin-bottom: 5px;
    border-radius: 1px;
    width: 100%;
    overflow: hidden;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 7.5px;
    table-layout: fixed;
}

th, td {
    border: 1px solid #C9D1D9;
    padding: 1px 2px;
    text-align: right;
    line-height: 1.0;
    height: 16px;
    overflow: hidden;
    text-overflow: ellipsis;
}

th {
    background: #2E75B6;
    color: #FFFFFF;
    font-weight: bold;
    padding: 2px;
}

th.group {
    background: #163A5F;
    text-align: center;
    font-size: 7.5px;
}

/* ===== COLUNAS FIXAS ===== */
td.label, th.label {
    text-align: left;
    font-weight: bold;
    background: #EEF2F7;
    width: 100px;
    min-width: 100px;
    max-width: 100px;
}

/* ===== CORES ===== */
.green {
    background: #D9F2E6 !important;
    color: #0B6E4F !important;
    font-weight: bold;
}

.red {
    background: #F8D7DA !important;
    color: #9B1C1F !important;
    font-weight: bold;
}

.yellow {
    background: #FFF2CC !important;
    color: #7A6000 !important;
    font-weight: bold;
}

/* ===== METAS ===== */
.metas {
    margin: 5px 0 8px 0;
    width: 220px;
    font-size: 7.5px;
    float: left;
}

.metas th {
    background: #0B3F6B;
    color: #FFFFFF;
    padding: 3px;
    font-size: 8px;
}

.metas td {
    font-weight: bold;
    padding: 2px 4px;
}

/* ===== SEÇÕES ===== */
.section-title {
    margin: 8px 0 4px 0;
    background: #0B3F6B;
    color: #FFFFFF;
    padding: 3px 5px;
    font-weight: bold;
    font-size: 9px;
    clear: both;
    border-radius: 1px;
}

/* ===== TEXTO PEQUENO ===== */
.small {
    font-size: 6.5px;
    color: #555555;
    text-align: center;
    margin-top: 6px;
    clear: both;
}

/* ===== AJUSTES DE COLUNAS - DIMINUÍDAS ===== */
.col-narrow {
    width: 38px;
    min-width: 38px;
    max-width: 38px;
}

.col-medium {
    width: 50px;
    min-width: 50px;
    max-width: 50px;
}

.col-wide {
    width: 65px;
    min-width: 65px;
    max-width: 65px;
}

/* ===== CLEARFIX ===== */
.clearfix::after {
    content: "";
    clear: both;
    display: table;
}

/* Estilo para números muito grandes */
.compact-number {
    font-size: 7px;
    letter-spacing: -0.3px;
    padding: 1px 1px !important;
}

/* Abreviações para economizar espaço */
.abbr {
    font-size: 7px;
}

/* Ajuste para cabeçalho de tabela complexa */
.complex-header {
    font-size: 7px;
    padding: 1px !important;
}
</style>

</head>

<body>

<div class="page-container">

<div class="title">KIXICRÉDITO ANGOLA (100%)</div>
<div class="subtitle">PCE: JOAQUIM CATINDA</div>

<div class="table-container">
<table>
    <thead>
        <tr>
            <th class="label" rowspan="2" style="width: 45px;">2024</th>
            <th class="group" colspan="6">2025</th>
            <th class="group" colspan="2">Variação</th>
            <th class="group col-wide">30/11/2025</th>
            <th class="group col-narrow" style="width: 25px;">OM</th>
        </tr>
        <tr>
            <th class="col-narrow complex-header">Dez/24</th>
            <th class="col-narrow complex-header">Jun/25</th>
            <th class="col-narrow complex-header">Jul/25</th>
            <th class="col-narrow complex-header">Ago/25</th>
            <th class="col-narrow complex-header">Set/25</th>
            <th class="col-narrow complex-header">Out/25</th>
            <th class="green col-narrow complex-header">+</th>
            <th class="red col-narrow complex-header">-</th>
            <th class="col-medium"></th>
            <th class="col-narrow"></th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td class="label">1. Balanço</td>
            <td class="compact-number">11.280M</td>
            <td class="abbr">15.136M</td>
            <td class="abbr">16.528M</td>
            <td class="abbr">17.034M</td>
            <td class="abbr">18.041M</td>
            <td class="compact-number">18.815M</td>
            <td class="green compact-number">2.090M</td>
            <td class="red compact-number">1.899M</td>
            <td class="yellow compact-number">19.024M</td>
            <td class="green">1%</td>
        </tr>

        <tr>
            <td class="label">2. Créditos Novos</td>
            <td>28.880</td>
            <td>35.508</td>
            <td>37.207</td>
            <td>39.437</td>
            <td>41.122</td>
            <td>43.147</td>
            <td class="green">2.845</td>
            <td class="red">1.354</td>
            <td>44.520</td>
            <td class="green">3%</td>
        </tr>

        <tr>
            <td class="label">4. NPL</td>
            <td class="compact-number">792.6M</td>
            <td class="abbr">1.103M</td>
            <td class="abbr">1.157M</td>
            <td class="abbr">1.202M</td>
            <td class="abbr">1.270M</td>
            <td class="abbr">1.324M</td>
            <td class="green compact-number">136.6M</td>
            <td class="red compact-number">70.0M</td>
            <td class="compact-number">1.391M</td>
            <td class="red">0%</td>
        </tr>

        <tr>
            <td class="label">5. PAR 1</td>
            <td class="compact-number">2.040M</td>
            <td class="abbr">2.654M</td>
            <td class="abbr">3.364M</td>
            <td class="abbr">2.618M</td>
            <td class="abbr">3.420M</td>
            <td class="compact-number">4.920M</td>
            <td class="green compact-number">1.249M</td>
            <td class="red compact-number">1.532M</td>
            <td class="compact-number">4.638M</td>
            <td class="red">-2%</td>
        </tr>

        <tr>
            <td class="label">7. Provisão</td>
            <td class="red compact-number">771.0M</td>
            <td class="red compact-number">991.3M</td>
            <td class="red compact-number">1.038M</td>
            <td class="red compact-number">1.064M</td>
            <td class="red compact-number">1.101M</td>
            <td class="red compact-number">1.096M</td>
            <td class="green compact-number">140.9M</td>
            <td class="red compact-number">51.8M</td>
            <td class="compact-number">1.185M</td>
            <td class="green">8%</td>
        </tr>

        <tr>
            <td class="label">8. Taxa de Reembolso</td>
            <td>92%</td>
            <td>89%</td>
            <td>89%</td>
            <td>92%</td>
            <td>91%</td>
            <td>89%</td>
            <td class="green">5.151</td>
            <td class="red">4.030</td>
            <td>89%</td>
            <td class="red">-1%</td>
        </tr>
    </tbody>
</table>
</div>

<!-- METAS -->
<div class="clearfix">
    <table class="metas">
        <tr>
            <th colspan="2">METAS</th>
        </tr>
        <tr>
            <td class="label">Desembolso</td>
            <td class="green">2.840M</td>
        </tr>
        <tr>
            <td class="label">Reembolso</td>
            <td class="yellow">1.900M - T.R. 72%</td>
        </tr>
    </table>
</div>

<!-- DISTRIBUIÇÃO -->
<div class="section-title">1. DISTRIBUIÇÃO DA CARTEIRA - KIXICRÉDITO ANGOLA</div>

<div class="table-container">
<table>
    <thead>
        <tr>
            <th class="label" style="width: 120px;">Capital / Província</th>
            <th class="col-narrow" style="width: 35px;">OA/PA</th>
            <th class="col-medium">Balanço</th>
            <th class="col-narrow">Créditos Novos</th>
            <th class="col-narrow">Clientes Novos</th>
            <th class="col-medium">PAR 1</th>
            <th class="col-medium">PAR 30</th>
            <th class="col-medium">Desembolso TA</th>
            <th class="col-medium">Reembolso</th>
            <th class="col-medium">Provisão</th>
            <th class="col-narrow" style="width: 30px;">Gestão</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="label">CAPITAL LUANDA (36%)</td>
            <td>76/38</td>
            <td class="compact-number">6.871M</td>
            <td>15.110</td>
            <td>15.033</td>
            <td class="abbr">1.648M (24%)</td>
            <td class="abbr">990.3K (9%)</td>
            <td class="green compact-number">721.6M</td>
            <td class="green compact-number">681.4M</td>
            <td class="red compact-number">402.7M</td>
            <td class="yellow">-21M</td>
        </tr>

        <tr>
            <td class="label">Viana (11%)</td>
            <td>55/27</td>
            <td class="compact-number">2.164M</td>
            <td>4.304</td>
            <td>4.289</td>
            <td class="abbr">439.4K (20%)</td>
            <td class="abbr">86.9K (4%)</td>
            <td class="green compact-number">375.1M</td>
            <td class="green compact-number">187.2M</td>
            <td class="red compact-number">58.3M</td>
            <td class="yellow">12B</td>
        </tr>
    </tbody>
</table>
</div>

<p class="small">Relatório gerado automaticamente – Layout corporativo</p>

</div> <!-- Fim do page-container -->

</body>
</html>
