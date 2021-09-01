<template>
    <div>
        <v-card
            class="mb-4"
            elevation="2"
            outlined
        >
            <v-card-title>Report detail</v-card-title>
            <v-card-text>
                <report-tracker></report-tracker>
            </v-card-text>
        </v-card>

        <v-card
            class="mb-4"
            elevation="2"
            outlined
        >
            <v-card-title>Report reconciliation Tracker/Timesheet</v-card-title>
            <v-card-text>
                <report-reconciliation></report-reconciliation>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import ReportTracker from './components/ReportTracker';
import ReportTimesheet from './components/ReportTimesheet';
import ReportReconciliation from './components/ReportReconciliation';

export default {
    name: 'report-tab',
    components: {
        ReportTracker,
        ReportTimesheet,
        ReportReconciliation,
    },
    data () {
        return {
        };
    },
    mounted() {
        this.$store.dispatch('Clients/getClientList', { search: null });
        this.$store.dispatch('Team/getCoworkers', { search: null });
    }
}
</script>

<style scoped>

</style>
