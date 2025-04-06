<template>
  <div>
    <MobileMenu />
    <div class="flex">
      <SideMenu />
      <div class="content">
        <TopBar :exchangeRateInfo="exchangeRateInfo"/>
        <router-view></router-view>
      </div>
    </div>
    <ToastContainer />
    <MessageToast />
    <ConfirmDialog />
    <portal-target name="destination"></portal-target>
  </div>
</template>

<script>

import SideMenu from './components/SideMenu.vue'
import MobileMenu from './components/MobileMenu.vue'
import TopBar from './components/TopBar.vue'
import MessageToast from './components/Toast/MessageToast.vue'
import ConfirmDialog from 'primevue/confirmdialog'
import eventBus from '../js/eventBus'

export default {
    components: {
        SideMenu,
        MobileMenu,
        TopBar,
        MessageToast,
        ConfirmDialog,
    },
    data() {
        return {
            exchangeRateInfo: {},
        };
    },
    methods: {
        getLatestExchangeRate() {
          /*console.log('Fetching latest exchange rate...');*/

            axios.get("/exchange-rate-service/get-latest").then((response) => {
              /*console.log('Got latest exchange rate:', response.data);*/
                this.exchangeRateInfo = response.data
            }).catch(err => {
              console.error('Error fetching exchange rate:', err);
            });
        },
    },
    mounted() {
        this.getLatestExchangeRate();
        // When exchange rate is updated
        eventBus.$on('exchangeRateUpdated', () => {
            this.getLatestExchangeRate()
        });
    },

    beforeUnmount () {
        /**
         * To remove this event handler to call only once
         */
         eventBus.$off('exchangeRateUpdated')
    },
}
</script>
