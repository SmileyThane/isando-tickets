<template>

</template>

<style>

</style>

<script>

import { Line } from 'vue-chartjs';

export default {
    extends: {
        Line
    },
    props: ['chartdata', 'options'],
    data() {
        return {

        };
    },
    mounted() {
        this.renderChart(this.chartdata, this.options);
    }
}
</script>
