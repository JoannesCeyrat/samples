<template>
  <v-row no-gutter>
    <v-col 
        cols="12" sm="6" md="4" lg="3" 
        v-for="cat in equipements"
        :key="'cat_'+cat.id"
    >
        <v-card  elevation="1" outlined>
            <v-card-title>{{ cat.name }}</v-card-title>
            <v-card-text>
                <v-list-item-group
                    v-model="equips"
                    multiple
                >
                    <v-list-item
                        v-for="E in cat.equipements"
                        :key="'e_'+E.id"
                        :value="E.id"
                    >
                        <template v-slot:default="{ active }">
                            <v-list-item-content>
                                <v-list-item-title v-text="E.name"></v-list-item-title>
                            </v-list-item-content>

                            <v-list-item-action>
                                <v-checkbox
                                    :input-value="active"
                                ></v-checkbox>
                            </v-list-item-action>
                        </template>
                    </v-list-item>
                </v-list-item-group>
            </v-card-text>
        </v-card>
    </v-col>
    
  </v-row>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

/** https://vuetifyjs.com/en/components/list-item-groups/ */

export default {
  name: "equipements",
  mixins: [],
  components: { },

  data: () => ({}),

  props: [ ],

  computed: {
    ...mapGetters(["editEc", "equipements"]),
    equips: {
      get() {
            let a= []
            for (let id of this.editEc.equipements) {
                a.push(parseInt(id))
            }
            return (a);
      },
      set(value) {
        this.setEditEcProperty({
          prop: "equipements",
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