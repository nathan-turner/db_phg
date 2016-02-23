jQuery.webshims.register("form-number-date-api",function(d,e){var j,p,u,h;if(!e.getStep)e.getStep=function(a,c){var b=d.attr(a,"step");if("any"===b)return b;c=c||l(a);if(!f[c]||!f[c].step)return b;b=j.asNumber(b);return(!isNaN(b)&&0<b?b:f[c].step)*f[c].stepScaleFactor};if(!e.addMinMaxNumberToCache)e.addMinMaxNumberToCache=function(a,c,b){a+"AsNumber"in b||(b[a+"AsNumber"]=f[b.type].asNumber(c.attr(a)),isNaN(b[a+"AsNumber"])&&a+"Default"in f[b.type]&&(b[a+"AsNumber"]=f[b.type][a+"Default"]))};var m=
parseInt("NaN",10),f=e.inputTypes,o=function(a){return"number"==typeof a||a&&a==1*a},n=function(a){return d('<input type="'+a+'" />').prop("type")===a},l=function(a){return(a.getAttribute("type")||"").toLowerCase()},i=e.addMinMaxNumberToCache,q=function(a,c){for(var a=""+a,c=c-a.length,b=0;b<c;b++)a="0"+a;return a},r=e.bugs.valueAsNumberSet||e.bugs.bustedValidity;e.addValidityRule("stepMismatch",function(a,c,b,d){if(""===c)return!1;if(!("type"in b))b.type=l(a[0]);if("date"==b.type)return!1;d=(d||
{}).stepMismatch;if(f[b.type]&&f[b.type].step){if(!("step"in b))b.step=e.getStep(a[0],b.type);if("any"==b.step)return!1;if(!("valueAsNumber"in b))b.valueAsNumber=f[b.type].asNumber(c);if(isNaN(b.valueAsNumber))return!1;i("min",a,b);a=b.minAsNumber;isNaN(a)&&(a=f[b.type].stepBase||0);d=Math.abs((b.valueAsNumber-a)%b.step);d=!(1.0E-7>=d||1.0E-7>=Math.abs(d-b.step))}return d});[{name:"rangeOverflow",attr:"max",factor:1},{name:"rangeUnderflow",attr:"min",factor:-1}].forEach(function(a){e.addValidityRule(a.name,
function(c,b,d,e){e=(e||{})[a.name]||!1;if(""===b)return e;if(!("type"in d))d.type=l(c[0]);if(f[d.type]&&f[d.type].asNumber){if(!("valueAsNumber"in d))d.valueAsNumber=f[d.type].asNumber(b);if(isNaN(d.valueAsNumber))return!1;i(a.attr,c,d);if(isNaN(d[a.attr+"AsNumber"]))return e;e=d[a.attr+"AsNumber"]*a.factor<d.valueAsNumber*a.factor-1.0E-7}return e})});e.reflectProperties(["input"],["max","min","step"]);var s=e.defineNodeNameProperty("input","valueAsNumber",{prop:{get:function(){var a=l(this),a=f[a]&&
f[a].asNumber?f[a].asNumber(d.prop(this,"value")):s.prop._supget&&s.prop._supget.apply(this,arguments);null==a&&(a=m);return a},set:function(a){var c=l(this);f[c]&&f[c].numberToString?isNaN(a)?d.prop(this,"value",""):(c=f[c].numberToString(a),!1!==c?d.prop(this,"value",c):e.warn("INVALID_STATE_ERR: DOM Exception 11")):s.prop._supset&&s.prop._supset.apply(this,arguments)}}}),t=e.defineNodeNameProperty("input","valueAsDate",{prop:{get:function(){var a=l(this);return f[a]&&f[a].asDate&&!f[a].noAsDate?
f[a].asDate(d.prop(this,"value")):t.prop._supget&&t.prop._supget.call(this)||null},set:function(a){var c=l(this);if(f[c]&&f[c].dateToString&&!f[c].noAsDate){if(null===a)return d.prop(this,"value",""),"";c=f[c].dateToString(a);if(!1!==c)return d.prop(this,"value",c),c;e.warn("INVALID_STATE_ERR: DOM Exception 11")}else return t.prop._supset&&t.prop._supset.apply(this,arguments)||null}}});j={mismatch:function(a){return!o(a)},step:1,stepScaleFactor:1,asNumber:function(a){return o(a)?1*a:m},numberToString:function(a){return o(a)?
a:!1}};p={minDefault:0,maxDefault:100};u={mismatch:function(a){if(!a||!a.split||!/\d$/.test(a))return!0;var c=a.split(/\u002D/);if(3!==c.length)return!0;var b=!1;d.each(c,function(a,c){if(!(o(c)||c&&c=="0"+1*c))return b=!0,!1});if(b)return b;if(4!==c[0].length||2!=c[1].length||12<c[1]||2!=c[2].length||33<c[2])b=!0;return a!==this.dateToString(this.asDate(a,!0))},step:1,stepScaleFactor:864E5,asDate:function(a,c){return!c&&this.mismatch(a)?null:new Date(this.asNumber(a,!0))},asNumber:function(a,c){var b=
m;if(c||!this.mismatch(a))a=a.split(/\u002D/),b=Date.UTC(a[0],a[1]-1,a[2]);return b},numberToString:function(a){return o(a)?this.dateToString(new Date(1*a)):!1},dateToString:function(a){return a&&a.getFullYear?a.getUTCFullYear()+"-"+q(a.getUTCMonth()+1,2)+"-"+q(a.getUTCDate(),2):!1}};h={mismatch:function(a,c){if(!a||!a.split||!/\d$/.test(a))return!0;a=a.split(/\u003A/);if(2>a.length||3<a.length)return!0;var b=!1,k;a[2]&&(a[2]=a[2].split(/\u002E/),k=parseInt(a[2][1],10),a[2]=a[2][0]);d.each(a,function(a,
c){if(!(o(c)||c&&c=="0"+1*c)||2!==c.length)return b=!0,!1});if(b||23<a[0]||0>a[0]||59<a[1]||0>a[1]||a[2]&&(59<a[2]||0>a[2])||k&&isNaN(k))return!0;k&&(100>k?k*=100:10>k&&(k*=10));return!0===c?[a,k]:!1},step:60,stepBase:0,stepScaleFactor:1E3,asDate:function(a){a=new Date(this.asNumber(a));return isNaN(a)?null:a},asNumber:function(a){var c=m,a=this.mismatch(a,!0);!0!==a&&(c=Date.UTC("1970",0,1,a[0][0],a[0][1],a[0][2]||0),a[1]&&(c+=a[1]));return c},dateToString:function(a){if(a&&a.getUTCHours){var c=
q(a.getUTCHours(),2)+":"+q(a.getUTCMinutes(),2),b=a.getSeconds();"0"!=b&&(c+=":"+q(b,2));b=a.getUTCMilliseconds();"0"!=b&&(c+="."+q(b,3));return c}return!1}};if(r||!n("range")||!n("time"))p=d.extend({},j,p),h=d.extend({},u,h);(r||!n("number"))&&e.addInputType("number",j);(r||!n("range"))&&e.addInputType("range",p);(r||!n("date"))&&e.addInputType("date",u);(r||!n("time"))&&e.addInputType("time",h)});
jQuery.webshims.register("form-number-date-ui",function(d,e,j,p,u,h){var m=e.triggerInlineForm,j=Modernizr.inputtypes,f=function(){var a={"padding-box":"innerWidth","border-box":"outerWidth","content-box":"width"},c=Modernizr.prefixed&&Modernizr.prefixed("boxSizing");d.browser.msie&&8>e.browserVersion&&(c=!1);return function(b,d){var e,f,h;f="width";c&&(f=a[b.css(c)]||f);e=b[f]();f="width"==f;if(e){var i=parseInt(d.css("marginLeft"),10)||0,v=d.outerWidth();(h=parseInt(b.css("marginRight"),10)||0)&&
b.css("marginRight",0);i<=-1*v?(d.css("marginRight",Math.floor(Math.abs(v+i-0.1)+h)),b.css("paddingRight",(parseInt(b.css("paddingRight"),10)||0)+Math.abs(i)),f&&b.css("width",Math.floor(e+i))):(d.css("marginRight",h),b.css("width",Math.floor(e-i-v-0.2)))}}}(),o={},n=d([]),l,i=function(a,c){d("input",a).add(c.filter("input")).each(function(){var a=d.prop(this,"type");if(i[a]&&!e.data(this,"shadowData"))i[a](d(this))})},q=function(a,c){if(h.lazyDate){var b=d.data(a[0],"setDateLazyTimer");b&&clearTimeout(b);
d.data(a[0],"setDateLazyTimer",setTimeout(function(){a.datepicker("setDate",c);d.removeData(a[0],"setDateLazyTimer");a=null},0))}else a.datepicker("setDate",c)},r={tabindex:1,tabIndex:1,title:1,"aria-required":1,"aria-invalid":1};if(!h.copyAttrs)h.copyAttrs={};e.extendUNDEFProp(h.copyAttrs,r);var s=function(a){return h.calculateWidth?{css:{marginRight:a.css("marginRight"),marginLeft:a.css("marginLeft")},outerWidth:a.outerWidth()}:{}};i.common=function(a,c,b){Modernizr.formvalidation&&a.bind("firstinvalid",
function(b){(e.fromSubmit||!l)&&a.unbind("invalid.replacedwidgetbubble").bind("invalid.replacedwidgetbubble",function(c){!b.isInvalidUIPrevented()&&!c.isDefaultPrevented()&&(e.validityAlert.showFor(b.target),b.preventDefault(),c.preventDefault());a.unbind("invalid.replacedwidgetbubble")})});var k,f,i=d("input, span.ui-slider-handle",c),j=a[0].attributes;for(k in h.copyAttrs)if((f=j[k])&&f.specified)r[k]&&i[0]?i.attr(k,f.nodeValue):c[0].setAttribute(k,f.nodeValue);k=(k=a.attr("id"))?d('label[for="'+
k+'"]',a[0].form):n;c.addClass(a[0].className);e.addShadowDom(a,c,{data:b||{},shadowFocusElement:d("input.input-datetime-local-date, span.ui-slider-handle",c)[0],shadowChilds:i});a.after(c);a[0].form&&d(a[0].form).bind("reset",function(b){b.originalEvent&&!b.isDefaultPrevented()&&setTimeout(function(){a.prop("value",a.prop("value"))},0)});k[0]&&(c.getShadowFocusElement().attr("aria-labelledby",e.getID(k)),k.bind("click",function(){a.getShadowFocusElement().focus();return!1}))};Modernizr.formvalidation&&
["input","form"].forEach(function(a){var c=e.defineNodeNameProperty(a,"checkValidity",{prop:{value:function(){l=!0;var a=c.prop._supvalue.apply(this,arguments);l=!1;return a}}})});if(!j.date||h.replaceUI){var t=function(a,c,b,k){var f,i,j=function(){l.dpDiv.unbind("mousedown.webshimsmousedownhandler");i=f=!1},l=c.bind("focusin",function(){j();l.dpDiv.unbind("mousedown.webshimsmousedownhandler").bind("mousedown.webshimsmousedownhandler",function(){f=!0})}).bind("focusout blur",function(a){f&&(i=!0,
a.stopImmediatePropagation())}).datepicker(d.extend({onClose:function(){i&&c.not(":focus")?(j(),c.trigger("focusout"),c.triggerHandler("blur")):j()}},o,h.datepicker,a.data("datepicker"))).bind("change",b).data("datepicker");l.dpDiv.addClass("input-date-datepicker-control");k&&e.triggerDomUpdate(k[0]);"disabled,min,max,value,step,data-placeholder".split(",").forEach(function(b){var c=a.attr(b);c&&a.attr(b,c)});return l};i.date=function(a){if(d.fn.datepicker){var c=d('<input class="input-date" type="text" />'),
b;this.common(a,c,i.date.attrs);b=t(a,c,function(b){i.date.blockAttr=!0;var e;if(h.lazyDate){var f=d.data(c[0],"setDateLazyTimer");f&&(clearTimeout(f),d.removeData(c[0],"setDateLazyTimer"))}try{e=(e=d.datepicker.parseDate(c.datepicker("option","dateFormat"),c.prop("value")))?d.datepicker.formatDate("yy-mm-dd",e):c.prop("value")}catch(j){e=c.prop("value")}a.prop("value",e);i.date.blockAttr=!1;b.stopImmediatePropagation();m(a[0],"input");m(a[0],"change")});d(a).bind("updateshadowdom",function(){if(b.trigger[0]&&
(a.css({display:""}),a[0].offsetWidth||a[0].offsetHeight)){var d=s(a);d.css&&(c.css(d.css),d.outerWidth&&c.outerWidth(d.outerWidth),f(c,b.trigger))}a.css({display:"none"})}).triggerHandler("updateshadowdom");b.trigger[0]&&setTimeout(function(){e.ready("WINDOWLOAD",function(){d(a).triggerHandler("updateshadowdom")})},9)}};i.date.attrs={disabled:function(a,c,b){d.prop(c,"disabled",!!b)},min:function(a,c,b){try{b=d.datepicker.parseDate("yy-mm-dd",b)}catch(e){b=!1}b&&d(c).datepicker("option","minDate",
b)},max:function(a,c,b){try{b=d.datepicker.parseDate("yy-mm-dd",b)}catch(e){b=!1}b&&d(c).datepicker("option","maxDate",b)},"data-placeholder":function(a,c,b){a=(b||"").split("-");3==a.length&&(b=d(c).datepicker("option","dateFormat").replace("yy",a[0]).replace("mm",a[1]).replace("dd",a[2]));d.prop(c,"placeholder",b)},value:function(a,c,b){if(!i.date.blockAttr){try{var e=d.datepicker.parseDate("yy-mm-dd",b)}catch(f){e=!1}e?q(d(c),e):d.prop(c,"value",b)}}}}if(!j.range||h.replaceUI)i.range=function(a){if(d.fn.slider){var c=
d('<span class="input-range"><span class="ui-slider-handle" role="slider" tabindex="0" /></span>');this.common(a,c,i.range.attrs);a.bind("updateshadowdom",function(){a.css({display:""});if(a[0].offsetWidth||a[0].offsetHeight){var b=s(a);b.css&&(c.css(b.css),b.outerWidth&&c.outerWidth(b.outerWidth))}a.css({display:"none"})}).triggerHandler("updateshadowdom");c.slider(d.extend(!0,{},h.slider,a.data("slider"))).bind("slide",function(b,c){if(b.originalEvent)i.range.blockAttr=!0,a.prop("value",c.value),
i.range.blockAttr=!1,m(a[0],"input")}).bind("slidechange",function(b){b.originalEvent&&m(a[0],"change")});["disabled","min","max","step","value"].forEach(function(b){var c=a.prop(b),e;"value"==b&&!c&&(e=a.getShadowElement())&&(c=(d(e).slider("option","max")-d(e).slider("option","min"))/2);null!=c&&a.prop(b,c)})}},i.range.attrs={disabled:function(a,c,b){b=!!b;d(c).slider("option","disabled",b);d("span",c).attr({"aria-disabled":b+"",tabindex:b?"-1":"0"})},min:function(a,c,b){b=b?1*b||0:0;d(c).slider("option",
"min",b);d("span",c).attr({"aria-valuemin":b})},max:function(a,c,b){b=b||0===b?1*b||100:100;d(c).slider("option","max",b);d("span",c).attr({"aria-valuemax":b})},value:function(a,c,b){b=d(a).prop("valueAsNumber");isNaN(b)||(i.range.blockAttr||d(c).slider("option","value",b),d("span",c).attr({"aria-valuenow":b,"aria-valuetext":b}))},step:function(a,c,b){b=b&&d.trim(b)?1*b||1:1;d(c).slider("option","step",b)}};if(!e.bugs.valueAsNumberSet&&(h.replaceUI||!Modernizr.inputtypes.date||!Modernizr.inputtypes.range))j=
function(){e.data(this,"hasShadow")&&d.prop(this,"value",d.prop(this,"value"))},e.onNodeNamesPropertyModify("input","valueAsNumber",j),e.onNodeNamesPropertyModify("input","valueAsDate",j);d.each("disabled,min,max,value,step,data-placeholder".split(","),function(a,c){e.onNodeNamesPropertyModify("input",c,function(a){var d=e.data(this,"shadowData");if(d&&d.data&&d.data[c]&&d.nativeElement===this)d.data[c](this,d.shadowElement,a)})});if(!h.availabeLangs)h.availabeLangs="af ar ar-DZ az bg bs ca cs da de el en-AU en-GB en-NZ eo es et eu fa fi fo fr fr-CH gl he hr hu hy id is it ja ko kz lt lv ml ms nl no pl pt-BR rm ro ru sk sl sq sr sr-SR sv ta th tr uk vi zh-CN zh-HK zh-TW".split(" ");
j=function(){d.datepicker&&(e.activeLang({langObj:d.datepicker.regional,module:"form-number-date-ui",callback:function(a){a=d.extend({},o,a,h.datepicker);a.dateFormat&&h.datepicker.dateFormat!=a.dateFormat&&d("input.hasDatepicker").filter(".input-date, .input-datetime-local-date").datepicker("option","dateFormat",a.dateFormat).getNativeElement().filter("[data-placeholder]").attr("data-placeholder",function(a,b){return b});d.datepicker.setDefaults(a)}}),d(p).unbind("jquery-uiReady.langchange input-widgetsReady.langchange"))};
d(p).bind("jquery-uiReady.langchange input-widgetsReady.langchange",j);j();(function(){var a=function(){var a={};return function(b){return b in a?a[b]:a[b]=d('<input type="'+b+'" />')[0].type===b}}();if(!a("number")||!a("time")){var c=e.cfg["forms-ext"],b=e.inputTypes,i={number:"0123456789.",time:"0123456789:."},h=function(a,c,g){g=g||{};if(!("type"in g))g.type=d.prop(a,"type");if(!("step"in g))g.step=e.getStep(a,g.type);if(!("valueAsNumber"in g))g.valueAsNumber=b[g.type].asNumber(d.prop(a,"value"));
var f="any"==g.step?b[g.type].step*b[g.type].stepScaleFactor:g.step;e.addMinMaxNumberToCache("min",d(a),g);e.addMinMaxNumberToCache("max",d(a),g);if(isNaN(g.valueAsNumber))g.valueAsNumber=b[g.type].stepBase||0;if("any"!==g.step&&(a=Math.round(1E7*((g.valueAsNumber-(g.minAsnumber||0))%g.step))/1E7)&&Math.abs(a)!=g.step)g.valueAsNumber-=a;a=g.valueAsNumber+f*c;return a=!isNaN(g.minAsNumber)&&a<g.minAsNumber?g.valueAsNumber*c<g.minAsNumber?g.minAsNumber:isNaN(g.maxAsNumber)?g.valueAsNumber:g.maxAsNumber:
!isNaN(g.maxAsNumber)&&a>g.maxAsNumber?g.valueAsNumber*c>g.maxAsNumber?g.maxAsNumber:isNaN(g.minAsNumber)?g.valueAsNumber:g.minAsNumber:Math.round(1E7*a)/1E7};e.modules["form-number-date-ui"].getNextStep=h;if(c.stepArrows){var j={set:function(){var a=e.data(this,"step-controls");if(a)a[this.disabled||this.readonly?"addClass":"removeClass"]("disabled-step-control")}};e.onNodeNamesPropertyModify("input","disabled",j);e.onNodeNamesPropertyModify("input","readonly",d.extend({},j))}var l={38:1,40:-1},
o=function(a,c){function e(){var b=d.prop(a,"value");b==i&&b!=k&&"string"==typeof b&&m(a,"change");k=b}var f=!1,i,k;(function(){k=d(a).bind({"change.stepcontrol focus.stepcontrol":function(b){if(!f||"focus"!=b.type)k=d.prop(a,"value")},"blur.stepcontrol":function(){f||setTimeout(function(){!f&&!d(a).is(":focus")&&e();i=!1},9)}}).prop("value")})();return{triggerChange:e,step:function(e){!d.prop(a,"disabled")&&!a.readOnly&&e&&(i=b[c].numberToString(h(a,e,{type:c})),d.prop(a,"value",i),m(a,"input"))},
setFocus:function(){f=!0;setTimeout(function(){f=!1},18);setTimeout(function(){if(!d(a).is(":focus"))try{a.focus()}catch(b){}},1)}}};e.addReady(function(h,j){c.stepArrows&&d("input",h).add(j.filter("input")).each(function(){var g=d.prop(this,"type");if(b[g]&&b[g].asNumber&&c.stepArrows&&!(!0!==c.stepArrows&&!c.stepArrows[g]||a(g)||d(h).hasClass("has-step-controls"))){var h=this,j=o(h,g),m=d('<span class="step-controls" unselectable="on"><span class="step-up" /><span class="step-down" /></span>').insertAfter(h).bind("selectstart dragstart",
function(){return!1}).bind("mousedown mousepress",function(a){d(a.target).hasClass("step-controls")||j.step(d(a.target).hasClass("step-up")?1:-1);j.setFocus();return!1}).bind("mousepressstart mousepressend",function(a){"mousepressend"==a.type&&j.triggerChange();d(a.target)["mousepressstart"==a.type?"addClass":"removeClass"]("mousepress-ui")}),p=function(a,b){if(b)return j.step(b),!1},n=d(h).addClass("has-step-controls").attr({readonly:h.readOnly,disabled:h.disabled,autocomplete:"off",role:"spinbutton"}).bind("keyup",
function(a){(a=l[a.keyCode])&&j.triggerChange(a)}).bind(d.browser.msie?"keydown":"keypress",function(a){if(a=l[a.keyCode])return j.step(a),!1});i[g]&&n.bind("keypress",function(){var a=i[g];return function(b){var c=String.fromCharCode(null==b.charCode?b.keyCode:b.charCode);return b.ctrlKey||b.metaKey||" ">c||-1<a.indexOf(c)}}());n.bind("focus",function(){n.add(m).unbind(".mwhellwebshims").bind("mousewheel.mwhellwebshims",p)}).bind("blur",function(){d(h).add(m).unbind(".mwhellwebshims")});e.data(h,
"step-controls",m);if(c.calculateWidth){var q;n.bind("updateshadowdom",function(){if(!q&&(h.offsetWidth||h.offsetHeight))q=!0,f(n,m),m.css("marginTop",(n.outerHeight()-m.outerHeight())/2)}).triggerHandler("updateshadowdom")}}})})}})();e.addReady(function(a,c){d(p).bind("jquery-uiReady.initinputui input-widgetsReady.initinputui",function(){if(d.datepicker||d.fn.slider){if(d.datepicker&&!o.dateFormat)o.dateFormat=d.datepicker._defaults.dateFormat;i(a,c)}d.datepicker&&d.fn.slider?d(p).unbind(".initinputui"):
e.modules["input-widgets"].src||e.warn('jQuery UI Widget factory is already included, but not datepicker or slider. configure src of $.webshims.modules["input-widgets"].src')})})});
