const login = document.querySelector(".form1");
const mensagem = document.getElementById("mensagem");

const toggle_senha = document.getElementById("toggleSenha");
const senha = document.getElementById("inputSenha");

if (toggle_senha) {
    const icon_senha = toggle_senha.querySelector("i");
    toggle_senha.onclick = () => {
        if (senha.type === "password") {
            senha.type = "text";
            icon_senha.className = "bi bi-eye-slash";
        } else {
            senha.type = "password";
            icon_senha.className = "bi bi-eye";
        }
    };
}

// Validação do form de cadastro de usuário

const input_cpf = document.getElementById("input_cpf");
const input_telefone = document.getElementById("input_telefone");

const toggle_senha_cadastro = document.getElementById("toggleSenhaCadastro");
const input_senha_cadastro = document.getElementById("input_senha");

if (toggle_senha_cadastro) {
    const icon_senha_cadastro = toggle_senha_cadastro.querySelector("i");
    toggle_senha_cadastro.onclick = () => {
        if (input_senha_cadastro.type === "password") {
            input_senha_cadastro.type = "text";
            icon_senha_cadastro.className = "bi bi-eye-slash";
        } else {
            input_senha_cadastro.type = "password";
            icon_senha_cadastro.className = "bi bi-eye";
        }
    };
}

if (input_cpf) {
    input_cpf.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });
}

if (input_telefone) {
    input_telefone.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);

        if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
        }
        if (value.length > 9) {
            value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
        } else if (value.length > 8) {
            value = value.replace(/(\d{4})(\d{1,4})$/, '$1-$2');
        }
        e.target.value = value;
    });
}

// Validação do form de cadastro de sensor (RF18 / RN03)

const form_cadastro_sensor = document.getElementById("form_cadastro_sensor");
const mensagem_cadastro_sensor = document.getElementById("mensagem_cadastro_sensor");
const select_localizacao_sensor = document.getElementById("input_localizacao_sensor");
const vinculo_trem_radio = document.getElementById("vinculo_trem");
const vinculo_rota_radio = document.getElementById("vinculo_rota");

// RN03: Todo sensor deve estar vinculado a um trem ou a um trecho da ferrovia.
// O toggle Trem/Rota controla quais opções aparecem no select de Localização.
const opcoes_localizacao_trem = [
    { value: "", label: " ", disabled: true },
    { value: "", label: "Trem" },
    { value: "trem_5823", label: "Trem - ID 5823" }
];

const opcoes_localizacao_rota = [
    { value: "", label: "Rota", disabled: true },
    { value: "trecho_rota1", label: "Rota 1 (Estação Norte → Estação Sul)" },
    { value: "trecho_rota2", label: "Rota 2 (Estação Norte → Estação Sul)" }
];

function preencher_select_localizacao(opcoes) {
    select_localizacao_sensor.innerHTML = "";
    opcoes.forEach(opcao => {
        const option = document.createElement("option");
        option.value = opcao.value;
        option.textContent = opcao.label;
        if (opcao.disabled) {
            option.disabled = true;
            option.selected = true;
        }
        select_localizacao_sensor.appendChild(option);
    });
}

if (select_localizacao_sensor && vinculo_trem_radio && vinculo_rota_radio) {
    preencher_select_localizacao(opcoes_localizacao_trem);

    vinculo_trem_radio.addEventListener('change', () => preencher_select_localizacao(opcoes_localizacao_trem));
    vinculo_rota_radio.addEventListener('change', () => preencher_select_localizacao(opcoes_localizacao_rota));
}

if (form_cadastro_sensor) {
    form_cadastro_sensor.addEventListener('submit', async function (event) {
        event.preventDefault();

        const nome_sensor = document.getElementById("input_nome_sensor").value.trim();
        const localizacao_sensor = select_localizacao_sensor.value;
        const tipo_sensor = document.getElementById("input_tipo_sensor").value;
        const statusSelecionado = form_cadastro_sensor.querySelector('input[name="status_sensor"]:checked');
        const status_sensor = statusSelecionado ? statusSelecionado.value : '';

        mensagem_cadastro_sensor.innerHTML = "";

        if (!nome_sensor || !localizacao_sensor || !tipo_sensor || !status_sensor) {
            mensagem_cadastro_sensor.innerHTML = "<div class='text-danger fw-bold'>Preencha todos os campos!</div>";
            return;
        }

        if (nome_sensor.length < 3) {
            mensagem_cadastro_sensor.innerHTML = "<div class='text-danger fw-bold'>Nome do sensor deve ter pelo menos 3 caracteres!</div>";
            return;
        }

        // RN03: Todo sensor deve estar vinculado a um trem ou a um trecho da ferrovia
        if (!localizacao_sensor.startsWith("trem_") && !localizacao_sensor.startsWith("trecho_")) {
            mensagem_cadastro_sensor.innerHTML = "<div class='text-danger fw-bold'>O sensor deve estar vinculado a um trem ou a uma rota!</div>";
            return;
        }

        const botaoSalvar = document.getElementById('btn_salvar_sensor');
        const textoBotao = botaoSalvar ? botaoSalvar.textContent : '';
        if (botaoSalvar) {
            botaoSalvar.disabled = true;
            botaoSalvar.textContent = 'Salvando...';
        }

        const dados = new FormData(form_cadastro_sensor);
        const localizacaoSelecionada = select_localizacao_sensor.options[select_localizacao_sensor.selectedIndex];
        const localizacao = dados.get('sensor_id') && form_cadastro_sensor.dataset.localizacaoChave === localizacao_sensor
            ? form_cadastro_sensor.dataset.localizacaoOriginal
            : localizacaoSelecionada.textContent.trim();
        dados.set('localizacao', localizacao);
        const selectTipoSensor = document.getElementById('input_tipo_sensor');
        const tipoSelecionado = selectTipoSensor.options[selectTipoSensor.selectedIndex];
        const tipoDado = dados.get('sensor_id') && form_cadastro_sensor.dataset.tipoChave === tipo_sensor
            ? form_cadastro_sensor.dataset.tipoOriginal
            : tipoSelecionado.textContent.trim();
        dados.set('tipo_dado', tipoDado);

        try {
            const resposta = await fetch(form_cadastro_sensor.action, {
                method: 'POST',
                body: dados,
                headers: { 'Accept': 'application/json' }
            });
            const resultado = await resposta.json();
            if (!resposta.ok || !resultado.success) {
                throw new Error(resultado.message || 'Não foi possível salvar o sensor.');
            }

            mensagem_cadastro_sensor.innerHTML = "<div class='text-success fw-bold'>Sensor salvo com sucesso! Status: " + status_sensor + ".</div>";
            window.location.reload();
        } catch (erro) {
            mensagem_cadastro_sensor.innerHTML = "<div class='text-danger fw-bold'>" + erro.message + "</div>";
        } finally {
            if (botaoSalvar) {
                botaoSalvar.disabled = false;
                botaoSalvar.textContent = textoBotao;
            }
        }
    });
}
