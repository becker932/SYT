=== WP Symposium Pro Social Network plugin ===
Author: WP Symposium Pro
Contributors: Simon Goodchild
Donate link: http://www.wpsymposiumpro.com
Link: http://www.wpsymposiumpro.com
Tags: wp symposium pro, social network, social networking, social media, wpsymposium pro, wp-symposium, wp symposium, symposium
Requires at least: 3.0
Tested up to: 4.0.1
License: GPLv2 or later
Stable tag: 14.12.1

Create your own social network in WordPress! Activity wall, friends, forums, private mail - and more!

== Description ==

**This is the ultimate social network plugin for WordPress.** You can create your own social network, on your WordPress website.

With profiles, activity (wall), unlimited forums, friends, email alerts and so much more, it's perfect for clubs, schools, interest groups, engaging with customers, support sites, gaming sites, dating sites, limited only by your imagination!

Just add the plugin, click a button and you have your own social network, simple as that. :)

**Incredibly compatible**

Incredibly compatible with themes and plugins. Find out more, including additional plugins for WP Symposium Pro, at http://www.wpsymposiumpro.com.

**Massively customizable**

Want to change something, the layout, text, button labels? WP Symposium Pro’s real power lies behind shortcodes with options galore, that allow you to change just about everything, and design your social network pages the way you want them!

**Multi-lingual site?**

No problem! Easily change all the text your users will see through options. Using WPML? Works happily with that plugin too!

**And there's even more...!**

Extra extensions are available that add features galore! Private messages, groups, forum subscriptions, choose who to share activity with, image and YouTube attachments, galleries, chat lounge, profile security and many (many) more! Please note that these are available as optional extensions via http://www.wpsymposiumpro.com.

**Find out more**

Find out all about it, and more, at http://www.wpsymposiumpro.com.

Oh, and drink tea, tea is good...

== Installation ==

Go to plugins in your admin dashboard and click Add New, and search for WP Symposium Pro.

Once activated, to get started quickly, go to WPS Pro->Setup (a new menu item), and click on the "Add Profile Pages" button (once).

Erm... that's it!

This will install a default profile page (with activity, etc), an edit profile page, a change avatar page and a friendships.

Then click on the "Add Forum" button for as many forums as you want!

You may then want to add your new WordPress pages to your site menu via Appearance->Menus.

An online book is available via http://www.wpsymposiumpro.com/book that covers all topics from beginner to expert.

To manually install, download the ZIP file, extract contents and place in wp_content/wp-symposium-pro folder.

== Frequently Asked Questions ==

The best source of news and information is the WP Symposium Pro blog at http://www.wpsymposiumpro.com/blog, it's constantly kept up-to-date with news, updates, articles and tips.

For more FAQs, please visit http://www.wpsymposiumpro.com/frequently-asked-questions

== Screenshots ==

The best way to see it in action, and try it out for free, is visit http://www.wpsymposiumpro.com !

== Changelog ==

14.12.1  Fixed layout issue with activity posts for gallery items

14.12    Core: New shortcode: [wps-close-account]. Displays a button allowing a user to close their account.
         Core: Added test alert via WPS Pro->Setup->Alerts.
         Activity: Changed avatar_size option to user_avatar_size for [wps-activity-page]
         Activity: Added account_closed_msg to [wps-activity-post]         
         Avatar: Added crop to [wps-avatar-change] to skip cropping step when uploading avatar (suggest uploading square avatar images on site)
         Avatar: Replaced WordPress crop function with SimpleImage for avatar, for better quality and support for non-JPF format
         Alerts: Added flag_src to [wps-alerts-activity] and [wps-alerts-friends] to replace icon, use relative or absolute URL
         Forums: Admin and post owner can move to another forum when replying
         Forums: Admin does not have to reply to move or close a post
         Forums: Added count_include_replies to [wps-forums], whether to include replies/comments to forum posts, default 1
         Forums: Changed default of show_posts_header for [wps-forums] to 0
         Forums: Can now make private replies on the forum, only visible by forum post author and site administrators. Set allow_private=1 and optionally private_msg to [wps-forum-comment]; also optionally set private_reply_msg to [wps-forum].

