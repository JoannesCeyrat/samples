<template>

  
        <div 
            align="center"
            class="dropzone  col margeUp margeBottom" 
            drag-over="handleDragOver" 
            @dragover="hovering = true" 
            @dragleave="hovering = false" 
            :class="{'hovered': hovering}"
            style="padding-top: 1.2rem; font-size: 0.85rem; background-color: rgb(254, 252, 255);"
        >
          <span class="rose">
            Glissez ici vos {{ cible }} ou cliquez pour les sélectionner             
          </span>
          <input type="file" multiple @change="onFileChange">
      </div>
 
</template>


<script>
    import { mapMutations } from "vuex";
    export default {
        name: 'droparea',
        props: [
            'cible'
        ],
        data () {
            return {
                image: '',
                hovering:false
            }
        },
        computed: {
            
        },
        watch: {
            
        },
        methods: {
            onFileChange(e) {
                  var files = e.target.files || e.dataTransfer.files;
                  if (!files.length) return;

                  let l= []
                  for (let F of files) {
                      l.push(F)
                  }
                  
                  // verification pour les images
                  if (this.cible == "images") {
                    for (let i=0; i<l.length; i++) {
                        if (!l[i].type.match('image/jpeg')) {
                                this.$store.commit('set_snackbar', {
                                    open: true,
                                    color: "#e2007a",
                                    text: "l'image \""+l[i]["name"]+"\" n'est pas au format jpg",
                                    icon: 'mdi-alert'
                                })
                                l.splice(i, 1)
                        }
                    }
                  } else if (this.cible == "justificatifs") {
                       for (let i=0; i<l.length; i++) {
                        if (!l[i].type.match('image|pdf')) {
                                this.$store.commit('set_snackbar', {
                                    open: true,
                                    color: "#e2007a",
                                    text: "le jsutificatif \""+l[i]["name"]+"\" doit être une image ou un pdf",
                                    icon: 'mdi-alert'
                                })
                                l.splice(i, 1)
                        }
                    }
                  }
                  if (l.length > 0) {
                    this.hovering= false
                    this.$root.$emit( 'ajout_documents', {cible : this.cible, documents: l})
                  }
            },
            

        },
        created () {
            
            
            
        },
        mounted () { // intervient lorsque le component est appelé

            
        }
    }

</script>