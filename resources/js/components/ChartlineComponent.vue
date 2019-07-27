<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="chartjs">
                    <line-chart
                            :chart-data="dataForChartJs"
                            :height="100"
                            :options="{
                            title: {
                                display: true,
                                fontSize: 14,
                                text: 'Статистика по проекту HelpDesk (по полю \'Виконевець\', 25.06 - 25.07)'
                            },
                            responsive: true,
                            maintainAspectRation: true,
                            tooltips: {
                                mode: 'label',
                                callbacks: {
                                    label: function(tooltipItem, data) {
                                        return data.datasets[tooltipItem.datasetIndex].label + ': ' + numberWithCommas(tooltipItem.yLabel);
                                    }
                                }
                            },
                            scales: {
                                xAxes: [{
                                    stacked: true,
                                    gridLines: { display: false },
                                }],
                                yAxes: [{
                                    stacked: true,
                                    ticks: {
                                        callback: function(value) { return numberWithCommas(value); },
                                    },
                                }],
                            }
                        }">
                    </line-chart>
                    <b-table
                            id="table-transition-example"
                            :items="itemsForTable"
                            :fields="fieldsForTable"
                            striped
                            small
                            :primary-key="firstCell"
                            :tbody-transition-props="transProps"
                    ></b-table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import LineChart from './LineChart'
    export default {
        props: ['data'],
        components: {
            LineChart
        },

        data: function () {
            return {
                firstCell: 'Прізвище',
                dataForChartJs: this.data,
                fieldsForTable: [],
                itemsForTable: [],
                transProps: {
                    // Transition name
                    name: 'flip-list'
                }
            };
        },
        computed: {
            channel() {
                return window.Echo.private('test');
            }
        },

        mounted() {
            // console.log(this.test);
            this.convertDataToTable(this.data);
            // console.log(this.data);
            var app = this;
            this.channel
                .listen('ChartjsEvent', (response) => {
                    app.dataForChartJs = response.result;
                    this.convertDataToTable(response.result);

                    console.log(response);
                });

            // this.update()
        },
        methods: {
            update: function () {
                axios.get('/start/data-chart ').then((response) => {
                    this.dataForChartJs = response.data;
                    console.log(this.dataForChartJs);
                    this.convertDataToTable(response.data);
                });
            },

            numberWithCommas: function (x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },

            convertDataToTable: function (data) {
                // console.log(data);
                this.fieldsForTable = [
                    {key: this.firstCell, sortable: true}
                ];

                // this.fieldsForTable = [
                //     { key: 'aa', sortable: true },
                //     { key: 'bb', sortable: true },
                // ];

                var app = this;
                data.datasets.forEach(function (element) {
                    app.fieldsForTable.push({key: element.label, sortable: true});
                });

                var key1 = this.firstCell;
                data.labels.forEach(function (element, key) {
                    var obj = {};
                    obj[key1] = element;
                    data.datasets.forEach(function (subElement) {
                        obj[subElement.label] = subElement.data[key];
                    });

                    app.itemsForTable.push(obj);
                });

                // console.log(this.itemsForTable);
                // this.itemsForTable = [
                //     { Призвище: Фомина, Тип1: 100, Тип2: 200, Тип3: 300 e: 'выа', f: 'asdf' },
                //     { aa: 1, bb: 'Three', c: 'Dog', d: 'ыва', e: 'выа', f: 'asdf' },
            }
        }
    }
</script>