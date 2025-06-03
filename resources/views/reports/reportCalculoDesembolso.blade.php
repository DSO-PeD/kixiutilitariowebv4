<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReportCalculoDesembolso</title>
    <style type="text/css">
        body {
            font-family: 'Calibri', Arial, sans-serif;
            color: #333333;
            font-size: 10px;
            line-height: 1.4;
            padding: 10px;
        }
        .client-info td, .credito-referencia td {
    vertical-align: middle;
}

        /* Estilo original do cabeçalho mantido */
        table.comBordaSimples2 {
            border-collapse: collapse;
            width: 100%;
            font-size: 8px;
            font-family: 'Calibri';
        }

        .rounded2 {
            border-radius: 10px;
            overflow: hidden;
            border: 1px Solid #666666;
            font-family: 'Calibri';
        }

        table.comBordaSimples3 {
            border-collapse: collapse;
            font-size: 8px;
            font-family: 'Calibri';
        }

        table.comBordaSimples3 th {
            background: #F0FFF0;
        }

        /* Barra de informações superior */
        .rounded {
            border-radius: 1px;
            overflow: hidden;
            border: 1px solid #666666;
            margin-top: 2px;
            font-family: 'Calibri';
            height: 20px
        }

        /* Estilos melhorados para o conteúdo */
        .textodetalhecliente {
            font-family: 'Calibri', sans-serif;
            color: #000000;
            font-size: 10px;
        }

        .info-container {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }

        .client-info {
            width: 30%;
            border: 1px solid #666666;
            border-radius: 10px;
            padding: 5px;
        }

        .credito-referencia {
            width: 32%;
            border: 1px solid #666666;
            border-radius: 10px;
            padding: 5px;
        }

        .client-info tr:nth-child(even) {
            background-color: #F0FFFF;
        }

        .client-info td {
            padding: 3px;
        }

        .dashed-border {
            border-left: 1px dashed #666666;
        }

        .reference-row {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .reference-icon {
            width: 20px;
            height: 20px;
            margin-right: 5px;
        }

        .reference-text {
            font-weight: bold;
        }

        /* Tabelas de conteúdo */
        .data-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }

        .data-table thead {
            color: #025336;
            border-bottom: 1.5px Solid #000000;
            font-weight: bold;
        }

        .data-table tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .data-table th, .data-table td {
            padding: 5px;
            text-align: left;
        }

        .data-table .text-right {
            text-align: right;
        }

        .data-table .text-center {
            text-align: center;
        }

        /* Seções de taxas */
        .section-title {
            color: #003366;
            font-weight: bold;
            margin: 15px 0 5px 0;
            display: flex;
            align-items: center;
        }

        .section-title img {
            margin-right: 5px;
        }

        .section-note {
            color: #056d83;
            font-weight: bold;
            margin-left: 25px;
            display: flex;
            align-items: center;
        }

        .section-note img {
            margin-right: 5px;
        }

        .highlight-row {
            background-color: #e6e6e6 !important;
        }

        .highlight-row-green {
            background-color: #cfcfcf !important;
        }

        /* Tabelas internas */
        .sub-table {
            width: 100%;
            margin: 5px 0;
            border: 0.5px solid #bebebe;
            border-radius: 3px;
            font-size: 9px;
        }

        .sub-table th {
            color: #047777;
            font-weight: bold;
            padding: 3px;
        }

        .sub-table td {
            padding: 3px;
        }

        .sub-table tr {
            background-color: #EAF4FF;
            color: #047777;
        }

        /* Rodapé */
        .total-row {
            border-top: 1.5px solid #666666;
            font-weight: bold;
            font-size: 12px;
        }

        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-line {
            border-top: 1px solid #666666;
            width: 200px;
            display: inline-block;
            margin-top: 30px;
        }

        .signature-label {
            color: #006666;
        }

        .value-bold {
            font-weight: bold;
        }

        .icon {
            width: 16px;
            height: 16px;
            vertical-align: middle;
            margin-right: 5px;
        }

        .small-icon {
            width: 10px;
            height: 10px;
            vertical-align: middle;
            margin-right: 3px;
        }
    </style>
