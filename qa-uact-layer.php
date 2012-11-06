<?php
/*
	Question2Answer User Activity Plus plugin, v1.0
	License: http://www.gnu.org/licenses/gpl.html
*/

class qa_html_theme_layer extends qa_html_theme_base
{
	function head_css()
	{
		parent::head_css();
		if ( $this->template === 'user' )
		{
			$this->output( uact_css() );
		}
	}

	function q_list_and_form($q_list)
	{
		qa_html_theme_base::q_list_and_form($q_list);
		$handle = $this->_user_handle();

		// output activity links under recent activity
		if ( $this->template === 'user' )
		{
			$this->output(
				'<div class="qa-useract-page-links">',
				qa_lang_html('useractivity/more_activity') . ':',
				'	<a href="' . qa_path('user-activity/questions/'.$handle) . '">' . qa_lang_html('useractivity/all_questions') . '</a>',
				'	&bull; ',
				'	<a href="' . qa_path('user-activity/answers/'.$handle) . '">' . qa_lang_html('useractivity/all_answers') . '</a>',
				'</div>'
			);
		}
	}

	// append activity links to question and answer counts
	function form_fields($form, $columns)
	{
		$handle = $this->_user_handle();

		if ( $this->template === 'user' && !empty($form['fields']) )
		{
			foreach ($form['fields'] as $key=>&$field)
			{
				if ( $key === 'questions' )
				{
					$url = qa_path('user-activity/questions/'.$handle);
					$field['value'] .= ' &mdash; <a href="' . $url . '">' . qa_lang_html_sub('useractivity/all_questions_by', $handle) . ' &rsaquo;</a>';
				}
				else if ( $key === 'answers' )
				{
					$url = qa_path('user-activity/answers/'.$handle);
					$field['value'] .= ' &mdash; <a href="' . $url . '">' . qa_lang_html_sub('useractivity/all_answers_by', $handle) . ' &rsaquo;</a>';
				}
			}
		}

		qa_html_theme_base::form_fields($form, $columns);
	}


	// grab the handle of the profile you're looking at
	function _user_handle()
	{
		preg_match( '#user/([^/]+)#', $this->request, $matches );
		return !empty($matches[1]) ? $matches[1] : null;
	}

}
