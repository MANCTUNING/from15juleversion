jQuery( document ).ready( function ( $ ) {

	var
	$context = $( '#wppw_catalog_products' ),
	$products = $( '.b-item-col', $context ),
	products = [ ],
	lowPrice = 99999999999999999,
	highPrice = 1;

	$products.each( function ( i, e ) {

		var $this = $( this ),
		url = $( 'a.b-item-name', $this ).attr( 'href' ),
		price = $( '.b-item-price__new', $this ).text().replace( /\s/g, '' ).match( /\d+/ )[0];
		
		// Корректируем цены
		if ( lowPrice > price )
			lowPrice = price;
		if ( highPrice < price )
			highPrice = price;

		products.push( {
			'@type' : 'Offer',
			'url' : url,
		} );

	} );

	var el = document.createElement( 'script' );
	el.type = 'application/ld+json';

	el.text = JSON.stringify( {
		"@context" : "http://schema.org/",
		"@type" : "Product",
		"name" : document.title,
		"offers" : {
			"@context" : "http://schema.org/",
			"@type" : "AggregateOffer",
			"offerCount" : $products.length,
			"lowPrice" : lowPrice,
			"highPrice" : highPrice,
			"priceCurrency" : "RUB",
			"offers" : products,
		}
	} );

	document.querySelector( 'body' ).appendChild( el );
} )