</head>

<body>
    <!-- Cabeçalho original mantido -->
    <table class="comBordaSimples2" style="width:100%">
        <tr>
            <td rowspan="2"> <img src="imagens/logokx.jpg" alt="Kixi Crédito" style="width:111px; height:31px" /> </td>
            <td rowspan="2" style="width: 550px;">
                <div class='rounded2'>
                    <table style="width:100%;" class="comBordaSimples3">
                        <tr>
                            <td style="color:#005B3B;text-align:center;font-size:12px;">
                                <b>FOLHA DE CÁLCULO DE DESEMBOLSO</b>
                            </td>
                        </tr>
                        <tr style="border-top: 0.25px Solid #666666;">
                            <td style="color:#006666;text-align:center;font-size:11px;text-transform: uppercase;">
                                <b>{{ $Dados_extrato[0]->Lnr }} :: {{ $Dados_extrato[0]->Cliente }}</b>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td rowspan="2" style="text-align:right">
                <img src="imagens/lgkxu.jpg" alt="Kixi Utilitário" style="width:51px; height:51px;" />
            </td>
        </tr>
        <tr></tr>
    </table>

    <!-- Barra de informações superior -->
    <div class='rounded'>
        <table class="comBordaSimples" style="width:100%;margin-top:2px;font-family: 'Calibri';">
            <tr>
                <td style="color:#000000;">&nbsp;Impresso por: <b>{{ $IMPRENSSO }}</b></td>
                <td style="color:#000000;">Data de Impressão: <b>{{ $date }}</b></td>
                <td style="color:#000000;">Agência: <b>{{ $AGENCIA }}</b></td>
                <td style="text-align:center;color:#000000;">Página 1 de 1</td>
            </tr>
        </table>
    </div>


<div style="display: flex; justify-content: space-between; margin: 20px 0; gap: 15px; align-items: stretch;">

       <table style="width:100%">
            <tr>
                <td>
                       <!-- Tabela de Informações do Cliente (esquerda) -->
    <div style="flex: 1; border: 1px solid #666666; border-radius: 10px; padding: 8px; background: white;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 5px 8px; width: 50%;">&nbsp;Produto&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;<b>{{ $Dados_extrato[0]->Produto }}</b></td>
                <td style="border-left: 1px dashed #666666; padding: 5px 8px;">&nbsp;Base da Aplicação:&nbsp;&nbsp;<b>{{ $Dados_extrato[0]->BaseOperacao }}</b></td>
            </tr>
            <tr style="background-color: #F0FFFF;">
                <td style="padding: 5px 8px;">&nbsp;Montante&nbsp;:&nbsp;&nbsp;&nbsp;<b><?php echo number_format($Dados_extrato[0]->ValorTotalCredito, 2, ',', '.'); ?></b></td>
                <td style="border-left: 1px dashed #666666; padding: 5px 8px;">&nbsp;Oficial de Crédito:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{ $Dados_extrato[0]->OficialCredito }}</b></td>
            </tr>
            <tr>
                <td style="padding: 5px 8px;">&nbsp;Aplicação&nbsp;:&nbsp;<b>
                    <?php
                    $date = date_create($Dados_extrato[0]->CiFecha);
                    echo date_format($date, 'd/m/Y H:i:s');
                    ?>
                </b></td>
                <td style="border-left: 1px dashed #666666; padding: 5px 8px;">&nbsp;Aplicado por&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{ $Dados_extrato[0]->UtCodigo }}</b></td>
            </tr>
        </table>


    </div>

                </td>
                <td>

        <!-- Tabela de Referência de Pagamento (direita) -->
    <div style="float: right;width: 30%; min-width: 200px; border: 1px solid #666666; border-radius: 10px; padding: 8px; background: white; display: flex; flex-direction: column; justify-content: space-between;"">

        <div style="text-align: center; margin: 10px 0; flex-grow: 1; display: flex; align-items: center; justify-content: center;">
            <b style="font-size: 18px; word-break: break-all; letter-spacing: 1px;">{{ $Dados_extrato[0]->referenciapagamento }}</b>
        </div>
        <div style="text-align: center;">
            <img src="imagens/mltc_expres.jpg" style="width: 30px; height: 30px;">
            <img src="imagens/atm.jpg" style="width: 30px; height: 30px;">
            <img src="imagens/internetbanking.jpg" style="width: 30px; height: 30px;">
        </div>
    </div>

                </td>
            </tr>

    </table>


