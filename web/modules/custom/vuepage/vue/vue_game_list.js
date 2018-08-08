/**
 *
 label: function(t, d) {
   return d.datasets[t.datasetIndex].label +
     ': (Day:' + t.xLabel + ', Total:' + t.yLabel + ')';
 }
 */
var default_ave_win  = 2.26
var default_ave_loss = null
var default_ave_draw = null

var default_diff_win  = 0.05
var default_diff_loss = null
var default_diff_draw = null

var default_tags = ['欧冠']

/**
 * @to vue-chartjs v3 to draw line chart
 */
Vue.component('line-chart', {
  extends: VueChartJs.Bubble,
  data () {
    return {
      chartDataSetSource: [    // dataset sample format
        {
          label: 'Data One',
          backgroundColor: '#f87979',
          data: [
            {
              x: 3.20,
              y: 1.72,
              r: 10
            },
            {
              x: 2.20,
              y: 2.12,
              r: 10
            }
          ]
        },
        {
          label: 'Data two',
          backgroundColor: '#7c89fb',
          data: [
            {
              x: 1.62,
              y: 2.50,
              r: 8
            }
          ]
        }
      ],
      options: {
        tooltips: {
          enabled: true,
          callbacks: {
            label:function (tooltipItems, data) {
              console.log(data)
              return tooltipItems.yLabel + '£'
            }
          }
        }
      }
    }
  },
  mounted () {
    axios.get(
      'http://localhost:8888/5wan/web/dashpage/game/chart/json',
      {
        params: {
          ave_win:  default_ave_win,
          ave_draw: default_ave_draw,
          ave_loss: default_ave_loss,
          diff_win:  default_diff_win,
          diff_draw: default_diff_draw,
          diff_loss: default_diff_loss,
          tags: default_tags,
        }
      }
    )
    .then(
      response => {
        console.log(response.request.responseURL)

        this.options = {
          tooltips: {
            callbacks: {
              label: function(tooltipItems, data) {
                return tooltipItems.yLabel + ' rmb';
              }
            }
          }
        }

        // JSON responses are automatically parsed.
        this.chartDataSetSource = response.data.chartDataSetSource

        this.renderChart({
          datasets: this.chartDataSetSource,
          options: this.options
        }, {responsive: true, maintainAspectRatio: false})
      }
    )
  }
})

/**
 *
 */
var vm = new Vue({
  el: '.appchartjs',
  data: {
    message: 'Chart title - Hello World'
  }
})

/** - - - grid table - - - - - - - - - - - - - */
/**
 * for game list table
 */
Vue.component('game-list-grid-tag', {
  template: '#grid-template',
  props: {
    data: Array,
    columns: Array,
    filterKey: String
  },
  data: function () {
    var sortOrders = {}
    this.columns.forEach(function (key) {
      sortOrders[key] = 1
    })
    return {
      sortKey: '',
      sortOrders: sortOrders,
      filteredTotal: 0,
    }
  },
  computed: {
    filteredData: function () {
      var sortKey = this.sortKey
      var filterKey = this.filterKey && this.filterKey.toLowerCase()
      var order = this.sortOrders[sortKey] || 1
      var data = this.data
      if (filterKey) {
        data = data.filter(function (row) {
          return Object.keys(row).some(function (key) {
            return String(row[key]).toLowerCase().indexOf(filterKey) > -1
          })
        })

        this.filteredTotal = data.length
      }
      if (sortKey) {
        data = data.slice().sort(function (a, b) {
          a = a[sortKey]
          b = b[sortKey]
          return (a === b ? 0 : a > b ? 1 : -1) * order
        })
      }
      return data
    }
  },
  filters: {
    capitalize: function (str) {
      return str.charAt(0).toUpperCase() + str.slice(1)
    }
  },
  methods: {
    sortBy: function (key) {
      this.sortKey = key
      this.sortOrders[key] = this.sortOrders[key] * -1
    }
  }
})

/**
 * bootstrap the demo data
 *
  var demo = new Vue({
    el: '#demo',
    data: {
      searchQuery: '',
      gridColumns: ['name', 'power'],
      gridData: [
        { name: 'Chuck Norris', power: Infinity },
        { name: 'Bruce Lee', power: 9000 },
        { name: 'Jackie Chan', power: 7000 },
        { name: 'Jet Li', power: 8000 }
      ]
    }
  })
 */

// the demo
var demo = new Vue({
  el: '#game-list-grid-wrapper',
  data () {
    return {
      searchQuery: '',
      gridColumns: [
        'Date',
        'Tags',
        'Home',
        'Away',
        'Win',
        'Draw',
        'Loss',
        'GoalH',
        'GoalA',
        'Num',
        'Result'
      ],
      gridData: [
        { name: 'Samle tbody', power: 7000 },
        { name: 'Jet Li', power: 8000 }
      ],
      totalRow: 0
    }
  },
  mounted () {
    axios.get(
      'http://localhost:8888/5wan/web/dashpage/game/list/json',
      {
        params: {
          ave_win:  default_ave_win,
          ave_draw: default_ave_draw,
          ave_loss: default_ave_loss,
          diff_win:  default_diff_win,
          diff_draw: default_diff_draw,
          diff_loss: default_diff_loss,
          tags: default_tags,
        }
      }
    )
    .then(
      response => {
        // JSON responses are automatically parsed.
        this.gridColumns = response.data.gridColumns,
        this.gridData = response.data.gridData,
        this.totalRow = this.gridData.length
      }
    )
  }
})
