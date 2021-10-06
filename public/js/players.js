$(document).ready(function () {

    getPlayers();

    $('#btnClear').click(function () {
        $('#idPlayer').val("")
        $('#txtName').val("")
        $('#txtLevel').val(1)
        $('#txtGoal').val(1)
    });

    $('#btnRaffle').click(function () {
        RaffleTeams($('#txtQtdplayer').val());
    });



    // registration Player
    $('#FormPlayer').submit(function (e) {
        //$('.Loading').show();

        e.preventDefault();

        var router;

        var data = {};

        var form = $('#FormPlayer').serializeArray();

        for (var i = 0; i < form.length; i++) {
            var element = form[i];
            data[element['name']] = element['value'];
        }

        console.log(data);

        if (data.idPlayer == '') {
            router = '../newPlayer';
            metodo = 'Post'
        } else {
            router = '../editPlayer';
            metodo = 'Put'
        }

        $.ajax({
            headers: {
                "Content-Type": "application/json",
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: router,
            type: metodo,
            data: JSON.stringify(data),
            cache: false,
            processData: false,
            timeout: 8000,
            contentType: "application/json; charset=utf-8",
            success: function (data) {


                if (router == './newPlayer') {

                    Swal.fire(
                        'Cadastrado',
                        'Cadastrado com sucesso!',
                        'success'
                    )


                } else if (router == './editPlayer') {
                    Swal.fire(
                        'Editado',
                        'Editado com sucesso!',
                        'success'
                    );

                }
                $('#idPlayer').val("")
                $('#txtName').val("")
                $('#txtLevel').val(1)
                $('#txtGoal').val(1)
                getPlayers();
            },
            error: function (data) {
                Swal.fire(
                    'erro!',
                    'error'
                );
            }
        });

    });
});


function RaffleTeams(Qtdplayer) {

    $.get('../randomDraw/' + Qtdplayer, {
        dataType: 'json',
        processData: false,
        contentType: false,
        _token: $('#token').val()
    }).then(function (data) {
        //console.log(data);
        var html = '';


        keys = [];

        Object.entries(data.dados).forEach(([key, value]) => {
            keys[key] = value
        });

         t = 1;
        console.log(keys);
        while (t <= data.teams) {

            var listHmlm = "";

             for (let i = 0; i < keys[('team'+t)].length; i++) {
                listHmlm += "<li class='list-group-item'>" + keys[('team'+t)][i].name + "</li>";
            }


            html += "<div class='col-6'>" +
                "<ul class='list-group'>" +
                "<li class='list-group-item active'>Time " + t + "</li>" +
                 listHmlm +
                "</ul>" +
                "</div>";

                console.log(html);


            t +=1;
        }



        $('#listTeam').html(html)
    });

}

function getPlayers() {

    $.get('../getPlayers', {
    }).then(function (data) {


        console.log(data);

        for (var i = 0; i < data.length; i++) {
            if (data[i].presence == 0) {
                data[i].actionPresence = "<a  onClick='confirmPresence(" + (data[i].idPlayer) + "," + 1 + ")' class='btn btn-outline-danger btn-sm waves-effect px-3'><i class='fas fa-power-off red-text pr-3' aria-hidden='true''></i>Confirmar</a>";
            } else {
                data[i].actionPresence = "<a onClick='confirmPresence(" + (data[i].idPlayer) + "," + 0 + ")' class='btn btn-outline-success btn-sm waves-effect px-3'><i class='fas fa-power-off grenn-text pr-3' aria-hidden='true''></i>Desconfirmar</a>";
            }

            if (data[i].goalkeeper == 1) {
                data[i].goalkeeper = "Sim";
            } else {
                data[i].goalkeeper = "Não";
            }

            data[i].update = "<a onClick='getPlayerId(" + (data[i].idPlayer) + ")' class='btn btn btn-outline-info btn-sm waves-effect px-3'><i class='fas fa-power-off grenn-text pr-3' aria-hidden='true''></i>Editar</a>";
        }

        startTable(data);
        // $('body').loading('stop');
    });
}




function startTable(data) {
    console.log("here");
    DataTable = $("#tablePlayers").DataTable({
        "processing": true,
        "serverSide": false,
        "data": data,
        "columns": [{
            "data": "idPlayer",
            "width": "5%"
        },
        {
            "data": "name",
            "width": "7%"
        },
        {
            "data": "level",
            "width": "7%"
        },
        {
            "data": "actionPresence",
            "width": "7%"
        },
        {
            "data": "goalkeeper",
            "width": "7%"
        },
        {
            "data": "update",
            "width": "7%"
        }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Portuguese-Brasil.json"
        },
        "columnDefs": [{
            "className": "dt-center",
            "targets": "_all"
        }],
        "pageLength": 20,
        "bLengthChange": false,
        "bFilter": false,
        "responsive": false,
        "destroy": true
    });
}

function getPlayerId(idPlayer) {
    $.get('../getPlayerId/' + idPlayer, {
        dataType: 'json',
        processData: false,
        contentType: false,
        _token: $('#token').val()
    }).then(function (data) {
        $('#idPlayer').val(data[0].idPlayer)
        $('#txtName').val(data[0].name)
        $('#txtLevel').val(data[0].level)
        $('#txtGoal').val(data[0].goalkeeper)

    });
}

function confirmPresence(idPlayer, presence) {

    $.post('../confirmPresence/' + idPlayer + "/" + presence, {
        dataType: 'json',
        processData: false,
        contentType: false,
        _token: $('#token').val()
    }).then(function (data) {
        getPlayers();
    });

}
