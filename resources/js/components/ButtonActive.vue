<template xmlns="http://www.w3.org/1999/html">

    <table class="uk-table uk-table-small uk-animation-scale-up ">
        <tbody>
        <tr>
            <td colspan="2"> Процесс:
            </td>
        </tr>
        <tr>
            <td>
                <div class="uk-margin-small">
                    <div class="uk-button-group">
                        <button @click="toggleActive" class="uk-button uk-button-primary" :disabled="!isActive">Start</button>
                        <button @click="toggleInActive" class="uk-button uk-button-danger" :disabled="!isInActive">Stop</button>
                    </div>
                </div>
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">
            </td>
        </tr>
        <tr>
            <td colspan="2"> Swap:
            </td>
        </tr>
        <tr>
            <td>

                <div class="uk-margin-small">
                    <div class="uk-button-group">
                        <button @click="toggleSwap" class="uk-button uk-button-primary" :disabled="!isSwap">Start</button>
                        <button @click="toggleInSwap" class="uk-button uk-button-danger" :disabled="!isInSwap">Stop</button>
                    </div>
                </div>

            </td>
            <td></td>
        </tr>
        </tbody>
    </table>

</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            isActive: false,
            isInActive: false,
            isSwap: false,
            isInSwap: false,
        };
    },
    mounted() {
        this.index();
    },
    methods: {
        index() {
            axios.get('/api/active-list')
                .then(res => {
                    if(res.data.active == 1){
                        this.isActive = false;
                        this.isInActive = true;
                        if(res.data.started_swap == 1){
                            this.isSwap = false ;
                            this.isInSwap = true;
                        }
                        else{
                            this.isSwap = true ;
                            this.isInSwap = false ;
                        }

                    }
                    else{
                        this.isActive = true;
                        this.isInActive = false;
                    }
                })
        },
        toggleActive() {
            axios.get('/api/active')
                .then(res => {
                    if(res.data == 1){
                        this.isActive = false;
                        this.isInActive = true;
                        this.isSwap = true ;
                        this.isInSwap = false;
                    }
                })
        },
        toggleInActive() {
            axios.get('/api/in-active')
                .then(res => {
                    if(res.data == 0){
                        this.isActive = true;
                        this.isInActive = false;
                        this.isSwap = false;
                        this.isInSwap = false;
                    }
                })
        },
        toggleSwap() {
            axios.get('/api/swap-active')
                .then(res => {
                    if(res.data == 1){
                        this.isSwap = false ;
                        this.isInSwap = true;
                    }
                })
        },
        toggleInSwap() {
            axios.get('/api/swap-in-active')
                .then(res => {
                    if(res.data == 0){
                        this.isSwap = true ;
                        this.isInSwap = false ;
                    }
                })
        },
    }
}
</script>

<style scoped>

</style>
