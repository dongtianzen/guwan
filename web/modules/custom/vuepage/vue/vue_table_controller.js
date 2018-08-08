/**
 * @see https://codepen.io/ratiw/pen/GmJayw
 */

Vue.use(Vuetable);

new Vue({
  el: '#app',
  components: {
    'vuetable-pagination': Vuetable.VuetablePagination
  },
  data: {
    // Fields defined as array of object
    fields: [
      {
        name: 'name',
        title: '<span class="orange glyphicon glyphicon-user"></span> Full Name',
        sortField: 'name'
      },
      {
        name: 'email',
        title: 'Email',
        sortField: 'email'
      },
      'birthdate',
      'salary',
      'nickname',
      {
        name: 'gender',
        title: 'Gender',
        sortField: 'gender'
      }
    ],
    sortOrder: [
      {
        field: 'name',
        direction: 'asc'
      }
    ],
    css: {
      table: {
        tableClass: 'table table-striped table-bordered table-hovered',
        loadingClass: 'loading',
        ascendingIcon: 'glyphicon glyphicon-chevron-up',
        descendingIcon: 'glyphicon glyphicon-chevron-down',
        handleIcon: 'glyphicon glyphicon-menu-hamburger',
      },
      pagination: {
        infoClass: 'pull-left',
        wrapperClass: 'vuetable-pagination pull-right',
        activeClass: 'btn-primary',
        disabledClass: 'disabled',
        pageClass: 'btn btn-border',
        linkClass: 'btn btn-border',
        icons: {
          first: '',
          prev: '',
          next: '',
          last: '',
        },
      }
    }
  },
  computed:{
  /*httpOptions(){
    return {headers: {'Authorization': "my-token"}} //table props -> :http-options="httpOptions"
  },*/
 },
 methods: {
    onPaginationData (paginationData) {
      this.$refs.pagination.setPaginationData(paginationData)
    },
    onChangePage (page) {
      this.$refs.vuetable.changePage(page)
    },
    onLoading() {
      console.log('loading... show your spinner here')
    },
    onLoaded() {
      console.log('loaded! .. hide your spinner here')
    }
  }
})
