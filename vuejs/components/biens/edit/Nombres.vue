<template>
  <v-row no-gutter>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="nbPers" label="Persone(s) max" required></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="nbC" label="Chambre(s)" required></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="nbL2" label="Lit(s) 2 personnes"></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="nbL1" label="Lit(s) 1 personne"></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="nbC2" label="Convertible(s) 2 personnes"></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="nbC1" label="Convertible(s) 1 personne"></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="nbD" label="Douche(s)"></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="nbB" label="Baignoire(s)"></v-text-field>
    </v-col>
    <v-col cols="12" xs="12" sm="6" md="3" lg="2">
      <v-text-field v-model="nbWC" label="WC"></v-text-field>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "nombres",
  mixins: [],
  components: {},

  data: () => ({}),

  props: [],

  computed: {
    ...mapGetters(["editEc"]),
    nbPers: {
      get() {
        return this.editEc.nombrePersonnes;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "nombrePersonnes",
          value: value,
        });
      }
    },
    nbC: {
      get() {
        return this.editEc.nombreChambres;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "nombreChambres",
          value: value,
        });
      }
    },
    nbL2: {
      get() {
        return this.editEc.lit2P;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "lit2P",
          value: value,
        });
      }
    },
    nbL1: {
      get() {
        return this.editEc.lit1P;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "lit1P",
          value: value,
        });
      }
    },
    nbC2: {
      get() {
        return this.editEc.convert2P;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "convert2P",
          value: value,
        });
      }
    },
    nbC1: {
      get() {
        return this.editEc.convert1P;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "convert1P",
          value: value,
        });
      }
    },
    nbD: {
      get() {
        return this.editEc.douche;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "douche",
          value: value,
        });
      }
    },
    nbB: {
      get() {
        return this.editEc.baignoire;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "baignoire",
          value: value,
        });
      }
    },
    nbWC: {
      get() {
        return this.editEc.wc;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "wc",
          value: value,
        });
      }
    },
  },

  methods: {
    ...mapActions(["setEditEcProperty"]),
  },

  created() {},

  mounted() {},

  watch: {},
};
</script>