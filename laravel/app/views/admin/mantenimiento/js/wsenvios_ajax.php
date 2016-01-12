<script type="text/javascript">
    var WsEnvios = {
        CargarWsEnvios: (function () {
            var columnDefs = [
                {
                    "targets": 0,
                    "data": "fecha",
                    "name": "fecha"
                },
                {
                    "searchable": true,
                    "targets": 1,
                    "data": "trama",
                    "name": "trama",
                    "render": function(data, type, full) {
                                var trama = eval('(' + data + ')');
                                return trama.Operation;
                            }
                },
                {
                    "searchable": true,
                    "targets": 2,
                    "data": "trama",
                    "name": "trama",
                    "render": function(data, type, full) {
                                var trama = eval('(' + data + ')');
                                return trama.TaskNumber;
                            }
                },
                {
                    "searchable": true,
                    "targets": 3,
                    "data": "trama",
                    "name": "trama",
                    "render": function(data, type, full) {
                                var trama = eval('(' + data + ')');
                                return trama.EmployeeNumber;
                            }
                },
                {
                    "targets": 4,
                    "data": "trama",
                    "name": "trama",
                    "render": function(data, type, full) {
                                var trama = eval('(' + data + ')');
                                return trama.Duration;
                            }
                },
                {
                    "targets": 5,
                    "data": "trama",
                    "name": "trama",
                    "render": function(data, type, full) {
                                var trama = eval('(' + data + ')');
                                return trama.Data19;
                            }
                },
                {
                    "targets": 6,
                    "data": "trama",
                    "name": "trama",
                    "render": function(data, type, full) {
                                var trama = eval('(' + data + ')');
                                return (trama.Data27 ? trama.Data27:"");
                            }
                },
                {
                    "searchable": true,
                    "targets": 7,
                    "data": "response",
                    "name": "response"
                }
            ];
            $('#t_wsenvios').dataTable().fnDestroy();
            $('#t_wsenvios')
                    .on('page.dt', function () {
                        $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                    })
                    .on('search.dt', function () {
                        $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                    })
                    .on('order.dt', function () {
                        $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                    })
                    .DataTable({
                        "processing": true,
                        "serverSide": true,
                        "stateSave": true,
                        "searchable": true,
                       
                        "stateLoadCallback": function (settings) {
                            $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                        },
                        "stateSaveCallback": function (settings) {
                            $(".overlay,.loading-img").remove();
                        },
                        "ajax": {
                            url: "wsenvios/paginacion",
                            type: "POST"
                        },
                        columnDefs
                    });   
        })
    };
</script>