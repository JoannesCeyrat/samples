<template>
  <v-card class="mx-auto">
    <v-col cols="12" :style="{
      'height': '4px',
      'background-color': infosBien.color,
      'padding': 0,
      'margin': 0
      }"></v-col>
    <v-img class="white--text align-end" height="200px" :src="B.vignette">
      <v-card-title class="textShadowed">
        <!-- CDC -->
          <v-img v-if="B.cdc == 1" class="cdc" src="https://www.innov-home.fr/pics/coeur.png"></v-img>
        <!-- titre bien -->
        {{ B.titre }}
      </v-card-title>
    </v-img>

    <v-card-subtitle class="pb-0 gras">
      <span class="rose">Ref {{ B.ref }}</span>
      <span class="margeLeft bleu">{{ B.contrat }}</span>
     
      <span>
        <v-icon class="margeLeft" :style="{color: infosBien.color}">{{ infosBien.icon }}</v-icon>
        <span :style="{color: infosBien.color}">{{ infosBien.text }}</span>
      </span>
    </v-card-subtitle>

    <v-card-text class="text--primary">
      <div>{{ B.adresse }}</div>
      <div>Gestionnaire : {{ B.gestionnaire }}</div>
      <div>Propriétaire : {{ B.proprio }}</div>
    </v-card-text>

    <v-card flat>
      <v-row class="margeBottom">
          <v-col>
            <div class="text-center">
                    <v-menu 
                      offset-y
                      top
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                         class="mx-2" fab dark small
                          color="#e2007a"
                          v-bind="attrs"
                          v-on="on"
                        >
                          <v-icon>mdi-dots-horizontal</v-icon>
                        </v-btn>
                      </template>
                      <v-list>
                        <v-list-item
                          v-for="(item, index) in actionsBien"
                          :key="B.ref+'_action_'+index"
                          @click="openChoix(item.value)"
                          dense
                        >
                          <v-icon dense left class="rose">{{ item.icone }}</v-icon><v-list-item-title>{{ item.text }}</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
            </div>  
        </v-col>
      </v-row>
    </v-card>
  </v-card>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "BienVignette",
  props: ["B"],
  data: () => ({
    actionsBien: [
        { text: 'Modifier', value:'EditBien', icone: 'mdi-home-edit' },
        { text: 'Calendrier', value:'CalenBien', icone: 'mdi-calendar' },
        { text: 'Supprimer', value:'DeleteBien', icone: 'mdi-home-remove' },
    ],
    menu: {
        top: true,
    },
    choix: "",
    action_open:false,
  }),
  computed: {
    ...mapGetters(["infosValide"]),
    infosBien() {
      const regAppel = /appel/i;
      if (regAppel.test(this.B.contrat)) {
        return this.infosValide["AP"]
      }
      return this.infosValide[this.B.valide]
    },
    cdc() {
      return false
    }
    
  },
  methods: {
    ...mapActions(["setIndexPanelEditBien"]),
    openChoix(val) {
      this.setIndexPanelEditBien(undefined)
      this.$parent.$emit('openDialog', {component: val, B: this.B})
    }
  },
  watch: { }
};
</script>
