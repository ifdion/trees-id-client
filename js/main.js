'use strict';

console.log('hello from main');

// function addEventListener(el, eventName, handler) {
// 	if (el.addEventListener) {
// 		el.addEventListener(eventName, handler);
// 	} else {
// 		el.attachEvent('on' + eventName, function(){
// 			handler.call(el);
// 		});
// 	}
// }

function renderMap(e){
	e.preventDefault();
	var mapObjectID = this.getAttribute('href');
	var mapObject = document.getElementById(mapObjectID);
	var mapType = mapObject.getAttribute('data-map-type');

	this.style.display = 'none';
	mapObject.style.display = 'block';

	if (mapType == 'archive') {
		archiveMap(mapObjectID);
	} else if (mapType == 'single') {
		singleMap(mapObjectID);
	}
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

