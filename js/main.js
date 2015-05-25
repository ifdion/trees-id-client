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
		console.log('start archive tree');
		
		var APIurl = 'http://api.trees.id/?object=tree&per_page=20&callback=callback';
		var queryParameter = [];

		var treePage = mapObject.getAttribute('data-tree-page');

		archiveParameter.forEach(function(item, i){
			var parameterValue = mapObject.getAttribute('data-'+item);
			if (parameterValue) {
				queryParameter[item] = parameterValue;
				APIurl = APIurl + '&' + item + '=' + parameterValue;
			}
		});
		mapObject.style.display = 'block';
		archiveMapTree(APIurl,treeCoordinate,treeData,1,treePage);
	} else if (mapObject) {
		mapObject.style.display = 'block';
		treeMap('trees-id-map');
	}
}

