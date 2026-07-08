
//https://fancyapps.com/fancybox/plugins/html/

function ToynoWorkshopManager({members, popID, target, url}) {


	let map, pop;


	const

		id = `toyno-workshop-popup-${ ( new Date() ).getTime() }`,

		empty = element => {

			while( element.firstElementChild ) {

				element.firstElementChild.remove();

			}
		
		},


		popup = value => {

			const html = new DOMParser().parseFromString(value, 'text/html');

			empty( pop );

			pop.append( html.body.firstChild );
			
			Fancybox.show([{ src: `#${ id }`, type: "inline" }]);			

		},


		refresh = data => {

			Object.entries( data ).forEach( ([key, value]) =>

				map.get( key ).addEventListener('click', event => popup( value ) )

			);

		};




	let ids = [];

	map = new Map();

	members.forEach( element => {

		map.set( element.dataset.id, element );

		ids.push( element.dataset.id );

	});

	if( ids.length ) {

		let path = url + '?' + ( new URLSearchParams( {ids: ids.join(',') } ) ).toString();

		fetch( path ).then( response => response.json() ).then( data =>refresh( data ) );


	}


	( pop = document.body.appendChild( document.createElement('div') ) ).classList.add('toyno-workshop-popup');

	pop.setAttribute('id', id);

}