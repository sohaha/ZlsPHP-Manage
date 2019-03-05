<style>
</style>
<template>
  <div class='page-1 panel'>
    <a v-link="'/main/main2'">首页</a>
  </div>
</template>
<script>
  Spa.define({
    data: function () {
      return {};
    },
    created: function () {

    },
    computed: {},
    init: function (query, search) {
      console.log(query, search);
    },
    methods: {}
  }, [], '/index');
</script>
