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
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID" v-model="busca.id">
                                </input-container-component>
                            </div>
                            <div class="col mb-3">
                                <input-container-component titulo="Nome" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional, informe o nome da marca">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="idNome" placeholder="Nome da Marca" v-model="busca.nome">
                                </input-container-component>
                            </div>
                        </div>
                    </template>
                    
                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right" @click="pesquisar()">Pesquisar</button>
                    </template>

                </card-component>

                <!-- Fim do card de pesquisa de marcas -->

                <!-- Card de listagem de marcas -->
                <card-component titulo="Listagem de Marcas">
                    <template v-slot:conteudo>
                        <table-component 
                            :dados="marcas.data"
                            :visualizar="{
                                visivel: true,
                                dataToggle: 'modal',
                                dataTarget: '#modalMarcaVisualizar'
                            }"
                            :atualizar="true"
                            :remover="{
                                visivel: true,
                                dataToggle: 'modal',
                                dataTarget: '#modalMarcaRemover'
                            }"
                            :titulos="{
                                id: {titulo: 'ID', tipo: 'texto'},
                                nome: {titulo: 'Nome', tipo: 'texto'},
                                imagem: {titulo: 'Imagem', tipo: 'imagem'},
                                created_at: {titulo: 'Criação', tipo: 'data'}
                            }"
                        >
                        </table-component> 
                    </template>

                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l,key in marcas.links" :key="key" 
                                        :class="l.active ? 'page-item active' : 'page-item'" 
                                        @click="paginacao(l)"
                                    >
                                        <a class="page-link" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </template>
                </card-component>

                <!-- Fim do card de listagem de marcas -->

            </div>
        </div>

        <!-- Início do modal de adicionar marca -->
        <modal-component id="modalMarca" titulo="Adicionar Marca">

            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso" v-if="transacaoStatus == 'adicionado'">
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
        <!-- Fim do modal de adicionar marca -->

        <!-- Início do modal de visualizar marca -->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar Marca">

            <template v-slot:alertas></template>

            <template v-slot:conteudo>
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>

                <input-container-component titulo="Imagem">
                    <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem">
                </input-container-component>

                <input-container-component titulo="Data de criação">
                    <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
                </input-container-component>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </template>

        </modal-component>
        <!-- Fim do modal de visualizar marca -->

        <!-- Início do modal de remoção de marca -->
        <modal-component id="modalMarcaRemover" titulo="Remover Marca">

            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Exclusão realizada com sucesso" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'">
                </alert-component>
                <alert-component tipo="danger" titulo="Erro ao excluir" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'">
                </alert-component>
            </template>

            <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" @click="remover()" v-if="$store.state.transacao.status != 'sucesso'">Remover</button>
            </template>

        </modal-component>
        <!-- Fim do modal de remoção de marca -->

    </div>
</template>

<script>
import InputContainer from './InputContainer.vue'
    export default {
  components: { InputContainer },
        data(){
            return {
                urlBase: 'http://localhost:8000/api/v1/marca',
                urlPaginacao: '',
                urlFiltro: '',
                nomeMarca: '',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: {},
                marcas: { 
                    data: []
                },
                busca: {
                    id: '',
                    nome: ''
                }
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
            remover(){
                let confirmacao = confirm('O registro será apagado, tem certeza?')

                if(!confirmacao){
                    return false;
                }

                let url = this.urlBase + '/' + this.$store.state.item.id

                let formData = new FormData()
                formData.append('_method','delete')

                let config = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }

                axios.post(url, formData, config)
                    .then(response => {
                        console.log('O registro foi excluído com sucesso', response)
                        this.$store.state.transacao.status = 'sucesso'
                        this.$store.state.transacao.mensagem = response.data.msg
                        this.carregarLista()
                    })
                    .catch(errors => {
                        console.log('Ocorreu algum erro ao excluir o registro', errors.response)
                        this.$store.state.transacao.status = 'erro'
                        this.$store.state.transacao.mensagem = errors.response.data.erro
                    })

            },
            pesquisar(){

                let filtro = ''

                for (let chave in this.busca) {
                    if(this.busca[chave]){
                        if(filtro != ''){
                            filtro += ';'
                        }
                        filtro += chave + ':like:' + this.busca[chave]
                    }
                }

                if(filtro != ''){
                    this.urlFiltro = '&filtro=' + filtro
                    this.urlPaginacao = 'page=1'
                }else{
                    this.urlFiltro = ''
                }   
            
                this.carregarLista();

            },
            paginacao(l){
                if(l.url){
                    this.urlPaginacao = l.url.split('?')[1]
                    this.carregarLista()
                }
            },
            carregarLista(){

                let config = {
                    headers:{
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }

                let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro

                axios.get(url, config)
                    .then(response => {
                        this.marcas = response.data
                        //console.log(this.marcas)
                    })
                    .catch(errors => {
                        console.log(errors)
                    })
            },
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
                        this.transacaoDetalhes = {
                            mensagem: "ID da marca: " + response.data.id
                        }
                    })
                    .catch(errors => {
                        console.log(errors)
                        this.transacaoStatus = 'erro'
                        this.transacaoDetalhes = {
                            mensagem: errors.response.data.message,
                            dados: errors.response.data.errors
                        }
                    })
            }
        },
        mounted(){
            this.carregarLista()
        }
    }
</script>
