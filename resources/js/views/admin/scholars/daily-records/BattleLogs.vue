<template>
    <div class="row d-flex align-items-center justify-content-center text-center h-100">
        <div class="col-md-12">
            <template v-if="loading">
                <i class="fas fa-circle-notch fa-spin fa-3x"></i>
            </template>
            <template v-else>
                <table class="table-responsive table table-dark text-center vh-75">
                    <thead>
                        <tr>
                        <th scope="col">Enemy Team</th>
                        <th scope="col">Result</th>
                        <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody v-if="battles.length">
                        <template v-for="battle in battles">
                            <tr :key="battle.id" class="align-middle">
                                <td>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <img  style="height: 60px" class="loading" @click="goToMarketPlace(battle, 0)" :src="getAxieImage(battle, 0)">
                                            <img  style="height: 60px" class="loading" @click="goToMarketPlace(battle, 1)" :src="getAxieImage(battle, 1)">
                                            <img  style="height: 60px" class="loading" @click="goToMarketPlace(battle, 2)" :src="getAxieImage(battle, 2)">
                                        </div>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-md-12">
                                            {{  parseDate(battle.created_at) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle" :class="getBattleResultColor(battle.first_client_id, battle.winner)">{{ getBattleResult(battle.first_client_id, battle.winner) }}</td>
                                <td class="align-middle">
                                    <a :href="'https://cdn.axieinfinity.com/game/deeplink.html?f=rpl&q=' + battle.battle_uuid" target="_blank"  class="btn btn-success">Watch Replay</a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
        </div>
    </div>
</template>

<script type="text/javascript">
import ListMixin from 'Mixins/list.js';

export default {
    data() {
        return {
            battles : [],
            loading : false,
        }
    },

    computed: {
        headers() {
            return [
                { text: 'Date', value: 'date', icon: 'others'},
                { text: 'Slp', value: 'slp', icon: 'number'},
            ];
        }
    },

    mounted() {
        this.fetch();
    },

    methods : {
        fetch() {
            this.loading = true;
            axios.get('/admin/analytics/battle-logs/' + this.roninAddress).then(response =>{
                this.battles = response.data.data;
                this.loading = false;
            }).catch(error =>{
                console.log(error);
            }).then(()=>{
                this.loading = false;
            })
        },

        goToMarketPlace(battle, count) {
            let adrress = this.roninAddress;
            let id = adrress == battle.first_client_id ? count + 3 : count;
            window.open('https://marketplace.axieinfinity.com/axie/' +  battle.fighters[id].fighter_id, '_blank').focus();
        },

        getAxieImage(battle, count) {
            let adrress = this.roninAddress;
            let id = adrress == battle.first_client_id ? count + 3 : count;
            return 'https://storage.googleapis.com/assets.axieinfinity.com/axies/'+ battle.fighters[id].fighter_id +'/axie/axie-full-transparent.png'
        },

        getBattleResult(id, result) {
            if(result == 2) {
                return 'Draw'
            }

            if(result == 0 && id == this.roninAddress) {
                return 'Win'
            }

            if(result == 1 && id != this.roninAddress) {
                return 'Win'
            }

           if(result == 0 && id != this.roninAddress) {
                return 'Lose'
            }

            if(result == 1 && id == this.roninAddress) {
                return 'Lose'
            }
        },


        getBattleResultColor(id, result) {
            if(result == 2) {
                return 'text-white'
            }
            if(result == 0 && id == this.roninAddress) {
                return 'text-success'
            }

            if(result == 1 && id != this.roninAddress) {
               return 'text-success'
            }

            if(result == 0 && id != this.roninAddress) {
                 return 'text-danger';
            }

            if(result == 1 && id == this.roninAddress) {
                 return 'text-danger';
            }
        },

        parseDate(date) {
            return moment(date).format('MM/DD/YYYY hh:mm A');
        }
    },

    props: {
        fetchUrl: String,
        roninAddress : String,
    },

    mixins: [ ListMixin ],

    components: {

    },
}
</script>
<style scoped>
    table tbody {
        display: block;
        max-height: 550px;
        overflow-y: scroll;
    }

    table thead, table tbody tr {
        display: table;
        width: 100%;
    }

    .table-responsive {
        border-collapse: separate;
        border-radius: 10px;
    }
</style>
