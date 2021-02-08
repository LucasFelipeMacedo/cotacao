class cotacao {
    constructor() {
        this._txtcodigo;
        this._txtparceiro;
        this._txtdata_criacao;
        this._txtcriador;
        this._txtstatus;
        this._txtdata_fechamento;
        this._txtprazo_entrega;
        this._txtnumero_orcamento;
        this._txtobservacao_fornecedor;
        this._txtvalor_total_itens;
        this._txtdesconto;
        this._txticms;
        this._txtipi;
        this._txtpis;
        this._txtcofins;
        this._txtfrete;
        this._txtvalor_total;
        this._txtobservacao_empresa;
        this._btnenviar;
        this._alertsucess;
        this._alerterror;
        this._screen;
        this.initialize();
    }

    set codigo(a) { this._txtcodigo = a; }
    set parceiro(a) { this._txtparceiro = a; }
    set data_criacao(a) { this._txtdata_criacao = a; }
    set criador(a) { this._txtcriador = a; }
    set status(a) { this._txtstatus = a; }
    set data_fechamento(a) { this._txtdata_fechamento = a; }
    set prazo_entrega(a) { this._txtprazo_entrega = a; }
    set numero_orcamento(a) { this._txtnumero_orcamento = a; }
    set observacao_fornecedor(a) { this._txtobservacao_fornecedor = a; }
    set valor_total_itens(a) { this._txtvalor_total_itens = a; }
    set desconto(a) { this._txtdesconto = a; }
    set icms(a) { this._txticms = a; }
    set ipi(a) { this._txtipi = a; }
    set pis(a) { this._txtpis = a; }
    set cofins(a) { this._txtcofins = a; }
    set frete(a) { this._txtfrete = a; }
    set valor_total(a) { this._txtvalor_total = a; }
    set observacao_empresa(a) { this._txtobservacao_empresa = a; }
    set enviar(a) { this._btnenviar = a; }
    set alertsucess(a) { this._alertsucess = a; }
    set alerterror(a) { this._alerterror = a; }
    set screen(a) { this._screen = a; }

    get codigo() { return this._txtcodigo; }
    get parceiro() { return this._txtparceiro; }
    get data_criacao() { return this._txtdata_criacao; }
    get criador() { return this._txtcriador; }
    get status() { return this._txtstatus; }
    get data_fechamento() { return this._txtdata_fechamento; }
    get prazo_entrega() { return this._txtprazo_entrega; }
    get numero_orcamento() { return this._txtnumero_orcamento; }
    get observacao_fornecedor() { return this._txtobservacao_fornecedor; }
    get valor_total_itens() { return this._txtvalor_total_itens; }
    get desconto() { return this._txtdesconto; }
    get icms() { return this._txticms; }
    get ipi() { return this._txtipi; }
    get pis() { return this._txtpis; }
    get cofins() { return this._txtcofins; }
    get frete() { return this._txtfrete; }
    get valor_total() { return this._txtvalor_total; }
    get observacao_empresa() { return this._txtobservacao_empresa; }
    get enviar() { return this._btnenviar; }
    get alertsucess() { return this._alertsucess; }
    get alerterror() { return this._alerterror; }
    get screen() { return this._screen; }

    initialize() {

        this.codigo = document.querySelector('#txtcodigo');
        this.parceiro = document.querySelector('#txtparceiro');
        this.data_criacao = document.querySelector('#txtdata_criacao');
        this.criador = document.querySelector('#txtcriador');
        this.status = document.querySelector('#txtstatus');
        this.data_fechamento = document.querySelector('#txtdata_fechamento');
        this.prazo_entrega = document.querySelector('#txtprazo_entrega');
        this.numero_orcamento = document.querySelector('#txtnumero_orcamento');
        this.observacao_fornecedor = document.querySelector('#txtobservacao_fornecedor');

        this.valor_total_itens = document.querySelector('#txtvalor_total_itens');
        this.desconto = document.querySelector('#txtdesconto');
        this.icms = document.querySelector('#txticms');
        this.ipi = document.querySelector('#txtipi');
        this.pis = document.querySelector('#txtpis');
        this.cofins = document.querySelector('#txtcofins');
        this.frete = document.querySelector('#txtfrete');
        this.valor_total = document.querySelector('#txtvalor_total');
        this.observacao_empresa = document.querySelector('#txtobservacao_empresa');
        this.enviar = document.querySelector('#btnenviar');

        this.alertsucess = document.querySelector('#alerta_sucesso');
        this.alerterror = document.querySelector('#alerta_erro');

        this.screen = document.querySelector('#form');

        this.eventListener();
    }

    eventListener() {

        let allElements = document.querySelectorAll('.calc');

        allElements.forEach(element => {
            element.addEventListener('blur', e => {
                this.calc(element);
            });
        });

        this.enviar.addEventListener('click', e => {
            this.send();
        });

    }

    calc(e) {

        let qtdElements = document.querySelectorAll('.table-qtd');
        let vlr_unitElements = document.querySelectorAll('.table-vlr-unit');
        let vlr_totalElements = document.querySelectorAll('.table-vlr-total');

        //Zera o valor total dos itens antes do calculo 
        this.valor_total_itens.value = 0;

        //Calcular a qtd x o valor unitário
        for (let i = 0; i < qtdElements.length; i++) {
            vlr_totalElements[i].value = this.cnum(qtdElements[i].value) * this.cnum(vlr_unitElements[i].value);
            vlr_totalElements[i].value = vlr_totalElements[i].value / 100;
            //console.log(vlr_totalElements[i].value);
            vlr_totalElements[i].value = this.fnum(vlr_totalElements[i].value, 2);
        }
        var total = 0;

        //Calcular o valor total dos itens
        for (let i = 0; i < vlr_totalElements.length; i++) {
            total += this.cnum(vlr_totalElements[i].value);
            this.valor_total_itens.value = total;
        }

        this.valor_total_itens.value = this.fnum(this.valor_total_itens.value);

        //Calcular valor total
        this.valor_total.value = this.cnum(this.valor_total_itens.value) - this.cnum(this.desconto.value) + this.cnum(this.icms.value) + this.cnum(this.ipi.value) + this.cnum(this.pis.value) + this.cnum(this.cofins.value) + this.cnum(this.frete.value);
        this.valor_total.value = this.fnum(this.valor_total.value, 2);
        e.value = this.cnum(e.value);
        e.value = this.fnum(e.value, 2);

    }

    cnum(value) {
        //Retira virgulas e pontos para o calculo
        if (typeof value != 'undefined') {
            let a = value.replace(',', '.');
            a = parseInt(a * 100);
            return a;
        } else {
            return 0;
        }
    }

    fnum(value, decimal_places = 2, dot = ",") {
        //Configura as casas decimais
        let negative = '';

        if (this.left(value, 1) == '-') {
            value = this.right(value, value.length - 1);
            negative = '-';
        }

        if (value.length <= decimal_places) {
            let q = decimal_places - value.length + 1;
            for (let i = 0; i < q; i++) {
                value = "0" + value.toString();
            }
        }

        let end = this.right(value, decimal_places);

        let ini = this.left(value, value.length - decimal_places);

        return negative + ini + dot + end;
    }

    right(value, caractNum) {
        return value.substring(value.length - caractNum, value.length);
    }

    left(value, caractNum) {
        return value.substring(0, caractNum);
    }

    send() {
        var current_form = document.querySelectorAll('.form-control');
        var form = new FormData();

        var path = window.location.toString().split('?');
        var chave = path[1];

        form.append('key', chave.replace('key=', ''));

        for (let i = 0; i < current_form.length; i++) {
            form.append(current_form[i].id, current_form[i].value);
        }

        xmlHttpPost('php/save.php', function() {
            success(function() {
                console.log(xhttp.responseText);
                if (xhttp.responseText == 'Send with success.') {
                    document.querySelector('#alerta_sucesso').style.display = "block";
                    document.querySelector('#form').style.display = "none";
                } else {
                    document.querySelector('#alerta_erro').style.display = "block";
                }
            });
            error(function() {
                alert('Error: Not was possible save this register.');
            });
        }, form);
    }

}