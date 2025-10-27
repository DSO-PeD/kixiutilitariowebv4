<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReportReembolso</title>
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
            width: 65%;
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
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
           /* border: 1px solid #666666;
            border-radius: 8px;
            background-color: #F0FFFF;*/
        }

        .bank-coordinates {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .bank-logo {
            width: 40px;
            height: 40px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 3px;
            background: white;
        }

        .account-info {
            font-weight: bold;
            color: #025336;
        }

        .signature-line {
            border-top: 1px solid #666666;
            width: 200px;
            display: inline-block;
            margin-top: 5px;
        }

        .signature-label {
            color: #006666;
            font-weight: bold;
            font-size: 11px;
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

        /* Novos estilos adicionados */
        .flex-container {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            gap: 15px;
        }

        .info-box {
            border: 1px solid #666666;
            border-radius: 10px;
            padding: 10px;
            background: white;
        }

        .client-details {
            flex: 1;
        }

        .payment-reference {
            width: 30%;
            min-width: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .reference-number {
            text-align: center;
            margin: 10px 0;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            word-break: break-all;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .payment-icons {
            text-align: center;
        }

        .payment-icons img {
            width: 30px;
            height: 30px;
            margin: 0 5px;
        }

        .operation-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .operation-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .operation-table .label {
            font-weight: bold;
            width: 30%;
            background-color: #f8f8f8;
        }

        .signature-fields {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-field {
            text-align: center;
            width: 45%;
        }

        .signature-space {
            border-top: 1px solid #666;
            width: 100%;
            margin-top: 25px;
            padding-top: 3px;
            font-size: 10px;
            color: #666;
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
                            <td style="color:#dc4e00;text-align:center;font-size:12px;">
                                <b>Comprovativo Digital Interno de Reembolso</b>
                            </td>
                        </tr>
                        <tr style="border-top: 0.25px Solid #666666;">
                            <td style="color:#006666;text-align:center;font-size:11px;text-transform: uppercase;">
                                <b>{{ $Dados_comprovativo[0]->BuDadoOrigem }} :: {{ optional($Dados_extrato[0])->Cliente ?? optional($Dados_extrato[0])->nomecliente ?? '' }}</b>
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
                <td style="color:#000000;">&nbsp;Impresso pôr: <b>{{ $IMPRENSSO }}</b></td>
                <td style="color:#000000;">Data de Impressão: <b>{{ $date }}</b></td>
                <td style="color:#000000;">Agência: <b>{{ $AGENCIA }}</b></td>
                <td style="text-align:center;color:#000000;">Página 1 de 1</td>
            </tr>
        </table>
    </div>

    <!-- Container principal para informações do cliente e referência
    <div class="flex-container" style="margin-top: 40px">

        <div class="info-box client-details">
            <table style="width: 100%; border-collapse: collapse; border-color: #025336;">

                <tr>
                    <td colspan="2" style="text-align: center; font-weight: bold; padding-bottom: 10px;">
                        Informações do Cliente
                    </td>
                </tr>
                <tr>
                    <td class="label">Nome:</td>
                    <td>{$Dados_extrato[0]->Cliente}}</td>
                </tr>
                <tr>
                    <td class="label">Loan Number:</td>
                    <td>{$Dados_extrato[0]->Lnr}}</td>
                </tr>
                <tr>
                    <td class="label">Montante do Crédito:</td>
                    <td>{$Dados_extrato[0]->ValorCreditoNoContrato}}</td>
                </tr>
                <tr>
                    <td class="label">Contacto:</td>
                    <td>{$Dados_comprovativo[0]->telefonecliente}}</td>
                </tr>
            </table>
        </div-->

        <!-- Referência de Pagamento -->
        <div class="info-box payment-reference" style="margin-top: 50px">
            <div class="reference-number">{{ optional($Dados_extrato[0])->referenciapagamento ?? optional($Dados_extrato[0])->referencia ?? '' }}</div>
            <div class="payment-icons">
                <img src="imagens/mltc_expres.jpg" alt="Multicaixa Express">
                <img src="imagens/atm.jpg" alt="ATM">
                <img src="imagens/internetbanking.jpg" alt="Internet Banking">
            </div>
        </div>
    </div>

    <br/> <br/> <br/> <br/>

    <!-- Detalhes da Operação -->
    <h3>Detalhe da operação realizada através de PAGAMENTO POR REFERÊNCIA</h3>

    <table class="operation-table">
        <tr>
            <td class="label">Data e Hora</td>
            <td class="text-center">{{$Dados_comprovativo[0]->CiFecha}}</td>
        </tr>
        <tr>
            <td class="label">Montante Pago</td>
            <td class="text-center"><?php echo number_format($Dados_comprovativo[0]->BuMontante, 2, ',', '.'); ?> Kz</td>
        </tr>
        <tr>
            <td class="label">Comissão da Transação</td>
            <td class="text-center"><?php echo number_format(0, 2, ',', '.'); ?></td>
        </tr>
        <tr>
            <td class="label">Imposto da Transação</td>
            <td class="text-center"><?php echo number_format(0, 2, ',', '.'); ?></td>
        </tr>
        <tr>
            <td class="label">Referência (Voucher transação)</td>
            <td class="text-center">{{$Dados_comprovativo[0]->BuReferenciaTransacao}}</td>
        </tr>
         <tr>
            <td class="label">Referência (Voucher do dia)</td>
            <td class="text-center">{{$Dados_comprovativo[0]->BuReferencia}}</td>
        </tr>
        <tr>
            <td class="label">Estado da Transação</td>
            <td class="text-center">Operação realizada com Sucesso!</td>
        </tr>
    </table>

    <!-- Rodapé -->
    <table class="data-table">
        <tr class="total-row">
            <td>Kixi Crédito, a sua parceira nos Negócios.
                <label>Caso necessite de obter alguma informação, contacte por favor a nossa linha de apoio.</label>
            </td>
        </tr>
    </table>

    <!-- Área de Coordenadas Bancárias Melhorada-->
    <div class="signature-area">
        <!--<div class="signature-label">COORDENADAS BANCÁRIAS DE TRANSAÇÃO</div>
        <div class="bank-coordinates">
            <img src="imagens/imgsbancos/BPA.jpg" alt="BPA" class="bank-logo" />
            <div>
                <div class="account-info">Banco Milénio Atlântico</div>
                <div class="account-info">Conta Bancária: 0000000</div>
            </div>
        </div>-->
    </div>

 <div class="signature-fields">
        <div class="signature-label">COORDENADA BANCÁRIA DE TRANSAÇÃO</div>
        <div class="bank-coordinates">
            <img src="imagens/imgsbancos/BPA.jpg" alt="BPA" class="bank-logo" />
            <div>
                <div class="account-info">Banco Milénio Atlântico</div>
                <div class="account-info">Conta Bancária: {{$Dados_comprovativo[0]->BuContaBancaria}}</div>
            </div>
        </div>
    </div>
</body>
</html>
