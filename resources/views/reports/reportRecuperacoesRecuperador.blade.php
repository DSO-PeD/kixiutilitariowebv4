<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Pagamentos - Recuperadores</title>
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

        /* Estilo para numeração de páginas */
        .page-number {
            position: fixed;
            bottom: 10px;
            right: 10px;
            font-size: 9px;
            color: #666666;
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
                                <b>FOLHA DE PAGAMENTO DE RECUPERADORES</b>
                            </td>
                        </tr>
                        <tr style="border-top: 0.25px Solid #666666;">
                            <td style="color:#006666;text-align:center;font-size:11px;">
                              Periodo de Recuperação: {{ $data_inicio }} à {{ $data_fim }}
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
                <td style="text-align:center;color:#000000;">Página <span class="current-page">1</span> de <span class="total-pages">1</span></td>
            </tr>
        </table>
    </div>
    <hr/>
<br/>
    @foreach ($recuperadores as $dd_r)

  <div class="recuperador-header py-4">

    <table>
        <tr>
            <td>
                <div class="recuperador-foto">
                        @if ($dd_r->Imagen)
                            <img src='imagens/imgsrecuperadores/{{$dd_r->Imagen}}' alt="Sem foto" class="foto-perfil" />
                        @else
                            <img src='imagens/imgsrecuperadores/avatar.jpg' alt="Foto padrão" class="foto-perfil" />
                        @endif
                    </div>
            </td>
            <td><div class="recuperador-nome">{{$dd_r->nome_recuperador}}</div>
              <div style="font-size:11px; color:#666; margin-top:3px;">
                    Localidade:
                    {{$dd_r->localidade}}
                </div>
            </td>
            <td style="background-color:  width:190%; padding-left: 10%;padding-top: 7px">
              <div class="dados-bancarios bg-slate-300" style="float: left; margin-left: 50px;">
                <img src="imagens/imgsbancos/{{$dd_r->banco}}.jpg" alt="Banco {{$dd_r->banco}}" style="float: left;" >
                <span class="conta " style="padding-left: 30px;position: fixed;">Conta: {{$dd_r->numero_conta}}</span>
                <br/>
                <span class="iban " style="padding-left: 30px; position: fixed;">IBAN:<br/> {{$dd_r->iban}}</span>
            </div>
            </td>
        </tr>


    </table>


</div>

<style type="text/css">
    .recuperador-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }

    .foto-perfil {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e0e0e0;
    }

    .recuperador-info {
        flex: 1;
    }

    .recuperador-nome {
        font-size: 13px;
        font-weight: bold;
        color: #005B3B;
        margin-bottom: 5px;
    }

    .recuperador-banco {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo-banco {
        width: 25px;
        height: 25px;
        object-fit: contain;
    }

    .dados-bancarios {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        color: #555;
    }

    .separador {
        color: #999;
    }

    .conta, .iban {
        font-family: 'Courier New', monospace;
    }
</style>
@php
    $totalRecuperado = 0;
    $totalComissao = 0;
    $totalIrt = 0;
    $totalReceber = 0;
@endphp

<!-- Primeiro calculamos todos os totais -->
@foreach ($Dados_Recuperador as $dd_recu)
    @if ($dd_recu->id_recuperador == $dd_r->id)
        @php
            $totalRecuperado += $dd_recu->ReBuMontante;
            $totalComissao += $dd_recu->comissao_bruta;
            $totalIrt += $dd_recu->desconto_IRT;
            $totalReceber += $dd_recu->valor_a_receber;
        @endphp
    @endif
@endforeach

<!-- Agora atribuímos o valor final -->
@php
    $totalReceber2 = $totalReceber;
@endphp


    <div class="section-note">
        <img src="imagens/back-128.png" class="small-icon">Dados da Recuperação<span style="float: right; font-size: 11px; color: #005B3B;">Total a Receber: {{ number_format($totalReceber2, 2, ',', '.') }}</span>
    </div>


    <table class="sub-table">
        <thead>
            <tr>
                <th>Lnr</th>
                 <th>Cliente</th>
                  <th>Voucher</th>
                <th>Valor Recuperado</th>

                <th style="background-color: #dde7f5; color: #3d526d;">% PGT.</th>
                <th style="background-color: #ebeff4; color: #3d526d;">Comissão Bruta</th>
                <th style="background-color: #ebeff4; color: #3d526d;">Desc. IRT(6.5%)</th>
                <th style="background-color: #ebeff4; color: #3d526d;">Valor a Receber</th>
                <th style="background-color: #ebeff4; color: #3d526d;">Situação</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($Dados_Recuperador as $dd_recu)
                @if ($dd_recu->id_recuperador == $dd_r->id)

                    <tr style="text-align: center; background-color: #f8f9fa;" >
                        <td>{{$dd_recu->ReBuDadoOrigem}}</td>
                        <td>{{$dd_recu->infoadicional}}</td>
                           <td>{{$dd_recu->voucher}}</td>
                        <td>{{ number_format($dd_recu->ReBuMontante, 2, ',', '.') }}</td>

                        <td style="background-color: #dde7f5; color: #3d526d">{{$dd_recu->taxa_comissao_percent}} %</td>
                        <td style="background-color: #dde7f5; color: #3d526d">{{ number_format($dd_recu->comissao_bruta, 2, ',', '.') }}</td>
                        <td style="background-color: #dde7f5; color: #3d526d">{{ number_format($dd_recu->desconto_IRT, 2, ',', '.') }}</td>
                        <td style="background-color: #dde7f5; color: #3d526d">{{ number_format($dd_recu->valor_a_receber, 2, ',', '.') }}
                        <td style="background-color: #dde7f5; color: #3d526d">{{$dd_recu->estado}} </td>

                        </td>
                    </tr>

                             <!-- Linha de totais -->
            <tr style="text-align: center; background-color: #dddddd; color: #000000; " >
                <td style="border-left: none;" colspan="3">

                    <strong>TOTAL</strong>
                <img src="imagens/icons8-right-arrow-48.png" alt="" style="width:16px;height:16px;float:right; " />
                </td>

                <td class="numeric-cell"><strong>{{ number_format($totalRecuperado, 2, ',', '.') }}</strong></td>
                 <td></td>

                <td class="numeric-cell"><strong>{{ number_format($totalComissao, 2, ',', '.') }}</strong></td>
                <td class="numeric-cell"><strong>{{ number_format($totalIrt, 2, ',', '.') }}</strong></td>
                <td class="numeric-cell"><strong>{{ number_format($totalReceber, 2, ',', '.') }}</strong></td>
                <td></td>
            </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <hr/>
    @endforeach

    <!-- Rodapé
    <table class="data-table">
        <tr class="total-row">
            <td>VALOR TOTAL</td>
            <td></td>
            <td class="text-right"><php echo number_format(0, 2, ',', '.'); ?></td>
        </tr>
    </table>
-->
    <div class="signature-area">
        <div>
            <span class="signature-label">Verificado por:</span>
            <span class="signature-line"></span>
        </div>
        <div>
            <span class="signature-label">Aprovado:</span>
            <span class="signature-line"></span>
        </div>
    </div>

    <!-- Número da página no rodapé -->
    <div class="page-number"></div>

    <script>
        // Script para numerar páginas dinamicamente
        document.addEventListener('DOMContentLoaded', function() {
            // Atualiza o número total de páginas
            const totalPages = document.querySelectorAll('.page').length || 1;
            document.querySelector('.total-pages').textContent = totalPages;

            // Atualiza o número da página atual
            document.querySelector('.current-page').textContent = '1';

            // Adiciona o número da página no rodapé
            const pageNumbers = document.querySelectorAll('.page-number');
            pageNumbers.forEach(el => {
                el.textContent = `Página 1 de ${totalPages}`;
            });
        });
    </script>
</body>
</html>
