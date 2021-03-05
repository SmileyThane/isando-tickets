<template>

</template>

<style>

</style>

<script>

import { Bar } from 'vue-chartjs';

export default {
    extends: {
        Bar
    },
    props: ['chartdata', 'options'],
    data: () => ({
        chartdata: {
            labels: ['Январь', 'Февраль'],
            datasets: [
                {
                    label: 'Данные 1',
                    backgroundColor: '#f87979',
                    data: [40, 20]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    }),
    mounted() {
        this.renderChart(this.chartdata, this.options);
    }
}
</script>
