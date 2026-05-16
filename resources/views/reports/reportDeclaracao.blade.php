<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Declaração Negativa</title>
    <style type="text/css">
        body {
            font-family: 'Calibri', Arial, sans-serif;
            color: #333333;
            font-size: 15px;
            line-height: 1.4;
            padding: 10px;
        }
    </style>
</head>
<body style="padding-left: 60px;padding-right: 60px;">
    <div>
        <img src="imagens/logokx.jpg" alt="Kixi Crédito" style="width:200px; height:60px" />       
    </div>

    @if($declaracao->BaSigla != 'GN')
        <div style="margin-top: 40px; text-align: right;">    
            <div style="display: inline-block; text-align: left;">
                <p style="margin: 0;">
                    <strong>AO</strong>
                </p>
                <p style="margin: 0;margin-top:10px">
                    <strong>{{ $declaracao->BaNome }}</strong>
                </p>
            </div>
        </div>
    @endif

    @if($declaracao->BaSigla == 'GN')
        <div>
            <p style="text-align: center;font-weight:bold;margin-top:80px">DECLARAÇÃO NEGATIVA</p>
        </div>
    @endif

    <div @if($declaracao->BaSigla == 'GN') style="margin-top: 50px" @endif>
        <p style="margin-top: 20px"><strong>Ref.</strong>DECN. {{ $declaracao->lnr }}</p>
        <p style="margin-top: 15px"><strong>Data:</strong> {{ $declaracao->data_aprovacao }}</p>
        @if($declaracao->BaSigla != 'GN')
            <p style="margin-top: 15px"><strong>Assunto:</strong> Declaração Negativa</p>
        @endif
    </div>

    @if($declaracao->BaSigla == 'GN')
        <div>
            <p style="margin-top:80px">A quem possa interessar,</p>
        </div>
    @endif

    <div style="margin-top: 0px">
        <p style="text-align: justify;line-height: 1.8;"><strong>KIXICRÉDITO (ANGOLA), S.A.,</strong> sociedade de microcrédito licenciada desde 2008, com 
        sede social na Província de Luanda, Largo de Teixeira Pascoaes, Vila Alice, Caixa Postal n.º
        3876, Contribuinte Fisccal n.º 5403096116, vem por este meio declarar que o(a)
        Senhor(a) <strong>{{ $declaracao->nome }}</strong>, titular do Bilhete de Identidade <strong>N.º {{ $declaracao->documento }}</strong> está
        isento de responsabilidades creditícias ativas com esta Instituição.</p>
        <p style="margin-top: 10px">A presente Declaração tem a validade de <strong>20 (vinte)</strong> dias.</p> 
        <p style="margin-top: 10px">Atentamente,</p> 
        <p style="margin-top: 20px">A Direção de Particulares e Parcerias</p> 
    </div>

    <!--<div style="margin-top: 20px">
        <img src="imagens/ass_andre.webp" alt="img" style="width:150px; height:60px; margin-left: 25px; mix-blend-mode: multiply" />
        <p style="margin-left: 65px;margin-top:-5px"><strong>(André José)</strong></p>
        <p style="margin-left: 25px;margin-top:-18px"><span>Chefe do Departamento de</span> <br> <span style="margin-left:30px">Análise de Crédito</span></p>
    </div>-->
    
    <div style="margin-top: 50px; text-align: center;">
        <img 
            src="imagens/ass_filipe.webp" 
            alt="img" 
            style="width:150px; height:60px;"
        />

        <p style="margin-top:5px">
            <strong>(Filipe Binza)</strong>
        </p>

        <p style="margin-top:-15px">
            <span>Chefe do Departamento de</span><br>
            <span>Recuperação de Crédito</span>
        </p>
    </div>

    <div style="position: fixed; bottom: 80px; right: 10px;">
        <img src="imagens/rodape.webp" style="width:180px; height:60px" />
    </div>
    <div style="position: fixed; bottom: 80px; left: 70px">
        <img src="data:image/png;base64,{{ $qr }}" />
    </div>
    <div style="position: fixed; bottom: 20px; width: 100%;margin-right:20px">
        <p style="font-style:italic;font-size:10px;text-align:center;padding-right:60px">*** Este documento é gerado automaticamente ***</p>
    </div>
    
</body>
</html>