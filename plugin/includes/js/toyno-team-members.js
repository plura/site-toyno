function ToynoTeamMembers({labels, target}) {

	const 

		toggle = element => {

			if( element.classList.contains('on') ) {

				element.classList.remove('on');

			} else {

				element.classList.add('on');

			}

		},




		set_heights = () => {

			const clss = 'toyno-team-member-bio';

			target.querySelectorAll(`.${ clss }`).forEach( element => { 

				let trigger = element.parentNode.querySelector(`.${ clss }-trigger`), wrapper = element.querySelector(`.${ clss }-wrapper`),

					em = element.querySelector('em:first-child'), off = 0, on;

				if( !wrapper ) {

					( wrapper = document.createElement('div') ).classList.add( `${ clss }-wrapper` );

					[ ...element.children ].forEach( child => wrapper.append( child ) );

					element.append( wrapper );

				}

				on = wrapper.offsetHeight;

				if( em ) {

					off = em.offsetHeight;

				}

				Object.entries({on: on, off: off}).forEach( ([key, value]) => element.style.setProperty(`--${key}`, `${value}px`) );

				element.classList.add('init');


				//if on/off have different values, a trigger will be required
				if( on > off && !trigger ) {

					( trigger = element.parentNode.appendChild( document.createElement('a') ) ).classList.add(`${ clss }-trigger`);

					trigger.textContent = labels?.['Read More'] || 'Read More';

					trigger.href = '#';

					trigger.addEventListener('click', event => {

						event.preventDefault();

						toggle( element );

					});

				}


			});


		},

		resizeObserver = new ResizeObserver( entries => set_heights() );



	resizeObserver.observe( target );

}