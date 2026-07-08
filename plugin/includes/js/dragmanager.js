
//https://stackoverflow.com/questions/34472704/vertically-draggable-division-of-two-areas
//https://www.kirupa.com/html5/drag.htm
function DragManager({container, handler, item, limits, drag_direction = 'y'}) {


	let active = false, currentX, currentY, initialX, initialY, xOffset = 0, yOffset = 0;


	const 


		drag = e => {

			let lx, ly;
		
			if ( active ) {

				e.preventDefault();

				if (e.type === "touchmove") {
					
					currentX = e.touches[0].clientX - initialX;
					
					currentY = e.touches[0].clientY - initialY;
				
				} else {
					
					currentX = e.clientX - initialX;
					
					currentY = e.clientY - initialY;
				
				}

				if( limits?.x && typeof limits.x === 'function' ) {

					lx = limits.x( currentX );

					currentX = lx || currentX;

				}

				if( limits?.y && typeof limits.y === 'function' ) {

					ly = limits.y( currentY );

					currentY = ly || currentY;

				}

				xOffset = currentX;
				
				yOffset = currentY;

				setTranslate(currentX, currentY, e.target);
			
			}
		
		},




		dragend = e => {
      	
			initialX = currentX;

			initialY = currentY;

			active = false;
    
    	},




		dragstart = e => {

			if (e.type === "touchstart") {
				
				initialX = e.touches[0].clientX - xOffset;
				
				initialY = e.touches[0].clientY - yOffset;
				
			} else {
			
				initialX = e.clientX - xOffset;
				
				initialY = e.clientY - yOffset;
			
			}


			if( e.target === item ) {

				active = true;
			
			}

		},



		dragdir = dir =>

			!drag_direction || (dir === 'y' && drag_direction === 'y') || (dir === 'x' && drag_direction === 'x'),
			



		setTranslate = (xPos, yPos, el) => {

			if( dragdir('x') && dragdir('y') ) {

				el.style.transform = "translate3d(" + xPos + "px, " + yPos + "px, 0)";

			} else if( dragdir('x') )  {

				el.style.transform = "translateX(" + xPos + "px)";

			} else if( dragdir('y') )  {

				el.style.transform = "translateY(" + yPos + "px)";

			}

			if( handler ) {

				handler({item: item, x: dragdir('x') && xPos, y: dragdir('y') && yPos});

			}
    	
    	};



    Object.entries({'dir': drag_direction || 'both'})

    .forEach( ([key, value]) => container.setAttribute(`data-drag-${key}`, value));



	//add drag events listeners

	['touchstart', 'mousedown'].forEach( eventType => container.addEventListener( eventType, dragstart, false ) );

	['touchend', 'mouseup'].forEach( eventType => container.addEventListener( eventType, dragend, false ) );

	['touchmove', 'mousemove'].forEach( eventType => container.addEventListener( eventType, drag, false ) );



}