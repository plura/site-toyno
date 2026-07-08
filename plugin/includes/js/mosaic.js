


function Mosaic({center = true, items, drag_direction = 'y', mobile = true,}) {


	let holder, map, drag_sliders;


	const 

		_this = this,

		observer = new ResizeObserver( entries => {

			const mode = 'data-mosaic-mobile-layout', mobile_mode = holder.getAttribute( mode );

			if( is_mobile() && mobile_mode !== '1' ) {

				holder.setAttribute(mode, 1);

			} else if( !is_mobile() && mobile_mode !== '0' ) {

				holder.setAttribute(mode, 0);

			}

			Object.entries({h: holder.offsetHeight, w: holder.offsetWidth})

			.forEach(([key, value]) => holder.style.setProperty(`--p-mobile-${ key }`, `${ value }px`));


			drag_sliders.forEach( slider => refresh_drag_slider({item: slider}) );


		}),


		//check for mobile environment. If true, allows dragging option (data-mosaic-layout-mobile is set to "1")
		is_mobile = () => window.innerWidth < 991,



		refresh_drag_slider = data => {

			let items = [], slider = data.item, prev = map.get( slider ).prev, next = map.get( slider ).next;

			if( prev ) {

				items.push( prev );

			}

			if( next ) {

				items.push( next );

			}


			/*console.log('h:' + slider.offsetHeight, 'x:' + slider.offsetLeft, 'y:' + slider.offsetTop, 'w:' + slider.offsetWidth );
			console.log('ty:' + data.y);*/

			items.forEach( item => {

				Object.entries({

					h: slider.offsetHeight,
					x: slider.offsetLeft + (data.x || 0),
					y: slider.offsetTop + (data.y || 0),
					w: slider.offsetWidth

				})

				.forEach( 

					([key,value]) => item.style.setProperty(`--p-mosaic-drag-slider${ slider.dataset.mosaicIndex }-${ key }`, `${ value }px`)

				);

				
				if( !item.classList.contains('has-drag-slider') ) {

					item.classList.add('has-drag-slider');

				}

			});

		},


		refresh_mouse_move = e => {

			if( !is_mobile() ) {

				Object.entries({x: e.pageX, y: e.pageY})

				.forEach( ([key, value]) => holder.style.setProperty(`--${key}`, value) );

			}

		};






	//init
	if( items ) {


		( holder = document.createElement('div') ).classList.add('p-mosaic');

		Object.entries({total: items.length, mobile: mobile ? 1 : 0})

		.forEach( ([key, value]) => holder.setAttribute(`data-mosaic-${ key }`, value) );

		//holder.setAttribute('data-mosaic-total', items.length);

		holder.style.setProperty('--mosaic-total', items.length);



		map = new Map();

		drag_sliders = [];



		items.forEach( (element, index) => {


			let drag_slider, objs = [ element ];

			element.classList.add('p-mosaic-item');


			if( index < items.length - 1 ) {


				( drag_slider = ( holder.appendChild( document.createElement('div') ) ) ).classList.add('p-mosaic-drag-slider');

				drag_sliders.push( drag_slider );


				map.set( drag_slider, { prev: items[ index ], next: items[ index + 1 ] } );


				objs.push( drag_slider );

			
			} else {


				//add holder if it doesn't exist
				if( element.parentNode !== holder ) {

					element.parentNode.append( holder );

				}

			
			}

			if( element.parentNode !== holder ) {

				holder.append( element );

			}		


			objs.forEach( obj => {

				obj.setAttribute('data-mosaic-index', index);

				obj.style.setProperty(`--p-mosaic-index`, index);

			});


		});





		drag_sliders.forEach( (slider, index) => {

			let limits;

			if( drag_sliders.length ) {

				limits = {};

				if( index < items.length - 1 ) {

					limits.bottom = drag_sliders[ index + 1 ];

				} else if( index > 0 ) {

					limits.top = drag_sliders[ index - 1 ];

				}

			}

			new DragManager({container: holder, handler: refresh_drag_slider, item: slider, limits: limits, drag_direction: 'y'});

		});



		observer.observe( document.body );



		document.addEventListener('mousemove', event => refresh_mouse_move( event ) );

		//initial position [non mobile]
		if( !is_mobile() && center ) {

			refresh_mouse_move({pageX: window.innerWidth / 2, pageY: window.innerHeight / 2 });

		}


	}


	_this.holder = holder;
	
	_this.mobile = is_mobile;

}