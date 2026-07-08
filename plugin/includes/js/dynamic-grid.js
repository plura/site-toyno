function DynamicGrid({path, target}) {

	let active, grid_cols;


	const 

		ui_filter_groups = target.querySelectorAll('.grid-filter-group'),

		ui_grid = target.querySelector('.grid-items'),



		activate = () => {

			let clss = 'filtered', tags = [], url = path + '/works/';

			ui_filter_groups.forEach( group => group.value.match(/^[0-9]+$/) && tags.push( group.value ) );

			if( tags.length ) {

				url += '?' + ( new URLSearchParams( {tags: tags.join(',') } ) ).toString();

				target.classList.add( clss );

			} else {

				target.classList.remove( clss );

			}

			fetch( url ).then( response => response.json() ).then( data => refresh( data ) );

		},



		//refreshes layout according to window size
		set_grid_cols = () => {

			let w = window.innerWidth, n;

			if( w >= 1600 ) {

				n = 6;

			} if( w >= 1366 && w < 1600 ) {

				n = 5;

			} else if( w >= 991 && w < 1366 ) {

				n = 4;

			} else if( w >= 768 && w < 991 ) {

				n = 3;

			}

			//set grid_cols var
			grid_cols = n || 2;

			console.log(w, grid_cols);
			
			//set grid properties
			Object.entries({w: `${ ui_grid.offsetWidth }px`, cols: grid_cols})

			.forEach(([key, value]) => ui_grid.style.setProperty(`--grid-${key}`, value) );

			//refresh grid
			refresh();

		},



		refresh = data => {

			if( data ) {

				active = data;

			}

			let ui_grid_items = ui_grid.querySelectorAll('.grid-item'),

				//get number of rows
				rows = !active || active.length > 0 ? Math.floor( ( !active ? ui_grid_items.length : active.length ) / grid_cols ) + 1 : 0;

			ui_grid_items.forEach( (work, index) => {

				const id = Number( work.dataset.id );

				if( !active || active.includes( id ) ) {

						//get item index (if no active is set use index)
					let n = !active ? index : active.indexOf( id ),

						//get item x pos
						x = n % grid_cols,

						//get item y pos
						y = Math.floor( n / grid_cols );

					//set item x/y css vars
					Object.entries({x: x, y: y}).forEach( ([key, value]) => work.style.setProperty(`--${key}`, value) );

					//set item 'on' status
					work.classList.add('on');
	
				} else {

					//remove item 'on' status
					work.classList.remove('on');

					//['x', 'y'].forEach( key => work.style.removeProperty(`--${key}`) );
				
				}

			});

			//set grid number of rows css var. b/c its children have their position set
			//to absolute, it's necessary to update their holder's height
			ui_grid.style.setProperty('--grid-rows', rows);

		},

		//observes browser window resize
		resizeObserver = new ResizeObserver( entries => set_grid_cols() );



	target.classList.add( ...['active', 'dynamic-grid'] );

	if( ui_filter_groups.length ) {

		ui_filter_groups.forEach( group => group.addEventListener('change', event => activate() ) );

	}


	resizeObserver.observe( target );


}
