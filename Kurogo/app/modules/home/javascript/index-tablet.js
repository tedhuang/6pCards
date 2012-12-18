/*
 * Copyright © 2010 - 2012 Modo Labs Inc. All rights reserved.
 *
 * The license governing the contents of this file is located in the LICENSE
 * file located at the root directory of this distribution. If the LICENSE file
 * is missing, please contact sales@modolabs.com.
 *
 */

function loadModulePages(modulePanes) {
    function loadModulePage(info) {
        var elem = document.getElementById(info['elementId']);
        if (!elem) { return; }
        
        ajaxContentIntoContainer({
            url: info['ajaxURL'], 
            container: elem, 
            timeout: 60, 
            loadMessage: false,
            success: function () {
                removeClass(elem, 'loading');
                onDOMChange();
                moduleHandleWindowResize();
            },
            error: function (code) {
                removeClass(elem, 'loading');
            }
        });
    }
    
    for (var pane in modulePanes) {
        loadModulePage(modulePanes[pane]);
    }
}

var paneResizeHandlers = [];
function registerPaneResizeHandler(handler) {
    paneResizeHandlers.push(typeof handler == 'string' ? window[handler] : handler);
}

function callPaneResizeHandlers() {
    for (var i = 0; i < paneResizeHandlers.length; i++) {
        paneResizeHandlers[i]();
    }
}

function moduleHandleWindowResize() {
    var blocks = document.getElementById('fillscreen').childNodes;
    
    for (var i = 0; i < blocks.length; i++) {
        var blockborder = blocks[i].childNodes[0];
        if (!blockborder) { continue; }
          
        var clipHeight = getCSSHeight(blocks[i])
            - parseFloat(getCSSValue(blockborder, 'border-top-width')) 
            - parseFloat(getCSSValue(blockborder, 'border-bottom-width'))
            - parseFloat(getCSSValue(blockborder, 'padding-top'))
            - parseFloat(getCSSValue(blockborder, 'padding-bottom'))
            - parseFloat(getCSSValue(blockborder, 'margin-top'))
            - parseFloat(getCSSValue(blockborder, 'margin-bottom'));
        
        blockborder.style.height = clipHeight+'px';
        
        // If the block ends in a list, clip off items in the list so that 
        // we don't see partial items
        if (blockborder.childNodes.length < 2) { continue; }
        var blockheader = blockborder.childNodes[0];
        var blockcontent = blockborder.childNodes[1];
        
        // How big can the content be?
        var contentClipHeight = clipHeight 
            - blockheader.offsetHeight
            - parseFloat(getCSSValue(blockheader, 'margin-top'))
            - parseFloat(getCSSValue(blockheader, 'margin-bottom'))
            - parseFloat(getCSSValue(blockheader, 'border-top-width'))
            - parseFloat(getCSSValue(blockheader, 'border-bottom-width'))
            - parseFloat(getCSSValue(blockcontent, 'border-top-width')) 
            - parseFloat(getCSSValue(blockcontent, 'border-bottom-width'))
            - parseFloat(getCSSValue(blockcontent, 'padding-top'))
            - parseFloat(getCSSValue(blockcontent, 'padding-bottom'))
            - parseFloat(getCSSValue(blockcontent, 'margin-top'))
            - parseFloat(getCSSValue(blockcontent, 'margin-bottom'));
        
        if (!blockcontent.childNodes.length) { continue; }
        var last = blockcontent.childNodes[blockcontent.childNodes.length - 1];
        
        blockcontent.style.height = 'auto';
        
        if (last.nodeName == 'UL') {
            var listItems = last.childNodes;
            for (var j = 0; j < listItems.length; j++) {
                listItems[j].style.display = 'list-item'; // make all list items visible
            }
            
            var k = listItems.length - 1;
            while (getCSSHeight(blockcontent) > contentClipHeight) {
                listItems[k].style.display = 'none';
                if (--k < 0) { break; } // hid everything, stop
            }
        }
    
        blockcontent.style.height = contentClipHeight+'px'; // set block content height
    }
  
    callPaneResizeHandlers();
}
