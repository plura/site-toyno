document.addEventListener('DOMContentLoaded', () => {


	const 

		//FOOTER	
		footer = document.querySelector('footer'),

		footer_rows = footer.querySelectorAll('footer .toyno-footer-row'),

		footer_row_wrapper = document.createElement('div');

		observer = new ResizeObserver( entries => {

			//first row is used for contacts on/off transition
			//last row height is used to calculate post content module height
			footer_rows.forEach( (row, index) => {

				let n = !index ? footer_row_wrapper.offsetHeight : row.offsetHeight;

				document.documentElement.style.setProperty(`--toyno-footer-row${ index + 1 }-h`, `${ n }px`);

			});

		});


	//FOOTER: toggle action
	footer_rows[1].querySelector('nav .menu-item a[href="#"]').addEventListener('click', event => {

		event.preventDefault();

		let body = document.body.classList, clss = 'toyno-footer-on';

		if( body.contains(clss) ) {

			body.remove(clss);

		} else {

			body.add(clss);

		}

	});

	//wrap first row content
	[ ...footer_rows[0].children ].forEach( child => footer_row_wrapper.append( child ) );

	( footer_rows[0].appendChild( footer_row_wrapper ) ).classList.add('toyno-footer-row-wrapper');



	observer.observe( document.body );




	//Custom Cursor
	if( typeof p_custom_cursor !== undefined ) {

		p_custom_cursor.marker( document.createElement('div') );

	}



	//Sidebar - add it to body 
	setTimeout( () => {

		document.body.appendChild( document.getElementById('toyno-sidebar') );

		console.log( document.getElementById('toyno-sidebar') );

	}, 100 );




	//home
	if( p.page(['wpmlobj-id-319', 'wpmlobj-id-1131', 'wpmlobj-id-1689']) ) {


		const

			//mosaic (mobile drag only for id 319)
			mosaic = new Mosaic({items: document.querySelectorAll('.home-grid-section'), mobile: !p.page(['wpmlobj-id-1131', 'wpmlobj-id-1689'])}),

			mosaic_items = document.querySelectorAll('.p-mosaic-item');


		//319 / 1131
		if( p.page( ['wpmlobj-id-319', 'wpmlobj-id-1131'] ) ) {

			const 

				row = mosaic_items[1].children[0],

				observer = new ResizeObserver( entries => {

					if( mosaic.mobile() && row.parentNode !== mosaic_items[2] ) {

						mosaic_items[2].append( row );

					} else if( !mosaic.mobile() && row.parentNode !== mosaic_items[1] ) {

						mosaic_items[1].append( row );

					}

				});

			observer.observe( document.body );

		}


		//1131 / 1689 [home2]
		if( p.page( ['wpmlobj-id-1131', 'wpmlobj-id-1689'] ) ) {

			let limits;

			const 

				h2_drag_container = ( mosaic.holder.appendChild( document.createElement('div') ) ),

				h2_drag = ( h2_drag_container.appendChild( document.createElement('div') ) ),

				h2_handler = data => mosaic.holder.style.setProperty('--bg-drag-y', data.y);


			h2_drag_container.classList.add('toyno-bg-drag-container');

			h2_drag.classList.add('toyno-bg-drag');


			//1131
			if( p.page('wpmlobj-id-1131') ) {
				
				const h2_observer = new ResizeObserver( entries => mosaic_items[2].style.setProperty(`--h`, `${ mosaic_items[2].offsetHeight }px` ) );

				h2_observer.observe( document.body );

				mosaic_items[2].addEventListener('click', event => {

					if( !event.currentTarget.classList.contains('on') ) {

						event.currentTarget.classList.add('on');

					} else {

						event.currentTarget.classList.remove('on');

					}

				});

			} else {


				limits = {

					y: ( y ) => {

						let h = document.querySelector('.p-mosaic-item .et_pb_text_inner').offsetHeight + ( window.innerWidth / 10 * 2 ) + 60;

						return Math.abs( y ) <= h ? y : h * -1;

					}

				};


			}


			new DragManager({container: h2_drag_container, drag_direction: 'y', handler: h2_handler, item: h2_drag, limits: limits});
		

		}




	//works
	} else if( p.page( ['page-id-14', 'wpmlobj-id-14'] ) ) {

		const 

			spans = document.querySelectorAll('#toyno-projects-txt .et_pb_text_inner span'),

			selects = document.querySelectorAll('.toyno-works-filter select');

		spans.forEach( (span, index) => span.parentNode.replaceChild( selects[index], span ) );


	} else if( p.page('single-toyno_work') ) {


		const  

			work_description = document.querySelector('.toyno-work-description'),

			observer = new ResizeObserver( entries => {

				const n = window.innerHeight - work_description.getBoundingClientRect().top - footer.offsetHeight;

				//console.log(n, window.innerHeight, work_description.getBoundingClientRect().top, footer.offsetHeight);

				work_description.style.setProperty('--h', `${ n }px`);

				if( work_description.clientHeight < work_description.scrollHeight ) {

					work_description.classList.add('has-scroll');

				} else {

					work_description.classList.remove('has-scroll');

				}

			});

		
		//observer will only observe window height change if document.body height is set to 100vh
		[document.body, footer].forEach( element => observer.observe( element ) );


	}


});