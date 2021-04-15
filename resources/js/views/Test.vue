<template>
    <div>
        <spin v-if="loading"></spin>
        <div v-else>

            <div class="uk-child-width-1-2@s" uk-grid>
                <div class="uk-animation-toggle" tabindex="0">
                    <div class="  uk-card-body  uk-animation-scale-up uk-transform-origin-top-center uk-box-shadow-hover-small uk-padding">
                        <div>
                            Время обращения: {{ varData }} {{ light }}
                        </div>
                        <table class="uk-table uk-table-small uk-table-divider uk-table-hover ">
                            <thead>
                            <tr >
                                <th style="text-align: center"></th>
                                <th style="text-align: center"><b>Баланс</b></th>
                                <th style="text-align: center"><b>Баланс USDT</b></th>
                                <th style="text-align: center"><b>Δ</b></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><b>USDT:</b></td>
                                <td>{{balances.USDT}}</td>
                                <td>{{balances.USDT}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>ETH:</b></td>
                                <td>{{balances.ETH}}</td>
                                <td>{{balances.ETHUSDT}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>BTC:</b></td>
                                <td>{{balances.BTC}}</td>
                                <td>{{balances.BTCUSDT}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>TRX:</b></td>
                                <td>{{balances.TRX}}</td>
                                <td>{{balances.TRXUSDT}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td><b></b></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Результат</td>
                                <td></td>
                                <td><b>{{res}}</b></td>
                                <td>{{limit_incr}} - {{max_incr}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="uk-animation-toggle" tabindex="0">
                    <div class="  uk-card-body uk-animation-fast uk-animation-fade">

                        <button-active></button-active>

                    </div>
                </div>

            </div>

            <div class="uk-child-width-1-2 uk-text-center uk-transform-origin-bottom-right" uk-grid>
                <div>

                </div>
                <div>

                </div>
            </div>

            <table class="uk-table uk-table-small uk-table-divider uk-table-hover">
                <thead style="font-weight: bold; text-align: center">
                <tr>
                    <td>Дата</td>
                    <td>TxId</td>
                    <td>Direct</td>
                    <td>Fee</td>
                    <td>Block</td>
                    <td>Trx 1</td>
                    <td>Trx 2</td>
                    <td>Результат</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="trade in trades">
                    <td>{{ trade.created_at }}</td>
                    <td><a :href="'https://tronscan.org/#/transaction/' + trade.txid"   target="_blank">{{ trade.txid  | truncate(20, ' . . .') }} >></a></td>
                    <td>{{ trade.direct }}</td>
                    <td>{{ trade.fee_trx }}</td>
                    <td>{{ trade.block_number }}</td>
                    <td>{{ trade.before_balance_trx }}</td>
                    <td>{{ trade.after_balance_trx }}</td>
                    <td>{{ trade.result }}</td>
                </tr>
                </tbody>
            </table>
        </div>


    </div>
</template>

<script>
import Spin from "../components/Spin";
import axios from 'axios';

import Pusher from 'pusher-js'



export default {

    configureWebpack: {
        plugins: [
            //new Dotenv()
        ]
    },
    components: {
        Spin
    },
    data: ()=>({
        loading: true,
        trades: [],
        balances: [],
        varData: '',
        res: '',
        light: '',
        max_incr: '',
        limit_incr: ''
    }),
    mounted() {

        // window.pusher.subscribe('chat-room.1')
        //     .bind('App\\Events\\ChatMessageWasReceived', function (data) {
        //         //alert(JSON.stringify(data));
        //         console.log(data.chatMessage.message);
        //     });

        //let api_url = process.env.PUSHER_APP_KEY;
        //console.log(api_url);

        this.subscribe();
        this.loadPosts();
        this.interval = setInterval(() => {
            this.loadPosts();
        }, 3000 );
    },
    methods:{
        subscribe () {
            let pusher = new Pusher('d8e5aa9062b273c29902', { cluster: 'eu' })
            pusher.subscribe('chat-room.1')
            pusher.bind('App\\Events\\TradeMessageWasReceived', data => {
                this.light = " ⭐⭐⭐⭐⭐";

                this.balances = data.tradeMessage.balances;
                this.varData = data.tradeMessage.data;
                this.res = data.tradeMessage.res;
                this.max_incr = data.tradeMessage.max_incr + '%';
                this.limit_incr = data.tradeMessage.limit_incr + '%';

                this.loadPosts();

                setTimeout(()=>{
                    this.light = '';
                }, 300);
            })
        },
        loadPosts(){
            axios.get('/api/trades')
            .then(res => {
                //console.log(res.data);
                this.trades = res.data;
                setTimeout(()=>{
                    this.loading = false;
                }, 500);
            })
        }

    }
}
</script>

<style scoped>

</style>
