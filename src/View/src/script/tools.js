console.warn('load: tools.js');
window['arrayAdd'] = function (arr, v) {
  var i = arr.indexOf(v);
  if (i < 0) {
    arr.push(v);
  }
};
window['arrayReduce'] = function (arr, v) {
  var i = arr.indexOf(v);
  if (i >= 0) {
    arr.splice(i, 1);
  }
};
window['scrollSmoothTo'] = function (el, position) {
  if (!window.requestAnimationFrame) {
    window.requestAnimationFrame = function (callback, element) {
      return setTimeout(callback, 17);
    };
  }
  var scrollTop = el.scrollTop;
  var step = function () {
    var distance = position - scrollTop;
    scrollTop = scrollTop + distance / 5;
    if (Math.abs(distance) < 1) {
      el.scrollTo(0, position);
    } else {
      el.scrollTo(0, scrollTop);
      requestAnimationFrame(step);
    }
  };
  step();
};