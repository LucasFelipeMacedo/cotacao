class form {
    constructor() {
        this._response = '';
        this._title = '';
        this._id = '';
        this.initialize();
        this._cSearchType = '';
        this._cSearchFilter = '';
        this._cSearchItems = '';
    }

    set reponse(a) {
        this._reponse = a;
    }

    get reponse() {
        return this._reponse;
    }

    set title(a) {
        this._title = a;
    }

    get title() {
        return this._title;
    }


    set id(a) {
        this._id = a;
    }

    get id() {
        return this._id;
    }

    set cSearchType(a) {
        this._cSearchType = a;
    }

    get cSearchType() {
        return this._cSearchType;
    }

    set cSearchFilter(a) {
        this._cSearchFilter = a;
    }

    get cSearchFilter() {
        return this._cSearchFilter;
    }

    set cSearchItems(a) {
        this._cSearchItems = a;
    }

    get cSearchItems() {
        return this._cSearchItems;
    }

    initialize() {
        this.reponse = xhttp.responseText;
        this.addcontrol();
        this.eventListener();
        this.maskEventListener();
    }

    addcontrol() {

        let jsonFormulario = JSON.parse(this.reponse).formulario;
        let jsonObjetos = JSON.parse(this.reponse).objetos;
        let formData = '';
        let lastLine = 0;
        let currentLine = 0;

        var path = window.location.toString().split('/');
        this.title = path[4];

        jsonObjetos.forEach(element => {
            currentLine = element.linha;

            //Verifica qual é a linha do formulário que esta sendo apresentada
            if (currentLine != lastLine) {
                //se é diferente e linha anterior igual a 0 significa que é a primeira execução
                if (lastLine == 0) {
                    formData += '<div class="form-row">';
                } else {
                    formData += `</div>
					        <div class="form-row">`;
                }
            }

            formData += this.createObject(element);

            lastLine = currentLine;
        });

        this.createModal(jsonFormulario.titulo, formData);
    }

    createObject(json) {

        let enable = json.ativado;
        let format = json.formato;
        let name = json.nome;
        let required = json.obrigatorio;
        let label = json.rotulo;
        let size = json.tamanho;
        let type = json.tipo;
        let value = json.valor;
        let itens = json.itens;
        let mask = json.mascara;
        let codname = json.codigo_nome;
        let codvalue = json.codigo_valor;
        let searchType = json.busca_tipo;
        let searchField = json.busca_campos;
        let searchFilter = json.busca_filtro;

        let htmValue = '';

        if (value == null) {
            value = '';
        } else if (value == '[HOJE]') {
            let cDate = new Date;
            value = currentDate('yyyy-mm-dd');
            htmValue = `value="${value}"`;
        } else {
            htmValue = `value="${value}"`;
        }

        let htmCodValue = '';

        if (codvalue == null) {
            codvalue = '';
        } else {
            htmCodValue = `value="${codvalue}"`;
        }

        if (required == 'Sim') {
            required = 'Required';
        } else {
            required = '';
        }

        if (itens == null) { itens = ''; }
        if (mask == null) { mask = ''; }

        if (searchFilter == null) { searchFilter = ''; }

        //Substitui no filtro a aspas simples para não gerar problemas no HTML
        if (searchFilter != null) { searchFilter = searchFilter.replace(/'/g, "@"); }

        let html = '';

        switch (type.toLowerCase()) {
            case "textbox":
                let htmFormat = '';
                let htmEnable = '';
                let htmMask = '';

                if (format == 'Double') {
                    htmFormat = 'type="number" step="0.01"';
                } else if (format == 'Número') {
                    htmFormat = 'type="number" step="1"';
                } else if (format == 'Data') {
                    htmFormat = 'type="Date"';
                } else {
                    htmFormat = 'type="text"';
                }

                if (mask != '') {
                    htmMask = `placeholder="${mask}"`;
                }

                if (enable == 'Não') {
                    htmEnable = 'readonly="readonly"';
                }

                //if ($this->getVisible()===false) {
                //	$visible = ' style="visibility: hidden;position:absolute;"';
                //}

                html = `<div class = "form-group col-md-${size}">
                            <label for = "${name}"> ${label}</label> 
                                <input ${htmFormat} name = "${name}" class="form-control" id="${name}" ${htmValue} ${htmMask} ${htmEnable} autocomplete="off" ${required}>
                        </div>
                        `;

                break;
            case "combobox":

                html += `<div class="form-group col-md-${size}">
                             <label for="${name}">${label}</label>
                             <select id="${name}" name="${name}" class="form-control">`;

                //Se tem uma string fonte não executa a query
                if (itens != '') {
                    //console.log(itens);
                    let cmbitens = itens.split(',');

                    for (let i = 0; i < cmbitens.length; i++) {
                        if (value == cmbitens[i]) {
                            html += `<option selected>${cmbitens[i]}</option>`;
                        } else {
                            html += `<option>${cmbitens[i]}</option>`;
                        }
                    }

                }
                html += `</select>
                    </div>`;

                break;
            case "button":
                html = `<button type="button" id = "${name}" class="btn btn-dark">${label}</button>`;
                break;
            case "search":
                html = `<div class = "form-group col-md-${size}">
                            <label for = "${name}">${label}</label> 
                            <div class="input-group-prepend">
                                <input type="text" class="" name = "${codname}" class="form-control" id="${codname}" onblur="window.form.leaveSearch('${codname}','${name}')" ${htmCodValue} autocomplete="off" ${required}>
                                <button type="button" id = "${codname}_search" class="btn btn-primary" onclick="window.form.createSearch('${searchType}','${searchFilter}',items = ${searchField})"><i class="material-icons">search</i></button>
                            </div>
                            <input type="text" name = "${name}" class="form-control" id="${name}" readonly="readonly" ${htmValue} autocomplete="off" ${required}>
                        </div>
                        `;
                break;
            default:
        }
        return html;
    }

    createModal(title, form, save_cancel = true, searchModal = '') {

        //verifica se já existe uma modal uma moda criada e exclui
        if ((searchModal == '') && (document.querySelector('.modal') !== null)) {
            document.querySelector('.modal').remove();
        }

        let html = `
        <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="modal${searchModal}">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">${title}</h5>
                  <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>-->
                </div>
                <div class="modal-body">
                    <form method = "POST" id = "form" role = "form" enctype="multipart/form-data">
                            ${form}
                    </form>
                </div>`;
        if (save_cancel) {
            html += `<div class="modal-footer">
                        <button type="button" class="btn btn-primary" name="btnsalvar" id="btnsalvar">Salvar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal${searchModal}" name="btncancelar" id="btncancelar">Cancelar</button>
                    </div>`;
        }
        html += `</div>
            </div>
          </div>`;

        $("body").append(html); //Incluir o modal no body
        $('.modal').modal(); //Abrir o modal

    }

    leaveSearch(codname, name) {
        if (document.querySelector("#" + codname).value == '') {
            document.querySelector("#" + name).value = '';
        }
    }

    createSearch(type, filter, items) {
        //Volta as aspas para enviar a requisição
        if (filter != null) { filter = filter.replace(/@/g, "'"); }

        xmlHttpGet('/cmd/search/parametros?', function() {
            success(function() {
                window.form.addTable(items);
            });
            error(function() {

            });
        }, 'type=' + type + '&q=' + filter);

    }

    addTable(items, operation = '') {
        //Muda a resposta
        this.reponse = xhttp.responseText;
        let jsonTable = JSON.parse(this.reponse).tabela;
        let tbody;
        let htm = '';

        let items_array = '';

        items.forEach(x => {
            if (items_array == '') {
                items_array += "['" + x[0] + "','" + x[1] + "']";
            } else {
                items_array += ",['" + x[0] + "','" + x[1] + "']";
            }
        });

        items_array = '[' + items_array + ']';

        if (operation == 'update') {
            //Apaga todas as linhas
            document.querySelector('tbody').remove();
            //Criar um novo tbody
            tbody = document.createx('tbody');
        } else {
            //Abre a chave tabela
            htm += `<div style = "padding: 20px">
                    <div class="table-responsive">`;

            if (jsonTable.modo_escuro == false) {
                htm += '<table class="table table-hover">'; //Normal
            } else {
                htm += '<table class="table table-dark">'; //Dark
            }

            //Sempre inicia com o ID
            let columns_title = `<th scope="col"></th>`;

            jsonTable.colunas.forEach(element => {
                columns_title += `<th scope="col">${element}</th>`;
            });

            //Adiciona os titulos das colunas
            htm += `<thead>
                    <tr>${columns_title}</tr>
                </thead>
                <tbody>`;
        }

        //Percorrer todas as colunas
        let col = [];

        for (var value in jsonTable.dados[0]) {
            col.push(value);
        }

        //Percorrer todas as linhas
        for (var i = 0; i < jsonTable.dados.length; i++) {
            //Adiciona o checkbox
            htm += '<tr>';

            for (var j = 0; j < col.length; j++) {
                let value = jsonTable.dados[i][col[j]];

                //Limpar valor
                if (value == null || value == '*NOTHING*') { value = '' };

                switch (col[j].toLowerCase()) {
                    case 'id':
                        //Cria o radio com o valor do id
                        htm += `<tr>
                                            <td>
                                                <input class="fdorm-check-input modal-table" type="radio" name="id" id="${value}" value="${value}">
                                            </td>
                                            <td style = "font-size: 12px">${value}</td>`;
                        break;
                    case 'status':
                        let statusColor = '';

                        if (value.toLowerCase() == 'aberto' || value.toLowerCase() == 'em aberto') {
                            statusColor = 'primary';
                        } else if (value.toLowerCase() == 'aprovado' || value.toLowerCase() == 'pago' || value.toLowerCase() == 'baixado' || value.toLowerCase() == 'faturado' || value.toLowerCase() == 'ativo') {
                            statusColor = 'success';
                        } else if (value.toLowerCase() == 'andamento' || value.toLowerCase() == 'pago com cheque') {
                            statusColor = 'warning';
                        } else if (value.toLowerCase() == 'cancelado') {
                            statusColor = 'light';
                        } else {
                            statusColor = 'danger';
                        }
                        //Cria status personalizado
                        htm += `<td style = "font-size: 12px"><span class="badge badge-${statusColor}">${value}</span></td>`;
                        break;
                    default:
                        //Cria o campo padrão
                        htm += `<td style = "font-size: 12px">${value}</td>`;
                        break;
                }
            }
        }

        htm += '</tr>';

        htm += `    </tbody>
                    </table>
                </div>
            </div>`;
        let form = '';

        //Cria o campo de busca
        form = `<div class="form-row">
                    <div class = "form-group col-md-10"> 
                        <input type = '' name = "txtsearch" class="form-control" id="txtsearch" autocomplete="off" placeholder="Procurar">
                    </div>
                    <button type="button" id = "btnsearch" class="btn btn-dark" data-dismiss="modal_search" onclick="window.form.searchID(items = ${items_array})">Selecionar</button>
                </div>
                    ${htm}`;

        this.createModal(jsonTable.titulo, form, false, '_search');
        this.searchEventListener();
    }

    searchID(items = []) {

        var elements = document.querySelectorAll(".modal-table");
        let jsonTable = JSON.parse(this.reponse).tabela;

        for (var i = 0; i < elements.length; i++) {
            if (elements[i].checked == true) {
                for (var k = 0; k < jsonTable.dados.length; k++) {
                    if (jsonTable.dados[k].id == elements[i].id) {

                        //Carregar valores nos campos
                        items.forEach(x => {
                            document.querySelector('#' + x[0]).value = jsonTable.dados[k][x[1]];
                        });

                        this.removeModal('_search');
                        return false;
                    }
                }
            }
        }
        alert('Nenhum registro foi selecionado!');
    }

    eventListener() {
        let btnsalvar = document.querySelector('#btnsalvar');
        let btncancelar = document.querySelector('#btncancelar');

        btnsalvar.addEventListener('click', e => {
            if (this.validObject()) {
                this.save(this.id, this.title);
            }
        });

        btncancelar.addEventListener('click', e => {
            this.cancel();
        });
    }

    searchEventListener() {
        //Ao clicar na linha marcar a radiobox
        let row = document.querySelector('#modal_search').querySelector('tbody').querySelectorAll('tr');
        row.forEach(element => {
            element.addEventListener('click', e => {
                element.querySelector('.fdorm-check-input').checked = true;
            });
        });
        //Alterar o ponteiro do mouse
        row.forEach(element => {
            addEventListenerAll(element, 'mouseover mouseup mousedown', e => {
                element.style.cursor = "pointer";
            });
        });
        //Ao digitar mais de 3 letras no campo procurar ativar o modo busca
        let txtsearch = document.querySelector('#txtsearch');
        txtsearch.addEventListener('change', e => {
            if (txtsearch.value.lenght > 2) {

            }
        });

    }

    maskEventListener() {

        let object = document.querySelectorAll('.form-control');

        object.forEach(element => {
            if (element.placeholder != '') {
                var mask = element.placeholder;
                element.addEventListener('keydown', e => {
                    element.value = maskFormat(element.value, mask);
                });
            }
        });

    }

    cancel() {
        this.removeModal();
        console.log('passou aqui ->cancel');
    }

    save(id, title) {
        var current_form = document.querySelector('#form');
        var form = new FormData(current_form);
        form.append('id', id);
        form.append('title', title);

        xmlHttpPost('/cmd/salvar-formulario', function() {
            success(function() {
                if (xhttp.responseText != 'Error') {
                    window.location.reload();
                } else {
                    alert(xhttp.responseText);
                }
            });
            error(function() {
                alert('Não foi possivel salvar, este registro.');
            });
        }, form);

    }

    removeModal(searchModal = '') {
        let mod = document.getElementById('modal' + searchModal);

        mod.remove();

        //Fechar todos os modalbackdrop fundo escuro
        if (searchModal == '') {
            console.log('passou aqui console log');
            let bk = document.querySelectorAll('.modal-backdrop');

            bk.forEach(element => {
                element.remove();
            });
        }
    }

    validObject() {
        let objects = document.querySelectorAll('.form-control');
        let valid = true;

        objects.forEach(element => {
            if (element.required == true && element.value == '') {
                alert('O campo ' + right(element.name, element.name.lenght - 3) + ' é obrigatório!');
                valid = false;
            }
        });

        return valid;
    }

}