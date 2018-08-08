/**
 *
 */
window.onload = function() {
  new Vue({
    el: '#appPage',
    data: {
      name: 'Sitepoint',
      message: 'Hello Vue!'
    },
    template: '<button >You clicked me times.</button>'
  })
}



var app = new Vue({
  el: '#vueAppPage',
  data: {
    message: 'Hello Vue!'
  }
})