</div>




    <!-- Restante do conteúdo permanece igual -->
    <!-- Tabela de valores -->
    <table class="data-table">
        <thead>
            <tr>
                <th>DESCRIÇÃO</th>
                <th class="text-center">PERCENTAGEM(%)</th>
                <th class="text-right">VALOR KZ</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Valor do Crédito no Contrato:</td>
                <td></td>
                <td class="text-right"><?php echo number_format($Dados_extrato[0]->ValorCreditoNoContrato, 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Valor Colateral Depositado/Activo [S08]:</td>
                <td class="text-center">{{ $Dados_extrato[0]->PercColateral }}%</td>
                <td class="text-right"><?php echo number_format($Dados_extrato[0]->ValorDoColateral, 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Valor Colateral Deduzido/Passivo [S06]:</td>
                <td class="text-center">{{ $Dados_extrato[0]->PercColateralDeduzido }}%</td>
                <td class="text-right"><?php echo number_format($Dados_extrato[0]->ValorDoColateralDeduzido, 2, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Seção Taxa de Imprevisto -->
    <div class="section-title">
        <img src="imagens/icons8-discount-80.png" class="icon">TAXA DE IMPREVISTO (Tipo de Pagamento:
        <span class="value-bold">
            @if ($Dados_extrato[0]->TaxaImprevisto <> 'Antecipado')
                Pós-Antecipado
            @else
                {{ $Dados_extrato[0]->TaxaImprevisto }}
            @endif
        </span>)
    </div>

    @if ($Dados_extrato[0]->TaxaImprevisto == 'Antecipado')
        <div class="section-note">
            <img src="imagens/icons8-right-arrow-48.png" class="icon">A taxa não foi deduzida no valor do crédito no contrato.
        </div>
    @else
        <div class="section-note">
            <img src="imagens/icons8-right-arrow-48.png" class="icon">A taxa foi deduzida no valor do crédito no contrato.
        </div>
    @endif

    <table class="data-table">
        <tr class="highlight-row">
            <td>Taxa: <span class="value-bold">{{ $Dados_extrato[0]->TXAImprePercenta }}%</span></td>
            <td>Valor KZ: <span class="value-bold"><?php echo number_format($Dados_extrato[0]->TXAImprePercentaValor, 2, ',', '.'); ?></span></td>
            <td>14% do IVA: <span class="value-bold"><?php echo number_format($Dados_extrato[0]->ValorIVATaxaImprevisto, 2, ',', '.'); ?></span></td>
            <td class="text-right">Valor Pago: <span class="value-bold">
                <?php
                $TotalPagoTI = $Dados_extrato[0]->ValorIVATaxaImprevisto + $Dados_extrato[0]->TXAImprePercentaValor;
                echo number_format($TotalPagoTI, 2, ',', '.');
                ?>
            </span></td>
        </tr>
    </table>

    @if ($Dados_extrato[0]->TaxaImprevisto == 'Antecipado')
        <div class="section-note">
            <img src="imagens/back-128.png" class="small-icon">Dados do Comprovativo
        </div>

        <table class="sub-table">
            <thead>
                <tr>
                    <th>Banco</th>
                    <th>Nº de Conta</th>
                    <th>Voucher</th>
                    <th>Montante</th>
                    <th>Data de Depósito</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @foreach ($bancos as $banco)
                            @if ($banco->BaCodigo == $Dados_extrato[0]->TXAImpreBuBanco && $Dados_extrato[0]->TXAImpreBuMontante)
                                {{ $banco->BaNome }}
                            @endif
                        @endforeach
                    </td>
                    <td>{{ $Dados_extrato[0]->TXAImpreBuNumeroConta }}</td>
                    <td>{{ $Dados_extrato[0]->TXAImpreAnteBuReferencia }}</td>
                    <td>
                        @if ($Dados_extrato[0]->TXAImpreBuMontante > 0)
                            <?php echo number_format($Dados_extrato[0]->TXAImpreBuMontante, 2, ',', '.'); ?>
                        @endif
                    </td>
                    <td>{{ $Dados_extrato[0]->TXAImpreBuData }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- Seção Taxa de Processamento -->
    <div class="section-title">
        <img src="imagens/icons8-discount-80.png" class="icon">TAXA DE PROCESSAMENTO (Tipo de Pagamento:
        <span class="value-bold">
            @if ($Dados_extrato[0]->TaxaProcessamento <> 'Antecipado')
                Pós-Antecipado
            @else
                {{ $Dados_extrato[0]->TaxaProcessamento }}
            @endif
        </span>)
    </div>

    @if ($Dados_extrato[0]->TaxaProcessamento <> 'Antecipado')
        <div class="section-note">
            <img src="imagens/icons8-right-arrow-48.png" class="icon">A Taxa foi deduzida no valor do crédito no contrato.
        </div>
    @endif

    <table class="data-table">
        <tr class="highlight-row-green">
            <td>Taxa: <span class="value-bold">{{ $Dados_extrato[0]->TXAProcePercenta }}%</span></td>
            <td>Valor KZ: <span class="value-bold"><?php echo number_format($Dados_extrato[0]->TXAProcePercentaValor, 2, ',', '.'); ?></span></td>
            <td>14% do IVA: <span class="value-bold"><?php echo number_format($Dados_extrato[0]->ValorIVATaxaProcessamento, 2, ',', '.'); ?></span></td>
            <td class="text-right">Valor Pago: <span class="value-bold">
                <?php
                $TotalPagoTP = $Dados_extrato[0]->ValorIVATaxaProcessamento + $Dados_extrato[0]->TXAProcePercentaValor;
                echo number_format($TotalPagoTP, 2, ',', '.');
                ?>
            </span></td>
        </tr>
    </table>

    @if ($Dados_extrato[0]->TaxaProcessamento == 'Antecipado' || $Dados_extrato[0]->TaxaProcessamentoAnte == 'Antecipado')
        <?php
        if ($Dados_extrato[0]->TaxaProcessamentoAnte == "" || $Dados_extrato[0]->TaxaProcessamentoAnte == "Nenhum") {
            $DescricaoTaxa = $Dados_extrato[0]->TaxaProcessamento;
            $PercenteAnte = $Dados_extrato[0]->TXAProcePercenta;
            $PercenteValorAnte = $Dados_extrato[0]->TXAProcePercentaValor;
            $ValorIVAAnte = $Dados_extrato[0]->ValorIVATaxaProcessamento;
            $TotalPagoTP = $ValorIVAAnte + $PercenteValorAnte;
        } else {
            $DescricaoTaxa = $Dados_extrato[0]->TaxaProcessamentoAnte;
            $PercenteAnte = $Dados_extrato[0]->TXAProcePercentaAnte;
            $PercenteValorAnte = $Dados_extrato[0]->TXAProcePercentaValorAnte;
            $ValorIVAAnte = $Dados_extrato[0]->ValorIVATaxaProcessamentoAnte;
            $TotalPagoTP = $ValorIVAAnte + $PercenteValorAnte;
        }
        ?>

        <div class="section-title">
            <img src="imagens/icons8-discount-80.png" class="icon">TAXA DE PROCESSAMENTO (Tipo de Pagamento:
            <span class="value-bold">{{ $DescricaoTaxa }}</span>)
        </div>

        <div class="section-note">
            <img src="imagens/icons8-right-arrow-48.png" class="icon">A Taxa não foi deduzida no valor do crédito no contrato.
        </div>

        <table class="data-table">
            <tr class="highlight-row-green">
                <td>Taxa: <span class="value-bold"><?php echo number_format($PercenteAnte, 2, ',', '.') . "%"; ?></span></td>
                <td>Valor KZ: <span class="value-bold"><?php echo number_format($PercenteValorAnte, 2, ',', '.'); ?></span></td>
                <td>14% do IVA: <span class="value-bold"><?php echo number_format($ValorIVAAnte, 2, ',', '.'); ?></span></td>
                <td class="text-right">Valor Pago: <span class="value-bold"><?php echo number_format($TotalPagoTP, 2, ',', '.'); ?></span></td>
            </tr>
        </table>

        <div class="section-note">
            <img src="imagens/back-128.png" class="small-icon">Dados do Comprovativo
        </div>

        <table class="sub-table">
            <thead>
                <tr>
                    <th>Banco</th>
                    <th>Nº de Conta</th>
                    <th>Voucher</th>
                    <th>Montante</th>
                    <th>Data de Depósito</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @foreach ($bancos as $banco)
                            @if ($banco->BaCodigo == $Dados_extrato[0]->TXAProceAnteBuBanco && $Dados_extrato[0]->TXAProceAnteBuMontante > 0)
                                {{ $banco->BaNome }}
                            @endif
                        @endforeach
                    </td>
                    <td>{{ $Dados_extrato[0]->TXAProceAnteBuNumeroConta }}</td>
                    <td>{{ $Dados_extrato[0]->TXAProceAnteBuReferencia }}</td>
                    <td>
                        @if ($Dados_extrato[0]->TXAProceAnteBuMontante > 0)
                            <?php echo number_format($Dados_extrato[0]->TXAProceAnteBuMontante, 2, ',', '.'); ?>
                        @endif
                    </td>
                    <td>{{ $Dados_extrato[0]->TXAProceAnteBuData }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="section-title">
            <img src="imagens/icons8-discount-80.png" class="icon">TAXA DE PROCESSAMENTO (Tipo de Pagamento:
            <span class="value-bold">Antecipado</span>)
        </div>

        <div class="section-note" style="color: red;">
            <img src="imagens/icons8-right-arrow-48.png" class="icon">Este crédito não foi cobrado Taxa de Processamento Antecipado.
        </div>
    @endif

    <!-- Seção Taxa de Confirmação -->
    <div class="section-title">
        <img src="imagens/icons8-discount-80.png" class="icon">TAXA DE CONFIRMAÇÃO
    </div>

    <table class="data-table">
        <tr>
            <td>Taxa: <span class="value-bold">0%</span></td>
            <td>Valor KZ: <span class="value-bold"><?php echo number_format($Dados_extrato[0]->ValorAKZTaxaDeConfirmacao, 2, ',', '.'); ?></span></td>
            <td>14% do IVA: <span class="value-bold"><?php echo number_format($Dados_extrato[0]->ValorIVATaxaConfirmacao, 2, ',', '.'); ?></span></td>
            <td class="text-right">Valor Pago: <span class="value-bold">
                <?php
                $TotalPagoTC = $Dados_extrato[0]->ValorIVATaxaConfirmacao + $Dados_extrato[0]->ValorAKZTaxaDeConfirmacao;
                echo number_format($TotalPagoTC, 2, ',', '.');
                ?>
            </span></td>
        </tr>
    </table>

    <!-- Rodapé -->
    <table class="data-table">
        <tr class="total-row">
            <td>VALOR A RECEBER:</td>
            <td></td>
            <td class="text-right"><?php echo number_format($Dados_extrato[0]->ValorTotalCredito, 2, ',', '.'); ?></td>
        </tr>
    </table>

    <div class="signature-area">
        <div>
            <span class="signature-label">Elaborou:</span>
            <span class="signature-line"></span>
        </div>
        <div>
            <span class="signature-label">Verificou:</span>
            <span class="signature-line"></span>
        </div>
    </div>
</body>
</html>
