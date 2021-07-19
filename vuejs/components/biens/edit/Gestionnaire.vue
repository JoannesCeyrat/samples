<template>
  <v-row no-gutter>
    <v-col cols="12">
      <v-select
        :items="gestionnaires"
        label="Conseiller gestionnaire"
        item-value="value"
        item-text="libelle"
        v-model="gest"
      ></v-select>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "gestionnaire",
  mixins: [],
  components: {},

  data: () => ({}),

  props: [],

  computed: {
    ...mapGetters(["editEc", "gestionnaires"]),
    gest: {
      get() {
        return this.editEc.gestionnaire_id
      },
      set(value) {
        this.setEditEcProperty({
          prop: "gestionnaire_id",
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