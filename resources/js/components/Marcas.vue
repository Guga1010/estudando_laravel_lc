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
                        <button type="button" class="btn btn-primary btn-sm float-right">Adicionar</button>
                    </template>
                </card-component>

                <!-- Fim do card de listagem de marcas -->

            </div>
        </div>
    </div>
</template>

<script>

</script>