14.11    Core: Moved icon color (dark or light) from Profile Page to Core Options (WPS Pro->Setup->Core Options)
         Core: Added option for external links to open in new window, with optional suffix after external links (WPS Pro->Setup->Core Options)
         Alerts: can now show alerts as a flag with count [wps-alerts-activity]
         Alerts: added ability to mark all as read, delete individual alerts and delete all alerts
         Alerts: added make_all_read_text and delete_all_text (shown with [wps-alerts-activity]
         Forums: added title_length to [wps-forum], [wps-forums] and [wps_forum_show_posts] to limit characters of forum titles (default, 50)
         Forums: added show_count, show_last_activity and show_freshess to [wps-forums] and [wps-forum]
         Forums: removed show_replies from [wps-forum] (replaced with show_count for consistency)
         Forums: added level_0_links to [wps-forums] set to 0, to disable links for top level forums
         Forums [wps-forum-show-posts]: changed include_replies option, now used for forum post replies (default 1)
         Forums [wps-forum-show-posts]: added include_comments option used for forum post reply comments (default 0)
         Forums [wps-forum-show-posts]: Added options to show summary of forum activity (good for a widget!):
             summary (whether to show as summary information, default 0)
             summary_format (if summary=1, format of text, default '%s %s %s %s ago %s')
                example of output: [simon] [replied to] [This topic] [5 mins] ago [the snippet]
             summary_started (text for 'started')
             summary_replied (text for 'replied to')
             summary_commented (text for 'commented on')
             summary_title_length (maximum length of title, default 50)
             summary_snippet_length (maximum length of snippet, default 50, set to 0 to hide)
             summary_avatar_size (size of avatar, default 32, set to 0 to hide)
             summary_show_unread (whether to bold topics with unread content, default 1)
         Friendships: New option (under WPS Pro->Setup->Friendships) to set all site members as friends with each other, always.
         Friendships: Can add pending friendship requests as a flag with count with [wps-alerts-friends]

14.10.3  Change in version number to match WP Symposium Pro (Extensions) update

14.10.2  Fix to activity when adding images to a gallery (was causing indent on following posts)

14.10.1  Change in version number to match WP Symposium Pro (Extensions) update

14.10    Activity: Members can now hide posts on their activity posted by others
         Activity: Avatar now shown when adding a comment to activity posts
         Activity: Can now set number of comments shown with [wps-activity comment_size=x]
                   Set text with comment_size_text_plural/comment_size_text_singular
         Forums:   Comments on forum post replies are now available (can be disabled/configured)
         Forums:   New option in Setup->Forum to set when a forum post automatically closes due to inactivity
         Misc:     Added wps_is_forum_page($id) core function to check if WordPress page with ID of $id is a forum page
         Misc:     Added .wps_submit class to all <input type="submit" /> in WPS for quick and easy styling of buttons, eg:
                         .wps_submit { border: 3px solid red !important; }         

14.9     Forums:   New Forum Administration, a handy single screen to manage all forums (including security if in use)
         Forums:   Added no_permission_msg to wps-forum-comment shortcode (relevant to forum security)
         Alerts:   Link in Friendship request alert now goes to user's page to accept/reject

14.8     Forums:   Updated forum-show-posts shortcode with bug fix for multi-site installations
         Forums:   After adding new post, new post is shown (instead of forum posts list)
                   -> http://www.wpsymposiumpro.com/development-to-do-list/after-adding-forum-topic-show-the-topic/
         Alerts:   Added option in setup to disable alerts from being sent out globally
         Setup:    Added warning if profile pages not setup in WPS Pro->Setup->Profile Pages

14.7.18  Avatar:   Added not_permitted (text) to [wps-avatar-change]
         Friends:  Fixed missing / in URL in the alert sent out
         Activity: When viewing a post (single view), a message is shown if it doesn't exist.
                   Change with [wps-activity not_found="Nothing to see!"]
         Forum:    When deleting comments, back to forum link is now shown
         Misc:     Increased security of custom post types

14.7.5   Forums:   Added include_closed option to [wps-forum-show-posts] (default 1)
         Setup:    Quick Start friends page now includes pending friendship requests

14.7.4   Maintenance Release

14.7.3   Core: simplified usort for activity for older versions of PHP
         Core: removed deprecated use of jQuery.browser

14.7.1   Maintenance Release
         Forums: Only published posts/replies now included in [wps-forum-show-posts]

14.7     Alerts
         New shortcode: [wps-alert-activity] to show alerts on site

         Forums
         New option: page_x_of_y to [wps-forum] for pagination subtitle, set to "" to hide

14.6.20  Forums
         Added pagination to [wps-forum]
         Added style to highlight unread posts, or posts with new replies since last viewed
         Added style to highlight posts that user has replied to. Add reply_icon=0 to [wps-forum] shortcode to hide
              http://www.wpsymposiumpro.com/development-to-do-list/icon-to-show-a-user-they-have-already-posted-on-a-topic/
         Deleted posts through front-end now put in trash
         Single Forum posts now set document <title> to the post title for SEO purposes

         Usermeta
         Added [wps-usermeta-button] shortcode to create button for URL with user ID

         Admin (Setup)
         After saving under WPS Pro->Setup, expanded section stays expanded


14.6.11  Removed rogue output when saving edited forum post

14.6.8   GMT should now be used as the 'base' for calculating freshness of content

14.05.25 Added show_replies to show/hide replies count column to [wps-forum], default 1
         Added show_comments_count to show/hide the count of replies after forum topic title, default 1

14.05.22 Added show_closed to [wps_forums], default 1, set to 0 to hide closed posts
         Improved error checking for avatar upload when creating avatar folder in wp-content/wps-pro-content

14.05.21 Added filters to support new Forum Extensions plugin

14.05.20 Added filters to support WYSIWYG option for Forum Toolbar

14.05.19 Fix for setting "wrench" not appearing on activity

14.05.18 Added [center] as a BB Code

14.05.17 Fixed problem with Friendships JS

14.05.16 Added layout option to [wps-friends], can be list or fluid, default list
         Added closed_switch and closed_switch_msg to toggle inclusion of closed forum posts
         Improved [wps-forum-show-posts] to ensure forum replies are found
         Forum post owners can now move posts to other forums (as well as site admins)
         Fixed CSS for forum textarea on some browsers, overlaying with Forum Toolbar
         Fixed to date format time difference on activity
         Removed friendships/js and friendships/css subfolders to reduce file/dir count
         Removed avatar/css subfolders to reduce file/dir count

14.05.13 Automatic fall back to older browsers added for activity attachments, and avatar upload, eg. MS IE <= 9
         Added choose, try_again_msg, and file_types_msg as options for [wps-avatar-change]
         Added missing date_format to [wps-forum]

14.05.12 Fix when "sticked" activity is older than the most recent "count" number of posts
         Added date_format across all plugins, where applicable
         Added status to [wps-forum], set to '' (all), 'open' or 'closed'
         Improve default CSS for [wps-friends]

14.05.11 Allow users to "stick" activity on their home page (not news feed)
         Use sticky_label and unsticky_label, set to '' to not offer option
         Added delete_label as option to [wps-activity] to change Settings delete text (set to '' to hide)

14.05.10 Small change to filter name typo
14.05.09 New version numbering introduced
         When editing a forum post (not reply) can move post to another forum
         Set [wps_forum moved_to="%s successfully moved to %s"] to change success message
         Set [wps_forum_post can_move_forum="0"] to disable
         Fixed space in profile link for email alerts
         Added [wps-activity hide_until_loaded, a flag to delay activity showing until after CSS loaded, default false] (can improve user experience)
         Added [wps_friends_status user_id='x'] where x is a user ID, or defaults to currently viewed user

0.59   Change Posts to Topics as default forum "post" header title
       View Forum Post (either link or button) now goes to post on front end
       View Activity (either link or button) now goes to post on front end

0.58.5 Added last_active_format option to [wps-friends]
0.58.4 Fix for [wps-forums] shortcode
0.58.3 New shortcode [wps-friends-status] to show status of friendship

0.58.2 Fixed potential security risk when viewing individual posts
       Added secure_post_msg to [wps-forum]
0.58.1 Minor changes
0.58   Can change sticky status when editing a post (admin only)
       Forum can now be locked (via forum setup) to stop new posts/replies
       Added options to [wps-forums]:
       -> show_summary, show_posts and show_posts_header
       Added translation options for [wps-forums], [wps-forum] and [wps-forum-page]:
       -> forum_title, forum_count, forum_last_activity and forum_freshness
       -> header_title, header_count, header_last_activity

0.57   Can now delete forum posts/replies if you are the owner or admin
       Various options added to [wps-forum] and [wps-usermeta] to support translations and delete feature

0.56.4 Update to wps_display_name() to support WPML plugin
0.56.3 Fixed bug when editing a forum post/comment when is the author
0.56.2 Minor fix to link from forum post on sub-folder installation
0.56.1 Minor code change for repository
0.56   First release to WordPress repository

0.55.2 Parameter to include user_id for [wps-usermeta]
0.55.1 Added IDs to some DIVs to help with styling
0.55   Prepared for release on WordPress repository
       Added option to edit profile page to switch off alerts for activity

0.54.1 Minor change
0.54   Added [wps-forum-page] shortcode

0.53.3 Improved Forum help on setup
0.53.2 Fix to shortcodes inserts when added a new quick-start forum
0.53.1 Update to setup to make new page clear
0.53   Bug fix (removed rogue </div> in forum setup)

0.52.1 Quickly setup unlimited forums via WPS Pro->Setup (quick start)
0.52   Forums plugin now included in core plugin. Forums plugin no longer required.

0.51   Alerts plugin now included in core plugin. Alerts plugin no longer required.

0.50.1 Changed default map type to dynamic
0.50   Made the getting started page more attractive and useful

0.49   Activity now included in core plugin. Activity plugin no longer required.

0.48   Added hooks to allow other plugins to hook into core setup

0.47.1 select2 only loaded when necessary

0.47   Removed external AJAX files (now handled through WP ajax)
       Added select2 to core
       
0.46   Added [map]address[/map] and [map zoom=n]address[/map] to BB Codes

0.45   Removed dependency on Taxonomy Metadata plugin

0.44.2 Added support for BB Codes: list and [*]
0.44.1 Added missing translations
0.44   Small changes to allow .mo translation file to be placed in plugins folder

0.43.3 Fix to Friendship cancellation
0.43.2 Moved BB Code CSS into core
0.43.1 Fix to [wps-friends]
0.43   Move wps_bbcode_replace() to core

0.41.2 Fixed count="n" for [wps-friends]
0.41.1 Fix to load CSS for friendship shortcodes
0.41   Only loads JS/CSS when required

0.40.2 Localised admin labels
0.40.1 Bug fix to hide when friend was last active if they haven't been active
0.40   If a user has not uploaded an avatar, the Wordpress default is shown instead

0.39   Removed use of jQuery UI from front-end, only used in admin

0.38   Added options to skip loading jQuery UI/CSS (WPS Pro->Setup->Core Options)

0.37.2 wps_are_friends() function improved
0.37.1 Added show_last_active option to [wps-friends]
0.37   Added generic wait jQuery modal, just use jQuery("body").addClass("wps_wait_loading");

0.36   Added wps_admin_setup_form_get_hook to admin Getting Started

0.35.1 Fixed wps_display_name to handle no permalinks
0.35   Added Quick Start to setup, including button to quickly create profile pages

0.34.1 Added wps_error CSS style
0.34   Added wps_query_mark() function

0.33   Added wps_get_words() function

0.32   Improved Getting Started/Setup hooks for easy extension

0.31   Added hook for Getting Started info (under WPS Pro->Setup)

0.30   Added map_style option to [wps-usermeta], static or dynamic

0.29.1 Changed wps_curPageURL() function to use HTTP_POST instead of SERVER_NAME

0.29   Added support for language translation

0.28.2 Added [wps-usermeta-change] to editor toolbar button
0.28.1 Bug fix
0.28   Added WordPress TinyMCE editor toolbar button that can be extended by other plugins
       Requires WordPress v3.9+
	     Added [wps-display-name] and [wps-avatar] to WPS editor toolbar button

0.27   Custom CSS fixed to support quotes

0.26   Removed dependency on profile page being setup in wps_display_name() function

0.25   Added wps_curPageURL() function to core

0.24   Added Custom CSS admin screen (persistent across updates)

0.23.1 Fix to JS path
0.23   Included font icon logo in menu
       Added button to WYSIWYG editor (in preparation)
     
0.22   Various code changes

0.21   Added wpspro_last_active meta for user, updated on every page load via wp_footer hook

0.20   Added password change to [wps-usermeta-change]

0.19   Added display_name and user_email to [wps-usermeta-change]

0.18   Added hook and filter to wps-usermeta-change shortcode (edit profile)

0.17   Added hooks to integrate with WPS Pro setup page
       Fixed missing file upload function

0.16   Added wps_get_friends function

0.15   Removed fileinput to attachments plugin

0.14   Removed bootstrap to ensure theme/plugin compatibility

0.13   Standardise private and none options

0.12   Added support for before and after options on shortcodes

0.11   Implemented friendship requests

0.10   Added link to edit profile page [wps-usermeta-change-link]

0.9    Added front end edit profile [wps-usermeta-change]

0.8    Added change avatar to front end and link [wps-avatar-change-link] and [wps-avatar-change]

0.7    Added admin choice for icons color scheme

0.6    Added profile page selection to setup page

0.5    Initial external release

0.1-4  Internal development

== Upgrade Notice ==

Latest news and information at http://www.wpsymposiumpro.com/blog.
