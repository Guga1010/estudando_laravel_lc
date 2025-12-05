<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <!-- Card de pesquisa de marcas -->
                <card-component titulo="Pesquisa de Marcas">
                    <template v-slot:conteudo>
                        <div class="row">
                            <div class="col mb-3">
                                <input-container-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional, informe o ID da marca">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID">
                                </input-container-component>
                            </div>
                            <div class="col mb-3">
                                <input-container-component titulo="Nome" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional, informe o nome da marca">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="idNome" placeholder="Nome da Marca">
                                </input-container-component>
                            </div>
                        </div>
                    </template>
                    
                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Pesquisar</button>
                    </template>

                </card-component>

                <!-- Fim do card de pesquisa de marcas -->

                <!-- Card de listagem de marcas -->
                <card-component titulo="Listagem de Marcas">
                    <template v-slot:conteudo>
                        <table-component></table-component> 
                    </template>

                    <template v-slot:rodape>
                        <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">
                            Adicionar
                        </button>
                    </template>
                </card-component>

                <!-- Fim do card de listagem de marcas -->

            </div>
        </div>
        <modal-component id="modalMarca" titulo="Adicionar Marca">

            <template v-slot:alertas>
                <alert-component tipo="success" v-if="transacaoStatus == 'adicionado'">
                </alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Ocorreu um erro ao cadastrar a marca" v-if="transacaoStatus == 'erro'">
                </alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da Marca" id="inputNovoNome" id-help="novoNomeHelp" texto-ajuda="Informe o nome da marca">
                        <input type="text" class="form-control" id="inputNovoNome" aria-describedby="novoNomeHelp" placeholder="Nome da marca" v-model="nomeMarca">
                    </input-container-component>
                </div>
                
                <div class="form-group">
                    <input-container-component titulo="Imagem" id="inputNovaImagem" id-help="novaImagemHelp" texto-ajuda="Selecione uma imagem">
                        <input type="file" class="form-control-file" id="inputNovaImagem" aria-describedby="novaImagemHelp" placeholder="Imagem da Marca" @change="carregarImagem($event)">
                    </input-container-component>
                </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>

        </modal-component>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                urlBase: 'http://localhost:8000/api/v1/marca',
                nomeMarca: '',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: []
            }
        },
        computed: {
            token(){
                let token = document.cookie.split(';').find(indice => {
                    return indice.includes('token=')
                })

                token = token.split('=')[1]
                token = 'Bearer ' + token

                return token;
            }
        },
        methods: {
            carregarImagem(e){
                this.arquivoImagem = e.target.files
            },
            salvar(){

                let formData = new FormData();
                formData.append('nome', this.nomeMarca)
                formData.append('imagem', this.arquivoImagem[0])

                let config = {
                    headers:{
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }

                axios.post(this.urlBase, formData, config)
                    .then(response => {
                        console.log(response)
                        this.transacaoStatus = 'adicionado'
                    })
                    .catch(errors => {
                        console.log(errors)
                        this.transacaoStatus = 'erro'
                        this.transacaoDetalhes = errors.response
                    })
            }
        }
    }
</script>
