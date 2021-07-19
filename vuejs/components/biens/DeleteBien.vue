<template>
  <v-sheet min-height="100%">
    <v-toolbar dark color="#C9006B" :src="main_dialog.data.hero" tile extended>
      <v-toolbar-title class="textShadowed">
        <h3>Suppression bien {{ main_dialog.data.bid }}</h3>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-card 
              flat
              color="transparent"
            >
              <v-card-actions>
              <v-btn
                class="mx-2" fab  large color="#e2007a"
                @click="deleteBien"
                v-on="on"
                dark
                :loading="btn_traiter.is_sending"
              >
                <v-icon large>mdi-delete</v-icon>
              </v-btn>
              </v-card-actions>
            </v-card>
          </template>
          <span>Supprimer ce bien</span>
        </v-tooltip>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              icon
              dark
              @click="setMainDialog({cle: 'open', value: false})"
              v-on="on"
              class="textShadowed"
            >
              <v-icon size="30" style="padding-top: 1rem">mdi-close</v-icon>
            </v-btn>
          </template>
          <span>Fermer</span>
        </v-tooltip>
      </v-toolbar-items>
    </v-toolbar>

    <v-row no-gutters class="margeUp">
      <v-col xs="12" md="10" offset-md="1" xl="8" offset-xl="2">
        <div
          :style="{
                            height: hauteur_iframe,
                            overflow: 'scroll',
                            padding: '0.2rem 0.75rem'
                        }"
        >
          <v-card flat>
            <v-card-title>
              Cliquez sur le bouton <v-icon>mdi-delete</v-icon> ci-dessus pour supprimer le bien.
            </v-card-title>
            <v-card-subtitle><v-icon left :style="{color: couleurs.alert}">mdi-alert</v-icon>Attention, le bien sera supprimé dès le click...<br>
            Ne pas cliquer par accident donc !</v-card-subtitle>
          </v-card>
          

          <div style="height: 1rem"></div>
        </div>
      </v-col>
    </v-row>
  </v-sheet>
</template>

<script>
import { mapGetters, mapActions } from "vuex";



export default {
  name: "DeleteBien",
  components: { 
      
  },
  data: () => ({}),
  computed: {
    ...mapGetters([
        "main_dialog",
        "hauteur_iframe",
        "btn_traiter",
        "couleurs"
    ]),
  },
  props: [],
  methods: {
    ...mapActions([
        "setMainDialog",
        "deleteBien"
    ]),
    
  },
  watch: {},
  mounted() {},
  
};
</script>
