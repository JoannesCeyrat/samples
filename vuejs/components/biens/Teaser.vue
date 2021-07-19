<template>
    <div class="contTeaser hand" 
        style="width: 21rem; float: left; margin-right: 1.5rem" :id="bien['id']" 
        title="Choisir ce logement"
        @click="cmoi"
    >
        <div class="rose gras small">ref : {{ bien['id']}}</div>                                 
        <div class="contImgTeaser">
            <div class="imgTeaserCont">
                <img 
                    :src="url_base +'/images-meubles/vignettes/'+ bien['image']" 
                    class="imgTeaser" 
                    :alt="'Location de meublé '+bien['titreFR']" />
            </div>
            <div class="infoPlusTeaser" >
                    <ul class="ulTeaser" v-html="bien['li']"></ul>
            </div>

            <div class="prixTeaserCont">
                <div class="la_semaine">semaine</div>
                <div class="prixTeaser_home">{{ Math.round(bien['loyer']) }} <span class="euro">&euro;</span></div>
                <div class="loyerPersBulle"><v-icon style="color:#f8b3cb">mdi-account</v-icon></div> 
            </div>
        </div>

        <!-- notes -->
        <div class=critGauche >
            <div class="critBar" :title="'Meublé confortable: '+bien['confort']+'/5'">
                <div class="critInt">confort</div><div class="critDraw"><div class="critTrait" :style="{width: (Math.ceil((bien['confort'] / 5)*100))+'px'}"></div></div>
            </div>
            <div class="critBar" :title="'Meublé calme: '+bien['calme']+'/5'">
                <div class="critInt">calme</div><div class="critDraw"><div class="critTrait" :style="{width: (Math.ceil((bien['calme'] / 5)*100))+'px'}"></div></div>
            </div>
            <div class="critBar" :title="'Accès loisirs et sorties: '+bien['sorties']+'/5'">
                <div class="critInt">loisirs et sorties</div><div class="critDraw"><div class="critTrait" :style="{width: (Math.ceil((bien['sorties'] / 5)*100))+'px'}"></div></div>
            </div>
            <div class="critBar" :title="'Parking: '+bien['parking']+'/5'">
                <div class="critInt">parking</div><div class="critDraw"><div class="critTrait" :style="{width: (Math.ceil((bien['parking'] / 5)*100))+'px'}"></div></div>
            </div>
        </div>
        
        <!-- loyers supplémentaire -->
        <div class="critPers">
            <!-- boucle des loyers -->
            <div v-for="i in [2,3,4,5]" :key="'prixb'+bien['id']+'p'+i">                        
                <div class="nbPers">
                    <div v-if="bien[('loyer'+i)] > 0">
                        <v-icon v-for="index in i" :key="'iconprixb'+bien['id']+'p'+i+'i'+index">mdi-account</v-icon>
                    </div>
                </div>
                <div class="prixDroit" >
                    <div v-if="bien[('loyer'+i)] > 0">
                        {{ Math.round(bien[('loyer'+i)]) }}<span class="euroSmallDroit"> &euro;/S</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    name: "teaser",
    mixins: [ ],
    components: { },

    data: () => ({
    
    }),

    props: [
        'bien'
    ],
  
    computed: {
        ...mapGetters([
            "url_base",
        ]),
    },

    methods: {
        cmoi() {
            this.$parent.$emit('bienchoisi', this.bien['id'])
        }
    },

    created() {

    },

    mounted() {

    },

    watch: {
       
    }
}
</script>

<style scoped>
    .euroSmallDroit {
        font-size: 0.7rem;
        letter-spacing: -0.05rem
    }
    .prixDroit {
        float: left;
        width: 44%;
        text-align: right;
        color:#484748;
        font-size: 0.9rem;
        color:#e2007a; 
        letter-spacing:-0.05rem;
    }
    .nbPers {
        float: left;
        width: 56%;
        text-align: right;
        color:#a8a8a8;

    }

    .contTeaser {
        margin-bottom: 25px;
        overflow: hidden;
        max-width: 34rem;
        
    }

    .imgTeaserCont {
        position: relative;
        top:0;
        left:0;
        width:100%;
        padding-top: 69%;
        outline: none;
        overflow: hidden;
    }

    .imgTeaser {
        position: absolute;
        top:0;
        left: 0;
    }

    .infoPlusTeaser {
        position: absolute;
        top:0;
        left: 0;
        background-color:RGBA(6, 6, 6, 0.58);
        width: 100%;
        height: 100%;
        opacity: 0;
        -webkit-transition: opacity 0.7s ease-in-out;
        -moz-transition: opacity 0.7s ease-in-out;
        -ms-transition: opacity 0.7s ease-in-out;
        -o-transition: opacity 0.7s ease-in-out;
        transition: opacity 0.7s ease-in-out;
        color:#fff;
        vertical-align: top;
        padding: 5%;
    }

    .infoPlusTeaser:hover {
        zoom: 1;
        filter: alpha(opacity=100);
        opacity: 1.0;
    }

    ul
    {
        list-style-type: none;
    }


    .ulTeaser{
        position: absolute;
        left: 0;
        top: 15%;
    }

    a.teaser, a.teaser:hover{
        color:#484748;
    }


    .contImgTeaser {
        position: relative;
        width: 100%;
        padding: 0;
        margin:0 0 5px 0;
        outline: none;
    }

    .prixTeaserCont, .prixTeaserContGris, .cdcCont {
        position: absolute;
        top:10%;
        right: 4%;
        width: 5.5rem;
        height: 5.5rem;
        
        background-color: #e2007a;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
        padding: 2%;
        border:solid 8px #484748;
        
    }

    .critPers {
        width: 33%;
        min-height: 82px;
        background-color: transparent;
        padding: 0 0.5rem 0 0;
        margin: 0;
        position: relative;
        border-left: 1px solid #484748;
        display: inline-block;
    }

    .prixTeaser, .prixTeaser_home, .prixTeaserV2{
        color:#fff;
        font-size: 1.4rem;
        letter-spacing: -0.15rem;
        position: absolute;
        top: 1.6rem;
        left: 0;
        width: 100%;
        text-align: center;
    }

    .prixTeaserV2 {
        text-shadow: 0 1px 1px #786777;
    }

    .prixTeaser_home {
        top: 1.15rem;
    }

    .la_semaine {
        color: #fff;
        font-size: 0.85rem;
        letter-spacing: -0.05rem;
        position: absolute;
        top: 0.35rem;
        left: 0;
        width: 100%;
        text-align: center;
    }

    .noir {
        color:#0a0a0a;
    }

    .loyerPersBulle, .ficheLoyerPers, .loyerPersBulleBlanc{
        color:#f8b3cb;
        font-size: 1.0rem;
        position: absolute;
        top: 2.8rem;
        left: 0;
        width: 100%;
        text-align: center;
    }

    .loyerPersBulleBlanc {
        color:#fff;
    }

    .v-icon.v-icon {
        font-size: 14px;
        margin: -2px -2px 4px -2px;
    }

    .critGauche {
		width:67%;
	}

    .critBar {
        width:95%;
        float: left;
        position: relative;
    }

    .critInt{
        float: left;
        width: 40%;
        text-align: left;
        letter-spacing: -0.04rem;
        font-size: 0.8rem;
    }

    .critDraw {
        float: left;
        width:50%;
        border:1px solid #F5CAD9;
        height: 6px;
        margin-top: 0.5rem;
    }

    .critTrait {
	height: 100%;
	background-color:#e2007a;
	width: 0;
	-webkit-transition: width 0.7s; /* Safari */
    transition: width 0.7s;
}

</style>