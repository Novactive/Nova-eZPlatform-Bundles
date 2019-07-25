(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("react"), require("prop-types"), require("jquery"), require("react-dom"));
	else if(typeof define === 'function' && define.amd)
		define(["react", "prop-types", "jquery", "react-dom"], factory);
	else if(typeof exports === 'object')
		exports["MenuManager"] = factory(require("react"), require("prop-types"), require("jquery"), require("react-dom"));
	else
		root["eZ"] = root["eZ"] || {}, root["eZ"]["modules"] = root["eZ"]["modules"] || {}, root["eZ"]["modules"]["MenuManager"] = factory(root["React"], root["PropTypes"], root["jQuery"], root["ReactDOM"]);
})(typeof self !== 'undefined' ? self : this, function(__WEBPACK_EXTERNAL_MODULE_0__, __WEBPACK_EXTERNAL_MODULE_1__, __WEBPACK_EXTERNAL_MODULE_3__, __WEBPACK_EXTERNAL_MODULE_4__) {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 11);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_0__;

/***/ }),
/* 1 */
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_1__;

/***/ }),
/* 2 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */
class MenuItem {
  constructor(props) {
    this.id = String(props.id || '_' + Math.random().toString(36).substr(2, 9));
    this.parentId = props.parentId || '#';
    this.name = props.name;
    this.position = props.position || 0;
    this.url = props.url || '';
    this.target = props.target || '';
    this.state = props.state;
    this.type = props.type;
    this.options = props.options || {};
  }

  getOption(name, defaultValue = null) {
    const option = this.options[name];
    return option !== undefined ? option : defaultValue;
  }

  setOption(name, value) {
    this.options[name] = value;
  }

  toTreeNode(language) {
    const isActive = this.getOption('active', true);
    let className = '';

    if (isActive === false) {
      className += 'jstree-desactivated';
    }

    return {
      id: this.id,
      parent: this.parentId,
      text: this.translateProperty(this.name, language),
      data: {
        position: this.position,
        url: this.url,
        target: this.target,
        options: this.options
      },
      state: this.state,
      type: this.type,
      a_attr: {
        class: className
      }
    };
  }

  translateProperty(property, language) {
    try {
      const values = JSON.parse(property);
      return values[language];
    } catch (e) {
      return property;
    }
  }

  isEnabled() {
    return this.state === undefined || !this.state.disabled;
  }

  static fromTreeNode(node, position = null) {
    return new MenuItem({
      id: node.id,
      parentId: node.parent,
      name: node.text,
      position: position || node.data.position,
      url: node.data.url,
      target: node.data.target,
      options: node.data.options,
      state: node.state,
      type: node.type
    });
  }

}
/* harmony export (immutable) */ __webpack_exports__["a"] = MenuItem;


/***/ }),
/* 3 */
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_3__;

/***/ }),
/* 4 */
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_4__;

/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*! jsTree - v3.3.8 - 2019-04-29 - (MIT) */
!function(a){"use strict"; true?!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(3)], __WEBPACK_AMD_DEFINE_FACTORY__ = (a),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)):"undefined"!=typeof module&&module.exports?module.exports=a(require("jquery")):a(jQuery)}(function(a,b){"use strict";if(!a.jstree){var c=0,d=!1,e=!1,f=!1,g=[],h=a("script:last").attr("src"),i=window.document;a.jstree={version:"3.3.8",defaults:{plugins:[]},plugins:{},path:h&&-1!==h.indexOf("/")?h.replace(/\/[^\/]+$/,""):"",idregex:/[\\:&!^|()\[\]<>@*'+~#";.,=\- \/${}%?`]/g,root:"#"},a.jstree.create=function(b,d){var e=new a.jstree.core(++c),f=d;return d=a.extend(!0,{},a.jstree.defaults,d),f&&f.plugins&&(d.plugins=f.plugins),a.each(d.plugins,function(a,b){"core"!==a&&(e=e.plugin(b,d[b]))}),a(b).data("jstree",e),e.init(b,d),e},a.jstree.destroy=function(){a(".jstree:jstree").jstree("destroy"),a(i).off(".jstree")},a.jstree.core=function(a){this._id=a,this._cnt=0,this._wrk=null,this._data={core:{themes:{name:!1,dots:!1,icons:!1,ellipsis:!1},selected:[],last_error:{},working:!1,worker_queue:[],focused:null}}},a.jstree.reference=function(b){var c=null,d=null;if(!b||!b.id||b.tagName&&b.nodeType||(b=b.id),!d||!d.length)try{d=a(b)}catch(e){}if(!d||!d.length)try{d=a("#"+b.replace(a.jstree.idregex,"\\$&"))}catch(e){}return d&&d.length&&(d=d.closest(".jstree")).length&&(d=d.data("jstree"))?c=d:a(".jstree").each(function(){var d=a(this).data("jstree");return d&&d._model.data[b]?(c=d,!1):void 0}),c},a.fn.jstree=function(c){var d="string"==typeof c,e=Array.prototype.slice.call(arguments,1),f=null;return c!==!0||this.length?(this.each(function(){var g=a.jstree.reference(this),h=d&&g?g[c]:null;return f=d&&h?h.apply(g,e):null,g||d||c!==b&&!a.isPlainObject(c)||a.jstree.create(this,c),(g&&!d||c===!0)&&(f=g||!1),null!==f&&f!==b?!1:void 0}),null!==f&&f!==b?f:this):!1},a.expr.pseudos.jstree=a.expr.createPseudo(function(c){return function(c){return a(c).hasClass("jstree")&&a(c).data("jstree")!==b}}),a.jstree.defaults.core={data:!1,strings:!1,check_callback:!1,error:a.noop,animation:200,multiple:!0,themes:{name:!1,url:!1,dir:!1,dots:!0,icons:!0,ellipsis:!1,stripes:!1,variant:!1,responsive:!1},expand_selected_onload:!0,worker:!0,force_text:!1,dblclick_toggle:!0,loaded_state:!1,restore_focus:!0,keyboard:{"ctrl-space":function(b){b.type="click",a(b.currentTarget).trigger(b)},enter:function(b){b.type="click",a(b.currentTarget).trigger(b)},left:function(b){if(b.preventDefault(),this.is_open(b.currentTarget))this.close_node(b.currentTarget);else{var c=this.get_parent(b.currentTarget);c&&c.id!==a.jstree.root&&this.get_node(c,!0).children(".jstree-anchor").focus()}},up:function(a){a.preventDefault();var b=this.get_prev_dom(a.currentTarget);b&&b.length&&b.children(".jstree-anchor").focus()},right:function(b){if(b.preventDefault(),this.is_closed(b.currentTarget))this.open_node(b.currentTarget,function(a){this.get_node(a,!0).children(".jstree-anchor").focus()});else if(this.is_open(b.currentTarget)){var c=this.get_node(b.currentTarget,!0).children(".jstree-children")[0];c&&a(this._firstChild(c)).children(".jstree-anchor").focus()}},down:function(a){a.preventDefault();var b=this.get_next_dom(a.currentTarget);b&&b.length&&b.children(".jstree-anchor").focus()},"*":function(a){this.open_all()},home:function(b){b.preventDefault();var c=this._firstChild(this.get_container_ul()[0]);c&&a(c).children(".jstree-anchor").filter(":visible").focus()},end:function(a){a.preventDefault(),this.element.find(".jstree-anchor").filter(":visible").last().focus()},f2:function(a){a.preventDefault(),this.edit(a.currentTarget)}}},a.jstree.core.prototype={plugin:function(b,c){var d=a.jstree.plugins[b];return d?(this._data[b]={},d.prototype=this,new d(c,this)):this},init:function(b,c){this._model={data:{},changed:[],force_full_redraw:!1,redraw_timeout:!1,default_state:{loaded:!0,opened:!1,selected:!1,disabled:!1}},this._model.data[a.jstree.root]={id:a.jstree.root,parent:null,parents:[],children:[],children_d:[],state:{loaded:!1}},this.element=a(b).addClass("jstree jstree-"+this._id),this.settings=c,this._data.core.ready=!1,this._data.core.loaded=!1,this._data.core.rtl="rtl"===this.element.css("direction"),this.element[this._data.core.rtl?"addClass":"removeClass"]("jstree-rtl"),this.element.attr("role","tree"),this.settings.core.multiple&&this.element.attr("aria-multiselectable",!0),this.element.attr("tabindex")||this.element.attr("tabindex","0"),this.bind(),this.trigger("init"),this._data.core.original_container_html=this.element.find(" > ul > li").clone(!0),this._data.core.original_container_html.find("li").addBack().contents().filter(function(){return 3===this.nodeType&&(!this.nodeValue||/^\s+$/.test(this.nodeValue))}).remove(),this.element.html("<ul class='jstree-container-ul jstree-children' role='group'><li id='j"+this._id+"_loading' class='jstree-initial-node jstree-loading jstree-leaf jstree-last' role='tree-item'><i class='jstree-icon jstree-ocl'></i><a class='jstree-anchor' href='#'><i class='jstree-icon jstree-themeicon-hidden'></i>"+this.get_string("Loading ...")+"</a></li></ul>"),this.element.attr("aria-activedescendant","j"+this._id+"_loading"),this._data.core.li_height=this.get_container_ul().children("li").first().outerHeight()||24,this._data.core.node=this._create_prototype_node(),this.trigger("loading"),this.load_node(a.jstree.root)},destroy:function(a){if(this.trigger("destroy"),this._wrk)try{window.URL.revokeObjectURL(this._wrk),this._wrk=null}catch(b){}a||this.element.empty(),this.teardown()},_create_prototype_node:function(){var a=i.createElement("LI"),b,c;return a.setAttribute("role","treeitem"),b=i.createElement("I"),b.className="jstree-icon jstree-ocl",b.setAttribute("role","presentation"),a.appendChild(b),b=i.createElement("A"),b.className="jstree-anchor",b.setAttribute("href","#"),b.setAttribute("tabindex","-1"),c=i.createElement("I"),c.className="jstree-icon jstree-themeicon",c.setAttribute("role","presentation"),b.appendChild(c),a.appendChild(b),b=c=null,a},_kbevent_to_func:function(a){var b={8:"Backspace",9:"Tab",13:"Enter",19:"Pause",27:"Esc",32:"Space",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"Left",38:"Up",39:"Right",40:"Down",44:"Print",45:"Insert",46:"Delete",96:"Numpad0",97:"Numpad1",98:"Numpad2",99:"Numpad3",100:"Numpad4",101:"Numpad5",102:"Numpad6",103:"Numpad7",104:"Numpad8",105:"Numpad9","-13":"NumpadEnter",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"Numlock",145:"Scrolllock",16:"Shift",17:"Ctrl",18:"Alt",48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",59:";",61:"=",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z",107:"+",109:"-",110:".",186:";",187:"=",188:",",189:"-",190:".",191:"/",192:"`",219:"[",220:"\\",221:"]",222:"'",111:"/",106:"*",173:"-"},c=[];a.ctrlKey&&c.push("ctrl"),a.altKey&&c.push("alt"),a.shiftKey&&c.push("shift"),c.push(b[a.which]||a.which),c=c.sort().join("-").toLowerCase();var d=this.settings.core.keyboard,e,f;for(e in d)if(d.hasOwnProperty(e)&&(f=e,"-"!==f&&"+"!==f&&(f=f.replace("--","-MINUS").replace("+-","-MINUS").replace("++","-PLUS").replace("-+","-PLUS"),f=f.split(/-|\+/).sort().join("-").replace("MINUS","-").replace("PLUS","+").toLowerCase()),f===c))return d[e];return null},teardown:function(){this.unbind(),this.element.removeClass("jstree").removeData("jstree").find("[class^='jstree']").addBack().attr("class",function(){return this.className.replace(/jstree[^ ]*|$/gi,"")}),this.element=null},bind:function(){var b="",c=null,d=0;this.element.on("dblclick.jstree",function(a){if(a.target.tagName&&"input"===a.target.tagName.toLowerCase())return!0;if(i.selection&&i.selection.empty)i.selection.empty();else if(window.getSelection){var b=window.getSelection();try{b.removeAllRanges(),b.collapse()}catch(c){}}}).on("mousedown.jstree",a.proxy(function(a){a.target===this.element[0]&&(a.preventDefault(),d=+new Date)},this)).on("mousedown.jstree",".jstree-ocl",function(a){a.preventDefault()}).on("click.jstree",".jstree-ocl",a.proxy(function(a){this.toggle_node(a.target)},this)).on("dblclick.jstree",".jstree-anchor",a.proxy(function(a){return a.target.tagName&&"input"===a.target.tagName.toLowerCase()?!0:void(this.settings.core.dblclick_toggle&&this.toggle_node(a.target))},this)).on("click.jstree",".jstree-anchor",a.proxy(function(b){b.preventDefault(),b.currentTarget!==i.activeElement&&a(b.currentTarget).focus(),this.activate_node(b.currentTarget,b)},this)).on("keydown.jstree",".jstree-anchor",a.proxy(function(a){if(a.target.tagName&&"input"===a.target.tagName.toLowerCase())return!0;this._data.core.rtl&&(37===a.which?a.which=39:39===a.which&&(a.which=37));var b=this._kbevent_to_func(a);if(b){var c=b.call(this,a);if(c===!1||c===!0)return c}},this)).on("load_node.jstree",a.proxy(function(b,c){c.status&&(c.node.id!==a.jstree.root||this._data.core.loaded||(this._data.core.loaded=!0,this._firstChild(this.get_container_ul()[0])&&this.element.attr("aria-activedescendant",this._firstChild(this.get_container_ul()[0]).id),this.trigger("loaded")),this._data.core.ready||setTimeout(a.proxy(function(){if(this.element&&!this.get_container_ul().find(".jstree-loading").length){if(this._data.core.ready=!0,this._data.core.selected.length){if(this.settings.core.expand_selected_onload){var b=[],c,d;for(c=0,d=this._data.core.selected.length;d>c;c++)b=b.concat(this._model.data[this._data.core.selected[c]].parents);for(b=a.vakata.array_unique(b),c=0,d=b.length;d>c;c++)this.open_node(b[c],!1,0)}this.trigger("changed",{action:"ready",selected:this._data.core.selected})}this.trigger("ready")}},this),0))},this)).on("keypress.jstree",a.proxy(function(d){if(d.target.tagName&&"input"===d.target.tagName.toLowerCase())return!0;c&&clearTimeout(c),c=setTimeout(function(){b=""},500);var e=String.fromCharCode(d.which).toLowerCase(),f=this.element.find(".jstree-anchor").filter(":visible"),g=f.index(i.activeElement)||0,h=!1;if(b+=e,b.length>1){if(f.slice(g).each(a.proxy(function(c,d){return 0===a(d).text().toLowerCase().indexOf(b)?(a(d).focus(),h=!0,!1):void 0},this)),h)return;if(f.slice(0,g).each(a.proxy(function(c,d){return 0===a(d).text().toLowerCase().indexOf(b)?(a(d).focus(),h=!0,!1):void 0},this)),h)return}if(new RegExp("^"+e.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&")+"+$").test(b)){if(f.slice(g+1).each(a.proxy(function(b,c){return a(c).text().toLowerCase().charAt(0)===e?(a(c).focus(),h=!0,!1):void 0},this)),h)return;if(f.slice(0,g+1).each(a.proxy(function(b,c){return a(c).text().toLowerCase().charAt(0)===e?(a(c).focus(),h=!0,!1):void 0},this)),h)return}},this)).on("init.jstree",a.proxy(function(){var a=this.settings.core.themes;this._data.core.themes.dots=a.dots,this._data.core.themes.stripes=a.stripes,this._data.core.themes.icons=a.icons,this._data.core.themes.ellipsis=a.ellipsis,this.set_theme(a.name||"default",a.url),this.set_theme_variant(a.variant)},this)).on("loading.jstree",a.proxy(function(){this[this._data.core.themes.dots?"show_dots":"hide_dots"](),this[this._data.core.themes.icons?"show_icons":"hide_icons"](),this[this._data.core.themes.stripes?"show_stripes":"hide_stripes"](),this[this._data.core.themes.ellipsis?"show_ellipsis":"hide_ellipsis"]()},this)).on("blur.jstree",".jstree-anchor",a.proxy(function(b){this._data.core.focused=null,a(b.currentTarget).filter(".jstree-hovered").trigger("mouseleave"),this.element.attr("tabindex","0")},this)).on("focus.jstree",".jstree-anchor",a.proxy(function(b){var c=this.get_node(b.currentTarget);c&&c.id&&(this._data.core.focused=c.id),this.element.find(".jstree-hovered").not(b.currentTarget).trigger("mouseleave"),a(b.currentTarget).trigger("mouseenter"),this.element.attr("tabindex","-1")},this)).on("focus.jstree",a.proxy(function(){if(+new Date-d>500&&!this._data.core.focused&&this.settings.core.restore_focus){d=0;var a=this.get_node(this.element.attr("aria-activedescendant"),!0);a&&a.find("> .jstree-anchor").focus()}},this)).on("mouseenter.jstree",".jstree-anchor",a.proxy(function(a){this.hover_node(a.currentTarget)},this)).on("mouseleave.jstree",".jstree-anchor",a.proxy(function(a){this.dehover_node(a.currentTarget)},this))},unbind:function(){this.element.off(".jstree"),a(i).off(".jstree-"+this._id)},trigger:function(a,b){b||(b={}),b.instance=this,this.element.triggerHandler(a.replace(".jstree","")+".jstree",b)},get_container:function(){return this.element},get_container_ul:function(){return this.element.children(".jstree-children").first()},get_string:function(b){var c=this.settings.core.strings;return a.isFunction(c)?c.call(this,b):c&&c[b]?c[b]:b},_firstChild:function(a){a=a?a.firstChild:null;while(null!==a&&1!==a.nodeType)a=a.nextSibling;return a},_nextSibling:function(a){a=a?a.nextSibling:null;while(null!==a&&1!==a.nodeType)a=a.nextSibling;return a},_previousSibling:function(a){a=a?a.previousSibling:null;while(null!==a&&1!==a.nodeType)a=a.previousSibling;return a},get_node:function(b,c){b&&b.id&&(b=b.id),b instanceof a&&b.length&&b[0].id&&(b=b[0].id);var d;try{if(this._model.data[b])b=this._model.data[b];else if("string"==typeof b&&this._model.data[b.replace(/^#/,"")])b=this._model.data[b.replace(/^#/,"")];else if("string"==typeof b&&(d=a("#"+b.replace(a.jstree.idregex,"\\$&"),this.element)).length&&this._model.data[d.closest(".jstree-node").attr("id")])b=this._model.data[d.closest(".jstree-node").attr("id")];else if((d=this.element.find(b)).length&&this._model.data[d.closest(".jstree-node").attr("id")])b=this._model.data[d.closest(".jstree-node").attr("id")];else{if(!(d=this.element.find(b)).length||!d.hasClass("jstree"))return!1;b=this._model.data[a.jstree.root]}return c&&(b=b.id===a.jstree.root?this.element:a("#"+b.id.replace(a.jstree.idregex,"\\$&"),this.element)),b}catch(e){return!1}},get_path:function(b,c,d){if(b=b.parents?b:this.get_node(b),!b||b.id===a.jstree.root||!b.parents)return!1;var e,f,g=[];for(g.push(d?b.id:b.text),e=0,f=b.parents.length;f>e;e++)g.push(d?b.parents[e]:this.get_text(b.parents[e]));return g=g.reverse().slice(1),c?g.join(c):g},get_next_dom:function(b,c){var d;if(b=this.get_node(b,!0),b[0]===this.element[0]){d=this._firstChild(this.get_container_ul()[0]);while(d&&0===d.offsetHeight)d=this._nextSibling(d);return d?a(d):!1}if(!b||!b.length)return!1;if(c){d=b[0];do d=this._nextSibling(d);while(d&&0===d.offsetHeight);return d?a(d):!1}if(b.hasClass("jstree-open")){d=this._firstChild(b.children(".jstree-children")[0]);while(d&&0===d.offsetHeight)d=this._nextSibling(d);if(null!==d)return a(d)}d=b[0];do d=this._nextSibling(d);while(d&&0===d.offsetHeight);return null!==d?a(d):b.parentsUntil(".jstree",".jstree-node").nextAll(".jstree-node:visible").first()},get_prev_dom:function(b,c){var d;if(b=this.get_node(b,!0),b[0]===this.element[0]){d=this.get_container_ul()[0].lastChild;while(d&&0===d.offsetHeight)d=this._previousSibling(d);return d?a(d):!1}if(!b||!b.length)return!1;if(c){d=b[0];do d=this._previousSibling(d);while(d&&0===d.offsetHeight);return d?a(d):!1}d=b[0];do d=this._previousSibling(d);while(d&&0===d.offsetHeight);if(null!==d){b=a(d);while(b.hasClass("jstree-open"))b=b.children(".jstree-children").first().children(".jstree-node:visible:last");return b}return d=b[0].parentNode.parentNode,d&&d.className&&-1!==d.className.indexOf("jstree-node")?a(d):!1},get_parent:function(b){return b=this.get_node(b),b&&b.id!==a.jstree.root?b.parent:!1},get_children_dom:function(a){return a=this.get_node(a,!0),a[0]===this.element[0]?this.get_container_ul().children(".jstree-node"):a&&a.length?a.children(".jstree-children").children(".jstree-node"):!1},is_parent:function(a){return a=this.get_node(a),a&&(a.state.loaded===!1||a.children.length>0)},is_loaded:function(a){return a=this.get_node(a),a&&a.state.loaded},is_loading:function(a){return a=this.get_node(a),a&&a.state&&a.state.loading},is_open:function(a){return a=this.get_node(a),a&&a.state.opened},is_closed:function(a){return a=this.get_node(a),a&&this.is_parent(a)&&!a.state.opened},is_leaf:function(a){return!this.is_parent(a)},load_node:function(b,c){var d,e,f,g,h;if(a.isArray(b))return this._load_nodes(b.slice(),c),!0;if(b=this.get_node(b),!b)return c&&c.call(this,b,!1),!1;if(b.state.loaded){for(b.state.loaded=!1,f=0,g=b.parents.length;g>f;f++)this._model.data[b.parents[f]].children_d=a.vakata.array_filter(this._model.data[b.parents[f]].children_d,function(c){return-1===a.inArray(c,b.children_d)});for(d=0,e=b.children_d.length;e>d;d++)this._model.data[b.children_d[d]].state.selected&&(h=!0),delete this._model.data[b.children_d[d]];h&&(this._data.core.selected=a.vakata.array_filter(this._data.core.selected,function(c){return-1===a.inArray(c,b.children_d)})),b.children=[],b.children_d=[],h&&this.trigger("changed",{action:"load_node",node:b,selected:this._data.core.selected})}return b.state.failed=!1,b.state.loading=!0,this.get_node(b,!0).addClass("jstree-loading").attr("aria-busy",!0),this._load_node(b,a.proxy(function(a){b=this._model.data[b.id],b.state.loading=!1,b.state.loaded=a,b.state.failed=!b.state.loaded;var d=this.get_node(b,!0),e=0,f=0,g=this._model.data,h=!1;for(e=0,f=b.children.length;f>e;e++)if(g[b.children[e]]&&!g[b.children[e]].state.hidden){h=!0;break}b.state.loaded&&d&&d.length&&(d.removeClass("jstree-closed jstree-open jstree-leaf"),h?"#"!==b.id&&d.addClass(b.state.opened?"jstree-open":"jstree-closed"):d.addClass("jstree-leaf")),d.removeClass("jstree-loading").attr("aria-busy",!1),this.trigger("load_node",{node:b,status:a}),c&&c.call(this,b,a)},this)),!0},_load_nodes:function(a,b,c,d){var e=!0,f=function(){this._load_nodes(a,b,!0)},g=this._model.data,h,i,j=[];for(h=0,i=a.length;i>h;h++)g[a[h]]&&(!g[a[h]].state.loaded&&!g[a[h]].state.failed||!c&&d)&&(this.is_loading(a[h])||this.load_node(a[h],f),e=!1);if(e){for(h=0,i=a.length;i>h;h++)g[a[h]]&&g[a[h]].state.loaded&&j.push(a[h]);b&&!b.done&&(b.call(this,j),b.done=!0)}},load_all:function(b,c){if(b||(b=a.jstree.root),b=this.get_node(b),!b)return!1;var d=[],e=this._model.data,f=e[b.id].children_d,g,h;for(b.state&&!b.state.loaded&&d.push(b.id),g=0,h=f.length;h>g;g++)e[f[g]]&&e[f[g]].state&&!e[f[g]].state.loaded&&d.push(f[g]);d.length?this._load_nodes(d,function(){this.load_all(b,c)}):(c&&c.call(this,b),this.trigger("load_all",{node:b}))},_load_node:function(b,c){var d=this.settings.core.data,e,f=function g(){return 3!==this.nodeType&&8!==this.nodeType};return d?a.isFunction(d)?d.call(this,b,a.proxy(function(d){d===!1?c.call(this,!1):this["string"==typeof d?"_append_html_data":"_append_json_data"](b,"string"==typeof d?a(a.parseHTML(d)).filter(f):d,function(a){c.call(this,a)})},this)):"object"==typeof d?d.url?(d=a.extend(!0,{},d),a.isFunction(d.url)&&(d.url=d.url.call(this,b)),a.isFunction(d.data)&&(d.data=d.data.call(this,b)),a.ajax(d).done(a.proxy(function(d,e,g){var h=g.getResponseHeader("Content-Type");return h&&-1!==h.indexOf("json")||"object"==typeof d?this._append_json_data(b,d,function(a){c.call(this,a)}):h&&-1!==h.indexOf("html")||"string"==typeof d?this._append_html_data(b,a(a.parseHTML(d)).filter(f),function(a){c.call(this,a)}):(this._data.core.last_error={error:"ajax",plugin:"core",id:"core_04",reason:"Could not load node",data:JSON.stringify({id:b.id,xhr:g})},this.settings.core.error.call(this,this._data.core.last_error),c.call(this,!1))},this)).fail(a.proxy(function(a){this._data.core.last_error={error:"ajax",plugin:"core",id:"core_04",reason:"Could not load node",data:JSON.stringify({id:b.id,xhr:a})},c.call(this,!1),this.settings.core.error.call(this,this._data.core.last_error)},this))):(e=a.isArray(d)?a.extend(!0,[],d):a.isPlainObject(d)?a.extend(!0,{},d):d,b.id===a.jstree.root?this._append_json_data(b,e,function(a){c.call(this,a)}):(this._data.core.last_error={error:"nodata",plugin:"core",id:"core_05",reason:"Could not load node",data:JSON.stringify({id:b.id})},this.settings.core.error.call(this,this._data.core.last_error),c.call(this,!1))):"string"==typeof d?b.id===a.jstree.root?this._append_html_data(b,a(a.parseHTML(d)).filter(f),function(a){c.call(this,a)}):(this._data.core.last_error={error:"nodata",plugin:"core",id:"core_06",reason:"Could not load node",data:JSON.stringify({id:b.id})},this.settings.core.error.call(this,this._data.core.last_error),c.call(this,!1)):c.call(this,!1):b.id===a.jstree.root?this._append_html_data(b,this._data.core.original_container_html.clone(!0),function(a){c.call(this,a)}):c.call(this,!1)},_node_changed:function(b){b=this.get_node(b),b&&-1===a.inArray(b.id,this._model.changed)&&this._model.changed.push(b.id)},_append_html_data:function(b,c,d){b=this.get_node(b),b.children=[],b.children_d=[];var e=c.is("ul")?c.children():c,f=b.id,g=[],h=[],i=this._model.data,j=i[f],k=this._data.core.selected.length,l,m,n;for(e.each(a.proxy(function(b,c){l=this._parse_model_from_html(a(c),f,j.parents.concat()),l&&(g.push(l),h.push(l),i[l].children_d.length&&(h=h.concat(i[l].children_d)))},this)),j.children=g,j.children_d=h,m=0,n=j.parents.length;n>m;m++)i[j.parents[m]].children_d=i[j.parents[m]].children_d.concat(h);this.trigger("model",{nodes:h,parent:f}),f!==a.jstree.root?(this._node_changed(f),this.redraw()):(this.get_container_ul().children(".jstree-initial-node").remove(),this.redraw(!0)),this._data.core.selected.length!==k&&this.trigger("changed",{action:"model",selected:this._data.core.selected}),d.call(this,!0)},_append_json_data:function(b,c,d,e){if(null!==this.element){b=this.get_node(b),b.children=[],b.children_d=[],c.d&&(c=c.d,"string"==typeof c&&(c=JSON.parse(c))),a.isArray(c)||(c=[c]);var f=null,g={df:this._model.default_state,dat:c,par:b.id,m:this._model.data,t_id:this._id,t_cnt:this._cnt,sel:this._data.core.selected},h=this,i=function(a,b){a.data&&(a=a.data);var c=a.dat,d=a.par,e=[],f=[],g=[],i=a.df,j=a.t_id,k=a.t_cnt,l=a.m,m=l[d],n=a.sel,o,p,q,r,s=function(a,c,d){d=d?d.concat():[],c&&d.unshift(c);var e=a.id.toString(),f,h,j,k,m={id:e,text:a.text||"",icon:a.icon!==b?a.icon:!0,parent:c,parents:d,children:a.children||[],children_d:a.children_d||[],data:a.data,state:{},li_attr:{id:!1},a_attr:{href:"#"},original:!1};for(f in i)i.hasOwnProperty(f)&&(m.state[f]=i[f]);if(a&&a.data&&a.data.jstree&&a.data.jstree.icon&&(m.icon=a.data.jstree.icon),(m.icon===b||null===m.icon||""===m.icon)&&(m.icon=!0),a&&a.data&&(m.data=a.data,a.data.jstree))for(f in a.data.jstree)a.data.jstree.hasOwnProperty(f)&&(m.state[f]=a.data.jstree[f]);if(a&&"object"==typeof a.state)for(f in a.state)a.state.hasOwnProperty(f)&&(m.state[f]=a.state[f]);if(a&&"object"==typeof a.li_attr)for(f in a.li_attr)a.li_attr.hasOwnProperty(f)&&(m.li_attr[f]=a.li_attr[f]);if(m.li_attr.id||(m.li_attr.id=e),a&&"object"==typeof a.a_attr)for(f in a.a_attr)a.a_attr.hasOwnProperty(f)&&(m.a_attr[f]=a.a_attr[f]);for(a&&a.children&&a.children===!0&&(m.state.loaded=!1,m.children=[],m.children_d=[]),l[m.id]=m,f=0,h=m.children.length;h>f;f++)j=s(l[m.children[f]],m.id,d),k=l[j],m.children_d.push(j),k.children_d.length&&(m.children_d=m.children_d.concat(k.children_d));return delete a.data,delete a.children,l[m.id].original=a,m.state.selected&&g.push(m.id),m.id},t=function(a,c,d){d=d?d.concat():[],c&&d.unshift(c);var e=!1,f,h,m,n,o;do e="j"+j+"_"+ ++k;while(l[e]);o={id:!1,text:"string"==typeof a?a:"",icon:"object"==typeof a&&a.icon!==b?a.icon:!0,parent:c,parents:d,children:[],children_d:[],data:null,state:{},li_attr:{id:!1},a_attr:{href:"#"},original:!1};for(f in i)i.hasOwnProperty(f)&&(o.state[f]=i[f]);if(a&&a.id&&(o.id=a.id.toString()),a&&a.text&&(o.text=a.text),a&&a.data&&a.data.jstree&&a.data.jstree.icon&&(o.icon=a.data.jstree.icon),(o.icon===b||null===o.icon||""===o.icon)&&(o.icon=!0),a&&a.data&&(o.data=a.data,a.data.jstree))for(f in a.data.jstree)a.data.jstree.hasOwnProperty(f)&&(o.state[f]=a.data.jstree[f]);if(a&&"object"==typeof a.state)for(f in a.state)a.state.hasOwnProperty(f)&&(o.state[f]=a.state[f]);if(a&&"object"==typeof a.li_attr)for(f in a.li_attr)a.li_attr.hasOwnProperty(f)&&(o.li_attr[f]=a.li_attr[f]);if(o.li_attr.id&&!o.id&&(o.id=o.li_attr.id.toString()),o.id||(o.id=e),o.li_attr.id||(o.li_attr.id=o.id),a&&"object"==typeof a.a_attr)for(f in a.a_attr)a.a_attr.hasOwnProperty(f)&&(o.a_attr[f]=a.a_attr[f]);if(a&&a.children&&a.children.length){for(f=0,h=a.children.length;h>f;f++)m=t(a.children[f],o.id,d),n=l[m],o.children.push(m),n.children_d.length&&(o.children_d=o.children_d.concat(n.children_d));o.children_d=o.children_d.concat(o.children)}return a&&a.children&&a.children===!0&&(o.state.loaded=!1,o.children=[],o.children_d=[]),delete a.data,delete a.children,o.original=a,l[o.id]=o,o.state.selected&&g.push(o.id),o.id};if(c.length&&c[0].id!==b&&c[0].parent!==b){for(p=0,q=c.length;q>p;p++)c[p].children||(c[p].children=[]),c[p].state||(c[p].state={}),l[c[p].id.toString()]=c[p];for(p=0,q=c.length;q>p;p++)l[c[p].parent.toString()]?(l[c[p].parent.toString()].children.push(c[p].id.toString()),m.children_d.push(c[p].id.toString())):"undefined"!=typeof h&&(h._data.core.last_error={error:"parse",plugin:"core",id:"core_07",reason:"Node with invalid parent",data:JSON.stringify({id:c[p].id.toString(),parent:c[p].parent.toString()})},h.settings.core.error.call(h,h._data.core.last_error));for(p=0,q=m.children.length;q>p;p++)o=s(l[m.children[p]],d,m.parents.concat()),f.push(o),l[o].children_d.length&&(f=f.concat(l[o].children_d));for(p=0,q=m.parents.length;q>p;p++)l[m.parents[p]].children_d=l[m.parents[p]].children_d.concat(f);r={cnt:k,mod:l,sel:n,par:d,dpc:f,add:g}}else{for(p=0,q=c.length;q>p;p++)o=t(c[p],d,m.parents.concat()),o&&(e.push(o),f.push(o),l[o].children_d.length&&(f=f.concat(l[o].children_d)));for(m.children=e,m.children_d=f,p=0,q=m.parents.length;q>p;p++)l[m.parents[p]].children_d=l[m.parents[p]].children_d.concat(f);r={cnt:k,mod:l,sel:n,par:d,dpc:f,add:g}}return"undefined"!=typeof window&&"undefined"!=typeof window.document?r:void postMessage(r)},j=function(b,c){if(null!==this.element){this._cnt=b.cnt;var e,f=this._model.data;for(e in f)f.hasOwnProperty(e)&&f[e].state&&f[e].state.loading&&b.mod[e]&&(b.mod[e].state.loading=!0);if(this._model.data=b.mod,c){var g,h=b.add,i=b.sel,j=this._data.core.selected.slice();if(f=this._model.data,i.length!==j.length||a.vakata.array_unique(i.concat(j)).length!==i.length){for(e=0,g=i.length;g>e;e++)-1===a.inArray(i[e],h)&&-1===a.inArray(i[e],j)&&(f[i[e]].state.selected=!1);for(e=0,g=j.length;g>e;e++)-1===a.inArray(j[e],i)&&(f[j[e]].state.selected=!0)}}b.add.length&&(this._data.core.selected=this._data.core.selected.concat(b.add)),this.trigger("model",{nodes:b.dpc,parent:b.par}),b.par!==a.jstree.root?(this._node_changed(b.par),this.redraw()):this.redraw(!0),b.add.length&&this.trigger("changed",{action:"model",selected:this._data.core.selected}),d.call(this,!0)}};if(this.settings.core.worker&&window.Blob&&window.URL&&window.Worker)try{null===this._wrk&&(this._wrk=window.URL.createObjectURL(new window.Blob(["self.onmessage = "+i.toString()],{type:"text/javascript"}))),!this._data.core.working||e?(this._data.core.working=!0,f=new window.Worker(this._wrk),f.onmessage=a.proxy(function(a){j.call(this,a.data,!0);try{f.terminate(),f=null}catch(b){}this._data.core.worker_queue.length?this._append_json_data.apply(this,this._data.core.worker_queue.shift()):this._data.core.working=!1},this),g.par?f.postMessage(g):this._data.core.worker_queue.length?this._append_json_data.apply(this,this._data.core.worker_queue.shift()):this._data.core.working=!1):this._data.core.worker_queue.push([b,c,d,!0])}catch(k){j.call(this,i(g),!1),this._data.core.worker_queue.length?this._append_json_data.apply(this,this._data.core.worker_queue.shift()):this._data.core.working=!1}else j.call(this,i(g),!1)}},_parse_model_from_html:function(c,d,e){e=e?[].concat(e):[],d&&e.unshift(d);var f,g,h=this._model.data,i={id:!1,text:!1,icon:!0,parent:d,parents:e,children:[],children_d:[],data:null,state:{},li_attr:{id:!1},a_attr:{href:"#"},original:!1},j,k,l;for(j in this._model.default_state)this._model.default_state.hasOwnProperty(j)&&(i.state[j]=this._model.default_state[j]);if(k=a.vakata.attributes(c,!0),a.each(k,function(b,c){return c=a.trim(c),c.length?(i.li_attr[b]=c,void("id"===b&&(i.id=c.toString()))):!0}),k=c.children("a").first(),k.length&&(k=a.vakata.attributes(k,!0),a.each(k,function(b,c){c=a.trim(c),c.length&&(i.a_attr[b]=c)})),k=c.children("a").first().length?c.children("a").first().clone():c.clone(),k.children("ins, i, ul").remove(),k=k.html(),k=a("<div />").html(k),i.text=this.settings.core.force_text?k.text():k.html(),k=c.data(),i.data=k?a.extend(!0,{},k):null,i.state.opened=c.hasClass("jstree-open"),i.state.selected=c.children("a").hasClass("jstree-clicked"),i.state.disabled=c.children("a").hasClass("jstree-disabled"),i.data&&i.data.jstree)for(j in i.data.jstree)i.data.jstree.hasOwnProperty(j)&&(i.state[j]=i.data.jstree[j]);k=c.children("a").children(".jstree-themeicon"),k.length&&(i.icon=k.hasClass("jstree-themeicon-hidden")?!1:k.attr("rel")),i.state.icon!==b&&(i.icon=i.state.icon),(i.icon===b||null===i.icon||""===i.icon)&&(i.icon=!0),k=c.children("ul").children("li");do l="j"+this._id+"_"+ ++this._cnt;while(h[l]);return i.id=i.li_attr.id?i.li_attr.id.toString():l,k.length?(k.each(a.proxy(function(b,c){f=this._parse_model_from_html(a(c),i.id,e),g=this._model.data[f],i.children.push(f),g.children_d.length&&(i.children_d=i.children_d.concat(g.children_d))},this)),i.children_d=i.children_d.concat(i.children)):c.hasClass("jstree-closed")&&(i.state.loaded=!1),i.li_attr["class"]&&(i.li_attr["class"]=i.li_attr["class"].replace("jstree-closed","").replace("jstree-open","")),i.a_attr["class"]&&(i.a_attr["class"]=i.a_attr["class"].replace("jstree-clicked","").replace("jstree-disabled","")),h[i.id]=i,i.state.selected&&this._data.core.selected.push(i.id),i.id},_parse_model_from_flat_json:function(a,c,d){d=d?d.concat():[],c&&d.unshift(c);var e=a.id.toString(),f=this._model.data,g=this._model.default_state,h,i,j,k,l={id:e,text:a.text||"",icon:a.icon!==b?a.icon:!0,parent:c,parents:d,children:a.children||[],children_d:a.children_d||[],data:a.data,state:{},li_attr:{id:!1},a_attr:{href:"#"},original:!1};for(h in g)g.hasOwnProperty(h)&&(l.state[h]=g[h]);if(a&&a.data&&a.data.jstree&&a.data.jstree.icon&&(l.icon=a.data.jstree.icon),(l.icon===b||null===l.icon||""===l.icon)&&(l.icon=!0),a&&a.data&&(l.data=a.data,a.data.jstree))for(h in a.data.jstree)a.data.jstree.hasOwnProperty(h)&&(l.state[h]=a.data.jstree[h]);if(a&&"object"==typeof a.state)for(h in a.state)a.state.hasOwnProperty(h)&&(l.state[h]=a.state[h]);if(a&&"object"==typeof a.li_attr)for(h in a.li_attr)a.li_attr.hasOwnProperty(h)&&(l.li_attr[h]=a.li_attr[h]);if(l.li_attr.id||(l.li_attr.id=e),a&&"object"==typeof a.a_attr)for(h in a.a_attr)a.a_attr.hasOwnProperty(h)&&(l.a_attr[h]=a.a_attr[h]);for(a&&a.children&&a.children===!0&&(l.state.loaded=!1,l.children=[],l.children_d=[]),f[l.id]=l,h=0,i=l.children.length;i>h;h++)j=this._parse_model_from_flat_json(f[l.children[h]],l.id,d),k=f[j],l.children_d.push(j),k.children_d.length&&(l.children_d=l.children_d.concat(k.children_d));return delete a.data,delete a.children,f[l.id].original=a,l.state.selected&&this._data.core.selected.push(l.id),l.id},_parse_model_from_json:function(a,c,d){d=d?d.concat():[],c&&d.unshift(c);var e=!1,f,g,h,i,j=this._model.data,k=this._model.default_state,l;do e="j"+this._id+"_"+ ++this._cnt;while(j[e]);l={id:!1,text:"string"==typeof a?a:"",icon:"object"==typeof a&&a.icon!==b?a.icon:!0,parent:c,parents:d,children:[],children_d:[],data:null,state:{},li_attr:{id:!1},a_attr:{href:"#"},original:!1};for(f in k)k.hasOwnProperty(f)&&(l.state[f]=k[f]);if(a&&a.id&&(l.id=a.id.toString()),a&&a.text&&(l.text=a.text),a&&a.data&&a.data.jstree&&a.data.jstree.icon&&(l.icon=a.data.jstree.icon),(l.icon===b||null===l.icon||""===l.icon)&&(l.icon=!0),a&&a.data&&(l.data=a.data,a.data.jstree))for(f in a.data.jstree)a.data.jstree.hasOwnProperty(f)&&(l.state[f]=a.data.jstree[f]);if(a&&"object"==typeof a.state)for(f in a.state)a.state.hasOwnProperty(f)&&(l.state[f]=a.state[f]);if(a&&"object"==typeof a.li_attr)for(f in a.li_attr)a.li_attr.hasOwnProperty(f)&&(l.li_attr[f]=a.li_attr[f]);
if(l.li_attr.id&&!l.id&&(l.id=l.li_attr.id.toString()),l.id||(l.id=e),l.li_attr.id||(l.li_attr.id=l.id),a&&"object"==typeof a.a_attr)for(f in a.a_attr)a.a_attr.hasOwnProperty(f)&&(l.a_attr[f]=a.a_attr[f]);if(a&&a.children&&a.children.length){for(f=0,g=a.children.length;g>f;f++)h=this._parse_model_from_json(a.children[f],l.id,d),i=j[h],l.children.push(h),i.children_d.length&&(l.children_d=l.children_d.concat(i.children_d));l.children_d=l.children.concat(l.children_d)}return a&&a.children&&a.children===!0&&(l.state.loaded=!1,l.children=[],l.children_d=[]),delete a.data,delete a.children,l.original=a,j[l.id]=l,l.state.selected&&this._data.core.selected.push(l.id),l.id},_redraw:function(){var b=this._model.force_full_redraw?this._model.data[a.jstree.root].children.concat([]):this._model.changed.concat([]),c=i.createElement("UL"),d,e,f,g=this._data.core.focused;for(e=0,f=b.length;f>e;e++)d=this.redraw_node(b[e],!0,this._model.force_full_redraw),d&&this._model.force_full_redraw&&c.appendChild(d);this._model.force_full_redraw&&(c.className=this.get_container_ul()[0].className,c.setAttribute("role","group"),this.element.empty().append(c)),null!==g&&this.settings.core.restore_focus&&(d=this.get_node(g,!0),d&&d.length&&d.children(".jstree-anchor")[0]!==i.activeElement?d.children(".jstree-anchor").focus():this._data.core.focused=null),this._model.force_full_redraw=!1,this._model.changed=[],this.trigger("redraw",{nodes:b})},redraw:function(a){a&&(this._model.force_full_redraw=!0),this._redraw()},draw_children:function(b){var c=this.get_node(b),d=!1,e=!1,f=!1,g=i;if(!c)return!1;if(c.id===a.jstree.root)return this.redraw(!0);if(b=this.get_node(b,!0),!b||!b.length)return!1;if(b.children(".jstree-children").remove(),b=b[0],c.children.length&&c.state.loaded){for(f=g.createElement("UL"),f.setAttribute("role","group"),f.className="jstree-children",d=0,e=c.children.length;e>d;d++)f.appendChild(this.redraw_node(c.children[d],!0,!0));b.appendChild(f)}},redraw_node:function(b,c,d,e){var f=this.get_node(b),g=!1,h=!1,j=!1,k=!1,l=!1,m=!1,n="",o=i,p=this._model.data,q=!1,r=!1,s=null,t=0,u=0,v=!1,w=!1;if(!f)return!1;if(f.id===a.jstree.root)return this.redraw(!0);if(c=c||0===f.children.length,b=i.querySelector?this.element[0].querySelector("#"+(-1!=="0123456789".indexOf(f.id[0])?"\\3"+f.id[0]+" "+f.id.substr(1).replace(a.jstree.idregex,"\\$&"):f.id.replace(a.jstree.idregex,"\\$&"))):i.getElementById(f.id))b=a(b),d||(g=b.parent().parent()[0],g===this.element[0]&&(g=null),h=b.index()),c||!f.children.length||b.children(".jstree-children").length||(c=!0),c||(j=b.children(".jstree-children")[0]),q=b.children(".jstree-anchor")[0]===i.activeElement,b.remove();else if(c=!0,!d){if(g=f.parent!==a.jstree.root?a("#"+f.parent.replace(a.jstree.idregex,"\\$&"),this.element)[0]:null,!(null===g||g&&p[f.parent].state.opened))return!1;h=a.inArray(f.id,null===g?p[a.jstree.root].children:p[f.parent].children)}b=this._data.core.node.cloneNode(!0),n="jstree-node ";for(k in f.li_attr)if(f.li_attr.hasOwnProperty(k)){if("id"===k)continue;"class"!==k?b.setAttribute(k,f.li_attr[k]):n+=f.li_attr[k]}for(f.a_attr.id||(f.a_attr.id=f.id+"_anchor"),b.setAttribute("aria-selected",!!f.state.selected),b.setAttribute("aria-level",f.parents.length),b.setAttribute("aria-labelledby",f.a_attr.id),f.state.disabled&&b.setAttribute("aria-disabled",!0),k=0,l=f.children.length;l>k;k++)if(!p[f.children[k]].state.hidden){v=!0;break}if(null!==f.parent&&p[f.parent]&&!f.state.hidden&&(k=a.inArray(f.id,p[f.parent].children),w=f.id,-1!==k))for(k++,l=p[f.parent].children.length;l>k;k++)if(p[p[f.parent].children[k]].state.hidden||(w=p[f.parent].children[k]),w!==f.id)break;f.state.hidden&&(n+=" jstree-hidden"),f.state.loading&&(n+=" jstree-loading"),f.state.loaded&&!v?n+=" jstree-leaf":(n+=f.state.opened&&f.state.loaded?" jstree-open":" jstree-closed",b.setAttribute("aria-expanded",f.state.opened&&f.state.loaded)),w===f.id&&(n+=" jstree-last"),b.id=f.id,b.className=n,n=(f.state.selected?" jstree-clicked":"")+(f.state.disabled?" jstree-disabled":"");for(l in f.a_attr)if(f.a_attr.hasOwnProperty(l)){if("href"===l&&"#"===f.a_attr[l])continue;"class"!==l?b.childNodes[1].setAttribute(l,f.a_attr[l]):n+=" "+f.a_attr[l]}if(n.length&&(b.childNodes[1].className="jstree-anchor "+n),(f.icon&&f.icon!==!0||f.icon===!1)&&(f.icon===!1?b.childNodes[1].childNodes[0].className+=" jstree-themeicon-hidden":-1===f.icon.indexOf("/")&&-1===f.icon.indexOf(".")?b.childNodes[1].childNodes[0].className+=" "+f.icon+" jstree-themeicon-custom":(b.childNodes[1].childNodes[0].style.backgroundImage='url("'+f.icon+'")',b.childNodes[1].childNodes[0].style.backgroundPosition="center center",b.childNodes[1].childNodes[0].style.backgroundSize="auto",b.childNodes[1].childNodes[0].className+=" jstree-themeicon-custom")),this.settings.core.force_text?b.childNodes[1].appendChild(o.createTextNode(f.text)):b.childNodes[1].innerHTML+=f.text,c&&f.children.length&&(f.state.opened||e)&&f.state.loaded){for(m=o.createElement("UL"),m.setAttribute("role","group"),m.className="jstree-children",k=0,l=f.children.length;l>k;k++)m.appendChild(this.redraw_node(f.children[k],c,!0));b.appendChild(m)}if(j&&b.appendChild(j),!d){for(g||(g=this.element[0]),k=0,l=g.childNodes.length;l>k;k++)if(g.childNodes[k]&&g.childNodes[k].className&&-1!==g.childNodes[k].className.indexOf("jstree-children")){s=g.childNodes[k];break}s||(s=o.createElement("UL"),s.setAttribute("role","group"),s.className="jstree-children",g.appendChild(s)),g=s,h<g.childNodes.length?g.insertBefore(b,g.childNodes[h]):g.appendChild(b),q&&(t=this.element[0].scrollTop,u=this.element[0].scrollLeft,b.childNodes[1].focus(),this.element[0].scrollTop=t,this.element[0].scrollLeft=u)}return f.state.opened&&!f.state.loaded&&(f.state.opened=!1,setTimeout(a.proxy(function(){this.open_node(f.id,!1,0)},this),0)),b},open_node:function(c,d,e){var f,g,h,i;if(a.isArray(c)){for(c=c.slice(),f=0,g=c.length;g>f;f++)this.open_node(c[f],d,e);return!0}return c=this.get_node(c),c&&c.id!==a.jstree.root?(e=e===b?this.settings.core.animation:e,this.is_closed(c)?this.is_loaded(c)?(h=this.get_node(c,!0),i=this,h.length&&(e&&h.children(".jstree-children").length&&h.children(".jstree-children").stop(!0,!0),c.children.length&&!this._firstChild(h.children(".jstree-children")[0])&&this.draw_children(c),e?(this.trigger("before_open",{node:c}),h.children(".jstree-children").css("display","none").end().removeClass("jstree-closed").addClass("jstree-open").attr("aria-expanded",!0).children(".jstree-children").stop(!0,!0).slideDown(e,function(){this.style.display="",i.element&&i.trigger("after_open",{node:c})})):(this.trigger("before_open",{node:c}),h[0].className=h[0].className.replace("jstree-closed","jstree-open"),h[0].setAttribute("aria-expanded",!0))),c.state.opened=!0,d&&d.call(this,c,!0),h.length||this.trigger("before_open",{node:c}),this.trigger("open_node",{node:c}),e&&h.length||this.trigger("after_open",{node:c}),!0):this.is_loading(c)?setTimeout(a.proxy(function(){this.open_node(c,d,e)},this),500):void this.load_node(c,function(a,b){return b?this.open_node(a,d,e):d?d.call(this,a,!1):!1}):(d&&d.call(this,c,!1),!1)):!1},_open_to:function(b){if(b=this.get_node(b),!b||b.id===a.jstree.root)return!1;var c,d,e=b.parents;for(c=0,d=e.length;d>c;c+=1)c!==a.jstree.root&&this.open_node(e[c],!1,0);return a("#"+b.id.replace(a.jstree.idregex,"\\$&"),this.element)},close_node:function(c,d){var e,f,g,h;if(a.isArray(c)){for(c=c.slice(),e=0,f=c.length;f>e;e++)this.close_node(c[e],d);return!0}return c=this.get_node(c),c&&c.id!==a.jstree.root?this.is_closed(c)?!1:(d=d===b?this.settings.core.animation:d,g=this,h=this.get_node(c,!0),c.state.opened=!1,this.trigger("close_node",{node:c}),void(h.length?d?h.children(".jstree-children").attr("style","display:block !important").end().removeClass("jstree-open").addClass("jstree-closed").attr("aria-expanded",!1).children(".jstree-children").stop(!0,!0).slideUp(d,function(){this.style.display="",h.children(".jstree-children").remove(),g.element&&g.trigger("after_close",{node:c})}):(h[0].className=h[0].className.replace("jstree-open","jstree-closed"),h.attr("aria-expanded",!1).children(".jstree-children").remove(),this.trigger("after_close",{node:c})):this.trigger("after_close",{node:c}))):!1},toggle_node:function(b){var c,d;if(a.isArray(b)){for(b=b.slice(),c=0,d=b.length;d>c;c++)this.toggle_node(b[c]);return!0}return this.is_closed(b)?this.open_node(b):this.is_open(b)?this.close_node(b):void 0},open_all:function(b,c,d){if(b||(b=a.jstree.root),b=this.get_node(b),!b)return!1;var e=b.id===a.jstree.root?this.get_container_ul():this.get_node(b,!0),f,g,h;if(!e.length){for(f=0,g=b.children_d.length;g>f;f++)this.is_closed(this._model.data[b.children_d[f]])&&(this._model.data[b.children_d[f]].state.opened=!0);return this.trigger("open_all",{node:b})}d=d||e,h=this,e=this.is_closed(b)?e.find(".jstree-closed").addBack():e.find(".jstree-closed"),e.each(function(){h.open_node(this,function(a,b){b&&this.is_parent(a)&&this.open_all(a,c,d)},c||0)}),0===d.find(".jstree-closed").length&&this.trigger("open_all",{node:this.get_node(d)})},close_all:function(b,c){if(b||(b=a.jstree.root),b=this.get_node(b),!b)return!1;var d=b.id===a.jstree.root?this.get_container_ul():this.get_node(b,!0),e=this,f,g;for(d.length&&(d=this.is_open(b)?d.find(".jstree-open").addBack():d.find(".jstree-open"),a(d.get().reverse()).each(function(){e.close_node(this,c||0)})),f=0,g=b.children_d.length;g>f;f++)this._model.data[b.children_d[f]].state.opened=!1;this.trigger("close_all",{node:b})},is_disabled:function(a){return a=this.get_node(a),a&&a.state&&a.state.disabled},enable_node:function(b){var c,d;if(a.isArray(b)){for(b=b.slice(),c=0,d=b.length;d>c;c++)this.enable_node(b[c]);return!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?(b.state.disabled=!1,this.get_node(b,!0).children(".jstree-anchor").removeClass("jstree-disabled").attr("aria-disabled",!1),void this.trigger("enable_node",{node:b})):!1},disable_node:function(b){var c,d;if(a.isArray(b)){for(b=b.slice(),c=0,d=b.length;d>c;c++)this.disable_node(b[c]);return!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?(b.state.disabled=!0,this.get_node(b,!0).children(".jstree-anchor").addClass("jstree-disabled").attr("aria-disabled",!0),void this.trigger("disable_node",{node:b})):!1},is_hidden:function(a){return a=this.get_node(a),a.state.hidden===!0},hide_node:function(b,c){var d,e;if(a.isArray(b)){for(b=b.slice(),d=0,e=b.length;e>d;d++)this.hide_node(b[d],!0);return c||this.redraw(),!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?void(b.state.hidden||(b.state.hidden=!0,this._node_changed(b.parent),c||this.redraw(),this.trigger("hide_node",{node:b}))):!1},show_node:function(b,c){var d,e;if(a.isArray(b)){for(b=b.slice(),d=0,e=b.length;e>d;d++)this.show_node(b[d],!0);return c||this.redraw(),!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?void(b.state.hidden&&(b.state.hidden=!1,this._node_changed(b.parent),c||this.redraw(),this.trigger("show_node",{node:b}))):!1},hide_all:function(b){var c,d=this._model.data,e=[];for(c in d)d.hasOwnProperty(c)&&c!==a.jstree.root&&!d[c].state.hidden&&(d[c].state.hidden=!0,e.push(c));return this._model.force_full_redraw=!0,b||this.redraw(),this.trigger("hide_all",{nodes:e}),e},show_all:function(b){var c,d=this._model.data,e=[];for(c in d)d.hasOwnProperty(c)&&c!==a.jstree.root&&d[c].state.hidden&&(d[c].state.hidden=!1,e.push(c));return this._model.force_full_redraw=!0,b||this.redraw(),this.trigger("show_all",{nodes:e}),e},activate_node:function(a,c){if(this.is_disabled(a))return!1;if(c&&"object"==typeof c||(c={}),this._data.core.last_clicked=this._data.core.last_clicked&&this._data.core.last_clicked.id!==b?this.get_node(this._data.core.last_clicked.id):null,this._data.core.last_clicked&&!this._data.core.last_clicked.state.selected&&(this._data.core.last_clicked=null),!this._data.core.last_clicked&&this._data.core.selected.length&&(this._data.core.last_clicked=this.get_node(this._data.core.selected[this._data.core.selected.length-1])),this.settings.core.multiple&&(c.metaKey||c.ctrlKey||c.shiftKey)&&(!c.shiftKey||this._data.core.last_clicked&&this.get_parent(a)&&this.get_parent(a)===this._data.core.last_clicked.parent))if(c.shiftKey){var d=this.get_node(a).id,e=this._data.core.last_clicked.id,f=this.get_node(this._data.core.last_clicked.parent).children,g=!1,h,i;for(h=0,i=f.length;i>h;h+=1)f[h]===d&&(g=!g),f[h]===e&&(g=!g),this.is_disabled(f[h])||!g&&f[h]!==d&&f[h]!==e?this.deselect_node(f[h],!0,c):this.is_hidden(f[h])||this.select_node(f[h],!0,!1,c);this.trigger("changed",{action:"select_node",node:this.get_node(a),selected:this._data.core.selected,event:c})}else this.is_selected(a)?this.deselect_node(a,!1,c):this.select_node(a,!1,!1,c);else!this.settings.core.multiple&&(c.metaKey||c.ctrlKey||c.shiftKey)&&this.is_selected(a)?this.deselect_node(a,!1,c):(this.deselect_all(!0),this.select_node(a,!1,!1,c),this._data.core.last_clicked=this.get_node(a));this.trigger("activate_node",{node:this.get_node(a),event:c})},hover_node:function(a){if(a=this.get_node(a,!0),!a||!a.length||a.children(".jstree-hovered").length)return!1;var b=this.element.find(".jstree-hovered"),c=this.element;b&&b.length&&this.dehover_node(b),a.children(".jstree-anchor").addClass("jstree-hovered"),this.trigger("hover_node",{node:this.get_node(a)}),setTimeout(function(){c.attr("aria-activedescendant",a[0].id)},0)},dehover_node:function(a){return a=this.get_node(a,!0),a&&a.length&&a.children(".jstree-hovered").length?(a.children(".jstree-anchor").removeClass("jstree-hovered"),void this.trigger("dehover_node",{node:this.get_node(a)})):!1},select_node:function(b,c,d,e){var f,g,h,i;if(a.isArray(b)){for(b=b.slice(),g=0,h=b.length;h>g;g++)this.select_node(b[g],c,d,e);return!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?(f=this.get_node(b,!0),void(b.state.selected||(b.state.selected=!0,this._data.core.selected.push(b.id),d||(f=this._open_to(b)),f&&f.length&&f.attr("aria-selected",!0).children(".jstree-anchor").addClass("jstree-clicked"),this.trigger("select_node",{node:b,selected:this._data.core.selected,event:e}),c||this.trigger("changed",{action:"select_node",node:b,selected:this._data.core.selected,event:e})))):!1},deselect_node:function(b,c,d){var e,f,g;if(a.isArray(b)){for(b=b.slice(),e=0,f=b.length;f>e;e++)this.deselect_node(b[e],c,d);return!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?(g=this.get_node(b,!0),void(b.state.selected&&(b.state.selected=!1,this._data.core.selected=a.vakata.array_remove_item(this._data.core.selected,b.id),g.length&&g.attr("aria-selected",!1).children(".jstree-anchor").removeClass("jstree-clicked"),this.trigger("deselect_node",{node:b,selected:this._data.core.selected,event:d}),c||this.trigger("changed",{action:"deselect_node",node:b,selected:this._data.core.selected,event:d})))):!1},select_all:function(b){var c=this._data.core.selected.concat([]),d,e;for(this._data.core.selected=this._model.data[a.jstree.root].children_d.concat(),d=0,e=this._data.core.selected.length;e>d;d++)this._model.data[this._data.core.selected[d]]&&(this._model.data[this._data.core.selected[d]].state.selected=!0);this.redraw(!0),this.trigger("select_all",{selected:this._data.core.selected}),b||this.trigger("changed",{action:"select_all",selected:this._data.core.selected,old_selection:c})},deselect_all:function(a){var b=this._data.core.selected.concat([]),c,d;for(c=0,d=this._data.core.selected.length;d>c;c++)this._model.data[this._data.core.selected[c]]&&(this._model.data[this._data.core.selected[c]].state.selected=!1);this._data.core.selected=[],this.element.find(".jstree-clicked").removeClass("jstree-clicked").parent().attr("aria-selected",!1),this.trigger("deselect_all",{selected:this._data.core.selected,node:b}),a||this.trigger("changed",{action:"deselect_all",selected:this._data.core.selected,old_selection:b})},is_selected:function(b){return b=this.get_node(b),b&&b.id!==a.jstree.root?b.state.selected:!1},get_selected:function(b){return b?a.map(this._data.core.selected,a.proxy(function(a){return this.get_node(a)},this)):this._data.core.selected.slice()},get_top_selected:function(b){var c=this.get_selected(!0),d={},e,f,g,h;for(e=0,f=c.length;f>e;e++)d[c[e].id]=c[e];for(e=0,f=c.length;f>e;e++)for(g=0,h=c[e].children_d.length;h>g;g++)d[c[e].children_d[g]]&&delete d[c[e].children_d[g]];c=[];for(e in d)d.hasOwnProperty(e)&&c.push(e);return b?a.map(c,a.proxy(function(a){return this.get_node(a)},this)):c},get_bottom_selected:function(b){var c=this.get_selected(!0),d=[],e,f;for(e=0,f=c.length;f>e;e++)c[e].children.length||d.push(c[e].id);return b?a.map(d,a.proxy(function(a){return this.get_node(a)},this)):d},get_state:function(){var b={core:{open:[],loaded:[],scroll:{left:this.element.scrollLeft(),top:this.element.scrollTop()},selected:[]}},c;for(c in this._model.data)this._model.data.hasOwnProperty(c)&&c!==a.jstree.root&&(this._model.data[c].state.loaded&&this.settings.core.loaded_state&&b.core.loaded.push(c),this._model.data[c].state.opened&&b.core.open.push(c),this._model.data[c].state.selected&&b.core.selected.push(c));return b},set_state:function(c,d){if(c){if(c.core&&c.core.selected&&c.core.initial_selection===b&&(c.core.initial_selection=this._data.core.selected.concat([]).sort().join(",")),c.core){var e,f,g,h,i;if(c.core.loaded)return this.settings.core.loaded_state&&a.isArray(c.core.loaded)&&c.core.loaded.length?this._load_nodes(c.core.loaded,function(a){delete c.core.loaded,this.set_state(c,d)}):(delete c.core.loaded,this.set_state(c,d)),!1;if(c.core.open)return a.isArray(c.core.open)&&c.core.open.length?this._load_nodes(c.core.open,function(a){this.open_node(a,!1,0),delete c.core.open,this.set_state(c,d)}):(delete c.core.open,this.set_state(c,d)),!1;if(c.core.scroll)return c.core.scroll&&c.core.scroll.left!==b&&this.element.scrollLeft(c.core.scroll.left),c.core.scroll&&c.core.scroll.top!==b&&this.element.scrollTop(c.core.scroll.top),delete c.core.scroll,this.set_state(c,d),!1;if(c.core.selected)return h=this,(c.core.initial_selection===b||c.core.initial_selection===this._data.core.selected.concat([]).sort().join(","))&&(this.deselect_all(),a.each(c.core.selected,function(a,b){h.select_node(b,!1,!0)})),delete c.core.initial_selection,delete c.core.selected,this.set_state(c,d),!1;for(i in c)c.hasOwnProperty(i)&&"core"!==i&&-1===a.inArray(i,this.settings.plugins)&&delete c[i];if(a.isEmptyObject(c.core))return delete c.core,this.set_state(c,d),!1}return a.isEmptyObject(c)?(c=null,d&&d.call(this),this.trigger("set_state"),!1):!0}return!1},refresh:function(b,c){this._data.core.state=c===!0?{}:this.get_state(),c&&a.isFunction(c)&&(this._data.core.state=c.call(this,this._data.core.state)),this._cnt=0,this._model.data={},this._model.data[a.jstree.root]={id:a.jstree.root,parent:null,parents:[],children:[],children_d:[],state:{loaded:!1}},this._data.core.selected=[],this._data.core.last_clicked=null,this._data.core.focused=null;var d=this.get_container_ul()[0].className;b||(this.element.html("<ul class='"+d+"' role='group'><li class='jstree-initial-node jstree-loading jstree-leaf jstree-last' role='treeitem' id='j"+this._id+"_loading'><i class='jstree-icon jstree-ocl'></i><a class='jstree-anchor' href='#'><i class='jstree-icon jstree-themeicon-hidden'></i>"+this.get_string("Loading ...")+"</a></li></ul>"),this.element.attr("aria-activedescendant","j"+this._id+"_loading")),this.load_node(a.jstree.root,function(b,c){c&&(this.get_container_ul()[0].className=d,this._firstChild(this.get_container_ul()[0])&&this.element.attr("aria-activedescendant",this._firstChild(this.get_container_ul()[0]).id),this.set_state(a.extend(!0,{},this._data.core.state),function(){this.trigger("refresh")})),this._data.core.state=null})},refresh_node:function(b){if(b=this.get_node(b),!b||b.id===a.jstree.root)return!1;var c=[],d=[],e=this._data.core.selected.concat([]);d.push(b.id),b.state.opened===!0&&c.push(b.id),this.get_node(b,!0).find(".jstree-open").each(function(){d.push(this.id),c.push(this.id)}),this._load_nodes(d,a.proxy(function(a){this.open_node(c,!1,0),this.select_node(e),this.trigger("refresh_node",{node:b,nodes:a})},this),!1,!0)},set_id:function(b,c){if(b=this.get_node(b),!b||b.id===a.jstree.root)return!1;var d,e,f=this._model.data,g=b.id;for(c=c.toString(),f[b.parent].children[a.inArray(b.id,f[b.parent].children)]=c,d=0,e=b.parents.length;e>d;d++)f[b.parents[d]].children_d[a.inArray(b.id,f[b.parents[d]].children_d)]=c;for(d=0,e=b.children.length;e>d;d++)f[b.children[d]].parent=c;for(d=0,e=b.children_d.length;e>d;d++)f[b.children_d[d]].parents[a.inArray(b.id,f[b.children_d[d]].parents)]=c;return d=a.inArray(b.id,this._data.core.selected),-1!==d&&(this._data.core.selected[d]=c),d=this.get_node(b.id,!0),d&&(d.attr("id",c),this.element.attr("aria-activedescendant")===b.id&&this.element.attr("aria-activedescendant",c)),delete f[b.id],b.id=c,b.li_attr.id=c,f[c]=b,this.trigger("set_id",{node:b,"new":b.id,old:g}),!0},get_text:function(b){return b=this.get_node(b),b&&b.id!==a.jstree.root?b.text:!1},set_text:function(b,c){var d,e;if(a.isArray(b)){for(b=b.slice(),d=0,e=b.length;e>d;d++)this.set_text(b[d],c);return!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?(b.text=c,this.get_node(b,!0).length&&this.redraw_node(b.id),this.trigger("set_text",{obj:b,text:c}),!0):!1},get_json:function(b,c,d){if(b=this.get_node(b||a.jstree.root),!b)return!1;c&&c.flat&&!d&&(d=[]);var e={id:b.id,text:b.text,icon:this.get_icon(b),li_attr:a.extend(!0,{},b.li_attr),a_attr:a.extend(!0,{},b.a_attr),state:{},data:c&&c.no_data?!1:a.extend(!0,a.isArray(b.data)?[]:{},b.data)},f,g;if(c&&c.flat?e.parent=b.parent:e.children=[],c&&c.no_state)delete e.state;else for(f in b.state)b.state.hasOwnProperty(f)&&(e.state[f]=b.state[f]);if(c&&c.no_li_attr&&delete e.li_attr,c&&c.no_a_attr&&delete e.a_attr,c&&c.no_id&&(delete e.id,e.li_attr&&e.li_attr.id&&delete e.li_attr.id,e.a_attr&&e.a_attr.id&&delete e.a_attr.id),c&&c.flat&&b.id!==a.jstree.root&&d.push(e),!c||!c.no_children)for(f=0,g=b.children.length;g>f;f++)c&&c.flat?this.get_json(b.children[f],c,d):e.children.push(this.get_json(b.children[f],c));return c&&c.flat?d:b.id===a.jstree.root?e.children:e},create_node:function(c,d,e,f,g){if(null===c&&(c=a.jstree.root),c=this.get_node(c),!c)return!1;if(e=e===b?"last":e,!e.toString().match(/^(before|after)$/)&&!g&&!this.is_loaded(c))return this.load_node(c,function(){this.create_node(c,d,e,f,!0)});d||(d={text:this.get_string("New node")}),d="string"==typeof d?{text:d}:a.extend(!0,{},d),d.text===b&&(d.text=this.get_string("New node"));var h,i,j,k;switch(c.id===a.jstree.root&&("before"===e&&(e="first"),"after"===e&&(e="last")),e){case"before":h=this.get_node(c.parent),e=a.inArray(c.id,h.children),c=h;break;case"after":h=this.get_node(c.parent),e=a.inArray(c.id,h.children)+1,c=h;break;case"inside":case"first":e=0;break;case"last":e=c.children.length;break;default:e||(e=0)}if(e>c.children.length&&(e=c.children.length),d.id||(d.id=!0),!this.check("create_node",d,c,e))return this.settings.core.error.call(this,this._data.core.last_error),!1;if(d.id===!0&&delete d.id,d=this._parse_model_from_json(d,c.id,c.parents.concat()),!d)return!1;for(h=this.get_node(d),i=[],i.push(d),i=i.concat(h.children_d),this.trigger("model",{nodes:i,parent:c.id}),c.children_d=c.children_d.concat(i),j=0,k=c.parents.length;k>j;j++)this._model.data[c.parents[j]].children_d=this._model.data[c.parents[j]].children_d.concat(i);for(d=h,h=[],j=0,k=c.children.length;k>j;j++)h[j>=e?j+1:j]=c.children[j];return h[e]=d.id,c.children=h,this.redraw_node(c,!0),this.trigger("create_node",{node:this.get_node(d),parent:c.id,position:e}),f&&f.call(this,this.get_node(d)),d.id},rename_node:function(b,c){var d,e,f;if(a.isArray(b)){for(b=b.slice(),d=0,e=b.length;e>d;d++)this.rename_node(b[d],c);return!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?(f=b.text,this.check("rename_node",b,this.get_parent(b),c)?(this.set_text(b,c),this.trigger("rename_node",{node:b,text:c,old:f}),!0):(this.settings.core.error.call(this,this._data.core.last_error),!1)):!1},delete_node:function(b){var c,d,e,f,g,h,i,j,k,l,m,n;if(a.isArray(b)){for(b=b.slice(),c=0,d=b.length;d>c;c++)this.delete_node(b[c]);return!0}if(b=this.get_node(b),!b||b.id===a.jstree.root)return!1;if(e=this.get_node(b.parent),f=a.inArray(b.id,e.children),l=!1,!this.check("delete_node",b,e,f))return this.settings.core.error.call(this,this._data.core.last_error),!1;for(-1!==f&&(e.children=a.vakata.array_remove(e.children,f)),g=b.children_d.concat([]),g.push(b.id),h=0,i=b.parents.length;i>h;h++)this._model.data[b.parents[h]].children_d=a.vakata.array_filter(this._model.data[b.parents[h]].children_d,function(b){return-1===a.inArray(b,g)});for(j=0,k=g.length;k>j;j++)if(this._model.data[g[j]].state.selected){l=!0;break}for(l&&(this._data.core.selected=a.vakata.array_filter(this._data.core.selected,function(b){return-1===a.inArray(b,g)})),this.trigger("delete_node",{node:b,parent:e.id}),l&&this.trigger("changed",{action:"delete_node",node:b,selected:this._data.core.selected,parent:e.id}),j=0,k=g.length;k>j;j++)delete this._model.data[g[j]];return-1!==a.inArray(this._data.core.focused,g)&&(this._data.core.focused=null,m=this.element[0].scrollTop,n=this.element[0].scrollLeft,e.id===a.jstree.root?this._model.data[a.jstree.root].children[0]&&this.get_node(this._model.data[a.jstree.root].children[0],!0).children(".jstree-anchor").focus():this.get_node(e,!0).children(".jstree-anchor").focus(),this.element[0].scrollTop=m,this.element[0].scrollLeft=n),this.redraw_node(e,!0),!0},check:function(b,c,d,e,f){c=c&&c.id?c:this.get_node(c),d=d&&d.id?d:this.get_node(d);var g=b.match(/^move_node|copy_node|create_node$/i)?d:c,h=this.settings.core.check_callback;if("move_node"===b||"copy_node"===b){if(!(f&&f.is_multi||"move_node"!==b||a.inArray(c.id,d.children)!==e))return this._data.core.last_error={error:"check",plugin:"core",id:"core_08",reason:"Moving node to its current position",data:JSON.stringify({chk:b,pos:e,obj:c&&c.id?c.id:!1,par:d&&d.id?d.id:!1})},!1;if(!(f&&f.is_multi||c.id!==d.id&&("move_node"!==b||a.inArray(c.id,d.children)!==e)&&-1===a.inArray(d.id,c.children_d)))return this._data.core.last_error={error:"check",plugin:"core",id:"core_01",reason:"Moving parent inside child",data:JSON.stringify({chk:b,pos:e,obj:c&&c.id?c.id:!1,par:d&&d.id?d.id:!1})},!1}return g&&g.data&&(g=g.data),g&&g.functions&&(g.functions[b]===!1||g.functions[b]===!0)?(g.functions[b]===!1&&(this._data.core.last_error={error:"check",plugin:"core",id:"core_02",reason:"Node data prevents function: "+b,data:JSON.stringify({chk:b,pos:e,obj:c&&c.id?c.id:!1,par:d&&d.id?d.id:!1})}),g.functions[b]):h===!1||a.isFunction(h)&&h.call(this,b,c,d,e,f)===!1||h&&h[b]===!1?(this._data.core.last_error={error:"check",plugin:"core",id:"core_03",reason:"User config for core.check_callback prevents function: "+b,data:JSON.stringify({chk:b,pos:e,obj:c&&c.id?c.id:!1,par:d&&d.id?d.id:!1})},!1):!0},last_error:function(){return this._data.core.last_error},move_node:function(c,d,e,f,g,h,i){var j,k,l,m,n,o,p,q,r,s,t,u,v,w;if(d=this.get_node(d),e=e===b?0:e,!d)return!1;if(!e.toString().match(/^(before|after)$/)&&!g&&!this.is_loaded(d))return this.load_node(d,function(){this.move_node(c,d,e,f,!0,!1,i)});if(a.isArray(c)){if(1!==c.length){for(j=0,k=c.length;k>j;j++)(r=this.move_node(c[j],d,e,f,g,!1,i))&&(d=r,e="after");return this.redraw(),!0}c=c[0]}if(c=c&&c.id?c:this.get_node(c),!c||c.id===a.jstree.root)return!1;if(l=(c.parent||a.jstree.root).toString(),n=e.toString().match(/^(before|after)$/)&&d.id!==a.jstree.root?this.get_node(d.parent):d,o=i?i:this._model.data[c.id]?this:a.jstree.reference(c.id),p=!o||!o._id||this._id!==o._id,m=o&&o._id&&l&&o._model.data[l]&&o._model.data[l].children?a.inArray(c.id,o._model.data[l].children):-1,o&&o._id&&(c=o._model.data[c.id]),p)return(r=this.copy_node(c,d,e,f,g,!1,i))?(o&&o.delete_node(c),r):!1;switch(d.id===a.jstree.root&&("before"===e&&(e="first"),"after"===e&&(e="last")),e){case"before":e=a.inArray(d.id,n.children);break;case"after":e=a.inArray(d.id,n.children)+1;break;case"inside":case"first":e=0;break;case"last":e=n.children.length;break;default:e||(e=0)}if(e>n.children.length&&(e=n.children.length),!this.check("move_node",c,n,e,{core:!0,origin:i,is_multi:o&&o._id&&o._id!==this._id,is_foreign:!o||!o._id}))return this.settings.core.error.call(this,this._data.core.last_error),!1;if(c.parent===n.id){for(q=n.children.concat(),r=a.inArray(c.id,q),-1!==r&&(q=a.vakata.array_remove(q,r),e>r&&e--),r=[],s=0,t=q.length;t>s;s++)r[s>=e?s+1:s]=q[s];r[e]=c.id,n.children=r,this._node_changed(n.id),this.redraw(n.id===a.jstree.root)}else{for(r=c.children_d.concat(),r.push(c.id),s=0,t=c.parents.length;t>s;s++){for(q=[],w=o._model.data[c.parents[s]].children_d,u=0,v=w.length;v>u;u++)-1===a.inArray(w[u],r)&&q.push(w[u]);o._model.data[c.parents[s]].children_d=q}for(o._model.data[l].children=a.vakata.array_remove_item(o._model.data[l].children,c.id),s=0,t=n.parents.length;t>s;s++)this._model.data[n.parents[s]].children_d=this._model.data[n.parents[s]].children_d.concat(r);for(q=[],s=0,t=n.children.length;t>s;s++)q[s>=e?s+1:s]=n.children[s];for(q[e]=c.id,n.children=q,n.children_d.push(c.id),n.children_d=n.children_d.concat(c.children_d),c.parent=n.id,r=n.parents.concat(),r.unshift(n.id),w=c.parents.length,c.parents=r,r=r.concat(),s=0,t=c.children_d.length;t>s;s++)this._model.data[c.children_d[s]].parents=this._model.data[c.children_d[s]].parents.slice(0,-1*w),Array.prototype.push.apply(this._model.data[c.children_d[s]].parents,r);(l===a.jstree.root||n.id===a.jstree.root)&&(this._model.force_full_redraw=!0),this._model.force_full_redraw||(this._node_changed(l),this._node_changed(n.id)),h||this.redraw()}return f&&f.call(this,c,n,e),this.trigger("move_node",{node:c,parent:n.id,position:e,old_parent:l,old_position:m,is_multi:o&&o._id&&o._id!==this._id,is_foreign:!o||!o._id,old_instance:o,new_instance:this}),c.id},copy_node:function(c,d,e,f,g,h,i){var j,k,l,m,n,o,p,q,r,s,t;if(d=this.get_node(d),e=e===b?0:e,!d)return!1;if(!e.toString().match(/^(before|after)$/)&&!g&&!this.is_loaded(d))return this.load_node(d,function(){this.copy_node(c,d,e,f,!0,!1,i)});if(a.isArray(c)){if(1!==c.length){for(j=0,k=c.length;k>j;j++)(m=this.copy_node(c[j],d,e,f,g,!0,i))&&(d=m,e="after");return this.redraw(),!0}c=c[0]}if(c=c&&c.id?c:this.get_node(c),!c||c.id===a.jstree.root)return!1;switch(q=(c.parent||a.jstree.root).toString(),r=e.toString().match(/^(before|after)$/)&&d.id!==a.jstree.root?this.get_node(d.parent):d,s=i?i:this._model.data[c.id]?this:a.jstree.reference(c.id),t=!s||!s._id||this._id!==s._id,s&&s._id&&(c=s._model.data[c.id]),d.id===a.jstree.root&&("before"===e&&(e="first"),"after"===e&&(e="last")),e){case"before":e=a.inArray(d.id,r.children);break;case"after":e=a.inArray(d.id,r.children)+1;break;case"inside":case"first":e=0;break;case"last":e=r.children.length;break;default:e||(e=0)}if(e>r.children.length&&(e=r.children.length),!this.check("copy_node",c,r,e,{core:!0,origin:i,is_multi:s&&s._id&&s._id!==this._id,is_foreign:!s||!s._id}))return this.settings.core.error.call(this,this._data.core.last_error),!1;if(p=s?s.get_json(c,{no_id:!0,no_data:!0,no_state:!0}):c,!p)return!1;if(p.id===!0&&delete p.id,p=this._parse_model_from_json(p,r.id,r.parents.concat()),!p)return!1;for(m=this.get_node(p),c&&c.state&&c.state.loaded===!1&&(m.state.loaded=!1),l=[],l.push(p),l=l.concat(m.children_d),this.trigger("model",{nodes:l,parent:r.id}),n=0,o=r.parents.length;o>n;n++)this._model.data[r.parents[n]].children_d=this._model.data[r.parents[n]].children_d.concat(l);for(l=[],n=0,o=r.children.length;o>n;n++)l[n>=e?n+1:n]=r.children[n];return l[e]=m.id,r.children=l,r.children_d.push(m.id),r.children_d=r.children_d.concat(m.children_d),r.id===a.jstree.root&&(this._model.force_full_redraw=!0),this._model.force_full_redraw||this._node_changed(r.id),h||this.redraw(r.id===a.jstree.root),f&&f.call(this,m,r,e),this.trigger("copy_node",{node:m,original:c,parent:r.id,position:e,old_parent:q,old_position:s&&s._id&&q&&s._model.data[q]&&s._model.data[q].children?a.inArray(c.id,s._model.data[q].children):-1,
is_multi:s&&s._id&&s._id!==this._id,is_foreign:!s||!s._id,old_instance:s,new_instance:this}),m.id},cut:function(b){if(b||(b=this._data.core.selected.concat()),a.isArray(b)||(b=[b]),!b.length)return!1;var c=[],g,h,i;for(h=0,i=b.length;i>h;h++)g=this.get_node(b[h]),g&&g.id&&g.id!==a.jstree.root&&c.push(g);return c.length?(d=c,f=this,e="move_node",void this.trigger("cut",{node:b})):!1},copy:function(b){if(b||(b=this._data.core.selected.concat()),a.isArray(b)||(b=[b]),!b.length)return!1;var c=[],g,h,i;for(h=0,i=b.length;i>h;h++)g=this.get_node(b[h]),g&&g.id&&g.id!==a.jstree.root&&c.push(g);return c.length?(d=c,f=this,e="copy_node",void this.trigger("copy",{node:b})):!1},get_buffer:function(){return{mode:e,node:d,inst:f}},can_paste:function(){return e!==!1&&d!==!1},paste:function(a,b){return a=this.get_node(a),a&&e&&e.match(/^(copy_node|move_node)$/)&&d?(this[e](d,a,b,!1,!1,!1,f)&&this.trigger("paste",{parent:a.id,node:d,mode:e}),d=!1,e=!1,void(f=!1)):!1},clear_buffer:function(){d=!1,e=!1,f=!1,this.trigger("clear_buffer")},edit:function(b,c,d){var e,f,g,h,j,k,l,m,n,o=!1;return(b=this.get_node(b))?this.check("edit",b,this.get_parent(b))?(n=b,c="string"==typeof c?c:b.text,this.set_text(b,""),b=this._open_to(b),n.text=c,e=this._data.core.rtl,f=this.element.width(),this._data.core.focused=n.id,g=b.children(".jstree-anchor").focus(),h=a("<span>"),j=c,k=a("<div />",{css:{position:"absolute",top:"-200px",left:e?"0px":"-1000px",visibility:"hidden"}}).appendTo(i.body),l=a("<input />",{value:j,"class":"jstree-rename-input",css:{padding:"0",border:"1px solid silver","box-sizing":"border-box",display:"inline-block",height:this._data.core.li_height+"px",lineHeight:this._data.core.li_height+"px",width:"150px"},blur:a.proxy(function(c){c.stopImmediatePropagation(),c.preventDefault();var e=h.children(".jstree-rename-input"),f=e.val(),i=this.settings.core.force_text,m;""===f&&(f=j),k.remove(),h.replaceWith(g),h.remove(),j=i?j:a("<div></div>").append(a.parseHTML(j)).html(),b=this.get_node(b),this.set_text(b,j),m=!!this.rename_node(b,i?a("<div></div>").text(f).text():a("<div></div>").append(a.parseHTML(f)).html()),m||this.set_text(b,j),this._data.core.focused=n.id,setTimeout(a.proxy(function(){var a=this.get_node(n.id,!0);a.length&&(this._data.core.focused=n.id,a.children(".jstree-anchor").focus())},this),0),d&&d.call(this,n,m,o),l=null},this),keydown:function(a){var b=a.which;27===b&&(o=!0,this.value=j),(27===b||13===b||37===b||38===b||39===b||40===b||32===b)&&a.stopImmediatePropagation(),(27===b||13===b)&&(a.preventDefault(),this.blur())},click:function(a){a.stopImmediatePropagation()},mousedown:function(a){a.stopImmediatePropagation()},keyup:function(a){l.width(Math.min(k.text("pW"+this.value).width(),f))},keypress:function(a){return 13===a.which?!1:void 0}}),m={fontFamily:g.css("fontFamily")||"",fontSize:g.css("fontSize")||"",fontWeight:g.css("fontWeight")||"",fontStyle:g.css("fontStyle")||"",fontStretch:g.css("fontStretch")||"",fontVariant:g.css("fontVariant")||"",letterSpacing:g.css("letterSpacing")||"",wordSpacing:g.css("wordSpacing")||""},h.attr("class",g.attr("class")).append(g.contents().clone()).append(l),g.replaceWith(h),k.css(m),l.css(m).width(Math.min(k.text("pW"+l[0].value).width(),f))[0].select(),void a(i).one("mousedown.jstree touchstart.jstree dnd_start.vakata",function(b){l&&b.target!==l&&a(l).blur()})):(this.settings.core.error.call(this,this._data.core.last_error),!1):!1},set_theme:function(b,c){if(!b)return!1;if(c===!0){var d=this.settings.core.themes.dir;d||(d=a.jstree.path+"/themes"),c=d+"/"+b+"/style.css"}c&&-1===a.inArray(c,g)&&(a("head").append('<link rel="stylesheet" href="'+c+'" type="text/css" />'),g.push(c)),this._data.core.themes.name&&this.element.removeClass("jstree-"+this._data.core.themes.name),this._data.core.themes.name=b,this.element.addClass("jstree-"+b),this.element[this.settings.core.themes.responsive?"addClass":"removeClass"]("jstree-"+b+"-responsive"),this.trigger("set_theme",{theme:b})},get_theme:function(){return this._data.core.themes.name},set_theme_variant:function(a){this._data.core.themes.variant&&this.element.removeClass("jstree-"+this._data.core.themes.name+"-"+this._data.core.themes.variant),this._data.core.themes.variant=a,a&&this.element.addClass("jstree-"+this._data.core.themes.name+"-"+this._data.core.themes.variant)},get_theme_variant:function(){return this._data.core.themes.variant},show_stripes:function(){this._data.core.themes.stripes=!0,this.get_container_ul().addClass("jstree-striped"),this.trigger("show_stripes")},hide_stripes:function(){this._data.core.themes.stripes=!1,this.get_container_ul().removeClass("jstree-striped"),this.trigger("hide_stripes")},toggle_stripes:function(){this._data.core.themes.stripes?this.hide_stripes():this.show_stripes()},show_dots:function(){this._data.core.themes.dots=!0,this.get_container_ul().removeClass("jstree-no-dots"),this.trigger("show_dots")},hide_dots:function(){this._data.core.themes.dots=!1,this.get_container_ul().addClass("jstree-no-dots"),this.trigger("hide_dots")},toggle_dots:function(){this._data.core.themes.dots?this.hide_dots():this.show_dots()},show_icons:function(){this._data.core.themes.icons=!0,this.get_container_ul().removeClass("jstree-no-icons"),this.trigger("show_icons")},hide_icons:function(){this._data.core.themes.icons=!1,this.get_container_ul().addClass("jstree-no-icons"),this.trigger("hide_icons")},toggle_icons:function(){this._data.core.themes.icons?this.hide_icons():this.show_icons()},show_ellipsis:function(){this._data.core.themes.ellipsis=!0,this.get_container_ul().addClass("jstree-ellipsis"),this.trigger("show_ellipsis")},hide_ellipsis:function(){this._data.core.themes.ellipsis=!1,this.get_container_ul().removeClass("jstree-ellipsis"),this.trigger("hide_ellipsis")},toggle_ellipsis:function(){this._data.core.themes.ellipsis?this.hide_ellipsis():this.show_ellipsis()},set_icon:function(c,d){var e,f,g,h;if(a.isArray(c)){for(c=c.slice(),e=0,f=c.length;f>e;e++)this.set_icon(c[e],d);return!0}return c=this.get_node(c),c&&c.id!==a.jstree.root?(h=c.icon,c.icon=d===!0||null===d||d===b||""===d?!0:d,g=this.get_node(c,!0).children(".jstree-anchor").children(".jstree-themeicon"),d===!1?(g.removeClass("jstree-themeicon-custom "+h).css("background","").removeAttr("rel"),this.hide_icon(c)):d===!0||null===d||d===b||""===d?(g.removeClass("jstree-themeicon-custom "+h).css("background","").removeAttr("rel"),h===!1&&this.show_icon(c)):-1===d.indexOf("/")&&-1===d.indexOf(".")?(g.removeClass(h).css("background",""),g.addClass(d+" jstree-themeicon-custom").attr("rel",d),h===!1&&this.show_icon(c)):(g.removeClass(h).css("background",""),g.addClass("jstree-themeicon-custom").css("background","url('"+d+"') center center no-repeat").attr("rel",d),h===!1&&this.show_icon(c)),!0):!1},get_icon:function(b){return b=this.get_node(b),b&&b.id!==a.jstree.root?b.icon:!1},hide_icon:function(b){var c,d;if(a.isArray(b)){for(b=b.slice(),c=0,d=b.length;d>c;c++)this.hide_icon(b[c]);return!0}return b=this.get_node(b),b&&b!==a.jstree.root?(b.icon=!1,this.get_node(b,!0).children(".jstree-anchor").children(".jstree-themeicon").addClass("jstree-themeicon-hidden"),!0):!1},show_icon:function(b){var c,d,e;if(a.isArray(b)){for(b=b.slice(),c=0,d=b.length;d>c;c++)this.show_icon(b[c]);return!0}return b=this.get_node(b),b&&b!==a.jstree.root?(e=this.get_node(b,!0),b.icon=e.length?e.children(".jstree-anchor").children(".jstree-themeicon").attr("rel"):!0,b.icon||(b.icon=!0),e.children(".jstree-anchor").children(".jstree-themeicon").removeClass("jstree-themeicon-hidden"),!0):!1}},a.vakata={},a.vakata.attributes=function(b,c){b=a(b)[0];var d=c?{}:[];return b&&b.attributes&&a.each(b.attributes,function(b,e){-1===a.inArray(e.name.toLowerCase(),["style","contenteditable","hasfocus","tabindex"])&&null!==e.value&&""!==a.trim(e.value)&&(c?d[e.name]=e.value:d.push(e.name))}),d},a.vakata.array_unique=function(a){var c=[],d,e,f,g={};for(d=0,f=a.length;f>d;d++)g[a[d]]===b&&(c.push(a[d]),g[a[d]]=!0);return c},a.vakata.array_remove=function(a,b){return a.splice(b,1),a},a.vakata.array_remove_item=function(b,c){var d=a.inArray(c,b);return-1!==d?a.vakata.array_remove(b,d):b},a.vakata.array_filter=function(a,b,c,d,e){if(a.filter)return a.filter(b,c);d=[];for(e in a)~~e+""==e+""&&e>=0&&b.call(c,a[e],+e,a)&&d.push(a[e]);return d},a.jstree.plugins.changed=function(a,b){var c=[];this.trigger=function(a,d){var e,f;if(d||(d={}),"changed"===a.replace(".jstree","")){d.changed={selected:[],deselected:[]};var g={};for(e=0,f=c.length;f>e;e++)g[c[e]]=1;for(e=0,f=d.selected.length;f>e;e++)g[d.selected[e]]?g[d.selected[e]]=2:d.changed.selected.push(d.selected[e]);for(e=0,f=c.length;f>e;e++)1===g[c[e]]&&d.changed.deselected.push(c[e]);c=d.selected.slice()}b.trigger.call(this,a,d)},this.refresh=function(a,d){return c=[],b.refresh.apply(this,arguments)}};var j=i.createElement("I");j.className="jstree-icon jstree-checkbox",j.setAttribute("role","presentation"),a.jstree.defaults.checkbox={visible:!0,three_state:!0,whole_node:!0,keep_selected_style:!0,cascade:"",tie_selection:!0,cascade_to_disabled:!0,cascade_to_hidden:!0},a.jstree.plugins.checkbox=function(c,d){this.bind=function(){d.bind.call(this),this._data.checkbox.uto=!1,this._data.checkbox.selected=[],this.settings.checkbox.three_state&&(this.settings.checkbox.cascade="up+down+undetermined"),this.element.on("init.jstree",a.proxy(function(){this._data.checkbox.visible=this.settings.checkbox.visible,this.settings.checkbox.keep_selected_style||this.element.addClass("jstree-checkbox-no-clicked"),this.settings.checkbox.tie_selection&&this.element.addClass("jstree-checkbox-selection")},this)).on("loading.jstree",a.proxy(function(){this[this._data.checkbox.visible?"show_checkboxes":"hide_checkboxes"]()},this)),-1!==this.settings.checkbox.cascade.indexOf("undetermined")&&this.element.on("changed.jstree uncheck_node.jstree check_node.jstree uncheck_all.jstree check_all.jstree move_node.jstree copy_node.jstree redraw.jstree open_node.jstree",a.proxy(function(){this._data.checkbox.uto&&clearTimeout(this._data.checkbox.uto),this._data.checkbox.uto=setTimeout(a.proxy(this._undetermined,this),50)},this)),this.settings.checkbox.tie_selection||this.element.on("model.jstree",a.proxy(function(a,b){var c=this._model.data,d=c[b.parent],e=b.nodes,f,g;for(f=0,g=e.length;g>f;f++)c[e[f]].state.checked=c[e[f]].state.checked||c[e[f]].original&&c[e[f]].original.state&&c[e[f]].original.state.checked,c[e[f]].state.checked&&this._data.checkbox.selected.push(e[f])},this)),(-1!==this.settings.checkbox.cascade.indexOf("up")||-1!==this.settings.checkbox.cascade.indexOf("down"))&&this.element.on("model.jstree",a.proxy(function(b,c){var d=this._model.data,e=d[c.parent],f=c.nodes,g=[],h,i,j,k,l,m,n=this.settings.checkbox.cascade,o=this.settings.checkbox.tie_selection;if(-1!==n.indexOf("down"))if(e.state[o?"selected":"checked"]){for(i=0,j=f.length;j>i;i++)d[f[i]].state[o?"selected":"checked"]=!0;this._data[o?"core":"checkbox"].selected=this._data[o?"core":"checkbox"].selected.concat(f)}else for(i=0,j=f.length;j>i;i++)if(d[f[i]].state[o?"selected":"checked"]){for(k=0,l=d[f[i]].children_d.length;l>k;k++)d[d[f[i]].children_d[k]].state[o?"selected":"checked"]=!0;this._data[o?"core":"checkbox"].selected=this._data[o?"core":"checkbox"].selected.concat(d[f[i]].children_d)}if(-1!==n.indexOf("up")){for(i=0,j=e.children_d.length;j>i;i++)d[e.children_d[i]].children.length||g.push(d[e.children_d[i]].parent);for(g=a.vakata.array_unique(g),k=0,l=g.length;l>k;k++){e=d[g[k]];while(e&&e.id!==a.jstree.root){for(h=0,i=0,j=e.children.length;j>i;i++)h+=d[e.children[i]].state[o?"selected":"checked"];if(h!==j)break;e.state[o?"selected":"checked"]=!0,this._data[o?"core":"checkbox"].selected.push(e.id),m=this.get_node(e,!0),m&&m.length&&m.attr("aria-selected",!0).children(".jstree-anchor").addClass(o?"jstree-clicked":"jstree-checked"),e=this.get_node(e.parent)}}}this._data[o?"core":"checkbox"].selected=a.vakata.array_unique(this._data[o?"core":"checkbox"].selected)},this)).on(this.settings.checkbox.tie_selection?"select_node.jstree":"check_node.jstree",a.proxy(function(b,c){var d=this,e=c.node,f=this._model.data,g=this.get_node(e.parent),h,i,j,k,l=this.settings.checkbox.cascade,m=this.settings.checkbox.tie_selection,n={},o=this._data[m?"core":"checkbox"].selected;for(h=0,i=o.length;i>h;h++)n[o[h]]=!0;if(-1!==l.indexOf("down")){var p=this._cascade_new_checked_state(e.id,!0),q=e.children_d.concat(e.id);for(h=0,i=q.length;i>h;h++)p.indexOf(q[h])>-1?n[q[h]]=!0:delete n[q[h]]}if(-1!==l.indexOf("up"))while(g&&g.id!==a.jstree.root){for(j=0,h=0,i=g.children.length;i>h;h++)j+=f[g.children[h]].state[m?"selected":"checked"];if(j!==i)break;g.state[m?"selected":"checked"]=!0,n[g.id]=!0,k=this.get_node(g,!0),k&&k.length&&k.attr("aria-selected",!0).children(".jstree-anchor").addClass(m?"jstree-clicked":"jstree-checked"),g=this.get_node(g.parent)}o=[];for(h in n)n.hasOwnProperty(h)&&o.push(h);this._data[m?"core":"checkbox"].selected=o},this)).on(this.settings.checkbox.tie_selection?"deselect_all.jstree":"uncheck_all.jstree",a.proxy(function(b,c){var d=this.get_node(a.jstree.root),e=this._model.data,f,g,h;for(f=0,g=d.children_d.length;g>f;f++)h=e[d.children_d[f]],h&&h.original&&h.original.state&&h.original.state.undetermined&&(h.original.state.undetermined=!1)},this)).on(this.settings.checkbox.tie_selection?"deselect_node.jstree":"uncheck_node.jstree",a.proxy(function(b,c){var d=this,e=c.node,f=this.get_node(e,!0),g,h,i,j=this.settings.checkbox.cascade,k=this.settings.checkbox.tie_selection,l=this._data[k?"core":"checkbox"].selected,m={},n=[],o=e.children_d.concat(e.id);if(-1!==j.indexOf("down")){var p=this._cascade_new_checked_state(e.id,!1);l=a.vakata.array_filter(l,function(a){return-1===o.indexOf(a)||p.indexOf(a)>-1})}if(-1!==j.indexOf("up")&&-1===l.indexOf(e.id)){for(g=0,h=e.parents.length;h>g;g++)i=this._model.data[e.parents[g]],i.state[k?"selected":"checked"]=!1,i&&i.original&&i.original.state&&i.original.state.undetermined&&(i.original.state.undetermined=!1),i=this.get_node(e.parents[g],!0),i&&i.length&&i.attr("aria-selected",!1).children(".jstree-anchor").removeClass(k?"jstree-clicked":"jstree-checked");l=a.vakata.array_filter(l,function(a){return-1===e.parents.indexOf(a)})}this._data[k?"core":"checkbox"].selected=l},this)),-1!==this.settings.checkbox.cascade.indexOf("up")&&this.element.on("delete_node.jstree",a.proxy(function(b,c){var d=this.get_node(c.parent),e=this._model.data,f,g,h,i,j=this.settings.checkbox.tie_selection;while(d&&d.id!==a.jstree.root&&!d.state[j?"selected":"checked"]){for(h=0,f=0,g=d.children.length;g>f;f++)h+=e[d.children[f]].state[j?"selected":"checked"];if(!(g>0&&h===g))break;d.state[j?"selected":"checked"]=!0,this._data[j?"core":"checkbox"].selected.push(d.id),i=this.get_node(d,!0),i&&i.length&&i.attr("aria-selected",!0).children(".jstree-anchor").addClass(j?"jstree-clicked":"jstree-checked"),d=this.get_node(d.parent)}},this)).on("move_node.jstree",a.proxy(function(b,c){var d=c.is_multi,e=c.old_parent,f=this.get_node(c.parent),g=this._model.data,h,i,j,k,l,m=this.settings.checkbox.tie_selection;if(!d){h=this.get_node(e);while(h&&h.id!==a.jstree.root&&!h.state[m?"selected":"checked"]){for(i=0,j=0,k=h.children.length;k>j;j++)i+=g[h.children[j]].state[m?"selected":"checked"];if(!(k>0&&i===k))break;h.state[m?"selected":"checked"]=!0,this._data[m?"core":"checkbox"].selected.push(h.id),l=this.get_node(h,!0),l&&l.length&&l.attr("aria-selected",!0).children(".jstree-anchor").addClass(m?"jstree-clicked":"jstree-checked"),h=this.get_node(h.parent)}}h=f;while(h&&h.id!==a.jstree.root){for(i=0,j=0,k=h.children.length;k>j;j++)i+=g[h.children[j]].state[m?"selected":"checked"];if(i===k)h.state[m?"selected":"checked"]||(h.state[m?"selected":"checked"]=!0,this._data[m?"core":"checkbox"].selected.push(h.id),l=this.get_node(h,!0),l&&l.length&&l.attr("aria-selected",!0).children(".jstree-anchor").addClass(m?"jstree-clicked":"jstree-checked"));else{if(!h.state[m?"selected":"checked"])break;h.state[m?"selected":"checked"]=!1,this._data[m?"core":"checkbox"].selected=a.vakata.array_remove_item(this._data[m?"core":"checkbox"].selected,h.id),l=this.get_node(h,!0),l&&l.length&&l.attr("aria-selected",!1).children(".jstree-anchor").removeClass(m?"jstree-clicked":"jstree-checked")}h=this.get_node(h.parent)}},this))},this.get_undetermined=function(c){if(-1===this.settings.checkbox.cascade.indexOf("undetermined"))return[];var d,e,f,g,h={},i=this._model.data,j=this.settings.checkbox.tie_selection,k=this._data[j?"core":"checkbox"].selected,l=[],m=this,n=[];for(d=0,e=k.length;e>d;d++)if(i[k[d]]&&i[k[d]].parents)for(f=0,g=i[k[d]].parents.length;g>f;f++){if(h[i[k[d]].parents[f]]!==b)break;i[k[d]].parents[f]!==a.jstree.root&&(h[i[k[d]].parents[f]]=!0,l.push(i[k[d]].parents[f]))}for(this.element.find(".jstree-closed").not(":has(.jstree-children)").each(function(){var c=m.get_node(this),j;if(c)if(c.state.loaded){for(d=0,e=c.children_d.length;e>d;d++)if(j=i[c.children_d[d]],!j.state.loaded&&j.original&&j.original.state&&j.original.state.undetermined&&j.original.state.undetermined===!0)for(h[j.id]===b&&j.id!==a.jstree.root&&(h[j.id]=!0,l.push(j.id)),f=0,g=j.parents.length;g>f;f++)h[j.parents[f]]===b&&j.parents[f]!==a.jstree.root&&(h[j.parents[f]]=!0,l.push(j.parents[f]))}else if(c.original&&c.original.state&&c.original.state.undetermined&&c.original.state.undetermined===!0)for(h[c.id]===b&&c.id!==a.jstree.root&&(h[c.id]=!0,l.push(c.id)),f=0,g=c.parents.length;g>f;f++)h[c.parents[f]]===b&&c.parents[f]!==a.jstree.root&&(h[c.parents[f]]=!0,l.push(c.parents[f]))}),d=0,e=l.length;e>d;d++)i[l[d]].state[j?"selected":"checked"]||n.push(c?i[l[d]]:l[d]);return n},this._undetermined=function(){if(null!==this.element){var a=this.get_undetermined(!1),b,c,d;for(this.element.find(".jstree-undetermined").removeClass("jstree-undetermined"),b=0,c=a.length;c>b;b++)d=this.get_node(a[b],!0),d&&d.length&&d.children(".jstree-anchor").children(".jstree-checkbox").addClass("jstree-undetermined")}},this.redraw_node=function(b,c,e,f){if(b=d.redraw_node.apply(this,arguments)){var g,h,i=null,k=null;for(g=0,h=b.childNodes.length;h>g;g++)if(b.childNodes[g]&&b.childNodes[g].className&&-1!==b.childNodes[g].className.indexOf("jstree-anchor")){i=b.childNodes[g];break}i&&(!this.settings.checkbox.tie_selection&&this._model.data[b.id].state.checked&&(i.className+=" jstree-checked"),k=j.cloneNode(!1),this._model.data[b.id].state.checkbox_disabled&&(k.className+=" jstree-checkbox-disabled"),i.insertBefore(k,i.childNodes[0]))}return e||-1===this.settings.checkbox.cascade.indexOf("undetermined")||(this._data.checkbox.uto&&clearTimeout(this._data.checkbox.uto),this._data.checkbox.uto=setTimeout(a.proxy(this._undetermined,this),50)),b},this.show_checkboxes=function(){this._data.core.themes.checkboxes=!0,this.get_container_ul().removeClass("jstree-no-checkboxes")},this.hide_checkboxes=function(){this._data.core.themes.checkboxes=!1,this.get_container_ul().addClass("jstree-no-checkboxes")},this.toggle_checkboxes=function(){this._data.core.themes.checkboxes?this.hide_checkboxes():this.show_checkboxes()},this.is_undetermined=function(b){b=this.get_node(b);var c=this.settings.checkbox.cascade,d,e,f=this.settings.checkbox.tie_selection,g=this._data[f?"core":"checkbox"].selected,h=this._model.data;if(!b||b.state[f?"selected":"checked"]===!0||-1===c.indexOf("undetermined")||-1===c.indexOf("down")&&-1===c.indexOf("up"))return!1;if(!b.state.loaded&&b.original.state.undetermined===!0)return!0;for(d=0,e=b.children_d.length;e>d;d++)if(-1!==a.inArray(b.children_d[d],g)||!h[b.children_d[d]].state.loaded&&h[b.children_d[d]].original.state.undetermined)return!0;return!1},this.disable_checkbox=function(b){var c,d,e;if(a.isArray(b)){for(b=b.slice(),c=0,d=b.length;d>c;c++)this.disable_checkbox(b[c]);return!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?(e=this.get_node(b,!0),void(b.state.checkbox_disabled||(b.state.checkbox_disabled=!0,e&&e.length&&e.children(".jstree-anchor").children(".jstree-checkbox").addClass("jstree-checkbox-disabled"),this.trigger("disable_checkbox",{node:b})))):!1},this.enable_checkbox=function(b){var c,d,e;if(a.isArray(b)){for(b=b.slice(),c=0,d=b.length;d>c;c++)this.enable_checkbox(b[c]);return!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?(e=this.get_node(b,!0),void(b.state.checkbox_disabled&&(b.state.checkbox_disabled=!1,e&&e.length&&e.children(".jstree-anchor").children(".jstree-checkbox").removeClass("jstree-checkbox-disabled"),this.trigger("enable_checkbox",{node:b})))):!1},this.activate_node=function(b,c){return a(c.target).hasClass("jstree-checkbox-disabled")?!1:(this.settings.checkbox.tie_selection&&(this.settings.checkbox.whole_node||a(c.target).hasClass("jstree-checkbox"))&&(c.ctrlKey=!0),this.settings.checkbox.tie_selection||!this.settings.checkbox.whole_node&&!a(c.target).hasClass("jstree-checkbox")?d.activate_node.call(this,b,c):this.is_disabled(b)?!1:(this.is_checked(b)?this.uncheck_node(b,c):this.check_node(b,c),void this.trigger("activate_node",{node:this.get_node(b)})))},this._cascade_new_checked_state=function(a,b){var c=this,d=this.settings.checkbox.tie_selection,e=this._model.data[a],f=[],g=[],h,i,j;if(!this.settings.checkbox.cascade_to_disabled&&e.state.disabled||!this.settings.checkbox.cascade_to_hidden&&e.state.hidden)j=this.get_checked_descendants(a),e.state[d?"selected":"checked"]&&j.push(e.id),f=f.concat(j);else{if(e.children)for(h=0,i=e.children.length;i>h;h++){var k=e.children[h];j=c._cascade_new_checked_state(k,b),f=f.concat(j),j.indexOf(k)>-1&&g.push(k)}var l=c.get_node(e,!0),m=g.length>0&&g.length<e.children.length;e.original&&e.original.state&&e.original.state.undetermined&&(e.original.state.undetermined=m),m?(e.state[d?"selected":"checked"]=!1,l.attr("aria-selected",!1).children(".jstree-anchor").removeClass(d?"jstree-clicked":"jstree-checked")):b&&g.length===e.children.length?(e.state[d?"selected":"checked"]=b,f.push(e.id),l.attr("aria-selected",!0).children(".jstree-anchor").addClass(d?"jstree-clicked":"jstree-checked")):(e.state[d?"selected":"checked"]=!1,l.attr("aria-selected",!1).children(".jstree-anchor").removeClass(d?"jstree-clicked":"jstree-checked"))}return f},this.get_checked_descendants=function(b){var c=this,d=c.settings.checkbox.tie_selection,e=c._model.data[b];return a.vakata.array_filter(e.children_d,function(a){return c._model.data[a].state[d?"selected":"checked"]})},this.check_node=function(b,c){if(this.settings.checkbox.tie_selection)return this.select_node(b,!1,!0,c);var d,e,f,g;if(a.isArray(b)){for(b=b.slice(),e=0,f=b.length;f>e;e++)this.check_node(b[e],c);return!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?(d=this.get_node(b,!0),void(b.state.checked||(b.state.checked=!0,this._data.checkbox.selected.push(b.id),d&&d.length&&d.children(".jstree-anchor").addClass("jstree-checked"),this.trigger("check_node",{node:b,selected:this._data.checkbox.selected,event:c})))):!1},this.uncheck_node=function(b,c){if(this.settings.checkbox.tie_selection)return this.deselect_node(b,!1,c);var d,e,f;if(a.isArray(b)){for(b=b.slice(),d=0,e=b.length;e>d;d++)this.uncheck_node(b[d],c);return!0}return b=this.get_node(b),b&&b.id!==a.jstree.root?(f=this.get_node(b,!0),void(b.state.checked&&(b.state.checked=!1,this._data.checkbox.selected=a.vakata.array_remove_item(this._data.checkbox.selected,b.id),f.length&&f.children(".jstree-anchor").removeClass("jstree-checked"),this.trigger("uncheck_node",{node:b,selected:this._data.checkbox.selected,event:c})))):!1},this.check_all=function(){if(this.settings.checkbox.tie_selection)return this.select_all();var b=this._data.checkbox.selected.concat([]),c,d;for(this._data.checkbox.selected=this._model.data[a.jstree.root].children_d.concat(),c=0,d=this._data.checkbox.selected.length;d>c;c++)this._model.data[this._data.checkbox.selected[c]]&&(this._model.data[this._data.checkbox.selected[c]].state.checked=!0);this.redraw(!0),this.trigger("check_all",{selected:this._data.checkbox.selected})},this.uncheck_all=function(){if(this.settings.checkbox.tie_selection)return this.deselect_all();var a=this._data.checkbox.selected.concat([]),b,c;for(b=0,c=this._data.checkbox.selected.length;c>b;b++)this._model.data[this._data.checkbox.selected[b]]&&(this._model.data[this._data.checkbox.selected[b]].state.checked=!1);this._data.checkbox.selected=[],this.element.find(".jstree-checked").removeClass("jstree-checked"),this.trigger("uncheck_all",{selected:this._data.checkbox.selected,node:a})},this.is_checked=function(b){return this.settings.checkbox.tie_selection?this.is_selected(b):(b=this.get_node(b),b&&b.id!==a.jstree.root?b.state.checked:!1)},this.get_checked=function(b){return this.settings.checkbox.tie_selection?this.get_selected(b):b?a.map(this._data.checkbox.selected,a.proxy(function(a){return this.get_node(a)},this)):this._data.checkbox.selected.slice()},this.get_top_checked=function(b){if(this.settings.checkbox.tie_selection)return this.get_top_selected(b);var c=this.get_checked(!0),d={},e,f,g,h;for(e=0,f=c.length;f>e;e++)d[c[e].id]=c[e];for(e=0,f=c.length;f>e;e++)for(g=0,h=c[e].children_d.length;h>g;g++)d[c[e].children_d[g]]&&delete d[c[e].children_d[g]];c=[];for(e in d)d.hasOwnProperty(e)&&c.push(e);return b?a.map(c,a.proxy(function(a){return this.get_node(a)},this)):c},this.get_bottom_checked=function(b){if(this.settings.checkbox.tie_selection)return this.get_bottom_selected(b);var c=this.get_checked(!0),d=[],e,f;for(e=0,f=c.length;f>e;e++)c[e].children.length||d.push(c[e].id);return b?a.map(d,a.proxy(function(a){return this.get_node(a)},this)):d},this.load_node=function(b,c){var e,f,g,h,i,j;if(!a.isArray(b)&&!this.settings.checkbox.tie_selection&&(j=this.get_node(b),j&&j.state.loaded))for(e=0,f=j.children_d.length;f>e;e++)this._model.data[j.children_d[e]].state.checked&&(i=!0,this._data.checkbox.selected=a.vakata.array_remove_item(this._data.checkbox.selected,j.children_d[e]));return d.load_node.apply(this,arguments)},this.get_state=function(){var a=d.get_state.apply(this,arguments);return this.settings.checkbox.tie_selection?a:(a.checkbox=this._data.checkbox.selected.slice(),a)},this.set_state=function(b,c){var e=d.set_state.apply(this,arguments);if(e&&b.checkbox){if(!this.settings.checkbox.tie_selection){this.uncheck_all();var f=this;a.each(b.checkbox,function(a,b){f.check_node(b)})}return delete b.checkbox,this.set_state(b,c),!1}return e},this.refresh=function(a,b){return this.settings.checkbox.tie_selection&&(this._data.checkbox.selected=[]),d.refresh.apply(this,arguments)}},a.jstree.defaults.conditionalselect=function(){return!0},a.jstree.plugins.conditionalselect=function(a,b){this.activate_node=function(a,c){return this.settings.conditionalselect.call(this,this.get_node(a),c)?b.activate_node.call(this,a,c):void 0}},a.jstree.defaults.contextmenu={select_node:!0,show_at_node:!0,items:function(b,c){return{create:{separator_before:!1,separator_after:!0,_disabled:!1,label:"Create",action:function(b){var c=a.jstree.reference(b.reference),d=c.get_node(b.reference);c.create_node(d,{},"last",function(a){try{c.edit(a)}catch(b){setTimeout(function(){c.edit(a)},0)}})}},rename:{separator_before:!1,separator_after:!1,_disabled:!1,label:"Rename",action:function(b){var c=a.jstree.reference(b.reference),d=c.get_node(b.reference);c.edit(d)}},remove:{separator_before:!1,icon:!1,separator_after:!1,_disabled:!1,label:"Delete",action:function(b){var c=a.jstree.reference(b.reference),d=c.get_node(b.reference);c.is_selected(d)?c.delete_node(c.get_selected()):c.delete_node(d)}},ccp:{separator_before:!0,icon:!1,separator_after:!1,label:"Edit",action:!1,submenu:{cut:{separator_before:!1,separator_after:!1,label:"Cut",action:function(b){var c=a.jstree.reference(b.reference),d=c.get_node(b.reference);c.is_selected(d)?c.cut(c.get_top_selected()):c.cut(d)}},copy:{separator_before:!1,icon:!1,separator_after:!1,label:"Copy",action:function(b){var c=a.jstree.reference(b.reference),d=c.get_node(b.reference);c.is_selected(d)?c.copy(c.get_top_selected()):c.copy(d)}},paste:{separator_before:!1,icon:!1,_disabled:function(b){return!a.jstree.reference(b.reference).can_paste()},separator_after:!1,label:"Paste",action:function(b){var c=a.jstree.reference(b.reference),d=c.get_node(b.reference);c.paste(d)}}}}}}},a.jstree.plugins.contextmenu=function(c,d){this.bind=function(){d.bind.call(this);var b=0,c=null,e,f;this.element.on("init.jstree loading.jstree ready.jstree",a.proxy(function(){this.get_container_ul().addClass("jstree-contextmenu")},this)).on("contextmenu.jstree",".jstree-anchor",a.proxy(function(a,d){"input"!==a.target.tagName.toLowerCase()&&(a.preventDefault(),b=a.ctrlKey?+new Date:0,(d||c)&&(b=+new Date+1e4),c&&clearTimeout(c),this.is_loading(a.currentTarget)||this.show_contextmenu(a.currentTarget,a.pageX,a.pageY,a))},this)).on("click.jstree",".jstree-anchor",a.proxy(function(c){this._data.contextmenu.visible&&(!b||+new Date-b>250)&&a.vakata.context.hide(),b=0},this)).on("touchstart.jstree",".jstree-anchor",function(b){b.originalEvent&&b.originalEvent.changedTouches&&b.originalEvent.changedTouches[0]&&(e=b.originalEvent.changedTouches[0].clientX,f=b.originalEvent.changedTouches[0].clientY,c=setTimeout(function(){a(b.currentTarget).trigger("contextmenu",!0)},750))}).on("touchmove.vakata.jstree",function(b){c&&b.originalEvent&&b.originalEvent.changedTouches&&b.originalEvent.changedTouches[0]&&(Math.abs(e-b.originalEvent.changedTouches[0].clientX)>10||Math.abs(f-b.originalEvent.changedTouches[0].clientY)>10)&&(clearTimeout(c),a.vakata.context.hide())}).on("touchend.vakata.jstree",function(a){c&&clearTimeout(c)}),a(i).on("context_hide.vakata.jstree",a.proxy(function(b,c){this._data.contextmenu.visible=!1,a(c.reference).removeClass("jstree-context")},this))},this.teardown=function(){this._data.contextmenu.visible&&a.vakata.context.hide(),d.teardown.call(this)},this.show_contextmenu=function(c,d,e,f){if(c=this.get_node(c),!c||c.id===a.jstree.root)return!1;var g=this.settings.contextmenu,h=this.get_node(c,!0),i=h.children(".jstree-anchor"),j=!1,k=!1;(g.show_at_node||d===b||e===b)&&(j=i.offset(),d=j.left,e=j.top+this._data.core.li_height),this.settings.contextmenu.select_node&&!this.is_selected(c)&&this.activate_node(c,f),k=g.items,a.isFunction(k)&&(k=k.call(this,c,a.proxy(function(a){this._show_contextmenu(c,d,e,a)},this))),a.isPlainObject(k)&&this._show_contextmenu(c,d,e,k)},this._show_contextmenu=function(b,c,d,e){var f=this.get_node(b,!0),g=f.children(".jstree-anchor");a(i).one("context_show.vakata.jstree",a.proxy(function(b,c){var d="jstree-contextmenu jstree-"+this.get_theme()+"-contextmenu";a(c.element).addClass(d),g.addClass("jstree-context")},this)),this._data.contextmenu.visible=!0,a.vakata.context.show(g,{x:c,y:d},e),this.trigger("show_contextmenu",{node:b,x:c,y:d})}},function(a){var b=!1,c={element:!1,reference:!1,position_x:0,position_y:0,items:[],html:"",is_visible:!1};a.vakata.context={settings:{hide_onmouseleave:0,icons:!0},_trigger:function(b){a(i).triggerHandler("context_"+b+".vakata",{reference:c.reference,element:c.element,position:{x:c.position_x,y:c.position_y}})},_execute:function(b){return b=c.items[b],b&&(!b._disabled||a.isFunction(b._disabled)&&!b._disabled({item:b,reference:c.reference,element:c.element}))&&b.action?b.action.call(null,{item:b,reference:c.reference,element:c.element,position:{x:c.position_x,y:c.position_y}}):!1},_parse:function(b,d){if(!b)return!1;d||(c.html="",c.items=[]);var e="",f=!1,g;return d&&(e+="<ul>"),a.each(b,function(b,d){return d?(c.items.push(d),!f&&d.separator_before&&(e+="<li class='vakata-context-separator'><a href='#' "+(a.vakata.context.settings.icons?"":'style="margin-left:0px;"')+">&#160;</a></li>"),f=!1,e+="<li class='"+(d._class||"")+(d._disabled===!0||a.isFunction(d._disabled)&&d._disabled({item:d,reference:c.reference,element:c.element})?" vakata-contextmenu-disabled ":"")+"' "+(d.shortcut?" data-shortcut='"+d.shortcut+"' ":"")+">",e+="<a href='#' rel='"+(c.items.length-1)+"' "+(d.title?"title='"+d.title+"'":"")+">",a.vakata.context.settings.icons&&(e+="<i ",
d.icon&&(e+=-1!==d.icon.indexOf("/")||-1!==d.icon.indexOf(".")?" style='background:url(\""+d.icon+"\") center center no-repeat' ":" class='"+d.icon+"' "),e+="></i><span class='vakata-contextmenu-sep'>&#160;</span>"),e+=(a.isFunction(d.label)?d.label({item:b,reference:c.reference,element:c.element}):d.label)+(d.shortcut?' <span class="vakata-contextmenu-shortcut vakata-contextmenu-shortcut-'+d.shortcut+'">'+(d.shortcut_label||"")+"</span>":"")+"</a>",d.submenu&&(g=a.vakata.context._parse(d.submenu,!0),g&&(e+=g)),e+="</li>",void(d.separator_after&&(e+="<li class='vakata-context-separator'><a href='#' "+(a.vakata.context.settings.icons?"":'style="margin-left:0px;"')+">&#160;</a></li>",f=!0))):!0}),e=e.replace(/<li class\='vakata-context-separator'\><\/li\>$/,""),d&&(e+="</ul>"),d||(c.html=e,a.vakata.context._trigger("parse")),e.length>10?e:!1},_show_submenu:function(c){if(c=a(c),c.length&&c.children("ul").length){var d=c.children("ul"),e=c.offset().left,f=e+c.outerWidth(),g=c.offset().top,h=d.width(),i=d.height(),j=a(window).width()+a(window).scrollLeft(),k=a(window).height()+a(window).scrollTop();b?c[f-(h+10+c.outerWidth())<0?"addClass":"removeClass"]("vakata-context-left"):c[f+h>j&&e>j-f?"addClass":"removeClass"]("vakata-context-right"),g+i+10>k&&d.css("bottom","-1px"),c.hasClass("vakata-context-right")?h>e&&d.css("margin-right",e-h):h>j-f&&d.css("margin-left",j-f-h),d.show()}},show:function(d,e,f){var g,h,j,k,l,m,n,o,p=!0;switch(c.element&&c.element.length&&c.element.width(""),p){case!e&&!d:return!1;case!!e&&!!d:c.reference=d,c.position_x=e.x,c.position_y=e.y;break;case!e&&!!d:c.reference=d,g=d.offset(),c.position_x=g.left+d.outerHeight(),c.position_y=g.top;break;case!!e&&!d:c.position_x=e.x,c.position_y=e.y}d&&!f&&a(d).data("vakata_contextmenu")&&(f=a(d).data("vakata_contextmenu")),a.vakata.context._parse(f)&&c.element.html(c.html),c.items.length&&(c.element.appendTo(i.body),h=c.element,j=c.position_x,k=c.position_y,l=h.width(),m=h.height(),n=a(window).width()+a(window).scrollLeft(),o=a(window).height()+a(window).scrollTop(),b&&(j-=h.outerWidth()-a(d).outerWidth(),j<a(window).scrollLeft()+20&&(j=a(window).scrollLeft()+20)),j+l+20>n&&(j=n-(l+20)),k+m+20>o&&(k=o-(m+20)),c.element.css({left:j,top:k}).show().find("a").first().focus().parent().addClass("vakata-context-hover"),c.is_visible=!0,a.vakata.context._trigger("show"))},hide:function(){c.is_visible&&(c.element.hide().find("ul").hide().end().find(":focus").blur().end().detach(),c.is_visible=!1,a.vakata.context._trigger("hide"))}},a(function(){b="rtl"===a(i.body).css("direction");var d=!1;c.element=a("<ul class='vakata-context'></ul>"),c.element.on("mouseenter","li",function(b){b.stopImmediatePropagation(),a.contains(this,b.relatedTarget)||(d&&clearTimeout(d),c.element.find(".vakata-context-hover").removeClass("vakata-context-hover").end(),a(this).siblings().find("ul").hide().end().end().parentsUntil(".vakata-context","li").addBack().addClass("vakata-context-hover"),a.vakata.context._show_submenu(this))}).on("mouseleave","li",function(b){a.contains(this,b.relatedTarget)||a(this).find(".vakata-context-hover").addBack().removeClass("vakata-context-hover")}).on("mouseleave",function(b){a(this).find(".vakata-context-hover").removeClass("vakata-context-hover"),a.vakata.context.settings.hide_onmouseleave&&(d=setTimeout(function(b){return function(){a.vakata.context.hide()}}(this),a.vakata.context.settings.hide_onmouseleave))}).on("click","a",function(b){b.preventDefault(),a(this).blur().parent().hasClass("vakata-context-disabled")||a.vakata.context._execute(a(this).attr("rel"))===!1||a.vakata.context.hide()}).on("keydown","a",function(b){var d=null;switch(b.which){case 13:case 32:b.type="click",b.preventDefault(),a(b.currentTarget).trigger(b);break;case 37:c.is_visible&&(c.element.find(".vakata-context-hover").last().closest("li").first().find("ul").hide().find(".vakata-context-hover").removeClass("vakata-context-hover").end().end().children("a").focus(),b.stopImmediatePropagation(),b.preventDefault());break;case 38:c.is_visible&&(d=c.element.find("ul:visible").addBack().last().children(".vakata-context-hover").removeClass("vakata-context-hover").prevAll("li:not(.vakata-context-separator)").first(),d.length||(d=c.element.find("ul:visible").addBack().last().children("li:not(.vakata-context-separator)").last()),d.addClass("vakata-context-hover").children("a").focus(),b.stopImmediatePropagation(),b.preventDefault());break;case 39:c.is_visible&&(c.element.find(".vakata-context-hover").last().children("ul").show().children("li:not(.vakata-context-separator)").removeClass("vakata-context-hover").first().addClass("vakata-context-hover").children("a").focus(),b.stopImmediatePropagation(),b.preventDefault());break;case 40:c.is_visible&&(d=c.element.find("ul:visible").addBack().last().children(".vakata-context-hover").removeClass("vakata-context-hover").nextAll("li:not(.vakata-context-separator)").first(),d.length||(d=c.element.find("ul:visible").addBack().last().children("li:not(.vakata-context-separator)").first()),d.addClass("vakata-context-hover").children("a").focus(),b.stopImmediatePropagation(),b.preventDefault());break;case 27:a.vakata.context.hide(),b.preventDefault()}}).on("keydown",function(a){a.preventDefault();var b=c.element.find(".vakata-contextmenu-shortcut-"+a.which).parent();b.parent().not(".vakata-context-disabled")&&b.click()}),a(i).on("mousedown.vakata.jstree",function(b){c.is_visible&&c.element[0]!==b.target&&!a.contains(c.element[0],b.target)&&a.vakata.context.hide()}).on("context_show.vakata.jstree",function(a,d){c.element.find("li:has(ul)").children("a").addClass("vakata-context-parent"),b&&c.element.addClass("vakata-context-rtl").css("direction","rtl"),c.element.find("ul").hide().end()})})}(a),a.jstree.defaults.dnd={copy:!0,open_timeout:500,is_draggable:!0,check_while_dragging:!0,always_copy:!1,inside_pos:0,drag_selection:!0,touch:!0,large_drop_target:!1,large_drag_target:!1,use_html5:!1};var k,l;a.jstree.plugins.dnd=function(b,c){this.init=function(a,b){c.init.call(this,a,b),this.settings.dnd.use_html5=this.settings.dnd.use_html5&&"draggable"in i.createElement("span")},this.bind=function(){c.bind.call(this),this.element.on(this.settings.dnd.use_html5?"dragstart.jstree":"mousedown.jstree touchstart.jstree",this.settings.dnd.large_drag_target?".jstree-node":".jstree-anchor",a.proxy(function(b){if(this.settings.dnd.large_drag_target&&a(b.target).closest(".jstree-node")[0]!==b.currentTarget)return!0;if("touchstart"===b.type&&(!this.settings.dnd.touch||"selected"===this.settings.dnd.touch&&!a(b.currentTarget).closest(".jstree-node").children(".jstree-anchor").hasClass("jstree-clicked")))return!0;var c=this.get_node(b.target),d=this.is_selected(c)&&this.settings.dnd.drag_selection?this.get_top_selected().length:1,e=d>1?d+" "+this.get_string("nodes"):this.get_text(b.currentTarget);if(this.settings.core.force_text&&(e=a.vakata.html.escape(e)),c&&c.id&&c.id!==a.jstree.root&&(1===b.which||"touchstart"===b.type||"dragstart"===b.type)&&(this.settings.dnd.is_draggable===!0||a.isFunction(this.settings.dnd.is_draggable)&&this.settings.dnd.is_draggable.call(this,d>1?this.get_top_selected(!0):[c],b))){if(k={jstree:!0,origin:this,obj:this.get_node(c,!0),nodes:d>1?this.get_top_selected():[c.id]},l=b.currentTarget,!this.settings.dnd.use_html5)return this.element.trigger("mousedown.jstree"),a.vakata.dnd.start(b,k,'<div id="jstree-dnd" class="jstree-'+this.get_theme()+" jstree-"+this.get_theme()+"-"+this.get_theme_variant()+" "+(this.settings.core.themes.responsive?" jstree-dnd-responsive":"")+'"><i class="jstree-icon jstree-er"></i>'+e+'<ins class="jstree-copy" style="display:none;">+</ins></div>');a.vakata.dnd._trigger("start",b,{helper:a(),element:l,data:k})}},this)),this.settings.dnd.use_html5&&this.element.on("dragover.jstree",function(b){return b.preventDefault(),a.vakata.dnd._trigger("move",b,{helper:a(),element:l,data:k}),!1}).on("drop.jstree",a.proxy(function(b){return b.preventDefault(),a.vakata.dnd._trigger("stop",b,{helper:a(),element:l,data:k}),!1},this))},this.redraw_node=function(a,b,d,e){if(a=c.redraw_node.apply(this,arguments),a&&this.settings.dnd.use_html5)if(this.settings.dnd.large_drag_target)a.setAttribute("draggable",!0);else{var f,g,h=null;for(f=0,g=a.childNodes.length;g>f;f++)if(a.childNodes[f]&&a.childNodes[f].className&&-1!==a.childNodes[f].className.indexOf("jstree-anchor")){h=a.childNodes[f];break}h&&h.setAttribute("draggable",!0)}return a}},a(function(){var c=!1,d=!1,e=!1,f=!1,g=a('<div id="jstree-marker">&#160;</div>').hide();a(i).on("dragover.vakata.jstree",function(b){l&&a.vakata.dnd._trigger("move",b,{helper:a(),element:l,data:k})}).on("drop.vakata.jstree",function(b){l&&(a.vakata.dnd._trigger("stop",b,{helper:a(),element:l,data:k}),l=null,k=null)}).on("dnd_start.vakata.jstree",function(a,b){c=!1,e=!1,b&&b.data&&b.data.jstree&&g.appendTo(i.body)}).on("dnd_move.vakata.jstree",function(h,i){var j=i.event.target!==e.target;if(f&&(!i.event||"dragover"!==i.event.type||j)&&clearTimeout(f),i&&i.data&&i.data.jstree&&(!i.event.target.id||"jstree-marker"!==i.event.target.id)){e=i.event;var k=a.jstree.reference(i.event.target),l=!1,m=!1,n=!1,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E;if(k&&k._data&&k._data.dnd)if(g.attr("class","jstree-"+k.get_theme()+(k.settings.core.themes.responsive?" jstree-dnd-responsive":"")),D=i.data.origin&&(i.data.origin.settings.dnd.always_copy||i.data.origin.settings.dnd.copy&&(i.event.metaKey||i.event.ctrlKey)),i.helper.children().attr("class","jstree-"+k.get_theme()+" jstree-"+k.get_theme()+"-"+k.get_theme_variant()+" "+(k.settings.core.themes.responsive?" jstree-dnd-responsive":"")).find(".jstree-copy").first()[D?"show":"hide"](),i.event.target!==k.element[0]&&i.event.target!==k.get_container_ul()[0]||0!==k.get_container_ul().children().length){if(l=k.settings.dnd.large_drop_target?a(i.event.target).closest(".jstree-node").children(".jstree-anchor"):a(i.event.target).closest(".jstree-anchor"),l&&l.length&&l.parent().is(".jstree-closed, .jstree-open, .jstree-leaf")&&(m=l.offset(),n=(i.event.pageY!==b?i.event.pageY:i.event.originalEvent.pageY)-m.top,r=l.outerHeight(),u=r/3>n?["b","i","a"]:n>r-r/3?["a","i","b"]:n>r/2?["i","a","b"]:["i","b","a"],a.each(u,function(b,e){switch(e){case"b":p=m.left-6,q=m.top,s=k.get_parent(l),t=l.parent().index();break;case"i":B=k.settings.dnd.inside_pos,C=k.get_node(l.parent()),p=m.left-2,q=m.top+r/2+1,s=C.id,t="first"===B?0:"last"===B?C.children.length:Math.min(B,C.children.length);break;case"a":p=m.left-6,q=m.top+r,s=k.get_parent(l),t=l.parent().index()+1}for(v=!0,w=0,x=i.data.nodes.length;x>w;w++)if(y=i.data.origin&&(i.data.origin.settings.dnd.always_copy||i.data.origin.settings.dnd.copy&&(i.event.metaKey||i.event.ctrlKey))?"copy_node":"move_node",z=t,"move_node"===y&&"a"===e&&i.data.origin&&i.data.origin===k&&s===k.get_parent(i.data.nodes[w])&&(A=k.get_node(s),z>a.inArray(i.data.nodes[w],A.children)&&(z-=1)),v=v&&(k&&k.settings&&k.settings.dnd&&k.settings.dnd.check_while_dragging===!1||k.check(y,i.data.origin&&i.data.origin!==k?i.data.origin.get_node(i.data.nodes[w]):i.data.nodes[w],s,z,{dnd:!0,ref:k.get_node(l.parent()),pos:e,origin:i.data.origin,is_multi:i.data.origin&&i.data.origin!==k,is_foreign:!i.data.origin})),!v){k&&k.last_error&&(d=k.last_error());break}return"i"===e&&l.parent().is(".jstree-closed")&&k.settings.dnd.open_timeout&&(!i.event||"dragover"!==i.event.type||j)&&(f&&clearTimeout(f),f=setTimeout(function(a,b){return function(){a.open_node(b)}}(k,l),k.settings.dnd.open_timeout)),v?(E=k.get_node(s,!0),E.hasClass(".jstree-dnd-parent")||(a(".jstree-dnd-parent").removeClass("jstree-dnd-parent"),E.addClass("jstree-dnd-parent")),c={ins:k,par:s,pos:"i"!==e||"last"!==B||0!==t||k.is_loaded(C)?t:"last"},g.css({left:p+"px",top:q+"px"}).show(),i.helper.find(".jstree-icon").first().removeClass("jstree-er").addClass("jstree-ok"),i.event.originalEvent&&i.event.originalEvent.dataTransfer&&(i.event.originalEvent.dataTransfer.dropEffect=D?"copy":"move"),d={},u=!0,!1):void 0}),u===!0))return}else{for(v=!0,w=0,x=i.data.nodes.length;x>w;w++)if(v=v&&k.check(i.data.origin&&(i.data.origin.settings.dnd.always_copy||i.data.origin.settings.dnd.copy&&(i.event.metaKey||i.event.ctrlKey))?"copy_node":"move_node",i.data.origin&&i.data.origin!==k?i.data.origin.get_node(i.data.nodes[w]):i.data.nodes[w],a.jstree.root,"last",{dnd:!0,ref:k.get_node(a.jstree.root),pos:"i",origin:i.data.origin,is_multi:i.data.origin&&i.data.origin!==k,is_foreign:!i.data.origin}),!v)break;if(v)return c={ins:k,par:a.jstree.root,pos:"last"},g.hide(),i.helper.find(".jstree-icon").first().removeClass("jstree-er").addClass("jstree-ok"),void(i.event.originalEvent&&i.event.originalEvent.dataTransfer&&(i.event.originalEvent.dataTransfer.dropEffect=D?"copy":"move"))}a(".jstree-dnd-parent").removeClass("jstree-dnd-parent"),c=!1,i.helper.find(".jstree-icon").removeClass("jstree-ok").addClass("jstree-er"),i.event.originalEvent&&i.event.originalEvent.dataTransfer,g.hide()}}).on("dnd_scroll.vakata.jstree",function(a,b){b&&b.data&&b.data.jstree&&(g.hide(),c=!1,e=!1,b.helper.find(".jstree-icon").first().removeClass("jstree-ok").addClass("jstree-er"))}).on("dnd_stop.vakata.jstree",function(b,h){if(a(".jstree-dnd-parent").removeClass("jstree-dnd-parent"),f&&clearTimeout(f),h&&h.data&&h.data.jstree){g.hide().detach();var i,j,k=[];if(c){for(i=0,j=h.data.nodes.length;j>i;i++)k[i]=h.data.origin?h.data.origin.get_node(h.data.nodes[i]):h.data.nodes[i];c.ins[h.data.origin&&(h.data.origin.settings.dnd.always_copy||h.data.origin.settings.dnd.copy&&(h.event.metaKey||h.event.ctrlKey))?"copy_node":"move_node"](k,c.par,c.pos,!1,!1,!1,h.data.origin)}else i=a(h.event.target).closest(".jstree"),i.length&&d&&d.error&&"check"===d.error&&(i=i.jstree(!0),i&&i.settings.core.error.call(this,d));e=!1,c=!1}}).on("keyup.jstree keydown.jstree",function(b,h){h=a.vakata.dnd._get(),h&&h.data&&h.data.jstree&&("keyup"===b.type&&27===b.which?(f&&clearTimeout(f),c=!1,d=!1,e=!1,f=!1,g.hide().detach(),a.vakata.dnd._clean()):(h.helper.find(".jstree-copy").first()[h.data.origin&&(h.data.origin.settings.dnd.always_copy||h.data.origin.settings.dnd.copy&&(b.metaKey||b.ctrlKey))?"show":"hide"](),e&&(e.metaKey=b.metaKey,e.ctrlKey=b.ctrlKey,a.vakata.dnd._trigger("move",e))))})}),function(a){a.vakata.html={div:a("<div />"),escape:function(b){return a.vakata.html.div.text(b).html()},strip:function(b){return a.vakata.html.div.empty().append(a.parseHTML(b)).text()}};var c={element:!1,target:!1,is_down:!1,is_drag:!1,helper:!1,helper_w:0,data:!1,init_x:0,init_y:0,scroll_l:0,scroll_t:0,scroll_e:!1,scroll_i:!1,is_touch:!1};a.vakata.dnd={settings:{scroll_speed:10,scroll_proximity:20,helper_left:5,helper_top:10,threshold:5,threshold_touch:10},_trigger:function(c,d,e){e===b&&(e=a.vakata.dnd._get()),e.event=d,a(i).triggerHandler("dnd_"+c+".vakata",e)},_get:function(){return{data:c.data,element:c.element,helper:c.helper}},_clean:function(){c.helper&&c.helper.remove(),c.scroll_i&&(clearInterval(c.scroll_i),c.scroll_i=!1),c={element:!1,target:!1,is_down:!1,is_drag:!1,helper:!1,helper_w:0,data:!1,init_x:0,init_y:0,scroll_l:0,scroll_t:0,scroll_e:!1,scroll_i:!1,is_touch:!1},a(i).off("mousemove.vakata.jstree touchmove.vakata.jstree",a.vakata.dnd.drag),a(i).off("mouseup.vakata.jstree touchend.vakata.jstree",a.vakata.dnd.stop)},_scroll:function(b){if(!c.scroll_e||!c.scroll_l&&!c.scroll_t)return c.scroll_i&&(clearInterval(c.scroll_i),c.scroll_i=!1),!1;if(!c.scroll_i)return c.scroll_i=setInterval(a.vakata.dnd._scroll,100),!1;if(b===!0)return!1;var d=c.scroll_e.scrollTop(),e=c.scroll_e.scrollLeft();c.scroll_e.scrollTop(d+c.scroll_t*a.vakata.dnd.settings.scroll_speed),c.scroll_e.scrollLeft(e+c.scroll_l*a.vakata.dnd.settings.scroll_speed),(d!==c.scroll_e.scrollTop()||e!==c.scroll_e.scrollLeft())&&a.vakata.dnd._trigger("scroll",c.scroll_e)},start:function(b,d,e){"touchstart"===b.type&&b.originalEvent&&b.originalEvent.changedTouches&&b.originalEvent.changedTouches[0]&&(b.pageX=b.originalEvent.changedTouches[0].pageX,b.pageY=b.originalEvent.changedTouches[0].pageY,b.target=i.elementFromPoint(b.originalEvent.changedTouches[0].pageX-window.pageXOffset,b.originalEvent.changedTouches[0].pageY-window.pageYOffset)),c.is_drag&&a.vakata.dnd.stop({});try{b.currentTarget.unselectable="on",b.currentTarget.onselectstart=function(){return!1},b.currentTarget.style&&(b.currentTarget.style.touchAction="none",b.currentTarget.style.msTouchAction="none",b.currentTarget.style.MozUserSelect="none")}catch(f){}return c.init_x=b.pageX,c.init_y=b.pageY,c.data=d,c.is_down=!0,c.element=b.currentTarget,c.target=b.target,c.is_touch="touchstart"===b.type,e!==!1&&(c.helper=a("<div id='vakata-dnd'></div>").html(e).css({display:"block",margin:"0",padding:"0",position:"absolute",top:"-2000px",lineHeight:"16px",zIndex:"10000"})),a(i).on("mousemove.vakata.jstree touchmove.vakata.jstree",a.vakata.dnd.drag),a(i).on("mouseup.vakata.jstree touchend.vakata.jstree",a.vakata.dnd.stop),!1},drag:function(b){if("touchmove"===b.type&&b.originalEvent&&b.originalEvent.changedTouches&&b.originalEvent.changedTouches[0]&&(b.pageX=b.originalEvent.changedTouches[0].pageX,b.pageY=b.originalEvent.changedTouches[0].pageY,b.target=i.elementFromPoint(b.originalEvent.changedTouches[0].pageX-window.pageXOffset,b.originalEvent.changedTouches[0].pageY-window.pageYOffset)),c.is_down){if(!c.is_drag){if(!(Math.abs(b.pageX-c.init_x)>(c.is_touch?a.vakata.dnd.settings.threshold_touch:a.vakata.dnd.settings.threshold)||Math.abs(b.pageY-c.init_y)>(c.is_touch?a.vakata.dnd.settings.threshold_touch:a.vakata.dnd.settings.threshold)))return;c.helper&&(c.helper.appendTo(i.body),c.helper_w=c.helper.outerWidth()),c.is_drag=!0,a(c.target).one("click.vakata",!1),a.vakata.dnd._trigger("start",b)}var d=!1,e=!1,f=!1,g=!1,h=!1,j=!1,k=!1,l=!1,m=!1,n=!1;return c.scroll_t=0,c.scroll_l=0,c.scroll_e=!1,a(a(b.target).parentsUntil("body").addBack().get().reverse()).filter(function(){return/^auto|scroll$/.test(a(this).css("overflow"))&&(this.scrollHeight>this.offsetHeight||this.scrollWidth>this.offsetWidth)}).each(function(){var d=a(this),e=d.offset();return this.scrollHeight>this.offsetHeight&&(e.top+d.height()-b.pageY<a.vakata.dnd.settings.scroll_proximity&&(c.scroll_t=1),b.pageY-e.top<a.vakata.dnd.settings.scroll_proximity&&(c.scroll_t=-1)),this.scrollWidth>this.offsetWidth&&(e.left+d.width()-b.pageX<a.vakata.dnd.settings.scroll_proximity&&(c.scroll_l=1),b.pageX-e.left<a.vakata.dnd.settings.scroll_proximity&&(c.scroll_l=-1)),c.scroll_t||c.scroll_l?(c.scroll_e=a(this),!1):void 0}),c.scroll_e||(d=a(i),e=a(window),f=d.height(),g=e.height(),h=d.width(),j=e.width(),k=d.scrollTop(),l=d.scrollLeft(),f>g&&b.pageY-k<a.vakata.dnd.settings.scroll_proximity&&(c.scroll_t=-1),f>g&&g-(b.pageY-k)<a.vakata.dnd.settings.scroll_proximity&&(c.scroll_t=1),h>j&&b.pageX-l<a.vakata.dnd.settings.scroll_proximity&&(c.scroll_l=-1),h>j&&j-(b.pageX-l)<a.vakata.dnd.settings.scroll_proximity&&(c.scroll_l=1),(c.scroll_t||c.scroll_l)&&(c.scroll_e=d)),c.scroll_e&&a.vakata.dnd._scroll(!0),c.helper&&(m=parseInt(b.pageY+a.vakata.dnd.settings.helper_top,10),n=parseInt(b.pageX+a.vakata.dnd.settings.helper_left,10),f&&m+25>f&&(m=f-50),h&&n+c.helper_w>h&&(n=h-(c.helper_w+2)),c.helper.css({left:n+"px",top:m+"px"})),a.vakata.dnd._trigger("move",b),!1}},stop:function(b){if("touchend"===b.type&&b.originalEvent&&b.originalEvent.changedTouches&&b.originalEvent.changedTouches[0]&&(b.pageX=b.originalEvent.changedTouches[0].pageX,b.pageY=b.originalEvent.changedTouches[0].pageY,b.target=i.elementFromPoint(b.originalEvent.changedTouches[0].pageX-window.pageXOffset,b.originalEvent.changedTouches[0].pageY-window.pageYOffset)),c.is_drag)b.target!==c.target&&a(c.target).off("click.vakata"),a.vakata.dnd._trigger("stop",b);else if("touchend"===b.type&&b.target===c.target){var d=setTimeout(function(){a(b.target).click()},100);a(b.target).one("click",function(){d&&clearTimeout(d)})}return a.vakata.dnd._clean(),!1}}}(a),a.jstree.defaults.massload=null,a.jstree.plugins.massload=function(b,c){this.init=function(a,b){this._data.massload={},c.init.call(this,a,b)},this._load_nodes=function(b,d,e,f){var g=this.settings.massload,h=JSON.stringify(b),i=[],j=this._model.data,k,l,m;if(!e){for(k=0,l=b.length;l>k;k++)(!j[b[k]]||!j[b[k]].state.loaded&&!j[b[k]].state.failed||f)&&(i.push(b[k]),m=this.get_node(b[k],!0),m&&m.length&&m.addClass("jstree-loading").attr("aria-busy",!0));if(this._data.massload={},i.length){if(a.isFunction(g))return g.call(this,i,a.proxy(function(a){var g,h;if(a)for(g in a)a.hasOwnProperty(g)&&(this._data.massload[g]=a[g]);for(g=0,h=b.length;h>g;g++)m=this.get_node(b[g],!0),m&&m.length&&m.removeClass("jstree-loading").attr("aria-busy",!1);c._load_nodes.call(this,b,d,e,f)},this));if("object"==typeof g&&g&&g.url)return g=a.extend(!0,{},g),a.isFunction(g.url)&&(g.url=g.url.call(this,i)),a.isFunction(g.data)&&(g.data=g.data.call(this,i)),a.ajax(g).done(a.proxy(function(a,g,h){var i,j;if(a)for(i in a)a.hasOwnProperty(i)&&(this._data.massload[i]=a[i]);for(i=0,j=b.length;j>i;i++)m=this.get_node(b[i],!0),m&&m.length&&m.removeClass("jstree-loading").attr("aria-busy",!1);c._load_nodes.call(this,b,d,e,f)},this)).fail(a.proxy(function(a){c._load_nodes.call(this,b,d,e,f)},this))}}return c._load_nodes.call(this,b,d,e,f)},this._load_node=function(b,d){var e=this._data.massload[b.id],f=null,g;return e?(f=this["string"==typeof e?"_append_html_data":"_append_json_data"](b,"string"==typeof e?a(a.parseHTML(e)).filter(function(){return 3!==this.nodeType}):e,function(a){d.call(this,a)}),g=this.get_node(b.id,!0),g&&g.length&&g.removeClass("jstree-loading").attr("aria-busy",!1),delete this._data.massload[b.id],f):c._load_node.call(this,b,d)}},a.jstree.defaults.search={ajax:!1,fuzzy:!1,case_sensitive:!1,show_only_matches:!1,show_only_matches_children:!1,close_opened_onclear:!0,search_leaves_only:!1,search_callback:!1},a.jstree.plugins.search=function(c,d){this.bind=function(){d.bind.call(this),this._data.search.str="",this._data.search.dom=a(),this._data.search.res=[],this._data.search.opn=[],this._data.search.som=!1,this._data.search.smc=!1,this._data.search.hdn=[],this.element.on("search.jstree",a.proxy(function(b,c){if(this._data.search.som&&c.res.length){var d=this._model.data,e,f,g=[],h,i;for(e=0,f=c.res.length;f>e;e++)if(d[c.res[e]]&&!d[c.res[e]].state.hidden&&(g.push(c.res[e]),g=g.concat(d[c.res[e]].parents),this._data.search.smc))for(h=0,i=d[c.res[e]].children_d.length;i>h;h++)d[d[c.res[e]].children_d[h]]&&!d[d[c.res[e]].children_d[h]].state.hidden&&g.push(d[c.res[e]].children_d[h]);g=a.vakata.array_remove_item(a.vakata.array_unique(g),a.jstree.root),this._data.search.hdn=this.hide_all(!0),this.show_node(g,!0),this.redraw(!0)}},this)).on("clear_search.jstree",a.proxy(function(a,b){this._data.search.som&&b.res.length&&(this.show_node(this._data.search.hdn,!0),this.redraw(!0))},this))},this.search=function(c,d,e,f,g,h){if(c===!1||""===a.trim(c.toString()))return this.clear_search();f=this.get_node(f),f=f&&f.id?f.id:null,c=c.toString();var i=this.settings.search,j=i.ajax?i.ajax:!1,k=this._model.data,l=null,m=[],n=[],o,p;if(this._data.search.res.length&&!g&&this.clear_search(),e===b&&(e=i.show_only_matches),h===b&&(h=i.show_only_matches_children),!d&&j!==!1)return a.isFunction(j)?j.call(this,c,a.proxy(function(b){b&&b.d&&(b=b.d),this._load_nodes(a.isArray(b)?a.vakata.array_unique(b):[],function(){this.search(c,!0,e,f,g,h)})},this),f):(j=a.extend({},j),j.data||(j.data={}),j.data.str=c,f&&(j.data.inside=f),this._data.search.lastRequest&&this._data.search.lastRequest.abort(),this._data.search.lastRequest=a.ajax(j).fail(a.proxy(function(){this._data.core.last_error={error:"ajax",plugin:"search",id:"search_01",reason:"Could not load search parents",data:JSON.stringify(j)},this.settings.core.error.call(this,this._data.core.last_error)},this)).done(a.proxy(function(b){b&&b.d&&(b=b.d),this._load_nodes(a.isArray(b)?a.vakata.array_unique(b):[],function(){this.search(c,!0,e,f,g,h)})},this)),this._data.search.lastRequest);if(g||(this._data.search.str=c,this._data.search.dom=a(),this._data.search.res=[],this._data.search.opn=[],this._data.search.som=e,this._data.search.smc=h),l=new a.vakata.search(c,!0,{caseSensitive:i.case_sensitive,fuzzy:i.fuzzy}),a.each(k[f?f:a.jstree.root].children_d,function(a,b){var d=k[b];d.text&&!d.state.hidden&&(!i.search_leaves_only||d.state.loaded&&0===d.children.length)&&(i.search_callback&&i.search_callback.call(this,c,d)||!i.search_callback&&l.search(d.text).isMatch)&&(m.push(b),n=n.concat(d.parents))}),m.length){for(n=a.vakata.array_unique(n),o=0,p=n.length;p>o;o++)n[o]!==a.jstree.root&&k[n[o]]&&this.open_node(n[o],null,0)===!0&&this._data.search.opn.push(n[o]);g?(this._data.search.dom=this._data.search.dom.add(a(this.element[0].querySelectorAll("#"+a.map(m,function(b){return-1!=="0123456789".indexOf(b[0])?"\\3"+b[0]+" "+b.substr(1).replace(a.jstree.idregex,"\\$&"):b.replace(a.jstree.idregex,"\\$&")}).join(", #")))),this._data.search.res=a.vakata.array_unique(this._data.search.res.concat(m))):(this._data.search.dom=a(this.element[0].querySelectorAll("#"+a.map(m,function(b){return-1!=="0123456789".indexOf(b[0])?"\\3"+b[0]+" "+b.substr(1).replace(a.jstree.idregex,"\\$&"):b.replace(a.jstree.idregex,"\\$&")}).join(", #"))),this._data.search.res=m),this._data.search.dom.children(".jstree-anchor").addClass("jstree-search")}this.trigger("search",{nodes:this._data.search.dom,str:c,res:this._data.search.res,show_only_matches:e})},this.clear_search=function(){this.settings.search.close_opened_onclear&&this.close_node(this._data.search.opn,0),this.trigger("clear_search",{nodes:this._data.search.dom,str:this._data.search.str,res:this._data.search.res}),this._data.search.res.length&&(this._data.search.dom=a(this.element[0].querySelectorAll("#"+a.map(this._data.search.res,function(b){return-1!=="0123456789".indexOf(b[0])?"\\3"+b[0]+" "+b.substr(1).replace(a.jstree.idregex,"\\$&"):b.replace(a.jstree.idregex,"\\$&")}).join(", #"))),this._data.search.dom.children(".jstree-anchor").removeClass("jstree-search")),this._data.search.str="",this._data.search.res=[],this._data.search.opn=[],this._data.search.dom=a()},this.redraw_node=function(b,c,e,f){if(b=d.redraw_node.apply(this,arguments),b&&-1!==a.inArray(b.id,this._data.search.res)){var g,h,i=null;for(g=0,h=b.childNodes.length;h>g;g++)if(b.childNodes[g]&&b.childNodes[g].className&&-1!==b.childNodes[g].className.indexOf("jstree-anchor")){i=b.childNodes[g];break}i&&(i.className+=" jstree-search")}return b}},function(a){a.vakata.search=function(b,c,d){d=d||{},d=a.extend({},a.vakata.search.defaults,d),d.fuzzy!==!1&&(d.fuzzy=!0),b=d.caseSensitive?b:b.toLowerCase();var e=d.location,f=d.distance,g=d.threshold,h=b.length,i,j,k,l;return h>32&&(d.fuzzy=!1),d.fuzzy&&(i=1<<h-1,j=function(){var a={},c=0;for(c=0;h>c;c++)a[b.charAt(c)]=0;for(c=0;h>c;c++)a[b.charAt(c)]|=1<<h-c-1;return a}(),k=function(a,b){var c=a/h,d=Math.abs(e-b);return f?c+d/f:d?1:c}),l=function(a){if(a=d.caseSensitive?a:a.toLowerCase(),b===a||-1!==a.indexOf(b))return{isMatch:!0,score:0};if(!d.fuzzy)return{isMatch:!1,score:1};var c,f,l=a.length,m=g,n=a.indexOf(b,e),o,p,q=h+l,r,s,t,u,v,w=1,x=[];for(-1!==n&&(m=Math.min(k(0,n),m),n=a.lastIndexOf(b,e+h),-1!==n&&(m=Math.min(k(0,n),m))),n=-1,c=0;h>c;c++){o=0,p=q;while(p>o)k(c,e+p)<=m?o=p:q=p,p=Math.floor((q-o)/2+o);for(q=p,s=Math.max(1,e-p+1),t=Math.min(e+p,l)+h,u=new Array(t+2),u[t+1]=(1<<c)-1,f=t;f>=s;f--)if(v=j[a.charAt(f-1)],0===c?u[f]=(u[f+1]<<1|1)&v:u[f]=(u[f+1]<<1|1)&v|((r[f+1]|r[f])<<1|1)|r[f+1],u[f]&i&&(w=k(c,f-1),m>=w)){if(m=w,n=f-1,x.push(n),!(n>e))break;s=Math.max(1,2*e-n)}if(k(c+1,e)>m)break;r=u}return{isMatch:n>=0,score:w}},c===!0?{search:l}:l(c)},a.vakata.search.defaults={location:0,distance:100,threshold:.6,fuzzy:!1,caseSensitive:!1}}(a),a.jstree.defaults.sort=function(a,b){return this.get_text(a)>this.get_text(b)?1:-1},a.jstree.plugins.sort=function(b,c){this.bind=function(){c.bind.call(this),this.element.on("model.jstree",a.proxy(function(a,b){this.sort(b.parent,!0)},this)).on("rename_node.jstree create_node.jstree",a.proxy(function(a,b){this.sort(b.parent||b.node.parent,!1),this.redraw_node(b.parent||b.node.parent,!0)},this)).on("move_node.jstree copy_node.jstree",a.proxy(function(a,b){this.sort(b.parent,!1),this.redraw_node(b.parent,!0)},this))},this.sort=function(b,c){var d,e;if(b=this.get_node(b),b&&b.children&&b.children.length&&(b.children.sort(a.proxy(this.settings.sort,this)),c))for(d=0,e=b.children_d.length;e>d;d++)this.sort(b.children_d[d],!1)}};var m=!1;a.jstree.defaults.state={key:"jstree",events:"changed.jstree open_node.jstree close_node.jstree check_node.jstree uncheck_node.jstree",ttl:!1,filter:!1,preserve_loaded:!1},a.jstree.plugins.state=function(b,c){this.bind=function(){c.bind.call(this);var b=a.proxy(function(){this.element.on(this.settings.state.events,a.proxy(function(){m&&clearTimeout(m),m=setTimeout(a.proxy(function(){this.save_state()},this),100)},this)),this.trigger("state_ready")},this);this.element.on("ready.jstree",a.proxy(function(a,c){this.element.one("restore_state.jstree",b),this.restore_state()||b()},this))},this.save_state=function(){var b=this.get_state();this.settings.state.preserve_loaded||delete b.core.loaded;var c={state:b,ttl:this.settings.state.ttl,sec:+new Date};a.vakata.storage.set(this.settings.state.key,JSON.stringify(c))},this.restore_state=function(){var b=a.vakata.storage.get(this.settings.state.key);if(b)try{b=JSON.parse(b)}catch(c){return!1}return b&&b.ttl&&b.sec&&+new Date-b.sec>b.ttl?!1:(b&&b.state&&(b=b.state),b&&a.isFunction(this.settings.state.filter)&&(b=this.settings.state.filter.call(this,b)),b?(this.settings.state.preserve_loaded||delete b.core.loaded,this.element.one("set_state.jstree",function(c,d){d.instance.trigger("restore_state",{state:a.extend(!0,{},b)})}),this.set_state(b),!0):!1)},this.clear_state=function(){return a.vakata.storage.del(this.settings.state.key)}},function(a,b){a.vakata.storage={set:function(a,b){return window.localStorage.setItem(a,b)},get:function(a){return window.localStorage.getItem(a)},del:function(a){return window.localStorage.removeItem(a)}}}(a),a.jstree.defaults.types={"default":{}},a.jstree.defaults.types[a.jstree.root]={},a.jstree.plugins.types=function(c,d){this.init=function(c,e){var f,g;if(e&&e.types&&e.types["default"])for(f in e.types)if("default"!==f&&f!==a.jstree.root&&e.types.hasOwnProperty(f))for(g in e.types["default"])e.types["default"].hasOwnProperty(g)&&e.types[f][g]===b&&(e.types[f][g]=e.types["default"][g]);d.init.call(this,c,e),this._model.data[a.jstree.root].type=a.jstree.root},this.refresh=function(b,c){d.refresh.call(this,b,c),this._model.data[a.jstree.root].type=a.jstree.root},this.bind=function(){this.element.on("model.jstree",a.proxy(function(c,d){var e=this._model.data,f=d.nodes,g=this.settings.types,h,i,j="default",k;for(h=0,i=f.length;i>h;h++){if(j="default",e[f[h]].original&&e[f[h]].original.type&&g[e[f[h]].original.type]&&(j=e[f[h]].original.type),e[f[h]].data&&e[f[h]].data.jstree&&e[f[h]].data.jstree.type&&g[e[f[h]].data.jstree.type]&&(j=e[f[h]].data.jstree.type),e[f[h]].type=j,e[f[h]].icon===!0&&g[j].icon!==b&&(e[f[h]].icon=g[j].icon),g[j].li_attr!==b&&"object"==typeof g[j].li_attr)for(k in g[j].li_attr)if(g[j].li_attr.hasOwnProperty(k)){if("id"===k)continue;e[f[h]].li_attr[k]===b?e[f[h]].li_attr[k]=g[j].li_attr[k]:"class"===k&&(e[f[h]].li_attr["class"]=g[j].li_attr["class"]+" "+e[f[h]].li_attr["class"])}if(g[j].a_attr!==b&&"object"==typeof g[j].a_attr)for(k in g[j].a_attr)if(g[j].a_attr.hasOwnProperty(k)){if("id"===k)continue;e[f[h]].a_attr[k]===b?e[f[h]].a_attr[k]=g[j].a_attr[k]:"href"===k&&"#"===e[f[h]].a_attr[k]?e[f[h]].a_attr.href=g[j].a_attr.href:"class"===k&&(e[f[h]].a_attr["class"]=g[j].a_attr["class"]+" "+e[f[h]].a_attr["class"])}}e[a.jstree.root].type=a.jstree.root},this)),d.bind.call(this)},this.get_json=function(b,c,e){var f,g,h=this._model.data,i=c?a.extend(!0,{},c,{no_id:!1}):{},j=d.get_json.call(this,b,i,e);if(j===!1)return!1;if(a.isArray(j))for(f=0,g=j.length;g>f;f++)j[f].type=j[f].id&&h[j[f].id]&&h[j[f].id].type?h[j[f].id].type:"default",
c&&c.no_id&&(delete j[f].id,j[f].li_attr&&j[f].li_attr.id&&delete j[f].li_attr.id,j[f].a_attr&&j[f].a_attr.id&&delete j[f].a_attr.id);else j.type=j.id&&h[j.id]&&h[j.id].type?h[j.id].type:"default",c&&c.no_id&&(j=this._delete_ids(j));return j},this._delete_ids=function(b){if(a.isArray(b)){for(var c=0,d=b.length;d>c;c++)b[c]=this._delete_ids(b[c]);return b}return delete b.id,b.li_attr&&b.li_attr.id&&delete b.li_attr.id,b.a_attr&&b.a_attr.id&&delete b.a_attr.id,b.children&&a.isArray(b.children)&&(b.children=this._delete_ids(b.children)),b},this.check=function(c,e,f,g,h){if(d.check.call(this,c,e,f,g,h)===!1)return!1;e=e&&e.id?e:this.get_node(e),f=f&&f.id?f:this.get_node(f);var i=e&&e.id?h&&h.origin?h.origin:a.jstree.reference(e.id):null,j,k,l,m;switch(i=i&&i._model&&i._model.data?i._model.data:null,c){case"create_node":case"move_node":case"copy_node":if("move_node"!==c||-1===a.inArray(e.id,f.children)){if(j=this.get_rules(f),j.max_children!==b&&-1!==j.max_children&&j.max_children===f.children.length)return this._data.core.last_error={error:"check",plugin:"types",id:"types_01",reason:"max_children prevents function: "+c,data:JSON.stringify({chk:c,pos:g,obj:e&&e.id?e.id:!1,par:f&&f.id?f.id:!1})},!1;if(j.valid_children!==b&&-1!==j.valid_children&&-1===a.inArray(e.type||"default",j.valid_children))return this._data.core.last_error={error:"check",plugin:"types",id:"types_02",reason:"valid_children prevents function: "+c,data:JSON.stringify({chk:c,pos:g,obj:e&&e.id?e.id:!1,par:f&&f.id?f.id:!1})},!1;if(i&&e.children_d&&e.parents){for(k=0,l=0,m=e.children_d.length;m>l;l++)k=Math.max(k,i[e.children_d[l]].parents.length);k=k-e.parents.length+1}(0>=k||k===b)&&(k=1);do{if(j.max_depth!==b&&-1!==j.max_depth&&j.max_depth<k)return this._data.core.last_error={error:"check",plugin:"types",id:"types_03",reason:"max_depth prevents function: "+c,data:JSON.stringify({chk:c,pos:g,obj:e&&e.id?e.id:!1,par:f&&f.id?f.id:!1})},!1;f=this.get_node(f.parent),j=this.get_rules(f),k++}while(f)}}return!0},this.get_rules=function(a){if(a=this.get_node(a),!a)return!1;var c=this.get_type(a,!0);return c.max_depth===b&&(c.max_depth=-1),c.max_children===b&&(c.max_children=-1),c.valid_children===b&&(c.valid_children=-1),c},this.get_type=function(b,c){return b=this.get_node(b),b?c?a.extend({type:b.type},this.settings.types[b.type]):b.type:!1},this.set_type=function(c,d){var e=this._model.data,f,g,h,i,j,k,l,m;if(a.isArray(c)){for(c=c.slice(),g=0,h=c.length;h>g;g++)this.set_type(c[g],d);return!0}if(f=this.settings.types,c=this.get_node(c),!f[d]||!c)return!1;if(l=this.get_node(c,!0),l&&l.length&&(m=l.children(".jstree-anchor")),i=c.type,j=this.get_icon(c),c.type=d,(j===!0||!f[i]||f[i].icon!==b&&j===f[i].icon)&&this.set_icon(c,f[d].icon!==b?f[d].icon:!0),f[i]&&f[i].li_attr!==b&&"object"==typeof f[i].li_attr)for(k in f[i].li_attr)if(f[i].li_attr.hasOwnProperty(k)){if("id"===k)continue;"class"===k?(e[c.id].li_attr["class"]=(e[c.id].li_attr["class"]||"").replace(f[i].li_attr[k],""),l&&l.removeClass(f[i].li_attr[k])):e[c.id].li_attr[k]===f[i].li_attr[k]&&(e[c.id].li_attr[k]=null,l&&l.removeAttr(k))}if(f[i]&&f[i].a_attr!==b&&"object"==typeof f[i].a_attr)for(k in f[i].a_attr)if(f[i].a_attr.hasOwnProperty(k)){if("id"===k)continue;"class"===k?(e[c.id].a_attr["class"]=(e[c.id].a_attr["class"]||"").replace(f[i].a_attr[k],""),m&&m.removeClass(f[i].a_attr[k])):e[c.id].a_attr[k]===f[i].a_attr[k]&&("href"===k?(e[c.id].a_attr[k]="#",m&&m.attr("href","#")):(delete e[c.id].a_attr[k],m&&m.removeAttr(k)))}if(f[d].li_attr!==b&&"object"==typeof f[d].li_attr)for(k in f[d].li_attr)if(f[d].li_attr.hasOwnProperty(k)){if("id"===k)continue;e[c.id].li_attr[k]===b?(e[c.id].li_attr[k]=f[d].li_attr[k],l&&("class"===k?l.addClass(f[d].li_attr[k]):l.attr(k,f[d].li_attr[k]))):"class"===k&&(e[c.id].li_attr["class"]=f[d].li_attr[k]+" "+e[c.id].li_attr["class"],l&&l.addClass(f[d].li_attr[k]))}if(f[d].a_attr!==b&&"object"==typeof f[d].a_attr)for(k in f[d].a_attr)if(f[d].a_attr.hasOwnProperty(k)){if("id"===k)continue;e[c.id].a_attr[k]===b?(e[c.id].a_attr[k]=f[d].a_attr[k],m&&("class"===k?m.addClass(f[d].a_attr[k]):m.attr(k,f[d].a_attr[k]))):"href"===k&&"#"===e[c.id].a_attr[k]?(e[c.id].a_attr.href=f[d].a_attr.href,m&&m.attr("href",f[d].a_attr.href)):"class"===k&&(e[c.id].a_attr["class"]=f[d].a_attr["class"]+" "+e[c.id].a_attr["class"],m&&m.addClass(f[d].a_attr[k]))}return!0}},a.jstree.defaults.unique={case_sensitive:!1,trim_whitespace:!1,duplicate:function(a,b){return a+" ("+b+")"}},a.jstree.plugins.unique=function(c,d){this.check=function(b,c,e,f,g){if(d.check.call(this,b,c,e,f,g)===!1)return!1;if(c=c&&c.id?c:this.get_node(c),e=e&&e.id?e:this.get_node(e),!e||!e.children)return!0;var h="rename_node"===b?f:c.text,i=[],j=this.settings.unique.case_sensitive,k=this.settings.unique.trim_whitespace,l=this._model.data,m,n,o;for(m=0,n=e.children.length;n>m;m++)o=l[e.children[m]].text,j||(o=o.toLowerCase()),k&&(o=o.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,"")),i.push(o);switch(j||(h=h.toLowerCase()),k&&(h=h.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,"")),b){case"delete_node":return!0;case"rename_node":return o=c.text||"",j||(o=o.toLowerCase()),k&&(o=o.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,"")),m=-1===a.inArray(h,i)||c.text&&o===h,m||(this._data.core.last_error={error:"check",plugin:"unique",id:"unique_01",reason:"Child with name "+h+" already exists. Preventing: "+b,data:JSON.stringify({chk:b,pos:f,obj:c&&c.id?c.id:!1,par:e&&e.id?e.id:!1})}),m;case"create_node":return m=-1===a.inArray(h,i),m||(this._data.core.last_error={error:"check",plugin:"unique",id:"unique_04",reason:"Child with name "+h+" already exists. Preventing: "+b,data:JSON.stringify({chk:b,pos:f,obj:c&&c.id?c.id:!1,par:e&&e.id?e.id:!1})}),m;case"copy_node":return m=-1===a.inArray(h,i),m||(this._data.core.last_error={error:"check",plugin:"unique",id:"unique_02",reason:"Child with name "+h+" already exists. Preventing: "+b,data:JSON.stringify({chk:b,pos:f,obj:c&&c.id?c.id:!1,par:e&&e.id?e.id:!1})}),m;case"move_node":return m=c.parent===e.id&&(!g||!g.is_multi)||-1===a.inArray(h,i),m||(this._data.core.last_error={error:"check",plugin:"unique",id:"unique_03",reason:"Child with name "+h+" already exists. Preventing: "+b,data:JSON.stringify({chk:b,pos:f,obj:c&&c.id?c.id:!1,par:e&&e.id?e.id:!1})}),m}return!0},this.create_node=function(c,e,f,g,h){if(!e||e.text===b){if(null===c&&(c=a.jstree.root),c=this.get_node(c),!c)return d.create_node.call(this,c,e,f,g,h);if(f=f===b?"last":f,!f.toString().match(/^(before|after)$/)&&!h&&!this.is_loaded(c))return d.create_node.call(this,c,e,f,g,h);e||(e={});var i,j,k,l,m,n=this._model.data,o=this.settings.unique.case_sensitive,p=this.settings.unique.trim_whitespace,q=this.settings.unique.duplicate,r;for(j=i=this.get_string("New node"),k=[],l=0,m=c.children.length;m>l;l++)r=n[c.children[l]].text,o||(r=r.toLowerCase()),p&&(r=r.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,"")),k.push(r);l=1,r=j,o||(r=r.toLowerCase()),p&&(r=r.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,""));while(-1!==a.inArray(r,k))j=q.call(this,i,++l).toString(),r=j,o||(r=r.toLowerCase()),p&&(r=r.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,""));e.text=j}return d.create_node.call(this,c,e,f,g,h)}};var n=i.createElement("DIV");if(n.setAttribute("unselectable","on"),n.setAttribute("role","presentation"),n.className="jstree-wholerow",n.innerHTML="&#160;",a.jstree.plugins.wholerow=function(b,c){this.bind=function(){c.bind.call(this),this.element.on("ready.jstree set_state.jstree",a.proxy(function(){this.hide_dots()},this)).on("init.jstree loading.jstree ready.jstree",a.proxy(function(){this.get_container_ul().addClass("jstree-wholerow-ul")},this)).on("deselect_all.jstree",a.proxy(function(a,b){this.element.find(".jstree-wholerow-clicked").removeClass("jstree-wholerow-clicked")},this)).on("changed.jstree",a.proxy(function(a,b){this.element.find(".jstree-wholerow-clicked").removeClass("jstree-wholerow-clicked");var c=!1,d,e;for(d=0,e=b.selected.length;e>d;d++)c=this.get_node(b.selected[d],!0),c&&c.length&&c.children(".jstree-wholerow").addClass("jstree-wholerow-clicked")},this)).on("open_node.jstree",a.proxy(function(a,b){this.get_node(b.node,!0).find(".jstree-clicked").parent().children(".jstree-wholerow").addClass("jstree-wholerow-clicked")},this)).on("hover_node.jstree dehover_node.jstree",a.proxy(function(a,b){"hover_node"===a.type&&this.is_disabled(b.node)||this.get_node(b.node,!0).children(".jstree-wholerow")["hover_node"===a.type?"addClass":"removeClass"]("jstree-wholerow-hovered")},this)).on("contextmenu.jstree",".jstree-wholerow",a.proxy(function(b){if(this._data.contextmenu){b.preventDefault();var c=a.Event("contextmenu",{metaKey:b.metaKey,ctrlKey:b.ctrlKey,altKey:b.altKey,shiftKey:b.shiftKey,pageX:b.pageX,pageY:b.pageY});a(b.currentTarget).closest(".jstree-node").children(".jstree-anchor").first().trigger(c)}},this)).on("click.jstree",".jstree-wholerow",function(b){b.stopImmediatePropagation();var c=a.Event("click",{metaKey:b.metaKey,ctrlKey:b.ctrlKey,altKey:b.altKey,shiftKey:b.shiftKey});a(b.currentTarget).closest(".jstree-node").children(".jstree-anchor").first().trigger(c).focus()}).on("dblclick.jstree",".jstree-wholerow",function(b){b.stopImmediatePropagation();var c=a.Event("dblclick",{metaKey:b.metaKey,ctrlKey:b.ctrlKey,altKey:b.altKey,shiftKey:b.shiftKey});a(b.currentTarget).closest(".jstree-node").children(".jstree-anchor").first().trigger(c).focus()}).on("click.jstree",".jstree-leaf > .jstree-ocl",a.proxy(function(b){b.stopImmediatePropagation();var c=a.Event("click",{metaKey:b.metaKey,ctrlKey:b.ctrlKey,altKey:b.altKey,shiftKey:b.shiftKey});a(b.currentTarget).closest(".jstree-node").children(".jstree-anchor").first().trigger(c).focus()},this)).on("mouseover.jstree",".jstree-wholerow, .jstree-icon",a.proxy(function(a){return a.stopImmediatePropagation(),this.is_disabled(a.currentTarget)||this.hover_node(a.currentTarget),!1},this)).on("mouseleave.jstree",".jstree-node",a.proxy(function(a){this.dehover_node(a.currentTarget)},this))},this.teardown=function(){this.settings.wholerow&&this.element.find(".jstree-wholerow").remove(),c.teardown.call(this)},this.redraw_node=function(b,d,e,f){if(b=c.redraw_node.apply(this,arguments)){var g=n.cloneNode(!0);-1!==a.inArray(b.id,this._data.core.selected)&&(g.className+=" jstree-wholerow-clicked"),this._data.core.focused&&this._data.core.focused===b.id&&(g.className+=" jstree-wholerow-hovered"),b.insertBefore(g,b.childNodes[0])}return b}},window.customElements&&Object&&Object.create){var o=Object.create(HTMLElement.prototype);o.createdCallback=function(){var b={core:{},plugins:[]},c;for(c in a.jstree.plugins)a.jstree.plugins.hasOwnProperty(c)&&this.attributes[c]&&(b.plugins.push(c),this.getAttribute(c)&&JSON.parse(this.getAttribute(c))&&(b[c]=JSON.parse(this.getAttribute(c))));for(c in a.jstree.defaults.core)a.jstree.defaults.core.hasOwnProperty(c)&&this.attributes[c]&&(b.core[c]=JSON.parse(this.getAttribute(c))||this.getAttribute(c));a(this).jstree(b)};try{window.customElements.define("vakata-jstree",function(){},{prototype:o})}catch(p){}}}});

/***/ }),
/* 6 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_prop_types__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_reactstrap__ = __webpack_require__(7);
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */



class MenuItemEditFormModule extends __WEBPACK_IMPORTED_MODULE_0_react__["Component"] {
  constructor(props) {
    super(props);
    this.defaultLanguage = eZ.adminUiConfig.languages.priority[0];
    this.state = {
      name: this.props.item.name,
      url: this.props.item.url,
      blank: this.props.item.target === '_blank',
      language: this.defaultLanguage
    };
    this.handleLanguageChange = this.handleLanguageChange.bind(this);
    this.handleInputChange = this.handleInputChange.bind(this);
    this.handleLocalizedInputChange = this.handleLocalizedInputChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleCancel = this.handleCancel.bind(this);
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevProps.item !== this.props.item) {
      this.setState(state => ({
        name: this.props.item.name,
        url: this.props.item.url,
        blank: this.props.item.target === '_blank'
      }));
    }
  }

  handleLanguageChange(event) {
    const target = event.target;
    const value = target.value;
    this.setState({
      language: value
    });
  }

  handleInputChange(event) {
    const target = event.target;
    const value = target.type === 'checkbox' ? target.checked : target.value;
    const name = target.name;
    this.setState({
      [name]: value
    });
  }

  handleLocalizedInputChange(event) {
    const target = event.target;
    const rawValue = target.type === 'checkbox' ? target.checked : target.value;
    const name = target.name;
    let value = {};

    try {
      value = JSON.parse(this.state[name]);
    } catch (e) {
      value[this.defaultLanguage] = this.state[name];
    }

    value[this.state.language] = rawValue;
    const stringValue = JSON.stringify(value);
    this.setState({
      [name]: stringValue
    });
  }

  handleSubmit(event) {
    event.preventDefault();
    const item = this.props.item;
    item.name = this.state.name;
    item.url = this.state.url;
    item.target = this.state.blank ? '_blank' : '';
    this.props.onSubmit(item);
  }

  handleCancel(event) {
    this.props.onCancel();
  }

  getInputValue(property) {
    try {
      const values = JSON.parse(property);
      return values[this.state.language] || '';
    } catch (e) {
      return property;
    }
  }

  render() {
    const languages = [];

    for (const languageCode in eZ.adminUiConfig.languages.mappings) {
      languages.push(eZ.adminUiConfig.languages.mappings[languageCode]);
    }

    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", null, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["b" /* Form */], {
      onSubmit: this.handleSubmit
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["c" /* FormGroup */], null, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["e" /* Label */], {
      for: "language"
    }, Translator.trans('menu_item.property.language')), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["d" /* Input */], {
      type: "select",
      onChange: this.handleLanguageChange
    }, [...languages].map(language => __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("option", {
      key: language.id,
      value: language.languageCode
    }, language.name)))), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["c" /* FormGroup */], null, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["e" /* Label */], {
      for: "item_name"
    }, Translator.trans('menu_item.property.name'), " (", this.state.language, ")"), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["d" /* Input */], {
      type: "text",
      name: "name",
      value: this.getInputValue(this.state.name),
      id: "item_name",
      onChange: this.handleLocalizedInputChange
    })), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["c" /* FormGroup */], null, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["e" /* Label */], {
      for: "item_url"
    }, Translator.trans('menu_item.property.url'), " (", this.state.language, ")"), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["d" /* Input */], {
      type: "text",
      name: "url",
      value: this.getInputValue(this.state.url),
      id: "item_url",
      onChange: this.handleLocalizedInputChange
    })), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["c" /* FormGroup */], {
      check: true
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["e" /* Label */], {
      check: true,
      for: "item_target"
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["d" /* Input */], {
      type: "checkbox",
      name: "blank",
      checked: this.state.blank,
      id: "item_target",
      onChange: this.handleInputChange
    }), Translator.trans('menu_item.property.new_window'))), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["a" /* Button */], {
      type: "submit",
      className: "pull-right",
      color: "primary"
    }, Translator.trans('menu_item.edit_form.save')), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2_reactstrap__["a" /* Button */], {
      type: "button",
      onClick: this.handleCancel
    }, Translator.trans('menu_item.edit_form.cancel'))));
  }

}
/* harmony export (immutable) */ __webpack_exports__["a"] = MenuItemEditFormModule;

MenuItemEditFormModule.propTypes = {
  item: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  onSubmit: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  onCancel: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func
};

/***/ }),
/* 7 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(process) {/* unused harmony export Alert */
/* unused harmony export Container */
/* unused harmony export Row */
/* unused harmony export Col */
/* unused harmony export Navbar */
/* unused harmony export NavbarBrand */
/* unused harmony export NavbarToggler */
/* unused harmony export Nav */
/* unused harmony export NavItem */
/* unused harmony export NavDropdown */
/* unused harmony export NavLink */
/* unused harmony export Breadcrumb */
/* unused harmony export BreadcrumbItem */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Button; });
/* unused harmony export ButtonDropdown */
/* unused harmony export ButtonGroup */
/* unused harmony export ButtonToolbar */
/* unused harmony export Dropdown */
/* unused harmony export DropdownItem */
/* unused harmony export DropdownMenu */
/* unused harmony export DropdownToggle */
/* unused harmony export Fade */
/* unused harmony export Badge */
/* unused harmony export Card */
/* unused harmony export CardLink */
/* unused harmony export CardGroup */
/* unused harmony export CardDeck */
/* unused harmony export CardColumns */
/* unused harmony export CardBody */
/* unused harmony export CardBlock */
/* unused harmony export CardFooter */
/* unused harmony export CardHeader */
/* unused harmony export CardImg */
/* unused harmony export CardImgOverlay */
/* unused harmony export Carousel */
/* unused harmony export UncontrolledCarousel */
/* unused harmony export CarouselControl */
/* unused harmony export CarouselItem */
/* unused harmony export CarouselIndicators */
/* unused harmony export CarouselCaption */
/* unused harmony export CardSubtitle */
/* unused harmony export CardText */
/* unused harmony export CardTitle */
/* unused harmony export Popover */
/* unused harmony export PopoverContent */
/* unused harmony export PopoverBody */
/* unused harmony export PopoverTitle */
/* unused harmony export PopoverHeader */
/* unused harmony export Progress */
/* unused harmony export Modal */
/* unused harmony export ModalHeader */
/* unused harmony export ModalBody */
/* unused harmony export ModalFooter */
/* unused harmony export PopperContent */
/* unused harmony export PopperTargetHelper */
/* unused harmony export Tooltip */
/* unused harmony export Table */
/* unused harmony export ListGroup */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return Form; });
/* unused harmony export FormFeedback */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return FormGroup; });
/* unused harmony export FormText */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return Input; });
/* unused harmony export InputGroup */
/* unused harmony export InputGroupAddon */
/* unused harmony export InputGroupButton */
/* unused harmony export InputGroupButtonDropdown */
/* unused harmony export InputGroupText */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return Label; });
/* unused harmony export CustomInput */
/* unused harmony export Media */
/* unused harmony export Pagination */
/* unused harmony export PaginationItem */
/* unused harmony export PaginationLink */
/* unused harmony export TabContent */
/* unused harmony export TabPane */
/* unused harmony export Jumbotron */
/* unused harmony export Collapse */
/* unused harmony export ListGroupItem */
/* unused harmony export ListGroupItemText */
/* unused harmony export ListGroupItemHeading */
/* unused harmony export UncontrolledAlert */
/* unused harmony export UncontrolledButtonDropdown */
/* unused harmony export UncontrolledCollapse */
/* unused harmony export UncontrolledDropdown */
/* unused harmony export UncontrolledNavDropdown */
/* unused harmony export UncontrolledTooltip */
/* unused harmony export Util */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_prop_types__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_classnames__ = __webpack_require__(23);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_classnames___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_classnames__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_lodash_isfunction__ = __webpack_require__(24);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_lodash_isfunction___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_lodash_isfunction__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_lodash_isobject__ = __webpack_require__(25);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_lodash_isobject___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_lodash_isobject__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_react_dom__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_react_dom___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_5_react_dom__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6_react_popper__ = __webpack_require__(26);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7_lodash_tonumber__ = __webpack_require__(32);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7_lodash_tonumber___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_7_lodash_tonumber__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8_react_lifecycles_compat__ = __webpack_require__(33);










var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) {
  return typeof obj;
} : function (obj) {
  return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
};











var classCallCheck = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

var createClass = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();





var defineProperty = function (obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
};

var _extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];

    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }

  return target;
};



var inherits = function (subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function, not " + typeof superClass);
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      enumerable: false,
      writable: true,
      configurable: true
    }
  });
  if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass;
};









var objectWithoutProperties = function (obj, keys) {
  var target = {};

  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }

  return target;
};

var possibleConstructorReturn = function (self, call) {
  if (!self) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return call && (typeof call === "object" || typeof call === "function") ? call : self;
};

// https://github.com/twbs/bootstrap/blob/v4.0.0-alpha.4/js/src/modal.js#L436-L443
function getScrollbarWidth() {
  var scrollDiv = document.createElement('div');
  // .modal-scrollbar-measure styles // https://github.com/twbs/bootstrap/blob/v4.0.0-alpha.4/scss/_modal.scss#L106-L113
  scrollDiv.style.position = 'absolute';
  scrollDiv.style.top = '-9999px';
  scrollDiv.style.width = '50px';
  scrollDiv.style.height = '50px';
  scrollDiv.style.overflow = 'scroll';
  document.body.appendChild(scrollDiv);
  var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
  document.body.removeChild(scrollDiv);
  return scrollbarWidth;
}

function setScrollbarWidth(padding) {
  document.body.style.paddingRight = padding > 0 ? padding + 'px' : null;
}

function isBodyOverflowing() {
  return document.body.clientWidth < window.innerWidth;
}

function getOriginalBodyPadding() {
  var style = window.getComputedStyle(document.body, null);

  return parseInt(style && style.getPropertyValue('padding-right') || 0, 10);
}

function conditionallyUpdateScrollbar() {
  var scrollbarWidth = getScrollbarWidth();
  // https://github.com/twbs/bootstrap/blob/v4.0.0-alpha.6/js/src/modal.js#L433
  var fixedContent = document.querySelectorAll('.fixed-top, .fixed-bottom, .is-fixed, .sticky-top')[0];
  var bodyPadding = fixedContent ? parseInt(fixedContent.style.paddingRight || 0, 10) : 0;

  if (isBodyOverflowing()) {
    setScrollbarWidth(bodyPadding + scrollbarWidth);
  }
}

var globalCssModule = void 0;

function setGlobalCssModule(cssModule) {
  globalCssModule = cssModule;
}

function mapToCssModules() {
  var className = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var cssModule = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : globalCssModule;

  if (!cssModule) return className;
  return className.split(' ').map(function (c) {
    return cssModule[c] || c;
  }).join(' ');
}

/**
 * Returns a new object with the key/value pairs from `obj` that are not in the array `omitKeys`.
 */
function omit(obj, omitKeys) {
  var result = {};
  Object.keys(obj).forEach(function (key) {
    if (omitKeys.indexOf(key) === -1) {
      result[key] = obj[key];
    }
  });
  return result;
}

/**
 * Returns a filtered copy of an object with only the specified keys.
 */
function pick(obj, keys) {
  var pickKeys = Array.isArray(keys) ? keys : [keys];
  var length = pickKeys.length;
  var key = void 0;
  var result = {};

  while (length > 0) {
    length -= 1;
    key = pickKeys[length];
    result[key] = obj[key];
  }
  return result;
}

var warned = {};

function warnOnce(message) {
  if (!warned[message]) {
    /* istanbul ignore else */
    if (typeof console !== 'undefined') {
      console.error(message); // eslint-disable-line no-console
    }
    warned[message] = true;
  }
}

function deprecated(propType, explanation) {
  return function validate(props, propName, componentName) {
    if (props[propName] !== null && typeof props[propName] !== 'undefined') {
      warnOnce('"' + propName + '" property of "' + componentName + '" has been deprecated.\n' + explanation);
    }

    for (var _len = arguments.length, rest = Array(_len > 3 ? _len - 3 : 0), _key = 3; _key < _len; _key++) {
      rest[_key - 3] = arguments[_key];
    }

    return propType.apply(undefined, [props, propName, componentName].concat(rest));
  };
}

function DOMElement(props, propName, componentName) {
  if (!(props[propName] instanceof Element)) {
    return new Error('Invalid prop `' + propName + '` supplied to `' + componentName + '`. Expected prop to be an instance of Element. Validation failed.');
  }
}

var targetPropType = __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, DOMElement, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.shape({ current: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.any })]);

/* eslint key-spacing: ["error", { afterColon: true, align: "value" }] */
// These are all setup to match what is in the bootstrap _variables.scss
// https://github.com/twbs/bootstrap/blob/v4-dev/scss/_variables.scss
var TransitionTimeouts = {
  Fade: 150, // $transition-fade
  Collapse: 350, // $transition-collapse
  Modal: 300, // $modal-transition
  Carousel: 600 // $carousel-transition
};

// Duplicated Transition.propType keys to ensure that Reactstrap builds
// for distribution properly exclude these keys for nested child HTML attributes
// since `react-transition-group` removes propTypes in production builds.
var TransitionPropTypeKeys = ['in', 'mountOnEnter', 'unmountOnExit', 'appear', 'enter', 'exit', 'timeout', 'onEnter', 'onEntering', 'onEntered', 'onExit', 'onExiting', 'onExited'];

var TransitionStatuses = {
  ENTERING: 'entering',
  ENTERED: 'entered',
  EXITING: 'exiting',
  EXITED: 'exited'
};

var keyCodes = {
  esc: 27,
  space: 32,
  enter: 13,
  tab: 9,
  up: 38,
  down: 40
};

var PopperPlacements = ['auto-start', 'auto', 'auto-end', 'top-start', 'top', 'top-end', 'right-start', 'right', 'right-end', 'bottom-end', 'bottom', 'bottom-start', 'left-end', 'left', 'left-start'];

var canUseDOM = !!(typeof window !== 'undefined' && window.document && window.document.createElement);

function isReactRefObj(target) {
  if (target && (typeof target === 'undefined' ? 'undefined' : _typeof(target)) === 'object') {
    return 'current' in target;
  }
  return false;
}

function findDOMElements(target) {
  if (isReactRefObj(target)) {
    return target.current;
  }
  if (__WEBPACK_IMPORTED_MODULE_3_lodash_isfunction___default()(target)) {
    return target();
  }
  if (typeof target === 'string' && canUseDOM) {
    var selection = document.querySelectorAll(target);
    if (!selection.length) {
      selection = document.querySelectorAll('#' + target);
    }
    if (!selection.length) {
      throw new Error('The target \'' + target + '\' could not be identified in the dom, tip: check spelling');
    }
    return selection;
  }
  return target;
}

function isArrayOrNodeList(els) {
  if (els === null) {
    return false;
  }
  return Array.isArray(els) || canUseDOM && typeof els.length === 'number';
}

function getTarget(target) {
  var els = findDOMElements(target);
  if (isArrayOrNodeList(els)) {
    return els[0];
  }
  return els;
}

var defaultToggleEvents = ['touchstart', 'click'];

function addMultipleEventListeners(_els, handler, _events) {
  var els = _els;
  if (!isArrayOrNodeList(els)) {
    els = [els];
  }

  var events = _events;
  if (typeof events === 'string') {
    events = events.split(/\s+/);
  }

  if (!isArrayOrNodeList(els) || typeof handler !== 'function' || !Array.isArray(events)) {
    throw new Error('\n      The first argument of this function must be DOM node or an array on DOM nodes or NodeList.\n      The second must be a function.\n      The third is a string or an array of strings that represents DOM events\n    ');
  }
  events.forEach(function (event) {
    els.forEach(function (el) {
      el.addEventListener(event, handler);
    });
  });
  return function removeEvents() {
    events.forEach(function (event) {
      els.forEach(function (el) {
        el.removeEventListener(event, handler);
      });
    });
  };
}

var focusableElements = ['a[href]', 'area[href]', 'input:not([disabled]):not([type=hidden])', 'select:not([disabled])', 'textarea:not([disabled])', 'button:not([disabled])', 'object', 'embed', '[tabindex]:not(.modal)', 'audio[controls]', 'video[controls]', '[contenteditable]:not([contenteditable="false"])'];



var utils = Object.freeze({
	getScrollbarWidth: getScrollbarWidth,
	setScrollbarWidth: setScrollbarWidth,
	isBodyOverflowing: isBodyOverflowing,
	getOriginalBodyPadding: getOriginalBodyPadding,
	conditionallyUpdateScrollbar: conditionallyUpdateScrollbar,
	setGlobalCssModule: setGlobalCssModule,
	mapToCssModules: mapToCssModules,
	omit: omit,
	pick: pick,
	warnOnce: warnOnce,
	deprecated: deprecated,
	DOMElement: DOMElement,
	targetPropType: targetPropType,
	TransitionTimeouts: TransitionTimeouts,
	TransitionPropTypeKeys: TransitionPropTypeKeys,
	TransitionStatuses: TransitionStatuses,
	keyCodes: keyCodes,
	PopperPlacements: PopperPlacements,
	canUseDOM: canUseDOM,
	isReactRefObj: isReactRefObj,
	findDOMElements: findDOMElements,
	isArrayOrNodeList: isArrayOrNodeList,
	getTarget: getTarget,
	defaultToggleEvents: defaultToggleEvents,
	addMultipleEventListeners: addMultipleEventListeners,
	focusableElements: focusableElements
});

var propTypes = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  fluid: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps = {
  tag: 'div'
};

var Container = function Container(props) {
  var className = props.className,
      cssModule = props.cssModule,
      fluid = props.fluid,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'fluid', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, fluid ? 'container-fluid' : 'container'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

Container.propTypes = propTypes;
Container.defaultProps = defaultProps;

var propTypes$1 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  noGutters: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  form: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool
};

var defaultProps$1 = {
  tag: 'div'
};

var Row = function Row(props) {
  var className = props.className,
      cssModule = props.cssModule,
      noGutters = props.noGutters,
      Tag = props.tag,
      form = props.form,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'noGutters', 'tag', 'form']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, noGutters ? 'no-gutters' : null, form ? 'form-row' : 'row'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

Row.propTypes = propTypes$1;
Row.defaultProps = defaultProps$1;

var colWidths = ['xs', 'sm', 'md', 'lg', 'xl'];
var stringOrNumberProp = __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]);

var columnProps = __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.shape({
  size: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  push: deprecated(stringOrNumberProp, 'Please use the prop "order"'),
  pull: deprecated(stringOrNumberProp, 'Please use the prop "order"'),
  order: stringOrNumberProp,
  offset: stringOrNumberProp
})]);

var propTypes$2 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  xs: columnProps,
  sm: columnProps,
  md: columnProps,
  lg: columnProps,
  xl: columnProps,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  widths: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.array
};

var defaultProps$2 = {
  tag: 'div',
  widths: colWidths
};

var getColumnSizeClass = function getColumnSizeClass(isXs, colWidth, colSize) {
  if (colSize === true || colSize === '') {
    return isXs ? 'col' : 'col-' + colWidth;
  } else if (colSize === 'auto') {
    return isXs ? 'col-auto' : 'col-' + colWidth + '-auto';
  }

  return isXs ? 'col-' + colSize : 'col-' + colWidth + '-' + colSize;
};

var Col = function Col(props) {
  var className = props.className,
      cssModule = props.cssModule,
      widths = props.widths,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'widths', 'tag']);

  var colClasses = [];

  widths.forEach(function (colWidth, i) {
    var columnProp = props[colWidth];

    delete attributes[colWidth];

    if (!columnProp && columnProp !== '') {
      return;
    }

    var isXs = !i;

    if (__WEBPACK_IMPORTED_MODULE_4_lodash_isobject___default()(columnProp)) {
      var _classNames;

      var colSizeInterfix = isXs ? '-' : '-' + colWidth + '-';
      var colClass = getColumnSizeClass(isXs, colWidth, columnProp.size);

      colClasses.push(mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()((_classNames = {}, defineProperty(_classNames, colClass, columnProp.size || columnProp.size === ''), defineProperty(_classNames, 'order' + colSizeInterfix + columnProp.order, columnProp.order || columnProp.order === 0), defineProperty(_classNames, 'offset' + colSizeInterfix + columnProp.offset, columnProp.offset || columnProp.offset === 0), _classNames)), cssModule));
    } else {
      var _colClass = getColumnSizeClass(isXs, colWidth, columnProp);
      colClasses.push(_colClass);
    }
  });

  if (!colClasses.length) {
    colClasses.push('col');
  }

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, colClasses), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

Col.propTypes = propTypes$2;
Col.defaultProps = defaultProps$2;

var propTypes$3 = {
  light: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  dark: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  inverse: deprecated(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, 'Please use the prop "dark"'),
  full: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  fixed: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  sticky: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  color: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  role: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  toggleable: deprecated(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]), 'Please use the prop "expand"'),
  expand: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string])
};

var defaultProps$3 = {
  tag: 'nav',
  expand: false
};

var getExpandClass = function getExpandClass(expand) {
  if (expand === false) {
    return false;
  } else if (expand === true || expand === 'xs') {
    return 'navbar-expand';
  }

  return 'navbar-expand-' + expand;
};

// To better maintain backwards compatibility while toggleable is deprecated.
// We must map breakpoints to the next breakpoint so that toggleable and expand do the same things at the same breakpoint.
var toggleableToExpand = {
  xs: 'sm',
  sm: 'md',
  md: 'lg',
  lg: 'xl'
};

var getToggleableClass = function getToggleableClass(toggleable) {
  if (toggleable === undefined || toggleable === 'xl') {
    return false;
  } else if (toggleable === false) {
    return 'navbar-expand';
  }

  return 'navbar-expand-' + (toggleable === true ? 'sm' : toggleableToExpand[toggleable] || toggleable);
};

var Navbar = function Navbar(props) {
  var _classNames;

  var toggleable = props.toggleable,
      expand = props.expand,
      className = props.className,
      cssModule = props.cssModule,
      light = props.light,
      dark = props.dark,
      inverse = props.inverse,
      fixed = props.fixed,
      sticky = props.sticky,
      color = props.color,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['toggleable', 'expand', 'className', 'cssModule', 'light', 'dark', 'inverse', 'fixed', 'sticky', 'color', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'navbar', getExpandClass(expand) || getToggleableClass(toggleable), (_classNames = {
    'navbar-light': light,
    'navbar-dark': inverse || dark
  }, defineProperty(_classNames, 'bg-' + color, color), defineProperty(_classNames, 'fixed-' + fixed, fixed), defineProperty(_classNames, 'sticky-' + sticky, sticky), _classNames)), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

Navbar.propTypes = propTypes$3;
Navbar.defaultProps = defaultProps$3;

var propTypes$4 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$4 = {
  tag: 'a'
};

var NavbarBrand = function NavbarBrand(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'navbar-brand'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

NavbarBrand.propTypes = propTypes$4;
NavbarBrand.defaultProps = defaultProps$4;

var propTypes$5 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  type: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node
};

var defaultProps$5 = {
  tag: 'button',
  type: 'button'
};

var NavbarToggler = function NavbarToggler(props) {
  var className = props.className,
      cssModule = props.cssModule,
      children = props.children,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'children', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'navbar-toggler'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    Tag,
    _extends({}, attributes, { className: classes }),
    children || __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('span', { className: mapToCssModules('navbar-toggler-icon', cssModule) })
  );
};

NavbarToggler.propTypes = propTypes$5;
NavbarToggler.defaultProps = defaultProps$5;

var propTypes$6 = {
  tabs: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  pills: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  vertical: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  horizontal: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  justified: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  fill: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  navbar: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  card: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$6 = {
  tag: 'ul',
  vertical: false
};

var getVerticalClass = function getVerticalClass(vertical) {
  if (vertical === false) {
    return false;
  } else if (vertical === true || vertical === 'xs') {
    return 'flex-column';
  }

  return 'flex-' + vertical + '-column';
};

var Nav = function Nav(props) {
  var className = props.className,
      cssModule = props.cssModule,
      tabs = props.tabs,
      pills = props.pills,
      vertical = props.vertical,
      horizontal = props.horizontal,
      justified = props.justified,
      fill = props.fill,
      navbar = props.navbar,
      card = props.card,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tabs', 'pills', 'vertical', 'horizontal', 'justified', 'fill', 'navbar', 'card', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, navbar ? 'navbar-nav' : 'nav', horizontal ? 'justify-content-' + horizontal : false, getVerticalClass(vertical), {
    'nav-tabs': tabs,
    'card-header-tabs': card && tabs,
    'nav-pills': pills,
    'card-header-pills': card && pills,
    'nav-justified': justified,
    'nav-fill': fill
  }), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

Nav.propTypes = propTypes$6;
Nav.defaultProps = defaultProps$6;

var propTypes$7 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  active: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$7 = {
  tag: 'li'
};

var NavItem = function NavItem(props) {
  var className = props.className,
      cssModule = props.cssModule,
      active = props.active,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'active', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'nav-item', active ? 'active' : false), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

NavItem.propTypes = propTypes$7;
NavItem.defaultProps = defaultProps$7;

/* eslint react/no-find-dom-node: 0 */
// https://github.com/yannickcr/eslint-plugin-react/blob/master/docs/rules/no-find-dom-node.md

var propTypes$8 = {
  disabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  dropup: deprecated(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, 'Please use the prop "direction" with the value "up".'),
  direction: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['up', 'down', 'left', 'right']),
  group: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  isOpen: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  nav: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  active: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  addonType: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['prepend', 'append'])]),
  size: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  toggle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  inNavbar: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  setActiveFromChild: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool
};

var defaultProps$8 = {
  isOpen: false,
  direction: 'down',
  nav: false,
  active: false,
  addonType: false,
  inNavbar: false,
  setActiveFromChild: false
};

var childContextTypes = {
  toggle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func.isRequired,
  isOpen: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool.isRequired,
  direction: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['up', 'down', 'left', 'right']).isRequired,
  inNavbar: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool.isRequired
};

var Dropdown = function (_React$Component) {
  inherits(Dropdown, _React$Component);

  function Dropdown(props) {
    classCallCheck(this, Dropdown);

    var _this = possibleConstructorReturn(this, (Dropdown.__proto__ || Object.getPrototypeOf(Dropdown)).call(this, props));

    _this.addEvents = _this.addEvents.bind(_this);
    _this.handleDocumentClick = _this.handleDocumentClick.bind(_this);
    _this.handleKeyDown = _this.handleKeyDown.bind(_this);
    _this.removeEvents = _this.removeEvents.bind(_this);
    _this.toggle = _this.toggle.bind(_this);
    return _this;
  }

  createClass(Dropdown, [{
    key: 'getChildContext',
    value: function getChildContext() {
      return {
        toggle: this.props.toggle,
        isOpen: this.props.isOpen,
        direction: this.props.direction === 'down' && this.props.dropup ? 'up' : this.props.direction,
        inNavbar: this.props.inNavbar
      };
    }
  }, {
    key: 'componentDidMount',
    value: function componentDidMount() {
      this.handleProps();
    }
  }, {
    key: 'componentDidUpdate',
    value: function componentDidUpdate(prevProps) {
      if (this.props.isOpen !== prevProps.isOpen) {
        this.handleProps();
      }
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      this.removeEvents();
    }
  }, {
    key: 'getContainer',
    value: function getContainer() {
      return __WEBPACK_IMPORTED_MODULE_5_react_dom___default.a.findDOMNode(this);
    }
  }, {
    key: 'addEvents',
    value: function addEvents() {
      var _this2 = this;

      ['click', 'touchstart', 'keyup'].forEach(function (event) {
        return document.addEventListener(event, _this2.handleDocumentClick, true);
      });
    }
  }, {
    key: 'removeEvents',
    value: function removeEvents() {
      var _this3 = this;

      ['click', 'touchstart', 'keyup'].forEach(function (event) {
        return document.removeEventListener(event, _this3.handleDocumentClick, true);
      });
    }
  }, {
    key: 'handleDocumentClick',
    value: function handleDocumentClick(e) {
      if (e && (e.which === 3 || e.type === 'keyup' && e.which !== keyCodes.tab)) return;
      var container = this.getContainer();

      if (container.contains(e.target) && container !== e.target && (e.type !== 'keyup' || e.which === keyCodes.tab)) {
        return;
      }

      this.toggle(e);
    }
  }, {
    key: 'handleKeyDown',
    value: function handleKeyDown(e) {
      if (keyCodes.tab === e.which || /button/i.test(e.target.tagName) && e.which === keyCodes.space || /input|textarea/i.test(e.target.tagName)) {
        return;
      }

      e.preventDefault();
      if (this.props.disabled) return;

      var container = this.getContainer();

      if (e.which === keyCodes.space && keyCodes.enter && this.props.isOpen && container !== e.target) {
        e.target.click();
      }

      if (e.which === keyCodes.esc || !this.props.isOpen) {
        this.toggle(e);
        container.querySelector('[aria-expanded]').focus();
        return;
      }

      var menuClass = mapToCssModules('dropdown-menu', this.props.cssModule);
      var itemClass = mapToCssModules('dropdown-item', this.props.cssModule);
      var disabledClass = mapToCssModules('disabled', this.props.cssModule);

      var items = container.querySelectorAll('.' + menuClass + ' .' + itemClass + ':not(.' + disabledClass + ')');
      if (!items.length) return;

      var index = -1;

      var charPressed = String.fromCharCode(e.which).toLowerCase();

      for (var i = 0; i < items.length; i += 1) {
        var firstLetter = items[i].textContent && items[i].textContent[0].toLowerCase();
        if (firstLetter === charPressed || items[i] === e.target) {
          index = i;
          break;
        }
      }

      if (e.which === keyCodes.up && index > 0) {
        index -= 1;
      }

      if (e.which === keyCodes.down && index < items.length - 1) {
        index += 1;
      }

      if (index < 0) {
        index = 0;
      }

      items[index].focus();
    }
  }, {
    key: 'handleProps',
    value: function handleProps() {
      if (this.props.isOpen) {
        this.addEvents();
      } else {
        this.removeEvents();
      }
    }
  }, {
    key: 'toggle',
    value: function toggle(e) {
      if (this.props.disabled) {
        return e && e.preventDefault();
      }

      return this.props.toggle(e);
    }
  }, {
    key: 'render',
    value: function render() {
      var _classNames;

      var _omit = omit(this.props, ['toggle', 'disabled', 'inNavbar', 'direction']),
          className = _omit.className,
          cssModule = _omit.cssModule,
          dropup = _omit.dropup,
          isOpen = _omit.isOpen,
          group = _omit.group,
          size = _omit.size,
          nav = _omit.nav,
          setActiveFromChild = _omit.setActiveFromChild,
          active = _omit.active,
          addonType = _omit.addonType,
          attrs = objectWithoutProperties(_omit, ['className', 'cssModule', 'dropup', 'isOpen', 'group', 'size', 'nav', 'setActiveFromChild', 'active', 'addonType']);

      var direction = this.props.direction === 'down' && dropup ? 'up' : this.props.direction;

      attrs.tag = attrs.tag || (nav ? 'li' : 'div');

      var subItemIsActive = false;
      if (setActiveFromChild) {
        __WEBPACK_IMPORTED_MODULE_0_react___default.a.Children.map(this.props.children[1].props.children, function (dropdownItem) {
          if (dropdownItem.props.active) subItemIsActive = true;
        });
      }

      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, direction !== 'down' && 'drop' + direction, nav && active ? 'active' : false, setActiveFromChild && subItemIsActive ? 'active' : false, (_classNames = {}, defineProperty(_classNames, 'input-group-' + addonType, addonType), defineProperty(_classNames, 'btn-group', group), defineProperty(_classNames, 'btn-group-' + size, !!size), defineProperty(_classNames, 'dropdown', !group && !addonType), defineProperty(_classNames, 'show', isOpen), defineProperty(_classNames, 'nav-item', nav), _classNames)), cssModule);

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_6_react_popper__["b" /* Manager */], _extends({}, attrs, { className: classes, onKeyDown: this.handleKeyDown }));
    }
  }]);
  return Dropdown;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

Dropdown.propTypes = propTypes$8;
Dropdown.defaultProps = defaultProps$8;
Dropdown.childContextTypes = childContextTypes;

function NavDropdown(props) {
  warnOnce('The "NavDropdown" component has been deprecated.\nPlease use component "Dropdown" with nav prop.');
  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Dropdown, _extends({ nav: true }, props));
}

var propTypes$9 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  disabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  active: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  onClick: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  href: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.any
};

var defaultProps$9 = {
  tag: 'a'
};

var NavLink = function (_React$Component) {
  inherits(NavLink, _React$Component);

  function NavLink(props) {
    classCallCheck(this, NavLink);

    var _this = possibleConstructorReturn(this, (NavLink.__proto__ || Object.getPrototypeOf(NavLink)).call(this, props));

    _this.onClick = _this.onClick.bind(_this);
    return _this;
  }

  createClass(NavLink, [{
    key: 'onClick',
    value: function onClick(e) {
      if (this.props.disabled) {
        e.preventDefault();
        return;
      }

      if (this.props.href === '#') {
        e.preventDefault();
      }

      if (this.props.onClick) {
        this.props.onClick(e);
      }
    }
  }, {
    key: 'render',
    value: function render() {
      var _props = this.props,
          className = _props.className,
          cssModule = _props.cssModule,
          active = _props.active,
          Tag = _props.tag,
          innerRef = _props.innerRef,
          attributes = objectWithoutProperties(_props, ['className', 'cssModule', 'active', 'tag', 'innerRef']);


      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'nav-link', {
        disabled: attributes.disabled,
        active: active
      }), cssModule);

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { ref: innerRef, onClick: this.onClick, className: classes }));
    }
  }]);
  return NavLink;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

NavLink.propTypes = propTypes$9;
NavLink.defaultProps = defaultProps$9;

var propTypes$10 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  listTag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  listClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  'aria-label': __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

var defaultProps$10 = {
  tag: 'nav',
  listTag: 'ol',
  'aria-label': 'breadcrumb'
};

var Breadcrumb = function Breadcrumb(props) {
  var className = props.className,
      listClassName = props.listClassName,
      cssModule = props.cssModule,
      children = props.children,
      Tag = props.tag,
      ListTag = props.listTag,
      label = props['aria-label'],
      attributes = objectWithoutProperties(props, ['className', 'listClassName', 'cssModule', 'children', 'tag', 'listTag', 'aria-label']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className), cssModule);

  var listClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('breadcrumb', listClassName), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    Tag,
    _extends({}, attributes, { className: classes, 'aria-label': label }),
    __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      ListTag,
      { className: listClasses },
      children
    )
  );
};

Breadcrumb.propTypes = propTypes$10;
Breadcrumb.defaultProps = defaultProps$10;

var propTypes$11 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  active: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$11 = {
  tag: 'li'
};

var BreadcrumbItem = function BreadcrumbItem(props) {
  var className = props.className,
      cssModule = props.cssModule,
      active = props.active,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'active', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, active ? 'active' : false, 'breadcrumb-item'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes, 'aria-current': active ? 'page' : undefined }));
};

BreadcrumbItem.propTypes = propTypes$11;
BreadcrumbItem.defaultProps = defaultProps$11;

var propTypes$12 = {
  active: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  'aria-label': __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  block: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  color: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  disabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  outline: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  onClick: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  size: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  close: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool
};

var defaultProps$12 = {
  color: 'secondary',
  tag: 'button'
};

var Button = function (_React$Component) {
  inherits(Button, _React$Component);

  function Button(props) {
    classCallCheck(this, Button);

    var _this = possibleConstructorReturn(this, (Button.__proto__ || Object.getPrototypeOf(Button)).call(this, props));

    _this.onClick = _this.onClick.bind(_this);
    return _this;
  }

  createClass(Button, [{
    key: 'onClick',
    value: function onClick(e) {
      if (this.props.disabled) {
        e.preventDefault();
        return;
      }

      if (this.props.onClick) {
        this.props.onClick(e);
      }
    }
  }, {
    key: 'render',
    value: function render() {
      var _props = this.props,
          active = _props.active,
          ariaLabel = _props['aria-label'],
          block = _props.block,
          className = _props.className,
          close = _props.close,
          cssModule = _props.cssModule,
          color = _props.color,
          outline = _props.outline,
          size = _props.size,
          Tag = _props.tag,
          innerRef = _props.innerRef,
          attributes = objectWithoutProperties(_props, ['active', 'aria-label', 'block', 'className', 'close', 'cssModule', 'color', 'outline', 'size', 'tag', 'innerRef']);


      if (close && typeof attributes.children === 'undefined') {
        attributes.children = __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
          'span',
          { 'aria-hidden': true },
          '\xD7'
        );
      }

      var btnOutlineColor = 'btn' + (outline ? '-outline' : '') + '-' + color;

      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, { close: close }, close || 'btn', close || btnOutlineColor, size ? 'btn-' + size : false, block ? 'btn-block' : false, { active: active, disabled: this.props.disabled }), cssModule);

      if (attributes.href && Tag === 'button') {
        Tag = 'a';
      }

      var defaultAriaLabel = close ? 'Close' : null;

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({
        type: Tag === 'button' && attributes.onClick ? 'button' : undefined
      }, attributes, {
        className: classes,
        ref: innerRef,
        onClick: this.onClick,
        'aria-label': ariaLabel || defaultAriaLabel
      }));
    }
  }]);
  return Button;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

Button.propTypes = propTypes$12;
Button.defaultProps = defaultProps$12;

var propTypes$13 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node
};

var ButtonDropdown = function ButtonDropdown(props) {
  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Dropdown, _extends({ group: true }, props));
};

ButtonDropdown.propTypes = propTypes$13;

var propTypes$14 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  'aria-label': __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  role: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  size: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  vertical: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool
};

var defaultProps$13 = {
  tag: 'div',
  role: 'group'
};

var ButtonGroup = function ButtonGroup(props) {
  var className = props.className,
      cssModule = props.cssModule,
      size = props.size,
      vertical = props.vertical,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'size', 'vertical', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, size ? 'btn-group-' + size : false, vertical ? 'btn-group-vertical' : 'btn-group'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

ButtonGroup.propTypes = propTypes$14;
ButtonGroup.defaultProps = defaultProps$13;

var propTypes$15 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  'aria-label': __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  role: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

var defaultProps$14 = {
  tag: 'div',
  role: 'toolbar'
};

var ButtonToolbar = function ButtonToolbar(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'btn-toolbar'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

ButtonToolbar.propTypes = propTypes$15;
ButtonToolbar.defaultProps = defaultProps$14;

var propTypes$16 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  active: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  disabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  divider: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  header: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  onClick: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  toggle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool
};

var contextTypes = {
  toggle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func
};

var defaultProps$15 = {
  tag: 'button',
  toggle: true
};

var DropdownItem = function (_React$Component) {
  inherits(DropdownItem, _React$Component);

  function DropdownItem(props) {
    classCallCheck(this, DropdownItem);

    var _this = possibleConstructorReturn(this, (DropdownItem.__proto__ || Object.getPrototypeOf(DropdownItem)).call(this, props));

    _this.onClick = _this.onClick.bind(_this);
    _this.getTabIndex = _this.getTabIndex.bind(_this);
    return _this;
  }

  createClass(DropdownItem, [{
    key: 'onClick',
    value: function onClick(e) {
      if (this.props.disabled || this.props.header || this.props.divider) {
        e.preventDefault();
        return;
      }

      if (this.props.onClick) {
        this.props.onClick(e);
      }

      if (this.props.toggle) {
        this.context.toggle(e);
      }
    }
  }, {
    key: 'getTabIndex',
    value: function getTabIndex() {
      if (this.props.disabled || this.props.header || this.props.divider) {
        return '-1';
      }

      return '0';
    }
  }, {
    key: 'render',
    value: function render() {
      var tabIndex = this.getTabIndex();

      var _omit = omit(this.props, ['toggle']),
          className = _omit.className,
          cssModule = _omit.cssModule,
          divider = _omit.divider,
          Tag = _omit.tag,
          header = _omit.header,
          active = _omit.active,
          props = objectWithoutProperties(_omit, ['className', 'cssModule', 'divider', 'tag', 'header', 'active']);

      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, {
        disabled: props.disabled,
        'dropdown-item': !divider && !header,
        active: active,
        'dropdown-header': header,
        'dropdown-divider': divider
      }), cssModule);

      if (Tag === 'button') {
        if (header) {
          Tag = 'h6';
        } else if (divider) {
          Tag = 'div';
        } else if (props.href) {
          Tag = 'a';
        }
      }

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({
        type: Tag === 'button' && (props.onClick || this.props.toggle) ? 'button' : undefined
      }, props, {
        tabIndex: tabIndex,
        className: classes,
        onClick: this.onClick
      }));
    }
  }]);
  return DropdownItem;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

DropdownItem.propTypes = propTypes$16;
DropdownItem.defaultProps = defaultProps$15;
DropdownItem.contextTypes = contextTypes;

var propTypes$17 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node.isRequired,
  right: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  flip: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  modifiers: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  persist: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool
};

var defaultProps$16 = {
  tag: 'div',
  flip: true
};

var contextTypes$1 = {
  isOpen: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool.isRequired,
  direction: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['up', 'down', 'left', 'right']).isRequired,
  inNavbar: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool.isRequired
};

var noFlipModifier = { flip: { enabled: false } };

var directionPositionMap = {
  up: 'top',
  left: 'left',
  right: 'right',
  down: 'bottom'
};

var DropdownMenu = function DropdownMenu(props, context) {
  var className = props.className,
      cssModule = props.cssModule,
      right = props.right,
      tag = props.tag,
      flip = props.flip,
      modifiers = props.modifiers,
      persist = props.persist,
      attrs = objectWithoutProperties(props, ['className', 'cssModule', 'right', 'tag', 'flip', 'modifiers', 'persist']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'dropdown-menu', {
    'dropdown-menu-right': right,
    show: context.isOpen
  }), cssModule);

  var Tag = tag;

  if (persist || context.isOpen && !context.inNavbar) {
    Tag = __WEBPACK_IMPORTED_MODULE_6_react_popper__["c" /* Popper */];

    var position1 = directionPositionMap[context.direction] || 'bottom';
    var position2 = right ? 'end' : 'start';
    attrs.placement = position1 + '-' + position2;
    attrs.component = tag;
    attrs.modifiers = !flip ? _extends({}, modifiers, noFlipModifier) : modifiers;
  }

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({
    tabIndex: '-1',
    role: 'menu'
  }, attrs, {
    'aria-hidden': !context.isOpen,
    className: classes,
    'x-placement': attrs.placement
  }));
};

DropdownMenu.propTypes = propTypes$17;
DropdownMenu.defaultProps = defaultProps$16;
DropdownMenu.contextTypes = contextTypes$1;

var propTypes$18 = {
  caret: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  color: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  disabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  onClick: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  'aria-haspopup': __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  split: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  nav: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool
};

var defaultProps$17 = {
  'aria-haspopup': true,
  color: 'secondary'
};

var contextTypes$2 = {
  isOpen: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool.isRequired,
  toggle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func.isRequired,
  inNavbar: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool.isRequired
};

var DropdownToggle = function (_React$Component) {
  inherits(DropdownToggle, _React$Component);

  function DropdownToggle(props) {
    classCallCheck(this, DropdownToggle);

    var _this = possibleConstructorReturn(this, (DropdownToggle.__proto__ || Object.getPrototypeOf(DropdownToggle)).call(this, props));

    _this.onClick = _this.onClick.bind(_this);
    return _this;
  }

  createClass(DropdownToggle, [{
    key: 'onClick',
    value: function onClick(e) {
      if (this.props.disabled) {
        e.preventDefault();
        return;
      }

      if (this.props.nav && !this.props.tag) {
        e.preventDefault();
      }

      if (this.props.onClick) {
        this.props.onClick(e);
      }

      this.context.toggle(e);
    }
  }, {
    key: 'render',
    value: function render() {
      var _props = this.props,
          className = _props.className,
          color = _props.color,
          cssModule = _props.cssModule,
          caret = _props.caret,
          split = _props.split,
          nav = _props.nav,
          tag = _props.tag,
          props = objectWithoutProperties(_props, ['className', 'color', 'cssModule', 'caret', 'split', 'nav', 'tag']);

      var ariaLabel = props['aria-label'] || 'Toggle Dropdown';
      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, {
        'dropdown-toggle': caret || split,
        'dropdown-toggle-split': split,
        'nav-link': nav
      }), cssModule);
      var children = props.children || __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        'span',
        { className: 'sr-only' },
        ariaLabel
      );

      var Tag = void 0;

      if (nav && !tag) {
        Tag = 'a';
        props.href = '#';
      } else if (!tag) {
        Tag = Button;
        props.color = color;
        props.cssModule = cssModule;
      } else {
        Tag = tag;
      }

      if (this.context.inNavbar) {
        return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, props, {
          className: classes,
          onClick: this.onClick,
          'aria-expanded': this.context.isOpen,
          children: children
        }));
      }

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_6_react_popper__["d" /* Target */], _extends({}, props, {
        className: classes,
        component: Tag,
        onClick: this.onClick,
        'aria-expanded': this.context.isOpen,
        children: children
      }));
    }
  }]);
  return DropdownToggle;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

DropdownToggle.propTypes = propTypes$18;
DropdownToggle.defaultProps = defaultProps$17;
DropdownToggle.contextTypes = contextTypes$2;

function unwrapExports (x) {
	return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
}

function createCommonjsModule(fn, module) {
	return module = { exports: {} }, fn(module, module.exports), module.exports;
}

var PropTypes$1 = createCommonjsModule(function (module, exports) {
  'use strict';

  exports.__esModule = true;
  exports.classNamesShape = exports.timeoutsShape = undefined;
  exports.transitionTimeout = transitionTimeout;

  var _propTypes2 = _interopRequireDefault(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a);

  function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : { default: obj };
  }

  function transitionTimeout(transitionType) {
    var timeoutPropName = 'transition' + transitionType + 'Timeout';
    var enabledPropName = 'transition' + transitionType;

    return function (props) {
      // If the transition is enabled
      if (props[enabledPropName]) {
        // If no timeout duration is provided
        if (props[timeoutPropName] == null) {
          return new Error(timeoutPropName + ' wasn\'t supplied to CSSTransitionGroup: ' + 'this can cause unreliable animations and won\'t be supported in ' + 'a future version of React. See ' + 'https://fb.me/react-animation-transition-group-timeout for more ' + 'information.');

          // If the duration isn't a number
        } else if (typeof props[timeoutPropName] !== 'number') {
          return new Error(timeoutPropName + ' must be a number (in milliseconds)');
        }
      }

      return null;
    };
  }

  var timeoutsShape = exports.timeoutsShape = _propTypes2.default.oneOfType([_propTypes2.default.number, _propTypes2.default.shape({
    enter: _propTypes2.default.number,
    exit: _propTypes2.default.number
  }).isRequired]);

  var classNamesShape = exports.classNamesShape = _propTypes2.default.oneOfType([_propTypes2.default.string, _propTypes2.default.shape({
    enter: _propTypes2.default.string,
    exit: _propTypes2.default.string,
    active: _propTypes2.default.string
  }), _propTypes2.default.shape({
    enter: _propTypes2.default.string,
    enterDone: _propTypes2.default.string,
    enterActive: _propTypes2.default.string,
    exit: _propTypes2.default.string,
    exitDone: _propTypes2.default.string,
    exitActive: _propTypes2.default.string
  })]);
});

unwrapExports(PropTypes$1);

var Transition_1 = createCommonjsModule(function (module, exports) {
  'use strict';

  exports.__esModule = true;
  exports.EXITING = exports.ENTERED = exports.ENTERING = exports.EXITED = exports.UNMOUNTED = undefined;

  var PropTypes$$1 = _interopRequireWildcard(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a);

  var _react2 = _interopRequireDefault(__WEBPACK_IMPORTED_MODULE_0_react___default.a);

  var _reactDom2 = _interopRequireDefault(__WEBPACK_IMPORTED_MODULE_5_react_dom___default.a);

  function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : { default: obj };
  }

  function _interopRequireWildcard(obj) {
    if (obj && obj.__esModule) {
      return obj;
    } else {
      var newObj = {};if (obj != null) {
        for (var key in obj) {
          if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key];
        }
      }newObj.default = obj;return newObj;
    }
  }

  function _objectWithoutProperties(obj, keys) {
    var target = {};for (var i in obj) {
      if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
    }return target;
  }

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _possibleConstructorReturn(self, call) {
    if (!self) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }return call && ((typeof call === 'undefined' ? 'undefined' : _typeof(call)) === "object" || typeof call === "function") ? call : self;
  }

  function _inherits(subClass, superClass) {
    if (typeof superClass !== "function" && superClass !== null) {
      throw new TypeError("Super expression must either be null or a function, not " + (typeof superClass === 'undefined' ? 'undefined' : _typeof(superClass)));
    }subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } });if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass;
  }

  var UNMOUNTED = exports.UNMOUNTED = 'unmounted';
  var EXITED = exports.EXITED = 'exited';
  var ENTERING = exports.ENTERING = 'entering';
  var ENTERED = exports.ENTERED = 'entered';
  var EXITING = exports.EXITING = 'exiting';

  /**
   * The Transition component lets you describe a transition from one component
   * state to another _over time_ with a simple declarative API. Most commonly
   * it's used to animate the mounting and unmounting of a component, but can also
   * be used to describe in-place transition states as well.
   *
   * By default the `Transition` component does not alter the behavior of the
   * component it renders, it only tracks "enter" and "exit" states for the components.
   * It's up to you to give meaning and effect to those states. For example we can
   * add styles to a component when it enters or exits:
   *
   * ```jsx
   * import Transition from 'react-transition-group/Transition';
   *
   * const duration = 300;
   *
   * const defaultStyle = {
   *   transition: `opacity ${duration}ms ease-in-out`,
   *   opacity: 0,
   * }
   *
   * const transitionStyles = {
   *   entering: { opacity: 0 },
   *   entered:  { opacity: 1 },
   * };
   *
   * const Fade = ({ in: inProp }) => (
   *   <Transition in={inProp} timeout={duration}>
   *     {(state) => (
   *       <div style={{
   *         ...defaultStyle,
   *         ...transitionStyles[state]
   *       }}>
   *         I'm a fade Transition!
   *       </div>
   *     )}
   *   </Transition>
   * );
   * ```
   *
   * As noted the `Transition` component doesn't _do_ anything by itself to its child component.
   * What it does do is track transition states over time so you can update the
   * component (such as by adding styles or classes) when it changes states.
   *
   * There are 4 main states a Transition can be in:
   *  - `'entering'`
   *  - `'entered'`
   *  - `'exiting'`
   *  - `'exited'`
   *
   * Transition state is toggled via the `in` prop. When `true` the component begins the
   * "Enter" stage. During this stage, the component will shift from its current transition state,
   * to `'entering'` for the duration of the transition and then to the `'entered'` stage once
   * it's complete. Let's take the following example:
   *
   * ```jsx
   * state = { in: false };
   *
   * toggleEnterState = () => {
   *   this.setState({ in: true });
   * }
   *
   * render() {
   *   return (
   *     <div>
   *       <Transition in={this.state.in} timeout={500} />
   *       <button onClick={this.toggleEnterState}>Click to Enter</button>
   *     </div>
   *   );
   * }
   * ```
   *
   * When the button is clicked the component will shift to the `'entering'` state and
   * stay there for 500ms (the value of `timeout`) before it finally switches to `'entered'`.
   *
   * When `in` is `false` the same thing happens except the state moves from `'exiting'` to `'exited'`.
   *
   * ## Timing
   *
   * Timing is often the trickiest part of animation, mistakes can result in slight delays
   * that are hard to pin down. A common example is when you want to add an exit transition,
   * you should set the desired final styles when the state is `'exiting'`. That's when the
   * transition to those styles will start and, if you matched the `timeout` prop with the
   * CSS Transition duration, it will end exactly when the state changes to `'exited'`.
   *
   * > **Note**: For simpler transitions the `Transition` component might be enough, but
   * > take into account that it's platform-agnostic, while the `CSSTransition` component
   * > [forces reflows](https://github.com/reactjs/react-transition-group/blob/5007303e729a74be66a21c3e2205e4916821524b/src/CSSTransition.js#L208-L215)
   * > in order to make more complex transitions more predictable. For example, even though
   * > classes `example-enter` and `example-enter-active` are applied immediately one after
   * > another, you can still transition from one to the other because of the forced reflow
   * > (read [this issue](https://github.com/reactjs/react-transition-group/issues/159#issuecomment-322761171)
   * > for more info). Take this into account when choosing between `Transition` and
   * > `CSSTransition`.
   *
   * ## Example
   *
   * <iframe src="https://codesandbox.io/embed/741op4mmj0?fontsize=14" style="width:100%; height:500px; border:0; border-radius: 4px; overflow:hidden;" sandbox="allow-modals allow-forms allow-popups allow-scripts allow-same-origin"></iframe>
   *
   */

  var Transition = function (_React$Component) {
    _inherits(Transition, _React$Component);

    function Transition(props, context) {
      _classCallCheck(this, Transition);

      var _this = _possibleConstructorReturn(this, _React$Component.call(this, props, context));

      var parentGroup = context.transitionGroup;
      // In the context of a TransitionGroup all enters are really appears
      var appear = parentGroup && !parentGroup.isMounting ? props.enter : props.appear;

      var initialStatus = void 0;
      _this.nextStatus = null;

      if (props.in) {
        if (appear) {
          initialStatus = EXITED;
          _this.nextStatus = ENTERING;
        } else {
          initialStatus = ENTERED;
        }
      } else {
        if (props.unmountOnExit || props.mountOnEnter) {
          initialStatus = UNMOUNTED;
        } else {
          initialStatus = EXITED;
        }
      }

      _this.state = { status: initialStatus };

      _this.nextCallback = null;
      return _this;
    }

    Transition.prototype.getChildContext = function getChildContext() {
      return { transitionGroup: null }; // allows for nested Transitions
    };

    Transition.prototype.componentDidMount = function componentDidMount() {
      this.updateStatus(true);
    };

    Transition.prototype.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
      var _ref = this.pendingState || this.state,
          status = _ref.status;

      if (nextProps.in) {
        if (status === UNMOUNTED) {
          this.setState({ status: EXITED });
        }
        if (status !== ENTERING && status !== ENTERED) {
          this.nextStatus = ENTERING;
        }
      } else {
        if (status === ENTERING || status === ENTERED) {
          this.nextStatus = EXITING;
        }
      }
    };

    Transition.prototype.componentDidUpdate = function componentDidUpdate() {
      this.updateStatus();
    };

    Transition.prototype.componentWillUnmount = function componentWillUnmount() {
      this.cancelNextCallback();
    };

    Transition.prototype.getTimeouts = function getTimeouts() {
      var timeout = this.props.timeout;

      var exit = void 0,
          enter = void 0,
          appear = void 0;

      exit = enter = appear = timeout;

      if (timeout != null && typeof timeout !== 'number') {
        exit = timeout.exit;
        enter = timeout.enter;
        appear = timeout.appear;
      }
      return { exit: exit, enter: enter, appear: appear };
    };

    Transition.prototype.updateStatus = function updateStatus() {
      var mounting = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

      var nextStatus = this.nextStatus;

      if (nextStatus !== null) {
        this.nextStatus = null;
        // nextStatus will always be ENTERING or EXITING.
        this.cancelNextCallback();
        var node = _reactDom2.default.findDOMNode(this);

        if (nextStatus === ENTERING) {
          this.performEnter(node, mounting);
        } else {
          this.performExit(node);
        }
      } else if (this.props.unmountOnExit && this.state.status === EXITED) {
        this.setState({ status: UNMOUNTED });
      }
    };

    Transition.prototype.performEnter = function performEnter(node, mounting) {
      var _this2 = this;

      var enter = this.props.enter;

      var appearing = this.context.transitionGroup ? this.context.transitionGroup.isMounting : mounting;

      var timeouts = this.getTimeouts();

      // no enter animation skip right to ENTERED
      // if we are mounting and running this it means appear _must_ be set
      if (!mounting && !enter) {
        this.safeSetState({ status: ENTERED }, function () {
          _this2.props.onEntered(node);
        });
        return;
      }

      this.props.onEnter(node, appearing);

      this.safeSetState({ status: ENTERING }, function () {
        _this2.props.onEntering(node, appearing);

        // FIXME: appear timeout?
        _this2.onTransitionEnd(node, timeouts.enter, function () {
          _this2.safeSetState({ status: ENTERED }, function () {
            _this2.props.onEntered(node, appearing);
          });
        });
      });
    };

    Transition.prototype.performExit = function performExit(node) {
      var _this3 = this;

      var exit = this.props.exit;

      var timeouts = this.getTimeouts();

      // no exit animation skip right to EXITED
      if (!exit) {
        this.safeSetState({ status: EXITED }, function () {
          _this3.props.onExited(node);
        });
        return;
      }
      this.props.onExit(node);

      this.safeSetState({ status: EXITING }, function () {
        _this3.props.onExiting(node);

        _this3.onTransitionEnd(node, timeouts.exit, function () {
          _this3.safeSetState({ status: EXITED }, function () {
            _this3.props.onExited(node);
          });
        });
      });
    };

    Transition.prototype.cancelNextCallback = function cancelNextCallback() {
      if (this.nextCallback !== null) {
        this.nextCallback.cancel();
        this.nextCallback = null;
      }
    };

    Transition.prototype.safeSetState = function safeSetState(nextState, callback) {
      var _this4 = this;

      // We need to track pending updates for instances where a cWRP fires quickly
      // after cDM and before the state flushes, which would double trigger a
      // transition
      this.pendingState = nextState;

      // This shouldn't be necessary, but there are weird race conditions with
      // setState callbacks and unmounting in testing, so always make sure that
      // we can cancel any pending setState callbacks after we unmount.
      callback = this.setNextCallback(callback);
      this.setState(nextState, function () {
        _this4.pendingState = null;
        callback();
      });
    };

    Transition.prototype.setNextCallback = function setNextCallback(callback) {
      var _this5 = this;

      var active = true;

      this.nextCallback = function (event) {
        if (active) {
          active = false;
          _this5.nextCallback = null;

          callback(event);
        }
      };

      this.nextCallback.cancel = function () {
        active = false;
      };

      return this.nextCallback;
    };

    Transition.prototype.onTransitionEnd = function onTransitionEnd(node, timeout, handler) {
      this.setNextCallback(handler);

      if (node) {
        if (this.props.addEndListener) {
          this.props.addEndListener(node, this.nextCallback);
        }
        if (timeout != null) {
          setTimeout(this.nextCallback, timeout);
        }
      } else {
        setTimeout(this.nextCallback, 0);
      }
    };

    Transition.prototype.render = function render() {
      var status = this.state.status;
      if (status === UNMOUNTED) {
        return null;
      }

      var _props = this.props,
          children = _props.children,
          childProps = _objectWithoutProperties(_props, ['children']);
      // filter props for Transtition


      delete childProps.in;
      delete childProps.mountOnEnter;
      delete childProps.unmountOnExit;
      delete childProps.appear;
      delete childProps.enter;
      delete childProps.exit;
      delete childProps.timeout;
      delete childProps.addEndListener;
      delete childProps.onEnter;
      delete childProps.onEntering;
      delete childProps.onEntered;
      delete childProps.onExit;
      delete childProps.onExiting;
      delete childProps.onExited;

      if (typeof children === 'function') {
        return children(status, childProps);
      }

      var child = _react2.default.Children.only(children);
      return _react2.default.cloneElement(child, childProps);
    };

    return Transition;
  }(_react2.default.Component);

  Transition.contextTypes = {
    transitionGroup: PropTypes$$1.object
  };
  Transition.childContextTypes = {
    transitionGroup: function transitionGroup() {}
  };

  Transition.propTypes = process.env.NODE_ENV !== "production" ? {
    /**
     * A `function` child can be used instead of a React element.
     * This function is called with the current transition status
     * ('entering', 'entered', 'exiting', 'exited', 'unmounted'), which can be used
     * to apply context specific props to a component.
     *
     * ```jsx
     * <Transition timeout={150}>
     *   {(status) => (
     *     <MyComponent className={`fade fade-${status}`} />
     *   )}
     * </Transition>
     * ```
     */
    children: PropTypes$$1.oneOfType([PropTypes$$1.func.isRequired, PropTypes$$1.element.isRequired]).isRequired,

    /**
     * Show the component; triggers the enter or exit states
     */
    in: PropTypes$$1.bool,

    /**
     * By default the child component is mounted immediately along with
     * the parent `Transition` component. If you want to "lazy mount" the component on the
     * first `in={true}` you can set `mountOnEnter`. After the first enter transition the component will stay
     * mounted, even on "exited", unless you also specify `unmountOnExit`.
     */
    mountOnEnter: PropTypes$$1.bool,

    /**
     * By default the child component stays mounted after it reaches the `'exited'` state.
     * Set `unmountOnExit` if you'd prefer to unmount the component after it finishes exiting.
     */
    unmountOnExit: PropTypes$$1.bool,

    /**
     * Normally a component is not transitioned if it is shown when the `<Transition>` component mounts.
     * If you want to transition on the first mount set `appear` to `true`, and the
     * component will transition in as soon as the `<Transition>` mounts.
     *
     * > Note: there are no specific "appear" states. `appear` only adds an additional `enter` transition.
     */
    appear: PropTypes$$1.bool,

    /**
     * Enable or disable enter transitions.
     */
    enter: PropTypes$$1.bool,

    /**
     * Enable or disable exit transitions.
     */
    exit: PropTypes$$1.bool,

    /**
     * The duration of the transition, in milliseconds.
     * Required unless `addEndListener` is provided
     *
     * You may specify a single timeout for all transitions like: `timeout={500}`,
     * or individually like:
     *
     * ```jsx
     * timeout={{
     *  enter: 300,
     *  exit: 500,
     * }}
     * ```
     *
     * @type {number | { enter?: number, exit?: number }}
     */
    timeout: function timeout(props) {
      for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }

      var pt = PropTypes$1.timeoutsShape;
      if (!props.addEndListener) pt = pt.isRequired;
      return pt.apply(undefined, [props].concat(args));
    },

    /**
     * Add a custom transition end trigger. Called with the transitioning
     * DOM node and a `done` callback. Allows for more fine grained transition end
     * logic. **Note:** Timeouts are still used as a fallback if provided.
     *
     * ```jsx
     * addEndListener={(node, done) => {
     *   // use the css transitionend event to mark the finish of a transition
     *   node.addEventListener('transitionend', done, false);
     * }}
     * ```
     */
    addEndListener: PropTypes$$1.func,

    /**
     * Callback fired before the "entering" status is applied. An extra parameter
     * `isAppearing` is supplied to indicate if the enter stage is occurring on the initial mount
     *
     * @type Function(node: HtmlElement, isAppearing: bool) -> void
     */
    onEnter: PropTypes$$1.func,

    /**
     * Callback fired after the "entering" status is applied. An extra parameter
     * `isAppearing` is supplied to indicate if the enter stage is occurring on the initial mount
     *
     * @type Function(node: HtmlElement, isAppearing: bool)
     */
    onEntering: PropTypes$$1.func,

    /**
     * Callback fired after the "entered" status is applied. An extra parameter
     * `isAppearing` is supplied to indicate if the enter stage is occurring on the initial mount
     *
     * @type Function(node: HtmlElement, isAppearing: bool) -> void
     */
    onEntered: PropTypes$$1.func,

    /**
     * Callback fired before the "exiting" status is applied.
     *
     * @type Function(node: HtmlElement) -> void
     */
    onExit: PropTypes$$1.func,

    /**
     * Callback fired after the "exiting" status is applied.
     *
     * @type Function(node: HtmlElement) -> void
     */
    onExiting: PropTypes$$1.func,

    /**
     * Callback fired after the "exited" status is applied.
     *
     * @type Function(node: HtmlElement) -> void
     */
    onExited: PropTypes$$1.func
  } : {};

  // Name the function so it is clearer in the documentation
  function noop() {}

  Transition.defaultProps = {
    in: false,
    mountOnEnter: false,
    unmountOnExit: false,
    appear: false,
    enter: true,
    exit: true,

    onEnter: noop,
    onEntering: noop,
    onEntered: noop,

    onExit: noop,
    onExiting: noop,
    onExited: noop
  };

  Transition.UNMOUNTED = 0;
  Transition.EXITED = 1;
  Transition.ENTERING = 2;
  Transition.ENTERED = 3;
  Transition.EXITING = 4;

  exports.default = Transition;
});

var Transition = unwrapExports(Transition_1);

var propTypes$19 = _extends({}, Transition.propTypes, {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.arrayOf(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node), __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node]),
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func]),
  baseClass: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  baseClassActive: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func])
});

var defaultProps$18 = _extends({}, Transition.defaultProps, {
  tag: 'div',
  baseClass: 'fade',
  baseClassActive: 'show',
  timeout: TransitionTimeouts.Fade,
  appear: true,
  enter: true,
  exit: true,
  in: true
});

function Fade(props) {
  var Tag = props.tag,
      baseClass = props.baseClass,
      baseClassActive = props.baseClassActive,
      className = props.className,
      cssModule = props.cssModule,
      children = props.children,
      innerRef = props.innerRef,
      otherProps = objectWithoutProperties(props, ['tag', 'baseClass', 'baseClassActive', 'className', 'cssModule', 'children', 'innerRef']);

  // In NODE_ENV=production the Transition.propTypes are wrapped which results in an
  // empty object "{}". This is the result of the `react-transition-group` babel
  // configuration settings. Therefore, to ensure that production builds work without
  // error, we can either explicitly define keys or use the Transition.defaultProps.
  // Using the Transition.defaultProps excludes any required props. Thus, the best
  // solution is to explicitly define required props in our utilities and reference these.
  // This also gives us more flexibility in the future to remove the prop-types
  // dependency in distribution builds (Similar to how `react-transition-group` does).
  // Note: Without omitting the `react-transition-group` props, the resulting child
  // Tag component would inherit the Transition properties as attributes for the HTML
  // element which results in errors/warnings for non-valid attributes.

  var transitionProps = pick(otherProps, TransitionPropTypeKeys);
  var childProps = omit(otherProps, TransitionPropTypeKeys);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    Transition,
    transitionProps,
    function (status) {
      var isActive = status === 'entered';
      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, baseClass, isActive && baseClassActive), cssModule);
      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        Tag,
        _extends({ className: classes }, childProps, { ref: innerRef }),
        children
      );
    }
  );
}

Fade.propTypes = propTypes$19;
Fade.defaultProps = defaultProps$18;

var propTypes$20 = {
  color: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  pill: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$19 = {
  color: 'secondary',
  pill: false,
  tag: 'span'
};

var Badge = function Badge(props) {
  var className = props.className,
      cssModule = props.cssModule,
      color = props.color,
      pill = props.pill,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'color', 'pill', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'badge', 'badge-' + color, pill ? 'badge-pill' : false), cssModule);

  if (attributes.href && Tag === 'span') {
    Tag = 'a';
  }

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

Badge.propTypes = propTypes$20;
Badge.defaultProps = defaultProps$19;

var propTypes$21 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  inverse: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  color: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  block: deprecated(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, 'Please use the props "body"'),
  body: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  outline: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func])
};

var defaultProps$20 = {
  tag: 'div'
};

var Card = function Card(props) {
  var className = props.className,
      cssModule = props.cssModule,
      color = props.color,
      block = props.block,
      body = props.body,
      inverse = props.inverse,
      outline = props.outline,
      Tag = props.tag,
      innerRef = props.innerRef,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'color', 'block', 'body', 'inverse', 'outline', 'tag', 'innerRef']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card', inverse ? 'text-white' : false, block || body ? 'card-body' : false, color ? (outline ? 'border' : 'bg') + '-' + color : false), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes, ref: innerRef }));
};

Card.propTypes = propTypes$21;
Card.defaultProps = defaultProps$20;

var propTypes$22 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$21 = {
  tag: 'div'
};

var CardGroup = function CardGroup(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-group'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardGroup.propTypes = propTypes$22;
CardGroup.defaultProps = defaultProps$21;

var propTypes$23 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$22 = {
  tag: 'div'
};

var CardDeck = function CardDeck(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-deck'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardDeck.propTypes = propTypes$23;
CardDeck.defaultProps = defaultProps$22;

var propTypes$24 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$23 = {
  tag: 'div'
};

var CardColumns = function CardColumns(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-columns'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardColumns.propTypes = propTypes$24;
CardColumns.defaultProps = defaultProps$23;

var propTypes$25 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$24 = {
  tag: 'div'
};

var CardBody = function CardBody(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-body'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardBody.propTypes = propTypes$25;
CardBody.defaultProps = defaultProps$24;

function CardBlock(props) {
  warnOnce('The "CardBlock" component has been deprecated.\nPlease use component "CardBody".');
  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(CardBody, props);
}

var propTypes$26 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$25 = {
  tag: 'a'
};

var CardLink = function CardLink(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      innerRef = props.innerRef,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag', 'innerRef']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-link'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { ref: innerRef, className: classes }));
};

CardLink.propTypes = propTypes$26;
CardLink.defaultProps = defaultProps$25;

var propTypes$27 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$26 = {
  tag: 'div'
};

var CardFooter = function CardFooter(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-footer'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardFooter.propTypes = propTypes$27;
CardFooter.defaultProps = defaultProps$26;

var propTypes$28 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$27 = {
  tag: 'div'
};

var CardHeader = function CardHeader(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-header'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardHeader.propTypes = propTypes$28;
CardHeader.defaultProps = defaultProps$27;

var propTypes$29 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  top: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  bottom: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$28 = {
  tag: 'img'
};

var CardImg = function CardImg(props) {
  var className = props.className,
      cssModule = props.cssModule,
      top = props.top,
      bottom = props.bottom,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'top', 'bottom', 'tag']);


  var cardImgClassName = 'card-img';
  if (top) {
    cardImgClassName = 'card-img-top';
  }
  if (bottom) {
    cardImgClassName = 'card-img-bottom';
  }

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, cardImgClassName), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardImg.propTypes = propTypes$29;
CardImg.defaultProps = defaultProps$28;

var propTypes$30 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$29 = {
  tag: 'div'
};

var CardImgOverlay = function CardImgOverlay(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-img-overlay'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardImgOverlay.propTypes = propTypes$30;
CardImgOverlay.defaultProps = defaultProps$29;

var CarouselItem = function (_React$Component) {
  inherits(CarouselItem, _React$Component);

  function CarouselItem(props) {
    classCallCheck(this, CarouselItem);

    var _this = possibleConstructorReturn(this, (CarouselItem.__proto__ || Object.getPrototypeOf(CarouselItem)).call(this, props));

    _this.state = {
      startAnimation: false
    };

    _this.onEnter = _this.onEnter.bind(_this);
    _this.onEntering = _this.onEntering.bind(_this);
    _this.onExit = _this.onExit.bind(_this);
    _this.onExiting = _this.onExiting.bind(_this);
    _this.onExited = _this.onExited.bind(_this);
    return _this;
  }

  createClass(CarouselItem, [{
    key: 'onEnter',
    value: function onEnter(node, isAppearing) {
      this.setState({ startAnimation: false });
      this.props.onEnter(node, isAppearing);
    }
  }, {
    key: 'onEntering',
    value: function onEntering(node, isAppearing) {
      // getting this variable triggers a reflow
      var offsetHeight = node.offsetHeight;
      this.setState({ startAnimation: true });
      this.props.onEntering(node, isAppearing);
      return offsetHeight;
    }
  }, {
    key: 'onExit',
    value: function onExit(node) {
      this.setState({ startAnimation: false });
      this.props.onExit(node);
    }
  }, {
    key: 'onExiting',
    value: function onExiting(node) {
      this.setState({ startAnimation: true });
      node.dispatchEvent(new CustomEvent('slide.bs.carousel'));
      this.props.onExiting(node);
    }
  }, {
    key: 'onExited',
    value: function onExited(node) {
      node.dispatchEvent(new CustomEvent('slid.bs.carousel'));
      this.props.onExited(node);
    }
  }, {
    key: 'render',
    value: function render() {
      var _this2 = this;

      var _props = this.props,
          isIn = _props.in,
          children = _props.children,
          cssModule = _props.cssModule,
          slide = _props.slide,
          Tag = _props.tag,
          className = _props.className,
          transitionProps = objectWithoutProperties(_props, ['in', 'children', 'cssModule', 'slide', 'tag', 'className']);


      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        Transition,
        _extends({}, transitionProps, {
          enter: slide,
          exit: slide,
          'in': isIn,
          onEnter: this.onEnter,
          onEntering: this.onEntering,
          onExit: this.onExit,
          onExiting: this.onExiting,
          onExited: this.onExited
        }),
        function (status) {
          var direction = _this2.context.direction;

          var isActive = status === TransitionStatuses.ENTERED || status === TransitionStatuses.EXITING;
          var directionClassName = (status === TransitionStatuses.ENTERING || status === TransitionStatuses.EXITING) && _this2.state.startAnimation && (direction === 'right' ? 'carousel-item-left' : 'carousel-item-right');
          var orderClassName = status === TransitionStatuses.ENTERING && (direction === 'right' ? 'carousel-item-next' : 'carousel-item-prev');
          var itemClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'carousel-item', isActive && 'active', directionClassName, orderClassName), cssModule);

          return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
            Tag,
            { className: itemClasses },
            children
          );
        }
      );
    }
  }]);
  return CarouselItem;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

CarouselItem.propTypes = _extends({}, Transition.propTypes, {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  in: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  slide: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
});

CarouselItem.defaultProps = _extends({}, Transition.defaultProps, {
  tag: 'div',
  timeout: TransitionTimeouts.Carousel,
  slide: true
});

CarouselItem.contextTypes = {
  direction: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

var Carousel = function (_React$Component) {
  inherits(Carousel, _React$Component);

  function Carousel(props) {
    classCallCheck(this, Carousel);

    var _this = possibleConstructorReturn(this, (Carousel.__proto__ || Object.getPrototypeOf(Carousel)).call(this, props));

    _this.handleKeyPress = _this.handleKeyPress.bind(_this);
    _this.renderItems = _this.renderItems.bind(_this);
    _this.hoverStart = _this.hoverStart.bind(_this);
    _this.hoverEnd = _this.hoverEnd.bind(_this);
    _this.state = {
      direction: 'right',
      indicatorClicked: false
    };
    return _this;
  }

  createClass(Carousel, [{
    key: 'getChildContext',
    value: function getChildContext() {
      return { direction: this.state.direction };
    }
  }, {
    key: 'componentDidMount',
    value: function componentDidMount() {
      // Set up the cycle
      if (this.props.ride === 'carousel') {
        this.setInterval();
      }

      // TODO: move this to the specific carousel like bootstrap. Currently it will trigger ALL carousels on the page.
      document.addEventListener('keyup', this.handleKeyPress);
    }
  }, {
    key: 'componentWillReceiveProps',
    value: function componentWillReceiveProps(nextProps) {
      this.setInterval(nextProps);
      // Calculate the direction to turn
      if (this.props.activeIndex + 1 === nextProps.activeIndex) {
        this.setState({ direction: 'right' });
      } else if (this.props.activeIndex - 1 === nextProps.activeIndex) {
        this.setState({ direction: 'left' });
      } else if (this.props.activeIndex > nextProps.activeIndex) {
        this.setState({ direction: this.state.indicatorClicked ? 'left' : 'right' });
      } else if (this.props.activeIndex !== nextProps.activeIndex) {
        this.setState({ direction: this.state.indicatorClicked ? 'right' : 'left' });
      }
      this.setState({ indicatorClicked: false });
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      this.clearInterval();
      document.removeEventListener('keyup', this.handleKeyPress);
    }
  }, {
    key: 'setInterval',
    value: function (_setInterval) {
      function setInterval() {
        return _setInterval.apply(this, arguments);
      }

      setInterval.toString = function () {
        return _setInterval.toString();
      };

      return setInterval;
    }(function () {
      var props = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.props;

      // make sure not to have multiple intervals going...
      this.clearInterval();
      if (props.interval) {
        this.cycleInterval = setInterval(function () {
          props.next();
        }, parseInt(props.interval, 10));
      }
    })
  }, {
    key: 'clearInterval',
    value: function (_clearInterval) {
      function clearInterval() {
        return _clearInterval.apply(this, arguments);
      }

      clearInterval.toString = function () {
        return _clearInterval.toString();
      };

      return clearInterval;
    }(function () {
      clearInterval(this.cycleInterval);
    })
  }, {
    key: 'hoverStart',
    value: function hoverStart() {
      if (this.props.pause === 'hover') {
        this.clearInterval();
      }
      if (this.props.mouseEnter) {
        var _props;

        (_props = this.props).mouseEnter.apply(_props, arguments);
      }
    }
  }, {
    key: 'hoverEnd',
    value: function hoverEnd() {
      if (this.props.pause === 'hover') {
        this.setInterval();
      }
      if (this.props.mouseLeave) {
        var _props2;

        (_props2 = this.props).mouseLeave.apply(_props2, arguments);
      }
    }
  }, {
    key: 'handleKeyPress',
    value: function handleKeyPress(evt) {
      if (this.props.keyboard) {
        if (evt.keyCode === 37) {
          this.props.previous();
        } else if (evt.keyCode === 39) {
          this.props.next();
        }
      }
    }
  }, {
    key: 'renderItems',
    value: function renderItems(carouselItems, className) {
      var _this2 = this;

      var slide = this.props.slide;

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        'div',
        { role: 'listbox', className: className },
        carouselItems.map(function (item, index) {
          var isIn = index === _this2.props.activeIndex;
          return __WEBPACK_IMPORTED_MODULE_0_react___default.a.cloneElement(item, {
            in: isIn,
            slide: slide
          });
        })
      );
    }
  }, {
    key: 'render',
    value: function render() {
      var _this3 = this;

      var _props3 = this.props,
          cssModule = _props3.cssModule,
          slide = _props3.slide,
          className = _props3.className;

      var outerClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'carousel', slide && 'slide'), cssModule);

      var innerClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('carousel-inner'), cssModule);

      // filter out booleans, null, or undefined
      var children = this.props.children.filter(function (child) {
        return child !== null && child !== undefined && typeof child !== 'boolean';
      });

      var slidesOnly = children.every(function (child) {
        return child.type === CarouselItem;
      });

      // Rendering only slides
      if (slidesOnly) {
        return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
          'div',
          { className: outerClasses, onMouseEnter: this.hoverStart, onMouseLeave: this.hoverEnd },
          this.renderItems(children, innerClasses)
        );
      }

      // Rendering slides and controls
      if (children[0] instanceof Array) {
        var _carouselItems = children[0];
        var _controlLeft = children[1];
        var _controlRight = children[2];

        return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
          'div',
          { className: outerClasses, onMouseEnter: this.hoverStart, onMouseLeave: this.hoverEnd },
          this.renderItems(_carouselItems, innerClasses),
          _controlLeft,
          _controlRight
        );
      }

      // Rendering indicators, slides and controls
      var indicators = children[0];
      var wrappedOnClick = function wrappedOnClick(e) {
        if (typeof indicators.props.onClickHandler === 'function') {
          _this3.setState({ indicatorClicked: true }, function () {
            return indicators.props.onClickHandler(e);
          });
        }
      };
      var wrappedIndicators = __WEBPACK_IMPORTED_MODULE_0_react___default.a.cloneElement(indicators, { onClickHandler: wrappedOnClick });
      var carouselItems = children[1];
      var controlLeft = children[2];
      var controlRight = children[3];

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        'div',
        { className: outerClasses, onMouseEnter: this.hoverStart, onMouseLeave: this.hoverEnd },
        wrappedIndicators,
        this.renderItems(carouselItems, innerClasses),
        controlLeft,
        controlRight
      );
    }
  }]);
  return Carousel;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

Carousel.propTypes = {
  // the current active slide of the carousel
  activeIndex: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number,
  // a function which should advance the carousel to the next slide (via activeIndex)
  next: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func.isRequired,
  // a function which should advance the carousel to the previous slide (via activeIndex)
  previous: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func.isRequired,
  // controls if the left and right arrow keys should control the carousel
  keyboard: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  /* If set to "hover", pauses the cycling of the carousel on mouseenter and resumes the cycling of the carousel on
   * mouseleave. If set to false, hovering over the carousel won't pause it. (default: "hover")
   */
  pause: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['hover', false]),
  // Autoplays the carousel after the user manually cycles the first item. If "carousel", autoplays the carousel on load.
  // This is how bootstrap defines it... I would prefer a bool named autoplay or something...
  ride: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['carousel']),
  // the interval at which the carousel automatically cycles (default: 5000)
  // eslint-disable-next-line react/no-unused-prop-types
  interval: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool]),
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.array,
  // called when the mouse enters the Carousel
  mouseEnter: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  // called when the mouse exits the Carousel
  mouseLeave: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  // controls whether the slide animation on the Carousel works or not
  slide: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

Carousel.defaultProps = {
  interval: 5000,
  pause: 'hover',
  keyboard: true,
  slide: true
};

Carousel.childContextTypes = {
  direction: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

var CarouselControl = function CarouselControl(props) {
  var direction = props.direction,
      onClickHandler = props.onClickHandler,
      cssModule = props.cssModule,
      directionText = props.directionText,
      className = props.className;


  var anchorClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'carousel-control-' + direction), cssModule);

  var iconClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('carousel-control-' + direction + '-icon'), cssModule);

  var screenReaderClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('sr-only'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    'a',
    {
      className: anchorClasses,
      role: 'button',
      tabIndex: '0',
      onClick: function onClick(e) {
        e.preventDefault();
        onClickHandler();
      }
    },
    __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('span', { className: iconClasses, 'aria-hidden': 'true' }),
    __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      'span',
      { className: screenReaderClasses },
      directionText || direction
    )
  );
};

CarouselControl.propTypes = {
  direction: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['prev', 'next']).isRequired,
  onClickHandler: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func.isRequired,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  directionText: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

var CarouselIndicators = function CarouselIndicators(props) {
  var items = props.items,
      activeIndex = props.activeIndex,
      cssModule = props.cssModule,
      onClickHandler = props.onClickHandler,
      className = props.className;


  var listClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'carousel-indicators'), cssModule);
  var indicators = items.map(function (item, idx) {
    var indicatorClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()({ active: activeIndex === idx }), cssModule);
    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('li', {
      key: '' + (item.key || item.src) + item.caption + item.altText,
      onClick: function onClick(e) {
        e.preventDefault();
        onClickHandler(idx);
      },
      className: indicatorClasses
    });
  });

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    'ol',
    { className: listClasses },
    indicators
  );
};

CarouselIndicators.propTypes = {
  items: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.array.isRequired,
  activeIndex: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number.isRequired,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  onClickHandler: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func.isRequired,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

var CarouselCaption = function CarouselCaption(props) {
  var captionHeader = props.captionHeader,
      captionText = props.captionText,
      cssModule = props.cssModule,
      className = props.className;

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'carousel-caption', 'd-none', 'd-md-block'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    'div',
    { className: classes },
    __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      'h3',
      null,
      captionHeader
    ),
    __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      'p',
      null,
      captionText
    )
  );
};

CarouselCaption.propTypes = {
  captionHeader: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  captionText: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string.isRequired,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

var propTypes$31 = {
  items: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.array.isRequired,
  indicators: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  controls: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  autoPlay: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  activeIndex: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number,
  next: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  previous: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  goToIndex: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func
};

var UncontrolledCarousel = function (_Component) {
  inherits(UncontrolledCarousel, _Component);

  function UncontrolledCarousel(props) {
    classCallCheck(this, UncontrolledCarousel);

    var _this = possibleConstructorReturn(this, (UncontrolledCarousel.__proto__ || Object.getPrototypeOf(UncontrolledCarousel)).call(this, props));

    _this.animating = false;
    _this.state = { activeIndex: 0 };
    _this.next = _this.next.bind(_this);
    _this.previous = _this.previous.bind(_this);
    _this.goToIndex = _this.goToIndex.bind(_this);
    _this.onExiting = _this.onExiting.bind(_this);
    _this.onExited = _this.onExited.bind(_this);
    return _this;
  }

  createClass(UncontrolledCarousel, [{
    key: 'onExiting',
    value: function onExiting() {
      this.animating = true;
    }
  }, {
    key: 'onExited',
    value: function onExited() {
      this.animating = false;
    }
  }, {
    key: 'next',
    value: function next() {
      if (this.animating) return;
      var nextIndex = this.state.activeIndex === this.props.items.length - 1 ? 0 : this.state.activeIndex + 1;
      this.setState({ activeIndex: nextIndex });
    }
  }, {
    key: 'previous',
    value: function previous() {
      if (this.animating) return;
      var nextIndex = this.state.activeIndex === 0 ? this.props.items.length - 1 : this.state.activeIndex - 1;
      this.setState({ activeIndex: nextIndex });
    }
  }, {
    key: 'goToIndex',
    value: function goToIndex(newIndex) {
      if (this.animating) return;
      this.setState({ activeIndex: newIndex });
    }
  }, {
    key: 'render',
    value: function render() {
      var _this2 = this;

      var _props = this.props,
          autoPlay = _props.autoPlay,
          indicators = _props.indicators,
          controls = _props.controls,
          items = _props.items,
          goToIndex = _props.goToIndex,
          props = objectWithoutProperties(_props, ['autoPlay', 'indicators', 'controls', 'items', 'goToIndex']);
      var activeIndex = this.state.activeIndex;


      var slides = items.map(function (item) {
        return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
          CarouselItem,
          {
            onExiting: _this2.onExiting,
            onExited: _this2.onExited,
            key: item.src
          },
          __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('img', { className: 'd-block w-100', src: item.src, alt: item.altText }),
          __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(CarouselCaption, { captionText: item.caption, captionHeader: item.header || item.caption })
        );
      });

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        Carousel,
        _extends({
          activeIndex: activeIndex,
          next: this.next,
          previous: this.previous,
          ride: autoPlay ? 'carousel' : undefined
        }, props),
        indicators && __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(CarouselIndicators, {
          items: items,
          activeIndex: props.activeIndex || activeIndex,
          onClickHandler: goToIndex || this.goToIndex
        }),
        slides,
        controls && __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(CarouselControl, {
          direction: 'prev',
          directionText: 'Previous',
          onClickHandler: props.previous || this.previous
        }),
        controls && __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(CarouselControl, {
          direction: 'next',
          directionText: 'Next',
          onClickHandler: props.next || this.next
        })
      );
    }
  }]);
  return UncontrolledCarousel;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);

UncontrolledCarousel.propTypes = propTypes$31;
UncontrolledCarousel.defaultProps = {
  controls: true,
  indicators: true,
  autoPlay: true
};

var propTypes$32 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$30 = {
  tag: 'h6'
};

var CardSubtitle = function CardSubtitle(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-subtitle'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardSubtitle.propTypes = propTypes$32;
CardSubtitle.defaultProps = defaultProps$30;

var propTypes$33 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$31 = {
  tag: 'p'
};

var CardText = function CardText(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-text'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardText.propTypes = propTypes$33;
CardText.defaultProps = defaultProps$31;

var propTypes$34 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$32 = {
  tag: 'h5'
};

var CardTitle = function CardTitle(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'card-title'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

CardTitle.propTypes = propTypes$34;
CardTitle.defaultProps = defaultProps$32;

var propTypes$35 = {
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  id: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number]).isRequired,
  type: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string.isRequired,
  label: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  inline: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  valid: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  invalid: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  bsSize: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.array, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func]),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func])
};

function CustomInput(props) {
  var className = props.className,
      label = props.label,
      inline = props.inline,
      valid = props.valid,
      invalid = props.invalid,
      cssModule = props.cssModule,
      children = props.children,
      bsSize = props.bsSize,
      innerRef = props.innerRef,
      attributes = objectWithoutProperties(props, ['className', 'label', 'inline', 'valid', 'invalid', 'cssModule', 'children', 'bsSize', 'innerRef']);


  var type = attributes.type;

  var customClass = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'custom-' + type, bsSize ? 'custom-' + type + '-' + bsSize : false), cssModule);

  var validationClassNames = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(invalid && 'is-invalid', valid && 'is-valid'), cssModule);

  if (type === 'select') {
    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      'select',
      _extends({}, attributes, { ref: innerRef, className: __WEBPACK_IMPORTED_MODULE_2_classnames___default()(validationClassNames, customClass) }),
      children
    );
  }

  if (type === 'file') {
    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      'div',
      { className: customClass },
      __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('input', _extends({}, attributes, { ref: innerRef, className: __WEBPACK_IMPORTED_MODULE_2_classnames___default()(validationClassNames, mapToCssModules('custom-file-input', cssModule)) })),
      __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        'label',
        { className: mapToCssModules('custom-file-label', cssModule), htmlFor: attributes.id },
        label || 'Choose file'
      )
    );
  }

  if (type !== 'checkbox' && type !== 'radio') {
    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('input', _extends({}, attributes, { ref: innerRef, className: __WEBPACK_IMPORTED_MODULE_2_classnames___default()(validationClassNames, customClass) }));
  }

  var wrapperClasses = __WEBPACK_IMPORTED_MODULE_2_classnames___default()(customClass, mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('custom-control', { 'custom-control-inline': inline }), cssModule));

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    'div',
    { className: wrapperClasses },
    __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('input', _extends({}, attributes, {
      ref: innerRef,
      className: __WEBPACK_IMPORTED_MODULE_2_classnames___default()(validationClassNames, mapToCssModules('custom-control-input', cssModule))
    })),
    __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      'label',
      { className: mapToCssModules('custom-control-label', cssModule), htmlFor: attributes.id },
      label
    ),
    children
  );
}

CustomInput.propTypes = propTypes$35;

var propTypes$36 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node.isRequired,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  placement: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  placementPrefix: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  arrowClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  hideArrow: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  isOpen: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool.isRequired,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  offset: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number]),
  fallbackPlacement: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.array]),
  flip: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  container: targetPropType,
  target: targetPropType.isRequired,
  modifiers: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  boundariesElement: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, DOMElement])
};

var defaultProps$33 = {
  boundariesElement: 'scrollParent',
  placement: 'auto',
  hideArrow: false,
  isOpen: false,
  offset: 0,
  fallbackPlacement: 'flip',
  flip: true,
  container: 'body',
  modifiers: {}
};

var childContextTypes$1 = {
  popperManager: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object.isRequired
};

var PopperContent = function (_React$Component) {
  inherits(PopperContent, _React$Component);

  function PopperContent(props) {
    classCallCheck(this, PopperContent);

    var _this = possibleConstructorReturn(this, (PopperContent.__proto__ || Object.getPrototypeOf(PopperContent)).call(this, props));

    _this.handlePlacementChange = _this.handlePlacementChange.bind(_this);
    _this.setTargetNode = _this.setTargetNode.bind(_this);
    _this.getTargetNode = _this.getTargetNode.bind(_this);
    _this.state = {};
    return _this;
  }

  createClass(PopperContent, [{
    key: 'getChildContext',
    value: function getChildContext() {
      return {
        popperManager: {
          setTargetNode: this.setTargetNode,
          getTargetNode: this.getTargetNode
        }
      };
    }
  }, {
    key: 'componentDidMount',
    value: function componentDidMount() {
      this.handleProps();
    }
  }, {
    key: 'componentDidUpdate',
    value: function componentDidUpdate(prevProps) {
      if (this.props.isOpen !== prevProps.isOpen) {
        this.handleProps();
      } else if (this._element) {
        // rerender
        this.renderIntoSubtree();
      }
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      this.hide();
    }
  }, {
    key: 'setTargetNode',
    value: function setTargetNode(node) {
      this.targetNode = node;
    }
  }, {
    key: 'getTargetNode',
    value: function getTargetNode() {
      return this.targetNode;
    }
  }, {
    key: 'getContainerNode',
    value: function getContainerNode() {
      return getTarget(this.props.container);
    }
  }, {
    key: 'handlePlacementChange',
    value: function handlePlacementChange(data) {
      if (this.state.placement !== data.placement) {
        this.setState({ placement: data.placement });
      }
      return data;
    }
  }, {
    key: 'handleProps',
    value: function handleProps() {
      if (this.props.container !== 'inline') {
        if (this.props.isOpen) {
          this.show();
        } else {
          this.hide();
        }
      }
    }
  }, {
    key: 'hide',
    value: function hide() {
      if (this._element) {
        this.getContainerNode().removeChild(this._element);
        __WEBPACK_IMPORTED_MODULE_5_react_dom___default.a.unmountComponentAtNode(this._element);
        this._element = null;
      }
    }
  }, {
    key: 'show',
    value: function show() {
      this._element = document.createElement('div');
      this.getContainerNode().appendChild(this._element);
      this.renderIntoSubtree();
      if (this._element.childNodes && this._element.childNodes[0] && this._element.childNodes[0].focus) {
        this._element.childNodes[0].focus();
      }
    }
  }, {
    key: 'renderIntoSubtree',
    value: function renderIntoSubtree() {
      __WEBPACK_IMPORTED_MODULE_5_react_dom___default.a.unstable_renderSubtreeIntoContainer(this, this.renderChildren(), this._element);
    }
  }, {
    key: 'renderChildren',
    value: function renderChildren() {
      var _props = this.props,
          cssModule = _props.cssModule,
          children = _props.children,
          isOpen = _props.isOpen,
          flip = _props.flip,
          target = _props.target,
          offset = _props.offset,
          fallbackPlacement = _props.fallbackPlacement,
          placementPrefix = _props.placementPrefix,
          _arrowClassName = _props.arrowClassName,
          hideArrow = _props.hideArrow,
          className = _props.className,
          tag = _props.tag,
          container = _props.container,
          modifiers = _props.modifiers,
          boundariesElement = _props.boundariesElement,
          attrs = objectWithoutProperties(_props, ['cssModule', 'children', 'isOpen', 'flip', 'target', 'offset', 'fallbackPlacement', 'placementPrefix', 'arrowClassName', 'hideArrow', 'className', 'tag', 'container', 'modifiers', 'boundariesElement']);

      var arrowClassName = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('arrow', _arrowClassName), cssModule);
      var placement = (this.state.placement || attrs.placement).split('-')[0];
      var popperClassName = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, placementPrefix ? placementPrefix + '-' + placement : placement), this.props.cssModule);

      var extendedModifiers = _extends({
        offset: { offset: offset },
        flip: { enabled: flip, behavior: fallbackPlacement },
        preventOverflow: { boundariesElement: boundariesElement },
        update: {
          enabled: true,
          order: 950,
          fn: this.handlePlacementChange
        }
      }, modifiers);

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        __WEBPACK_IMPORTED_MODULE_6_react_popper__["c" /* Popper */],
        _extends({ modifiers: extendedModifiers }, attrs, { component: tag, className: popperClassName, 'x-placement': this.state.placement || attrs.placement }),
        children,
        !hideArrow && __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_6_react_popper__["a" /* Arrow */], { className: arrowClassName })
      );
    }
  }, {
    key: 'render',
    value: function render() {
      this.setTargetNode(getTarget(this.props.target));

      if (this.props.container === 'inline') {
        return this.props.isOpen ? this.renderChildren() : null;
      }

      return null;
    }
  }]);
  return PopperContent;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

PopperContent.propTypes = propTypes$36;
PopperContent.defaultProps = defaultProps$33;
PopperContent.childContextTypes = childContextTypes$1;

var PopperTargetHelper = function PopperTargetHelper(props, context) {
  context.popperManager.setTargetNode(getTarget(props.target));
  return null;
};

PopperTargetHelper.contextTypes = {
  popperManager: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object.isRequired
};

PopperTargetHelper.propTypes = {
  target: targetPropType.isRequired
};

var propTypes$37 = {
  placement: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(PopperPlacements),
  target: targetPropType.isRequired,
  container: targetPropType,
  boundariesElement: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, DOMElement]),
  isOpen: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  disabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  hideArrow: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  innerClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  placementPrefix: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  toggle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  delay: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.shape({ show: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number, hide: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number }), __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number]),
  modifiers: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  offset: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number])
};

var DEFAULT_DELAYS = {
  show: 0,
  hide: 0
};

var defaultProps$34 = {
  isOpen: false,
  hideArrow: false,
  placement: 'right',
  placementPrefix: 'bs-popover',
  delay: DEFAULT_DELAYS,
  toggle: function toggle() {}
};

var Popover = function (_React$Component) {
  inherits(Popover, _React$Component);

  function Popover(props) {
    classCallCheck(this, Popover);

    var _this = possibleConstructorReturn(this, (Popover.__proto__ || Object.getPrototypeOf(Popover)).call(this, props));

    _this.addTargetEvents = _this.addTargetEvents.bind(_this);
    _this.handleDocumentClick = _this.handleDocumentClick.bind(_this);
    _this.removeTargetEvents = _this.removeTargetEvents.bind(_this);
    _this.getRef = _this.getRef.bind(_this);
    _this.toggle = _this.toggle.bind(_this);
    _this.show = _this.show.bind(_this);
    _this.hide = _this.hide.bind(_this);
    _this._target = null;
    return _this;
  }

  createClass(Popover, [{
    key: 'componentDidMount',
    value: function componentDidMount() {
      this._target = getTarget(this.props.target);
      this.handleProps();
    }
  }, {
    key: 'componentDidUpdate',
    value: function componentDidUpdate() {
      this.handleProps();
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      this.clearShowTimeout();
      this.clearHideTimeout();
      this.removeTargetEvents();
    }
  }, {
    key: 'getRef',
    value: function getRef(ref) {
      this._popover = ref;
    }
  }, {
    key: 'getDelay',
    value: function getDelay(key) {
      var delay = this.props.delay;

      if ((typeof delay === 'undefined' ? 'undefined' : _typeof(delay)) === 'object') {
        return isNaN(delay[key]) ? DEFAULT_DELAYS[key] : delay[key];
      }
      return delay;
    }
  }, {
    key: 'handleProps',
    value: function handleProps() {
      if (this.props.isOpen) {
        this.show();
      } else {
        this.hide();
      }
    }
  }, {
    key: 'show',
    value: function show() {
      this.clearHideTimeout();
      this.addTargetEvents();
      if (!this.props.isOpen) {
        this.clearShowTimeout();
        this._showTimeout = setTimeout(this.toggle, this.getDelay('show'));
      }
    }
  }, {
    key: 'hide',
    value: function hide() {
      this.clearShowTimeout();
      this.removeTargetEvents();
      if (this.props.isOpen) {
        this.clearHideTimeout();
        this._hideTimeout = setTimeout(this.toggle, this.getDelay('hide'));
      }
    }
  }, {
    key: 'clearShowTimeout',
    value: function clearShowTimeout() {
      clearTimeout(this._showTimeout);
      this._showTimeout = undefined;
    }
  }, {
    key: 'clearHideTimeout',
    value: function clearHideTimeout() {
      clearTimeout(this._hideTimeout);
      this._hideTimeout = undefined;
    }
  }, {
    key: 'handleDocumentClick',
    value: function handleDocumentClick(e) {
      if (this._target) {
        if (e.target !== this._target && !this._target.contains(e.target) && e.target !== this._popover && !(this._popover && this._popover.contains(e.target))) {
          if (this._hideTimeout) {
            this.clearHideTimeout();
          }

          if (this.props.isOpen) {
            this.toggle(e);
          }
        }
      }
    }
  }, {
    key: 'addTargetEvents',
    value: function addTargetEvents() {
      var _this2 = this;

      ['click', 'touchstart'].forEach(function (event) {
        return document.addEventListener(event, _this2.handleDocumentClick, true);
      });
    }
  }, {
    key: 'removeTargetEvents',
    value: function removeTargetEvents() {
      var _this3 = this;

      ['click', 'touchstart'].forEach(function (event) {
        return document.removeEventListener(event, _this3.handleDocumentClick, true);
      });
    }
  }, {
    key: 'toggle',
    value: function toggle(e) {
      if (this.props.disabled) {
        return e && e.preventDefault();
      }

      return this.props.toggle(e);
    }
  }, {
    key: 'render',
    value: function render() {
      if (!this.props.isOpen) {
        return null;
      }

      var attributes = omit(this.props, Object.keys(propTypes$37));
      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('popover-inner', this.props.innerClassName), this.props.cssModule);

      var popperClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('popover', 'show', this.props.className), this.props.cssModule);

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        PopperContent,
        {
          className: popperClasses,
          target: this.props.target,
          isOpen: this.props.isOpen,
          hideArrow: this.props.hideArrow,
          placement: this.props.placement,
          placementPrefix: this.props.placementPrefix,
          container: this.props.container,
          modifiers: this.props.modifiers,
          offset: this.props.offset,
          boundariesElement: this.props.boundariesElement
        },
        __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('div', _extends({}, attributes, { className: classes, ref: this.getRef }))
      );
    }
  }]);
  return Popover;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

Popover.propTypes = propTypes$37;
Popover.defaultProps = defaultProps$34;

var propTypes$38 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$35 = {
  tag: 'h3'
};

var PopoverHeader = function PopoverHeader(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'popover-header'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

PopoverHeader.propTypes = propTypes$38;
PopoverHeader.defaultProps = defaultProps$35;

function PopoverTitle(props) {
  warnOnce('The "PopoverTitle" component has been deprecated.\nPlease use component "PopoverHeader".');
  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(PopoverHeader, props);
}

var propTypes$39 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$36 = {
  tag: 'div'
};

var PopoverBody = function PopoverBody(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'popover-body'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

PopoverBody.propTypes = propTypes$39;
PopoverBody.defaultProps = defaultProps$36;

function PopoverContent(props) {
  warnOnce('The "PopoverContent" component has been deprecated.\nPlease use component "PopoverBody".');
  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(PopoverBody, props);
}

var propTypes$40 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  bar: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  multi: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  value: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number]),
  max: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number]),
  animated: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  striped: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  color: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  barClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$37 = {
  tag: 'div',
  value: 0,
  max: 100
};

var Progress = function Progress(props) {
  var children = props.children,
      className = props.className,
      barClassName = props.barClassName,
      cssModule = props.cssModule,
      value = props.value,
      max = props.max,
      animated = props.animated,
      striped = props.striped,
      color = props.color,
      bar = props.bar,
      multi = props.multi,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['children', 'className', 'barClassName', 'cssModule', 'value', 'max', 'animated', 'striped', 'color', 'bar', 'multi', 'tag']);


  var percent = __WEBPACK_IMPORTED_MODULE_7_lodash_tonumber___default()(value) / __WEBPACK_IMPORTED_MODULE_7_lodash_tonumber___default()(max) * 100;

  var progressClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'progress'), cssModule);

  var progressBarClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('progress-bar', bar ? className || barClassName : barClassName, animated ? 'progress-bar-animated' : null, color ? 'bg-' + color : null, striped || animated ? 'progress-bar-striped' : null), cssModule);

  var ProgressBar = multi ? children : __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('div', {
    className: progressBarClasses,
    style: { width: percent + '%' },
    role: 'progressbar',
    'aria-valuenow': value,
    'aria-valuemin': '0',
    'aria-valuemax': max,
    children: children
  });

  if (bar) {
    return ProgressBar;
  }

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: progressClasses, children: ProgressBar }));
};

Progress.propTypes = propTypes$40;
Progress.defaultProps = defaultProps$37;

var propTypes$42 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node.isRequired,
  node: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.any
};

var Portal = function (_React$Component) {
  inherits(Portal, _React$Component);

  function Portal() {
    classCallCheck(this, Portal);
    return possibleConstructorReturn(this, (Portal.__proto__ || Object.getPrototypeOf(Portal)).apply(this, arguments));
  }

  createClass(Portal, [{
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      if (this.defaultNode) {
        document.body.removeChild(this.defaultNode);
      }
      this.defaultNode = null;
    }
  }, {
    key: 'render',
    value: function render() {
      if (!canUseDOM) {
        return null;
      }

      if (!this.props.node && !this.defaultNode) {
        this.defaultNode = document.createElement('div');
        document.body.appendChild(this.defaultNode);
      }

      return __WEBPACK_IMPORTED_MODULE_5_react_dom___default.a.createPortal(this.props.children, this.props.node || this.defaultNode);
    }
  }]);
  return Portal;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

Portal.propTypes = propTypes$42;

function noop() {}

var FadePropTypes = __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.shape(Fade.propTypes);

var propTypes$41 = {
  isOpen: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  autoFocus: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  centered: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  size: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  toggle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  keyboard: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  role: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  labelledBy: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  backdrop: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['static'])]),
  onEnter: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  onExit: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  onOpened: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  onClosed: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  wrapClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  modalClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  backdropClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  contentClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  external: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  fade: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  zIndex: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  backdropTransition: FadePropTypes,
  modalTransition: FadePropTypes,
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func])
};

var propsToOmit = Object.keys(propTypes$41);

var defaultProps$38 = {
  isOpen: false,
  autoFocus: true,
  centered: false,
  role: 'dialog',
  backdrop: true,
  keyboard: true,
  zIndex: 1050,
  fade: true,
  onOpened: noop,
  onClosed: noop,
  modalTransition: {
    timeout: TransitionTimeouts.Modal
  },
  backdropTransition: {
    mountOnEnter: true,
    timeout: TransitionTimeouts.Fade // uses standard fade transition
  }
};

var Modal = function (_React$Component) {
  inherits(Modal, _React$Component);

  function Modal(props) {
    classCallCheck(this, Modal);

    var _this = possibleConstructorReturn(this, (Modal.__proto__ || Object.getPrototypeOf(Modal)).call(this, props));

    _this._element = null;
    _this._originalBodyPadding = null;
    _this.getFocusableChildren = _this.getFocusableChildren.bind(_this);
    _this.handleBackdropClick = _this.handleBackdropClick.bind(_this);
    _this.handleBackdropMouseDown = _this.handleBackdropMouseDown.bind(_this);
    _this.handleEscape = _this.handleEscape.bind(_this);
    _this.handleTab = _this.handleTab.bind(_this);
    _this.onOpened = _this.onOpened.bind(_this);
    _this.onClosed = _this.onClosed.bind(_this);

    _this.state = {
      isOpen: props.isOpen
    };

    if (props.isOpen) {
      _this.init();
    }
    return _this;
  }

  createClass(Modal, [{
    key: 'componentDidMount',
    value: function componentDidMount() {
      if (this.props.onEnter) {
        this.props.onEnter();
      }

      if (this.state.isOpen && this.props.autoFocus) {
        this.setFocus();
      }

      this._isMounted = true;
    }
  }, {
    key: 'componentWillReceiveProps',
    value: function componentWillReceiveProps(nextProps) {
      if (nextProps.isOpen && !this.props.isOpen) {
        this.setState({ isOpen: nextProps.isOpen });
      }
    }
  }, {
    key: 'componentWillUpdate',
    value: function componentWillUpdate(nextProps, nextState) {
      if (nextState.isOpen && !this.state.isOpen) {
        this.init();
      }
    }
  }, {
    key: 'componentDidUpdate',
    value: function componentDidUpdate(prevProps, prevState) {
      if (this.props.autoFocus && this.state.isOpen && !prevState.isOpen) {
        this.setFocus();
      }

      if (this._element && prevProps.zIndex !== this.props.zIndex) {
        this._element.style.zIndex = this.props.zIndex;
      }
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      if (this.props.onExit) {
        this.props.onExit();
      }

      if (this.state.isOpen) {
        this.destroy();
      }

      this._isMounted = false;
    }
  }, {
    key: 'onOpened',
    value: function onOpened(node, isAppearing) {
      this.props.onOpened();
      (this.props.modalTransition.onEntered || noop)(node, isAppearing);
    }
  }, {
    key: 'onClosed',
    value: function onClosed(node) {
      // so all methods get called before it is unmounted
      this.props.onClosed();
      (this.props.modalTransition.onExited || noop)(node);
      this.destroy();

      if (this._isMounted) {
        this.setState({ isOpen: false });
      }
    }
  }, {
    key: 'setFocus',
    value: function setFocus() {
      if (this._dialog && this._dialog.parentNode && typeof this._dialog.parentNode.focus === 'function') {
        this._dialog.parentNode.focus();
      }
    }
  }, {
    key: 'getFocusableChildren',
    value: function getFocusableChildren() {
      return this._element.querySelectorAll(focusableElements.join(', '));
    }
  }, {
    key: 'getFocusedChild',
    value: function getFocusedChild() {
      var currentFocus = void 0;
      var focusableChildren = this.getFocusableChildren();

      try {
        currentFocus = document.activeElement;
      } catch (err) {
        currentFocus = focusableChildren[0];
      }
      return currentFocus;
    }

    // not mouseUp because scrollbar fires it, shouldn't close when user scrolls

  }, {
    key: 'handleBackdropClick',
    value: function handleBackdropClick(e) {
      if (e.target === this._mouseDownElement) {
        e.stopPropagation();
        if (!this.props.isOpen || this.props.backdrop !== true) return;

        var container = this._dialog;

        if (e.target && !container.contains(e.target) && this.props.toggle) {
          this.props.toggle(e);
        }
      }
    }
  }, {
    key: 'handleTab',
    value: function handleTab(e) {
      if (e.which !== 9) return;

      var focusableChildren = this.getFocusableChildren();
      var totalFocusable = focusableChildren.length;
      var currentFocus = this.getFocusedChild();

      var focusedIndex = 0;

      for (var i = 0; i < totalFocusable; i += 1) {
        if (focusableChildren[i] === currentFocus) {
          focusedIndex = i;
          break;
        }
      }

      if (e.shiftKey && focusedIndex === 0) {
        e.preventDefault();
        focusableChildren[totalFocusable - 1].focus();
      } else if (!e.shiftKey && focusedIndex === totalFocusable - 1) {
        e.preventDefault();
        focusableChildren[0].focus();
      }
    }
  }, {
    key: 'handleBackdropMouseDown',
    value: function handleBackdropMouseDown(e) {
      this._mouseDownElement = e.target;
    }
  }, {
    key: 'handleEscape',
    value: function handleEscape(e) {
      if (this.props.isOpen && this.props.keyboard && e.keyCode === 27 && this.props.toggle) {
        this.props.toggle(e);
      }
    }
  }, {
    key: 'init',
    value: function init() {
      try {
        this._triggeringElement = document.activeElement;
      } catch (err) {
        this._triggeringElement = null;
      }
      this._element = document.createElement('div');
      this._element.setAttribute('tabindex', '-1');
      this._element.style.position = 'relative';
      this._element.style.zIndex = this.props.zIndex;
      this._originalBodyPadding = getOriginalBodyPadding();

      conditionallyUpdateScrollbar();

      document.body.appendChild(this._element);
      if (Modal.openCount === 0) {
        document.body.className = __WEBPACK_IMPORTED_MODULE_2_classnames___default()(document.body.className, mapToCssModules('modal-open', this.props.cssModule));
      }
      Modal.openCount += 1;
    }
  }, {
    key: 'destroy',
    value: function destroy() {
      if (this._element) {
        document.body.removeChild(this._element);
        this._element = null;
      }

      if (this._triggeringElement) {
        if (this._triggeringElement.focus) this._triggeringElement.focus();
        this._triggeringElement = null;
      }

      if (Modal.openCount <= 1) {
        var modalOpenClassName = mapToCssModules('modal-open', this.props.cssModule);
        // Use regex to prevent matching `modal-open` as part of a different class, e.g. `my-modal-opened`
        var modalOpenClassNameRegex = new RegExp('(^| )' + modalOpenClassName + '( |$)');
        document.body.className = document.body.className.replace(modalOpenClassNameRegex, ' ').trim();
      }
      Modal.openCount -= 1;

      setScrollbarWidth(this._originalBodyPadding);
    }
  }, {
    key: 'renderModalDialog',
    value: function renderModalDialog() {
      var _classNames,
          _this2 = this;

      var attributes = omit(this.props, propsToOmit);
      var dialogBaseClass = 'modal-dialog';

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        'div',
        _extends({}, attributes, {
          className: mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(dialogBaseClass, this.props.className, (_classNames = {}, defineProperty(_classNames, 'modal-' + this.props.size, this.props.size), defineProperty(_classNames, dialogBaseClass + '-centered', this.props.centered), _classNames)), this.props.cssModule),
          role: 'document',
          ref: function ref(c) {
            _this2._dialog = c;
          }
        }),
        __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
          'div',
          {
            className: mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('modal-content', this.props.contentClassName), this.props.cssModule)
          },
          this.props.children
        )
      );
    }
  }, {
    key: 'render',
    value: function render() {
      if (this.state.isOpen) {
        var _props = this.props,
            wrapClassName = _props.wrapClassName,
            modalClassName = _props.modalClassName,
            backdropClassName = _props.backdropClassName,
            cssModule = _props.cssModule,
            isOpen = _props.isOpen,
            backdrop = _props.backdrop,
            role = _props.role,
            labelledBy = _props.labelledBy,
            external = _props.external,
            innerRef = _props.innerRef;


        var modalAttributes = {
          onClick: this.handleBackdropClick,
          onMouseDown: this.handleBackdropMouseDown,
          onKeyUp: this.handleEscape,
          onKeyDown: this.handleTab,
          style: { display: 'block' },
          'aria-labelledby': labelledBy,
          role: role,
          tabIndex: '-1'
        };

        var hasTransition = this.props.fade;
        var modalTransition = _extends({}, Fade.defaultProps, this.props.modalTransition, {
          baseClass: hasTransition ? this.props.modalTransition.baseClass : '',
          timeout: hasTransition ? this.props.modalTransition.timeout : 0
        });
        var backdropTransition = _extends({}, Fade.defaultProps, this.props.backdropTransition, {
          baseClass: hasTransition ? this.props.backdropTransition.baseClass : '',
          timeout: hasTransition ? this.props.backdropTransition.timeout : 0
        });

        var Backdrop = hasTransition ? __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Fade, _extends({}, backdropTransition, {
          'in': isOpen && !!backdrop,
          cssModule: cssModule,
          className: mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('modal-backdrop', backdropClassName), cssModule)
        })) : __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('div', { className: mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('modal-backdrop', 'show', backdropClassName), cssModule) });

        return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
          Portal,
          { node: this._element },
          __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
            'div',
            { className: mapToCssModules(wrapClassName) },
            __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
              Fade,
              _extends({}, modalAttributes, modalTransition, {
                'in': isOpen,
                onEntered: this.onOpened,
                onExited: this.onClosed,
                cssModule: cssModule,
                className: mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('modal', modalClassName), cssModule),
                innerRef: innerRef
              }),
              external,
              this.renderModalDialog()
            ),
            Backdrop
          )
        );
      }

      return null;
    }
  }]);
  return Modal;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

Modal.propTypes = propTypes$41;
Modal.defaultProps = defaultProps$38;
Modal.openCount = 0;

var propTypes$43 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  wrapTag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  toggle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  closeAriaLabel: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  charCode: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number]),
  close: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$39 = {
  tag: 'h5',
  wrapTag: 'div',
  closeAriaLabel: 'Close',
  charCode: 215
};

var ModalHeader = function ModalHeader(props) {
  var closeButton = void 0;
  var className = props.className,
      cssModule = props.cssModule,
      children = props.children,
      toggle = props.toggle,
      Tag = props.tag,
      WrapTag = props.wrapTag,
      closeAriaLabel = props.closeAriaLabel,
      charCode = props.charCode,
      close = props.close,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'children', 'toggle', 'tag', 'wrapTag', 'closeAriaLabel', 'charCode', 'close']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'modal-header'), cssModule);

  if (!close && toggle) {
    var closeIcon = typeof charCode === 'number' ? String.fromCharCode(charCode) : charCode;
    closeButton = __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      'button',
      { type: 'button', onClick: toggle, className: mapToCssModules('close', cssModule), 'aria-label': closeAriaLabel },
      __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        'span',
        { 'aria-hidden': 'true' },
        closeIcon
      )
    );
  }

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    WrapTag,
    _extends({}, attributes, { className: classes }),
    __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      Tag,
      { className: mapToCssModules('modal-title', cssModule) },
      children
    ),
    close || closeButton
  );
};

ModalHeader.propTypes = propTypes$43;
ModalHeader.defaultProps = defaultProps$39;

var propTypes$44 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$40 = {
  tag: 'div'
};

var ModalBody = function ModalBody(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'modal-body'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

ModalBody.propTypes = propTypes$44;
ModalBody.defaultProps = defaultProps$40;

var propTypes$45 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$41 = {
  tag: 'div'
};

var ModalFooter = function ModalFooter(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'modal-footer'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

ModalFooter.propTypes = propTypes$45;
ModalFooter.defaultProps = defaultProps$41;

var propTypes$46 = {
  placement: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(PopperPlacements),
  target: targetPropType.isRequired,
  container: targetPropType,
  isOpen: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  disabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  hideArrow: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  boundariesElement: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, DOMElement]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  innerClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  arrowClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  toggle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  autohide: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  placementPrefix: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  delay: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.shape({ show: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number, hide: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number }), __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number]),
  modifiers: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  offset: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number]),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object]),
  trigger: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

var DEFAULT_DELAYS$1 = {
  show: 0,
  hide: 250
};

var defaultProps$42 = {
  isOpen: false,
  hideArrow: false,
  placement: 'top',
  placementPrefix: 'bs-tooltip',
  delay: DEFAULT_DELAYS$1,
  autohide: true,
  toggle: function toggle() {}
};

var Tooltip = function (_React$Component) {
  inherits(Tooltip, _React$Component);

  function Tooltip(props) {
    classCallCheck(this, Tooltip);

    var _this = possibleConstructorReturn(this, (Tooltip.__proto__ || Object.getPrototypeOf(Tooltip)).call(this, props));

    _this._target = null;
    _this.addTargetEvents = _this.addTargetEvents.bind(_this);
    _this.handleDocumentClick = _this.handleDocumentClick.bind(_this);
    _this.removeTargetEvents = _this.removeTargetEvents.bind(_this);
    _this.toggle = _this.toggle.bind(_this);
    _this.onMouseOverTooltip = _this.onMouseOverTooltip.bind(_this);
    _this.onMouseLeaveTooltip = _this.onMouseLeaveTooltip.bind(_this);
    _this.onMouseOverTooltipContent = _this.onMouseOverTooltipContent.bind(_this);
    _this.onMouseLeaveTooltipContent = _this.onMouseLeaveTooltipContent.bind(_this);
    _this.show = _this.show.bind(_this);
    _this.hide = _this.hide.bind(_this);
    _this.onEscKeyDown = _this.onEscKeyDown.bind(_this);
    return _this;
  }

  createClass(Tooltip, [{
    key: 'componentDidMount',
    value: function componentDidMount() {
      this._target = getTarget(this.props.target);
      this.addTargetEvents();
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      this.clearHideTimeout();
      this.clearShowTimeout();
      this.removeTargetEvents();
    }
  }, {
    key: 'onMouseOverTooltip',
    value: function onMouseOverTooltip(e) {
      if (this._hideTimeout) {
        this.clearHideTimeout();
      }
      this._showTimeout = setTimeout(this.show.bind(this, e), this.getDelay('show'));
    }
  }, {
    key: 'onMouseLeaveTooltip',
    value: function onMouseLeaveTooltip(e) {
      if (this._showTimeout) {
        this.clearShowTimeout();
      }
      this._hideTimeout = setTimeout(this.hide.bind(this, e), this.getDelay('hide'));
    }
  }, {
    key: 'onMouseOverTooltipContent',
    value: function onMouseOverTooltipContent() {
      if (this.props.autohide) {
        return;
      }
      if (this._hideTimeout) {
        this.clearHideTimeout();
      }
    }
  }, {
    key: 'onMouseLeaveTooltipContent',
    value: function onMouseLeaveTooltipContent(e) {
      if (this.props.autohide) {
        return;
      }
      if (this._showTimeout) {
        this.clearShowTimeout();
      }
      e.persist();
      this._hideTimeout = setTimeout(this.hide.bind(this, e), this.getDelay('hide'));
    }
  }, {
    key: 'onEscKeyDown',
    value: function onEscKeyDown(e) {
      if (e.key === 'Escape') {
        this.hide(e);
      }
    }
  }, {
    key: 'getDelay',
    value: function getDelay(key) {
      var delay = this.props.delay;

      if ((typeof delay === 'undefined' ? 'undefined' : _typeof(delay)) === 'object') {
        return isNaN(delay[key]) ? DEFAULT_DELAYS$1[key] : delay[key];
      }
      return delay;
    }
  }, {
    key: 'show',
    value: function show(e) {
      if (!this.props.isOpen) {
        this.clearShowTimeout();
        this.toggle(e);
      }
    }
  }, {
    key: 'hide',
    value: function hide(e) {
      if (this.props.isOpen) {
        this.clearHideTimeout();
        this.toggle(e);
      }
    }
  }, {
    key: 'clearShowTimeout',
    value: function clearShowTimeout() {
      clearTimeout(this._showTimeout);
      this._showTimeout = undefined;
    }
  }, {
    key: 'clearHideTimeout',
    value: function clearHideTimeout() {
      clearTimeout(this._hideTimeout);
      this._hideTimeout = undefined;
    }
  }, {
    key: 'handleDocumentClick',
    value: function handleDocumentClick(e) {
      if (this._target !== null && (e.target === this._target || this._target.contains(e.target))) {
        if (this._hideTimeout) {
          this.clearHideTimeout();
        }

        if (!this.props.isOpen) {
          this.toggle(e);
        }
      } else if (this.props.isOpen && e.target.getAttribute('role') !== 'tooltip') {
        if (this._showTimeout) {
          this.clearShowTimeout();
        }
        this._hideTimeout = setTimeout(this.hide.bind(this, e), this.getDelay('hide'));
      }
    }
  }, {
    key: 'addTargetEvents',
    value: function addTargetEvents() {
      var _this2 = this;

      if (this.props.trigger) {
        var triggers = this.props.trigger.split(' ');
        if (triggers.indexOf('manual') === -1) {
          if (triggers.indexOf('click') > -1) {
            ['click', 'touchstart'].forEach(function (event) {
              return document.addEventListener(event, _this2.handleDocumentClick, true);
            });
          }
          if (this._target !== null) {
            if (triggers.indexOf('hover') > -1) {
              this._target.addEventListener('mouseover', this.onMouseOverTooltip, true);
              this._target.addEventListener('mouseout', this.onMouseLeaveTooltip, true);
            }
            if (triggers.indexOf('focus') > -1) {
              this._target.addEventListener('focusin', this.show, true);
              this._target.addEventListener('focusout', this.hide, true);
            }
            this._target.addEventListener('keydown', this.onEscKeyDown, true);
          }
        }
      } else {
        if (this._target !== null) {
          this._target.addEventListener('mouseover', this.onMouseOverTooltip, true);
          this._target.addEventListener('mouseout', this.onMouseLeaveTooltip, true);
          this._target.addEventListener('keydown', this.onEscKeyDown, true);
          this._target.addEventListener('focusin', this.show, true);
          this._target.addEventListener('focusout', this.hide, true);
        }
        ['click', 'touchstart'].forEach(function (event) {
          return document.addEventListener(event, _this2.handleDocumentClick, true);
        });
      }
    }
  }, {
    key: 'removeTargetEvents',
    value: function removeTargetEvents() {
      var _this3 = this;

      if (this._target !== null) {
        this._target.removeEventListener('mouseover', this.onMouseOverTooltip, true);
        this._target.removeEventListener('mouseout', this.onMouseLeaveTooltip, true);
        this._target.addEventListener('keydown', this.onEscKeyDown, true);
        this._target.addEventListener('focusin', this.show, true);
        this._target.addEventListener('focusout', this.hide, true);
      }
      ['click', 'touchstart'].forEach(function (event) {
        return document.removeEventListener(event, _this3.handleDocumentClick, true);
      });
    }
  }, {
    key: 'toggle',
    value: function toggle(e) {
      if (this.props.disabled) {
        return e && e.preventDefault();
      }

      return this.props.toggle(e);
    }
  }, {
    key: 'render',
    value: function render() {
      if (!this.props.isOpen) {
        return null;
      }

      var attributes = omit(this.props, Object.keys(propTypes$46));
      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('tooltip-inner', this.props.innerClassName), this.props.cssModule);

      var popperClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('tooltip', 'show', this.props.className), this.props.cssModule);

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        PopperContent,
        {
          className: popperClasses,
          target: this.props.target,
          isOpen: this.props.isOpen,
          hideArrow: this.props.hideArrow,
          boundariesElement: this.props.boundariesElement,
          placement: this.props.placement,
          placementPrefix: this.props.placementPrefix,
          arrowClassName: this.props.arrowClassName,
          container: this.props.container,
          modifiers: this.props.modifiers,
          offset: this.props.offset,
          cssModule: this.props.cssModule
        },
        __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement('div', _extends({}, attributes, {
          ref: this.props.innerRef,
          className: classes,
          role: 'tooltip',
          'aria-hidden': this.props.isOpen,
          onMouseOver: this.onMouseOverTooltipContent,
          onMouseLeave: this.onMouseLeaveTooltipContent,
          onKeyDown: this.onEscKeyDown
        }))
      );
    }
  }]);
  return Tooltip;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

Tooltip.propTypes = propTypes$46;
Tooltip.defaultProps = defaultProps$42;

var propTypes$47 = {
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  size: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  bordered: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  borderless: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  striped: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  inverse: deprecated(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, 'Please use the prop "dark"'),
  dark: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  hover: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  responsive: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  responsiveTag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string])
};

var defaultProps$43 = {
  tag: 'table',
  responsiveTag: 'div'
};

var Table = function Table(props) {
  var className = props.className,
      cssModule = props.cssModule,
      size = props.size,
      bordered = props.bordered,
      borderless = props.borderless,
      striped = props.striped,
      inverse = props.inverse,
      dark = props.dark,
      hover = props.hover,
      responsive = props.responsive,
      Tag = props.tag,
      ResponsiveTag = props.responsiveTag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'size', 'bordered', 'borderless', 'striped', 'inverse', 'dark', 'hover', 'responsive', 'tag', 'responsiveTag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'table', size ? 'table-' + size : false, bordered ? 'table-bordered' : false, borderless ? 'table-borderless' : false, striped ? 'table-striped' : false, dark || inverse ? 'table-dark' : false, hover ? 'table-hover' : false), cssModule);

  var table = __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));

  if (responsive) {
    var responsiveClassName = responsive === true ? 'table-responsive' : 'table-responsive-' + responsive;

    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      ResponsiveTag,
      { className: responsiveClassName },
      table
    );
  }

  return table;
};

Table.propTypes = propTypes$47;
Table.defaultProps = defaultProps$43;

var propTypes$48 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  flush: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$44 = {
  tag: 'ul'
};

var ListGroup = function ListGroup(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      flush = props.flush,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag', 'flush']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'list-group', flush ? 'list-group-flush' : false), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

ListGroup.propTypes = propTypes$48;
ListGroup.defaultProps = defaultProps$44;

var propTypes$49 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  inline: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$45 = {
  tag: 'form'
};

var Form = function (_Component) {
  inherits(Form, _Component);

  function Form(props) {
    classCallCheck(this, Form);

    var _this = possibleConstructorReturn(this, (Form.__proto__ || Object.getPrototypeOf(Form)).call(this, props));

    _this.getRef = _this.getRef.bind(_this);
    _this.submit = _this.submit.bind(_this);
    return _this;
  }

  createClass(Form, [{
    key: 'getRef',
    value: function getRef(ref) {
      if (this.props.innerRef) {
        this.props.innerRef(ref);
      }
      this.ref = ref;
    }
  }, {
    key: 'submit',
    value: function submit() {
      if (this.ref) {
        this.ref.submit();
      }
    }
  }, {
    key: 'render',
    value: function render() {
      var _props = this.props,
          className = _props.className,
          cssModule = _props.cssModule,
          inline = _props.inline,
          Tag = _props.tag,
          innerRef = _props.innerRef,
          attributes = objectWithoutProperties(_props, ['className', 'cssModule', 'inline', 'tag', 'innerRef']);


      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, inline ? 'form-inline' : false), cssModule);

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { ref: innerRef, className: classes }));
    }
  }]);
  return Form;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);

Form.propTypes = propTypes$49;
Form.defaultProps = defaultProps$45;

var propTypes$50 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  valid: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tooltip: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool
};

var defaultProps$46 = {
  tag: 'div',
  valid: undefined
};

var FormFeedback = function FormFeedback(props) {
  var className = props.className,
      cssModule = props.cssModule,
      valid = props.valid,
      tooltip = props.tooltip,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'valid', 'tooltip', 'tag']);


  var validMode = tooltip ? 'tooltip' : 'feedback';

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, valid ? 'valid-' + validMode : 'invalid-' + validMode), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

FormFeedback.propTypes = propTypes$50;
FormFeedback.defaultProps = defaultProps$46;

var propTypes$51 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  row: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  check: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  inline: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  disabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$47 = {
  tag: 'div'
};

var FormGroup = function FormGroup(props) {
  var className = props.className,
      cssModule = props.cssModule,
      row = props.row,
      disabled = props.disabled,
      check = props.check,
      inline = props.inline,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'row', 'disabled', 'check', 'inline', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'position-relative', row ? 'row' : false, check ? 'form-check' : 'form-group', check && inline ? 'form-check-inline' : false, check && disabled ? 'disabled' : false), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

FormGroup.propTypes = propTypes$51;
FormGroup.defaultProps = defaultProps$47;

var propTypes$52 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  inline: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  color: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$48 = {
  tag: 'small',
  color: 'muted'
};

var FormText = function FormText(props) {
  var className = props.className,
      cssModule = props.cssModule,
      inline = props.inline,
      color = props.color,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'inline', 'color', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, !inline ? 'form-text' : false, color ? 'text-' + color : false), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

FormText.propTypes = propTypes$52;
FormText.defaultProps = defaultProps$48;

/* eslint react/prefer-stateless-function: 0 */

var propTypes$53 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  type: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  size: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  bsSize: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  state: deprecated(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, 'Please use the props "valid" and "invalid" to indicate the state.'),
  valid: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  invalid: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  static: deprecated(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool, 'Please use the prop "plaintext"'),
  plaintext: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  addon: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$49 = {
  type: 'text'
};

var Input = function (_React$Component) {
  inherits(Input, _React$Component);

  function Input(props) {
    classCallCheck(this, Input);

    var _this = possibleConstructorReturn(this, (Input.__proto__ || Object.getPrototypeOf(Input)).call(this, props));

    _this.getRef = _this.getRef.bind(_this);
    _this.focus = _this.focus.bind(_this);
    return _this;
  }

  createClass(Input, [{
    key: 'getRef',
    value: function getRef(ref) {
      if (this.props.innerRef) {
        this.props.innerRef(ref);
      }
      this.ref = ref;
    }
  }, {
    key: 'focus',
    value: function focus() {
      if (this.ref) {
        this.ref.focus();
      }
    }
  }, {
    key: 'render',
    value: function render() {
      var _props = this.props,
          className = _props.className,
          cssModule = _props.cssModule,
          type = _props.type,
          bsSize = _props.bsSize,
          state = _props.state,
          valid = _props.valid,
          invalid = _props.invalid,
          tag = _props.tag,
          addon = _props.addon,
          staticInput = _props.static,
          plaintext = _props.plaintext,
          innerRef = _props.innerRef,
          attributes = objectWithoutProperties(_props, ['className', 'cssModule', 'type', 'bsSize', 'state', 'valid', 'invalid', 'tag', 'addon', 'static', 'plaintext', 'innerRef']);


      var checkInput = ['radio', 'checkbox'].indexOf(type) > -1;
      var isNotaNumber = new RegExp('\\D', 'g');

      var fileInput = type === 'file';
      var textareaInput = type === 'textarea';
      var selectInput = type === 'select';
      var Tag = tag || (selectInput || textareaInput ? type : 'input');

      var formControlClass = 'form-control';

      if (plaintext || staticInput) {
        formControlClass = formControlClass + '-plaintext';
        Tag = tag || 'p';
      } else if (fileInput) {
        formControlClass = formControlClass + '-file';
      } else if (checkInput) {
        if (addon) {
          formControlClass = null;
        } else {
          formControlClass = 'form-check-input';
        }
      }

      if (state && typeof valid === 'undefined' && typeof invalid === 'undefined') {
        if (state === 'danger') {
          invalid = true;
        } else if (state === 'success') {
          valid = true;
        }
      }

      if (attributes.size && isNotaNumber.test(attributes.size)) {
        warnOnce('Please use the prop "bsSize" instead of the "size" to bootstrap\'s input sizing.');
        bsSize = attributes.size;
        delete attributes.size;
      }

      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, invalid && 'is-invalid', valid && 'is-valid', bsSize ? 'form-control-' + bsSize : false, formControlClass), cssModule);

      if (Tag === 'input' || tag && typeof tag === 'function') {
        attributes.type = type;
      }

      if (attributes.children && !(plaintext || staticInput || type === 'select' || typeof Tag !== 'string' || Tag === 'select')) {
        warnOnce('Input with a type of "' + type + '" cannot have children. Please use "value"/"defaultValue" instead.');
        delete attributes.children;
      }

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { ref: innerRef, className: classes }));
    }
  }]);
  return Input;
}(__WEBPACK_IMPORTED_MODULE_0_react___default.a.Component);

Input.propTypes = propTypes$53;
Input.defaultProps = defaultProps$49;

var propTypes$54 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  size: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$50 = {
  tag: 'div'
};

var InputGroup = function InputGroup(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      size = props.size,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag', 'size']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'input-group', size ? 'input-group-' + size : null), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

InputGroup.propTypes = propTypes$54;
InputGroup.defaultProps = defaultProps$50;

var propTypes$56 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$52 = {
  tag: 'span'
};

var InputGroupText = function InputGroupText(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'input-group-text'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

InputGroupText.propTypes = propTypes$56;
InputGroupText.defaultProps = defaultProps$52;

var propTypes$55 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  addonType: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['prepend', 'append']).isRequired,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$51 = {
  tag: 'div'
};

var InputGroupAddon = function InputGroupAddon(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      addonType = props.addonType,
      children = props.children,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag', 'addonType', 'children']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'input-group-' + addonType), cssModule);

  // Convenience to assist with transition
  if (typeof children === 'string') {
    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      Tag,
      _extends({}, attributes, { className: classes }),
      __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(InputGroupText, { children: children })
    );
  }

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes, children: children }));
};

InputGroupAddon.propTypes = propTypes$55;
InputGroupAddon.defaultProps = defaultProps$51;

var propTypes$57 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  addonType: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['prepend', 'append']).isRequired,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  groupClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  groupAttributes: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var InputGroupButton = function InputGroupButton(props) {
  warnOnce('The "InputGroupButton" component has been deprecated.\nPlease use component "InputGroupAddon".');

  var children = props.children,
      groupClassName = props.groupClassName,
      groupAttributes = props.groupAttributes,
      propsWithoutGroup = objectWithoutProperties(props, ['children', 'groupClassName', 'groupAttributes']);


  if (typeof children === 'string') {
    var cssModule = propsWithoutGroup.cssModule,
        tag = propsWithoutGroup.tag,
        addonType = propsWithoutGroup.addonType,
        attributes = objectWithoutProperties(propsWithoutGroup, ['cssModule', 'tag', 'addonType']);


    var allGroupAttributes = _extends({}, groupAttributes, {
      cssModule: cssModule,
      tag: tag,
      addonType: addonType
    });

    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      InputGroupAddon,
      _extends({}, allGroupAttributes, { className: groupClassName }),
      __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Button, _extends({}, attributes, { children: children }))
    );
  }

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(InputGroupAddon, _extends({}, props, { children: children }));
};

InputGroupButton.propTypes = propTypes$57;

var propTypes$58 = {
  addonType: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(['prepend', 'append']).isRequired,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node
};

var InputGroupButtonDropdown = function InputGroupButtonDropdown(props) {
  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Dropdown, props);
};

InputGroupButtonDropdown.propTypes = propTypes$58;

var colWidths$1 = ['xs', 'sm', 'md', 'lg', 'xl'];

var stringOrNumberProp$1 = __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]);

var columnProps$1 = __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.shape({
  size: stringOrNumberProp$1,
  push: deprecated(stringOrNumberProp$1, 'Please use the prop "order"'),
  pull: deprecated(stringOrNumberProp$1, 'Please use the prop "order"'),
  order: stringOrNumberProp$1,
  offset: stringOrNumberProp$1
})]);

var propTypes$59 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  hidden: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  check: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  size: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  for: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  xs: columnProps$1,
  sm: columnProps$1,
  md: columnProps$1,
  lg: columnProps$1,
  xl: columnProps$1,
  widths: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.array
};

var defaultProps$53 = {
  tag: 'label',
  widths: colWidths$1
};

var getColumnSizeClass$1 = function getColumnSizeClass(isXs, colWidth, colSize) {
  if (colSize === true || colSize === '') {
    return isXs ? 'col' : 'col-' + colWidth;
  } else if (colSize === 'auto') {
    return isXs ? 'col-auto' : 'col-' + colWidth + '-auto';
  }

  return isXs ? 'col-' + colSize : 'col-' + colWidth + '-' + colSize;
};

var Label = function Label(props) {
  var className = props.className,
      cssModule = props.cssModule,
      hidden = props.hidden,
      widths = props.widths,
      Tag = props.tag,
      check = props.check,
      size = props.size,
      htmlFor = props.for,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'hidden', 'widths', 'tag', 'check', 'size', 'for']);


  var colClasses = [];

  widths.forEach(function (colWidth, i) {
    var columnProp = props[colWidth];

    delete attributes[colWidth];

    if (!columnProp && columnProp !== '') {
      return;
    }

    var isXs = !i;
    var colClass = void 0;

    if (__WEBPACK_IMPORTED_MODULE_4_lodash_isobject___default()(columnProp)) {
      var _classNames;

      var colSizeInterfix = isXs ? '-' : '-' + colWidth + '-';
      colClass = getColumnSizeClass$1(isXs, colWidth, columnProp.size);

      colClasses.push(mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()((_classNames = {}, defineProperty(_classNames, colClass, columnProp.size || columnProp.size === ''), defineProperty(_classNames, 'order' + colSizeInterfix + columnProp.order, columnProp.order || columnProp.order === 0), defineProperty(_classNames, 'offset' + colSizeInterfix + columnProp.offset, columnProp.offset || columnProp.offset === 0), _classNames))), cssModule);
    } else {
      colClass = getColumnSizeClass$1(isXs, colWidth, columnProp);
      colClasses.push(colClass);
    }
  });

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, hidden ? 'sr-only' : false, check ? 'form-check-label' : false, size ? 'col-form-label-' + size : false, colClasses, colClasses.length ? 'col-form-label' : false), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({ htmlFor: htmlFor }, attributes, { className: classes }));
};

Label.propTypes = propTypes$59;
Label.defaultProps = defaultProps$53;

var propTypes$60 = {
  body: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  bottom: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  heading: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  left: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  list: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  middle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  object: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  right: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  top: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool
};

var Media = function Media(props) {
  var body = props.body,
      bottom = props.bottom,
      className = props.className,
      cssModule = props.cssModule,
      heading = props.heading,
      left = props.left,
      list = props.list,
      middle = props.middle,
      object = props.object,
      right = props.right,
      tag = props.tag,
      top = props.top,
      attributes = objectWithoutProperties(props, ['body', 'bottom', 'className', 'cssModule', 'heading', 'left', 'list', 'middle', 'object', 'right', 'tag', 'top']);


  var defaultTag = void 0;
  if (heading) {
    defaultTag = 'h4';
  } else if (attributes.href) {
    defaultTag = 'a';
  } else if (attributes.src || object) {
    defaultTag = 'img';
  } else if (list) {
    defaultTag = 'ul';
  } else {
    defaultTag = 'div';
  }
  var Tag = tag || defaultTag;

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, {
    'media-body': body,
    'media-heading': heading,
    'media-left': left,
    'media-right': right,
    'media-top': top,
    'media-bottom': bottom,
    'media-middle': middle,
    'media-object': object,
    'media-list': list,
    media: !body && !heading && !left && !right && !top && !bottom && !middle && !object && !list
  }), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

Media.propTypes = propTypes$60;

var propTypes$61 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  listClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  size: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  listTag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  'aria-label': __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

var defaultProps$54 = {
  tag: 'nav',
  listTag: 'ul',
  'aria-label': 'pagination'
};

var Pagination = function Pagination(props) {
  var className = props.className,
      listClassName = props.listClassName,
      cssModule = props.cssModule,
      size = props.size,
      Tag = props.tag,
      ListTag = props.listTag,
      label = props['aria-label'],
      attributes = objectWithoutProperties(props, ['className', 'listClassName', 'cssModule', 'size', 'tag', 'listTag', 'aria-label']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className), cssModule);

  var listClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(listClassName, 'pagination', defineProperty({}, 'pagination-' + size, !!size)), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    Tag,
    { className: classes, 'aria-label': label },
    __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(ListTag, _extends({}, attributes, { className: listClasses }))
  );
};

Pagination.propTypes = propTypes$61;
Pagination.defaultProps = defaultProps$54;

var propTypes$62 = {
  active: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  disabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string])
};

var defaultProps$55 = {
  tag: 'li'
};

var PaginationItem = function PaginationItem(props) {
  var active = props.active,
      className = props.className,
      cssModule = props.cssModule,
      disabled = props.disabled,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['active', 'className', 'cssModule', 'disabled', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'page-item', {
    active: active,
    disabled: disabled
  }), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

PaginationItem.propTypes = propTypes$62;
PaginationItem.defaultProps = defaultProps$55;

var propTypes$63 = {
  'aria-label': __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  next: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  previous: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string])
};

var defaultProps$56 = {
  tag: 'a'
};

var PaginationLink = function PaginationLink(props) {
  var className = props.className,
      cssModule = props.cssModule,
      next = props.next,
      previous = props.previous,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'next', 'previous', 'tag']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'page-link'), cssModule);

  var defaultAriaLabel = void 0;
  if (previous) {
    defaultAriaLabel = 'Previous';
  } else if (next) {
    defaultAriaLabel = 'Next';
  }
  var ariaLabel = props['aria-label'] || defaultAriaLabel;

  var defaultCaret = void 0;
  if (previous) {
    defaultCaret = '\xAB';
  } else if (next) {
    defaultCaret = '\xBB';
  }

  var children = props.children;
  if (children && Array.isArray(children) && children.length === 0) {
    children = null;
  }

  if (!attributes.href && Tag === 'a') {
    Tag = 'button';
  }

  if (previous || next) {
    children = [__WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      'span',
      {
        'aria-hidden': 'true',
        key: 'caret'
      },
      children || defaultCaret
    ), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      'span',
      {
        className: 'sr-only',
        key: 'sr'
      },
      ariaLabel
    )];
  }

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    Tag,
    _extends({}, attributes, {
      className: classes,
      'aria-label': ariaLabel
    }),
    children
  );
};

PaginationLink.propTypes = propTypes$63;
PaginationLink.defaultProps = defaultProps$56;

var propTypes$64 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  activeTab: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.any,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$57 = {
  tag: 'div'
};

var childContextTypes$2 = {
  activeTabId: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.any
};

var TabContent = function (_Component) {
  inherits(TabContent, _Component);
  createClass(TabContent, null, [{
    key: 'getDerivedStateFromProps',
    value: function getDerivedStateFromProps(nextProps, prevState) {
      if (prevState.activeTab !== nextProps.activeTab) {
        return {
          activeTab: nextProps.activeTab
        };
      }
      return null;
    }
  }]);

  function TabContent(props) {
    classCallCheck(this, TabContent);

    var _this = possibleConstructorReturn(this, (TabContent.__proto__ || Object.getPrototypeOf(TabContent)).call(this, props));

    _this.state = {
      activeTab: _this.props.activeTab
    };
    return _this;
  }

  createClass(TabContent, [{
    key: 'getChildContext',
    value: function getChildContext() {
      return {
        activeTabId: this.state.activeTab
      };
    }
  }, {
    key: 'render',
    value: function render() {
      var _props = this.props,
          className = _props.className,
          cssModule = _props.cssModule,
          Tag = _props.tag;


      var attributes = omit(this.props, Object.keys(propTypes$64));

      var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('tab-content', className), cssModule);

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
    }
  }]);
  return TabContent;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);

Object(__WEBPACK_IMPORTED_MODULE_8_react_lifecycles_compat__["a" /* polyfill */])(TabContent);
TabContent.propTypes = propTypes$64;
TabContent.defaultProps = defaultProps$57;
TabContent.childContextTypes = childContextTypes$2;

var propTypes$65 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  tabId: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.any
};

var defaultProps$58 = {
  tag: 'div'
};

var contextTypes$3 = {
  activeTabId: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.any
};

function TabPane(props, context) {
  var className = props.className,
      cssModule = props.cssModule,
      tabId = props.tabId,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tabId', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('tab-pane', className, { active: tabId === context.activeTabId }), cssModule);
  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
}
TabPane.propTypes = propTypes$65;
TabPane.defaultProps = defaultProps$58;
TabPane.contextTypes = contextTypes$3;

var propTypes$66 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  fluid: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$59 = {
  tag: 'div'
};

var Jumbotron = function Jumbotron(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      fluid = props.fluid,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag', 'fluid']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'jumbotron', fluid ? 'jumbotron-fluid' : false), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

Jumbotron.propTypes = propTypes$66;
Jumbotron.defaultProps = defaultProps$59;

var propTypes$67 = {
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  closeClassName: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  closeAriaLabel: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  color: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  fade: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  isOpen: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  toggle: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  transition: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.shape(Fade.propTypes),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func])
};

var defaultProps$60 = {
  color: 'success',
  isOpen: true,
  tag: 'div',
  closeAriaLabel: 'Close',
  fade: true,
  transition: _extends({}, Fade.defaultProps, {
    unmountOnExit: true
  })
};

function Alert(props) {
  var className = props.className,
      closeClassName = props.closeClassName,
      closeAriaLabel = props.closeAriaLabel,
      cssModule = props.cssModule,
      Tag = props.tag,
      color = props.color,
      isOpen = props.isOpen,
      toggle = props.toggle,
      children = props.children,
      transition = props.transition,
      fade = props.fade,
      innerRef = props.innerRef,
      attributes = objectWithoutProperties(props, ['className', 'closeClassName', 'closeAriaLabel', 'cssModule', 'tag', 'color', 'isOpen', 'toggle', 'children', 'transition', 'fade', 'innerRef']);


  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'alert', 'alert-' + color, { 'alert-dismissible': toggle }), cssModule);

  var closeClasses = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()('close', closeClassName), cssModule);

  var alertTransition = _extends({}, Fade.defaultProps, transition, {
    baseClass: fade ? transition.baseClass : '',
    timeout: fade ? transition.timeout : 0
  });

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
    Fade,
    _extends({}, attributes, alertTransition, { tag: Tag, className: classes, 'in': isOpen, role: 'alert', innerRef: innerRef }),
    toggle ? __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
      'button',
      { type: 'button', className: closeClasses, 'aria-label': closeAriaLabel, onClick: toggle },
      __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        'span',
        { 'aria-hidden': 'true' },
        '\xD7'
      )
    ) : null,
    children
  );
}

Alert.propTypes = propTypes$67;
Alert.defaultProps = defaultProps$60;

var _transitionStatusToCl;

var propTypes$68 = _extends({}, Transition.propTypes, {
  isOpen: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.arrayOf(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node), __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node]),
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node,
  navbar: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object])
});

var defaultProps$61 = _extends({}, Transition.defaultProps, {
  isOpen: false,
  appear: false,
  enter: true,
  exit: true,
  tag: 'div',
  timeout: TransitionTimeouts.Collapse
});

var transitionStatusToClassHash = (_transitionStatusToCl = {}, defineProperty(_transitionStatusToCl, TransitionStatuses.ENTERING, 'collapsing'), defineProperty(_transitionStatusToCl, TransitionStatuses.ENTERED, 'collapse show'), defineProperty(_transitionStatusToCl, TransitionStatuses.EXITING, 'collapsing'), defineProperty(_transitionStatusToCl, TransitionStatuses.EXITED, 'collapse'), _transitionStatusToCl);

function getTransitionClass(status) {
  return transitionStatusToClassHash[status] || 'collapse';
}

function getHeight(node) {
  return node.scrollHeight;
}

var Collapse = function (_Component) {
  inherits(Collapse, _Component);

  function Collapse(props) {
    classCallCheck(this, Collapse);

    var _this = possibleConstructorReturn(this, (Collapse.__proto__ || Object.getPrototypeOf(Collapse)).call(this, props));

    _this.state = {
      height: null
    };

    ['onEntering', 'onEntered', 'onExit', 'onExiting', 'onExited'].forEach(function (name) {
      _this[name] = _this[name].bind(_this);
    });
    return _this;
  }

  createClass(Collapse, [{
    key: 'onEntering',
    value: function onEntering(node, isAppearing) {
      this.setState({ height: getHeight(node) });
      this.props.onEntering(node, isAppearing);
    }
  }, {
    key: 'onEntered',
    value: function onEntered(node, isAppearing) {
      this.setState({ height: null });
      this.props.onEntered(node, isAppearing);
    }
  }, {
    key: 'onExit',
    value: function onExit(node) {
      this.setState({ height: getHeight(node) });
      this.props.onExit(node);
    }
  }, {
    key: 'onExiting',
    value: function onExiting(node) {
      // getting this variable triggers a reflow
      var _unused = node.offsetHeight; // eslint-disable-line no-unused-vars
      this.setState({ height: 0 });
      this.props.onExiting(node);
    }
  }, {
    key: 'onExited',
    value: function onExited(node) {
      this.setState({ height: null });
      this.props.onExited(node);
    }
  }, {
    key: 'render',
    value: function render() {
      var _this2 = this;

      var _props = this.props,
          Tag = _props.tag,
          isOpen = _props.isOpen,
          className = _props.className,
          navbar = _props.navbar,
          cssModule = _props.cssModule,
          children = _props.children,
          innerRef = _props.innerRef,
          otherProps = objectWithoutProperties(_props, ['tag', 'isOpen', 'className', 'navbar', 'cssModule', 'children', 'innerRef']);
      var height = this.state.height;

      // In NODE_ENV=production the Transition.propTypes are wrapped which results in an
      // empty object "{}". This is the result of the `react-transition-group` babel
      // configuration settings. Therefore, to ensure that production builds work without
      // error, we can either explicitly define keys or use the Transition.defaultProps.
      // Using the Transition.defaultProps excludes any required props. Thus, the best
      // solution is to explicitly define required props in our utilities and reference these.
      // This also gives us more flexibility in the future to remove the prop-types
      // dependency in distribution builds (Similar to how `react-transition-group` does).
      // Note: Without omitting the `react-transition-group` props, the resulting child
      // Tag component would inherit the Transition properties as attributes for the HTML
      // element which results in errors/warnings for non-valid attributes.

      var transitionProps = pick(otherProps, TransitionPropTypeKeys);
      var childProps = omit(otherProps, TransitionPropTypeKeys);
      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
        Transition,
        _extends({}, transitionProps, {
          'in': isOpen,
          onEntering: this.onEntering,
          onEntered: this.onEntered,
          onExit: this.onExit,
          onExiting: this.onExiting,
          onExited: this.onExited
        }),
        function (status) {
          var collapseClass = getTransitionClass(status);
          var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, collapseClass, navbar && 'navbar-collapse'), cssModule);
          var style = height === null ? null : { height: height };
          return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(
            Tag,
            _extends({}, childProps, {
              style: _extends({}, childProps.style, style),
              className: classes,
              ref: _this2.props.innerRef
            }),
            children
          );
        }
      );
    }
  }]);
  return Collapse;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);

Collapse.propTypes = propTypes$68;
Collapse.defaultProps = defaultProps$61;

var propTypes$69 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  active: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  disabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  color: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string,
  action: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.any,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$62 = {
  tag: 'li'
};

var handleDisabledOnClick = function handleDisabledOnClick(e) {
  e.preventDefault();
};

var ListGroupItem = function ListGroupItem(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      active = props.active,
      disabled = props.disabled,
      action = props.action,
      color = props.color,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag', 'active', 'disabled', 'action', 'color']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, active ? 'active' : false, disabled ? 'disabled' : false, action ? 'list-group-item-action' : false, color ? 'list-group-item-' + color : false, 'list-group-item'), cssModule);

  // Prevent click event when disabled.
  if (disabled) {
    attributes.onClick = handleDisabledOnClick;
  }
  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

ListGroupItem.propTypes = propTypes$69;
ListGroupItem.defaultProps = defaultProps$62;

var propTypes$70 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.any,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$63 = {
  tag: 'h5'
};

var ListGroupItemHeading = function ListGroupItemHeading(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'list-group-item-heading'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

ListGroupItemHeading.propTypes = propTypes$70;
ListGroupItemHeading.defaultProps = defaultProps$63;

var propTypes$71 = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string]),
  className: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.any,
  cssModule: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

var defaultProps$64 = {
  tag: 'p'
};

var ListGroupItemText = function ListGroupItemText(props) {
  var className = props.className,
      cssModule = props.cssModule,
      Tag = props.tag,
      attributes = objectWithoutProperties(props, ['className', 'cssModule', 'tag']);

  var classes = mapToCssModules(__WEBPACK_IMPORTED_MODULE_2_classnames___default()(className, 'list-group-item-text'), cssModule);

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tag, _extends({}, attributes, { className: classes }));
};

ListGroupItemText.propTypes = propTypes$71;
ListGroupItemText.defaultProps = defaultProps$64;

var UncontrolledAlert = function (_Component) {
  inherits(UncontrolledAlert, _Component);

  function UncontrolledAlert(props) {
    classCallCheck(this, UncontrolledAlert);

    var _this = possibleConstructorReturn(this, (UncontrolledAlert.__proto__ || Object.getPrototypeOf(UncontrolledAlert)).call(this, props));

    _this.state = { isOpen: true };
    _this.toggle = _this.toggle.bind(_this);
    return _this;
  }

  createClass(UncontrolledAlert, [{
    key: 'toggle',
    value: function toggle() {
      this.setState({ isOpen: !this.state.isOpen });
    }
  }, {
    key: 'render',
    value: function render() {
      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Alert, _extends({ isOpen: this.state.isOpen, toggle: this.toggle }, this.props));
    }
  }]);
  return UncontrolledAlert;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);

var UncontrolledButtonDropdown = function (_Component) {
  inherits(UncontrolledButtonDropdown, _Component);

  function UncontrolledButtonDropdown(props) {
    classCallCheck(this, UncontrolledButtonDropdown);

    var _this = possibleConstructorReturn(this, (UncontrolledButtonDropdown.__proto__ || Object.getPrototypeOf(UncontrolledButtonDropdown)).call(this, props));

    _this.state = { isOpen: false };
    _this.toggle = _this.toggle.bind(_this);
    return _this;
  }

  createClass(UncontrolledButtonDropdown, [{
    key: 'toggle',
    value: function toggle() {
      this.setState({ isOpen: !this.state.isOpen });
    }
  }, {
    key: 'render',
    value: function render() {
      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(ButtonDropdown, _extends({ isOpen: this.state.isOpen, toggle: this.toggle }, this.props));
    }
  }]);
  return UncontrolledButtonDropdown;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);

var propTypes$72 = {
  toggler: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string.isRequired,
  toggleEvents: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.arrayOf(__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string)
};

var defaultProps$65 = {
  toggleEvents: defaultToggleEvents
};

var UncontrolledCollapse = function (_Component) {
  inherits(UncontrolledCollapse, _Component);

  function UncontrolledCollapse(props) {
    classCallCheck(this, UncontrolledCollapse);

    var _this = possibleConstructorReturn(this, (UncontrolledCollapse.__proto__ || Object.getPrototypeOf(UncontrolledCollapse)).call(this, props));

    _this.togglers = null;
    _this.removeEventListeners = null;
    _this.toggle = _this.toggle.bind(_this);

    _this.state = {
      isOpen: false
    };
    return _this;
  }

  createClass(UncontrolledCollapse, [{
    key: 'componentDidMount',
    value: function componentDidMount() {
      this.togglers = findDOMElements(this.props.toggler);
      if (this.togglers.length) {
        this.removeEventListeners = addMultipleEventListeners(this.togglers, this.toggle, this.props.toggleEvents);
      }
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      if (this.togglers.length && this.removeEventListeners) {
        this.removeEventListeners();
      }
    }
  }, {
    key: 'toggle',
    value: function toggle(e) {
      this.setState(function (_ref) {
        var isOpen = _ref.isOpen;
        return { isOpen: !isOpen };
      });
      e.preventDefault();
    }
  }, {
    key: 'render',
    value: function render() {
      var _props = this.props,
          toggleEvents = _props.toggleEvents,
          rest = objectWithoutProperties(_props, ['toggleEvents']);

      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Collapse, _extends({ isOpen: this.state.isOpen }, rest));
    }
  }]);
  return UncontrolledCollapse;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);

UncontrolledCollapse.propTypes = propTypes$72;
UncontrolledCollapse.defaultProps = defaultProps$65;

var UncontrolledDropdown = function (_Component) {
  inherits(UncontrolledDropdown, _Component);

  function UncontrolledDropdown(props) {
    classCallCheck(this, UncontrolledDropdown);

    var _this = possibleConstructorReturn(this, (UncontrolledDropdown.__proto__ || Object.getPrototypeOf(UncontrolledDropdown)).call(this, props));

    _this.state = { isOpen: false };
    _this.toggle = _this.toggle.bind(_this);
    return _this;
  }

  createClass(UncontrolledDropdown, [{
    key: 'toggle',
    value: function toggle() {
      this.setState({ isOpen: !this.state.isOpen });
    }
  }, {
    key: 'render',
    value: function render() {
      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Dropdown, _extends({ isOpen: this.state.isOpen, toggle: this.toggle }, this.props));
    }
  }]);
  return UncontrolledDropdown;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);

var UncontrolledNavDropdown = function UncontrolledNavDropdown(props) {
  warnOnce('The "UncontrolledNavDropdown" component has been deprecated.\nPlease use component "UncontrolledDropdown" with nav prop.');

  return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(UncontrolledDropdown, _extends({ nav: true }, props));
};

var UncontrolledTooltip = function (_Component) {
  inherits(UncontrolledTooltip, _Component);

  function UncontrolledTooltip(props) {
    classCallCheck(this, UncontrolledTooltip);

    var _this = possibleConstructorReturn(this, (UncontrolledTooltip.__proto__ || Object.getPrototypeOf(UncontrolledTooltip)).call(this, props));

    _this.state = { isOpen: false };
    _this.toggle = _this.toggle.bind(_this);
    return _this;
  }

  createClass(UncontrolledTooltip, [{
    key: 'toggle',
    value: function toggle() {
      this.setState({ isOpen: !this.state.isOpen });
    }
  }, {
    key: 'render',
    value: function render() {
      return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(Tooltip, _extends({ isOpen: this.state.isOpen, toggle: this.toggle }, this.props));
    }
  }]);
  return UncontrolledTooltip;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);


//# sourceMappingURL=reactstrap.es.js.map

/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__(22)))

/***/ }),
/* 8 */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),
/* 9 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__components_form_MenuItemEditFormModule__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__MenuItemType__ = __webpack_require__(10);
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */



class DefaultMenuItemType extends __WEBPACK_IMPORTED_MODULE_2__MenuItemType__["a" /* default */] {
  /**
     * @inheritDoc
     */
  getContextualMenu(treeView, node, item) {
    const items = {};
    items['editItem'] = {
      label: Translator.trans('menu_item.action.edit'),
      action: treeView.handleEditTreeNode,
      separator_after: true
    };
    return Object.assign(items, super.getContextualMenu(treeView, node, item) || {});
  }
  /**
     * @inheritDoc
     * @returns {{action: action, label: *}}
     */


  getContextualMenuCreateBtn(treeView, parentNode) {
    const getNewItem = this.getNewItem.bind(this);
    return {
      label: Translator.trans("menu_item.action.create.".concat(this.identifier)),
      action: () => {
        const item = getNewItem({
          parentId: parentNode.id
        });
        treeView.handleCreateTreeNode(item);
      }
    };
  }
  /**
     * @param item
     * @param onSubmit
     * @param onCancel
     * @returns {React.CElement<Readonly<{children?: React.ReactNode}> & Readonly<P>, MenuItemEditFormModule>}
     */


  getEditForm(item, onSubmit, onCancel) {
    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_1__components_form_MenuItemEditFormModule__["a" /* default */], {
      item: item,
      onSubmit: onSubmit,
      onCancel: onCancel
    });
  }

}
/* harmony export (immutable) */ __webpack_exports__["a"] = DefaultMenuItemType;


/***/ }),
/* 10 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__entity_MenuItem__ = __webpack_require__(2);
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */

class MenuItemType {
  /**
     * @returns {{max_depth: number, icon: string, max_children: number, valid_children: number}}
     */
  getTreeType() {
    return {
      icon: 'oi oi-link-intact',
      max_children: -1,
      max_depth: -1,
      valid_children: -1
    };
  }

  constructor(identifier, type) {
    this.identifier = identifier;
    this.type = type;
  }
  /**
     * @param {MenuTreeView} treeView
     * @param node
     * @param {MenuItem} item
     * @returns {{label: string, action:function}}
     */


  getContextualMenu(treeView, node, item) {
    const parent = treeView.getNode(node.parent);
    const isActive = item.getOption('active', true);
    const items = {};

    if (isActive) {
      items['desactivateItem'] = {
        label: Translator.trans('menu_item.action.desactivate'),
        action: event => {
          const selectedNode = treeView.getNode(event.reference);
          selectedNode.data.options['active'] = false;
          treeView.onTreeChange();
        }
      };
    } else {
      items['activateItem'] = {
        label: Translator.trans('menu_item.action.activate'),
        action: event => {
          const selectedNode = treeView.getNode(event.reference);
          selectedNode.data.options['active'] = true;
          treeView.onTreeChange();
        }
      };
    }

    if (node.state.disabled && !parent.state.disabled) {
      items['restoreItem'] = {
        label: Translator.trans('menu_item.action.restore'),
        action: event => {
          treeView.enableTree(event.reference);
          treeView.onTreeChange();
        }
      };
    } else {
      items['removeItem'] = {
        label: Translator.trans('menu_item.action.remove'),
        action: event => {
          treeView.disableTree(event.reference);
          treeView.onTreeChange();
        }
      };
    }

    return items;
  }
  /**
     * @param {MenuTreeView} treeView
     * @param parentNode
     * @returns {null}
     */


  getContextualMenuCreateBtn(treeView, parentNode) {
    return null;
  }
  /**
     * @param props
     * @returns {MenuItem}
     */


  getNewItem(props) {
    const defaultProps = {
      id: null,
      name: Translator.trans('menu_item.default_title'),
      type: this.type
    };
    props = Object.assign(defaultProps, props || {});
    return new __WEBPACK_IMPORTED_MODULE_0__entity_MenuItem__["a" /* default */](props);
  }

}
/* harmony export (immutable) */ __webpack_exports__["a"] = MenuItemType;


/***/ }),
/* 11 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react_dom__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react_dom___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react_dom__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__MenuManagerModule__ = __webpack_require__(12);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__type_ContainerMenuItemType__ = __webpack_require__(20);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__type_ContentMenuItemType__ = __webpack_require__(34);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__type_DefaultMenuItemType__ = __webpack_require__(9);
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */







(function (global) {
  const MenuManagerConfig = {
    config: {
      types: [],
      jsTreeTypes: {
        root: {
          icon: 'oi oi-menu'
        }
      }
    },
    registerType: itemType => {
      MenuManagerConfig.config['types'].push(itemType);
      MenuManagerConfig.config['jsTreeTypes'][itemType.type] = itemType.getTreeType();
    },
    getTypesMap: () => {
      const map = new Map();

      for (const itemType of MenuManagerConfig.config.types) {
        map.set(itemType.type, itemType);
      }

      return map;
    },
    getJsTreeTypes: () => {
      return MenuManagerConfig.config.jsTreeTypes;
    }
  };
  const MenuManagerRenderer = {
    render: (container, input) => {
      __WEBPACK_IMPORTED_MODULE_0_react_dom___default.a.render(__WEBPACK_IMPORTED_MODULE_1_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2__MenuManagerModule__["a" /* default */], {
        loadJson: () => {
          return input.value;
        },
        onChange: items => {
          const json = [];

          for (const item of items.values()) {
            if (!item.isEnabled()) {
              continue;
            }

            json.push({
              id: item.id,
              name: item.name,
              parentId: item.parentId !== '#' ? item.parentId : null,
              position: item.position,
              url: item.url,
              target: item.target,
              type: item.type,
              options: item.options
            });
          }

          input.value = JSON.stringify(json);
        },
        config: MenuManagerConfig
      }), container);
    }
  };
  MenuManagerConfig.registerType(new __WEBPACK_IMPORTED_MODULE_3__type_ContainerMenuItemType__["a" /* default */]('container', 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem\\ContainerMenuItem'));
  MenuManagerConfig.registerType(new __WEBPACK_IMPORTED_MODULE_4__type_ContentMenuItemType__["a" /* default */]('content', 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem\\ContentMenuItem'));
  MenuManagerConfig.registerType(new __WEBPACK_IMPORTED_MODULE_5__type_DefaultMenuItemType__["a" /* default */]('default', 'Novactive\\EzMenuManagerBundle\\Entity\\MenuItem'));
  global['Novactive'] = global.Novactive || {};
  global.Novactive['MenuManagerRenderer'] = MenuManagerRenderer;
  global.Novactive['MenuManagerConfig'] = MenuManagerConfig;
  const SELECTOR_FIELD = '.menu-edit__items-collection';
  const SELECTOR_MENU_MANAGER = '.menu-manager';
  const SELECTOR_INPUT = '.ez-data-source__input';
  document.querySelectorAll(SELECTOR_FIELD).forEach(fieldContainer => {
    const input = fieldContainer.querySelector(SELECTOR_INPUT);
    global.Novactive.MenuManagerRenderer.render(fieldContainer.querySelector(SELECTOR_MENU_MANAGER), input);
  });
})(window);

/***/ }),
/* 12 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_prop_types__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__components_ContentTreeView__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__components_MenuTreeView__ = __webpack_require__(14);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__entity_MenuItem__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__css_menu_manager_css__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__css_menu_manager_css___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_5__css_menu_manager_css__);
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */






class MenuManagerModule extends __WEBPACK_IMPORTED_MODULE_0_react__["Component"] {
  constructor(props) {
    super(props);
    this.state = {
      items: new Map(),
      editedItem: null
    };
    this.handleTreeChange = this.handleTreeChange.bind(this);
    this.handeEdit = this.handeEdit.bind(this);
    this.handleFormSubmit = this.handleFormSubmit.bind(this);
    this.handleFormCancel = this.handleFormCancel.bind(this);
  }

  handleTreeChange(items) {
    this.props.onChange(items);
    this.setState(() => ({
      items: items
    }));
  }

  handeEdit(item) {
    this.setState(() => ({
      editedItem: item
    }));
  }

  handleFormCancel() {
    this.setState(() => ({
      editedItem: null
    }));
  }

  handleFormSubmit(item) {
    this.setState(() => {
      const newItems = new Map(this.state.items);
      newItems.set(item.id, item);
      this.props.onChange(newItems);
      return {
        items: newItems,
        editedItem: null
      };
    });
  }

  componentDidMount() {
    const json = this.props.loadJson();
    const parsedItems = JSON.parse(json);
    const items = this.state.items;

    for (const parsedItem of parsedItems) {
      const item = new __WEBPACK_IMPORTED_MODULE_4__entity_MenuItem__["a" /* default */]({
        id: parsedItem['id'],
        parentId: parsedItem['parentId'] || '#',
        name: parsedItem['name'],
        position: parsedItem['position'],
        url: parsedItem['url'],
        target: parsedItem['target'],
        type: parsedItem['type'],
        options: parsedItem['options']
      });
      items.set(item.id, item);
    }

    this.setState(() => ({
      items: items
    }));
  }

  render() {
    let editForm = null;

    if (this.state.editedItem) {
      const itemType = this.props.config.getTypesMap().get(this.state.editedItem.type);
      editForm = itemType.getEditForm(this.state.editedItem, this.handleFormSubmit, this.handleFormCancel);
    }

    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", {
      className: "row"
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", {
      className: "col-md-6"
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", {
      className: "card"
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", {
      className: "card-body"
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_3__components_MenuTreeView__["a" /* default */], {
      items: this.state.items,
      onChange: this.handleTreeChange,
      onEdit: this.handeEdit,
      language: eZ.adminUiConfig.languages.priority[0],
      types: this.props.config.getTypesMap(),
      jsTreeTypes: this.props.config.getJsTreeTypes()
    })))), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", {
      className: "col-md-6"
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", {
      className: "card"
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", {
      className: "card-body"
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_2__components_ContentTreeView__["a" /* default */], {
      treeRootLocationId: 2
    })))), editForm && __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", {
      className: "col-md-6 card menu-manager-edit-form-container"
    }, editForm));
  }

}
/* harmony export (immutable) */ __webpack_exports__["a"] = MenuManagerModule;

MenuManagerModule.propTypes = {
  loadJson: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  onChange: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  config: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};

/***/ }),
/* 13 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_prop_types__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_jquery__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_jquery__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_jstree_dist_jstree_min__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_jstree_dist_jstree_min___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_jstree_dist_jstree_min__);
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    florian
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */




const ROOT_ID = 'root';
class ContentTreeView extends __WEBPACK_IMPORTED_MODULE_0_react__["PureComponent"] {
  constructor(props) {
    super(props);
    this.tree = null;
    this.treeRootLocationId = props.treeRootLocationId;
  }

  getTreeData() {
    const data = [{
      id: ROOT_ID,
      text: Translator.trans('menu.root'),
      parent: '#',
      type: ROOT_ID,
      state: {
        opened: true
      }
    }];
    return data;
  }

  handleCheck(operation, node, parentNode, nodePosition, more) {
    if (operation === 'move_node') {
      if (node.id === ROOT_ID) {
        return false;
      }

      if (parentNode.id === '#') {
        return false;
      }

      if (node.state.disabled) {
        return false;
      }
    }

    return true;
  }

  getNode(id) {
    return this.tree.get_node(id);
  }

  componentDidMount() {
    this.tree = __WEBPACK_IMPORTED_MODULE_2_jquery___default()(this.treeContainer).jstree({
      core: {
        data: this.getTreeData(),
        check_callback: this.handleCheck
      },
      conditionalselect: (node, event) => {
        return false;
      },
      plugins: ['changed', 'dnd', 'conditionalselect', 'types']
    }).jstree(true); // $(this.treeContainer).on('move_node.jstree copy_node.jstree delete_node.jstree create_node.jstree', this.onTreeChange)
  }

  componentDidUpdate() {
    this.tree.settings.core.data = this.getTreeData();
    this.tree.refresh({
      skip_loading: true
    });
  }

  render() {
    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", {
      ref: div => this.treeContainer = div
    });
  }

}
/* harmony export (immutable) */ __webpack_exports__["a"] = ContentTreeView;

ContentTreeView.propTypes = {
  treeRootLocationId: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number
};

/***/ }),
/* 14 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_prop_types__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_jquery__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_jquery___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_jquery__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_jstree_dist_jstree_min__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_jstree_dist_jstree_min___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_jstree_dist_jstree_min__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__entity_MenuItem__ = __webpack_require__(2);
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */





const ROOT_ID = 'root';
class MenuTreeView extends __WEBPACK_IMPORTED_MODULE_0_react__["PureComponent"] {
  constructor(props) {
    super(props);
    this.tree = null;
    this.onTreeChange = this.onTreeChange.bind(this);
    this.handleCheck = this.handleCheck.bind(this);
    this.getNode = this.getNode.bind(this);
    this.handleContextMenu = this.handleContextMenu.bind(this);
    this.handleEditTreeNode = this.handleEditTreeNode.bind(this);
    this.handleCreateTreeNode = this.handleCreateTreeNode.bind(this);
  }

  getTreeData() {
    const data = [{
      id: ROOT_ID,
      text: Translator.trans('menu.root'),
      parent: '#',
      type: ROOT_ID,
      state: {
        opened: true
      }
    }];
    /**
         * @var item MenuItem
         */

    for (const item of this.props.items.values()) {
      const node = item.toTreeNode(this.props.language);

      if (node.parent === '#') {
        node.parent = ROOT_ID;
      }

      data.push(node);
    }

    return data;
  }

  onTreeChange() {
    const items = new Map();
    const tree = this.tree.get_json(null, {
      flat: true
    });

    for (const [index, node] of Object.entries(tree)) {
      if (node.id === ROOT_ID) {
        continue;
      }

      if (node.parent === ROOT_ID) {
        node.parent = '#';
      }

      const item = __WEBPACK_IMPORTED_MODULE_4__entity_MenuItem__["a" /* default */].fromTreeNode(node, index);
      items.set(item.id, item);
    }

    this.props.onChange(items);
  }

  handleCheck(operation, node, parentNode, nodePosition, more) {
    if (operation === 'move_node') {
      if (node.id === ROOT_ID) {
        return false;
      }

      if (parentNode.id === '#') {
        return false;
      }

      if (node.state.disabled) {
        return false;
      }
    }

    return true;
  }

  handleContextMenu(node) {
    const item = this.props.items.get(node.id);
    let contextMenuItems = {};

    if (!node.state.disabled) {
      const itemTypes = [...this.props.types.values()];
      const submenuItems = {};

      for (const itemType of itemTypes) {
        const btn = itemType.getContextualMenuCreateBtn(this, node, item);

        if (btn) {
          submenuItems["createItem_".concat(itemType.identifier)] = btn;
        }
      }

      contextMenuItems["createItem"] = {
        label: Translator.trans("menu_item.action.create"),
        submenu: submenuItems,
        separator_after: true
      };
    }

    if (node.id !== ROOT_ID) {
      const itemType = this.props.types.get(node.type);
      contextMenuItems = Object.assign(contextMenuItems, itemType.getContextualMenu(this, node, item) || {});
    }

    return contextMenuItems;
  }

  handleCreateTreeNode(item) {
    this.props.onEdit(item);
  }

  handleEditTreeNode(event) {
    const node = this.tree.get_node(event.reference);
    const item = this.props.items.get(node.id);
    this.props.onEdit(item);
  }

  getNode(id) {
    return this.tree.get_node(id);
  }

  disableTree(id) {
    const node = this.disableNode(id);

    if (node && node.children.length > 0) {
      for (const childId of node.children) {
        this.disableTree(childId);
      }
    }
  }

  enableTree(id) {
    const node = this.enableNode(id);

    if (node && node.children.length > 0) {
      for (const childId of node.children) {
        this.enableTree(childId);
      }
    }
  }

  enableNode(id) {
    const node = this.tree.get_node(id);
    this.tree.enable_node(node);
    return node;
  }

  disableNode(id) {
    const node = this.tree.get_node(id);
    this.tree.disable_node(node);
    return node;
  }

  componentDidMount() {
    this.tree = __WEBPACK_IMPORTED_MODULE_2_jquery___default()(this.treeContainer).jstree({
      core: {
        data: this.getTreeData(),
        check_callback: this.handleCheck
      },
      types: this.props.jsTreeTypes,
      conditionalselect: (node, event) => {
        return false;
      },
      contextmenu: {
        items: this.handleContextMenu
      },
      plugins: ['changed', 'dnd', 'conditionalselect', 'contextmenu', 'types']
    }).jstree(true);
    __WEBPACK_IMPORTED_MODULE_2_jquery___default()(this.treeContainer).on('move_node.jstree copy_node.jstree delete_node.jstree create_node.jstree', this.onTreeChange);
  }

  componentDidUpdate() {
    this.tree.settings.core.data = this.getTreeData();
    this.tree.refresh({
      skip_loading: true
    });
  }

  render() {
    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", {
      ref: div => this.treeContainer = div
    });
  }

}
/* harmony export (immutable) */ __webpack_exports__["a"] = MenuTreeView;

MenuTreeView.propTypes = {
  items: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.instanceOf(Map),
  types: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.instanceOf(Map),
  jsTreeTypes: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  onChange: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  onEdit: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  language: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string
};

/***/ }),
/* 15 */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(16);

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(18)(content, options);

if(content.locals) module.exports = content.locals;

if(false) {
	module.hot.accept("!!../../../../node_modules/css-loader/dist/cjs.js!./menu-manager.css", function() {
		var newContent = require("!!../../../../node_modules/css-loader/dist/cjs.js!./menu-manager.css");

		if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];

		var locals = (function(a, b) {
			var key, idx = 0;

			for(key in a) {
				if(!b || a[key] !== b[key]) return false;
				idx++;
			}

			for(key in b) idx--;

			return idx === 0;
		}(content.locals, newContent.locals));

		if(!locals) throw new Error('Aborting CSS HMR due to changed css-modules locals.');

		update(newContent);
	});

	module.hot.dispose(function() { update(); });
}

/***/ }),
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(17)(false);
// Module
exports.push([module.i, "/*\n * NovaeZMenuManagerBundle.\n *\n * @package   NovaeZMenuManagerBundle\n *\n * @author    Novactive <f.alexandre@novactive.com>\n * @copyright 2019 Novactive\n * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE\n *\n */\n\n.menu-manager {\n  min-height: 360px;\n  position: relative;\n}\n\n.menu-manager-edit-form-container {\n  position: absolute;\n  background: white;\n  right: -1px;\n  padding: 1em;\n  top: -1px;\n  bottom: -1px;\n  -moz-box-shadow: -10px 0 10px -5px #cfcfcf;\n  -webkit-box-shadow: -10px 0 10px -5px #cfcfcf;\n  -o-box-shadow: -10px 0 10px -5px #cfcfcf;\n  box-shadow: -10px 0 10px -5px #cfcfcf;\n  filter: progid:dximagetransform.microsoft.shadow(color=#cfcfcf, Direction=180, Strength=10);\n}\n\n.menu-manager .jstree-desactivated {\n  background: transparent;\n  color: #666;\n}\n\n.menu-manager .jstree-disabled {\n  text-decoration: line-through;\n}\n\n.menu-tree__wrapper {\n  max-height: 400px;\n  overflow: auto;\n  padding: 10px 0;\n}\n", ""]);


/***/ }),
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
// eslint-disable-next-line func-names
module.exports = function (useSourceMap) {
  var list = []; // return the list of modules as css string

  list.toString = function toString() {
    return this.map(function (item) {
      var content = cssWithMappingToString(item, useSourceMap);

      if (item[2]) {
        return "@media ".concat(item[2], "{").concat(content, "}");
      }

      return content;
    }).join('');
  }; // import a list of modules into the list
  // eslint-disable-next-line func-names


  list.i = function (modules, mediaQuery) {
    if (typeof modules === 'string') {
      // eslint-disable-next-line no-param-reassign
      modules = [[null, modules, '']];
    }

    var alreadyImportedModules = {};

    for (var i = 0; i < this.length; i++) {
      // eslint-disable-next-line prefer-destructuring
      var id = this[i][0];

      if (id != null) {
        alreadyImportedModules[id] = true;
      }
    }

    for (var _i = 0; _i < modules.length; _i++) {
      var item = modules[_i]; // skip already imported module
      // this implementation is not 100% perfect for weird media query combinations
      // when a module is imported multiple times with different media queries.
      // I hope this will never occur (Hey this way we have smaller bundles)

      if (item[0] == null || !alreadyImportedModules[item[0]]) {
        if (mediaQuery && !item[2]) {
          item[2] = mediaQuery;
        } else if (mediaQuery) {
          item[2] = "(".concat(item[2], ") and (").concat(mediaQuery, ")");
        }

        list.push(item);
      }
    }
  };

  return list;
};

function cssWithMappingToString(item, useSourceMap) {
  var content = item[1] || ''; // eslint-disable-next-line prefer-destructuring

  var cssMapping = item[3];

  if (!cssMapping) {
    return content;
  }

  if (useSourceMap && typeof btoa === 'function') {
    var sourceMapping = toComment(cssMapping);
    var sourceURLs = cssMapping.sources.map(function (source) {
      return "/*# sourceURL=".concat(cssMapping.sourceRoot).concat(source, " */");
    });
    return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
  }

  return [content].join('\n');
} // Adapted from convert-source-map (MIT)


function toComment(sourceMap) {
  // eslint-disable-next-line no-undef
  var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
  var data = "sourceMappingURL=data:application/json;charset=utf-8;base64,".concat(base64);
  return "/*# ".concat(data, " */");
}

/***/ }),
/* 18 */
/***/ (function(module, exports, __webpack_require__) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

var stylesInDom = {};

var	memoize = function (fn) {
	var memo;

	return function () {
		if (typeof memo === "undefined") memo = fn.apply(this, arguments);
		return memo;
	};
};

var isOldIE = memoize(function () {
	// Test for IE <= 9 as proposed by Browserhacks
	// @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
	// Tests for existence of standard globals is to allow style-loader
	// to operate correctly into non-standard environments
	// @see https://github.com/webpack-contrib/style-loader/issues/177
	return window && document && document.all && !window.atob;
});

var getTarget = function (target, parent) {
  if (parent){
    return parent.querySelector(target);
  }
  return document.querySelector(target);
};

var getElement = (function (fn) {
	var memo = {};

	return function(target, parent) {
                // If passing function in options, then use it for resolve "head" element.
                // Useful for Shadow Root style i.e
                // {
                //   insertInto: function () { return document.querySelector("#foo").shadowRoot }
                // }
                if (typeof target === 'function') {
                        return target();
                }
                if (typeof memo[target] === "undefined") {
			var styleTarget = getTarget.call(this, target, parent);
			// Special case to return head of iframe instead of iframe itself
			if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
				try {
					// This will throw an exception if access to iframe is blocked
					// due to cross-origin restrictions
					styleTarget = styleTarget.contentDocument.head;
				} catch(e) {
					styleTarget = null;
				}
			}
			memo[target] = styleTarget;
		}
		return memo[target]
	};
})();

var singleton = null;
var	singletonCounter = 0;
var	stylesInsertedAtTop = [];

var	fixUrls = __webpack_require__(19);

module.exports = function(list, options) {
	if (typeof DEBUG !== "undefined" && DEBUG) {
		if (typeof document !== "object") throw new Error("The style-loader cannot be used in a non-browser environment");
	}

	options = options || {};

	options.attrs = typeof options.attrs === "object" ? options.attrs : {};

	// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
	// tags it will allow on a page
	if (!options.singleton && typeof options.singleton !== "boolean") options.singleton = isOldIE();

	// By default, add <style> tags to the <head> element
        if (!options.insertInto) options.insertInto = "head";

	// By default, add <style> tags to the bottom of the target
	if (!options.insertAt) options.insertAt = "bottom";

	var styles = listToStyles(list, options);

	addStylesToDom(styles, options);

	return function update (newList) {
		var mayRemove = [];

		for (var i = 0; i < styles.length; i++) {
			var item = styles[i];
			var domStyle = stylesInDom[item.id];

			domStyle.refs--;
			mayRemove.push(domStyle);
		}

		if(newList) {
			var newStyles = listToStyles(newList, options);
			addStylesToDom(newStyles, options);
		}

		for (var i = 0; i < mayRemove.length; i++) {
			var domStyle = mayRemove[i];

			if(domStyle.refs === 0) {
				for (var j = 0; j < domStyle.parts.length; j++) domStyle.parts[j]();

				delete stylesInDom[domStyle.id];
			}
		}
	};
};

function addStylesToDom (styles, options) {
	for (var i = 0; i < styles.length; i++) {
		var item = styles[i];
		var domStyle = stylesInDom[item.id];

		if(domStyle) {
			domStyle.refs++;

			for(var j = 0; j < domStyle.parts.length; j++) {
				domStyle.parts[j](item.parts[j]);
			}

			for(; j < item.parts.length; j++) {
				domStyle.parts.push(addStyle(item.parts[j], options));
			}
		} else {
			var parts = [];

			for(var j = 0; j < item.parts.length; j++) {
				parts.push(addStyle(item.parts[j], options));
			}

			stylesInDom[item.id] = {id: item.id, refs: 1, parts: parts};
		}
	}
}

function listToStyles (list, options) {
	var styles = [];
	var newStyles = {};

	for (var i = 0; i < list.length; i++) {
		var item = list[i];
		var id = options.base ? item[0] + options.base : item[0];
		var css = item[1];
		var media = item[2];
		var sourceMap = item[3];
		var part = {css: css, media: media, sourceMap: sourceMap};

		if(!newStyles[id]) styles.push(newStyles[id] = {id: id, parts: [part]});
		else newStyles[id].parts.push(part);
	}

	return styles;
}

function insertStyleElement (options, style) {
	var target = getElement(options.insertInto)

	if (!target) {
		throw new Error("Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid.");
	}

	var lastStyleElementInsertedAtTop = stylesInsertedAtTop[stylesInsertedAtTop.length - 1];

	if (options.insertAt === "top") {
		if (!lastStyleElementInsertedAtTop) {
			target.insertBefore(style, target.firstChild);
		} else if (lastStyleElementInsertedAtTop.nextSibling) {
			target.insertBefore(style, lastStyleElementInsertedAtTop.nextSibling);
		} else {
			target.appendChild(style);
		}
		stylesInsertedAtTop.push(style);
	} else if (options.insertAt === "bottom") {
		target.appendChild(style);
	} else if (typeof options.insertAt === "object" && options.insertAt.before) {
		var nextSibling = getElement(options.insertAt.before, target);
		target.insertBefore(style, nextSibling);
	} else {
		throw new Error("[Style Loader]\n\n Invalid value for parameter 'insertAt' ('options.insertAt') found.\n Must be 'top', 'bottom', or Object.\n (https://github.com/webpack-contrib/style-loader#insertat)\n");
	}
}

function removeStyleElement (style) {
	if (style.parentNode === null) return false;
	style.parentNode.removeChild(style);

	var idx = stylesInsertedAtTop.indexOf(style);
	if(idx >= 0) {
		stylesInsertedAtTop.splice(idx, 1);
	}
}

function createStyleElement (options) {
	var style = document.createElement("style");

	if(options.attrs.type === undefined) {
		options.attrs.type = "text/css";
	}

	if(options.attrs.nonce === undefined) {
		var nonce = getNonce();
		if (nonce) {
			options.attrs.nonce = nonce;
		}
	}

	addAttrs(style, options.attrs);
	insertStyleElement(options, style);

	return style;
}

function createLinkElement (options) {
	var link = document.createElement("link");

	if(options.attrs.type === undefined) {
		options.attrs.type = "text/css";
	}
	options.attrs.rel = "stylesheet";

	addAttrs(link, options.attrs);
	insertStyleElement(options, link);

	return link;
}

function addAttrs (el, attrs) {
	Object.keys(attrs).forEach(function (key) {
		el.setAttribute(key, attrs[key]);
	});
}

function getNonce() {
	if (false) {
		return null;
	}

	return __webpack_require__.nc;
}

function addStyle (obj, options) {
	var style, update, remove, result;

	// If a transform function was defined, run it on the css
	if (options.transform && obj.css) {
	    result = typeof options.transform === 'function'
		 ? options.transform(obj.css) 
		 : options.transform.default(obj.css);

	    if (result) {
	    	// If transform returns a value, use that instead of the original css.
	    	// This allows running runtime transformations on the css.
	    	obj.css = result;
	    } else {
	    	// If the transform function returns a falsy value, don't add this css.
	    	// This allows conditional loading of css
	    	return function() {
	    		// noop
	    	};
	    }
	}

	if (options.singleton) {
		var styleIndex = singletonCounter++;

		style = singleton || (singleton = createStyleElement(options));

		update = applyToSingletonTag.bind(null, style, styleIndex, false);
		remove = applyToSingletonTag.bind(null, style, styleIndex, true);

	} else if (
		obj.sourceMap &&
		typeof URL === "function" &&
		typeof URL.createObjectURL === "function" &&
		typeof URL.revokeObjectURL === "function" &&
		typeof Blob === "function" &&
		typeof btoa === "function"
	) {
		style = createLinkElement(options);
		update = updateLink.bind(null, style, options);
		remove = function () {
			removeStyleElement(style);

			if(style.href) URL.revokeObjectURL(style.href);
		};
	} else {
		style = createStyleElement(options);
		update = applyToTag.bind(null, style);
		remove = function () {
			removeStyleElement(style);
		};
	}

	update(obj);

	return function updateStyle (newObj) {
		if (newObj) {
			if (
				newObj.css === obj.css &&
				newObj.media === obj.media &&
				newObj.sourceMap === obj.sourceMap
			) {
				return;
			}

			update(obj = newObj);
		} else {
			remove();
		}
	};
}

var replaceText = (function () {
	var textStore = [];

	return function (index, replacement) {
		textStore[index] = replacement;

		return textStore.filter(Boolean).join('\n');
	};
})();

function applyToSingletonTag (style, index, remove, obj) {
	var css = remove ? "" : obj.css;

	if (style.styleSheet) {
		style.styleSheet.cssText = replaceText(index, css);
	} else {
		var cssNode = document.createTextNode(css);
		var childNodes = style.childNodes;

		if (childNodes[index]) style.removeChild(childNodes[index]);

		if (childNodes.length) {
			style.insertBefore(cssNode, childNodes[index]);
		} else {
			style.appendChild(cssNode);
		}
	}
}

function applyToTag (style, obj) {
	var css = obj.css;
	var media = obj.media;

	if(media) {
		style.setAttribute("media", media)
	}

	if(style.styleSheet) {
		style.styleSheet.cssText = css;
	} else {
		while(style.firstChild) {
			style.removeChild(style.firstChild);
		}

		style.appendChild(document.createTextNode(css));
	}
}

function updateLink (link, options, obj) {
	var css = obj.css;
	var sourceMap = obj.sourceMap;

	/*
		If convertToAbsoluteUrls isn't defined, but sourcemaps are enabled
		and there is no publicPath defined then lets turn convertToAbsoluteUrls
		on by default.  Otherwise default to the convertToAbsoluteUrls option
		directly
	*/
	var autoFixUrls = options.convertToAbsoluteUrls === undefined && sourceMap;

	if (options.convertToAbsoluteUrls || autoFixUrls) {
		css = fixUrls(css);
	}

	if (sourceMap) {
		// http://stackoverflow.com/a/26603875
		css += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + " */";
	}

	var blob = new Blob([css], { type: "text/css" });

	var oldSrc = link.href;

	link.href = URL.createObjectURL(blob);

	if(oldSrc) URL.revokeObjectURL(oldSrc);
}


/***/ }),
/* 19 */
/***/ (function(module, exports) {


/**
 * When source maps are enabled, `style-loader` uses a link element with a data-uri to
 * embed the css on the page. This breaks all relative urls because now they are relative to a
 * bundle instead of the current page.
 *
 * One solution is to only use full urls, but that may be impossible.
 *
 * Instead, this function "fixes" the relative urls to be absolute according to the current page location.
 *
 * A rudimentary test suite is located at `test/fixUrls.js` and can be run via the `npm test` command.
 *
 */

module.exports = function (css) {
  // get current location
  var location = typeof window !== "undefined" && window.location;

  if (!location) {
    throw new Error("fixUrls requires window.location");
  }

	// blank or null?
	if (!css || typeof css !== "string") {
	  return css;
  }

  var baseUrl = location.protocol + "//" + location.host;
  var currentDir = baseUrl + location.pathname.replace(/\/[^\/]*$/, "/");

	// convert each url(...)
	/*
	This regular expression is just a way to recursively match brackets within
	a string.

	 /url\s*\(  = Match on the word "url" with any whitespace after it and then a parens
	   (  = Start a capturing group
	     (?:  = Start a non-capturing group
	         [^)(]  = Match anything that isn't a parentheses
	         |  = OR
	         \(  = Match a start parentheses
	             (?:  = Start another non-capturing groups
	                 [^)(]+  = Match anything that isn't a parentheses
	                 |  = OR
	                 \(  = Match a start parentheses
	                     [^)(]*  = Match anything that isn't a parentheses
	                 \)  = Match a end parentheses
	             )  = End Group
              *\) = Match anything and then a close parens
          )  = Close non-capturing group
          *  = Match anything
       )  = Close capturing group
	 \)  = Match a close parens

	 /gi  = Get all matches, not the first.  Be case insensitive.
	 */
	var fixedCss = css.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi, function(fullMatch, origUrl) {
		// strip quotes (if they exist)
		var unquotedOrigUrl = origUrl
			.trim()
			.replace(/^"(.*)"$/, function(o, $1){ return $1; })
			.replace(/^'(.*)'$/, function(o, $1){ return $1; });

		// already a full url? no change
		if (/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/|\s*$)/i.test(unquotedOrigUrl)) {
		  return fullMatch;
		}

		// convert the url to a full url
		var newUrl;

		if (unquotedOrigUrl.indexOf("//") === 0) {
		  	//TODO: should we add protocol?
			newUrl = unquotedOrigUrl;
		} else if (unquotedOrigUrl.indexOf("/") === 0) {
			// path should be relative to the base url
			newUrl = baseUrl + unquotedOrigUrl; // already starts with '/'
		} else {
			// path should be relative to current directory
			newUrl = currentDir + unquotedOrigUrl.replace(/^\.\//, ""); // Strip leading './'
		}

		// send back the fixed url(...)
		return "url(" + JSON.stringify(newUrl) + ")";
	});

	// send back the fixed css
	return fixedCss;
};


/***/ }),
/* 20 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__components_form_ContainerMenuItemEditFormModule__ = __webpack_require__(21);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__DefaultMenuItemType__ = __webpack_require__(9);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__entity_MenuItem__ = __webpack_require__(2);
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */




class ContainerMenuItemType extends __WEBPACK_IMPORTED_MODULE_2__DefaultMenuItemType__["a" /* default */] {
  /**
     * @inheritDoc
     */
  getTreeType() {
    return {
      icon: 'oi oi-folder',
      max_children: -1,
      max_depth: -1,
      valid_children: -1
    };
  }
  /**
     * @inheritDoc
     */


  getEditForm(item, onSubmit, onCancel) {
    const form = __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_1__components_form_ContainerMenuItemEditFormModule__["a" /* default */], {
      item: item,
      onSubmit: onSubmit,
      onCancel: onCancel
    });
    return form;
  }
  /**
     * @param props
     * @returns {MenuItem}
     */


  getNewItem(props) {
    const defaultProps = {
      id: null,
      name: Translator.trans('menu_item.default_container_title'),
      type: this.type
    };
    props = Object.assign(defaultProps, props || {});
    return new __WEBPACK_IMPORTED_MODULE_3__entity_MenuItem__["a" /* default */](props);
  }

}
/* harmony export (immutable) */ __webpack_exports__["a"] = ContainerMenuItemType;


/***/ }),
/* 21 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_prop_types__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__MenuItemEditFormModule__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_reactstrap__ = __webpack_require__(7);
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */




class ContainerMenuItemEditFormModule extends __WEBPACK_IMPORTED_MODULE_2__MenuItemEditFormModule__["a" /* default */] {
  constructor(props) {
    super(props);
    this.defaultLanguage = eZ.adminUiConfig.languages.priority[0];
    this.state = {
      name: this.props.item.name,
      language: this.defaultLanguage
    };
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevProps.item !== this.props.item) {
      this.setState(state => ({
        name: this.props.item.name
      }));
    }
  }

  handleSubmit(event) {
    event.preventDefault();
    const item = this.props.item;
    item.name = this.state.name;
    this.props.onSubmit(item);
  }

  render() {
    const languages = [];

    for (const languageCode in eZ.adminUiConfig.languages.mappings) {
      languages.push(eZ.adminUiConfig.languages.mappings[languageCode]);
    }

    return __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("div", null, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_3_reactstrap__["b" /* Form */], {
      onSubmit: this.handleSubmit
    }, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_3_reactstrap__["c" /* FormGroup */], null, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_3_reactstrap__["e" /* Label */], {
      for: "language"
    }, Translator.trans('menu_item.property.language')), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_3_reactstrap__["d" /* Input */], {
      type: "select",
      onChange: this.handleLanguageChange
    }, [...languages].map(language => __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement("option", {
      key: language.id,
      value: language.languageCode
    }, language.name)))), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_3_reactstrap__["c" /* FormGroup */], null, __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_3_reactstrap__["e" /* Label */], {
      for: "item_name"
    }, Translator.trans('menu_item.property.container_name'), " (", this.state.language, ")"), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_3_reactstrap__["d" /* Input */], {
      type: "text",
      name: "name",
      value: this.getInputValue(this.state.name),
      id: "item_name",
      onChange: this.handleLocalizedInputChange
    })), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_3_reactstrap__["a" /* Button */], {
      type: "submit",
      className: "pull-right",
      color: "primary"
    }, Translator.trans('menu_item.edit_form.save')), __WEBPACK_IMPORTED_MODULE_0_react___default.a.createElement(__WEBPACK_IMPORTED_MODULE_3_reactstrap__["a" /* Button */], {
      type: "button",
      onClick: this.handleCancel
    }, Translator.trans('menu_item.edit_form.cancel'))));
  }

}
/* harmony export (immutable) */ __webpack_exports__["a"] = ContainerMenuItemEditFormModule;

ContainerMenuItemEditFormModule.propTypes = {
  item: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  onSubmit: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  onCancel: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func
};

/***/ }),
/* 22 */
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),
/* 23 */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2017 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg) && arg.length) {
				var inner = classNames.apply(null, arg);
				if (inner) {
					classes.push(inner);
				}
			} else if (argType === 'object') {
				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
				}
			}
		}

		return classes.join(' ');
	}

	if (typeof module !== 'undefined' && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {
		window.classNames = classNames;
	}
}());


/***/ }),
/* 24 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {/**
 * Lodash (Custom Build) <https://lodash.com/>
 * Build: `lodash modularize exports="npm" -o ./`
 * Copyright JS Foundation and other contributors <https://js.foundation/>
 * Released under MIT license <https://lodash.com/license>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 */

/** `Object#toString` result references. */
var asyncTag = '[object AsyncFunction]',
    funcTag = '[object Function]',
    genTag = '[object GeneratorFunction]',
    nullTag = '[object Null]',
    proxyTag = '[object Proxy]',
    undefinedTag = '[object Undefined]';

/** Detect free variable `global` from Node.js. */
var freeGlobal = typeof global == 'object' && global && global.Object === Object && global;

/** Detect free variable `self`. */
var freeSelf = typeof self == 'object' && self && self.Object === Object && self;

/** Used as a reference to the global object. */
var root = freeGlobal || freeSelf || Function('return this')();

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */
var nativeObjectToString = objectProto.toString;

/** Built-in value references. */
var Symbol = root.Symbol,
    symToStringTag = Symbol ? Symbol.toStringTag : undefined;

/**
 * The base implementation of `getTag` without fallbacks for buggy environments.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the `toStringTag`.
 */
function baseGetTag(value) {
  if (value == null) {
    return value === undefined ? undefinedTag : nullTag;
  }
  return (symToStringTag && symToStringTag in Object(value))
    ? getRawTag(value)
    : objectToString(value);
}

/**
 * A specialized version of `baseGetTag` which ignores `Symbol.toStringTag` values.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the raw `toStringTag`.
 */
function getRawTag(value) {
  var isOwn = hasOwnProperty.call(value, symToStringTag),
      tag = value[symToStringTag];

  try {
    value[symToStringTag] = undefined;
    var unmasked = true;
  } catch (e) {}

  var result = nativeObjectToString.call(value);
  if (unmasked) {
    if (isOwn) {
      value[symToStringTag] = tag;
    } else {
      delete value[symToStringTag];
    }
  }
  return result;
}

/**
 * Converts `value` to a string using `Object.prototype.toString`.
 *
 * @private
 * @param {*} value The value to convert.
 * @returns {string} Returns the converted string.
 */
function objectToString(value) {
  return nativeObjectToString.call(value);
}

/**
 * Checks if `value` is classified as a `Function` object.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a function, else `false`.
 * @example
 *
 * _.isFunction(_);
 * // => true
 *
 * _.isFunction(/abc/);
 * // => false
 */
function isFunction(value) {
  if (!isObject(value)) {
    return false;
  }
  // The use of `Object#toString` avoids issues with the `typeof` operator
  // in Safari 9 which returns 'object' for typed arrays and other constructors.
  var tag = baseGetTag(value);
  return tag == funcTag || tag == genTag || tag == asyncTag || tag == proxyTag;
}

/**
 * Checks if `value` is the
 * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
 * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(_.noop);
 * // => true
 *
 * _.isObject(null);
 * // => false
 */
function isObject(value) {
  var type = typeof value;
  return value != null && (type == 'object' || type == 'function');
}

module.exports = isFunction;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(8)))

/***/ }),
/* 25 */
/***/ (function(module, exports) {

/**
 * lodash 3.0.2 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

module.exports = isObject;


/***/ }),
/* 26 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Manager__ = __webpack_require__(27);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return __WEBPACK_IMPORTED_MODULE_0__Manager__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__Target__ = __webpack_require__(28);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return __WEBPACK_IMPORTED_MODULE_1__Target__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__Popper__ = __webpack_require__(29);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return __WEBPACK_IMPORTED_MODULE_2__Popper__["a"]; });
/* unused harmony reexport placements */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__Arrow__ = __webpack_require__(31);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_3__Arrow__["a"]; });





/***/ }),
/* 27 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_prop_types__);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _objectWithoutProperties(obj, keys) { var target = {}; for (var i in obj) { if (keys.indexOf(i) >= 0) continue; if (!Object.prototype.hasOwnProperty.call(obj, i)) continue; target[i] = obj[i]; } return target; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }




var Manager = function (_Component) {
  _inherits(Manager, _Component);

  function Manager() {
    var _ref;

    var _temp, _this, _ret;

    _classCallCheck(this, Manager);

    for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    return _ret = (_temp = (_this = _possibleConstructorReturn(this, (_ref = Manager.__proto__ || Object.getPrototypeOf(Manager)).call.apply(_ref, [this].concat(args))), _this), _this._setTargetNode = function (node) {
      _this._targetNode = node;
    }, _this._getTargetNode = function () {
      return _this._targetNode;
    }, _temp), _possibleConstructorReturn(_this, _ret);
  }

  _createClass(Manager, [{
    key: 'getChildContext',
    value: function getChildContext() {
      return {
        popperManager: {
          setTargetNode: this._setTargetNode,
          getTargetNode: this._getTargetNode
        }
      };
    }
  }, {
    key: 'render',
    value: function render() {
      var _props = this.props,
          tag = _props.tag,
          children = _props.children,
          restProps = _objectWithoutProperties(_props, ['tag', 'children']);

      if (tag !== false) {
        return Object(__WEBPACK_IMPORTED_MODULE_0_react__["createElement"])(tag, restProps, children);
      } else {
        return children;
      }
    }
  }]);

  return Manager;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);

Manager.childContextTypes = {
  popperManager: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object.isRequired
};
Manager.propTypes = {
  tag: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.string, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool]),
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func])
};
Manager.defaultProps = {
  tag: 'div'
};


/* harmony default export */ __webpack_exports__["a"] = (Manager);

/***/ }),
/* 28 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_prop_types__);
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

function _objectWithoutProperties(obj, keys) { var target = {}; for (var i in obj) { if (keys.indexOf(i) >= 0) continue; if (!Object.prototype.hasOwnProperty.call(obj, i)) continue; target[i] = obj[i]; } return target; }




var Target = function Target(props, context) {
  var _props$component = props.component,
      component = _props$component === undefined ? 'div' : _props$component,
      innerRef = props.innerRef,
      children = props.children,
      restProps = _objectWithoutProperties(props, ['component', 'innerRef', 'children']);

  var popperManager = context.popperManager;

  var targetRef = function targetRef(node) {
    popperManager.setTargetNode(node);
    if (typeof innerRef === 'function') {
      innerRef(node);
    }
  };

  if (typeof children === 'function') {
    var targetProps = { ref: targetRef };
    return children({ targetProps: targetProps, restProps: restProps });
  }

  var componentProps = _extends({}, restProps);

  if (typeof component === 'string') {
    componentProps.ref = targetRef;
  } else {
    componentProps.innerRef = targetRef;
  }

  return Object(__WEBPACK_IMPORTED_MODULE_0_react__["createElement"])(component, componentProps, children);
};

Target.contextTypes = {
  popperManager: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object.isRequired
};

Target.propTypes = {
  component: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func]),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func])
};

/* harmony default export */ __webpack_exports__["a"] = (Target);

/***/ }),
/* 29 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export placements */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_prop_types__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_popper_js__ = __webpack_require__(30);
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _objectWithoutProperties(obj, keys) { var target = {}; for (var i in obj) { if (keys.indexOf(i) >= 0) continue; if (!Object.prototype.hasOwnProperty.call(obj, i)) continue; target[i] = obj[i]; } return target; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }





var placements = __WEBPACK_IMPORTED_MODULE_2_popper_js__["a" /* default */].placements;

var Popper = function (_Component) {
  _inherits(Popper, _Component);

  function Popper() {
    var _ref;

    var _temp, _this, _ret;

    _classCallCheck(this, Popper);

    for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    return _ret = (_temp = (_this = _possibleConstructorReturn(this, (_ref = Popper.__proto__ || Object.getPrototypeOf(Popper)).call.apply(_ref, [this].concat(args))), _this), _this.state = {}, _this._setArrowNode = function (node) {
      _this._arrowNode = node;
    }, _this._getTargetNode = function () {
      if (_this.props.target) {
        return _this.props.target;
      } else if (!_this.context.popperManager || !_this.context.popperManager.getTargetNode()) {
        throw new Error('Target missing. Popper must be given a target from the Popper Manager, or as a prop.');
      }
      return _this.context.popperManager.getTargetNode();
    }, _this._getOffsets = function (data) {
      return Object.keys(data.offsets).map(function (key) {
        return data.offsets[key];
      });
    }, _this._isDataDirty = function (data) {
      if (_this.state.data) {
        return JSON.stringify(_this._getOffsets(_this.state.data)) !== JSON.stringify(_this._getOffsets(data));
      } else {
        return true;
      }
    }, _this._updateStateModifier = {
      enabled: true,
      order: 900,
      fn: function fn(data) {
        if (_this._isDataDirty(data)) {
          _this.setState({ data: data });
        }
        return data;
      }
    }, _this._getPopperStyle = function () {
      var data = _this.state.data;


      if (!_this._popper || !data) {
        return {
          position: 'absolute',
          pointerEvents: 'none',
          opacity: 0
        };
      }

      return _extends({
        position: data.offsets.popper.position
      }, data.styles);
    }, _this._getPopperPlacement = function () {
      return _this.state.data ? _this.state.data.placement : undefined;
    }, _this._getPopperHide = function () {
      return !!_this.state.data && _this.state.data.hide ? '' : undefined;
    }, _this._getArrowStyle = function () {
      if (!_this.state.data || !_this.state.data.offsets.arrow) {
        return {};
      } else {
        var _this$state$data$offs = _this.state.data.offsets.arrow,
            top = _this$state$data$offs.top,
            left = _this$state$data$offs.left;

        return { top: top, left: left };
      }
    }, _this._handlePopperRef = function (node) {
      _this._popperNode = node;
      if (node) {
        _this._createPopper();
      } else {
        _this._destroyPopper();
      }
      if (_this.props.innerRef) {
        _this.props.innerRef(node);
      }
    }, _this._scheduleUpdate = function () {
      _this._popper && _this._popper.scheduleUpdate();
    }, _temp), _possibleConstructorReturn(_this, _ret);
  }

  _createClass(Popper, [{
    key: 'getChildContext',
    value: function getChildContext() {
      return {
        popper: {
          setArrowNode: this._setArrowNode,
          getArrowStyle: this._getArrowStyle
        }
      };
    }
  }, {
    key: 'componentDidUpdate',
    value: function componentDidUpdate(lastProps) {
      if (lastProps.placement !== this.props.placement || lastProps.eventsEnabled !== this.props.eventsEnabled || lastProps.target !== this.props.target) {
        this._destroyPopper();
        this._createPopper();
      }
      if (lastProps.children !== this.props.children) {
        this._scheduleUpdate();
      }
    }
  }, {
    key: 'componentWillUnmount',
    value: function componentWillUnmount() {
      this._destroyPopper();
    }
  }, {
    key: '_createPopper',
    value: function _createPopper() {
      var _this2 = this;

      var _props = this.props,
          placement = _props.placement,
          eventsEnabled = _props.eventsEnabled,
          positionFixed = _props.positionFixed;

      var modifiers = _extends({}, this.props.modifiers, {
        applyStyle: { enabled: false },
        updateState: this._updateStateModifier
      });
      if (this._arrowNode) {
        modifiers.arrow = _extends({}, this.props.modifiers.arrow || {}, {
          element: this._arrowNode
        });
      }
      this._popper = new __WEBPACK_IMPORTED_MODULE_2_popper_js__["a" /* default */](this._getTargetNode(), this._popperNode, {
        placement: placement,
        positionFixed: positionFixed,
        eventsEnabled: eventsEnabled,
        modifiers: modifiers
      });

      // TODO: look into setTimeout scheduleUpdate call, without it, the popper will not position properly on creation
      setTimeout(function () {
        return _this2._scheduleUpdate();
      });
    }
  }, {
    key: '_destroyPopper',
    value: function _destroyPopper() {
      if (this._popper) {
        this._popper.destroy();
      }
    }
  }, {
    key: 'render',
    value: function render() {
      var _props2 = this.props,
          component = _props2.component,
          innerRef = _props2.innerRef,
          placement = _props2.placement,
          eventsEnabled = _props2.eventsEnabled,
          positionFixed = _props2.positionFixed,
          modifiers = _props2.modifiers,
          children = _props2.children,
          restProps = _objectWithoutProperties(_props2, ['component', 'innerRef', 'placement', 'eventsEnabled', 'positionFixed', 'modifiers', 'children']);

      var popperStyle = this._getPopperStyle();
      var popperPlacement = this._getPopperPlacement();
      var popperHide = this._getPopperHide();

      if (typeof children === 'function') {
        var popperProps = {
          ref: this._handlePopperRef,
          style: popperStyle,
          'data-placement': popperPlacement,
          'data-x-out-of-boundaries': popperHide
        };
        return children({
          popperProps: popperProps,
          restProps: restProps,
          scheduleUpdate: this._scheduleUpdate
        });
      }

      var componentProps = _extends({}, restProps, {
        style: _extends({}, restProps.style, popperStyle),
        'data-placement': popperPlacement,
        'data-x-out-of-boundaries': popperHide
      });

      if (typeof component === 'string') {
        componentProps.ref = this._handlePopperRef;
      } else {
        componentProps.innerRef = this._handlePopperRef;
      }

      return Object(__WEBPACK_IMPORTED_MODULE_0_react__["createElement"])(component, componentProps, children);
    }
  }]);

  return Popper;
}(__WEBPACK_IMPORTED_MODULE_0_react__["Component"]);

Popper.contextTypes = {
  popperManager: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object
};
Popper.childContextTypes = {
  popper: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object.isRequired
};
Popper.propTypes = {
  component: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func]),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  placement: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOf(placements),
  eventsEnabled: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  positionFixed: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.bool,
  modifiers: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func]),
  target: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([
  // the following check is needed for SSR
  __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.instanceOf(typeof Element !== 'undefined' ? Element : Object), __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.shape({
    getBoundingClientRect: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func.isRequired,
    clientWidth: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number.isRequired,
    clientHeight: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.number.isRequired
  })])
};
Popper.defaultProps = {
  component: 'div',
  placement: 'bottom',
  eventsEnabled: true,
  positionFixed: false,
  modifiers: {}
};


/* harmony default export */ __webpack_exports__["a"] = (Popper);

/***/ }),
/* 30 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {/**!
 * @fileOverview Kickass library to create and place poppers near their reference elements.
 * @version 1.15.0
 * @license
 * Copyright (c) 2016 Federico Zivolo and contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
var isBrowser = typeof window !== 'undefined' && typeof document !== 'undefined';

var longerTimeoutBrowsers = ['Edge', 'Trident', 'Firefox'];
var timeoutDuration = 0;
for (var i = 0; i < longerTimeoutBrowsers.length; i += 1) {
  if (isBrowser && navigator.userAgent.indexOf(longerTimeoutBrowsers[i]) >= 0) {
    timeoutDuration = 1;
    break;
  }
}

function microtaskDebounce(fn) {
  var called = false;
  return function () {
    if (called) {
      return;
    }
    called = true;
    window.Promise.resolve().then(function () {
      called = false;
      fn();
    });
  };
}

function taskDebounce(fn) {
  var scheduled = false;
  return function () {
    if (!scheduled) {
      scheduled = true;
      setTimeout(function () {
        scheduled = false;
        fn();
      }, timeoutDuration);
    }
  };
}

var supportsMicroTasks = isBrowser && window.Promise;

/**
* Create a debounced version of a method, that's asynchronously deferred
* but called in the minimum time possible.
*
* @method
* @memberof Popper.Utils
* @argument {Function} fn
* @returns {Function}
*/
var debounce = supportsMicroTasks ? microtaskDebounce : taskDebounce;

/**
 * Check if the given variable is a function
 * @method
 * @memberof Popper.Utils
 * @argument {Any} functionToCheck - variable to check
 * @returns {Boolean} answer to: is a function?
 */
function isFunction(functionToCheck) {
  var getType = {};
  return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}

/**
 * Get CSS computed property of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Eement} element
 * @argument {String} property
 */
function getStyleComputedProperty(element, property) {
  if (element.nodeType !== 1) {
    return [];
  }
  // NOTE: 1 DOM access here
  var window = element.ownerDocument.defaultView;
  var css = window.getComputedStyle(element, null);
  return property ? css[property] : css;
}

/**
 * Returns the parentNode or the host of the element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} parent
 */
function getParentNode(element) {
  if (element.nodeName === 'HTML') {
    return element;
  }
  return element.parentNode || element.host;
}

/**
 * Returns the scrolling parent of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} scroll parent
 */
function getScrollParent(element) {
  // Return body, `getScroll` will take care to get the correct `scrollTop` from it
  if (!element) {
    return document.body;
  }

  switch (element.nodeName) {
    case 'HTML':
    case 'BODY':
      return element.ownerDocument.body;
    case '#document':
      return element.body;
  }

  // Firefox want us to check `-x` and `-y` variations as well

  var _getStyleComputedProp = getStyleComputedProperty(element),
      overflow = _getStyleComputedProp.overflow,
      overflowX = _getStyleComputedProp.overflowX,
      overflowY = _getStyleComputedProp.overflowY;

  if (/(auto|scroll|overlay)/.test(overflow + overflowY + overflowX)) {
    return element;
  }

  return getScrollParent(getParentNode(element));
}

var isIE11 = isBrowser && !!(window.MSInputMethodContext && document.documentMode);
var isIE10 = isBrowser && /MSIE 10/.test(navigator.userAgent);

/**
 * Determines if the browser is Internet Explorer
 * @method
 * @memberof Popper.Utils
 * @param {Number} version to check
 * @returns {Boolean} isIE
 */
function isIE(version) {
  if (version === 11) {
    return isIE11;
  }
  if (version === 10) {
    return isIE10;
  }
  return isIE11 || isIE10;
}

/**
 * Returns the offset parent of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} offset parent
 */
function getOffsetParent(element) {
  if (!element) {
    return document.documentElement;
  }

  var noOffsetParent = isIE(10) ? document.body : null;

  // NOTE: 1 DOM access here
  var offsetParent = element.offsetParent || null;
  // Skip hidden elements which don't have an offsetParent
  while (offsetParent === noOffsetParent && element.nextElementSibling) {
    offsetParent = (element = element.nextElementSibling).offsetParent;
  }

  var nodeName = offsetParent && offsetParent.nodeName;

  if (!nodeName || nodeName === 'BODY' || nodeName === 'HTML') {
    return element ? element.ownerDocument.documentElement : document.documentElement;
  }

  // .offsetParent will return the closest TH, TD or TABLE in case
  // no offsetParent is present, I hate this job...
  if (['TH', 'TD', 'TABLE'].indexOf(offsetParent.nodeName) !== -1 && getStyleComputedProperty(offsetParent, 'position') === 'static') {
    return getOffsetParent(offsetParent);
  }

  return offsetParent;
}

function isOffsetContainer(element) {
  var nodeName = element.nodeName;

  if (nodeName === 'BODY') {
    return false;
  }
  return nodeName === 'HTML' || getOffsetParent(element.firstElementChild) === element;
}

/**
 * Finds the root node (document, shadowDOM root) of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} node
 * @returns {Element} root node
 */
function getRoot(node) {
  if (node.parentNode !== null) {
    return getRoot(node.parentNode);
  }

  return node;
}

/**
 * Finds the offset parent common to the two provided nodes
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element1
 * @argument {Element} element2
 * @returns {Element} common offset parent
 */
function findCommonOffsetParent(element1, element2) {
  // This check is needed to avoid errors in case one of the elements isn't defined for any reason
  if (!element1 || !element1.nodeType || !element2 || !element2.nodeType) {
    return document.documentElement;
  }

  // Here we make sure to give as "start" the element that comes first in the DOM
  var order = element1.compareDocumentPosition(element2) & Node.DOCUMENT_POSITION_FOLLOWING;
  var start = order ? element1 : element2;
  var end = order ? element2 : element1;

  // Get common ancestor container
  var range = document.createRange();
  range.setStart(start, 0);
  range.setEnd(end, 0);
  var commonAncestorContainer = range.commonAncestorContainer;

  // Both nodes are inside #document

  if (element1 !== commonAncestorContainer && element2 !== commonAncestorContainer || start.contains(end)) {
    if (isOffsetContainer(commonAncestorContainer)) {
      return commonAncestorContainer;
    }

    return getOffsetParent(commonAncestorContainer);
  }

  // one of the nodes is inside shadowDOM, find which one
  var element1root = getRoot(element1);
  if (element1root.host) {
    return findCommonOffsetParent(element1root.host, element2);
  } else {
    return findCommonOffsetParent(element1, getRoot(element2).host);
  }
}

/**
 * Gets the scroll value of the given element in the given side (top and left)
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @argument {String} side `top` or `left`
 * @returns {number} amount of scrolled pixels
 */
function getScroll(element) {
  var side = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'top';

  var upperSide = side === 'top' ? 'scrollTop' : 'scrollLeft';
  var nodeName = element.nodeName;

  if (nodeName === 'BODY' || nodeName === 'HTML') {
    var html = element.ownerDocument.documentElement;
    var scrollingElement = element.ownerDocument.scrollingElement || html;
    return scrollingElement[upperSide];
  }

  return element[upperSide];
}

/*
 * Sum or subtract the element scroll values (left and top) from a given rect object
 * @method
 * @memberof Popper.Utils
 * @param {Object} rect - Rect object you want to change
 * @param {HTMLElement} element - The element from the function reads the scroll values
 * @param {Boolean} subtract - set to true if you want to subtract the scroll values
 * @return {Object} rect - The modifier rect object
 */
function includeScroll(rect, element) {
  var subtract = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

  var scrollTop = getScroll(element, 'top');
  var scrollLeft = getScroll(element, 'left');
  var modifier = subtract ? -1 : 1;
  rect.top += scrollTop * modifier;
  rect.bottom += scrollTop * modifier;
  rect.left += scrollLeft * modifier;
  rect.right += scrollLeft * modifier;
  return rect;
}

/*
 * Helper to detect borders of a given element
 * @method
 * @memberof Popper.Utils
 * @param {CSSStyleDeclaration} styles
 * Result of `getStyleComputedProperty` on the given element
 * @param {String} axis - `x` or `y`
 * @return {number} borders - The borders size of the given axis
 */

function getBordersSize(styles, axis) {
  var sideA = axis === 'x' ? 'Left' : 'Top';
  var sideB = sideA === 'Left' ? 'Right' : 'Bottom';

  return parseFloat(styles['border' + sideA + 'Width'], 10) + parseFloat(styles['border' + sideB + 'Width'], 10);
}

function getSize(axis, body, html, computedStyle) {
  return Math.max(body['offset' + axis], body['scroll' + axis], html['client' + axis], html['offset' + axis], html['scroll' + axis], isIE(10) ? parseInt(html['offset' + axis]) + parseInt(computedStyle['margin' + (axis === 'Height' ? 'Top' : 'Left')]) + parseInt(computedStyle['margin' + (axis === 'Height' ? 'Bottom' : 'Right')]) : 0);
}

function getWindowSizes(document) {
  var body = document.body;
  var html = document.documentElement;
  var computedStyle = isIE(10) && getComputedStyle(html);

  return {
    height: getSize('Height', body, html, computedStyle),
    width: getSize('Width', body, html, computedStyle)
  };
}

var classCallCheck = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

var createClass = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();





var defineProperty = function (obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
};

var _extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];

    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }

  return target;
};

/**
 * Given element offsets, generate an output similar to getBoundingClientRect
 * @method
 * @memberof Popper.Utils
 * @argument {Object} offsets
 * @returns {Object} ClientRect like output
 */
function getClientRect(offsets) {
  return _extends({}, offsets, {
    right: offsets.left + offsets.width,
    bottom: offsets.top + offsets.height
  });
}

/**
 * Get bounding client rect of given element
 * @method
 * @memberof Popper.Utils
 * @param {HTMLElement} element
 * @return {Object} client rect
 */
function getBoundingClientRect(element) {
  var rect = {};

  // IE10 10 FIX: Please, don't ask, the element isn't
  // considered in DOM in some circumstances...
  // This isn't reproducible in IE10 compatibility mode of IE11
  try {
    if (isIE(10)) {
      rect = element.getBoundingClientRect();
      var scrollTop = getScroll(element, 'top');
      var scrollLeft = getScroll(element, 'left');
      rect.top += scrollTop;
      rect.left += scrollLeft;
      rect.bottom += scrollTop;
      rect.right += scrollLeft;
    } else {
      rect = element.getBoundingClientRect();
    }
  } catch (e) {}

  var result = {
    left: rect.left,
    top: rect.top,
    width: rect.right - rect.left,
    height: rect.bottom - rect.top
  };

  // subtract scrollbar size from sizes
  var sizes = element.nodeName === 'HTML' ? getWindowSizes(element.ownerDocument) : {};
  var width = sizes.width || element.clientWidth || result.right - result.left;
  var height = sizes.height || element.clientHeight || result.bottom - result.top;

  var horizScrollbar = element.offsetWidth - width;
  var vertScrollbar = element.offsetHeight - height;

  // if an hypothetical scrollbar is detected, we must be sure it's not a `border`
  // we make this check conditional for performance reasons
  if (horizScrollbar || vertScrollbar) {
    var styles = getStyleComputedProperty(element);
    horizScrollbar -= getBordersSize(styles, 'x');
    vertScrollbar -= getBordersSize(styles, 'y');

    result.width -= horizScrollbar;
    result.height -= vertScrollbar;
  }

  return getClientRect(result);
}

function getOffsetRectRelativeToArbitraryNode(children, parent) {
  var fixedPosition = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

  var isIE10 = isIE(10);
  var isHTML = parent.nodeName === 'HTML';
  var childrenRect = getBoundingClientRect(children);
  var parentRect = getBoundingClientRect(parent);
  var scrollParent = getScrollParent(children);

  var styles = getStyleComputedProperty(parent);
  var borderTopWidth = parseFloat(styles.borderTopWidth, 10);
  var borderLeftWidth = parseFloat(styles.borderLeftWidth, 10);

  // In cases where the parent is fixed, we must ignore negative scroll in offset calc
  if (fixedPosition && isHTML) {
    parentRect.top = Math.max(parentRect.top, 0);
    parentRect.left = Math.max(parentRect.left, 0);
  }
  var offsets = getClientRect({
    top: childrenRect.top - parentRect.top - borderTopWidth,
    left: childrenRect.left - parentRect.left - borderLeftWidth,
    width: childrenRect.width,
    height: childrenRect.height
  });
  offsets.marginTop = 0;
  offsets.marginLeft = 0;

  // Subtract margins of documentElement in case it's being used as parent
  // we do this only on HTML because it's the only element that behaves
  // differently when margins are applied to it. The margins are included in
  // the box of the documentElement, in the other cases not.
  if (!isIE10 && isHTML) {
    var marginTop = parseFloat(styles.marginTop, 10);
    var marginLeft = parseFloat(styles.marginLeft, 10);

    offsets.top -= borderTopWidth - marginTop;
    offsets.bottom -= borderTopWidth - marginTop;
    offsets.left -= borderLeftWidth - marginLeft;
    offsets.right -= borderLeftWidth - marginLeft;

    // Attach marginTop and marginLeft because in some circumstances we may need them
    offsets.marginTop = marginTop;
    offsets.marginLeft = marginLeft;
  }

  if (isIE10 && !fixedPosition ? parent.contains(scrollParent) : parent === scrollParent && scrollParent.nodeName !== 'BODY') {
    offsets = includeScroll(offsets, parent);
  }

  return offsets;
}

function getViewportOffsetRectRelativeToArtbitraryNode(element) {
  var excludeScroll = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  var html = element.ownerDocument.documentElement;
  var relativeOffset = getOffsetRectRelativeToArbitraryNode(element, html);
  var width = Math.max(html.clientWidth, window.innerWidth || 0);
  var height = Math.max(html.clientHeight, window.innerHeight || 0);

  var scrollTop = !excludeScroll ? getScroll(html) : 0;
  var scrollLeft = !excludeScroll ? getScroll(html, 'left') : 0;

  var offset = {
    top: scrollTop - relativeOffset.top + relativeOffset.marginTop,
    left: scrollLeft - relativeOffset.left + relativeOffset.marginLeft,
    width: width,
    height: height
  };

  return getClientRect(offset);
}

/**
 * Check if the given element is fixed or is inside a fixed parent
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @argument {Element} customContainer
 * @returns {Boolean} answer to "isFixed?"
 */
function isFixed(element) {
  var nodeName = element.nodeName;
  if (nodeName === 'BODY' || nodeName === 'HTML') {
    return false;
  }
  if (getStyleComputedProperty(element, 'position') === 'fixed') {
    return true;
  }
  var parentNode = getParentNode(element);
  if (!parentNode) {
    return false;
  }
  return isFixed(parentNode);
}

/**
 * Finds the first parent of an element that has a transformed property defined
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} first transformed parent or documentElement
 */

function getFixedPositionOffsetParent(element) {
  // This check is needed to avoid errors in case one of the elements isn't defined for any reason
  if (!element || !element.parentElement || isIE()) {
    return document.documentElement;
  }
  var el = element.parentElement;
  while (el && getStyleComputedProperty(el, 'transform') === 'none') {
    el = el.parentElement;
  }
  return el || document.documentElement;
}

/**
 * Computed the boundaries limits and return them
 * @method
 * @memberof Popper.Utils
 * @param {HTMLElement} popper
 * @param {HTMLElement} reference
 * @param {number} padding
 * @param {HTMLElement} boundariesElement - Element used to define the boundaries
 * @param {Boolean} fixedPosition - Is in fixed position mode
 * @returns {Object} Coordinates of the boundaries
 */
function getBoundaries(popper, reference, padding, boundariesElement) {
  var fixedPosition = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : false;

  // NOTE: 1 DOM access here

  var boundaries = { top: 0, left: 0 };
  var offsetParent = fixedPosition ? getFixedPositionOffsetParent(popper) : findCommonOffsetParent(popper, reference);

  // Handle viewport case
  if (boundariesElement === 'viewport') {
    boundaries = getViewportOffsetRectRelativeToArtbitraryNode(offsetParent, fixedPosition);
  } else {
    // Handle other cases based on DOM element used as boundaries
    var boundariesNode = void 0;
    if (boundariesElement === 'scrollParent') {
      boundariesNode = getScrollParent(getParentNode(reference));
      if (boundariesNode.nodeName === 'BODY') {
        boundariesNode = popper.ownerDocument.documentElement;
      }
    } else if (boundariesElement === 'window') {
      boundariesNode = popper.ownerDocument.documentElement;
    } else {
      boundariesNode = boundariesElement;
    }

    var offsets = getOffsetRectRelativeToArbitraryNode(boundariesNode, offsetParent, fixedPosition);

    // In case of HTML, we need a different computation
    if (boundariesNode.nodeName === 'HTML' && !isFixed(offsetParent)) {
      var _getWindowSizes = getWindowSizes(popper.ownerDocument),
          height = _getWindowSizes.height,
          width = _getWindowSizes.width;

      boundaries.top += offsets.top - offsets.marginTop;
      boundaries.bottom = height + offsets.top;
      boundaries.left += offsets.left - offsets.marginLeft;
      boundaries.right = width + offsets.left;
    } else {
      // for all the other DOM elements, this one is good
      boundaries = offsets;
    }
  }

  // Add paddings
  padding = padding || 0;
  var isPaddingNumber = typeof padding === 'number';
  boundaries.left += isPaddingNumber ? padding : padding.left || 0;
  boundaries.top += isPaddingNumber ? padding : padding.top || 0;
  boundaries.right -= isPaddingNumber ? padding : padding.right || 0;
  boundaries.bottom -= isPaddingNumber ? padding : padding.bottom || 0;

  return boundaries;
}

function getArea(_ref) {
  var width = _ref.width,
      height = _ref.height;

  return width * height;
}

/**
 * Utility used to transform the `auto` placement to the placement with more
 * available space.
 * @method
 * @memberof Popper.Utils
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function computeAutoPlacement(placement, refRect, popper, reference, boundariesElement) {
  var padding = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : 0;

  if (placement.indexOf('auto') === -1) {
    return placement;
  }

  var boundaries = getBoundaries(popper, reference, padding, boundariesElement);

  var rects = {
    top: {
      width: boundaries.width,
      height: refRect.top - boundaries.top
    },
    right: {
      width: boundaries.right - refRect.right,
      height: boundaries.height
    },
    bottom: {
      width: boundaries.width,
      height: boundaries.bottom - refRect.bottom
    },
    left: {
      width: refRect.left - boundaries.left,
      height: boundaries.height
    }
  };

  var sortedAreas = Object.keys(rects).map(function (key) {
    return _extends({
      key: key
    }, rects[key], {
      area: getArea(rects[key])
    });
  }).sort(function (a, b) {
    return b.area - a.area;
  });

  var filteredAreas = sortedAreas.filter(function (_ref2) {
    var width = _ref2.width,
        height = _ref2.height;
    return width >= popper.clientWidth && height >= popper.clientHeight;
  });

  var computedPlacement = filteredAreas.length > 0 ? filteredAreas[0].key : sortedAreas[0].key;

  var variation = placement.split('-')[1];

  return computedPlacement + (variation ? '-' + variation : '');
}

/**
 * Get offsets to the reference element
 * @method
 * @memberof Popper.Utils
 * @param {Object} state
 * @param {Element} popper - the popper element
 * @param {Element} reference - the reference element (the popper will be relative to this)
 * @param {Element} fixedPosition - is in fixed position mode
 * @returns {Object} An object containing the offsets which will be applied to the popper
 */
function getReferenceOffsets(state, popper, reference) {
  var fixedPosition = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;

  var commonOffsetParent = fixedPosition ? getFixedPositionOffsetParent(popper) : findCommonOffsetParent(popper, reference);
  return getOffsetRectRelativeToArbitraryNode(reference, commonOffsetParent, fixedPosition);
}

/**
 * Get the outer sizes of the given element (offset size + margins)
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Object} object containing width and height properties
 */
function getOuterSizes(element) {
  var window = element.ownerDocument.defaultView;
  var styles = window.getComputedStyle(element);
  var x = parseFloat(styles.marginTop || 0) + parseFloat(styles.marginBottom || 0);
  var y = parseFloat(styles.marginLeft || 0) + parseFloat(styles.marginRight || 0);
  var result = {
    width: element.offsetWidth + y,
    height: element.offsetHeight + x
  };
  return result;
}

/**
 * Get the opposite placement of the given one
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement
 * @returns {String} flipped placement
 */
function getOppositePlacement(placement) {
  var hash = { left: 'right', right: 'left', bottom: 'top', top: 'bottom' };
  return placement.replace(/left|right|bottom|top/g, function (matched) {
    return hash[matched];
  });
}

/**
 * Get offsets to the popper
 * @method
 * @memberof Popper.Utils
 * @param {Object} position - CSS position the Popper will get applied
 * @param {HTMLElement} popper - the popper element
 * @param {Object} referenceOffsets - the reference offsets (the popper will be relative to this)
 * @param {String} placement - one of the valid placement options
 * @returns {Object} popperOffsets - An object containing the offsets which will be applied to the popper
 */
function getPopperOffsets(popper, referenceOffsets, placement) {
  placement = placement.split('-')[0];

  // Get popper node sizes
  var popperRect = getOuterSizes(popper);

  // Add position, width and height to our offsets object
  var popperOffsets = {
    width: popperRect.width,
    height: popperRect.height
  };

  // depending by the popper placement we have to compute its offsets slightly differently
  var isHoriz = ['right', 'left'].indexOf(placement) !== -1;
  var mainSide = isHoriz ? 'top' : 'left';
  var secondarySide = isHoriz ? 'left' : 'top';
  var measurement = isHoriz ? 'height' : 'width';
  var secondaryMeasurement = !isHoriz ? 'height' : 'width';

  popperOffsets[mainSide] = referenceOffsets[mainSide] + referenceOffsets[measurement] / 2 - popperRect[measurement] / 2;
  if (placement === secondarySide) {
    popperOffsets[secondarySide] = referenceOffsets[secondarySide] - popperRect[secondaryMeasurement];
  } else {
    popperOffsets[secondarySide] = referenceOffsets[getOppositePlacement(secondarySide)];
  }

  return popperOffsets;
}

/**
 * Mimics the `find` method of Array
 * @method
 * @memberof Popper.Utils
 * @argument {Array} arr
 * @argument prop
 * @argument value
 * @returns index or -1
 */
function find(arr, check) {
  // use native find if supported
  if (Array.prototype.find) {
    return arr.find(check);
  }

  // use `filter` to obtain the same behavior of `find`
  return arr.filter(check)[0];
}

/**
 * Return the index of the matching object
 * @method
 * @memberof Popper.Utils
 * @argument {Array} arr
 * @argument prop
 * @argument value
 * @returns index or -1
 */
function findIndex(arr, prop, value) {
  // use native findIndex if supported
  if (Array.prototype.findIndex) {
    return arr.findIndex(function (cur) {
      return cur[prop] === value;
    });
  }

  // use `find` + `indexOf` if `findIndex` isn't supported
  var match = find(arr, function (obj) {
    return obj[prop] === value;
  });
  return arr.indexOf(match);
}

/**
 * Loop trough the list of modifiers and run them in order,
 * each of them will then edit the data object.
 * @method
 * @memberof Popper.Utils
 * @param {dataObject} data
 * @param {Array} modifiers
 * @param {String} ends - Optional modifier name used as stopper
 * @returns {dataObject}
 */
function runModifiers(modifiers, data, ends) {
  var modifiersToRun = ends === undefined ? modifiers : modifiers.slice(0, findIndex(modifiers, 'name', ends));

  modifiersToRun.forEach(function (modifier) {
    if (modifier['function']) {
      // eslint-disable-line dot-notation
      console.warn('`modifier.function` is deprecated, use `modifier.fn`!');
    }
    var fn = modifier['function'] || modifier.fn; // eslint-disable-line dot-notation
    if (modifier.enabled && isFunction(fn)) {
      // Add properties to offsets to make them a complete clientRect object
      // we do this before each modifier to make sure the previous one doesn't
      // mess with these values
      data.offsets.popper = getClientRect(data.offsets.popper);
      data.offsets.reference = getClientRect(data.offsets.reference);

      data = fn(data, modifier);
    }
  });

  return data;
}

/**
 * Updates the position of the popper, computing the new offsets and applying
 * the new style.<br />
 * Prefer `scheduleUpdate` over `update` because of performance reasons.
 * @method
 * @memberof Popper
 */
function update() {
  // if popper is destroyed, don't perform any further update
  if (this.state.isDestroyed) {
    return;
  }

  var data = {
    instance: this,
    styles: {},
    arrowStyles: {},
    attributes: {},
    flipped: false,
    offsets: {}
  };

  // compute reference element offsets
  data.offsets.reference = getReferenceOffsets(this.state, this.popper, this.reference, this.options.positionFixed);

  // compute auto placement, store placement inside the data object,
  // modifiers will be able to edit `placement` if needed
  // and refer to originalPlacement to know the original value
  data.placement = computeAutoPlacement(this.options.placement, data.offsets.reference, this.popper, this.reference, this.options.modifiers.flip.boundariesElement, this.options.modifiers.flip.padding);

  // store the computed placement inside `originalPlacement`
  data.originalPlacement = data.placement;

  data.positionFixed = this.options.positionFixed;

  // compute the popper offsets
  data.offsets.popper = getPopperOffsets(this.popper, data.offsets.reference, data.placement);

  data.offsets.popper.position = this.options.positionFixed ? 'fixed' : 'absolute';

  // run the modifiers
  data = runModifiers(this.modifiers, data);

  // the first `update` will call `onCreate` callback
  // the other ones will call `onUpdate` callback
  if (!this.state.isCreated) {
    this.state.isCreated = true;
    this.options.onCreate(data);
  } else {
    this.options.onUpdate(data);
  }
}

/**
 * Helper used to know if the given modifier is enabled.
 * @method
 * @memberof Popper.Utils
 * @returns {Boolean}
 */
function isModifierEnabled(modifiers, modifierName) {
  return modifiers.some(function (_ref) {
    var name = _ref.name,
        enabled = _ref.enabled;
    return enabled && name === modifierName;
  });
}

/**
 * Get the prefixed supported property name
 * @method
 * @memberof Popper.Utils
 * @argument {String} property (camelCase)
 * @returns {String} prefixed property (camelCase or PascalCase, depending on the vendor prefix)
 */
function getSupportedPropertyName(property) {
  var prefixes = [false, 'ms', 'Webkit', 'Moz', 'O'];
  var upperProp = property.charAt(0).toUpperCase() + property.slice(1);

  for (var i = 0; i < prefixes.length; i++) {
    var prefix = prefixes[i];
    var toCheck = prefix ? '' + prefix + upperProp : property;
    if (typeof document.body.style[toCheck] !== 'undefined') {
      return toCheck;
    }
  }
  return null;
}

/**
 * Destroys the popper.
 * @method
 * @memberof Popper
 */
function destroy() {
  this.state.isDestroyed = true;

  // touch DOM only if `applyStyle` modifier is enabled
  if (isModifierEnabled(this.modifiers, 'applyStyle')) {
    this.popper.removeAttribute('x-placement');
    this.popper.style.position = '';
    this.popper.style.top = '';
    this.popper.style.left = '';
    this.popper.style.right = '';
    this.popper.style.bottom = '';
    this.popper.style.willChange = '';
    this.popper.style[getSupportedPropertyName('transform')] = '';
  }

  this.disableEventListeners();

  // remove the popper if user explicity asked for the deletion on destroy
  // do not use `remove` because IE11 doesn't support it
  if (this.options.removeOnDestroy) {
    this.popper.parentNode.removeChild(this.popper);
  }
  return this;
}

/**
 * Get the window associated with the element
 * @argument {Element} element
 * @returns {Window}
 */
function getWindow(element) {
  var ownerDocument = element.ownerDocument;
  return ownerDocument ? ownerDocument.defaultView : window;
}

function attachToScrollParents(scrollParent, event, callback, scrollParents) {
  var isBody = scrollParent.nodeName === 'BODY';
  var target = isBody ? scrollParent.ownerDocument.defaultView : scrollParent;
  target.addEventListener(event, callback, { passive: true });

  if (!isBody) {
    attachToScrollParents(getScrollParent(target.parentNode), event, callback, scrollParents);
  }
  scrollParents.push(target);
}

/**
 * Setup needed event listeners used to update the popper position
 * @method
 * @memberof Popper.Utils
 * @private
 */
function setupEventListeners(reference, options, state, updateBound) {
  // Resize event listener on window
  state.updateBound = updateBound;
  getWindow(reference).addEventListener('resize', state.updateBound, { passive: true });

  // Scroll event listener on scroll parents
  var scrollElement = getScrollParent(reference);
  attachToScrollParents(scrollElement, 'scroll', state.updateBound, state.scrollParents);
  state.scrollElement = scrollElement;
  state.eventsEnabled = true;

  return state;
}

/**
 * It will add resize/scroll events and start recalculating
 * position of the popper element when they are triggered.
 * @method
 * @memberof Popper
 */
function enableEventListeners() {
  if (!this.state.eventsEnabled) {
    this.state = setupEventListeners(this.reference, this.options, this.state, this.scheduleUpdate);
  }
}

/**
 * Remove event listeners used to update the popper position
 * @method
 * @memberof Popper.Utils
 * @private
 */
function removeEventListeners(reference, state) {
  // Remove resize event listener on window
  getWindow(reference).removeEventListener('resize', state.updateBound);

  // Remove scroll event listener on scroll parents
  state.scrollParents.forEach(function (target) {
    target.removeEventListener('scroll', state.updateBound);
  });

  // Reset state
  state.updateBound = null;
  state.scrollParents = [];
  state.scrollElement = null;
  state.eventsEnabled = false;
  return state;
}

/**
 * It will remove resize/scroll events and won't recalculate popper position
 * when they are triggered. It also won't trigger `onUpdate` callback anymore,
 * unless you call `update` method manually.
 * @method
 * @memberof Popper
 */
function disableEventListeners() {
  if (this.state.eventsEnabled) {
    cancelAnimationFrame(this.scheduleUpdate);
    this.state = removeEventListeners(this.reference, this.state);
  }
}

/**
 * Tells if a given input is a number
 * @method
 * @memberof Popper.Utils
 * @param {*} input to check
 * @return {Boolean}
 */
function isNumeric(n) {
  return n !== '' && !isNaN(parseFloat(n)) && isFinite(n);
}

/**
 * Set the style to the given popper
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element - Element to apply the style to
 * @argument {Object} styles
 * Object with a list of properties and values which will be applied to the element
 */
function setStyles(element, styles) {
  Object.keys(styles).forEach(function (prop) {
    var unit = '';
    // add unit if the value is numeric and is one of the following
    if (['width', 'height', 'top', 'right', 'bottom', 'left'].indexOf(prop) !== -1 && isNumeric(styles[prop])) {
      unit = 'px';
    }
    element.style[prop] = styles[prop] + unit;
  });
}

/**
 * Set the attributes to the given popper
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element - Element to apply the attributes to
 * @argument {Object} styles
 * Object with a list of properties and values which will be applied to the element
 */
function setAttributes(element, attributes) {
  Object.keys(attributes).forEach(function (prop) {
    var value = attributes[prop];
    if (value !== false) {
      element.setAttribute(prop, attributes[prop]);
    } else {
      element.removeAttribute(prop);
    }
  });
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} data.styles - List of style properties - values to apply to popper element
 * @argument {Object} data.attributes - List of attribute properties - values to apply to popper element
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The same data object
 */
function applyStyle(data) {
  // any property present in `data.styles` will be applied to the popper,
  // in this way we can make the 3rd party modifiers add custom styles to it
  // Be aware, modifiers could override the properties defined in the previous
  // lines of this modifier!
  setStyles(data.instance.popper, data.styles);

  // any property present in `data.attributes` will be applied to the popper,
  // they will be set as HTML attributes of the element
  setAttributes(data.instance.popper, data.attributes);

  // if arrowElement is defined and arrowStyles has some properties
  if (data.arrowElement && Object.keys(data.arrowStyles).length) {
    setStyles(data.arrowElement, data.arrowStyles);
  }

  return data;
}

/**
 * Set the x-placement attribute before everything else because it could be used
 * to add margins to the popper margins needs to be calculated to get the
 * correct popper offsets.
 * @method
 * @memberof Popper.modifiers
 * @param {HTMLElement} reference - The reference element used to position the popper
 * @param {HTMLElement} popper - The HTML element used as popper
 * @param {Object} options - Popper.js options
 */
function applyStyleOnLoad(reference, popper, options, modifierOptions, state) {
  // compute reference element offsets
  var referenceOffsets = getReferenceOffsets(state, popper, reference, options.positionFixed);

  // compute auto placement, store placement inside the data object,
  // modifiers will be able to edit `placement` if needed
  // and refer to originalPlacement to know the original value
  var placement = computeAutoPlacement(options.placement, referenceOffsets, popper, reference, options.modifiers.flip.boundariesElement, options.modifiers.flip.padding);

  popper.setAttribute('x-placement', placement);

  // Apply `position` to popper before anything else because
  // without the position applied we can't guarantee correct computations
  setStyles(popper, { position: options.positionFixed ? 'fixed' : 'absolute' });

  return options;
}

/**
 * @function
 * @memberof Popper.Utils
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Boolean} shouldRound - If the offsets should be rounded at all
 * @returns {Object} The popper's position offsets rounded
 *
 * The tale of pixel-perfect positioning. It's still not 100% perfect, but as
 * good as it can be within reason.
 * Discussion here: https://github.com/FezVrasta/popper.js/pull/715
 *
 * Low DPI screens cause a popper to be blurry if not using full pixels (Safari
 * as well on High DPI screens).
 *
 * Firefox prefers no rounding for positioning and does not have blurriness on
 * high DPI screens.
 *
 * Only horizontal placement and left/right values need to be considered.
 */
function getRoundedOffsets(data, shouldRound) {
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;
  var round = Math.round,
      floor = Math.floor;

  var noRound = function noRound(v) {
    return v;
  };

  var referenceWidth = round(reference.width);
  var popperWidth = round(popper.width);

  var isVertical = ['left', 'right'].indexOf(data.placement) !== -1;
  var isVariation = data.placement.indexOf('-') !== -1;
  var sameWidthParity = referenceWidth % 2 === popperWidth % 2;
  var bothOddWidth = referenceWidth % 2 === 1 && popperWidth % 2 === 1;

  var horizontalToInteger = !shouldRound ? noRound : isVertical || isVariation || sameWidthParity ? round : floor;
  var verticalToInteger = !shouldRound ? noRound : round;

  return {
    left: horizontalToInteger(bothOddWidth && !isVariation && shouldRound ? popper.left - 1 : popper.left),
    top: verticalToInteger(popper.top),
    bottom: verticalToInteger(popper.bottom),
    right: horizontalToInteger(popper.right)
  };
}

var isFirefox = isBrowser && /Firefox/i.test(navigator.userAgent);

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function computeStyle(data, options) {
  var x = options.x,
      y = options.y;
  var popper = data.offsets.popper;

  // Remove this legacy support in Popper.js v2

  var legacyGpuAccelerationOption = find(data.instance.modifiers, function (modifier) {
    return modifier.name === 'applyStyle';
  }).gpuAcceleration;
  if (legacyGpuAccelerationOption !== undefined) {
    console.warn('WARNING: `gpuAcceleration` option moved to `computeStyle` modifier and will not be supported in future versions of Popper.js!');
  }
  var gpuAcceleration = legacyGpuAccelerationOption !== undefined ? legacyGpuAccelerationOption : options.gpuAcceleration;

  var offsetParent = getOffsetParent(data.instance.popper);
  var offsetParentRect = getBoundingClientRect(offsetParent);

  // Styles
  var styles = {
    position: popper.position
  };

  var offsets = getRoundedOffsets(data, window.devicePixelRatio < 2 || !isFirefox);

  var sideA = x === 'bottom' ? 'top' : 'bottom';
  var sideB = y === 'right' ? 'left' : 'right';

  // if gpuAcceleration is set to `true` and transform is supported,
  //  we use `translate3d` to apply the position to the popper we
  // automatically use the supported prefixed version if needed
  var prefixedProperty = getSupportedPropertyName('transform');

  // now, let's make a step back and look at this code closely (wtf?)
  // If the content of the popper grows once it's been positioned, it
  // may happen that the popper gets misplaced because of the new content
  // overflowing its reference element
  // To avoid this problem, we provide two options (x and y), which allow
  // the consumer to define the offset origin.
  // If we position a popper on top of a reference element, we can set
  // `x` to `top` to make the popper grow towards its top instead of
  // its bottom.
  var left = void 0,
      top = void 0;
  if (sideA === 'bottom') {
    // when offsetParent is <html> the positioning is relative to the bottom of the screen (excluding the scrollbar)
    // and not the bottom of the html element
    if (offsetParent.nodeName === 'HTML') {
      top = -offsetParent.clientHeight + offsets.bottom;
    } else {
      top = -offsetParentRect.height + offsets.bottom;
    }
  } else {
    top = offsets.top;
  }
  if (sideB === 'right') {
    if (offsetParent.nodeName === 'HTML') {
      left = -offsetParent.clientWidth + offsets.right;
    } else {
      left = -offsetParentRect.width + offsets.right;
    }
  } else {
    left = offsets.left;
  }
  if (gpuAcceleration && prefixedProperty) {
    styles[prefixedProperty] = 'translate3d(' + left + 'px, ' + top + 'px, 0)';
    styles[sideA] = 0;
    styles[sideB] = 0;
    styles.willChange = 'transform';
  } else {
    // othwerise, we use the standard `top`, `left`, `bottom` and `right` properties
    var invertTop = sideA === 'bottom' ? -1 : 1;
    var invertLeft = sideB === 'right' ? -1 : 1;
    styles[sideA] = top * invertTop;
    styles[sideB] = left * invertLeft;
    styles.willChange = sideA + ', ' + sideB;
  }

  // Attributes
  var attributes = {
    'x-placement': data.placement
  };

  // Update `data` attributes, styles and arrowStyles
  data.attributes = _extends({}, attributes, data.attributes);
  data.styles = _extends({}, styles, data.styles);
  data.arrowStyles = _extends({}, data.offsets.arrow, data.arrowStyles);

  return data;
}

/**
 * Helper used to know if the given modifier depends from another one.<br />
 * It checks if the needed modifier is listed and enabled.
 * @method
 * @memberof Popper.Utils
 * @param {Array} modifiers - list of modifiers
 * @param {String} requestingName - name of requesting modifier
 * @param {String} requestedName - name of requested modifier
 * @returns {Boolean}
 */
function isModifierRequired(modifiers, requestingName, requestedName) {
  var requesting = find(modifiers, function (_ref) {
    var name = _ref.name;
    return name === requestingName;
  });

  var isRequired = !!requesting && modifiers.some(function (modifier) {
    return modifier.name === requestedName && modifier.enabled && modifier.order < requesting.order;
  });

  if (!isRequired) {
    var _requesting = '`' + requestingName + '`';
    var requested = '`' + requestedName + '`';
    console.warn(requested + ' modifier is required by ' + _requesting + ' modifier in order to work, be sure to include it before ' + _requesting + '!');
  }
  return isRequired;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function arrow(data, options) {
  var _data$offsets$arrow;

  // arrow depends on keepTogether in order to work
  if (!isModifierRequired(data.instance.modifiers, 'arrow', 'keepTogether')) {
    return data;
  }

  var arrowElement = options.element;

  // if arrowElement is a string, suppose it's a CSS selector
  if (typeof arrowElement === 'string') {
    arrowElement = data.instance.popper.querySelector(arrowElement);

    // if arrowElement is not found, don't run the modifier
    if (!arrowElement) {
      return data;
    }
  } else {
    // if the arrowElement isn't a query selector we must check that the
    // provided DOM node is child of its popper node
    if (!data.instance.popper.contains(arrowElement)) {
      console.warn('WARNING: `arrow.element` must be child of its popper element!');
      return data;
    }
  }

  var placement = data.placement.split('-')[0];
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var isVertical = ['left', 'right'].indexOf(placement) !== -1;

  var len = isVertical ? 'height' : 'width';
  var sideCapitalized = isVertical ? 'Top' : 'Left';
  var side = sideCapitalized.toLowerCase();
  var altSide = isVertical ? 'left' : 'top';
  var opSide = isVertical ? 'bottom' : 'right';
  var arrowElementSize = getOuterSizes(arrowElement)[len];

  //
  // extends keepTogether behavior making sure the popper and its
  // reference have enough pixels in conjunction
  //

  // top/left side
  if (reference[opSide] - arrowElementSize < popper[side]) {
    data.offsets.popper[side] -= popper[side] - (reference[opSide] - arrowElementSize);
  }
  // bottom/right side
  if (reference[side] + arrowElementSize > popper[opSide]) {
    data.offsets.popper[side] += reference[side] + arrowElementSize - popper[opSide];
  }
  data.offsets.popper = getClientRect(data.offsets.popper);

  // compute center of the popper
  var center = reference[side] + reference[len] / 2 - arrowElementSize / 2;

  // Compute the sideValue using the updated popper offsets
  // take popper margin in account because we don't have this info available
  var css = getStyleComputedProperty(data.instance.popper);
  var popperMarginSide = parseFloat(css['margin' + sideCapitalized], 10);
  var popperBorderSide = parseFloat(css['border' + sideCapitalized + 'Width'], 10);
  var sideValue = center - data.offsets.popper[side] - popperMarginSide - popperBorderSide;

  // prevent arrowElement from being placed not contiguously to its popper
  sideValue = Math.max(Math.min(popper[len] - arrowElementSize, sideValue), 0);

  data.arrowElement = arrowElement;
  data.offsets.arrow = (_data$offsets$arrow = {}, defineProperty(_data$offsets$arrow, side, Math.round(sideValue)), defineProperty(_data$offsets$arrow, altSide, ''), _data$offsets$arrow);

  return data;
}

/**
 * Get the opposite placement variation of the given one
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement variation
 * @returns {String} flipped placement variation
 */
function getOppositeVariation(variation) {
  if (variation === 'end') {
    return 'start';
  } else if (variation === 'start') {
    return 'end';
  }
  return variation;
}

/**
 * List of accepted placements to use as values of the `placement` option.<br />
 * Valid placements are:
 * - `auto`
 * - `top`
 * - `right`
 * - `bottom`
 * - `left`
 *
 * Each placement can have a variation from this list:
 * - `-start`
 * - `-end`
 *
 * Variations are interpreted easily if you think of them as the left to right
 * written languages. Horizontally (`top` and `bottom`), `start` is left and `end`
 * is right.<br />
 * Vertically (`left` and `right`), `start` is top and `end` is bottom.
 *
 * Some valid examples are:
 * - `top-end` (on top of reference, right aligned)
 * - `right-start` (on right of reference, top aligned)
 * - `bottom` (on bottom, centered)
 * - `auto-end` (on the side with more space available, alignment depends by placement)
 *
 * @static
 * @type {Array}
 * @enum {String}
 * @readonly
 * @method placements
 * @memberof Popper
 */
var placements = ['auto-start', 'auto', 'auto-end', 'top-start', 'top', 'top-end', 'right-start', 'right', 'right-end', 'bottom-end', 'bottom', 'bottom-start', 'left-end', 'left', 'left-start'];

// Get rid of `auto` `auto-start` and `auto-end`
var validPlacements = placements.slice(3);

/**
 * Given an initial placement, returns all the subsequent placements
 * clockwise (or counter-clockwise).
 *
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement - A valid placement (it accepts variations)
 * @argument {Boolean} counter - Set to true to walk the placements counterclockwise
 * @returns {Array} placements including their variations
 */
function clockwise(placement) {
  var counter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  var index = validPlacements.indexOf(placement);
  var arr = validPlacements.slice(index + 1).concat(validPlacements.slice(0, index));
  return counter ? arr.reverse() : arr;
}

var BEHAVIORS = {
  FLIP: 'flip',
  CLOCKWISE: 'clockwise',
  COUNTERCLOCKWISE: 'counterclockwise'
};

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function flip(data, options) {
  // if `inner` modifier is enabled, we can't use the `flip` modifier
  if (isModifierEnabled(data.instance.modifiers, 'inner')) {
    return data;
  }

  if (data.flipped && data.placement === data.originalPlacement) {
    // seems like flip is trying to loop, probably there's not enough space on any of the flippable sides
    return data;
  }

  var boundaries = getBoundaries(data.instance.popper, data.instance.reference, options.padding, options.boundariesElement, data.positionFixed);

  var placement = data.placement.split('-')[0];
  var placementOpposite = getOppositePlacement(placement);
  var variation = data.placement.split('-')[1] || '';

  var flipOrder = [];

  switch (options.behavior) {
    case BEHAVIORS.FLIP:
      flipOrder = [placement, placementOpposite];
      break;
    case BEHAVIORS.CLOCKWISE:
      flipOrder = clockwise(placement);
      break;
    case BEHAVIORS.COUNTERCLOCKWISE:
      flipOrder = clockwise(placement, true);
      break;
    default:
      flipOrder = options.behavior;
  }

  flipOrder.forEach(function (step, index) {
    if (placement !== step || flipOrder.length === index + 1) {
      return data;
    }

    placement = data.placement.split('-')[0];
    placementOpposite = getOppositePlacement(placement);

    var popperOffsets = data.offsets.popper;
    var refOffsets = data.offsets.reference;

    // using floor because the reference offsets may contain decimals we are not going to consider here
    var floor = Math.floor;
    var overlapsRef = placement === 'left' && floor(popperOffsets.right) > floor(refOffsets.left) || placement === 'right' && floor(popperOffsets.left) < floor(refOffsets.right) || placement === 'top' && floor(popperOffsets.bottom) > floor(refOffsets.top) || placement === 'bottom' && floor(popperOffsets.top) < floor(refOffsets.bottom);

    var overflowsLeft = floor(popperOffsets.left) < floor(boundaries.left);
    var overflowsRight = floor(popperOffsets.right) > floor(boundaries.right);
    var overflowsTop = floor(popperOffsets.top) < floor(boundaries.top);
    var overflowsBottom = floor(popperOffsets.bottom) > floor(boundaries.bottom);

    var overflowsBoundaries = placement === 'left' && overflowsLeft || placement === 'right' && overflowsRight || placement === 'top' && overflowsTop || placement === 'bottom' && overflowsBottom;

    // flip the variation if required
    var isVertical = ['top', 'bottom'].indexOf(placement) !== -1;

    // flips variation if reference element overflows boundaries
    var flippedVariationByRef = !!options.flipVariations && (isVertical && variation === 'start' && overflowsLeft || isVertical && variation === 'end' && overflowsRight || !isVertical && variation === 'start' && overflowsTop || !isVertical && variation === 'end' && overflowsBottom);

    // flips variation if popper content overflows boundaries
    var flippedVariationByContent = !!options.flipVariationsByContent && (isVertical && variation === 'start' && overflowsRight || isVertical && variation === 'end' && overflowsLeft || !isVertical && variation === 'start' && overflowsBottom || !isVertical && variation === 'end' && overflowsTop);

    var flippedVariation = flippedVariationByRef || flippedVariationByContent;

    if (overlapsRef || overflowsBoundaries || flippedVariation) {
      // this boolean to detect any flip loop
      data.flipped = true;

      if (overlapsRef || overflowsBoundaries) {
        placement = flipOrder[index + 1];
      }

      if (flippedVariation) {
        variation = getOppositeVariation(variation);
      }

      data.placement = placement + (variation ? '-' + variation : '');

      // this object contains `position`, we want to preserve it along with
      // any additional property we may add in the future
      data.offsets.popper = _extends({}, data.offsets.popper, getPopperOffsets(data.instance.popper, data.offsets.reference, data.placement));

      data = runModifiers(data.instance.modifiers, data, 'flip');
    }
  });
  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function keepTogether(data) {
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var placement = data.placement.split('-')[0];
  var floor = Math.floor;
  var isVertical = ['top', 'bottom'].indexOf(placement) !== -1;
  var side = isVertical ? 'right' : 'bottom';
  var opSide = isVertical ? 'left' : 'top';
  var measurement = isVertical ? 'width' : 'height';

  if (popper[side] < floor(reference[opSide])) {
    data.offsets.popper[opSide] = floor(reference[opSide]) - popper[measurement];
  }
  if (popper[opSide] > floor(reference[side])) {
    data.offsets.popper[opSide] = floor(reference[side]);
  }

  return data;
}

/**
 * Converts a string containing value + unit into a px value number
 * @function
 * @memberof {modifiers~offset}
 * @private
 * @argument {String} str - Value + unit string
 * @argument {String} measurement - `height` or `width`
 * @argument {Object} popperOffsets
 * @argument {Object} referenceOffsets
 * @returns {Number|String}
 * Value in pixels, or original string if no values were extracted
 */
function toValue(str, measurement, popperOffsets, referenceOffsets) {
  // separate value from unit
  var split = str.match(/((?:\-|\+)?\d*\.?\d*)(.*)/);
  var value = +split[1];
  var unit = split[2];

  // If it's not a number it's an operator, I guess
  if (!value) {
    return str;
  }

  if (unit.indexOf('%') === 0) {
    var element = void 0;
    switch (unit) {
      case '%p':
        element = popperOffsets;
        break;
      case '%':
      case '%r':
      default:
        element = referenceOffsets;
    }

    var rect = getClientRect(element);
    return rect[measurement] / 100 * value;
  } else if (unit === 'vh' || unit === 'vw') {
    // if is a vh or vw, we calculate the size based on the viewport
    var size = void 0;
    if (unit === 'vh') {
      size = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    } else {
      size = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    }
    return size / 100 * value;
  } else {
    // if is an explicit pixel unit, we get rid of the unit and keep the value
    // if is an implicit unit, it's px, and we return just the value
    return value;
  }
}

/**
 * Parse an `offset` string to extrapolate `x` and `y` numeric offsets.
 * @function
 * @memberof {modifiers~offset}
 * @private
 * @argument {String} offset
 * @argument {Object} popperOffsets
 * @argument {Object} referenceOffsets
 * @argument {String} basePlacement
 * @returns {Array} a two cells array with x and y offsets in numbers
 */
function parseOffset(offset, popperOffsets, referenceOffsets, basePlacement) {
  var offsets = [0, 0];

  // Use height if placement is left or right and index is 0 otherwise use width
  // in this way the first offset will use an axis and the second one
  // will use the other one
  var useHeight = ['right', 'left'].indexOf(basePlacement) !== -1;

  // Split the offset string to obtain a list of values and operands
  // The regex addresses values with the plus or minus sign in front (+10, -20, etc)
  var fragments = offset.split(/(\+|\-)/).map(function (frag) {
    return frag.trim();
  });

  // Detect if the offset string contains a pair of values or a single one
  // they could be separated by comma or space
  var divider = fragments.indexOf(find(fragments, function (frag) {
    return frag.search(/,|\s/) !== -1;
  }));

  if (fragments[divider] && fragments[divider].indexOf(',') === -1) {
    console.warn('Offsets separated by white space(s) are deprecated, use a comma (,) instead.');
  }

  // If divider is found, we divide the list of values and operands to divide
  // them by ofset X and Y.
  var splitRegex = /\s*,\s*|\s+/;
  var ops = divider !== -1 ? [fragments.slice(0, divider).concat([fragments[divider].split(splitRegex)[0]]), [fragments[divider].split(splitRegex)[1]].concat(fragments.slice(divider + 1))] : [fragments];

  // Convert the values with units to absolute pixels to allow our computations
  ops = ops.map(function (op, index) {
    // Most of the units rely on the orientation of the popper
    var measurement = (index === 1 ? !useHeight : useHeight) ? 'height' : 'width';
    var mergeWithPrevious = false;
    return op
    // This aggregates any `+` or `-` sign that aren't considered operators
    // e.g.: 10 + +5 => [10, +, +5]
    .reduce(function (a, b) {
      if (a[a.length - 1] === '' && ['+', '-'].indexOf(b) !== -1) {
        a[a.length - 1] = b;
        mergeWithPrevious = true;
        return a;
      } else if (mergeWithPrevious) {
        a[a.length - 1] += b;
        mergeWithPrevious = false;
        return a;
      } else {
        return a.concat(b);
      }
    }, [])
    // Here we convert the string values into number values (in px)
    .map(function (str) {
      return toValue(str, measurement, popperOffsets, referenceOffsets);
    });
  });

  // Loop trough the offsets arrays and execute the operations
  ops.forEach(function (op, index) {
    op.forEach(function (frag, index2) {
      if (isNumeric(frag)) {
        offsets[index] += frag * (op[index2 - 1] === '-' ? -1 : 1);
      }
    });
  });
  return offsets;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @argument {Number|String} options.offset=0
 * The offset value as described in the modifier description
 * @returns {Object} The data object, properly modified
 */
function offset(data, _ref) {
  var offset = _ref.offset;
  var placement = data.placement,
      _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var basePlacement = placement.split('-')[0];

  var offsets = void 0;
  if (isNumeric(+offset)) {
    offsets = [+offset, 0];
  } else {
    offsets = parseOffset(offset, popper, reference, basePlacement);
  }

  if (basePlacement === 'left') {
    popper.top += offsets[0];
    popper.left -= offsets[1];
  } else if (basePlacement === 'right') {
    popper.top += offsets[0];
    popper.left += offsets[1];
  } else if (basePlacement === 'top') {
    popper.left += offsets[0];
    popper.top -= offsets[1];
  } else if (basePlacement === 'bottom') {
    popper.left += offsets[0];
    popper.top += offsets[1];
  }

  data.popper = popper;
  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function preventOverflow(data, options) {
  var boundariesElement = options.boundariesElement || getOffsetParent(data.instance.popper);

  // If offsetParent is the reference element, we really want to
  // go one step up and use the next offsetParent as reference to
  // avoid to make this modifier completely useless and look like broken
  if (data.instance.reference === boundariesElement) {
    boundariesElement = getOffsetParent(boundariesElement);
  }

  // NOTE: DOM access here
  // resets the popper's position so that the document size can be calculated excluding
  // the size of the popper element itself
  var transformProp = getSupportedPropertyName('transform');
  var popperStyles = data.instance.popper.style; // assignment to help minification
  var top = popperStyles.top,
      left = popperStyles.left,
      transform = popperStyles[transformProp];

  popperStyles.top = '';
  popperStyles.left = '';
  popperStyles[transformProp] = '';

  var boundaries = getBoundaries(data.instance.popper, data.instance.reference, options.padding, boundariesElement, data.positionFixed);

  // NOTE: DOM access here
  // restores the original style properties after the offsets have been computed
  popperStyles.top = top;
  popperStyles.left = left;
  popperStyles[transformProp] = transform;

  options.boundaries = boundaries;

  var order = options.priority;
  var popper = data.offsets.popper;

  var check = {
    primary: function primary(placement) {
      var value = popper[placement];
      if (popper[placement] < boundaries[placement] && !options.escapeWithReference) {
        value = Math.max(popper[placement], boundaries[placement]);
      }
      return defineProperty({}, placement, value);
    },
    secondary: function secondary(placement) {
      var mainSide = placement === 'right' ? 'left' : 'top';
      var value = popper[mainSide];
      if (popper[placement] > boundaries[placement] && !options.escapeWithReference) {
        value = Math.min(popper[mainSide], boundaries[placement] - (placement === 'right' ? popper.width : popper.height));
      }
      return defineProperty({}, mainSide, value);
    }
  };

  order.forEach(function (placement) {
    var side = ['left', 'top'].indexOf(placement) !== -1 ? 'primary' : 'secondary';
    popper = _extends({}, popper, check[side](placement));
  });

  data.offsets.popper = popper;

  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function shift(data) {
  var placement = data.placement;
  var basePlacement = placement.split('-')[0];
  var shiftvariation = placement.split('-')[1];

  // if shift shiftvariation is specified, run the modifier
  if (shiftvariation) {
    var _data$offsets = data.offsets,
        reference = _data$offsets.reference,
        popper = _data$offsets.popper;

    var isVertical = ['bottom', 'top'].indexOf(basePlacement) !== -1;
    var side = isVertical ? 'left' : 'top';
    var measurement = isVertical ? 'width' : 'height';

    var shiftOffsets = {
      start: defineProperty({}, side, reference[side]),
      end: defineProperty({}, side, reference[side] + reference[measurement] - popper[measurement])
    };

    data.offsets.popper = _extends({}, popper, shiftOffsets[shiftvariation]);
  }

  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function hide(data) {
  if (!isModifierRequired(data.instance.modifiers, 'hide', 'preventOverflow')) {
    return data;
  }

  var refRect = data.offsets.reference;
  var bound = find(data.instance.modifiers, function (modifier) {
    return modifier.name === 'preventOverflow';
  }).boundaries;

  if (refRect.bottom < bound.top || refRect.left > bound.right || refRect.top > bound.bottom || refRect.right < bound.left) {
    // Avoid unnecessary DOM access if visibility hasn't changed
    if (data.hide === true) {
      return data;
    }

    data.hide = true;
    data.attributes['x-out-of-boundaries'] = '';
  } else {
    // Avoid unnecessary DOM access if visibility hasn't changed
    if (data.hide === false) {
      return data;
    }

    data.hide = false;
    data.attributes['x-out-of-boundaries'] = false;
  }

  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function inner(data) {
  var placement = data.placement;
  var basePlacement = placement.split('-')[0];
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var isHoriz = ['left', 'right'].indexOf(basePlacement) !== -1;

  var subtractLength = ['top', 'left'].indexOf(basePlacement) === -1;

  popper[isHoriz ? 'left' : 'top'] = reference[basePlacement] - (subtractLength ? popper[isHoriz ? 'width' : 'height'] : 0);

  data.placement = getOppositePlacement(placement);
  data.offsets.popper = getClientRect(popper);

  return data;
}

/**
 * Modifier function, each modifier can have a function of this type assigned
 * to its `fn` property.<br />
 * These functions will be called on each update, this means that you must
 * make sure they are performant enough to avoid performance bottlenecks.
 *
 * @function ModifierFn
 * @argument {dataObject} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {dataObject} The data object, properly modified
 */

/**
 * Modifiers are plugins used to alter the behavior of your poppers.<br />
 * Popper.js uses a set of 9 modifiers to provide all the basic functionalities
 * needed by the library.
 *
 * Usually you don't want to override the `order`, `fn` and `onLoad` props.
 * All the other properties are configurations that could be tweaked.
 * @namespace modifiers
 */
var modifiers = {
  /**
   * Modifier used to shift the popper on the start or end of its reference
   * element.<br />
   * It will read the variation of the `placement` property.<br />
   * It can be one either `-end` or `-start`.
   * @memberof modifiers
   * @inner
   */
  shift: {
    /** @prop {number} order=100 - Index used to define the order of execution */
    order: 100,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: shift
  },

  /**
   * The `offset` modifier can shift your popper on both its axis.
   *
   * It accepts the following units:
   * - `px` or unit-less, interpreted as pixels
   * - `%` or `%r`, percentage relative to the length of the reference element
   * - `%p`, percentage relative to the length of the popper element
   * - `vw`, CSS viewport width unit
   * - `vh`, CSS viewport height unit
   *
   * For length is intended the main axis relative to the placement of the popper.<br />
   * This means that if the placement is `top` or `bottom`, the length will be the
   * `width`. In case of `left` or `right`, it will be the `height`.
   *
   * You can provide a single value (as `Number` or `String`), or a pair of values
   * as `String` divided by a comma or one (or more) white spaces.<br />
   * The latter is a deprecated method because it leads to confusion and will be
   * removed in v2.<br />
   * Additionally, it accepts additions and subtractions between different units.
   * Note that multiplications and divisions aren't supported.
   *
   * Valid examples are:
   * ```
   * 10
   * '10%'
   * '10, 10'
   * '10%, 10'
   * '10 + 10%'
   * '10 - 5vh + 3%'
   * '-10px + 5vh, 5px - 6%'
   * ```
   * > **NB**: If you desire to apply offsets to your poppers in a way that may make them overlap
   * > with their reference element, unfortunately, you will have to disable the `flip` modifier.
   * > You can read more on this at this [issue](https://github.com/FezVrasta/popper.js/issues/373).
   *
   * @memberof modifiers
   * @inner
   */
  offset: {
    /** @prop {number} order=200 - Index used to define the order of execution */
    order: 200,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: offset,
    /** @prop {Number|String} offset=0
     * The offset value as described in the modifier description
     */
    offset: 0
  },

  /**
   * Modifier used to prevent the popper from being positioned outside the boundary.
   *
   * A scenario exists where the reference itself is not within the boundaries.<br />
   * We can say it has "escaped the boundaries" — or just "escaped".<br />
   * In this case we need to decide whether the popper should either:
   *
   * - detach from the reference and remain "trapped" in the boundaries, or
   * - if it should ignore the boundary and "escape with its reference"
   *
   * When `escapeWithReference` is set to`true` and reference is completely
   * outside its boundaries, the popper will overflow (or completely leave)
   * the boundaries in order to remain attached to the edge of the reference.
   *
   * @memberof modifiers
   * @inner
   */
  preventOverflow: {
    /** @prop {number} order=300 - Index used to define the order of execution */
    order: 300,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: preventOverflow,
    /**
     * @prop {Array} [priority=['left','right','top','bottom']]
     * Popper will try to prevent overflow following these priorities by default,
     * then, it could overflow on the left and on top of the `boundariesElement`
     */
    priority: ['left', 'right', 'top', 'bottom'],
    /**
     * @prop {number} padding=5
     * Amount of pixel used to define a minimum distance between the boundaries
     * and the popper. This makes sure the popper always has a little padding
     * between the edges of its container
     */
    padding: 5,
    /**
     * @prop {String|HTMLElement} boundariesElement='scrollParent'
     * Boundaries used by the modifier. Can be `scrollParent`, `window`,
     * `viewport` or any DOM element.
     */
    boundariesElement: 'scrollParent'
  },

  /**
   * Modifier used to make sure the reference and its popper stay near each other
   * without leaving any gap between the two. Especially useful when the arrow is
   * enabled and you want to ensure that it points to its reference element.
   * It cares only about the first axis. You can still have poppers with margin
   * between the popper and its reference element.
   * @memberof modifiers
   * @inner
   */
  keepTogether: {
    /** @prop {number} order=400 - Index used to define the order of execution */
    order: 400,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: keepTogether
  },

  /**
   * This modifier is used to move the `arrowElement` of the popper to make
   * sure it is positioned between the reference element and its popper element.
   * It will read the outer size of the `arrowElement` node to detect how many
   * pixels of conjunction are needed.
   *
   * It has no effect if no `arrowElement` is provided.
   * @memberof modifiers
   * @inner
   */
  arrow: {
    /** @prop {number} order=500 - Index used to define the order of execution */
    order: 500,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: arrow,
    /** @prop {String|HTMLElement} element='[x-arrow]' - Selector or node used as arrow */
    element: '[x-arrow]'
  },

  /**
   * Modifier used to flip the popper's placement when it starts to overlap its
   * reference element.
   *
   * Requires the `preventOverflow` modifier before it in order to work.
   *
   * **NOTE:** this modifier will interrupt the current update cycle and will
   * restart it if it detects the need to flip the placement.
   * @memberof modifiers
   * @inner
   */
  flip: {
    /** @prop {number} order=600 - Index used to define the order of execution */
    order: 600,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: flip,
    /**
     * @prop {String|Array} behavior='flip'
     * The behavior used to change the popper's placement. It can be one of
     * `flip`, `clockwise`, `counterclockwise` or an array with a list of valid
     * placements (with optional variations)
     */
    behavior: 'flip',
    /**
     * @prop {number} padding=5
     * The popper will flip if it hits the edges of the `boundariesElement`
     */
    padding: 5,
    /**
     * @prop {String|HTMLElement} boundariesElement='viewport'
     * The element which will define the boundaries of the popper position.
     * The popper will never be placed outside of the defined boundaries
     * (except if `keepTogether` is enabled)
     */
    boundariesElement: 'viewport',
    /**
     * @prop {Boolean} flipVariations=false
     * The popper will switch placement variation between `-start` and `-end` when
     * the reference element overlaps its boundaries.
     *
     * The original placement should have a set variation.
     */
    flipVariations: false,
    /**
     * @prop {Boolean} flipVariationsByContent=false
     * The popper will switch placement variation between `-start` and `-end` when
     * the popper element overlaps its reference boundaries.
     *
     * The original placement should have a set variation.
     */
    flipVariationsByContent: false
  },

  /**
   * Modifier used to make the popper flow toward the inner of the reference element.
   * By default, when this modifier is disabled, the popper will be placed outside
   * the reference element.
   * @memberof modifiers
   * @inner
   */
  inner: {
    /** @prop {number} order=700 - Index used to define the order of execution */
    order: 700,
    /** @prop {Boolean} enabled=false - Whether the modifier is enabled or not */
    enabled: false,
    /** @prop {ModifierFn} */
    fn: inner
  },

  /**
   * Modifier used to hide the popper when its reference element is outside of the
   * popper boundaries. It will set a `x-out-of-boundaries` attribute which can
   * be used to hide with a CSS selector the popper when its reference is
   * out of boundaries.
   *
   * Requires the `preventOverflow` modifier before it in order to work.
   * @memberof modifiers
   * @inner
   */
  hide: {
    /** @prop {number} order=800 - Index used to define the order of execution */
    order: 800,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: hide
  },

  /**
   * Computes the style that will be applied to the popper element to gets
   * properly positioned.
   *
   * Note that this modifier will not touch the DOM, it just prepares the styles
   * so that `applyStyle` modifier can apply it. This separation is useful
   * in case you need to replace `applyStyle` with a custom implementation.
   *
   * This modifier has `850` as `order` value to maintain backward compatibility
   * with previous versions of Popper.js. Expect the modifiers ordering method
   * to change in future major versions of the library.
   *
   * @memberof modifiers
   * @inner
   */
  computeStyle: {
    /** @prop {number} order=850 - Index used to define the order of execution */
    order: 850,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: computeStyle,
    /**
     * @prop {Boolean} gpuAcceleration=true
     * If true, it uses the CSS 3D transformation to position the popper.
     * Otherwise, it will use the `top` and `left` properties
     */
    gpuAcceleration: true,
    /**
     * @prop {string} [x='bottom']
     * Where to anchor the X axis (`bottom` or `top`). AKA X offset origin.
     * Change this if your popper should grow in a direction different from `bottom`
     */
    x: 'bottom',
    /**
     * @prop {string} [x='left']
     * Where to anchor the Y axis (`left` or `right`). AKA Y offset origin.
     * Change this if your popper should grow in a direction different from `right`
     */
    y: 'right'
  },

  /**
   * Applies the computed styles to the popper element.
   *
   * All the DOM manipulations are limited to this modifier. This is useful in case
   * you want to integrate Popper.js inside a framework or view library and you
   * want to delegate all the DOM manipulations to it.
   *
   * Note that if you disable this modifier, you must make sure the popper element
   * has its position set to `absolute` before Popper.js can do its work!
   *
   * Just disable this modifier and define your own to achieve the desired effect.
   *
   * @memberof modifiers
   * @inner
   */
  applyStyle: {
    /** @prop {number} order=900 - Index used to define the order of execution */
    order: 900,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: applyStyle,
    /** @prop {Function} */
    onLoad: applyStyleOnLoad,
    /**
     * @deprecated since version 1.10.0, the property moved to `computeStyle` modifier
     * @prop {Boolean} gpuAcceleration=true
     * If true, it uses the CSS 3D transformation to position the popper.
     * Otherwise, it will use the `top` and `left` properties
     */
    gpuAcceleration: undefined
  }
};

/**
 * The `dataObject` is an object containing all the information used by Popper.js.
 * This object is passed to modifiers and to the `onCreate` and `onUpdate` callbacks.
 * @name dataObject
 * @property {Object} data.instance The Popper.js instance
 * @property {String} data.placement Placement applied to popper
 * @property {String} data.originalPlacement Placement originally defined on init
 * @property {Boolean} data.flipped True if popper has been flipped by flip modifier
 * @property {Boolean} data.hide True if the reference element is out of boundaries, useful to know when to hide the popper
 * @property {HTMLElement} data.arrowElement Node used as arrow by arrow modifier
 * @property {Object} data.styles Any CSS property defined here will be applied to the popper. It expects the JavaScript nomenclature (eg. `marginBottom`)
 * @property {Object} data.arrowStyles Any CSS property defined here will be applied to the popper arrow. It expects the JavaScript nomenclature (eg. `marginBottom`)
 * @property {Object} data.boundaries Offsets of the popper boundaries
 * @property {Object} data.offsets The measurements of popper, reference and arrow elements
 * @property {Object} data.offsets.popper `top`, `left`, `width`, `height` values
 * @property {Object} data.offsets.reference `top`, `left`, `width`, `height` values
 * @property {Object} data.offsets.arrow] `top` and `left` offsets, only one of them will be different from 0
 */

/**
 * Default options provided to Popper.js constructor.<br />
 * These can be overridden using the `options` argument of Popper.js.<br />
 * To override an option, simply pass an object with the same
 * structure of the `options` object, as the 3rd argument. For example:
 * ```
 * new Popper(ref, pop, {
 *   modifiers: {
 *     preventOverflow: { enabled: false }
 *   }
 * })
 * ```
 * @type {Object}
 * @static
 * @memberof Popper
 */
var Defaults = {
  /**
   * Popper's placement.
   * @prop {Popper.placements} placement='bottom'
   */
  placement: 'bottom',

  /**
   * Set this to true if you want popper to position it self in 'fixed' mode
   * @prop {Boolean} positionFixed=false
   */
  positionFixed: false,

  /**
   * Whether events (resize, scroll) are initially enabled.
   * @prop {Boolean} eventsEnabled=true
   */
  eventsEnabled: true,

  /**
   * Set to true if you want to automatically remove the popper when
   * you call the `destroy` method.
   * @prop {Boolean} removeOnDestroy=false
   */
  removeOnDestroy: false,

  /**
   * Callback called when the popper is created.<br />
   * By default, it is set to no-op.<br />
   * Access Popper.js instance with `data.instance`.
   * @prop {onCreate}
   */
  onCreate: function onCreate() {},

  /**
   * Callback called when the popper is updated. This callback is not called
   * on the initialization/creation of the popper, but only on subsequent
   * updates.<br />
   * By default, it is set to no-op.<br />
   * Access Popper.js instance with `data.instance`.
   * @prop {onUpdate}
   */
  onUpdate: function onUpdate() {},

  /**
   * List of modifiers used to modify the offsets before they are applied to the popper.
   * They provide most of the functionalities of Popper.js.
   * @prop {modifiers}
   */
  modifiers: modifiers
};

/**
 * @callback onCreate
 * @param {dataObject} data
 */

/**
 * @callback onUpdate
 * @param {dataObject} data
 */

// Utils
// Methods
var Popper = function () {
  /**
   * Creates a new Popper.js instance.
   * @class Popper
   * @param {Element|referenceObject} reference - The reference element used to position the popper
   * @param {Element} popper - The HTML / XML element used as the popper
   * @param {Object} options - Your custom options to override the ones defined in [Defaults](#defaults)
   * @return {Object} instance - The generated Popper.js instance
   */
  function Popper(reference, popper) {
    var _this = this;

    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    classCallCheck(this, Popper);

    this.scheduleUpdate = function () {
      return requestAnimationFrame(_this.update);
    };

    // make update() debounced, so that it only runs at most once-per-tick
    this.update = debounce(this.update.bind(this));

    // with {} we create a new object with the options inside it
    this.options = _extends({}, Popper.Defaults, options);

    // init state
    this.state = {
      isDestroyed: false,
      isCreated: false,
      scrollParents: []
    };

    // get reference and popper elements (allow jQuery wrappers)
    this.reference = reference && reference.jquery ? reference[0] : reference;
    this.popper = popper && popper.jquery ? popper[0] : popper;

    // Deep merge modifiers options
    this.options.modifiers = {};
    Object.keys(_extends({}, Popper.Defaults.modifiers, options.modifiers)).forEach(function (name) {
      _this.options.modifiers[name] = _extends({}, Popper.Defaults.modifiers[name] || {}, options.modifiers ? options.modifiers[name] : {});
    });

    // Refactoring modifiers' list (Object => Array)
    this.modifiers = Object.keys(this.options.modifiers).map(function (name) {
      return _extends({
        name: name
      }, _this.options.modifiers[name]);
    })
    // sort the modifiers by order
    .sort(function (a, b) {
      return a.order - b.order;
    });

    // modifiers have the ability to execute arbitrary code when Popper.js get inited
    // such code is executed in the same order of its modifier
    // they could add new properties to their options configuration
    // BE AWARE: don't add options to `options.modifiers.name` but to `modifierOptions`!
    this.modifiers.forEach(function (modifierOptions) {
      if (modifierOptions.enabled && isFunction(modifierOptions.onLoad)) {
        modifierOptions.onLoad(_this.reference, _this.popper, _this.options, modifierOptions, _this.state);
      }
    });

    // fire the first update to position the popper in the right place
    this.update();

    var eventsEnabled = this.options.eventsEnabled;
    if (eventsEnabled) {
      // setup event listeners, they will take care of update the position in specific situations
      this.enableEventListeners();
    }

    this.state.eventsEnabled = eventsEnabled;
  }

  // We can't use class properties because they don't get listed in the
  // class prototype and break stuff like Sinon stubs


  createClass(Popper, [{
    key: 'update',
    value: function update$$1() {
      return update.call(this);
    }
  }, {
    key: 'destroy',
    value: function destroy$$1() {
      return destroy.call(this);
    }
  }, {
    key: 'enableEventListeners',
    value: function enableEventListeners$$1() {
      return enableEventListeners.call(this);
    }
  }, {
    key: 'disableEventListeners',
    value: function disableEventListeners$$1() {
      return disableEventListeners.call(this);
    }

    /**
     * Schedules an update. It will run on the next UI update available.
     * @method scheduleUpdate
     * @memberof Popper
     */


    /**
     * Collection of utilities useful when writing custom modifiers.
     * Starting from version 1.7, this method is available only if you
     * include `popper-utils.js` before `popper.js`.
     *
     * **DEPRECATION**: This way to access PopperUtils is deprecated
     * and will be removed in v2! Use the PopperUtils module directly instead.
     * Due to the high instability of the methods contained in Utils, we can't
     * guarantee them to follow semver. Use them at your own risk!
     * @static
     * @private
     * @type {Object}
     * @deprecated since version 1.8
     * @member Utils
     * @memberof Popper
     */

  }]);
  return Popper;
}();

/**
 * The `referenceObject` is an object that provides an interface compatible with Popper.js
 * and lets you use it as replacement of a real DOM node.<br />
 * You can use this method to position a popper relatively to a set of coordinates
 * in case you don't have a DOM node to use as reference.
 *
 * ```
 * new Popper(referenceObject, popperNode);
 * ```
 *
 * NB: This feature isn't supported in Internet Explorer 10.
 * @name referenceObject
 * @property {Function} data.getBoundingClientRect
 * A function that returns a set of coordinates compatible with the native `getBoundingClientRect` method.
 * @property {number} data.clientWidth
 * An ES6 getter that will return the width of the virtual reference element.
 * @property {number} data.clientHeight
 * An ES6 getter that will return the height of the virtual reference element.
 */


Popper.Utils = (typeof window !== 'undefined' ? window : global).PopperUtils;
Popper.placements = placements;
Popper.Defaults = Defaults;

/* harmony default export */ __webpack_exports__["a"] = (Popper);
//# sourceMappingURL=popper.js.map

/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__(8)))

/***/ }),
/* 31 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_react___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_react__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_prop_types___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_prop_types__);
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

function _objectWithoutProperties(obj, keys) { var target = {}; for (var i in obj) { if (keys.indexOf(i) >= 0) continue; if (!Object.prototype.hasOwnProperty.call(obj, i)) continue; target[i] = obj[i]; } return target; }




var Arrow = function Arrow(props, context) {
  var _props$component = props.component,
      component = _props$component === undefined ? 'span' : _props$component,
      innerRef = props.innerRef,
      children = props.children,
      restProps = _objectWithoutProperties(props, ['component', 'innerRef', 'children']);

  var popper = context.popper;

  var arrowRef = function arrowRef(node) {
    popper.setArrowNode(node);
    if (typeof innerRef === 'function') {
      innerRef(node);
    }
  };
  var arrowStyle = popper.getArrowStyle();

  if (typeof children === 'function') {
    var arrowProps = {
      ref: arrowRef,
      style: arrowStyle
    };
    return children({ arrowProps: arrowProps, restProps: restProps });
  }

  var componentProps = _extends({}, restProps, {
    style: _extends({}, arrowStyle, restProps.style)
  });

  if (typeof component === 'string') {
    componentProps.ref = arrowRef;
  } else {
    componentProps.innerRef = arrowRef;
  }

  return Object(__WEBPACK_IMPORTED_MODULE_0_react__["createElement"])(component, componentProps, children);
};

Arrow.contextTypes = {
  popper: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.object.isRequired
};

Arrow.propTypes = {
  component: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func]),
  innerRef: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func,
  children: __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.oneOfType([__WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.node, __WEBPACK_IMPORTED_MODULE_1_prop_types___default.a.func])
};

/* harmony default export */ __webpack_exports__["a"] = (Arrow);

/***/ }),
/* 32 */
/***/ (function(module, exports) {

/**
 * lodash (Custom Build) <https://lodash.com/>
 * Build: `lodash modularize exports="npm" -o ./`
 * Copyright jQuery Foundation and other contributors <https://jquery.org/>
 * Released under MIT license <https://lodash.com/license>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 */

/** Used as references for various `Number` constants. */
var NAN = 0 / 0;

/** `Object#toString` result references. */
var symbolTag = '[object Symbol]';

/** Used to match leading and trailing whitespace. */
var reTrim = /^\s+|\s+$/g;

/** Used to detect bad signed hexadecimal string values. */
var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;

/** Used to detect binary string values. */
var reIsBinary = /^0b[01]+$/i;

/** Used to detect octal string values. */
var reIsOctal = /^0o[0-7]+$/i;

/** Built-in method references without a dependency on `root`. */
var freeParseInt = parseInt;

/** Used for built-in method references. */
var objectProto = Object.prototype;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */
var objectToString = objectProto.toString;

/**
 * Checks if `value` is the
 * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
 * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(_.noop);
 * // => true
 *
 * _.isObject(null);
 * // => false
 */
function isObject(value) {
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

/**
 * Checks if `value` is object-like. A value is object-like if it's not `null`
 * and has a `typeof` result of "object".
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 * @example
 *
 * _.isObjectLike({});
 * // => true
 *
 * _.isObjectLike([1, 2, 3]);
 * // => true
 *
 * _.isObjectLike(_.noop);
 * // => false
 *
 * _.isObjectLike(null);
 * // => false
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/**
 * Checks if `value` is classified as a `Symbol` primitive or object.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a symbol, else `false`.
 * @example
 *
 * _.isSymbol(Symbol.iterator);
 * // => true
 *
 * _.isSymbol('abc');
 * // => false
 */
function isSymbol(value) {
  return typeof value == 'symbol' ||
    (isObjectLike(value) && objectToString.call(value) == symbolTag);
}

/**
 * Converts `value` to a number.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to process.
 * @returns {number} Returns the number.
 * @example
 *
 * _.toNumber(3.2);
 * // => 3.2
 *
 * _.toNumber(Number.MIN_VALUE);
 * // => 5e-324
 *
 * _.toNumber(Infinity);
 * // => Infinity
 *
 * _.toNumber('3.2');
 * // => 3.2
 */
function toNumber(value) {
  if (typeof value == 'number') {
    return value;
  }
  if (isSymbol(value)) {
    return NAN;
  }
  if (isObject(value)) {
    var other = typeof value.valueOf == 'function' ? value.valueOf() : value;
    value = isObject(other) ? (other + '') : other;
  }
  if (typeof value != 'string') {
    return value === 0 ? value : +value;
  }
  value = value.replace(reTrim, '');
  var isBinary = reIsBinary.test(value);
  return (isBinary || reIsOctal.test(value))
    ? freeParseInt(value.slice(2), isBinary ? 2 : 8)
    : (reIsBadHex.test(value) ? NAN : +value);
}

module.exports = toNumber;


/***/ }),
/* 33 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return polyfill; });
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

function componentWillMount() {
  // Call this.constructor.gDSFP to support sub-classes.
  var state = this.constructor.getDerivedStateFromProps(this.props, this.state);
  if (state !== null && state !== undefined) {
    this.setState(state);
  }
}

function componentWillReceiveProps(nextProps) {
  // Call this.constructor.gDSFP to support sub-classes.
  // Use the setState() updater to ensure state isn't stale in certain edge cases.
  function updater(prevState) {
    var state = this.constructor.getDerivedStateFromProps(nextProps, prevState);
    return state !== null && state !== undefined ? state : null;
  }
  // Binding "this" is important for shallow renderer support.
  this.setState(updater.bind(this));
}

function componentWillUpdate(nextProps, nextState) {
  try {
    var prevProps = this.props;
    var prevState = this.state;
    this.props = nextProps;
    this.state = nextState;
    this.__reactInternalSnapshotFlag = true;
    this.__reactInternalSnapshot = this.getSnapshotBeforeUpdate(
      prevProps,
      prevState
    );
  } finally {
    this.props = prevProps;
    this.state = prevState;
  }
}

// React may warn about cWM/cWRP/cWU methods being deprecated.
// Add a flag to suppress these warnings for this special case.
componentWillMount.__suppressDeprecationWarning = true;
componentWillReceiveProps.__suppressDeprecationWarning = true;
componentWillUpdate.__suppressDeprecationWarning = true;

function polyfill(Component) {
  var prototype = Component.prototype;

  if (!prototype || !prototype.isReactComponent) {
    throw new Error('Can only polyfill class components');
  }

  if (
    typeof Component.getDerivedStateFromProps !== 'function' &&
    typeof prototype.getSnapshotBeforeUpdate !== 'function'
  ) {
    return Component;
  }

  // If new component APIs are defined, "unsafe" lifecycles won't be called.
  // Error if any of these lifecycles are present,
  // Because they would work differently between older and newer (16.3+) versions of React.
  var foundWillMountName = null;
  var foundWillReceivePropsName = null;
  var foundWillUpdateName = null;
  if (typeof prototype.componentWillMount === 'function') {
    foundWillMountName = 'componentWillMount';
  } else if (typeof prototype.UNSAFE_componentWillMount === 'function') {
    foundWillMountName = 'UNSAFE_componentWillMount';
  }
  if (typeof prototype.componentWillReceiveProps === 'function') {
    foundWillReceivePropsName = 'componentWillReceiveProps';
  } else if (typeof prototype.UNSAFE_componentWillReceiveProps === 'function') {
    foundWillReceivePropsName = 'UNSAFE_componentWillReceiveProps';
  }
  if (typeof prototype.componentWillUpdate === 'function') {
    foundWillUpdateName = 'componentWillUpdate';
  } else if (typeof prototype.UNSAFE_componentWillUpdate === 'function') {
    foundWillUpdateName = 'UNSAFE_componentWillUpdate';
  }
  if (
    foundWillMountName !== null ||
    foundWillReceivePropsName !== null ||
    foundWillUpdateName !== null
  ) {
    var componentName = Component.displayName || Component.name;
    var newApiName =
      typeof Component.getDerivedStateFromProps === 'function'
        ? 'getDerivedStateFromProps()'
        : 'getSnapshotBeforeUpdate()';

    throw Error(
      'Unsafe legacy lifecycles will not be called for components using new component APIs.\n\n' +
        componentName +
        ' uses ' +
        newApiName +
        ' but also contains the following legacy lifecycles:' +
        (foundWillMountName !== null ? '\n  ' + foundWillMountName : '') +
        (foundWillReceivePropsName !== null
          ? '\n  ' + foundWillReceivePropsName
          : '') +
        (foundWillUpdateName !== null ? '\n  ' + foundWillUpdateName : '') +
        '\n\nThe above lifecycles should be removed. Learn more about this warning here:\n' +
        'https://fb.me/react-async-component-lifecycle-hooks'
    );
  }

  // React <= 16.2 does not support static getDerivedStateFromProps.
  // As a workaround, use cWM and cWRP to invoke the new static lifecycle.
  // Newer versions of React will ignore these lifecycles if gDSFP exists.
  if (typeof Component.getDerivedStateFromProps === 'function') {
    prototype.componentWillMount = componentWillMount;
    prototype.componentWillReceiveProps = componentWillReceiveProps;
  }

  // React <= 16.2 does not support getSnapshotBeforeUpdate.
  // As a workaround, use cWU to invoke the new lifecycle.
  // Newer versions of React will ignore that lifecycle if gSBU exists.
  if (typeof prototype.getSnapshotBeforeUpdate === 'function') {
    if (typeof prototype.componentDidUpdate !== 'function') {
      throw new Error(
        'Cannot polyfill getSnapshotBeforeUpdate() for components that do not define componentDidUpdate() on the prototype'
      );
    }

    prototype.componentWillUpdate = componentWillUpdate;

    var componentDidUpdate = prototype.componentDidUpdate;

    prototype.componentDidUpdate = function componentDidUpdatePolyfill(
      prevProps,
      prevState,
      maybeSnapshot
    ) {
      // 16.3+ will not execute our will-update method;
      // It will pass a snapshot value to did-update though.
      // Older versions will require our polyfilled will-update value.
      // We need to handle both cases, but can't just check for the presence of "maybeSnapshot",
      // Because for <= 15.x versions this might be a "prevContext" object.
      // We also can't just check "__reactInternalSnapshot",
      // Because get-snapshot might return a falsy value.
      // So check for the explicit __reactInternalSnapshotFlag flag to determine behavior.
      var snapshot = this.__reactInternalSnapshotFlag
        ? this.__reactInternalSnapshot
        : maybeSnapshot;

      componentDidUpdate.call(this, prevProps, prevState, snapshot);
    };
  }

  return Component;
}




/***/ }),
/* 34 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__MenuItemType__ = __webpack_require__(10);
/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */

class ContentMenuItemType extends __WEBPACK_IMPORTED_MODULE_0__MenuItemType__["a" /* default */] {
  /**
     * @inheritDoc
     */
  getTreeType() {
    return {
      icon: 'oi oi-document',
      max_children: -1,
      max_depth: -1,
      valid_children: -1
    };
  }

}
/* harmony export (immutable) */ __webpack_exports__["a"] = ContentMenuItemType;


/***/ })
/******/ ])["default"];
});
//# sourceMappingURL=MenuManager.module.js.map