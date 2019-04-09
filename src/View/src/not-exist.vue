<style>
.page-404 {
  height : 100%;
  height : 100vh;
  background-color : #FFFFFF;
  color : #2C6EB1;
  text-align : center;
  box-sizing : border-box;
  padding-top : 150px;
}

img.image-404 {
  max-width : 600px;
  display : block;
  margin : auto;
}

</style>
<template>
  <div class="page-404">
    <!--<h1>404 页面不存在</h1>-->
    <div v-link.back="1">
      <a>返回上一个页面</a>
      <img src="./src/images/404.png" class="image-404">
    </div>
  </div>
</template>
<script>
Spa.define({
  data: function() {
    return {};
  },
  created: function() {
    this.$root.loading = false;
  },
  methods: {
    back: function() {
      history.go(-2);
    }
  }
});
</script>
