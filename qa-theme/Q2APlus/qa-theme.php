<?php
/*  
	Theme Name :Q2A+
	Theme URI: http://www.Askoverflow.com
	Theme Version: 1.0
	Theme Date: 2013-03-08
	Theme Author: Robin Sharaya
	Theme Author URI: http://www.Askoverflow.com
	Theme License: GPLv2
	Theme Update Check URI: 
*/
/*
Custom Options For The Theme.
*/
    class qa_html_theme extends qa_html_theme_base
    {
	
	
			function nav_user_search() // outputs login form if user not logged in
		{
			if (!qa_is_logged_in()) {
				$login=@$this->content['navigation']['user']['login'];
				
				if (isset($login)) {
					$this->output(
						'<!--[Begin: login form]-->',				
						'<form id="qa-loginform" action="'.$login['url'].'" method="post">',
							'<input type="text" id="qa-userid" name="emailhandle" placeholder="'.trim(qa_lang_html('users/email_handle_label'), ':').'" />',
							'<input type="password" id="qa-password" name="password" placeholder="'.trim(qa_lang_html('users/password_label'), ':').'" />',
							'<div id="qa-rememberbox"><input type="checkbox" name="remember" id="qa-rememberme" value="1"/>',
							'<label for="qa-rememberme" id="qa-remember">'.qa_lang_html('users/remember').'</label></div>',
							'<input type="submit" value="'.$login['label'].'" id="qa-login" name="dologin" />',
						'</form>',				
						'<!--[End: login form]-->'
					);
					unset($this->content['navigation']['user']['login']); // removes regular navigation link to log in page
				}
			}
			
			qa_html_theme_base::nav_user_search();
		}
				
	function footer()
		{
			// Custom Footer For Theme
			
			$this->output('<DIV CLASS="qa-footer">');
			$this->output('<DIV CLASS="qa-footer-inner">');
			$this->nav('footer');
			$this->attribution();
			$this->footer_clear();
			$this->output('<DIV CLASS="copyright">');
			$this->output('<p>Copyright &copy; '.date('Y').' '.$this->content['site_title'].' - All rights reserved. <span CLASS="footer-Centercopyright">Powered by <A HREF="http://www.question2answer.org/" rel="nofollow" target="_blank">Question2Answer</A></span><span CLASS="footer-Rightcopyright">Theme Designed by <A HREF="http://www.Askoverflow.com" rel="dofollow" target="_blank">Askoverflow</A></span></p>');
			$this->output('</DIV>');
			$this->output('</DIV>');
			$this->output('</DIV> <!-- END qa-footer -->', '');
		}

		
		function attribution()
		{
				
			$this->output(
				'<DIV CLASS="qa-attribution">',
				'</DIV>'            
			);
		}	

		
		function footer_clear()
		{
			$this->output(
				'<DIV CLASS="qa-footer-clear">',
				'</DIV>'
			);
		}

		
		function post_meta_who($post, $class)
		{
			if (isset($post['who'])) {
				$this->output('<SPAN CLASS="'.$class.'-who">');
				
				if (strlen(@$post['who']['prefix']))
					$this->output('<SPAN CLASS="'.$class.'-who-pad">'.$post['who']['prefix'].'</SPAN>');
				
				if (isset($post['who']['data']))
					$this->output('<SPAN CLASS="'.$class.'-who-data">'.$post['who']['data'].'</SPAN>');
				
				if (isset($post['who']['title']))
					$this->output('<SPAN CLASS="'.$class.'-who-title">'.$post['who']['title'].'</SPAN>');
					
				// You can also use $post['level'] to get the author's privilege level (as a string)
	
				if (isset($post['who']['points'])) {
					$post['who']['points']['prefix']='('.$post['who']['points']['prefix'];
					$post['who']['points']['suffix'].=')';
					$this->output_split($post['who']['points'], $class.'-who-points', 'SPAN title="Reputation Score"');
				}
				
				if (strlen(@$post['who']['suffix']))
					$this->output('<SPAN CLASS="'.$class.'-who-pad">'.$post['who']['suffix'].'</SPAN>');
	
				$this->output('</SPAN>');
			}
		}		
		
		
		function view_count($post) 
		{ 
			$post['views']['data'] = preg_replace('/000$/', 'k', $post['views']['data']); 
			parent::view_count($post); 
		}
		
		function post_tag_item($tag, $class) 
		{ 
			$this->output('<LI CLASS="'.$class.'-tag-item '.strip_tags($tag).'">'.$tag.'</LI>');
		}
		
		
		function q_item_main($q_item) 
        { 
            $this->output('<DIV CLASS="qa-q-item-main">'); 
             
            $this->view_count($q_item); 
            $this->q_item_title($q_item); 
            $this->q_item_content($q_item); 

            $this->post_avatar_meta($q_item, 'qa-q-item'); 
            $this->post_tags($q_item, 'qa-q-item'); 
            $this->q_item_buttons($q_item); 
                 
            $this->output('</DIV>'); 
        }
    
		function body_content()
		{
			$this->body_prefix();
			$this->notices();
			
			$this->output('<DIV CLASS="header-wrapper">', '');
			$this->widgets('full', 'top');
			$this->header();
			$this->output('</DIV> <!-- END header-wrapper -->');
			
			$this->output('<DIV CLASS="left-content">', '');
			$this->nav_main_sub();
			$this->output('</DIV> <!-- END left-content -->');
			
			$this->output('<DIV CLASS="qa-body-wrapper">', '');	
			
			$this->output('<DIV CLASS="qa-nav-sub-menu">', '');
			$this->nav('sub');
			$this->output('</DIV> <!-- END qa-nav-sub-menu -->');
			
			$this->output('<DIV CLASS="right-content">', '');
			$this->widgets('full', 'high');
			$this->sidepanel();
			$this->main();
			$this->widgets('full', 'low');
			$this->output('</DIV> <!-- END left-content -->');
			
			$this->output('</DIV> <!-- END body-wrapper -->');
			
			$this->output('<DIV CLASS="footer-wrapper">', '');
			$this->footer();
			$this->widgets('full', 'bottom');
			$this->output('</DIV> <!-- END footer-wrapper -->');
			

			
			$this->body_suffix();
		}
		
		function header()
		{
			$this->output('<DIV CLASS="qa-header">');
			
			$this->logo();
			$this->nav_user_search();
			$this->header_clear();
			
			$this->output('</DIV> <!-- END qa-header -->', '');
		}
		
		function nav_main_sub()
		{
			$this->nav('main');
		}
		
    }
?>