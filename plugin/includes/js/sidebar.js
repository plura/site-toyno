function Sidebar({target, toggle}) {

	const 

		activate = (status = true) => {

			if( status ) {

				target.classList.add('on');

			} else {

				target.classList.remove('on');

			}

		},

		is_active = () => target.classList.contains('on');


	target.addEventListener('click', event => toggle ? activate( !is_active() ) : activate() );

	target.classList.add( ...['p-sidebar'] );


	//if click outside -> close
	document.addEventListener('click', event => {

		if( !target.contains( event.target ) ) {

			activate( false );

		}

	});





}