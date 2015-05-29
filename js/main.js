'use strict';

var opts = {
	onOpen : function() {
		// var mapObjectID = this.getAttribute('href');
		var mapObject = document.getElementById('trees-id-map');
		var mapType = mapObject.getAttribute('data-map-type');

		mapObject.style.display = 'block';

		if (mapType == 'archive') {
			archiveMap('trees-id-map');
		} else if (mapType == 'single') {
			singleMap('trees-id-map');
		}
	}
}

function renderMap(e){
	e.preventDefault();
	modal.open('#modal-map');
}

var mapTrigger = document.getElementById('render-map-btn');

if (mapTrigger) {
	var modal = new VanillaModal(opts);
	mapTrigger.addEventListener('click', renderMap);

} else {
	var mapObject = document.getElementById('trees-id-map');
	var mapType = mapObject.getAttribute('data-map-type');
	if (mapType == 'archive-tree'){
		archiveTreeMap('trees-id-map');
	} else if (mapObject) {
		treeMap('trees-id-map');
	}
}

