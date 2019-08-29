// JavaScript Document
	function wpsu_go_premium(){
		alert('Go premium for this feature.');
	}
	
	jQuery(document).ready(function($){
		$('.main .selection_css').click(function(){
			$('.selection_div, .wpsu_toggle').hide();
			$('.selection_div.css, .wpsu_toggle.css').show();
			//document.location.href = 'options-general.php?page=wp_su&type=css';
		});
		$('.main .selection_js').click(function(){
			$('.selection_div, .wpsu_toggle').hide();
			$('.selection_div.js, .wpsu_toggle.js').show();			
			//document.location.href = 'options-general.php?page=wp_su&type=js';
		});	
		
		$('.main .images_compression_report').click(function(){
			document.location.href = 'options-general.php?page=wp_su&type=img';
		});	
		
		$('.images_compression_report ul li.wpsu_link_dir a.wpsu_ud').click(function(event){
			
			event.preventDefault();
			
			var linked = $(this).parent().attr('data-linked');
			if(wpsu_pro){
				document.location.href = 'options-general.php?page=wp_su&type=img&wpsu_link_dir='+linked;
			}else{
				wpsu_go_premium();
			}
			
		});			
	
		$('a.wpsu_ct').click(function(event){
			event.preventDefault();
			
			if(wpsu_pro){
				var ask = true;
				if($('.wpsu_temp_text').length>0){					
					ask = confirm("Are you sure you want to compress the already compressed images again?\n\n"+"This action is not reversible and you might will lose your original images.\n\n"+"It is recommended that you switch back all original directories and then compress or make a backup prior this action.");
				}
				if(ask){
					document.location.href = 'options-general.php?page=wp_su&type=img&wpsu_ct';
				}
			}else{
				wpsu_go_premium();
			}
			
		});	
		
		
		$('a.wpsu_ctr').click(function(event){	
			event.preventDefault();
			document.location.href = 'options-general.php?page=wp_su&type=img';
			
		});		
		
			
		$('a.wpsu_ci').click(function(event){	
			event.preventDefault();
			var ask = confirm("Are you sure you want to delete all temp directories?\n\n"+$(this).attr('title'));
			if(ask){
				document.location.href = 'options-general.php?page=wp_su&type=img&wpsu_clear_imgs';
			}else{
				return false;
			}
			
		});	
		
		$('.settings .selection_js').click(function(){
			if($(this).hasClass('disabled')){
				$(this).removeClass('disabled');
				$('input[name="selection_js"]').val(1);
			}else{
				$(this).addClass('disabled');
				$('input[name="selection_js"]').val(0);
			}
		});	
		
		$('.settings .selection_css').click(function(){
			if($(this).hasClass('disabled')){
				$(this).removeClass('disabled');
				$('input[name="selection_css"]').val(1);
			}else{
				$(this).addClass('disabled');
				$('input[name="selection_css"]').val(0);
			}
		});		
		
		$('.wpsu_toggle').click(function(){
			$('.selection_div.sub').hide();
			$('.selection_div.main').show();
			$(this).hide();
		});
		
		$('.wpsu_modes').click(function(){
			var mode = $(this).attr('data-mode');
			$('.wpsu_todo_area').hide();
			$('.selection_div.main').hide();
			$('.wpsu_booster_area').hide();
			switch(mode){
				case "classic":					
					$('.selection_div.main').show();
				break;
				case "advanced":
					$('.wpsu_todo_area').show();									
				break;
				case "booster":
					$('.wpsu_booster_area').show();			
				break;				
			}
		});
	});