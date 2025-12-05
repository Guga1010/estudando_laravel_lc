<template>
    <div :class="estilo" role="alert">
        {{titulo}}
        <hr>
        {{ detalhes.data.message }}
        <br>
        <ul v-if="detalhes.data.errors">
            <li v-for="e,key in detalhes.data.errors" :key="key">{{ e[0] }}</li>
        </ul>
    </div>
</template>

<script>

export default {
    props: ['tipo', 'titulo', 'detalhes'],
    computed: {
        estilo(){
            return "alert alert-"+this.tipo
        }
    }
}

</script>
