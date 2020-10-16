<template>
  <el-select v-model="value" :placeholder="placeholder" size="small" @change="setData(value)" :disabled="isDisabled">
    <el-option
      size="small"
      v-for="item in options"
      :key="item.value"
      :label="item.label"
      :value="item.value">
    </el-option>
  </el-select>
</template>

<script>
Spa.define({
  data:function () {
    return{
      options:[],
      value: '',
      placeholder:'请选择',
      isDisabled:false,
    };
  },
  methods:{
    setData:function (value) {
      var that = this;
      var data = that.options;
      for(var i = 0;i < data.length;i++){
        if(data[i].value === value){
          that.$emit('change',value,data[i]);
          return;
        }
      }

    }
  },
});

</script>

<style scoped>

</style>
