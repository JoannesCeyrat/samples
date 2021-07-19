<template>
  <v-row no-gutter>
    <v-col cols="12">
      <v-select
        :items="typesContrats"
        label="Type du contrat"
        item-value="value"
        item-text="libelle"
        v-model="cont"
      ></v-select>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "contrat",
  mixins: [],
  components: {},

  data: () => ({}),

  props: [],

  computed: {
    ...mapGetters(["editEc", "typesContrats"]),
    cont: {
      get() {
        return this.editEc.typeContrat_id;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "typeContrat_id",
          value: value,
        });
      },
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