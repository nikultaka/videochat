import vcCake from 'vc-cake'
import TradelineElement from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  // Component callback
  function (component) {
    component.add(TradelineElement)
  },
  // css settings // css for element
  {
    css: false,
    editorCss: require('raw-loader!./editor.css')
  }
)
