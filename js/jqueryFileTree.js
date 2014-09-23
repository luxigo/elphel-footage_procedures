/**
 * jQuery File Tree Plugin
 *
 * @author - Cory S.N. LaViska - A Beautiful Site (http://abeautifulsite.net/) - 24 March 2008
 * @author - Dave Rogers - https://github.com/daverogers/jQueryFileTree
 *
 *
 * Usage: $('.fileTreeDemo').fileTree( options, callback )
 *
 * Options:  root           - root folder to display; default = /
 *           script         - location of the serverside AJAX file to use; default = jqueryFileTree.php
 *           folderEvent    - event to trigger expand/collapse; default = click
 *           expandSpeed    - default = 500 (ms); use -1 for no animation
 *           collapseSpeed  - default = 500 (ms); use -1 for no animation
 *           expandEasing   - easing function to use on expand (optional)
 *           collapseEasing - easing function to use on collapse (optional)
 *           multiFolder    - whether or not to limit the browser to one subfolder at a time
 *           loadMessage    - Message to display while initial tree loads (can be HTML)
 *           multiSelect    - append checkbox to each line item to select more than one
 *
 *
 * TERMS OF USE
 *
 * This plugin is dual-licensed under the GNU General Public License and the MIT License and
 * is copyright 2008 A Beautiful Site, LLC.
 */

if(jQuery) (function($){

	$.extend($.fn, {
		fileTree: function(options, file) {
			// Default options
			if( options.root			=== undefined ) options.root			= '/';
			if( options.script			=== undefined ) options.script			= 'php/jqueryFileTree.php';
			if( options.folderEvent		=== undefined ) options.folderEvent		= 'click';
			if( options.expandSpeed		=== undefined ) options.expandSpeed		= -1;
			if( options.collapseSpeed	=== undefined ) options.collapseSpeed	= -1;
			if( options.expandEasing	=== undefined ) options.expandEasing	= null;
			if( options.collapseEasing	=== undefined ) options.collapseEasing	= null;
			if( options.multiFolder		=== undefined ) options.multiFolder		= true;
			if( options.loadMessage		=== undefined ) options.loadMessage		= 'Loading...';
			if( options.multiSelect		=== undefined ) options.multiSelect		= false;
			if( options.showFiles		=== undefined ) options.showFiles		= true;
			if( options.showDirectories		=== undefined ) options.showDirectories		= true;

			$(this).each( function() {

				function showTree(element, dir) {
					$(element).addClass('wait');
					$(".jqueryFileTree.start").remove();
          options.dir=dir;
					$.post(options.script, options)
					.done(function(data){
						$('.start',element).html('');
						$(element).removeClass('wait').append(data);
						if( options.root == dir ) $('UL:hidden',element).show(); else $('UL:hidden',element).slideDown({ duration: options.expandSpeed, easing: options.expandEasing });
						bindTree(element);

						$(element).removeClass('collapsed').addClass('expanded');

						_trigger(element, 'filetreeexpanded', data);
					})
					.fail(function(){
						$('.start',element).html('');
						$(element).removeClass('wait').append("<li>Unable to get file tree information</li>");
					});
				}

				function bindTree(element) {
					$('LI A',element).off('.jqueryFileTree').on(options.folderEvent+'.jqueryFileTree', function() {
						// set up data object to send back via trigger
						var data = {};
						data.li = $(this).closest('li');
            $('.selected',$(this).closest('div')).not(data.li).removeClass('selected');
            if (data.li.hasClass('directory')) {
              if (!options.showFiles) {
                data.li.addClass('selected');
              }
            } else {
              data.li.addClass('selected');
            }
						data.type = ( data.li.hasClass('directory') ? 'directory' : 'file' );
						data.value	= $(this).text();
						data.rel	= $(this).prop('rel');

						if( $(this).parent().hasClass('directory') ) {
							if( $(this).parent().hasClass('collapsed') ) {
								// Expand
								_trigger($(this), 'filetreeexpand', data);

								if( !options.multiFolder ) {
                  var parent=$(this).parent().parent();
									$('UL',parent).slideUp({ duration: options.collapseSpeed, easing: options.collapseEasing });
									$('LI.directory',parent).removeClass('expanded').addClass('collapsed');
								}
								$('UL',$(this).parent()).remove(); // cleanup
								showTree( $(this).parent(), escape($(this).attr('rel').match( /.*\// )) );
							} else {
								// Collapse
								_trigger($(this), 'filetreecollapse', data);
                var parent=$(this).parent();
								$('UL',parent).slideUp({ duration: options.collapseSpeed, easing: options.collapseEasing });
								parent.removeClass('expanded').addClass('collapsed');

								_trigger($(this), 'filetreecollapsed', data);
							}
						} else {
							// this is a file click, return file information
							file($(this).attr('rel'));

							_trigger($(this), 'filetreeclicked', data);
						}
						return false;
					});
					// Prevent A from triggering the # on non-click events
					if( options.folderEvent.toLowerCase != 'click' ) $('LI A',element).off('click');
				}

				// Loading message
				$(this).html('<ul class="jqueryFileTree start"><li class="wait">' + options.loadMessage + '<li></ul>');

				// Get the initial file list
				showTree( $(this), escape(options.root) );

				// wrapper to append trigger type to data
				function _trigger(element, eventType, data) {
					data.trigger = eventType;
					element.trigger(eventType, data);
				}

				// checkbox event (multiSelect)
				$(this).off('change.jqueryFileTree').on('change.jqueryFileTree', 'input:checkbox' , function(){
					var data = {};
					data.li		= $(this).closest('li');
					data.type	= ( data.li.hasClass('directory') ? 'directory' : 'file' );
					data.value	= data.li.children('a').text();
					data.rel	= data.li.children('a').prop('rel');

					// propagate check status to (visible) child checkboxes
					$('input:checkbox',data.li).prop( 'checked', $(this).prop('checked') );

					// set triggers
					if( $(this).prop('checked') )
						_trigger($(this), 'filetreechecked', data);
					else
						_trigger($(this), 'filetreeunchecked', data);
				});
			});
		}
	});

})(jQuery);
