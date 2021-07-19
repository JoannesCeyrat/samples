<template>
    <v-text-field
        v-model="sync" 
        label="Indiquez le lien fourni par la plateforme" 
        required
    ></v-text-field>
</template>

<script>

import { mapGetters, mapActions } from "vuex";

export default {
    name: "synchro",
    mixins: [ ],
    components: { },

    data: () => ({
    
    }),

    props: [
        'index'
    ],
  
    computed: {
        ...mapGetters(["editEc"]),
        S() {
            return this.editEc.synchro[this.index]
        },
        sync: {
            get() {
                return this.S.url
            },
            set(val) {
                this.setEditEcSynchro({
                    index: this.index,
                    value: val,
                });
            }
        }
    },

    methods: {
        ...mapActions(["setEditEcSynchro"]),
    },

    created() {

    },

    mounted() {

    },

    watch: {
       
    }
}
</script>