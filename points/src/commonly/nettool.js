import axios from 'axios'
const isdev = process.env.NODE_ENV === 'development'

function baseurl() {
  let xie = '/'
  let str = window.location.pathname
  let arr =  str.split(xie)
  arr.pop()
  arr.pop()
  return 'http://'+window.location.host+arr.join(xie)+xie
}
const baseURL = {
  apiPath: isdev?'http://localhost/points/m.php':baseurl()+'m.php'
}
const net = axios.create({
  baseURL: baseURL.apiPath
})

net.interceptors.response.use(res => {

  return res.data
})

export default net