<?php

/**
 * Constants
 */
$discussions = null;
$discussion = null;
$discussions_count = 0;
$discussions_index = 0;

$replies = null;
$reply = null;
$replies_count = 0;
$replies_index = 0;

/**
 * Check if have discussions to loop through
 */
function have_discussion() {
	global $discussions_index, $discussion, $discussions, $discussions_count, $discussion_title;

	// check if single discussion page
	if (!$discussion_title) {
		$discussions_count = count(get_discussions());
		$discussions = get_discussions();
	} else {
		$discussions_count = 1;
		$discussions = get_discussion($discussion_title);
	}

	if ($discussions && $discussions_index + 1 <= $discussions_count) {
		$discussions_index++;
		return true;
	} else {
		$discussions_count = 0;
		return false;
	}
}

/**
 * LOOP
 * Updates the discussion object
 */
function thediscussion() {
	global $discussions_index, $discussion, $discussions, $discussion_title;
	$discussion = $discussions[$discussions_index - 1];
	return $discussion;
}

/**
 * Get all discussions
 */
function get_discussions() {
	$query = database::getInstance()->query("SELECT * FROM `" . DB_PREFIX . "Discussion` ORDER BY `timestamp` DESC");
	$rows = $query->fetchAll();
	$discuss_array;
	for($i = 0; $i < count($rows); $i++) {
	    $discuss_array[$i]['title'] = $rows[$i]['title'];
	    $discuss_array[$i]['content'] = $rows[$i]['content'];
	    $discuss_array[$i]['author'] = $rows[$i]['author'];
	    $discuss_array[$i]['time'] = $rows[$i]['time'];
	    $discuss_array[$i]['replies'] = $rows[$i]['replies'];
	}
	return $discuss_array;
}

/**
 * Get indivigual discussion details
 */
function get_discussion($title) {
	$decodedtitle = discussion::decode_title($title);
	$query = database::getInstance()->query("SELECT * FROM `" . DB_PREFIX . "Discussion` WHERE `title`='$decodedtitle'");
	$rows = $query->fetchAll();
	$discuss_array;
	foreach ($rows as $row) {
		$discuss_array[0]['title'] = $row['title'];
	    $discuss_array[0]['content'] = $row['content'];
	    $discuss_array[0]['author'] = $row['author'];
	    $discuss_array[0]['time'] = $row['time'];
	    $discuss_array[0]['replies'] = $row['replies'];
	}
	return $discuss_array;
}

/**
 * Show the discussion menu
 */
function discussion_menu($title) {
	$title = discussion::decode_title($title);
	$query = database::getInstance()->query("SELECT * FROM `" . DB_PREFIX . "Discussion` WHERE `title`='$title'");
	$rows = $query->fetchAll();
	$owner;
	foreach ($rows as $row) { $owner = $row['author']; }
	// display these if it's the owner of the article
	if ($owner == auth::getCurrentUser()) {
		echo '<a href="' . delete_link($title) . '"><button class="red">Delete</button></a>';
	} else {
		// don't display delete, because they're not the owner
	}
}

/**
 * Get's the link to delete a discussion
 */
function delete_link($title) {
	return BASE . 'discussion' . DS . discussion::encode_title($title) . DS . 'delete';
}

/**
 * Create new discussion link
 */
function get_createlink() {
	return BASE . 'discussion' . DS . 'create';
}

/**
 * Create new discussion backend link
 */
function get_submitLink() {
	return BASE . 'discussion' . DS . 'submit';
}

/**
 * LOOP
 * Create get the reply form
 */
function reply_form($btnText = 'Reply') {
	global $discussion_title;

	if (auth::isLoggedIn()) {
		echo '
		<form name="input" action="' . BASE . 'discussion' . DS . discussion::encode_title($discussion_title) . DS . 'reply' . '" method="post">
			<textarea rows="18" placeholder="Your thoughts..." name="content" class="boxsizingBorder"></textarea><br/>
			<input type="submit" class="submit small" value="' . $btnText .'"/>
		</form>
		';
	} else {
		echo '<a href="http://' . getenv(DOMAIN_NAME) . BASE . 'login' . '">Please log in to reply</a>';
	}
}

/**
 * LOOP
 * Gets replies for a discussion
 */
function get_replies($title) {
	if (is_array(discussion::get_replies($title))) {
		return discussion::get_replies($title);
	} else {
		return array();
	}
}

/**
 * LOOP
 * Gets discussion title
 */
function the_title() {
	global $discussion;
	return $discussion['title'];
}

/**
 * LOOP
 * Gets discussion author
 */
function the_author() {
	global $discussion;
	return $discussion['author'];
}

/**
 * LOOP
 * Gets discussion link
 */
function the_link() {
	global $discussion;
	return BASE . 'discussion' . DS . discussion::encode_title($discussion['title']);
}

/**
 * LOOP
 * Gets discussion posted time
 */
function the_time() {
	global $discussion;
	return $discussion['time'];
}

/**
 * LOOP
 * Gets discussion content
 */
function the_content() {
	global $discussion;
	return Parsedown::instance()->parse($discussion['content']);
}

/**
 * LOOP
 * Check if have discussions to loop through
 */
function have_replies() {
	global $replies_index, $reply, $replies, $replies_count, $discussion_title;

	$replies_count = count(get_replies($discussion_title));
	$replies = get_replies($discussion_title);

	if ($replies && $replies_index + 1 <= $replies_count) {
		$replies_index++;
		return true;
	} else {
		$replies_count = 0;
		return false;
	}
}

/**
 * LOOP
 * Updates the reply object
 */
function thereply() {
	global $replies_index, $reply, $replies;

	$reply = $replies[$replies_index - 1];
	return $reply;
}

/**
 * LOOP
 * Gets the reply author
 */
function reply_author() {
	global $reply;
	return $reply['author'];
}

/**
 * LOOP
 * Gets the reply posted time
 */
function reply_time() {
	global $reply;
	return $reply['time'];
}

/**
 * LOOP
 * Gets the reply content
 */
function reply_content() {
	global $reply;
	return Parsedown::instance()->parse($reply['content']);
}

?>