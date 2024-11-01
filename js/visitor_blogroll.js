YUI().use('node', 'json', 'io-xdr', 'io-form', function(Y) {
    var node = Y.get('#visitor_blogroll_container');
		var formNode = Y.get('#visitor_blogroll_form');
    Y.log('Found node.. Setting style');
		node.on('click', function(e) {
				var target = e.target;
				Y.log('click');
		});

		var redrawer = {
				renderList: function( params ) {
						var feeds = params.feeds;
						var listNode = Y.get('#visitor_blogroll_list');
						listNode.set('innerHTML', '');
						for (var idx in feeds) {
								var feed = feeds[idx];
								var anchor = document.createElement('a');
								anchor.href = feed.link;
								anchor.innerHTML = feed.title;
								var li = document.createElement('li');
								li.style.cursor = 'hover';
								li.appendChild( anchor );

								if (feed.age) {
										var span = document.createElement('span');
										span.style.fontSize = '80%';
										span.style.marginLeft = '20px';
										span.innerHTML = feed.age + ' ago';
										li.appendChild( span );
								}
								listNode.appendChild( li );
						}
				}
		};

		var GlobalEventHandler = {
				start: function(id, args) {
				},
				success: function(id, response, args) {
						Y.log('success');
						try {
								var ret = Y.JSON.parse( response.responseText );
								redrawer.renderList( { feeds: ret.feeds } );
						} catch(e) {
								Y.log(e);
						}

				},
				end: function(id, args) {
						Y.log('rest');
				},
				failure: function(id, response, args) {
						Y.log('request failed');
						Y.log(response);
						
						var ret;
						var errorNode = Y.one('#vbr_error');
						var errorMsg;
						try {
								ret = Y.JSON.parse( response.responseText );
						} catch(e) {
								Y.log(e);
						}
						Y.log(ret);
						if (ret) {
								if (ret.form_errors.rss) {
										if (ret.form_errors.rss == 'missing') {
												errorMsg = 'RSS field is empty';
										} else if (ret.form_errors.rss == 'invalid_rss') {
												errorMsg = 'RSS feed does not appear to be valid';
										}
								} else if (ret.form_errors.key) {
										errorMsg = 'The API key is not valid';
								}
						}
						if (!errorMsg) {
								errorMsg = 'Uh oh, there was an unknown error. Your feed probably wasn\'t added.';
						}
						
						errorNode.set('innerHTML', errorMsg);
						errorNode.removeClass('vbr_hidden');
				}
		};

		var xdrCfg = {
				id: 'flash',
				src:'wp-content/plugins/visitor-blogroll/io.swf?t=' + new Date().valueOf().toString(),
				yid: Y.id
		};
		Y.io.transport(xdrCfg);
		// Define the configurations to be used for each transaciton..
		var cfg = {
				method: 'GET', 
				xdr: {
						use: 'flash',
						responseXML:false
				},
				timeout: 3000,
				on: {
						start: GlobalEventHandler.start,
						success: GlobalEventHandler.success,
						end: GlobalEventHandler.end,
						failure: GlobalEventHandler.failure
				}
		};

		Y.on('io:xdrReady', function() {
				var uri = 'http://www.makers-online.co.uk/projects/visitor_blogroll/api/get_feeds?host=' + escape(window.location.hostname) + '&path=' + escape(window.location.pathname);
				Y.log(uri);				
				var request = Y.io(uri, cfg);
				Y.log('made req');
				Y.log(request);
		});

		formNode.on('submit', function(e) {
				var uri = 'http://www.makers-online.co.uk/projects/visitor_blogroll/api/add_feed?host=' + escape(window.location.hostname) + '&path=' + escape(window.location.pathname);
				Y.log(e);
				var formObject = e.target;
				e.halt();

				// Define the configurations to be used for each transaciton..
				var cfg = {
						method: 'POST', 
						form: { 
				        id: formObject, 
			          useDisabled: true 
						},
						xdr: {
								use: 'flash',
								responseXML:false
						},
						on: {
								start: GlobalEventHandler.start,
								success: GlobalEventHandler.success,
								end: GlobalEventHandler.end,
								failure: GlobalEventHandler.failure
						},
						use: 'flash',
						timeout: 3000
				};

				var request = Y.io(uri, cfg);
		});

});

