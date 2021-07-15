jQuery( document ).ready( function ( $ ) {


	var
	$context = $( '.b-wrapper-content' ),
	$images = $( '.wppw_gallery_img', $context ),
	images = [ ],
	sku = $( '.wppw_sku', $context ).text(),
	url = document.location.href,
	priceCurrency = 'RUB',
	price = $( '.wppw_price', $context ).text().replace( /\s/g, '' ).match( /\d+/ )[0],
	priceValidUntil = '2031-01-01';
	

	// Собираем адреса изображений
	$images.each( function ( i, e ) {

		var
		$this = $( this ),
		src = $this.attr( 'href' );

		images.push( src );
	} );


	var el = document.createElement( 'script' );
	el.type = 'application/ld+json';

	el.text = JSON.stringify( {
		"@context" : "http://schema.org/",
		"@type" : "Product",
		"name" : document.title,
		"description" : document.title,
		"image" : images,
		"sku" : sku,
		"offers" : {
			"@context" : "http://schema.org/",
			"@type" : "Offer",
			"url" : url,
			"priceCurrency" : priceCurrency,
			"price" : price,
			"priceValidUntil" : priceValidUntil,
			"itemCondition" : "https://schema.org/UsedCondition",
			"availability" : "https://schema.org/InStock",

		}
	} );

	document.querySelector( 'body' ).appendChild( el );
} );