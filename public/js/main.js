/*
Modernizr.load({
  test: Modernizr.inputtypes.date,
  yep : 'date.js',
  nope: 'date-polyfill.js'
});
*/

//call polyfill before DOM-Ready to implement everything as soon and as fast as possible
$.webshims.polyfill('forms forms-ext json-storage');

// the following function is the reduced custom version of the below

/* ============================================================
 * bootstrap-button.js v2.2.1
 * http://twitter.github.com/bootstrap/javascript.html#buttons
 * ============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */

!function ($) {
  "use strict"; // jshint ;_;
  var fuButton = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.fubutton.defaults, options)
  }
  fuButton.prototype.setState = function (state) {
    var d = 'disabled'
      , $el = this.$element
      , data = $el.data()
      , val = $el.is('input') ? 'val' : 'html'
    state = state + 'Text'
    data.resetText || $el.data('resetText', $el[val]())
    $el[val](data[state] || this.options[state])

    // push to event loop to allow forms to submit
    setTimeout(function () {
      state == 'loadingText' ?
        $el.addClass(d).attr(d, d) :
        $el.removeClass(d).removeAttr(d)
    }, 0)
  }
  
  $.fn.fubutton = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('fubutton')
        , options = typeof option == 'object' && option
      if (!data) $this.data('fubutton', (data = new fuButton(this, options)))
      if (option) data.setState(option)
    })  }
  $.fn.fubutton.defaults = { loadingText: 'Wait ....' }
  $.fn.fubutton.Constructor = fuButton
}(window.jQuery);