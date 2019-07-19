import { Bar, mixins } from 'vue-chartjs'
// import ChartDataLabels from 'chartjs-plugin-datalabels';

export default {
    extends: Bar,
    mixins: [mixins.reactiveProp],
    props: ['chartData', 'options'],
    mounted () {
        this.renderChart(this.chartData, this.options)
    }
}