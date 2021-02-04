<?php

$configuracoesQueTemNoCertoENaoTemNoDiferenciado = [];
$configuracoesQueTemNoDiferenciadoENaoTemNoCerto = [];
$valoresDiferentesEntreCertoEDiferenciado = [];

$configuracoesQueTemNosDoisEEstaoDiferente = [];

$definesArquivoCerto = [];
$definesArquivoDiferenciado = [];
$compared = false;

$correta = 'CORRETA';
$diferenciado = 'DIFERENCIADO';


if (!empty($_FILES)) {
    $ar_certo = $_FILES['sdk_certo']['tmp_name'];
    $ar_difereciado = $_FILES['sdk_diferenciado']['tmp_name'];

    if ($ar_certo && $ar_difereciado) {

        $compared = true;

        $correta = $_POST['c'];
        $diferenciado = $_POST['d'];

        $leitura_ar_certo = fopen($ar_certo, "r");
        $leitura_ar_diferenciado = fopen($ar_difereciado, "r");

        while (($linha = fgets($leitura_ar_certo)) !== false) {
            if (strpos($linha, "#define") !== false) {
                $comando = explode(" ", $linha);

                if (array_key_exists($comando[1], $definesArquivoCerto)) continue;
                $definesArquivoCerto[$comando[1]] = trim($comando[2]);
            }
        }


        while (($linha = fgets($leitura_ar_diferenciado)) !== false) {
            if (strpos($linha, "#define") !== false) {
                $comando = explode(" ", $linha);

                if (array_key_exists($comando[1], $definesArquivoDiferenciado)) continue;
                $definesArquivoDiferenciado[$comando[1]] = trim($comando[2]);
            }
        }


        foreach ($definesArquivoCerto as $define => $valor) {
            if (!array_key_exists($define, $definesArquivoDiferenciado)) {
                $configuracoesQueTemNoCertoENaoTemNoDiferenciado[$define] = $valor;
            } else {
                if ($valor != $definesArquivoDiferenciado[$define]) {
                    $valoresDiferentesEntreCertoEDiferenciado[$define] = [$valor, $definesArquivoDiferenciado[$define]];
                }
            }
        }


        foreach ($definesArquivoDiferenciado as $define => $valor) {
            if (!array_key_exists($define, $definesArquivoCerto)) {
                $configuracoesQueTemNoDiferenciadoENaoTemNoCerto[$define] = $valor;
            }
        }

        if ($leitura_ar_certo && $leitura_ar_diferenciado) {
            fclose($leitura_ar_certo);
            fclose($leitura_ar_diferenciado);
        }
    }
}
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>COMPARA SDK</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>

<nav class="navbar">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">COMPARA SDK</a>
        </div>
    </div>
</nav>

<style>
    .inline-input {
        display: inline-block;
        width: auto;
        height: 27px;
        padding-left: 2px;
    }
</style>

<div class="container">

    <div class="card">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="exampleInputEmail1">SDK <input type="text" class="form-control inline-input"
                                                               value="<?= $correta ?>" name="c"></label>
                    <input type="file" class="form-control" name="sdk_certo">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">SDK <input type="text" class="form-control inline-input"
                                                                  value="<?= $diferenciado ?>" name="d"></label>
                    <input type="file" class="form-control" name="sdk_diferenciado">
                </div>
                <button type="submit" class="btn btn-primary">COMPARAR</button>
            </form>
        </div>
    </div>

    <div class="result">
        <?php
        if ($compared) {
            ?>
            <hr style="margin: 50px 0px 50px 0px;">
            <h2>Itens que estão no <span class="badge badge-success"><?= $correta ?></span> e não no <span
                        class="badge badge-danger"><?= $diferenciado ?></span>.
                Qtd: <?= count($configuracoesQueTemNoCertoENaoTemNoDiferenciado) ?></h2>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">CONFIGURAÇÃO</th>
                    <th scope="col">VALOR</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                foreach ($configuracoesQueTemNoCertoENaoTemNoDiferenciado as $define => $value) {
                    $i++;
                    ?>
                    <tr>
                        <th scope="row"><?= $i ?></th>
                        <td><?= $define ?></td>
                        <td><?= $value ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>

            <!--



            -->
            <hr style="margin: 50px 0px 50px 0px;">
            <h2>Valores diferentes entre o <span class="badge badge-success"><?= $correta ?></span> e o <span
                        class="badge badge-danger"><?= $diferenciado ?></span>.
                Qtd: <?= count($valoresDiferentesEntreCertoEDiferenciado) ?></h2>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">CONFIGURAÇÃO</th>
                    <th scope="col">VALOR <?= $correta ?></th>
                    <th scope="col">VALOR <?= $diferenciado ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                foreach ($valoresDiferentesEntreCertoEDiferenciado as $define => $value) {
                    $i++;
                    ?>
                    <tr>
                        <th scope="row"><?= $i ?></th>
                        <td><?= $define ?></td>
                        <td style="color: limegreen"><?= $value[0] ?></td>
                        <td style="color: indianred"><?= $value[1] ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>


            <hr style="margin: 50px 0px 50px 0px;">
            <h2>Itens que estão no <span
                        class="badge badge-danger"><?= $diferenciado ?></span> e não no <span
                        class="badge badge-success"><?= $correta ?></span>.
                Qtd: <?= count($configuracoesQueTemNoDiferenciadoENaoTemNoCerto) ?></h2>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">CONFIGURAÇÃO</th>
                    <th scope="col">VALOR</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                foreach ($configuracoesQueTemNoDiferenciadoENaoTemNoCerto as $define => $value) {
                    $i++;
                    ?>
                    <tr>
                        <th scope="row"><?= $i ?></th>
                        <td><?= $define ?></td>
                        <td><?= $value ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php
        }
        ?>
    </div>
</div><!-- /.container -->
</body>
</html>
