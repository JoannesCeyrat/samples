<template>
  <v-row no-gutter>
    <v-col  cols="8" xs="8" sm="9" md="10">
      Le bien a t'il subi un sinistre ayant donné lieu au versement d’une indemnité d’assurance garantissant 
      les risques de catastrophes naturelles visés à l’article L 1252 ou technologiques visés à l’article L 1282 du code des assurances ? 
    </v-col>
    <v-col  cols="4" xs="4"  sm="3" md="2" >
        <v-row justify="center">
            <v-switch v-model="switch1" :label="titre1"></v-switch>
        </v-row>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "sinistre",
  mixins: [],
  components: {},

  data: () => ({}),

  props: [],

  computed: {
    ...mapGetters(["editEc"]),
    switch1: {
      get() {
        return this.editEc.sinistre;
      },
      set(value) {
        let s= value ? 1 : 0
        this.setEditEcProperty({
          prop: "sinistre",
          value: s,
        });
      }
    },
    titre1() {
        return this.editEc.sinistre == 1 ? "Oui" : "Non"
    }
    
  },

  methods: {
    ...mapActions(["setEditEcProperty"]),
  },

  created() {},

  mounted() {},

  watch: {},
};
</script>