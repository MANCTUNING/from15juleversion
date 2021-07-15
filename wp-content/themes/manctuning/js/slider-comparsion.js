jQuery( document ).ready( function ( jQuery ) {
	// проверка является ли .cd-image-container в области видимости
	// ...

	// делаем элемент .cd-handle движимым и сменяем позицию .cd-resize-img
	jQuery( '.cd-image-container' ).each( function () {
		var actual = jQuery( this );
		drags( actual.find( '.cd-handle' ), actual.find( '.cd-resize-img' ), actual );
	} );
	setPositionToText( ".cd-image-label_left", "left" )
	setPositionToText( ".cd-image-label_right", "right" )
	jQuery( window ).resize( function () {
		setPositionToText( ".cd-image-label_left", "left" )
		setPositionToText( ".cd-image-label_right", "right" )
	} )
} );


// реализация перетаскивание http://css-tricks.com/snippets/jquery/draggable-without-jquery-ui/
function drags( dragElement, resizeElement, container ) {
	dragElement.on( "mousedown vmousedown", function ( e ) {
		dragElement.addClass( 'i-draggable' );
		resizeElement.addClass( 'resizable' );

		var dragWidth = dragElement.outerWidth(),
		xPosition = dragElement.offset().left + dragWidth - e.pageX,
		containerOffset = container.offset().left,
		containerWidth = container.outerWidth(),
		minLeft = containerOffset + 10,
		maxLeft = containerOffset + containerWidth - dragWidth - 10;

		dragElement.parents().on( "mousemove vmousemove", function ( e ) {
			leftValue = e.pageX + xPosition - dragWidth;

			if ( leftValue < minLeft ) {
				leftValue = minLeft;
			} else if ( leftValue > maxLeft ) {
				leftValue = maxLeft;
			}

			widthValue = ( leftValue + dragWidth / 2 - containerOffset ) * 100 / containerWidth + '%';

			jQuery( '.i-draggable' ).css( 'left', widthValue ).on( "mouseup vmouseup", function () {
				jQuery( this ).removeClass( 'i-draggable' );
				resizeElement.removeClass( 'resizable' );
			} );

			jQuery( '.resizable' ).css( 'width', widthValue );

			// ...

		} ).on( "mouseup vmouseup", function ( e ) {
			dragElement.removeClass( 'i-draggable' );
			resizeElement.removeClass( 'resizable' );
		} );
		e.preventDefault();
	} ).on( "mouseup vmouseup", function ( e ) {
		dragElement.removeClass( 'i-draggable' );
		resizeElement.removeClass( 'resizable' );
	} );
}

function setPositionToText( selector, side ) {
	var windowWidth = window.innerWidth
	var selectorWidth = jQuery( selector ).innerWidth()

	if ( side === "left" ) {
		jQuery( selector ).css( "left", ( ( ( window.innerWidth / 2 ) - ( windowWidth / 10 ) - selectorWidth ) + "px" ) )
	} else if ( side === "right" ) {
		jQuery( selector ).css( "left", ( ( window.innerWidth / 2 + ( windowWidth / 10 ) ) + "px" ) )
	}
}
