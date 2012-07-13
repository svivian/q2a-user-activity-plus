<?php
/*
	Question2Answer User Activity Plus plugin, v1.0
	License: http://www.gnu.org/licenses/gpl.html
*/

class qa_html_theme_layer extends qa_html_theme_base
{
	function q_list_and_form($q_list)
	{
		qa_html_theme_base::q_list_and_form($q_list);
		$handle = $this->_user_handle();

		// output activity links under recent activity
		if ( $this->template === 'user' )
		{
			$this->output(
				'<div class="qa-useract-page-links">',
				'	More activity: ',
				'	<a href="' . qa_path('user-activity/questions/'.$handle) . '">All questions</a>',
				'	&bull; ',
				'	<a href="' . qa_path('user-activity/answers/'.$handle) . '">All answers</a>',
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
					$field['value'] .= ' &mdash; <a href="' . $url . '">All questions by ' . qa_html($handle) . ' &rsaquo;</a>';
				}
				else if ( $key === 'answers' )
				{
					$url = qa_path('user-activity/answers/'.$handle);
					$field['value'] .= ' &mdash; <a href="' . $url . '">All answers by ' . qa_html($handle) . ' &rsaquo;</a>';
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
