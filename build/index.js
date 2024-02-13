/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/edit.js":
/*!*********************!*\
  !*** ./src/edit.js ***!
  \*********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_6__);

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */






function Edit({
  attributes,
  setAttributes
}) {
  const {
    category,
    tag,
    id,
    hstart,
    order,
    sort,
    lang,
    additional_class,
    color,
    load_open,
    expand_all_link,
    hide_title,
    hide_accordion,
    glossarystyle,
    glossary
  } = attributes;
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.useBlockProps)();
  const [categorystate, setSelectedCategories] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(['']);
  const [tagstate, setSelectedTags] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(['']);
  const [idstate, setSelectedIDs] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(['']);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useEffect)(() => {
    setAttributes({
      category: category
    });
    setAttributes({
      tag: tag
    });
    setAttributes({
      id: id
    });
    setAttributes({
      hstart: hstart
    });
    setAttributes({
      order: order
    });
    setAttributes({
      sort: sort
    });
    setAttributes({
      lang: lang
    });
    setAttributes({
      additional_class: additional_class
    });
    setAttributes({
      color: color
    });
    setAttributes({
      load_open: load_open
    });
    setAttributes({
      expand_all_link: expand_all_link
    });
    setAttributes({
      hide_title: hide_title
    });
    setAttributes({
      hide_accordion: hide_accordion
    });
    setAttributes({
      glossarystyle: glossarystyle
    });
    setAttributes({
      glossary: glossary
    });
  }, [category, tag, id, hstart, order, sort, lang, additional_class, color, load_open, expand_all_link, hide_title, hide_accordion, glossarystyle, glossary, setAttributes]);
  const categories = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.useSelect)(select => {
    return select('core').getEntityRecords('taxonomy', 'faq_category', {
      per_page: -1,
      orderby: 'name',
      order: 'asc',
      status: 'publish',
      ['_fields']: 'id,name,slug'
    });
  }, []);
  const categoryoptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('all', 'rrze-faq'),
    value: ''
  }];
  if (!!categories) {
    Object.values(categories).forEach(category => {
      categoryoptions.push({
        label: category.name,
        value: category.slug
      });
    });
  }
  const tags = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.useSelect)(select => {
    return select('core').getEntityRecords('taxonomy', 'faq_tag', {
      per_page: -1,
      orderby: 'name',
      order: 'asc',
      status: 'publish',
      ['_fields']: 'id,name,slug'
    });
  }, []);
  const tagoptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('all', 'rrze-faq'),
    value: ''
  }];
  if (!!tags) {
    Object.values(tags).forEach(tag => {
      tagoptions.push({
        label: tag.name,
        value: tag.slug
      });
    });
  }
  const faqs = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.useSelect)(select => {
    return select('core').getEntityRecords('postType', 'faq', {
      per_page: -1,
      orderby: 'title',
      order: 'asc',
      status: 'publish',
      ['_fields']: 'id,title.rendered'
    });
  }, []);
  const faqoptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('all', 'rrze-faq'),
    value: 0
  }];
  if (!!faqs) {
    Object.values(faqs).forEach(faq => {
      faqoptions.push({
        label: faq.title.rendered ? faq.title.rendered : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('No title', 'rrze-faq'),
        value: faq.id
      });
    });
  }
  const langoptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('all', 'rrze-faq'),
    value: ''
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('German', 'rrze-faq'),
    value: 'de'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('English', 'rrze-faq'),
    value: 'en'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('French', 'rrze-faq'),
    value: 'fr'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Spanish', 'rrze-faq'),
    value: 'es'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Russian', 'rrze-faq'),
    value: 'ru'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Chinese', 'rrze-faq'),
    value: 'zh'
  }];
  const glossaryoptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('none', 'rrze-faq'),
    value: ''
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Categories', 'rrze-faq'),
    value: 'category'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Tags', 'rrze-faq'),
    value: 'tag'
  }];
  const glossarystyleoptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('A - Z', 'rrze-faq'),
    value: 'a-z'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Tagcloud', 'rrze-faq'),
    value: 'tagcloud'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Tabs', 'rrze-faq'),
    value: 'tabs'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('-- hidden --', 'rrze-faq'),
    value: ''
  }];
  const coloroptions = [{
    label: 'fau',
    value: 'fau'
  }, {
    label: 'med',
    value: 'med'
  }, {
    label: 'nat',
    value: 'nat'
  }, {
    label: 'phil',
    value: 'phil'
  }, {
    label: 'rw',
    value: 'rw'
  }, {
    label: 'tf',
    value: 'tf'
  }];
  const sortoptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Title', 'rrze-faq'),
    value: 'title'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('ID', 'rrze-faq'),
    value: 'id'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Sort field', 'rrze-faq'),
    value: 'sortfield'
  }];
  const orderoptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('ASC', 'rrze-faq'),
    value: 'ASC'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('DESC', 'rrze-faq'),
    value: 'DESC'
  }];

  // console.log('edit.js attributes: ' + JSON.stringify(attributes));

  const onChangeCategory = newValues => {
    setSelectedCategories(newValues);
    setAttributes({
      category: String(newValues)
    });
  };
  const onChangeTag = newValues => {
    setSelectedTags(newValues);
    setAttributes({
      tag: String(newValues)
    });
  };
  const onChangeID = newValues => {
    setSelectedIDs(newValues);
    setAttributes({
      id: String(newValues)
    });
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.InspectorControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Settings', 'rrze-faq')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Categories', 'rrze-faq'),
    value: categorystate,
    options: categoryoptions,
    onChange: onChangeCategory,
    multiple: true
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Tags', 'rrze-faq'),
    value: tagstate,
    options: tagoptions,
    onChange: onChangeTag,
    multiple: true
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('FAQ', 'rrze-faq'),
    value: idstate,
    options: faqoptions,
    onChange: onChangeID,
    multiple: true
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Language', 'rrze-faq'),
    options: langoptions,
    onChange: value => setAttributes({
      lang: value
    })
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.InspectorControls, {
    group: "styles"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Styles', 'rrze-faq')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Glossary content', 'rrze-faq'),
    options: glossaryoptions,
    onChange: value => setAttributes({
      glossary: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Glossary style', 'rrze-faq'),
    options: glossarystyleoptions,
    onChange: value => setAttributes({
      glossarystyle: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.ToggleControl, {
    checked: !!hide_accordion,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Hide accordion', 'rrze-faq'),
    onChange: () => setAttributes({
      hide_accordion: !hide_accordion
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.ToggleControl, {
    checked: !!hide_title,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Hide title', 'rrze-faq'),
    onChange: () => setAttributes({
      hide_title: !hide_title
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.ToggleControl, {
    checked: !!expand_all_link,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show "expand all" button', 'rrze-faq'),
    onChange: () => setAttributes({
      expand_all_link: !expand_all_link
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.ToggleControl, {
    checked: !!load_open,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Load website with opened accordions', 'rrze-faq'),
    onChange: () => setAttributes({
      load_open: !load_open
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Color', 'rrze-faq'),
    options: coloroptions,
    onChange: value => setAttributes({
      color: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Additional CSS-class(es) for sourrounding DIV', 'rrze-faq'),
    onChange: value => setAttributes({
      additional_class: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Sort', 'rrze-faq'),
    options: sortoptions,
    onChange: value => setAttributes({
      sort: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Order', 'rrze-faq'),
    options: orderoptions,
    onChange: value => setAttributes({
      order: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.RangeControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Heading starts with...', 'rrze-faq'),
    onChange: value => setAttributes({
      hstart: value
    }),
    min: 2,
    max: 6,
    initialPosition: 2
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    ...blockProps
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_6___default()), {
    block: "create-block/rrze-faq",
    attributes: attributes
  })));
}

/***/ }),

/***/ "./src/save.js":
/*!*********************!*\
  !*** ./src/save.js ***!
  \*********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ save)
/* harmony export */ });
//It's a dynamic block
function save() {
  return null;
}

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ ((module) => {

module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "./src/block.json":
/*!************************!*\
  !*** ./src/block.json ***!
  \************************/
/***/ ((module) => {

module.exports = JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"create-block/rrze-faq","version":"5.0.2","title":"RRZE FAQ","category":"widgets","description":"Display FAQ from your website or others","icon":"editor-help","example":{},"attributes":{"glossary":{"type":"string"},"glossarystyle":{"type":"string"},"category":{"type":"string"},"tag":{"type":"string"},"id":{"type":"string"},"hide_accordion":{"type":"boolean"},"hide_title":{"type":"boolean"},"expand_all_link":{"type":"boolean"},"load_open":{"type":"boolean"},"color":{"type":"string"},"additional_class":{"type":"string"},"lang":{"type":"string"},"sort":{"type":"string"},"order":{"type":"string"},"hstart":{"type":"number"}},"supports":{"color":{"background":false,"text":true},"html":false,"typography":{"fontSize":true}},"textdomain":"rrze-faq","editorScript":"file:./index.js","render":"file:./render.php"}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./block.json */ "./src/block.json");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./save */ "./src/save.js");
/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */


/**
 * Internal dependencies
 */



(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_1__.name, {
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: _save__WEBPACK_IMPORTED_MODULE_3__["default"]
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map