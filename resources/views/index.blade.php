<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Soteador de times</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous">
    </script>


    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.0/color-thief.umd.js"></script>
    <script type="application/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/r-2.2.2/sc-2.0.0/datatables.min.js">
    </script>

    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js" type="application/javascript">
    </script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js" type="application/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="application/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js" type="application/javascript">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js" type="application/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js" type="application/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js" type="application/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="application/javascript">
    </script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script src="./js/players.js"></script>


</head>

<body>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

    <div class="row">
        <div class="row">

            <div class="table-responsive-sm col-8 container">
                <table class="dt-table table table-hover table-sm" id="tablePlayers">
                    <thead class="thead-inverse">
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Nivel</th>
                            <th>Goleiro</th>
                            <th>Presença</th>
                            <th></th>

                        </tr>
                    </thead>
                </table>
            </div>



        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    Novo jogador
                </div>
                <div class="card-body">
                    <form id="FormPlayer" action="" enctype="multipart/form-data">
                        @csrf
                        <input type="text" id="idPlayer" name="idPlayer" hidden class="form-control">

                        <div class="form-group">
                            <label for="txtName">Nome</label>
                            <input type="text" class="form-control" name="txtName" id="txtName">
                        </div>
                        <div class="form-group">
                            <label for="txtLevel">Nivel</label>
                            <select class="form-control" id="txtLevel" name="txtLevel">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="txtGoal">É Goleiro</label>
                            <select class="form-control" id="txtGoal" name="txtGoal">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>

                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-success">Salvar</button>
                            <button id="btnClear" type="button" class="btn btn-secondary">limpar</button>

                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-6">
            <input type="text" class="form-control" name="txtQtdplayer" id="txtQtdplayer">
            <button id="btnRaffle" type="button" class="btn btn-secondary">Sortear</button>
            <div class="row" id="listTeam">

            </div>
        </div>
    </div>
</body>

</html>
