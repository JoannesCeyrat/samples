<template>
  <v-row no-gutter>
    <v-col cols="12">
      <v-textarea
            label="Idéal pour ..."
            required
            v-model="ideal"
            height="350"
            full-width
          ></v-textarea>
    </v-col>
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";


export default {
  name: "ideal",
  mixins: [],
  components: { },

  data: () => ({}),

  props: [ ],

  computed: {
    ...mapGetters(["editEc"]),
    ideal: {
      get() {
        return (this.editEc.ideal)//.replace(regex, '<br>');
      },
      set(value) {
        this.setEditEcProperty({
          prop: "ideal",
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

  watch: {
    
  },
};
</script>