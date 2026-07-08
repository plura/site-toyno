let p_custom_cursor;

document.addEventListener('DOMContentLoaded', () => {

	const 

		path = toyno_core.restURL + 'toyno/v1',

		sidebar = document.getElementById('toyno-sidebar'),

		team = document.querySelector('.toyno-team-members'),

		works = document.querySelector('.toyno-works'),

		workshop_speakers = document.querySelectorAll('.single-toyno_workshop .toyno-team-members[data-layout-type="speakers"] .toyno-team-member');



	//add back action
	document.querySelectorAll('.toyno-ui-goback').forEach( element => element.addEventListener('click', event => {

		event.preventDefault();

		window.history.back();

	}));



	//substitute wpml menu language text  with its acronyms  
	document.querySelectorAll('.menu-item.wpml-ls-item > a > span').forEach( element => 

		element.setAttribute('data-lang-code', element.getAttribute('lang').replace(/-[a-z]+/, '') )

	);




	//add cursor
	p_custom_cursor = new CustomCursor();


	if( sidebar ) {

		new Sidebar({target: sidebar, toggle: true});

	}


	if( team ) {

		new ToynoTeamMembers({target: team, labels: toyno_core.dictionary});

	}


	if( works ) {

		new DynamicGrid({path: path, target: works});

	}


	//workshop speakers
	if( workshop_speakers.length ) {

		new ToynoWorkshopManager({members: workshop_speakers, popID: 1891, url: path + '/team/'});

	}



	if( toyno_core.type === 'toyno_work' ) {

		Fancybox.bind('img.toyno-work-gallery-media', {
			groupAll: true
		});

	} else {

		Fancybox.bind('.wp-block-gallery figure img', {
			groupAll: true
		});

		Fancybox.bind('.wp-block-image img');

	}



});