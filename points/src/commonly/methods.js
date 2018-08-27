/* eslint-disable indent */
export default {

  /*
   * @date 是 年月日 eg:1994-1-23
   * @return 数字年龄
   * */
  ageForYear(dateStr) {
    let birthdayTime = Date.parse(dateStr)
    let ageTime = new Date().getTime() - birthdayTime
    return Math.floor(ageTime / 1000 / 60 / 60 / 24 / 365)
  }
  ,
  isPC() {
    let userAgentInfo = navigator.userAgent
    let Agents = ['Android', 'iPhone', 'SymbianOS', 'Windows Phone', 'iPad', 'iPod']
    let flag = true
    for (let num = 0; num < Agents.length; num++) {
      if (userAgentInfo.indexOf(Agents[num]) > 0) {
        flag = false
        break
      }
    }
    return flag
  }
  ,
  isWeiXin() {
    let ua = navigator.userAgent.toLowerCase()
    return ua.indexOf('micromessenger') !== -1
  }
  ,
  is768() {
    return window.screen.width < 768
  }
  ,
  isValueNumber(value) {
    return (/(^-?[0-9]+\.{1}\d+$)|(^-?[1-9][0-9]*$)|(^-?0{1}$)/).test(value + '')
  }
  ,
  isiOS() {
    return /(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)
  }
  ,
  getQueryletiable(letiable) {
    let query = window.location.search.substring(1)
    let lets = query.split("&")
    for (let i = 0; i < lets.length; i++) {
      let pair = lets[i].split("=")
      if (pair[0] == letiable) {
        return pair[1]
      }
    }
    return (false)
  }
  ,
  pageForTotal(total, pageSize) {
    let page = total / pageSize + 1
    if (total % pageSize !== 0) page += 1
    return page
  }
  ,


  timeFormat(seconds) {

    let date = new Date(seconds * 1000)

    return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()} ${date.getHours()}:${date.getMinutes()}`
  }


}
