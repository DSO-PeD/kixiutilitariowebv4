<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento por Referência</title>
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
            border-radius: 2px;
            overflow: hidden;
            border: 1px solid #666666;

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


         .page-container {
      max-width: 1000px;
      margin: 0 auto;
      background: #fff;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.12);
      position: relative;
    }


 /* ===== Box ===== */
    .box {
      border: 1.8px solid #8c4cbf;
      border-radius: 10px;
      background: #ffffff;
      padding: 15px;
      margin-top: 28px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    .box-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #e5dbf2;
      margin-bottom: 10px;
      padding-bottom: 5px;
    }

    .box-header-left img {
      height: 24px;
      float: right;
    }

    .agency {
      background: #efe8f5;
      color: #6b2c91;
      padding: 4px 10px;
      border-radius: 5px;
      font-weight: bold;
      font-size: 20px;
      text-transform: uppercase;
      text-align: center;
      margin:8px;

    }

    .header-right span {
      background: #6b2c91;
      color: #fff;
      padding: 4px 10px;
      border-radius: 4px;
      font-size: 14px;
      font-weight: bold;
      box-shadow: 0 1px 2px rgba(0,0,0,0.15);
      margin-left: 8px
    }
.header-rightpro span {
      background: #eaf9e2;
      color: #005b3b;
      padding: 4px 10px;
      border-radius: 5px;
      font-size: 12x;
      font-weight: bold;
      box-shadow: 0 1px 2px rgba(0,0,0,0.15);
      margin-left: 8px
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11px;
      margin-top: 6px;
    }

    td {
      padding: 5px 4px;
      vertical-align: top;
    }

    strong {
      color: #222;
      font-weight: 600;
    }

    .highlight {
      background: #fff5a6;
      font-weight: bold;
      padding: 2px 5px;
      border-radius: 3px;
    }

    .footer {
      margin-top: 12px;
      border-top: 1px dashed #bbb;
      font-size: 9px;
      color: #555;
      padding-top: 4px;
      text-align: left;
      font-style: italic;
    }

    /* ===== Linha de corte com tesoura ===== */
    .cut-line-container {
      position: relative;
      text-align: center;
      margin: 30px 0;
    }

    .cut-line {
      border-top: 1px dashed #666;
      position: relative;
      height: 1px;
    }

    .scissors {
      position: absolute;
      top: -11px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 13px;
      background: #fff;
      padding: 0 10px;
      color: #6b2c91;
      font-weight: bold;
      font-family: 'Segoe UI Symbol', 'Arial';
    }
    .payment-icons{
        margin-left: 6px
    }
.payment-icons img {
            width: 20px;
            height: 20px;
            float: left;
            align-self: auto;
            margin: 2px

        }
    /* ===== Impressão ===== */
    @media print {
      body {
        background: #fff;
        padding: 0;
      }

      .page-container {
        box-shadow: none;
        border-radius: 0;
        padding: 10px;
      }

      .cut-line-container {
        margin: 20px 0;
      }
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
                            <td style="color:#dc4e00;text-align:center;font-size:14px; padding: 10px; text-transform: uppercase;">
                                <b>Card´s Digital de Referência de Pagamento</b>
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






    <br/> <br/> <br/> <br/>

   <div class="page-container">

    <!-- BLOCO 1 -->
    <div class="box">
      <div class="box-header">
        <div class="box-header-left">
          <img src="imagens/logokx.jpg" alt="Kixi Crédito">
        </div>
        <br/>
         <div class="payment-icons">
                <img src="imagens/mltc_expres.jpg" alt="Multicaixa Express">
                <img src="imagens/atm.jpg" alt="ATM">
                <img src="imagens/internetbanking.jpg" alt="Internet Banking">
        </div>
        <div class="header-right"><span>Nº da Referência de Pagamento</span></div>
        <div class="agency">{{$Dados_comprovativo[0]->referencia}}</div>
       <div class="header-rightpro"><span>{{$produto}}</span></div>
      </div>

      <table style="font-size: 12px">
        <tr><td><strong>Montante:</strong> <span class="highlight">AKZ <?php echo number_format($Dados_comprovativo[0]->montante, 2, ',', '.'); ?></span></td><td><strong>Código do Cliente:</strong> {{$Dados_comprovativo[0]->BuDadoOrigem}}</td></tr>
        <tr><td><strong>Validade:</strong> <span class="highlight"><?php echo date("d/m/Y", strtotime($Dados_comprovativo[0]->fim)); ?> </span></td><td style="background: #f0f0f0"><strong>Nome:</strong> {{$Dados_comprovativo[0]->nomecliente}}</td></tr>

        <tr><td><strong>Agência:</strong> {{$agencia}}</td><td><strong>Telefone:</strong> {{$Dados_comprovativo[0]->telefone}}</td></tr>

      </table>

      <div class="footer">
        Operador: {{ $IMPRENSSO }} | Impressão: [{{ $date }}] | <b style="float: right">Entidade Nº 00589 — Kixi Crédito, a sua parceira nos negócios.</b>
      </div>
    </div>

    <!-- Linha de corte -->
    <div class="cut-line-container">
      <div class="cut-line"></div>
      <div class="scissors">  <img src="imagens/05-48.png"   style="width:15px; height:15px;" /> Corte aqui </div>
    </div>

    <!-- BLOCO 2 -->
    <div class="box">
      <div class="box-header">
        <div class="box-header-left">
          <img src="imagens/logokx.jpg" alt="Kixi Crédito">
        </div>
        <br/>
        <div class="payment-icons">
                <img src="imagens/mltc_expres.jpg" alt="Multicaixa Express">
                <img src="imagens/atm.jpg" alt="ATM">
                <img src="imagens/internetbanking.jpg" alt="Internet Banking">
        </div>
        <div class="header-right"><span>Nº da Referência de Pagamento</span>

        </div>
        <div class="agency">{{$Dados_comprovativo[0]->referencia}}</div>
        <div class="header-rightpro"><span>{{$produto}}</span></div>
      </div>

      <table style="font-size: 12px">
        <tr><td><strong>Montante:</strong> <span class="highlight">AKZ <?php echo number_format($Dados_comprovativo[0]->montante, 2, ',', '.'); ?></span></td><td><strong>Código do Cliente:</strong> {{$Dados_comprovativo[0]->BuDadoOrigem}}</td></tr>
        <tr><td><strong>Validade:</strong> <span class="highlight"><?php echo date("d/m/Y", strtotime($Dados_comprovativo[0]->fim)); ?></span></td><td style="background: #f0f0f0"><strong>Nome:</strong> {{$Dados_comprovativo[0]->nomecliente}}</td></tr>

        <tr><td><strong>Agência:</strong> {{$agencia}}</td><td><strong>Telefone:</strong> {{$Dados_comprovativo[0]->telefone}}</td></tr>

      </table>

      <div class="footer">
        Operador: {{ $IMPRENSSO }} | Impressão: [{{ $date }}] | <b style="float: right">Entidade Nº 00589 — Kixi Crédito, a sua parceira nos negócios.</b>
      </div>
    </div>
  </div>





</body>
</html>
