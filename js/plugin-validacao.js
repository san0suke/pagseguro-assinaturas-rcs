$ = jQuery;
$(function () {
    $("#validacao_plugin").submit(function () {
        $.post("admin-ajax.php",
                $("#validacao_plugin").serialize(),
                function (response) {
                    alert(response.retorno);
                    if (response.status) {
                        document.location = response.redirect;
                    } else {
                        location.reload();
                    }
                }, "json");

        return false;
    });
});