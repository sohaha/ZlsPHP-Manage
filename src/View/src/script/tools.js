window.createUrl = function (newStr, domain) {
  console.log('createUrl:', newStr, domain);
  var dirArr = [];
  var type1 =
    domain +
    '/A' +
    randomString(2) +
    'C5' +
    randomString(2) +
    '/' +
    randomString(5) +
    '/' +
    randomString(6) +
    '?' +
    randomString(3) +
    '=' +
    randomString(3) +
    to64(newStr) +
    randomString(10);
  dirArr.push(type1);
  var type2 =
    domain +
    '/x' +
    randomString(3) +
    'P0' +
    randomString(2) +
    '/' +
    randomString(5) +
    '/' +
    randomString(6) +
    '?' +
    randomString(3) +
    '=' +
    randomString(3) +
    to64(newStr) +
    randomString(10);
  dirArr.push(type2);
  var type3 =
    domain +
    '/0' +
    randomString(3) +
    '0G' +
    randomString(2) +
    '/' +
    randomString(5) +
    '/' +
    randomString(6) +
    '?' +
    randomString(3) +
    '=' +
    randomString(3) +
    to64(newStr) +
    randomString(10);
  dirArr.push(type3);
  return dirArr[Math.floor(Math.random() * dirArr.length + 1) - 1];
};

function randomString(len) {
  var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz1234567890';
  var maxPos = $chars.length;
  var pwd = '';
  for (var i = 0; i < len; i++) {
    pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
  }
  return pwd;
}
function to64(e) {
  // window.btoa
  return e
}
