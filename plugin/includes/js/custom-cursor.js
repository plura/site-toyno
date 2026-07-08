function CustomCursor({center = true, marker, target} = {}) {

	const 

		_this = this,

		clss = 'p-custom-cursor',


		el = (target || document.body).appendChild( document.createElement('div') ),


		refresh = e => {

			const x = e.clientX, y = e.clientY;

			Object.entries({x: x, y: y}).forEach( ([key, value]) => el.style.setProperty(`--${key}`, value) );

		},


		set_marker = obj => el.appendChild( obj ).classList.add(`${clss}-obj`);


	el.classList.add('p-custom-cursor');

	if( marker ) {

		set_marker( marker );

	}


	document.addEventListener('mousemove', event => refresh( event ) );


	//initial position
	if( center ) {

		refresh({clientX: window.innerWidth / 2, clientY: window.innerHeight / 2});

	}


	_this.marker = set_marker;


}