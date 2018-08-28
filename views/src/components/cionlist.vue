<template>
    <div class="cionlist">
        <Row>
            <Button type="primary" @click="modal2 = true">创建币</Button>

        </Row>
        <Row>
            <Table :columns="columns1" :data="data1" ></Table>

        </Row>
        <Row>
            <Page :total="total" :page-size="count" @on-change="loadData"/>

        </Row>


        <Modal
                v-model="modal2"
                width="360"
                @on-cancel="cancel">
            <h2 slot="header" style="text-align:center">
                <Icon type="md-create" />
                <span>创建币</span>
            </h2>

            <Input v-model="bname" placeholder="请输入币名" style="margin-bottom: 10px"/>
            <Input v-model="bcode" placeholder="请输入币code"  />

            <div slot="footer">
                <Button type="primary" size="large" long :loading="modal_loading" @click="createCoin">创建</Button>
            </div>
        </Modal>
    </div>
    
</template>

<script>
  export default {
    name: "cionlist",
    data() {
      return {
        bname: '',
        bcode: '',
        modal2: false,
        modal_loading: false,
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
            title: 'code',
            key: 'code'
          }
        ],
        data1: [],
        total: 0,
        count: 10,
      }
    },
    methods: {
      loadData(page){
        let params = {}
        params.page = page
        params.count = this.count
        params.org_id = this.$route.query.org_id

        this.$net.get('/v1/org/coin_list',{params:params}).then(data=>{

          this.data1 = data.list
          this.total = parseInt(data.total)
        })
      },
      createCoin(){
        this.modal_loading = true
        let params = {}
        params.name = this.bname
        params.code = this.bcode
        params.org_id = this.$route.query.org_id

        this.$net.get('/v1/org/add_coin',{params:params}).then(data=>{
          this.modal2 = false
          this.modal_loading = false
          if (data.ok) {
            this.$Message.success(data.msg)
            this.loadData(1)
          }else {
            this.$Message.warning(data.msg)
          }
        })

      },
      cancel() {
        this.bname = ''
        this.bcode = ''
      }
    },
    mounted() {
      this.loadData(1)
    }
  }
</script>

<style scoped>

    .cionlist {
        margin: 50px;
    }
    .ivu-row{
        margin-bottom: 15px;
    }
</style>