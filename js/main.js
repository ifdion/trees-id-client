'use strict';

var opts = {
	onOpen : function() {
		console.log('onOpen hook');

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

var modal = new VanillaModal(opts);

function renderMap(e){
	e.preventDefault();
	modal.open('#modal-map');
}

var mapTrigger = document.getElementById('render-map-btn');

if (mapTrigger) {
	mapTrigger.addEventListener('click', renderMap);
} else {
	var mapObject = document.getElementById('trees-id-map');
	if (mapObject) {
		mapObject.style.display = 'block';
		treeMap('trees-id-map');
	}
}

