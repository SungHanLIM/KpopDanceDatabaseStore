(function(){

	tinymce.PluginManager.add('txshortcodes', function( editor, url ) {
		editor.addButton('txshortcodes', {
					title : 'TX Shortcodes', // title of the button
					//image : '../wp-content/plugins/tx-toolkit/tx-shortcode.png',  // path to the button's image
					icon : 'tx-mce-icon',  // path to the button's image					
			onclick: function() {
				
				var $form = jQuery("#txshortcodes-form");
				jQuery.colorbox({inline:true, href:"#tx-shortcode-form"});

			}
		});
	});

	
	// executes this when the DOM is ready
	jQuery(function(){
		// creates a form to be displayed everytime the button is clicked
		// you should achieve this using AJAX instead of direct html code like this
		var form_tx = jQuery('<div id="txshortcodes-form"><div id="tx-shortcode-form"><table id="txshortcodes-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>TX Shortcodes</h2></td>\
			</tr>\
			<tr>\
				<td class="shortcode-list" width="50%"><span id="columns">Columns</span></td><td class="shortcode-list"><span id="deviders">Divider</span></td>\
			</tr>\
			<tr>\
				<td class="shortcode-list"><span id="spacer">Spacer</span></td><td class="shortcode-list"><span id="testimonials">Testimonials</span></td>\
			</tr>\
			<tr>\
				<td class="shortcode-list"><span id="buttons">Butons</span></td><td class="shortcode-list"><span id="calltoact">Call To Act</span></td>\
			</tr>\
			<tr>\
				<td class="shortcode-list"><span id="services">Services</span></td><td class="shortcode-list"><span id="portfolios">Portfolios</span></td>\
			</tr>\
			<tr>\
				<td class="shortcode-list"><span id="recentposts">Posts</span></td><td class="shortcode-list"><span id="heading">Heading</span></td>\
			</tr>\
			<tr>\
				<td class="shortcode-list"><span id="wooprods">Product Carousel <small>(WooCommerce)</small></span></td><td class="shortcode-list"><span id="itrans-slider">i-trans Slider</span></td>\
			</tr>\
			<!-- <tr>\
				<td class="shortcode-list"><span id="tximage">Image</small></span></td><td class="shortcode-list">&nbsp;</td>\
			</tr> -->\
		</table>\
		<div class="nx-sh-cancel">\
			<input type="button" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</div>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
		W = W - 80;
		H = H - 84;		
		
		var table = form_tx.find('#txshortcodes-table');
		form_tx.appendTo('body').hide();
		//form_tx.appendTo('body');
		
		//call columns
		form_tx.find('#columns').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-column-form"});
			}, 500);
		});

		//call deviders
		form_tx.find('#deviders').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-devider-form"});
			}, 500);
		});
		
		//call Heding
		form_tx.find('#heading').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-heading-form"});
			}, 500);
		});			
		
		//call deviders
		form_tx.find('#testimonials').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-testimonial-form"});
			}, 500);
		});	
		
		//call buttons
		form_tx.find('#buttons').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-button-form"});
			}, 500);
		});	
		
		//call calltoact
		form_tx.find('#calltoact').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-calltoact-form"});
			}, 500);
		});
		
		//call Services
		form_tx.find('#services').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-service-form"});
			}, 500);
		});									
				
		//call portfolio
		form_tx.find('#portfolios').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-portfolio-form"});
			}, 500);
		});	
		
		//call blog
		form_tx.find('#recentposts').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-blog-form"});
			}, 500);
		});	
		
		//call spacer
		form_tx.find('#spacer').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-spacer-form"});
			}, 500);
		});
		
		//Woocommerce Products
		form_tx.find('#wooprods').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-wooprods-form"});
			}, 500);
		});	
		
		//i-trans slider
		form_tx.find('#itrans-slider').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-slider-form"});
			}, 500);
		});	

		//Insert Image
		form_tx.find('#tximage').click(function(){			
			setTimeout(function() {
				jQuery.colorbox({inline:true, href:"#tx-image-form"});
			}, 500);
		});												
		
		form_tx.find('.modal-close').click(function(){
			jQuery.colorbox.close();
		});
		
	});
	
	
	jQuery(function(){
		// creates a form to be displayed everytime the button is clicked
		// you should achieve this using AJAX instead of direct html code like this
		var form_portfolio = jQuery('<div id="portfolio-form"><div id="tx-portfolio-form"><table id="portfolio-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Portfolio</h2></td>\
			</tr>\
			<tr>\
				<th><label for="portfolio-style">Portfolio Style</label></th>\
				<td><select name="style" id="portfolio-style">\
					<option value="default">Default</option>\
					<option value="gallery">Gallery</option>\
				</select><br />\
				<small>Specify the portfolio style, "Gallery" style will not work with carousel.</small></td>\
			</tr>\
			<tr>\
				<th><label for="portfolio-items">Number of item</label></th>\
				<td><input type="number" max="12" min="0" id="portfolio-items" name="items" value="4" /><br />\
				<small>Specify the number of portfolio items to show.</small></td>\
			</tr>\
			<tr>\
				<th><label for="portfolio-columns">Number of columns</label></th>\
				<td><input type="number" min="1" max="4" name="columns" id="portfolio-columns" value="4" /><br />\
				<small>Specify number of portfolio columns.</small>\
			</tr>\
			<tr>\
				<th><label for="portfolio-hidecat">Show/hide item category labels</label></th>\
				<td><select name="hidecat" id="portfolio-hidecat">\
					<option value="no">Show category labels</option>\
					<option value="yes">Hide category labels</option>\
				</select>\
				</td>\
			</tr>\
			<tr>\
				<th><label for="portfolio-hideexcerpt">Show/hide item excerpt</label></th>\
				<td><select name="hideexcerpt" id="portfolio-hideexcerpt">\
					<option value="no">Show excerpt</option>\
					<option value="yes">Hide excerpt</option>\
				</select>\
				</td>\
			</tr>\
			<tr>\
				<th><label for="portfolio-showpage">Show Pagination</label></th>\
				<td><select name="showpage" id="portfolio-showpage">\
					<option value="no">No</option>\
					<option value="yes">Yes</option>\
				</select><br />\
				<small>Pagination will not work with carousel</small>\
				</td>\
			</tr>\
			<tr>\
				<th><label for="portfolio-carusel">Show as carousel</label></th>\
				<td><select name="carusel" id="portfolio-carusel">\
					<option value="no">No</option>\
					<option value="yes">Yes</option>\
				</select><br />\
				<small>Number of items must be greater then number of column</small>\
				</td>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="portfolio-submit" class="button-primary" value="Insert Portfolio" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		
		
		var table = form_portfolio.find('#portfolio-table');
		form_portfolio.appendTo('body').hide();
		
		
		// handles the click event of the submit button
		form_portfolio.find('#portfolio-submit').click(function(){

			var portfolio_style = table.find('#portfolio-style').val(); 
			var number_of_item = table.find('#portfolio-items').val(); 
			var number_of_column = table.find('#portfolio-columns').val(); 
			var hide_cat = table.find('#portfolio-hidecat').val();
			var hide_excerpt = table.find('#portfolio-hideexcerpt').val();
			var show_page = table.find('#portfolio-showpage').val();			
			var show_carusel = table.find('#portfolio-carusel').val(); 			
			
			
			var shortcode = '[tx_portfolio style="'+portfolio_style+'" items="'+number_of_item+'" columns="'+number_of_column+'" hide_cat="'+hide_cat+'" hide_excerpt="'+hide_excerpt+'" show_pagination="'+show_page+'" carousel="'+show_carusel+'"]<br/>';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});
		


		form_portfolio.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
	
	});	
	

	/*
	* Blog Posts
	*/	

	jQuery(function(){
		// creates a form to be displayed everytime the button is clicked
		// you should achieve this using AJAX instead of direct html code like this
		var form_blog = jQuery('<div id="blog-form" class="tx-sh-form"><div id="tx-blog-form"><table id="blog-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Posts</h2></td>\
			</tr>\
			<tr>\
				<th><label for="blog-ids">Category Id (optional)</label></th>\
				<td><input type="text" name="ids" id="blog-ids" value="" /><br />\
				<small>Add ids of categories to filter, keep it blank for all categories</small>\
			</tr>\
			<tr>\
				<th><label for="blog-items">Number of item</label></th>\
				<td><input type="number" max="12" min="0" id="blog-items" name="items" value="4" /><br />\
				<small>Specify the number of recent posts to show.</small></td>\
			</tr>\
			<tr>\
				<th><label for="blog-columns">Number of columns</label></th>\
				<td><input type="number" min="1" max="4" name="columns" id="blog-columns" value="4" /><br />\
				<small>Specify number of columns.</small>\
			</tr>\
			<tr>\
				<th><label for="blog-hidecat">Show/hide item category labels</label></th>\
				<td><select name="hidecat" id="blog-hidecat">\
					<option value="show">Show category labels</option>\
					<option value="hide">Hide category labels</option>\
				</select><br />\
				</td>\
			</tr>\
			<tr>\
				<th><label for="blog-showpage">Show Pagination</label></th>\
				<td><select name="showpage" id="blog-showpage">\
					<option value="no">No</option>\
					<option value="yes">Yes</option>\
				</select><br />\
				<small>Pagination will not work with carousel</small>\
				</td>\
			</tr>\
			<tr>\
				<th><label for="blog-carusel">Show as carousel</label></th>\
				<td><select name="carusel" id="blog-carusel">\
					<option value="no">No</option>\
					<option value="yes">Yes</option>\
				</select><br />\
				<small>Number of items must be greater then number of column</small>\
				</td>\
			</tr>\
			</table>\
		<p class="submit">\
			<input type="button" id="blog-submit" class="button-primary" value="Insert Posts" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		
		
		var table = form_blog.find('#blog-table');
		form_blog.appendTo('body').hide();
		
		
		// handles the click event of the submit button
		form_blog.find('#blog-submit').click(function(){

			var number_of_item = table.find('#blog-items').val(); 
			var number_of_column = table.find('#blog-columns').val();
			var show_hide_cat = table.find('#blog-hidecat').val();
			var category_id = table.find('#blog-ids').val();
			var show_page = table.find('#blog-showpage').val();			
			var show_carusel = table.find('#blog-carusel').val(); 			
			
			var shortcode = '[tx_blog items="'+number_of_item+'" columns="'+number_of_column+'" showcat="'+show_hide_cat+'" category_id="'+category_id+'" show_pagination="'+show_page+'" carousel="'+show_carusel+'"]<br/>';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});
		
		form_blog.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
	
	});		
	
	
	/*
	* Columns form
	*/
	jQuery(function(){
		// creates a form to be displayed everytime the button is clicked
		// you should achieve this using AJAX instead of direct html code like this
		var form_column = jQuery('<div id="column-form" class="tx-sh-form"><div id="tx-column-form"><table id="column-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Columns</h2></td>\
			</tr>\
			<tr>\
				<th><label for="column-size">Column Size</label></th>\
				<td><select name="size" id="column-size">\
					<option value="1/2">2 columns in a row</option>\
					<option value="1/3">3 columns in a row</option>\
					<option value="1/4">4 columns in a row</option>\
				</select><br />\
				<small>specify the column size, you can fruther manually edit them, 2/3 and 3/4 also can be used.</small></td>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="column-submit" class="button-primary" value="Insert Columns" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_column.find('#column-table');
		form_column.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_column.find('#column-submit').click(function(){
			
			var columns = table.find('#column-size').val(); 	
			var shortcode = '[tx_row]<br/>';
			
			if(columns=='1/2')
			{
				shortcode += '[tx_column size="1/2"]Content[/tx_column]<br/>[tx_column size="1/2"]Content[/tx_column]';
			}else if(columns=='1/3')
			{
				shortcode += '[tx_column size="1/3"]Content[/tx_column]<br/>[tx_column size="1/3"]Content[/tx_column]<br/>[tx_column size="1/3"]Content[/tx_column]';
			} else if(columns=='1/4')
			{
				shortcode += '[tx_column size="1/4"]Content[/tx_column]<br/>[tx_column size="1/4"]Content[/tx_column]<br/>[tx_column size="1/4"]Content[/tx_column]<br/>[tx_column size="1/4"]Content[/tx_column]';
			}
			
			shortcode += '<br/>[/tx_row]';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});

		form_column.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
			
	});	
	
	
	/*
	* heading form
	*/
	jQuery(function(){
		// creates a form to be displayed everytime the button is clicked
		// you should achieve this using AJAX instead of direct html code like this
		var form_heading = jQuery('<div id="heading-form" class="tx-sh-form"><div id="tx-heading-form"><table id="heading-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Heading</h2></td>\
			</tr>\
			<tr>\
				<th><label for="heading-style">Heading Style</label></th>\
				<td><select name="style" id="heading-style">\
					<option value="default">Default</option>\
				</select><br />\
				<small>Select the heading style</small></td>\
			</tr>\
			<tr>\
				<th><label for="heading-text">Heading Text</label></th>\
				<td><input type="text" name="text" id="heading-text" value="Heading Text" /><br />\
				<small>Specify the heading text.</small>\
			</tr>\
			<tr>\
				<th><label for="heading-tag">Heading Tag</label></th>\
				<td><select name="tag" id="heading-tag">\
					<option value="h1">H1</option>\
					<option value="h2" selected>H2</option>\
					<option value="h3">H3</option>\
					<option value="h4">H4</option>\
					<option value="h5">H5</option>\
					<option value="h6">H6</option>\
				</select><br />\
				<small>Select the Heading tag.</small></td>\
			</tr>\
			<tr>\
				<th><label for="heading-align">Text Alignment</label></th>\
				<td><select name="align" id="heading-align">\
					<option value="left">Left</option>\
					<option value="center">Center</option>\
					<option value="right">right</option>\
				</select><br />\
				<small>Select heading text alignment</small></td>\
			</tr>\
			<tr>\
				<th><label for="heading-size">Heading Size</label></th>\
				<td><input type="number" name="size" id="heading-size" min="0" max="120" value="24" /><br />\
				<small>Heading font size in px</small>\
			</tr>\
			<tr>\
				<th><label for="heading-margin">Heading Margin</label></th>\
				<td><input type="number" name="margin" id="heading-margin" min="0" max="120" value="24" /><br />\
				<small>Heading bottom margin in px</small>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="heading-submit" class="button-primary" value="Insert Heading" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_heading.find('#heading-table');
		form_heading.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_heading.find('#heading-submit').click(function(){
			
			var style = table.find('#heading-style').val();
			var heading_text = table.find('#heading-text').val();			
			var tag = table.find('#heading-tag').val();
			var size = table.find('#heading-size').val();
			var margin = table.find('#heading-margin').val();
			var align = table.find('#heading-align').val();			
													
			var shortcode = '[tx_heading style="'+style+'" heading_text="'+heading_text+'" tag="'+tag+'" size="'+size+'" margin="'+margin+'" align="'+align+'"]<br/>';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});

		form_heading.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
			
	});	
		
	
	
	/*
	* deviders form
	*/
	jQuery(function(){
		var form_devider = jQuery('<div id="devider-form" class="tx-sh-form"><div id="tx-devider-form"><table id="devider-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Dividers</h2></td>\
			</tr>\
			<tr>\
				<th><label for="devider-style">Divider Style</label></th>\
				<td><select name="style" id="devider-style">\
					<option value="default">Default</option>\
				</select><br />\
				<small>specify the divider style</small></td>\
			</tr>\
            <tr>\
				<th><label for="devider-padding">Divider Margin</label></th>\
				<td><input type="number" name="padding" id="devider-padding" min="0" max="120" value="24" /><br />\
				<small>Top and bottom margin in px</small>\
			</tr>\
        </table>\
		<p class="submit">\
			<input type="button" id="devider-submit" class="button-primary" value="Insert Devider" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_devider.find('#devider-table');
		form_devider.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_devider.find('#devider-submit').click(function(){
			
			var deviderpadding = table.find('#devider-padding').val(); 	
			var shortcode = '[tx_divider size="'+deviderpadding+'"]<br/>';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});

		form_devider.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
			
	});	
	
	
	/*
	* testimonials form
	*/
	jQuery(function(){
		var form_testimonial = jQuery('<div id="testimonial-form" class="tx-sh-form"><div id="tx-testimonial-form"><table id="testimonial-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Testimonials</h2></td>\
			</tr>\
			<tr>\
				<th><label for="testimonial-style">Testimonial Style</label></th>\
				<td><select name="style" id="testimonial-style">\
					<option value="default">Default</option>\
				</select><br />\
				<small>specify the testimonial style</small></td>\
			</tr>\
        </table>\
		<p class="submit">\
			<input type="button" id="testimonial-submit" class="button-primary" value="Insert Testimonials" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_testimonial.find('#testimonial-table');
		form_testimonial.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_testimonial.find('#testimonial-submit').click(function(){
			
			var testimonial_style = table.find('#testimonial-style').val(); 	
			var shortcode = '[tx_testimonial style="'+testimonial_style+'"]<br/>';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});

		form_testimonial.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
			
	});	
	
	
	/*
	* buttons form
	*/
	jQuery(function(){
		var form_button = jQuery('<div id="button-form" class="tx-sh-form"><div id="tx-button-form"><table id="button-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Buttons</h2></td>\
			</tr>\
			<tr>\
				<th><label for="button-style">Button Style</label></th>\
				<td><select name="style" id="button-style">\
					<option value="default">Default</option>\
				</select><br />\
				<small>specify the button style</small></td>\
			</tr>\
			<tr>\
				<th><label for="button-text">Button text</label></th>\
				<td><input type="text" name="text" id="button-text" value="Know More.." /><br />\
				<small>specify the button text.</small>\
			</tr>\
			<tr>\
				<th><label for="button-url">Button url</label></th>\
				<td><input type="text" name="url" id="button-url" value="" /><br />\
				<small>specify the button url.</small>\
			</tr>\
			<tr>\
				<th><label for="button-color">Button Color</label></th>\
				<td><input type="text" class="color" name="color" id="button-color" value="">\<br />\
				<small>Select button background color</small></td>\
			</tr>\
			<tr>\
				<th><label for="button-textcolor">Button Text Color</label></th>\
				<td><input type="text" class="color" name="textcolor" id="button-textcolor" value="">\<br />\
				<small>Select button text color</small></td>\
			</tr>\
        </table>\
		<p class="submit">\
			<input type="button" id="button-submit" class="button-primary" value="Insert Button" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_button.find('#button-table');
		
		tx_color_picker(form_button.find('.color'));
		
		form_button.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_button.find('#button-submit').click(function(){
			
			var button_style = table.find('#button-style').val();
			var button_text = table.find('#button-text').val();
			var button_url = table.find('#button-url').val();
			var button_color = table.find('#button-color').val();
			var button_textcolor = table.find('#button-textcolor').val();
			
			 	
			var shortcode = '[tx_button style="'+button_style+'" text="'+button_text+'" url="'+button_url+'" color="'+button_color+'" textcolor="'+button_textcolor+'"]';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});
		
		form_button.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
			
	});	
	
	
	/*
	* calltoact form
	*/
	jQuery(function(){
		var form_calltoact = jQuery('<div id="calltoact-form" class="tx-sh-form"><div id="tx-calltoact-form"><table id="calltoact-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Call To Act</h2></td>\
			</tr>\
			<tr>\
				<th><label for="calltoact-text">Call to act text</label></th>\
				<td><input type="text" name="text" id="calltoact-text" value="Call To Act Text" /><br />\
				<small>Specify the Call toa ct text.</small>\
			</tr>\
            <tr>\
				<th><label for="calltoact-button-text">Button text</label></th>\
				<td><input type="text" name="button-text" id="calltoact-button-text" value="Know More.." /><br />\
				<small>Specify the calltoact text.</small>\
			</tr>\
			<tr>\
				<th><label for="calltoact-url">Call to act url</label></th>\
				<td><input type="text" name="url" id="calltoact-url" value="" /><br />\
				<small>specify the calltoact button url.</small>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="button-submit" class="button-primary" value="Insert Call to act" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_calltoact.find('#calltoact-table');
		
		//tx_color_picker(form_calltoact.find('.color'));
		
		form_calltoact.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_calltoact.find('#button-submit').click(function(){
			
			var calltoact_text = table.find('#calltoact-text').val();
			var calltoact_button_text = table.find('#calltoact-button-text').val();
			var calltoact_url = table.find('#calltoact-url').val();
	 	
			var shortcode = '[tx_calltoact button_text="'+calltoact_button_text+'" url="'+calltoact_url+'"]'+calltoact_text+'[/tx_calltoact]';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});	
		form_calltoact.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});			
		
	});	
	
	
	/*
	* Services form
	*/
	jQuery(function(){
		var form_services = jQuery('<div id="services-form" class="tx-sh-form"><div id="tx-service-form"><table id="services-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Services</h2></td>\
			</tr>\
			<tr>\
				<th><label for="services-style">Services Style</label></th>\
				<td><select name="style" id="services-style">\
					<option value="default">Default (Circle)</option>\
					<option value="curved">Curved Corner</option>\
					<option value="square">Square</option>\
				</select><br />\
				<small>Specify the services style</small></td>\
			</tr>\
			<tr>\
				<th><label for="services-title">Services Title</label></th>\
				<td><input type="text" name="title" id="services-title" value="Services Title" /><br />\
				<small>Specify the Call toa ct text.</small>\
			</tr>\
			<tr>\
				<th><label for="services-icon">Services Icons</label></th>\
				<td><div class="awedrop">'+tx_font_awesome_include('tx-fa-icons')+'</div><br /><input type="text" name="icon" id="services-icon" value="" /></td>\
			</tr>\
			<tr>\
				<th><label for="services-content">Services Text</label></th>\
				<td><textarea name="content" id="services-content">Services content</textarea><br />\
				<small>Services content</small>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="button-submit" class="button-primary" value="Insert Services" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_services.find('#services-table');
		
		form_services.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_services.find('#button-submit').click(function(){
			
			var services_style = table.find('#services-style').val();
			var services_title = table.find('#services-title').val();
			var services_icon = table.find('#services-icon').val();
			var services_content = table.find('#services-content').val();
	 	
			var shortcode = '[tx_services style="'+services_style+'" title="'+services_title+'" icon="'+services_icon+'"]'+services_content+'[/tx_services]';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});

		form_services.find('.tx-fa-icons .fa').click(function(){
			jQuery('.tx-fa-icons .active').removeClass('active');
			jQuery(this).addClass('active');
			//console.log( jQuery(this).data('value') );
			var tx_icon = jQuery(this).data('value');
			jQuery('#services-icon').val(tx_icon);
		});				
		
		form_services.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});			
		
	});	
					
	
	/*
	* spacer form
	*/
	jQuery(function(){
		var form_spacer = jQuery('<div id="spacer-form" class="tx-sh-form"><div id="tx-spacer-form"><table id="spacer-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Spacer</h2></td>\
			</tr>\
			<tr>\
				<th><label for="spacer-size">Spacer Size (height in px)</label></th>\
				<td><input type="number" min="0" max="120" name="size" id="spacer-size" value="16" /><br />\
				<small>Use spacer to manage vertical gaps</small>\
			</tr>\
        </table>\
		<p class="submit">\
			<input type="button" id="spacer-submit" class="button-primary" value="Insert Spacer" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_spacer.find('#spacer-table');
		form_spacer.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_spacer.find('#spacer-submit').click(function(){
			
			var spacer_size = table.find('#spacer-size').val(); 	
			var shortcode = '[tx_spacer size="'+spacer_size+'"]<br/>';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});

		form_spacer.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
			
	});	
	
	
	/*
	* wooproducts form
	*/
	jQuery(function(){
		var form_wooprods = jQuery('<div id="wooprods-form" class="tx-sh-form"><div id="tx-wooprods-form"><table id="wooprods-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>WooCommerce Products Carousel</h2></td>\
			</tr>\
			<tr>\
				<th><label for="wooprods-type">Product Listing Type</label></th>\
				<td><select name="style" id="wooprods-type">\
					<option value="product_categories">Product Categories</option>\
					<option value="recent_products">Recent Products</option>\
					<option value="featured_products">Featured Products</option>\
					<option value="sale_products">Products On Sale</option>\
					<option value="best_selling_products">Best Selling Products</option>\
					<option value="top_rated_products">Top Rated Products</option>\
					<option value="products">Products By Ids</option>\
				</select><br />\
				<small>Specify product listing type</small></td>\
			</tr>\
			<tr>\
				<th><label for="wooprods-ids">Category/Product Ids (optional)</label></th>\
				<td><input type="text" name="ids" id="wooprods-ids" value="" /><br />\
				<small>Comma separeted category or product ids (works with "Product Categories" and "Products By Ids" )</small>\
			</tr>\
			<tr>\
				<th><label for="wooprods-columns">Number Of Columns</label></th>\
				<td><input type="number" min="1" max="4" name="coumns" id="wooprods-columns" value="4" /><br />\
				<small>Number of columns or items visible</small>\
			</tr>\
			<tr>\
				<th><label for="wooprods-items">Number Of Items</label></th>\
				<td><input type="number" min="1" max="16" name="items" id="wooprods-items" value="8" /><br />\
				<small>Total number of items</small>\
			</tr>\
        </table>\
		<div class="nx-sh-cancel">\
			<input type="button" id="wooprods-submit" class="button-primary" value="Insert Wooprods" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</div>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_wooprods.find('#wooprods-table');
		form_wooprods.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_wooprods.find('#wooprods-submit').click(function(){
			
			var wooprods_type = table.find('#wooprods-type').val();
			var wooprods_ids = table.find('#wooprods-ids').val();
			var wooprods_columns = table.find('#wooprods-columns').val();			
			var wooprods_items = table.find('#wooprods-items').val(); 
				
			var shortcode = '[tx_prodscroll type="'+wooprods_type+'" ids="'+wooprods_ids+'" columns="'+wooprods_columns+'" items="'+wooprods_items+'"]<br/>';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});

		form_wooprods.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
			
	});	
	
	
	/*
	* Slider
	*/
	jQuery(function(){
		var form_slider = jQuery('<div id="slider-form" class="tx-sh-form"><div id="tx-slider-form"><table id="slider-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>i-trans Slider</h2></td>\
			</tr>\
			<tr>\
				<th><label for="slider-style">Slider Style</label></th>\
				<td><select name="style" id="slider-style">\
					<option value="default">Default</option>\
				</select><br />\
				<small>Select slider style</small></td>\
			</tr>\
			<tr>\
				<th><label for="slider-category">Slider Category</label></th>\
				<td><select name="category" id="slider-category">\
					<option value="">All</option>'+tx_slider_cat()+'\
				</select><br />\
				<small>Select slider category for category based multiple slider(optional)</small></td>\
			</tr>\
			<tr>\
				<th><label for="slider-items">Number Of Items (slides)</label></th>\
				<td><input type="number" min="1" max="16" name="items" id="slider-items" value="4" /><br />\
				<small>Number of slides in the slider</small>\
			</tr>\
			<tr>\
				<th><label for="slider-delay">Delay</label></th>\
				<td><input type="number" min="1000" max="16000" name="delay" step="500" id="slider-delay" value="8000" /><br />\
				<small>Duration between slides in miliseconds</small>\
			</tr>\
        </table>\
		<div class="nx-sh-cancel">\
			<input type="button" id="slider-submit" class="button-primary" value="Insert Slider" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</div>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_slider.find('#slider-table');
		form_slider.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_slider.find('#slider-submit').click(function(){
			
			var style = table.find('#slider-style').val();
			var category = table.find('#slider-category').val();
			var slider_items = table.find('#slider-items').val();			
			var slider_delay = table.find('#slider-delay').val(); 
				
			var shortcode = '[tx_slider style="'+style+'" category="'+category+'" delay="'+slider_delay+'" items="'+slider_items+'"]<br/>';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});

		form_slider.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
			
	});
	
	
	/*
	* image form
	*/
	jQuery(function(){
		var form_image = jQuery('<div id="image-form" class="tx-sh-form"><div id="tx-image-form"><table id="image-table" class="form-table">\
			<tr>\
				<td class="tx-heading" colspan="2"><h2>Insert Image</h2></td>\
			</tr>\
			<tr>\
				<th><label for="image-width">Image Width</label></th>\
				<td><input type="number" min="60" max="1200" name="width" id="image-width" value="600" /><br />\
			</tr>\
			<tr>\
				<th><label for="image-height">Image Height</label></th>\
				<td><input type="number" min="60" max="1200" name="height" id="image-height" value="600" /><br />\
			</tr>\
			<tr>\
				<th><label for="image-alt">Alternate Text</label></th>\
				<td><input type="text" name="alttext" id="image-alt" /><br />\
			</tr>\
			<tr>\
				<th><label for="image-url">Image URL</label></th>\
				<td><input type="text" name="url" id="image-url" /><br />\
				<input type="button" class="tx-button" name="tx-img-upload" id="tx-upload-button" value="Upload Image">\
			</tr>\
        </table>\
		<p class="submit">\
			<input type="button" id="image-submit" class="button-primary" value="Insert Image" name="submit" />\
			<input type="button" id="modal-close" class="modal-close button-primary" value="Cancel" name="Cancel" />\
		</p>\
		<div class="tnext-bottom-lebel">'+tx_footer_include()+'</div>\
		</div></div>');
		
		var table = form_image.find('#image-table');
		form_image.appendTo('body').hide();
		
		// handles the click event of the submit button
		form_image.find('#image-submit').click(function(){
			
			var image_width = table.find('#image-width').val(); 
			var image_height = table.find('#image-height').val(); 			
			var image_url = table.find('#image-url').val();
			
			var shortcode = '[tx_image width="'+image_width+'" height="'+image_height+'" url="'+image_url+'"]<br/>';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			jQuery.colorbox.close();
		});
		
		var file_frame;
		
		form_image.find('#tx-upload-button').click(function(event){

			event.preventDefault();
		
			// If the media frame already exists, reopen it.
			if ( file_frame ) {
			  file_frame.open();
			  return;
			}
		
			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
			  title: jQuery( this ).data( 'uploader_title' ),
			  button: {
				text: jQuery( this ).data( 'uploader_button_text' ),
			  },
			  multiple: false  // Set to true to allow multiple files to be selected
			});
		
			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
			  // We set multiple to false so only get one image from the uploader
			  attachment = file_frame.state().get('selection').first().toJSON();
		
			  // Do something with attachment.id and/or attachment.url here
			  table.find('#image-url').val(attachment.url); 	
			  
			});
		
			// Finally, open the modal
			file_frame.open();

			
		});			

		form_image.find('#modal-close').click(function(){
			jQuery.colorbox.close();
		});	
			
	});
			
	

})();


jQuery(window).resize( function() {
	tx_resize_thickbox();
});

function tx_resize_thickbox() {
	var TB_WIDTH;
	var TB_HEIGHT;
	jQuery(document).find('#TB_window').width( TB_WIDTH ).height( TB_HEIGHT ).css( 'margin-left', - TB_WIDTH / 2 );
}

