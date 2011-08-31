<?php
/*
	Question2Answer User Activity Plus plugin, v1.0
	License: http://www.gnu.org/licenses/gpl.html
*/

class qa_user_activity
{
	private $directory;
	private $urltoroot;
	private $user;
	private $reqmatch = '#user-activity/(questions|answers)/([^/]+)#';


	function load_module( $directory, $urltoroot )
	{
		$this->directory = $directory;
		$this->urltoroot = $urltoroot;
	}

	function suggest_requests() // for display in admin interface
	{
		return array(
			array(
				'title' => 'User Activity Plus',
				'request' => 'user-activity',
				'nav' => null,
			),
		);
	}

	function match_request( $request )
	{
		return preg_match( $this->reqmatch, $request ) > 0;
	}

	function process_request( $request )
	{
		// get all variables
		preg_match( $this->reqmatch, $request, $matches );
		$post_type = $matches[1];
		$handle = $matches[2];
		$start = (int) qa_get('start');
		$pagesize = qa_opt('page_size_qs');

		// regular page request
		$qa_content=qa_content_prepare();
		require_once QA_INCLUDE_DIR.'qa-util-string.php';

		// list of questions by this user
		if ( $post_type === 'questions' )
		{
			$qa_content['title']='Questions asked by ' . qa_html($handle);

			list( $userid, $count, $sel_count ) = $this->_questions_stats( $handle );

			// get questions
			$columns = 'postid, categoryid, type, LEFT(type,1) AS basetype, INSTR(type,"_HIDDEN")>0 AS hidden, acount, selchildid, upvotes, downvotes, netvotes, hotness, flagcount, BINARY title AS title, BINARY tags AS tags, UNIX_TIMESTAMP(created) AS created';
			$sql_questions = 'SELECT '.$columns.' FROM ^posts WHERE type="Q" AND userid=# ORDER BY created DESC LIMIT #,#';
			$result = qa_db_query_sub( $sql_questions, $userid, $start, $pagesize );
			$questions = qa_db_read_all_assoc($result);

			$htmloptions = qa_post_html_defaults('Q');
			$htmloptions['whoview'] = false;
			$htmloptions['avatarsize'] = 0;

			// html for stats
			$qa_content['custom'] =
				'<div class="qa-useract-stats">' .
				'	<div class="qa-useract-stat"><span class="qa-useract-count">' . $count . '</span><br>questions</div>' .
				'	<div class="qa-useract-stat"><span class="qa-useract-count">' . $sel_count . '</span><br>selected answers</div>' .
				'</div>';

			// create html for question list
			$qa_content['q_list']['qs']=array();
			foreach ( $questions as $question )
				$qa_content['q_list']['qs'][] = qa_any_to_q_html_fields($question, qa_get_logged_in_userid(), qa_cookie_get(), null, null, $htmloptions);

			// pagination
			$qa_content['page_links'] = qa_html_page_links($request, $start, $pagesize, $count, qa_opt('pages_prev_next'), null);

			return $qa_content;
		}
		else if ( $post_type === 'answers' )
		{
			$qa_content['title']='Questions answered by ' . qa_html($handle);

			// userid and answer count
			$sql_count =
				'SELECT u.userid, count(a.postid) AS qs, sum(q.selchildid=a.postid) AS selected ' .
				'FROM ^posts a, ^posts q, ^users u ' .
				'WHERE a.parentid=q.postid AND u.userid=a.userid AND a.type="A" AND q.type="Q" AND u.handle=$';
			$result = qa_db_query_sub( $sql_count, $handle );
			$row = qa_db_read_one_assoc($result);
			$userid = $row['userid'];
			$count = $row['qs'];
			$sel_count = $row['selected'];

			// get answers
			$columns = 'q.postid AS qpostid, BINARY q.title AS qtitle, q.selchildid AS qselid, q.netvotes AS qvotes, a.postid AS apostid, BINARY a.content AS acontent, a.netvotes AS avotes, UNIX_TIMESTAMP(a.created) AS acreated';
			$sql_answers = 'SELECT '.$columns.' FROM ^posts a, ^posts q WHERE a.parentid=q.postid AND a.type="A" AND q.type="Q" AND a.userid=# ORDER BY a.created DESC LIMIT #,#';

			$result = qa_db_query_sub( $sql_answers, $userid, $start, $pagesize );
			$answers = qa_db_read_all_assoc($result);

			$qa_content['custom'] =
				'<div class="qa-useract-stats">' .
				'	<div class="qa-useract-stat"><span class="qa-useract-count">' . $count . '</span><br>answers</div>' .
				'	<div class="qa-useract-stat"><span class="qa-useract-count">' . $sel_count . '</span><br>best answers</div>' .
				'</div>';

			$qa_content['custom_2'] = '<div class="qa-useract-wrapper">';

			foreach ( $answers as $answer )
			{
				// answer snippet
				$answer['acontent'] = qa_substr( strip_tags($answer['acontent']), 0, 100 );
				if ( strlen($answer['acontent']) == 100 )
					$answer['acontent'] .= '...';

				// question url
				$answer['qurl'] = qa_path_html( qa_q_request( $answer['qpostid'], $answer['qtitle'] ) );

				// answer date
				$answer['acreated'] = qa_html( qa_time_to_string( qa_opt('db_time')-$answer['acreated'] ) );

				// html content
				$qa_content['custom_2'] .= $this->_answer_tmpl( $answer );
			}
			$qa_content['custom_2'] .= '</div>';

			// pagination
			$qa_content['page_links'] = qa_html_page_links($request, $start, $pagesize, $count, qa_opt('pages_prev_next'), null);

			return $qa_content;
		}
	}


