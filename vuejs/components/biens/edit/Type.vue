<template>
  <v-row no-gutter>
    <v-col cols="12">
      <v-select
        :items="typesBiens"
        label="Type de bien"
        item-value="value"
        item-text="libelle"
        v-model="sel"
      ></v-select>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "type",
  mixins: [],
  components: {},

  data: () => ({}),

  props: [],

  computed: {
    ...mapGetters(["editEc", "typesBiens"]),
    sel: {
      get() {
        return this.editEc.typeBien_id;
      },
      set(value) {
        this.setEditEcProperty({
          prop: "typeBien_id",
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