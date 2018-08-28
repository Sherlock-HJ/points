<template>
    <div class="orglist">
        <Row>
            <Button type="primary" :loading="loading" @click="createOrg">
                <span v-if="!loading">创建组织</span>
                <span v-else>创建组织中...</span>
            </Button>

        </Row>
        <Row>
            <Table :columns="columns1" :data="data1" @on-row-click="selectedRow"></Table>

        </Row>
        <Row>
            <Page :total="total" :page-size="count" @on-change="loadData"/>

        </Row>



    </div>
    
</template>

<script>
  export default {
    name: "orglist",
    data() {
      return {
        columns1: [
          {
            title: 'id',
            key: 'id'
          },
          {
            title: 'name',
            key: 'name'
          },
          {
            title: 'org_id',
            key: 'org_id'
          },{
            title: 'org_secrt',
            key: 'org_secrt'
          }
        ],
        data1: [],
        total: 0,
        count: 10,
        loading: false
      }
    },
    methods: {
      loadData(page){
        let params = {}
        params.page = page
        params.count = this.count

        this.$net.get('/v1/org/olist',{params:params}).then(data=>{

          this.data1 = data.list
          this.total = parseInt(data.total)
        })
      },
      createOrg(){
        this.loading = true
       let name=prompt("创建组织","请在这里输入组织名称")
        this.$net.get('/v1/org/add',{params:{name:name}}).then(data=>{
          this.loading = false

          if (data.ok) {
            this.$Message.success(data.msg)
            this.loadData(1)
          }else {
            this.$Message.warning(data.msg)

          }

        })
      },

      selectedRow(obj) {
        this.$router.push({path:'/cionlist',query:obj})

      }

    },
    mounted() {
      this.loadData(1)
    }
  }
</script>

<style scoped>
    .orglist {
        margin: 50px;
    }
    .ivu-row{
        margin-bottom: 15px;
    }
</style>