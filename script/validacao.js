const login = document.querySelector(".form1");
const elMensagem = document.getElementById("mensagem");

if (login) {

    login.onsubmit = function (event) {
        event.preventDefault();

        const email = document.getElementById("inputEmail").value;
        const senha = document.getElementById("inputSenha").value;

        const email_definido = "admin@teste.com";
        const senha_definida = "adm1n123";

        elMensagem.innerHTML = "";

        if (email === "" || senha === "") {
            elMensagem.innerHTML = "<div class = 'text-danger fw-bold'> Preencha todos os campos</div>";
            return;
        }

        if (!email.includes("@") || !email.includes(".")) {
            elMensagem.innerHTML = "<div class = 'text-danger fw-bold'> Email Inválido!</div>";
            return;
        }

        if (senha.length < 4) {
            elMensagem.innerHTML = "<div class = 'text-danger fw-bold'> Senha muito curta!</div>";
            return
        }

        if (email === email_definido && senha === senha_definida) {
            window.location.href = "tela_geral_home.html";
        }else{
            elMensagem.innerHTML = "<div class = 'text-danger fw-bold'>Dados incorretos!</div>";
        }
    };
}