	// userid, question count and selected count
	function _questions_stats( $handle )
	{
		$sql_count = 'SELECT u.userid, count(p.postid) AS qs, count(p.selchildid) AS selected FROM qa_posts p, qa_users u WHERE p.type="Q" AND u.userid=p.userid AND u.handle=$';
		$result = qa_db_query_sub( $sql_count, $handle );
		$row = qa_db_read_one_assoc($result);

		return array( $row['userid'], $row['qs'], $row['selected'] );

	}

	function _answer_tmpl( $answer )
	{
		$qa_html = '';
		$qa_html .= '<div class="qa-q-list-item">';
		$qa_html .= '		<span class="qa-a-count' . ($answer['qselid']==$answer['apostid'] ? ' qa-a-count-selected' : '') . '">';
		$qa_html .= '			<span class="qa-a-count-data">' . $answer['avotes'] . '</span>';
		$qa_html .= '			<span class="qa-a-count-pad"> votes</span>';
		$qa_html .= '		</span>';
		$qa_html .= '	<div class="qa-q-item-main">';
		$qa_html .= '		<div class="qa-q-item-title">';
		$qa_html .= '			<a href="' . $answer['qurl'] . '#a' . $answer['apostid'] . '">' . $answer['qtitle'] . '</a>';
		$qa_html .= '		</div>';
		$qa_html .= '		<span class="qa-q-item-meta">';
		$qa_html .= '			<span class="qa-q-item-what">answered</span>';
		$qa_html .= '			<span class="qa-q-item-when">';
		$qa_html .= '				<span class="qa-q-item-when-data">' . $answer['acreated'] . '</span>';
		$qa_html .= '				<span class="qa-q-item-when-pad"> ago</span>';
		$qa_html .= '			</span>';
		$qa_html .= '		</span>';
		$qa_html .= '		<div class="qa-a-snippet">';
		$qa_html .= '			' . $answer['acontent'];
		$qa_html .= '		</div>';
		$qa_html .= '	</div>';
		$qa_html .= '	<div class="qa-q-item-clear">';
		$qa_html .= '	</div>';
		$qa_html .= '</div>';

		return $qa_html;
	}

}
