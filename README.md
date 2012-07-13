
User Activity Plus (Question2Answer plugin)
=================================================

This is a page plugin for popular open source Q&A platform, [Question2Answer](http://www.question2answer.org). It adds the functionality to show every question and answer of a user.

The posts list is paginated and uses the value set under Admin > Lists > Questions page length (default 20). Answers use a similar design to question lists but also shows a snippet of the answer. Both lists include the class `qa-a-count-selected` where applicable for appropriate styling of selected answers.



Installation & Usage
-------------------------------------------------

1. Download and extract the files to a subfolder such as `user-activity-plus` inside the `qa-plugins` folder of your Q2A installation.

2. By default, the required CSS styles are automatically inserted on the appropriate pages. However, if you want to copy the styles to your global stylesheet (which is generally more efficient), there is an option to do so under Admin > Plugins. The styles can be found in the file `samples.css`; simply copy and paste into your theme's stylesheet (usually `qa-styles.css`).

3. Now on a user's profile are links to see all questions and answers of the user. They are show both in the activity stats table and below the "Recent Activity" question list.



Translation
-------------------------------------------------

User Activity Plus is quite easy to localize to your own language:

1. Rename the language file `qa-uact-lang-default.php`, changing `default` to your site's language code. For example if your language is German, the file would be named `qa-uact-lang-de.php`.
2. Open the file in a text editor (for example [Sublime Text](http://www.sublimetext.com) or [Notepad++](http://notepad-plus-plus.org)) and edit each of the text strings on the right hand side (e.g. 'Best Answer').
3. A ^ (caret symbol) in a translation string will be replaced by some data, so *do not remove them*. For example in 'Questions asked by ^' the caret is replaced by the username.



Changelog
-------------------------------------------------

v1.1:

- Implemented localization
- Set answer snippets to plain text, no HTML/Markdown formatting
- Removed raw URLs in answer snippets (truncated URLs were getting picked up by Google)
- Displayed CSS inline by default, with option to avoid that if you copied the styles to your global stylesheet.



Pay What You Like
-------------------------------------------------

Most of my code is released under the open source GPLv3 license, and provided with a 'Pay What You Like' approach. Feel free to download and modify the plugins/themes to suit your needs, and I hope you value them enough to make a small donation of a few dollars or more.

### [Donate here](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4R5SHBNM3UDLU)
